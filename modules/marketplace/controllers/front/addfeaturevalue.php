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

class MarketplaceAddFeatureValueModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if ($this->context->customer->isLogged()) {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if ($mpSeller && Configuration::get('WK_MP_PRESTA_FEATURE_ACCESS')) {
                if (Tools::getValue('id_feature')) {
                    $idFeature = Tools::getValue('id_feature');
                    $this->context->smarty->assign('id_feature', $idFeature);

                    // Edit feature value page
                    if (Tools::getValue('id_feature_value')) {
                        $idFeatureValue = Tools::getValue('id_feature_value');
                        $featureInfo = [];
                        $featureVal = [];
                        // IF FEATURE VALUE IS EDITABLE OR NOT ( 0 = NON EDITABLE)
                        if ($idFeatureValue != 0) {
                            $featureData = Feature::getFeature($this->context->language->id, $idFeature);
                            if (!$featureData) {
                                $this->context->smarty->assign('mp_error_message', $this->module->l('Feature value not found. Something went wrong.', 'addfeaturevalue'));
                            } else {
                                $featureInfo['name'] = $featureData['name'];
                                $featureInfo['id'] = $idFeature;
                                $data = FeatureValue::getFeatureValueLang($idFeatureValue);
                                foreach ($data as $data_each) {
                                    $featureVal[$data_each['id_lang']] = $data_each['value'];
                                }
                            }

                            $this->context->smarty->assign('id_feature_value', $idFeatureValue);
                            $this->context->smarty->assign('feature_info', $featureInfo);
                            $this->context->smarty->assign('feature_val', $featureVal);
                        } else {
                            Tools::redirect($this->context->link->getModuleLink('marketplace', 'viewfeaturevalue', ['id_feature' => $idFeature, 'error_attr' => 1]));
                        }
                    }
                }

                $defaultLang = WkMpHelper::getDefaultLanguageBeforeFormSave($mpSeller['default_lang']);
                $featureData = Feature::getFeatures($defaultLang);
                $i = 0;
                $featureSet = [];
                foreach ($featureData as $featureDataEach) {
                    $featureSet[$i]['id'] = $featureDataEach['id_feature'];
                    $featureSet[$i]['name'] = $featureDataEach['name'];
                    ++$i;
                }

                // Set default lang at every form according to configuration multi-language
                WkMpHelper::assignDefaultLang($mpSeller['id_seller']);
                $this->context->smarty->assign([
                    'feature_set' => $featureSet,
                    'wkself' => dirname(__FILE__),
                    'logic' => 'mp_prod_features',
                ]);
                $this->setTemplate('module:marketplace/views/templates/front/product/features/addfeaturevalue.tpl');
            } else {
                Tools::redirect(__PS_BASE_URI__ . 'pagenotfound');
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('SubmitFeatureValue')) {
            if ($this->context->customer->isLogged()) {
                $featureGroup = Tools::getValue('feature_group');
                $sellerDefaultLanguage = Tools::getValue('default_lang');
                $defaultLang = WkMpHelper::getDefaultLanguageBeforeFormSave($sellerDefaultLanguage);

                // Check fields sizes
                $className = 'FeatureValue';
                $rules = call_user_func([$className, 'getValidationRules'], $className);

                if (!trim(Tools::getValue('feature_value_' . $defaultLang))) {
                    if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                        $sellerLang = Language::getLanguage((int) $defaultLang);
                        $this->errors[] = sprintf($this->module->l('Feature value is required in %s.', 'addfeaturevalue'), $sellerLang['name']);
                    } else {
                        $this->errors[] = $this->module->l('Feature value is required.', 'addfeaturevalue');
                    }
                } else {
                    $languages = Language::getLanguages();
                    foreach ($languages as $language) {
                        $languageName = '';
                        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                            $languageName = '(' . $language['name'] . ')';
                        }

                        if (!Validate::isGenericName(Tools::getValue('feature_value_' . $language['id_lang']))) {
                            $this->errors[] = sprintf($this->module->l('Feature value field %s is invalid.', $className), $languageName);
                        } elseif (Tools::strlen(Tools::getValue('feature_value_' . $language['id_lang'])) > $rules['sizeLang']['value']) {
                            $this->errors[] = sprintf($this->module->l('Feature value field is too long (%2$d chars max).', $className), call_user_func([$className, 'displayFieldName'], $className), $rules['sizeLang']['value']);
                        }
                    }
                }

                if (!count($this->errors)) {
                    $idFeatureValue = Tools::getValue('id_feature_value');
                    if ($idFeatureValue) {
                        $successAttr = 2;
                        $objFeatureValue = new FeatureValue($idFeatureValue);
                    } else {
                        $successAttr = 1;
                        $objFeatureValue = new FeatureValue();
                    }

                    $objFeatureValue->id_feature = $featureGroup;
                    $objFeatureValue->custom = 0;
                    foreach (Language::getLanguages(false) as $language) {
                        $featureLangId = $language['id_lang'];
                        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                            // if feature value in other language is not available then fill with seller language same for others
                            if (!Tools::getValue('feature_value_' . $language['id_lang'])) {
                                $featureLangId = $defaultLang;
                            }
                        } else {
                            // if multilang is OFF then all fields will be filled as default lang content
                            $featureLangId = $defaultLang;
                        }
                        $objFeatureValue->value[$language['id_lang']] = trim(Tools::getValue('feature_value_' . $featureLangId));
                    }
                    $objFeatureValue->save();

                    Tools::redirect($this->context->link->getModuleLink('marketplace', 'viewfeaturevalue', ['id_feature' => $featureGroup, 'success_attr' => $successAttr]));
                }
            } else {
                Tools::redirect($this->context->link->getPageLink('my-account'));
            }
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = [];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Marketplace', 'addfeaturevalue'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        ];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Product Features', 'addfeaturevalue'),
            'url' => $this->context->link->getModuleLink('marketplace', 'productfeature'),
        ];
        if (Tools::getValue('id_feature_value')) {
            $breadcrumb['links'][] = [
                'title' => $this->module->l('Edit Feature Value', 'addfeaturevalue'),
                'url' => '',
            ];
        } else {
            $breadcrumb['links'][] = [
                'title' => $this->module->l('Add New Value', 'addfeaturevalue'),
                'url' => '',
            ];
        }

        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->registerStylesheet('mp-marketplace_account', 'modules/' . $this->module->name . '/views/css/marketplace_account.css');
        $this->registerStylesheet('mp_global_style-css', 'modules/' . $this->module->name . '/views/css/mp_global_style.css');
        $this->registerStylesheet('mp_productfeature-css', 'modules/' . $this->module->name . '/views/css/productfeature.css');

        $this->registerJavascript('mp-change_multilang', 'modules/' . $this->module->name . '/views/js/change_multilang.js');
    }
}
