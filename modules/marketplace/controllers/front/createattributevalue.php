<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.txt
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to a newer
 * versions in the future. If you wish to customize this module for your needs
 * please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright Since 2010 Webkul
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class MarketplaceCreateAttributeValueModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if ($this->context->customer->isLogged()) {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if ($mpSeller['active'] && Configuration::get('WK_MP_PRESTA_ATTRIBUTE_ACCESS')) {
                $idSeller = $mpSeller['id_seller'];
                $idAttribute = Tools::getValue('id_attribute');
                $idGroup = Tools::getValue('id_group');
                if ($idGroup) {
                    $this->context->smarty->assign('id_group', $idGroup);
                    $idGroupJsVal = $idGroup;
                } else {
                    $idGroupJsVal = 0;
                }
                if ($idAttribute === '0') {
                    // if Attribute value is already in use
                    Tools::redirect($this->context->link->getModuleLink('marketplace', 'viewattributegroupvalue'));
                } elseif ($idAttribute) {
                    $attributeGroupList = AttributeGroup::getAttributesGroups($this->context->language->id);
                    $attribGroup = [];
                    foreach ($attributeGroupList as $attributeGroupEach) {
                        if ($attributeGroupEach['id_attribute_group'] == $idGroup) {
                            $attribGroup['name'] = $attributeGroupEach['name'];
                            $attribGroup['id'] = $attributeGroupEach['id_attribute_group'];
                        }
                    }
                    $this->context->smarty->assign('attrib_grp', $attribGroup);

                    if (_PS_VERSION_ >= '8.0.0') {
                        $groupAttributeSet = new ProductAttribute($idAttribute);
                    } else {
                        $groupAttributeSet = new Attribute($idAttribute);
                    }
                    if ($groupAttributeSet->id) {
                        $this->context->smarty->assign('attrib_valname', $groupAttributeSet->name);

                        if (WkMpProductAttribute::ifColorAttributegroup($idGroup)) {
                            $this->context->smarty->assign('attrib_color', $groupAttributeSet->color);
                        }
                    } else {
                        $this->context->smarty->assign('mp_error_message', $this->module->l('Attribute value not found. Something went wrong.', 'createattributevalue'));
                    }

                    // code for image texture
                    $image = _PS_IMG_DIR_ . 'co/' . $idAttribute . '.jpg';
                    $this->context->smarty->assign('imageTextureExists', file_exists($image));
                    $this->context->smarty->assign('id_attribute', $idAttribute);
                    $this->context->smarty->assign('id_group', $idGroup);
                } else {
                    $attributeGroupList = AttributeGroup::getAttributesGroups(
                        WkMpSeller::getSellerDefaultLanguage($idSeller)
                    );
                    $attribSet = [];
                    foreach ($attributeGroupList as $attributeGroupEach) {
                        $i = $attributeGroupEach['id_attribute_group'];
                        $attribSet[$i]['name'] = $attributeGroupEach['name'];
                        $attribSet[$i]['id'] = $attributeGroupEach['id_attribute_group'];
                    }
                    ksort($attribSet);

                    $this->context->smarty->assign('attrib_set', $attribSet);
                }

                WkMpHelper::assignDefaultLang($idSeller);
                $this->context->smarty->assign([
                    'wkself' => dirname(__FILE__),
                    'logic' => 'mp_prod_attribute',
                    'img_col_dir' => _THEME_COL_DIR_,
                ]);

                $jsVars = [
                    'createattributevalue_controller' => $this->context->link->getModuleLink(
                        'marketplace',
                        'createattributevalue'
                    ),
                    'id_group' => $idGroupJsVal,
                ];
                Media::addJsDef($jsVars);

                $this->setTemplate(
                    'module:marketplace/views/templates/front/product/combination/createattributevalue.tpl'
                );
            } else {
                Tools::redirect(__PS_BASE_URI__ . 'pagenotfound');
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('SubmitAttributeValue')) {
            $attribGroup = Tools::getValue('attrib_group');
            $sellerDefaultLanguage = Tools::getValue('default_lang');
            $defaultLang = WkMpHelper::getDefaultLanguageBeforeFormSave($sellerDefaultLanguage);

            // Check fields sizes
            if (_PS_VERSION_ >= '8.0.0') {
                $className = 'ProductAttribute';
            } else {
                $className = 'Attribute';
            }
            $rules = call_user_func([$className, 'getValidationRules'], $className);

            if (!trim(Tools::getValue('attrib_value_' . $defaultLang))) {
                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    $sellerLang = Language::getLanguage((int) $defaultLang);
                    $this->errors[] = sprintf(
                        $this->module->l('Attribute value is required in %s', 'createattributevalue'),
                        $sellerLang['name']
                    );
                } else {
                    $this->errors[] = $this->module->l('Attribute value is required', 'createattributevalue');
                }
            } else {
                $languages = Language::getLanguages();
                foreach ($languages as $language) {
                    $languageName = '';
                    if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                        $languageName = '(' . $language['name'] . ')';
                    }

                    if (!Validate::isGenericName(Tools::getValue('attrib_value_' . $language['id_lang']))) {
                        $this->errors[] = sprintf(
                            $this->module->l('Attribute value field %s is invalid.', $className),
                            $languageName
                        );
                    } elseif (Tools::strlen(Tools::getValue('attrib_value_' . $language['id_lang'])) > $rules['sizeLang']['name']) {
                        $this->errors[] = sprintf(
                            $this->module->l('Attribute value field is too long (%2$d chars max).', $className),
                            call_user_func([$className, 'displayFieldName'], $className),
                            $rules['sizeLang']['name']
                        );
                    }
                }
            }

            // add attribute value
            $isColor = 0;
            if (WkMpProductAttribute::ifColorAttributegroup($attribGroup)) {
                $isColor = 1;
                $attribValueColor = Tools::getValue('attrib_value_color');
                if (!$attribValueColor || !Validate::isColor($attribValueColor)) {
                    $this->errors[] = $this->module->l('Problem occured while adding data.', 'createattributevalue');
                }

                // validate product texture image
                if (!empty($_FILES['color_img']['name'])) {
                    $this->validAddAttrTextureImage($_FILES['color_img']);
                }
            }

            if (!count($this->errors)) {
                $usedAttribute = 0;
                $idAttribute = Tools::getValue('id_attribute');
                if ($idAttribute) {
                    // edit attribute
                    if (_PS_VERSION_ >= '8.0.0') {
                        $objAttribute = new ProductAttribute($idAttribute);
                    } else {
                        $objAttribute = new Attribute($idAttribute);
                    }
                    if (!WkMpProductAttribute::checkCombinationByAttribute($idAttribute)) {
                        $usedAttribute = 1;
                    }
                } else {
                    if (_PS_VERSION_ >= '8.0.0') {
                        $objAttribute = new ProductAttribute();
                    } else {
                        $objAttribute = new Attribute();
                    }
                    $usedAttribute = 1;
                }

                if ($usedAttribute) {
                    $objAttribute->id_attribute_group = $attribGroup;
                    foreach (Language::getLanguages(false) as $language) {
                        $attributeLangId = $language['id_lang'];
                        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                            // if attribute name in other language is not available then fill with seller language same for others
                            if (!Tools::getValue('attrib_value_' . $language['id_lang'])) {
                                $attributeLangId = $defaultLang;
                            }
                        } else {
                            // if multilang is OFF then all fields will be filled as default lang content
                            $attributeLangId = $defaultLang;
                        }
                        $objAttribute->name[$language['id_lang']] = trim(Tools::getValue('attrib_value_' . $attributeLangId));
                    }
                    if ($isColor) {
                        $objAttribute->color = $attribValueColor;
                    }
                    if ($objAttribute->save()) {
                        if ($isColor) {
                            $imageName = $objAttribute->id . '.jpg';
                            $uploadPath = _PS_IMG_DIR_ . 'co/';
                            ImageManager::resize($_FILES['color_img']['tmp_name'], $uploadPath . $imageName);
                        }
                    }

                    if ($idAttribute) {
                        Tools::redirect(
                            $this->context->link->getModuleLink(
                                'marketplace',
                                'viewattributegroupvalue',
                                ['id_group' => $attribGroup, 'updated' => 1]
                            )
                        );
                    } else {
                        Tools::redirect(
                            $this->context->link->getModuleLink(
                                'marketplace',
                                'viewattributegroupvalue',
                                ['id_group' => $attribGroup, 'created' => 1]
                            )
                        );
                    }
                } else {
                    $this->errors[] = $this->module->l('This attribute value is already in use you cannot edit or delete it.', 'createattributevalue');
                }
            }
        }
    }

    public function validAddAttrTextureImage($image)
    {
        if ($image['size'] > 0) {
            if ($image['tmp_name'] != '') {
                if (!ImageManager::isCorrectImageFileExt($image['name'])) {
                    $this->errors[] = $image['name'] .
                    $this->module->l(' : Image format not recognized, allowed formats are: .gif, .jpg, .jpeg, .png');
                }
            }
        } else {
            return true;
        }
    }

    public function displayAjaxCheckColorType()
    {
        if ($idGroup = Tools::getValue('group_id')) {
            $flag = WkMpProductAttribute::ifColorAttributegroup($idGroup);
            if ($flag) {
                exit('1');
            }
        }
        exit('0'); // ajax close
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = [];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Marketplace', 'createattributevalue'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        ];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Product Attribute', 'createattributevalue'),
            'url' => $this->context->link->getModuleLink('marketplace', 'productattribute'),
        ];
        if (Tools::getValue('id_attribute')) {
            $breadcrumb['links'][] = [
                'title' => $this->module->l('Edit Attribute Value', 'createattributevalue'),
                'url' => '',
            ];
        } else {
            $breadcrumb['links'][] = [
                'title' => $this->module->l('Add New Attribute Value', 'createattributevalue'),
                'url' => '',
            ];
        }

        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->registerStylesheet(
            'mp-marketplace_account',
            'modules/' . $this->module->name . '/views/css/marketplace_account.css'
        );
        $this->registerStylesheet(
            'mp_global_style-css',
            'modules/' . $this->module->name . '/views/css/mp_global_style.css'
        );

        $this->registerJavascript(
            'mp-productattribute',
            'modules/' . $this->module->name . '/views/js/productattribute.js'
        );
        $this->registerJavascript(
            'mp-change_multilang',
            'modules/' . $this->module->name . '/views/js/change_multilang.js'
        );
    }
}
