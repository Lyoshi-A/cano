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

class MarketplaceCreateFeatureModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if ($this->context->customer->isLogged()) {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if ($mpSeller && Configuration::get('WK_MP_PRESTA_FEATURE_ACCESS')) {
                if (Tools::getValue('id_feature')) {
                    $idFeature = Tools::getValue('id_feature');

                    // IF FEATURE IS EDITABLE OR NOT ( 0 = NON EDITABLE)
                    if ($idFeature != 0) {
                        $objFeatureData = new Feature($idFeature);
                        if (!$objFeatureData->id) {
                            $this->context->smarty->assign('mp_error_message', $this->module->l('Feature not found. Something went wrong.', 'createfeature'));
                        }
                        $this->context->smarty->assign('feature_name_val', $objFeatureData->name);
                        $this->context->smarty->assign('id_feature', $idFeature);
                    } else {
                        Tools::redirect($this->context->link->getModuleLink('marketplace', 'productfeature', ['error_attr' => 1]));
                    }
                }

                // Set default lang at every form according to configuration multi-language
                WkMpHelper::assignDefaultLang($mpSeller['id_seller']);
                $this->context->smarty->assign([
                    'wkself' => dirname(__FILE__),
                    'logic' => 'mp_prod_features',
                ]);
                $this->setTemplate('module:marketplace/views/templates/front/product/features/createfeature.tpl');
            } else {
                Tools::redirect(__PS_BASE_URI__ . 'pagenotfound');
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('SubmitFeature')) {
            if ($this->context->customer->isLogged()) {
                $sellerDefaultLanguage = Tools::getValue('default_lang');
                $defaultLang = WkMpHelper::getDefaultLanguageBeforeFormSave($sellerDefaultLanguage);

                // Check fields sizes
                $className = 'Feature';
                $rules = call_user_func([$className, 'getValidationRules'], $className);

                if (!trim(Tools::getValue('feature_name_' . $defaultLang))) {
                    if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                        $sellerLang = Language::getLanguage((int) $defaultLang);
                        $this->errors[] = sprintf($this->module->l('Feature name is required in %s', 'createfeature'), $sellerLang['name']);
                    } else {
                        $this->errors[] = $this->module->l('Feature name is required.', 'createfeature');
                    }
                } else {
                    $languages = Language::getLanguages();
                    foreach ($languages as $language) {
                        $languageName = '';
                        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                            $languageName = '(' . $language['name'] . ')';
                        }

                        if (!Validate::isGenericName(Tools::getValue('feature_name_' . $language['id_lang']))) {
                            $this->errors[] = sprintf($this->module->l('Feature name field %s is invalid.', $className), $languageName);
                        } elseif (Tools::strlen(Tools::getValue('feature_name_' . $language['id_lang'])) > $rules['sizeLang']['name']) {
                            $this->errors[] = sprintf($this->module->l('Feature name field is too long (%2$d chars max).', $className), call_user_func([$className, 'displayFieldName'], $className), $rules['sizeLang']['name']);
                        }
                    }
                }

                if (!count($this->errors)) {
                    $idFeature = Tools::getValue('id_feature');
                    if ($idFeature) {
                        $objFeature = new Feature($idFeature); // edit feature
                    } else {
                        $objFeature = new Feature(); // create feature
                    }

                    foreach (Language::getLanguages(false) as $language) {
                        $featureLangId = $language['id_lang'];
                        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                            // if feature name in other language is not available then fill with seller language same for others
                            if (!Tools::getValue('feature_name_' . $language['id_lang'])) {
                                $featureLangId = $defaultLang;
                            }
                        } else {
                            // if multilang is OFF then all fields will be filled as default lang content
                            $featureLangId = $defaultLang;
                        }
                        $objFeature->name[$language['id_lang']] = trim(Tools::getValue('feature_name_' . $featureLangId));
                    }
                    $objFeature->save();
                    if ($idFeature) {
                        $successAttr = 2;
                    } else {
                        if ($objFeature->id) {
                            WkMpProductFeature::addPSLayeredIndexableFeature(['id_feature' => $objFeature->id, 'indexable' => 1]);
                        }
                        $successAttr = 1;
                    }

                    Tools::redirect($this->context->link->getModuleLink('marketplace', 'productfeature', ['success_attr' => $successAttr]));
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
            'title' => $this->module->l('Marketplace', 'createfeature'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        ];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Product Features', 'createfeature'),
            'url' => $this->context->link->getModuleLink('marketplace', 'productfeature'),
        ];
        if (Tools::getValue('id_feature')) {
            $breadcrumb['links'][] = [
                'title' => $this->module->l('Edit Feature', 'createfeature'),
                'url' => '',
            ];
        } else {
            $breadcrumb['links'][] = [
                'title' => $this->module->l('Add New Feature', 'createfeature'),
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
        $this->registerJavascript('mp-productfeature', 'modules/' . $this->module->name . '/views/js/productfeature.js');
    }
}
