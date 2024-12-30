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

class MarketplaceAddMpShippingModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $link = new Link();
        $idCustomer = $this->context->customer->id;
        if ($idCustomer) {
            $mpCustomerInfo = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($mpCustomerInfo && $mpCustomerInfo['active'] && Configuration::get('WK_MP_SELLER_SHIPPING')) {
                $mpIdSeller = $mpCustomerInfo['id_seller'];

                // get total zone available in prestashop
                $zoneDetail = Zone::getZones(false, true);
                $this->context->smarty->assign('zones', $zoneDetail);

                // Get customer group
                if ($customerAllGroups = Group::getGroups($this->context->language->id)) {
                    $this->context->smarty->assign('customerAllGroups', $customerAllGroups);
                }

                $mpShippingId = Tools::getValue('mpshipping_id');
                $mpShippingProcess = '';
                if ($mpShippingId) {
                    $objWkMpShipping = new WkMpSellerShipping($mpShippingId);
                    if ($mpIdSeller == $objWkMpShipping->id_seller) {
                        $objCarrier = Carrier::getCarrierByReference($objWkMpShipping->id_ps_reference);
                        if (!$objWkMpShipping->carrierAllowedOnShop($objCarrier->id)) {
                            // Other shop carrier edit is not allowed
                            Tools::redirect($this->context->link->getModuleLink('marketplace', 'mpshippinglist'));
                        }

                        // Delete logo
                        if (Tools::getValue('delete_logo')) {
                            // Delete from MP
                            if (file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/mpshipping/' . $mpShippingId . '.jpg')) {
                                unlink(_PS_MODULE_DIR_ . 'marketplace/views/img/mpshipping/' . $mpShippingId . '.jpg');
                            }
                            // Delete from PS
                            if (file_exists(_PS_SHIP_IMG_DIR_ . $objCarrier->id . '.jpg')) {
                                unlink(_PS_SHIP_IMG_DIR_ . $objCarrier->id . '.jpg');
                            }

                            Tools::redirect(
                                $this->context->link->getModuleLink(
                                    'marketplace',
                                    'addmpshipping',
                                    ['mpshipping_id' => $mpShippingId, 'delete_success' => 1]
                                )
                            );
                        }

                        $this->context->smarty->assign(
                            'wk_delete_logo_path',
                            $this->context->link->getModuleLink(
                                'marketplace',
                                'addmpshipping',
                                ['mpshipping_id' => $mpShippingId, 'delete_logo' => 1]
                            )
                        );

                        // Edit carrier
                        $carrierZones = array_column($objCarrier->getZones(), 'id_zone');
                        $this->context->smarty->assign('wk_carrier_zones', $carrierZones);
                        $this->context->smarty->assign('mp_shipping_id', $mpShippingId);
                        // tax option for seller shipping
                        $this->context->smarty->assign('tax_rules', TaxRulesGroup::getTaxRulesGroups(true));
                        $this->context->smarty->assign('id_tax_rule_group', $objCarrier->getIdTaxRulesGroup());
                        $this->context->smarty->assign('range_behavior', $objCarrier->range_behavior);
                        // end
                        $this->context->smarty->assign('mp_shipping_name', $objCarrier->name);
                        $this->context->smarty->assign('transit_delay', $objCarrier->delay);
                        $this->context->smarty->assign('shipping_method', $objCarrier->shipping_method);
                        $this->context->smarty->assign('tracking_url', $objCarrier->url);
                        $this->context->smarty->assign('grade', $objCarrier->grade);
                        $this->context->smarty->assign('shipping_handling', $objCarrier->shipping_handling);
                        $this->context->smarty->assign('shipping_handling_charge', Configuration::get('PS_SHIPPING_HANDLING'));
                        $this->context->smarty->assign('is_free', $objCarrier->is_free);
                        $this->context->smarty->assign('max_width', $objCarrier->max_width);
                        $this->context->smarty->assign('max_height', $objCarrier->max_height);
                        $this->context->smarty->assign('max_depth', $objCarrier->max_depth);
                        $this->context->smarty->assign('max_weight', $objCarrier->max_weight);

                        // Get Shipping group
                        if ($shippingGroup = $objCarrier->getGroups()) {
                            $this->context->smarty->assign('shippingGroup', array_column($shippingGroup, 'id_group'));
                        }

                        // @shippingMethod==1 billing accroding to weight
                        // @shippingMethod==2 billing accroding to price
                        $ranges = $objWkMpShipping->getCarrierRangeValue($objCarrier);
                        $shippingMethod = $objCarrier->shipping_method;

                        if (!count($ranges)) {
                            $ranges[] = ['id_range' => 0, 'delimiter1' => 0, 'delimiter2' => 0];
                        }
                        $this->context->smarty->assign('ranges', $ranges);

                        $imageexist = _PS_MODULE_DIR_ . 'marketplace/views/img/mpshipping/' . $mpShippingId . '.jpg';
                        if (file_exists($imageexist)) {
                            $this->context->smarty->assign('imageexist', $imageexist);
                        }

                        Media::addJsDef([
                            'mp_shipping_id' => $mpShippingId,
                            'is_free' => $objCarrier->is_free,
                            'shipping_handling' => $objCarrier->shipping_handling,
                            'shipping_method' => $shippingMethod,
                        ]);
                        $mpShippingProcess = $link->getModuleLink(
                            'marketplace',
                            'addmpshipping',
                            [
                                'submitUpdateshipping' => 1,
                                'mp_id_seller' => $mpIdSeller,
                                'mp_shipping_id' => $mpShippingId,
                            ]
                        );
                    } else {
                        $this->context->smarty->assign([
                            'logic' => 'mp_carriers',
                            'mp_error_message' => $this->module->l('Carrier not found. Something went wrong.', 'addmpshipping'),
                        ]);
                    }
                } else {
                    // Add carrier
                    $this->context->smarty->assign('shipping_method', 2);
                    $this->context->smarty->assign('mp_shipping_name', '');
                    $this->context->smarty->assign('transit_delay', '');
                    $this->context->smarty->assign('tracking_url', '');
                    $this->context->smarty->assign('grade', 0);
                    $this->context->smarty->assign('shipping_handling_charge', Configuration::get('PS_SHIPPING_HANDLING'));
                    $this->context->smarty->assign('max_width', 0);
                    $this->context->smarty->assign('max_height', 0);
                    $this->context->smarty->assign('max_depth', 0);
                    $this->context->smarty->assign('max_weight', 0);
                    // tax option for seller shipping
                    $this->context->smarty->assign('tax_rules', TaxRulesGroup::getTaxRulesGroups(true));
                    Media::addJsDef(['mp_shipping_id' => '']);

                    $mpShippingProcess = $link->getModuleLink('marketplace', 'addmpshipping', ['submitAddshipping' => 1, 'mp_id_seller' => $mpIdSeller]);
                }

                // Set default lang at every form according to configuration multi-language
                WkMpHelper::assignDefaultLang($mpIdSeller);

                $this->context->smarty->assign('title_text_color', Configuration::get('WK_MP_TITLE_TEXT_COLOR'));
                $this->context->smarty->assign('title_bg_color', Configuration::get('WK_MP_TITLE_BG_COLOR'));
                $this->context->smarty->assign('mpshippingprocess', $mpShippingProcess);
                $this->context->smarty->assign('self', dirname(__FILE__));
                $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
                $this->context->smarty->assign('currency_sign', $currency->sign);
                $this->context->smarty->assign('PS_WEIGHT_UNIT', Configuration::get('PS_WEIGHT_UNIT'));
                $this->context->smarty->assign('logic', 'mp_carriers');
                /*$this->context->smarty->assign('title_text_color', Configuration::get('MP_TITLE_TEXT_COLOR'));
                $this->context->smarty->assign('title_bg_color', Configuration::get('MP_TITLE_BG_COLOR'));*/

                $jsDefVar = [
                    'currency_sign' => $currency->sign,
                    'PS_WEIGHT_UNIT' => Configuration::get('PS_WEIGHT_UNIT'),
                    'string_price' => $this->module->l('Will be applied when the price is', 'addmpshipping'),
                    'string_weight' => $this->module->l('Will be applied when the weight is', 'addmpshipping'),
                    'invalid_range' => $this->module->l('This range is not valid', 'addmpshipping'),
                    'need_to_validate' => $this->module->l('Please validate the last range before create a new one.', 'addmpshipping'),
                    'delete_range_confirm' => $this->module->l('Are you sure to delete this range ?', 'addmpshipping'),
                    'labelDelete' => $this->module->l('Delete', 'addmpshipping'),
                    'labelValidate' => $this->module->l('Validate', 'addmpshipping'),
                    'range_is_overlapping' => $this->module->l('Ranges are overlapping.', 'addmpshipping'),
                    'finish_error' => $this->module->l('You need to go through all step.', 'addmpshipping'),
                    'shipping_name_error' => $this->module->l('Carrier name is required field.', 'addmpshipping'),
                    'transit_time_error' => $this->module->l('Transit time is required in', 'addmpshipping'),
                    'transit_time_error_other' => $this->module->l('Transit time is required in', 'addmpshipping'),
                    'speedgradeinvalid' => $this->module->l('Speed grade must be integer.', 'addmpshipping'),
                    'speedgradevalue' => $this->module->l('Speed grade must be from 0 to ', 'addmpshipping'),
                    'invalid_logo_file_error' => $this->module->l('Invalid logo file!', 'addmpshipping'),
                    'shipping_charge_error_message' => $this->module->l('Shipping charge is not valid.', 'addmpshipping'),
                    'shipping_charge_lower_limit_error1' => $this->module->l('Shipping charge lower limit must be numeric.', 'addmpshipping'),
                    'shipping_charge_lower_limit_error2' => $this->module->l('Shipping charge lower limit should not negative.', 'addmpshipping'),
                    'shipping_charge_upper_limit_error1' => $this->module->l('Shipping charge upper limit must be numeric.', 'addmpshipping'),
                    'shipping_charge_upper_limit_error2' => $this->module->l('Shipping charge upper limit should not negative.', 'addmpshipping'),
                    'shipping_charge_limit_error' => $this->module->l('Shipping charge upper limit must be greater than lower limit.', 'addmpshipping'),
                    'shipping_charge_limit_equal_error' => $this->module->l('Shipping charge lower limit and upper limit should not equal.', 'addmpshipping'),
                    'invalid_logo_size_error' => $this->module->l('Invalid logo size.', 'addmpshipping'),
                    'invalid_range_value' => $this->module->l('Ranges upper and lower values should not clash to one another.', 'addmpshipping'),
                    'shipping_select_zone_err' => $this->module->l('Select atleast one zone.', 'addmpshipping'),
                    'impact_price_text' => $this->module->l('Impact Price', 'addmpshipping'),
                    'interger_price_text' => $this->module->l('Enter price should be an integer.', 'addmpshipping'),
                    'confirm_msg' => $this->module->l('Are you sure?', 'addmpshipping'),
                    'invalidNumeric' => $this->module->l('Enter numeric value.', 'addmpshipping'),
                    'wk_static_token' => Tools::getToken(false),
                ];

                Media::addJsDef($jsDefVar);

                if (Tools::getValue('addmpshipping_step4') == 1) {
                    $mpShippingId = Tools::getValue('mpshipping_id');
                    if ($mpShippingId) {
                        $objWkMpShipping = new WkMpSellerShipping($mpShippingId);
                        $objCarrier = Carrier::getCarrierByReference($objWkMpShipping->id_ps_reference);
                        if ($objCarrier->is_free) {
                            Tools::redirect($link->getModuleLink('marketplace', 'mpshippinglist', ['addmpshipping_success' => 1]));
                        } else {
                            $shippingMethod = 0;
                            if (Tools::getValue('updateimpact')) {
                                // Delete impact price
                                if (Tools::getValue('impact_id')) {
                                    $impactId = Tools::getValue('impact_id');
                                    $objMpShipImpact = new WkMpSellerShippingImpact((int) $impactId);
                                    $objMpShippingNew = new WkMpSellerShipping($objMpShipImpact->mp_shipping_id);
                                    if ($objMpShippingNew->id_seller == $mpIdSeller) {
                                        $objMpShipImpact->delete();
                                        $this->context->smarty->assign('deleteimpact', 1);
                                    }
                                }

                                $shippingMethod = $objCarrier->shipping_method;
                                $getImpactPriceArr = WkMpSellerShippingImpact::getAllImpactPriceByMpshippingid($mpShippingId);
                                if ($getImpactPriceArr) {
                                    $impactPriceArr = [];
                                    foreach ($getImpactPriceArr as $key => $getImpactPrice) {
                                        $zoneArr = WkMpSellerShippingImpact::getZonenameByZoneid($getImpactPrice['id_zone']);
                                        $impactPriceArr[$key]['id_zone'] = $zoneArr['name'];

                                        $countryName = CountryCore::getNameById($this->context->language->id, $getImpactPrice['id_country']);
                                        $impactPriceArr[$key]['id_country'] = $countryName;

                                        if ($getImpactPrice['id_state']) {
                                            $stateName = StateCore::getNameById($getImpactPrice['id_state']);
                                            $impactPriceArr[$key]['id_state'] = $stateName;
                                        } else {
                                            $impactPriceArr[$key]['id_state'] = 'All';
                                        }

                                        $impactPriceArr[$key]['shipping_delivery_id'] = $getImpactPrice['shipping_delivery_id'];
                                        $impactPriceArr[$key]['impact_price'] = $getImpactPrice['impact_price'];
                                        $impactPriceArr[$key]['impact_price_display'] = WkMpHelper::displayPrice(
                                            $getImpactPrice['impact_price'],
                                            $currency
                                        );
                                        $impactPriceArr[$key]['id'] = $getImpactPrice['id'];
                                        $impactPriceArr[$key]['mp_shipping_id'] = $mpShippingId;

                                        /* Range Or weight for the impact */
                                        $impactPriceArr[$key]['price_range'] = '';
                                        $impactPriceArr[$key]['weight_range'] = '';
                                        $ranges = $objWkMpShipping->getCarrierRangeValue($objCarrier);
                                        if ($ranges) {
                                            if ($deliveryId = $getImpactPrice['shipping_delivery_id']) {
                                                if (isset($ranges['range'][$deliveryId])) {
                                                    $delimiter1 = $ranges['range'][$deliveryId]['delimiter1'];
                                                    $delimiter2 = $ranges['range'][$deliveryId]['delimiter2'];
                                                    if ($shippingMethod == 2) {
                                                        $impactPriceArr[$key]['price_range'] = Tools::ps_round($delimiter1, 2) . '-' . Tools::ps_round($delimiter2, 2);
                                                    } else {
                                                        $impactPriceArr[$key]['weight_range'] = Tools::ps_round($delimiter1, 2) . '-' . Tools::ps_round($delimiter2, 2);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $this->context->smarty->assign('ship_method', $shippingMethod);
                                    $this->context->smarty->assign('impactprice_arr', $impactPriceArr);
                                }
                                $this->context->smarty->assign('updateimpact', 1);
                            } else {
                                $this->context->smarty->assign('addmpshipping_success', 1);
                            }
                            $this->context->smarty->assign('mpshipping_id', $mpShippingId);
                            $this->context->smarty->assign('mpshipping_name', $objCarrier->name);
                            $shippingAjaxLink = $link->getModuleLink('marketplace', 'addmpshipping');
                            $this->context->smarty->assign('shipping_ajax_link', $shippingAjaxLink);
                            $this->context->smarty->assign('modules_dir', _MODULE_DIR_);
                            $updateImpactLink = $link->getModuleLink('marketplace', 'addmpshipping', ['mpshipping_id' => $mpShippingId, 'addmpshipping_step4' => 1, 'updateimpact' => 1]);
                            $lastJsDef = [
                                'img_ps_dir' => _MODULE_DIR_ . 'marketplace/views/img/',
                                'shipping_ajax_link' => $shippingAjaxLink,
                                'select_country' => $this->module->l('Select country', 'addmpshipping'),
                                'select_state' => $this->module->l('All', 'addmpshipping'),
                                'zone_error' => $this->module->l('Select zone', 'addmpshipping'),
                                'no_range_available_error' => $this->module->l('No range available.', 'addmpshipping'),
                                'ranges_info' => $this->module->l('Ranges', 'addmpshipping'),
                                'message_impact_price_error' => $this->module->l('Impact price is invalid.', 'addmpshipping'),
                                'message_impact_price' => $this->module->l('Impact added sucessfully.', 'addmpshipping'),
                                'update_impact_link' => $updateImpactLink,
                                'currency_sign' => $currency->sign,
                            ];

                            Media::addJsDef($lastJsDef);

                            if ($shippingMethod == 2) {
                                Media::addJsDef(['range_sign' => $currency->sign]);
                            } else {
                                Media::addJsDef(['range_sign' => Configuration::get('PS_WEIGHT_UNIT')]);
                            }

                            $this->setTemplate('module:marketplace/views/templates/front/shipping/addshippingstep4.tpl');
                        }
                    }
                } else {
                    $this->setTemplate('module:marketplace/views/templates/front/shipping/addmpshipping.tpl');
                }
            } else {
                Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect('index.php?controller=authentication&back=' . urlencode($this->context->link->getModuleLink('marketplace', 'addmpshipping')));
        }
    }

    public function postProcess()
    {
        if (Tools::getValue('submitAddshipping') || Tools::getValue('submitUpdateshipping')) {
            $shippingName = trim(Tools::getValue('shipping_name'));
            $isValidShippingName = Validate::isCarrierName($shippingName);
            $grade = Tools::getValue('grade');
            $isValidGrade = Validate::isUnsignedInt($grade);
            $shippingMethod = Tools::getValue('shipping_method');
            $trackingUrl = Tools::getValue('tracking_url');
            $isValidTrackingUrl = Validate::isAbsoluteUrl($trackingUrl);
            $isFree = Tools::getValue('is_free');
            $rangeInf = Tools::getValue('range_inf');
            $rangeSup = Tools::getValue('range_sup');
            $shippingHandling = Tools::getValue('shipping_handling');
            // for tax option in seller shipping
            $idTaxRuleGroup = Tools::getValue('id_tax_rule_group');
            $rangeBehavior = Tools::getValue('range_behavior');
            $maxHeight = Tools::getValue('max_height');
            $maxWidth = Tools::getValue('max_width');
            $maxDepth = Tools::getValue('max_depth');
            $maxWeight = Tools::getValue('max_weight');
            $shippingGroup = Tools::getValue('shipping_group');
            $zoneFees = Tools::getValue('fees');

            // If multi-lang is OFF then PS default lang will be default lang for seller
            $defaultLang = 0;
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $defaultLang = Tools::getValue('current_lang');
            } else {
                if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') { // Admin default lang
                    $defaultLang = Configuration::get('PS_LANG_DEFAULT');
                } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') { // Seller default lang
                    $defaultLang = Tools::getValue('current_lang');
                }
            }
            $mpIdSeller = Tools::getValue('mp_id_seller');
            $isValid = false;
            if (isset($this->context->customer, $this->context->customer->id)
                && $this->context->customer->id
                && $this->context->customer->isLogged()
            ) {
                $sellerDetail = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
                if (!empty($sellerDetail)) {
                    $isValid = true;
                    if ($mpIdSeller != $sellerDetail['id_seller']) {
                        $mpIdSeller = $sellerDetail['id_seller'];
                    }
                }
            }
            if (!$isValid) {
                Tools::redirect($this->context->link->getModuleLink($this->module->name, 'mpshippinglist'));
            }
            $idMpSellerByCustomer = WkMpSeller::getSellerByCustomerId($this->context->customer->id);
            if (!is_array($idMpSellerByCustomer) || $mpIdSeller != $idMpSellerByCustomer['id_seller']) {
                $this->errors[] = $this->module->l('Something went wrong please try again', 'addmpshipping');
            } else {
                $mpShippingId = Tools::getValue('mp_shipping_id');
                if ($mpShippingId) {
                    $objMpShippingMethod = new WkMpSellerShipping($mpShippingId);
                    if (!Validate::isLoadedObject($objMpShippingMethod) || $mpIdSeller != $objMpShippingMethod->id_seller) {
                        $this->errors[] = $this->module->l('Something went wrong please try again', 'addmpshipping');
                    }
                }
            }

            if (!$shippingName) {
                $this->errors[] = $this->module->l('Carrier name is required.', 'addmpshipping');
            } elseif (!$isValidShippingName) {
                $this->errors[] = $this->module->l('Carrier name must not have Invalid characters /^[^<>;=#{}]*$/u', 'addmpshipping');
            } elseif (Tools::strlen($shippingName) > 64) {
                $this->errors[] = $this->module->l('Carrier name field is too long (64 chars max).', 'addmpshipping');
            }

            if (!trim(Tools::getValue('transit_time_' . $defaultLang))) {
                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    $defLangArr = Language::getLanguage((int) $defaultLang);
                    $this->errors[] = $this->module->l('Transit time is required in ' . $defLangArr['name'], 'addmpshipping');
                } else {
                    $this->errors[] = $this->module->l('Transit time is required', 'addmpshipping');
                }
            } else {
                foreach (Language::getLanguages() as $language) {
                    $languageName = '';
                    if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                        $languageName = $language['name'];
                    }

                    if (!Validate::isCatalogName(Tools::getValue('transit_time_' . $language['id_lang']))) {
                        $this->errors[] = $this->module->l('Transit time must not have invalid characters /^[^<>={}]*$/u in ' . $languageName, 'addmpshipping');
                    }
                }
            }

            if (!$isValidGrade) {
                $this->errors[] = $this->module->l('Speed grade must be numeric', 'addmpshipping');
            } elseif ($grade < 0 || $grade > 9) {
                $this->errors[] = $this->module->l('Speed grade must be from 0 to 9', 'addmpshipping');
            }

            if (!$isValidTrackingUrl) {
                $this->errors[] = $this->module->l('Invalid Tracking Url', 'addmpshipping');
            }

            // if shipping is not free then range is mandatory
            if (Tools::getValue('is_free') == 0) {
                $rangeInf = Tools::getValue('range_inf');
                $rangeSup = Tools::getValue('range_sup');
                if (isset($rangeInf[0]) && $rangeInf[0] == '') {
                    $this->errors[] = $this->module->l('Shipping charge lower limit should not blank', 'addmpshipping');
                }
                if (isset($rangeSup[0]) && $rangeSup[0] == '') {
                    $this->errors[] = $this->module->l('Shipping charge upper limit should not blank', 'addmpshipping');
                }
            }

            $isNewImage = false;
            if (isset($_FILES['shipping_logo'])) {
                if ($_FILES['shipping_logo']['size'] > 0 && $_FILES['shipping_logo']['tmp_name'] != '') {
                    if ($error = ImageManager::validateUpload($_FILES['shipping_logo'])) {
                        $this->errors[] = $error;
                    }
                    $imageType = ['jpg', 'jpeg', 'png'];
                    $extention = explode('.', $_FILES['shipping_logo']['name']);
                    $ext = Tools::strtolower($extention['1']);
                    if (!in_array($ext, $imageType)) {
                        $this->errors[] = $this->module->l('Only jpg,png,jpeg image formats are allowed and image size should not exceed 125*125', 'addmpshipping');
                    } else {
                        list($width, $height) = getimagesize($_FILES['shipping_logo']['tmp_name']);
                        if ($width > 125 || $height > 125) {
                            $this->errors[] = $this->module->l('Only jpg,png,jpeg image formats are allowed and image size should not exceed 125*125', 'addmpshipping');
                        }
                        $isNewImage = true;
                    }
                }
            }
            if ($maxHeight == '') {
                $maxHeight = (int) 0;
            } elseif (!Validate::isUnsignedInt($maxHeight)) {
                $this->errors[] = $this->module->l('The max height field is invalid', 'addmpshipping');
            }

            if ($maxWidth == '') {
                $maxWidth = (int) 0;
            } elseif (!Validate::isUnsignedInt($maxWidth)) {
                $this->errors[] = $this->module->l('The max width field is invalid', 'addmpshipping');
            }

            if ($maxDepth == '') {
                $maxDepth = (int) 0;
            } elseif (!Validate::isUnsignedInt($maxDepth)) {
                $this->errors[] = $this->module->l('The max depth field is invalid', 'addmpshipping');
            }

            if ($maxWeight == '') {
                $maxWeight = (float) 0;
            } elseif (!Validate::isFloat($maxWeight)) {
                $this->errors[] = $this->module->l('The max weight field is invalid', 'addmpshipping');
            }
            if (empty($this->errors) && Configuration::get('WK_MP_SELLER_SHIPPING')) {
                $mpShippingId = Tools::getValue('mp_shipping_id');
                if (Tools::getValue('submitUpdateshipping')) {
                    $idPsReference = WkMpSellerShipping::getReferenceByMpShippingId($mpShippingId);
                    // Delete Carrier
                    Db::getInstance()->update('carrier', ['deleted' => 1], 'id_reference=' . $idPsReference);
                }
                if ($mpShippingId) {
                    $objWkMpShipping = new WkMpSellerShipping($mpShippingId); // Edit carrier
                    $updateShipping = 1;
                    $oldIdReference = (int) $objWkMpShipping->id_ps_reference;
                } else {
                    $objWkMpShipping = new WkMpSellerShipping(); // Add carrier
                    $updateShipping = 0;
                }
                $objCarrier = new Carrier();

                foreach (Language::getLanguages(true) as $language) {
                    $transitLangId = $language['id_lang'];

                    if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                        // if product name in other language is not available then fill with seller language same for others
                        if (!Tools::getValue('transit_time_' . $language['id_lang'])) {
                            $transitLangId = $defaultLang;
                        }
                    } else {
                        // if multilang is OFF then all fields will be filled as default lang content
                        $transitLangId = $defaultLang;
                    }

                    $objCarrier->delay[$language['id_lang']] = Tools::getValue('transit_time_' . $transitLangId);
                }
                $objCarrier->name = pSQL($shippingName);
                if ($updateShipping) {
                    // If update carrier then check old carrier ID status (active/inactive)
                    $objCarrier->active = (int) WkMpSellerShipping::getOldCarrierReferenceStatus($oldIdReference);
                } else {
                    $objCarrier->active = (int) (Configuration::get('MP_SHIPPING_ADMIN_APPROVE') == 0) ? 1 : 0;
                }
                $objCarrier->url = pSQL($trackingUrl);
                $objCarrier->range_behavior = (int) $rangeBehavior;
                $objCarrier->position = (int) Carrier::getHigherPosition() + 1;
                $objCarrier->shipping_method = (int) $shippingMethod;
                $objCarrier->max_width = (int) $maxWidth;
                $objCarrier->max_height = (int) $maxHeight;
                $objCarrier->max_depth = (int) $maxDepth;
                $objCarrier->max_weight = (float) $maxWeight;
                $objCarrier->grade = (int) $grade;
                $objCarrier->shipping_handling = (int) $shippingHandling;
                $objCarrier->shipping_external = (int) true;
                $objCarrier->external_module_name = pSQL('marketplace');
                $objCarrier->is_module = (int) 1;
                $objCarrier->need_range = (int) 1;

                if ($isFree) {
                    $objCarrier->is_free = (int) 1;
                }
                if ($objCarrier->save()) {
                    $idPsCarrier = $objCarrier->id; // First time idPsCarrier and id_reference both are same
                    $objWkMpShipping->id_seller = (int) $mpIdSeller;
                    $objWkMpShipping->id_ps_reference = (int) isset($oldIdReference) ? $oldIdReference : $idPsCarrier;
                    $objWkMpShipping->save();
                    $mpShippingId = $objWkMpShipping->id;

                    $objWkMpShipping->changeZones($objCarrier, $zoneFees);
                    $objWkMpShipping->updateRange($objCarrier, $rangeInf, $rangeSup, $zoneFees, $mpShippingId);
                    $objCarrier->setTaxRulesGroup($idTaxRuleGroup, true);

                    $objWkMpShipping->updateZoneShop($idPsCarrier, $this->context->shop->id);
                    $objWkMpShipping->changeGroups($idPsCarrier, $shippingGroup);

                    $imgPath = _PS_MODULE_DIR_ . 'marketplace/views/img/mpshipping/' . $mpShippingId . '.jpg';
                    if ($isNewImage) {
                        // New image upload
                        ImageManager::resize($_FILES['shipping_logo']['tmp_name'], $imgPath);
                        if (file_exists($imgPath)) {
                            copy($imgPath, _PS_SHIP_IMG_DIR_ . '/' . $idPsCarrier . '.jpg');
                        }
                    } elseif (file_exists($imgPath)) {
                        // Image already exist on carrier which editing carrier
                        copy($imgPath, _PS_SHIP_IMG_DIR_ . '/' . $idPsCarrier . '.jpg');
                    }

                    if (Tools::getValue('submitAddshipping')) {
                        if ($objCarrier->active) {
                            if (Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL') == 1) {
                                $objWkMpShipping->mailToSeller($mpIdSeller, $mpShippingId, 1);
                            }
                        }

                        // Mail to Admin if configuration set to "YES".
                        if (Configuration::get('MP_MAIL_ADMIN_SHIPPING_ADDED') == 1) {
                            $objWkMpShipping->mailToAdminShippingAdded($mpIdSeller, $mpShippingId);
                        }
                    }
                    WkMpAdminShipping::updatePsShippingDistributionType($idPsCarrier, 'seller');
                }
                $link = new Link();
                if ($updateShipping) {
                    if (isset($oldIdReference) && $oldIdReference) {
                        $newObjCarrier = new Carrier($idPsCarrier);
                        $newObjCarrier->id_reference = (int) $oldIdReference;
                        $newObjCarrier->save();
                    }
                    $addMpShippingListLink = $link->getModuleLink('marketplace', 'mpshippinglist', ['updatempshipping_success' => 1]);
                } else {
                    $addMpShippingListLink = $link->getModuleLink('marketplace', 'addmpshipping', ['mpshipping_id' => $mpShippingId, 'addmpshipping_step4' => 1]);
                }

                Tools::redirect($addMpShippingListLink);
            }
        }
    }

    public function displayAjaxShippingImpact()
    {
        if (!$this->isTokenValid() && (Tools::getAdminToken('shipping') != Tools::getValue('token'))) {
            exit('Something went wrong!');
        }

        $fun = Tools::getValue('fun');
        if ($fun == 'find_country') {
            $idZone = Tools::getValue('id_zone');
            $countryDetail = $this->findCountry($idZone);
            $jsonArrayRev = json_encode($countryDetail);
            echo $jsonArrayRev;
            exit; // ajax close
        } elseif ($fun == 'find_state') {
            $idCountry = Tools::getValue('id_country');
            $stateDetail = $this->findState($idCountry);
            $jsonArrayRev = json_encode($stateDetail);
            echo $jsonArrayRev;
            exit; // ajax close
        } elseif ($fun == 'find_range') {
            $idZone = Tools::getValue('id_zone');
            $idCountry = Tools::getValue('id_country');
            $idState = Tools::getValue('id_state');
            $shippingMethod = Tools::getValue('shipping_method');
            $mpShippingId = Tools::getValue('mpshipping_id');

            $objMpShipping = new WkMpSellerShipping($mpShippingId);
            $objCarrier = Carrier::getCarrierByReference($objMpShipping->id_ps_reference);
            $ranges = $objMpShipping->getCarrierRangeValue($objCarrier);
            if ($shippingMethod == 2) {
                $rangeId = 'id_range_price';
            } else {
                $rangeId = 'id_range_weight';
            }
            if ($ranges) {
                $currentPrice = $this->currentPrice($ranges, $idZone, $idCountry, $idState, $mpShippingId, $rangeId);
                $jsonArrayRev = json_encode($currentPrice);
                echo $jsonArrayRev;
            } else {
                echo 0;
            }
            exit; // ajax close
        } elseif ($fun == 'range_add') {
            $mpShippingId = Tools::getValue('range_mpshipping_id');
            $idZone = Tools::getValue('range_mpshipping_id_zone');
            $idCountry = Tools::getValue('range_mpshipping_id_country');
            $idState = Tools::getValue('range_mpshipping_id_state');
            $shippingMethod = Tools::getValue('range_shipping_method');
            // $idState = 0 for all
            $success = 0;
            $objMpShipping = new WkMpSellerShipping($mpShippingId);
            $objCarrier = Carrier::getCarrierByReference($objMpShipping->id_ps_reference);
            $ranges = $objMpShipping->getCarrierRangeValue($objCarrier);
            if ($shippingMethod == 2) {
                $rangeId = 'id_range_price';
            } else {
                $rangeId = 'id_range_weight';
            }
            if ($ranges) {
                if ($this->impactEntry($ranges, $idZone, $idCountry, $idState, $mpShippingId, $rangeId)) {
                    $success = 1;
                } else {
                    $success = 0;
                }
            } else {
                $success = 0;
            }

            echo $success;
            exit; // ajax close
        }
    }

    public function findCountry($idZone)
    {
        $objShippingImp = new WkMpSellerShippingImpact();

        return $objShippingImp->getCountriesByZoneId($idZone, $this->context->language->id);
    }

    public function findState($idCountry)
    {
        $objShippingImp = new WkMpSellerShippingImpact();

        return $objShippingImp->getStatesByIdCountry($idCountry);
    }

    public function impactEntry($ranges, $idZone, $idCountry, $idState, $mpShippingId, $rangeId)
    {
        $objShippingImp = new WkMpSellerShippingImpact();
        $objShippingImp->mp_shipping_id = $mpShippingId;
        $objShippingImp->id_zone = $idZone;
        $objShippingImp->id_country = $idCountry;
        $objShippingImp->id_state = $idState;

        foreach ($ranges['range'] as $deliveryRange) {
            $shippingDeliveryId = $deliveryRange[$rangeId];
            $objShippingImp->shipping_delivery_id = $shippingDeliveryId;
            $newImpactPrice = Tools::getValue('delivery' . $shippingDeliveryId);
            if (!Validate::isPrice($newImpactPrice)) {
                return false;
            }
            $objShippingImp->impact_price = (float) $newImpactPrice;

            $isExistImpact = $objShippingImp->isAllReadyInImpact($mpShippingId, $shippingDeliveryId, $idZone, $idCountry, $idState);
            if ($isExistImpact) {
                $objShippingImp->id = $isExistImpact['id_wk_mp_shipping_impact'];
                $objShippingImp->save();
            } else {
                $objShippingImp->add();
            }
        }

        return true;
    }

    // find current impact price by zone and delivery method
    public function currentPrice($ranges, $idZone, $idCountry, $idState, $mpShippingId, $rangeId)
    {
        $currentPriceArray = [];
        foreach ($ranges['range'] as $deliveryRange) {
            $shippingDeliveryId = $deliveryRange[$rangeId];
            $delimiter1 = Tools::ps_round($deliveryRange['delimiter1'], 2);
            $delimiter2 = Tools::ps_round($deliveryRange['delimiter2'], 2);
            $idRange = $deliveryRange['id_range'];
            if ($idRange) {
                $mpShippingImpact = new WkMpSellerShippingImpact();
                $isInImpact = $mpShippingImpact->isAllReadyInImpact($mpShippingId, $shippingDeliveryId, $idZone, $idCountry, $idState);

                if ($isInImpact) {
                    $impactPrice = $isInImpact['impact_price'];
                } else {
                    $impactPrice = 0;
                }

                $currentPriceArray[] = ['id' => $shippingDeliveryId, 'delimiter1' => $delimiter1, 'delimiter2' => $delimiter2, 'id_range' => $idRange, 'impact_price' => $impactPrice];
            }
        }

        return $currentPriceArray;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = [];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Marketplace', 'addmpshipping'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        ];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Carriers', 'addmpshipping'),
            'url' => $this->context->link->getModuleLink('marketplace', 'mpshippinglist'),
        ];

        if (Tools::getValue('updateimpact') && Tools::getValue('mpshipping_id')) {
            $breadcrumb['links'][] = [
                'title' => $this->module->l('Impact Price', 'addmpshipping'),
                'url' => '',
            ];
        } elseif (Tools::getValue('mpshipping_id')) {
            $breadcrumb['links'][] = [
                'title' => $this->module->l('Update Carrier', 'addmpshipping'),
                'url' => '',
            ];
        } else {
            $breadcrumb['links'][] = [
                'title' => $this->module->l('Add Carrier', 'addmpshipping'),
                'url' => '',
            ];
        }

        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addjQueryPlugin('growl', null, false);
        $this->registerJavascript(
            'addmpshipping',
            'modules/' . $this->module->name . '/views/js/shipping/addmpshipping.js'
        );
        $this->registerJavascript(
            'mpshippinglistjs',
            'modules/' . $this->module->name . '/views/js/shipping/mpshippinglist.js'
        );
        $this->registerStylesheet(
            'mpshippinglistcss',
            'modules/' . $this->module->name . '/views/css/shipping/mpshippinglist.css'
        );
        $this->registerStylesheet(
            'addmpshippingcss',
            'modules/' . $this->module->name . '/views/css/shipping/addmpshipping.css'
        );
        $this->registerStylesheet(
            'marketplace_account',
            'modules/' . $this->module->name . '/views/css/marketplace_account.css'
        );

        return true;
    }
}
