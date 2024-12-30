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

class AdminMpSellerShippingController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->table = 'wk_mp_seller_shipping';
        $this->className = 'WkMpSellerShipping';
        $this->list_no_link = true;
        $this->identifier = 'id_wk_mp_shipping';
        parent::__construct();
        $this->toolbar_title = $this->l('Carriers');

        $this->_select = 'a.*, c.*, cl.*, a.`id_wk_mp_shipping` as `seller_carrier_id`, ';

        $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'carrier` c ON (c.`id_reference` = a.`id_ps_reference`)';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'carrier_lang` cl ON (cl.`id_carrier` = c.`id_carrier`)';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'carrier_shop` cs ON (cs.`id_carrier` = c.`id_carrier`)';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_mp_seller` mpsi ON (a.`id_seller` = mpsi.`id_seller`)';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_mp_seller_lang` mpsil ON (mpsil.`id_seller` = mpsi.`id_seller` AND mpsil.`id_lang` = ' . (int) $this->context->language->id . ')';
        $this->_select .= 'CONCAT(mpsi.`seller_firstname`, " ", mpsi.`seller_lastname`) as `seller_name`,mpsi.`shop_name_unique`, a.`id_wk_mp_shipping` as `ship_id`, cl.`delay`';
        $this->_where .= ' AND c.`deleted` = 0 AND cl.`id_lang` = ' . (int) $this->context->language->id;
        $this->_where .= WkMpSeller::addSqlRestriction('mpsi');
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            // In case of All Shops
            $this->_select .= ', shp.`name` as wk_ps_shop_name';
            $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'shop` shp ON (shp.`id_shop` = cs.`id_shop`)';
        } else {
            $this->_where .= ' AND cs.`id_shop` = ' . (int) $this->context->shop->id;
        }
        $this->_group = 'GROUP BY c.`id_carrier`';
        $this->fields_list = [
            'id_wk_mp_shipping' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'id_carrier' => [
                'title' => $this->l('Prestashop carrier ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'filter_key' => 'c!id_carrier',
                'hint' => $this->l('Generated prestashop carrier ID in carriers'),
            ],
            'seller_carrier_id' => [
                'title' => $this->l('Image'),
                'callback' => 'displayCarrierImage',
                'search' => false,
                'havingFilter' => true,
            ],
            'name' => [
                'title' => $this->l('Carrier name'),
                'align' => 'center',
                'havingFilter' => true,
            ],
            'seller_name' => [
                'title' => $this->l('Seller name'),
                'align' => 'center',
                'havingFilter' => true,
            ],
            'shop_name_unique' => [
                'title' => $this->l('Unique shop name'),
                'align' => 'center',
                'havingFilter' => true,
            ],
            'active' => [
                'title' => $this->l('Status'),
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'c!active',
            ],
            'ship_id' => [
                'title' => $this->l('Assign impact price'),
                'width' => 35,
                'align' => 'center',
                'callback' => 'assignImpact',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true,
            ],
        ];
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            // In case of All Shops
            $this->fields_list['wk_ps_shop_name'] = [
                'title' => $this->l('Shop'),
                'havingFilter' => true,
                'orderby' => false,
            ];
        }

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash',
            ],
        ];

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
    }

    public function displayCarrierImage($idMpShipping, $rowData)
    {
        $imageLink = _MODULE_DIR_ . 'marketplace/views/img/home-default.jpg';
        if ($rowData['id_carrier']) {
            $carrierLogo = _PS_SHIP_IMG_DIR_ . (int) $rowData['id_carrier'] . '.jpg';
            if (file_exists($carrierLogo)) {
                $imageLink = _THEME_SHIP_DIR_ . (int) $rowData['id_carrier'] . '.jpg';
            }
        }

        $this->context->smarty->assign([
            'callback' => 'displayCarrierImage',
            'image_link' => $imageLink,
        ]);

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/wk_admin_callback.tpl'
        );
    }

    public function assignImpact($mpShippingId, $row)
    {
        if ($row['is_free']) {
            $html = $this->l('Free');
        } else {
            $this->context->smarty->assign([
                'callback' => 'assignImpact',
                'sellerShippingCurrentIndex' => self::$currentIndex,
                'sellerShippingToken' => $this->token,
                'mpShippingId' => $mpShippingId,
            ]);

            $html = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/wk_admin_callback.tpl');
        }

        return $html;
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = [
            'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
            'desc' => $this->l('Add new'),
        ];
    }

    public function renderForm()
    {
        $link = new Link();
        // $idLang = $this->context->language->id;
        $objMpSellerInfo = new WkMpSeller();
        // get total zone available in prestashop
        $zoneDetail = Zone::getZones(false, true);
        $this->context->smarty->assign('zones', $zoneDetail);
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

        // Get customer group
        if ($customerAllGroups = Group::getGroups($this->context->language->id)) {
            $this->context->smarty->assign('customerAllGroups', $customerAllGroups);
        }

        $mpShippingId = Tools::getValue('id_wk_mp_shipping');
        if ($mpShippingId) {
            $objWkMpShipping = new WkMpSellerShipping($mpShippingId);
            $objCarrier = Carrier::getCarrierByReference($objWkMpShipping->id_ps_reference);

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

                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminMpSellerShipping') .
                    '&updatewk_mp_seller_shipping=&id_wk_mp_shipping=' . $mpShippingId . '&delete_success=1'
                );
            }

            $this->context->smarty->assign(
                'wk_delete_logo_path',
                $this->context->link->getAdminLink('AdminMpSellerShipping') .
                '&updatewk_mp_seller_shipping=&id_wk_mp_shipping=' . $mpShippingId . '&delete_logo=1'
            );

            // Edit carrier
            $carrierZones = array_column($objCarrier->getZones(), 'id_zone');
            $this->context->smarty->assign('wk_carrier_zones', $carrierZones);
            $this->context->smarty->assign('mp_shipping_id', $mpShippingId);
            $this->context->smarty->assign('mp_shipping_name', $objCarrier->name);
            // tax options for seller carrier
            $this->context->smarty->assign('tax_rules', TaxRulesGroup::getTaxRulesGroups(true));
            $this->context->smarty->assign('id_tax_rule_group', $objCarrier->getIdTaxRulesGroup());
            // end
            $this->context->smarty->assign('range_behavior', $objCarrier->range_behavior);
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
            $this->context->smarty->assign('mpShippingActive', $objCarrier->active);

            $mpIdSeller = $objWkMpShipping->id_seller;
            $sellerCustomerId = $objMpSellerInfo->getCustomerIdBySellerId($mpIdSeller);
            $this->context->smarty->assign('seller_customer_id', $sellerCustomerId);

            $objMpSeller = new WkMpSeller($mpIdSeller);
            if (!in_array($objMpSeller->id_shop_group, Shop::getContextListShopID())) {
                // For shop group
                $this->errors[] = $this->l('You can not add or edit a carriers in this shop context: carrier does not belongs to current shop context.');

                return;
            }

            // Get carrier group
            if ($shippingGroup = $objCarrier->getGroups()) {
                $this->context->smarty->assign('shippingGroup', array_column($shippingGroup, 'id_group'));
            }

            // @shippingMethod==1 billing accroding to weight
            // @shippingMethod==2 billing accroding to price
            $shippingMethod = $objCarrier->shipping_method;
            $ranges = $objWkMpShipping->getCarrierRangeValue($objCarrier);

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

            if (Tools::getValue('updateimpact') == '1') {
                if ($objCarrier->is_free) {
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminMpSellerShipping'));
                } else {
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

                    $shippingAjaxLink = $link->getModuleLink('marketplace', 'addmpshipping');
                    $this->context->smarty->assign('mpshipping_id', $mpShippingId);
                    $this->context->smarty->assign('shipping_ajax_link', $shippingAjaxLink);
                    $this->context->smarty->assign('updateimpact', 1);

                    $jsDefVar = [
                        'shipping_ajax_link' => $shippingAjaxLink,
                        'img_ps_dir' => _MODULE_DIR_ . 'marketplace/views/img/',
                        'select_country' => $this->l('Select country'),
                        'select_state' => $this->l('All'),
                        'zone_error' => $this->l('Select zone'),
                        'no_range_available_error' => $this->l('No range available.'),
                        'ranges_info' => $this->l('Ranges'),
                        'message_impact_price_error' => $this->l('Impact price is invalid.'),
                        'message_impact_price' => $this->l('Impact added sucessfully.'),
                    ];

                    Media::addJsDef($jsDefVar);
                }
            }
        } else {
            $shippingMethod = 2;
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
            $this->context->smarty->assign('mpShippingActive', 0);
            // for tax options seller carrier
            $this->context->smarty->assign('tax_rules', TaxRulesGroup::getTaxRulesGroups(true));
            // seller customer information
            $customerInfo = WkMpSeller::getAllSeller();
            if ($customerInfo) {
                if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
                    // In case of All Shops
                    foreach ($customerInfo as &$customer) {
                        if (isset($customer['id_shop']) && $customer['id_shop']) {
                            $objShop = new Shop($customer['id_shop']);
                            $customer['ps_shop_name'] = $objShop->name;
                        } else {
                            $customer['ps_shop_name'] = '';
                        }
                    }
                    $this->context->smarty->assign('all_shop', 1);
                } else {
                    $this->context->smarty->assign('all_shop', 0);
                }

                $this->context->smarty->assign('customer_info', $customerInfo);

                // get first seller from the list
                $firstSellerDetails = $customerInfo[0];
                $mpIdSeller = $firstSellerDetails['id_seller'];
            } else {
                $mpIdSeller = 0;
            }
        }
        // Multi-lang start
        $adminProductUrl = $this->context->link->getAdminLink('AdminSellerProductDetail');
        $this->context->smarty->assign('adminproducturl', $adminProductUrl);
        // Set default lang at every form according to configuration multi-language
        WkMpHelper::assignDefaultLang($mpIdSeller);
        // Multilang end

        if (!$mpIdSeller) {
            $currLang = Configuration::get('PS_LANG_DEFAULT');
            $this->context->smarty->assign('current_lang', Language::getLanguage((int) $currLang));
        }
        $updateImpactLink = $this->context->link->getAdminLink('AdminMpSellerShipping') . '&id_wk_mp_seller_shipping=' . $mpShippingId . '&updatewk_mp_seller_shipping&updateimpact=1';
        $this->context->smarty->assign('update_impact_link', $updateImpactLink);
        $this->context->smarty->assign('modules_dir', _MODULE_DIR_);
        $this->context->smarty->assign('img_ps_dir', _MODULE_DIR_ . 'marketplace/views/img/');
        $this->context->smarty->assign('self', dirname(__FILE__));
        $this->context->smarty->assign('currency_sign', $currency->sign);
        $this->context->smarty->assign('PS_WEIGHT_UNIT', Configuration::get('PS_WEIGHT_UNIT'));
        $this->context->smarty->assign('isAdminAddCarrier', 1);
        $this->fields_form = [
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('statuswk_mp_seller_shipping')) {
            $this->toggleStatus();
            Tools::redirectAdmin(self::$currentIndex . '&conf=5&token=' . $this->token);
        } elseif (Tools::isSubmit('deletewk_mp_seller_shipping')) {
            $this->deleteShipping();
            Tools::redirectAdmin(self::$currentIndex . '&conf=1&token=' . $this->token);
        }
        // Delete impact price
        if (Tools::getValue('deleteimpact')) {
            $mpShippingId = Tools::getValue('id_wk_mp_shipping');
            $impactId = Tools::getValue('impact_id');
            if ($impactId) {
                $objMpShipImpact = new WkMpSellerShippingImpact($impactId);
                $objMpShipImpact->delete();
            }
            Tools::redirectAdmin(self::$currentIndex . '&id_wk_mp_shipping=' . $mpShippingId . '&updatewk_mp_seller_shipping&updateimpact=1&conf=1&token=' . $this->token);
        }

        if (Tools::isSubmit('FinishButtonclick')) {
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

            // If multi-lang is OFF then PS default lang will be default lang for seller from Marketplace Configuration page
            $defaultLang = 0;
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $defaultLang = Tools::getValue('current_lang');
            } else {
                if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') { // Admin Default lang
                    $defaultLang = Configuration::get('PS_LANG_DEFAULT');
                } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') { // Seller Default lang
                    $defaultLang = Tools::getValue('current_lang');
                }
            }

            $sellerCustomerId = Tools::getValue('seller_customer_id');
            /* $obj_seller_info = new WkMpSeller(); */
            $mpCustomerInfo = WkMpSeller::getSellerDetailByCustomerId($sellerCustomerId);
            if ($mpCustomerInfo && $mpCustomerInfo['id_seller']) {
                $mpIdSeller = $mpCustomerInfo['id_seller'];
                if (!$mpCustomerInfo['active']) {
                    $this->errors[] = $this->l('Selected seller is not active.');
                }
            } else {
                $mpIdSeller = 0;
            }

            if (!$mpIdSeller) {
                $this->errors[] = $this->l('Selected customer is not a seller.');
            }

            if (!$shippingName) {
                $this->errors[] = $this->l('Carrier name is required.');
            } elseif (!$isValidShippingName) {
                $this->errors[] = $this->l('Carrier name must not have Invalid characters /^[^<>;=#{}]*$/u');
            } elseif (Tools::strlen($shippingName) > 64) {
                $this->errors[] = $this->l('Carrier name field is too long (64 chars max).');
            }

            if (!trim(Tools::getValue('transit_time_' . $defaultLang))) {
                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    $sellerLangArr = Language::getLanguage((int) $defaultLang);
                    $this->errors[] = $this->l('Transit time is required in ') . $sellerLangArr['name'];
                } else {
                    $this->errors[] = $this->l('Transit time is required');
                }
            } else {
                foreach (Language::getLanguages() as $language) {
                    $languageName = '';
                    if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                        $languageName = $language['name'];
                    }

                    if (!Validate::isCatalogName(Tools::getValue('transit_time_' . $language['id_lang']))) {
                        $this->errors[] = $this->l('Transit time must not have invalid characters /^[^<>={}]*$/u in ') . $languageName;
                    }
                }
            }

            if (!$isValidGrade) {
                $this->errors[] = $this->l('Speed grade must be numeric');
            } elseif ($grade < 0 || $grade > 9) {
                $this->errors[] = $this->l('Speed grade must be from 0 to 9');
            }

            if (!$isValidTrackingUrl) {
                $this->errors[] = $this->l('Invalid Tracking Url');
            }

            // if carrier is not free then range is mandatory
            if (Tools::getValue('is_free') == 0) {
                $rangeInf = Tools::getValue('range_inf');
                $rangeSup = Tools::getValue('range_sup');
                if (isset($rangeInf[0]) && $rangeInf[0] == '') {
                    $this->errors[] = $this->l('Shipping charge lower limit should not blank');
                }
                if (isset($rangeSup[0]) && $rangeSup[0] == '') {
                    $this->errors[] = $this->l('Shipping charge upper limit should not blank');
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
                        $this->errors[] = $this->l('Only jpg,png,jpeg image allow and image size should not exceed 125*125');
                    } else {
                        list($width, $height) = getimagesize($_FILES['shipping_logo']['tmp_name']);
                        if ($width > 125 || $height > 125) {
                            $this->errors[] = $this->l('Only jpg,png,jpeg image allow and image size should not exceed 125*125');
                        }
                        $isNewImage = true;
                    }
                }
            }

            if ($maxHeight == '') {
                $maxHeight = (int) 0;
            } elseif (!Validate::isUnsignedInt($maxHeight)) {
                $this->errors[] = $this->l('The max height field is invalid.');
            }

            if ($maxWidth == '') {
                $maxWidth = (int) 0;
            } elseif (!Validate::isUnsignedInt($maxWidth)) {
                $this->errors[] = $this->l('The max width field is invalid.');
            }

            if ($maxDepth == '') {
                $maxDepth = (int) 0;
            } elseif (!Validate::isUnsignedInt($maxDepth)) {
                $this->errors[] = $this->l('The max depth field is invalid.');
            }

            if ($maxWeight == '') {
                $maxWeight = (float) 0;
            } elseif (!Validate::isFloat($maxWeight)) {
                $this->errors[] = $this->l('The max weight field is invalid.');
            }

            if (empty($this->errors)) {
                $mpShippingId = Tools::getValue('mpshipping_id');
                $mpShippingActive = Tools::getValue('mpShippingActive');
                if (Tools::getIsset('update' . $this->table)) {
                    $idPsReference = WkMpSellerShipping::getReferenceByMpShippingId($mpShippingId);
                    // Delete Carrier
                    Db::getInstance()->update('carrier', ['deleted' => 1], 'id_reference=' . $idPsReference);
                }
                if ($mpShippingId) {
                    $objWkMpShipping = new WkMpSellerShipping($mpShippingId); // Edit carrier
                    $oldIdReference = (int) $objWkMpShipping->id_ps_reference;
                } else {
                    $objWkMpShipping = new WkMpSellerShipping(); // Add carrier
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
                $objCarrier->active = (int) $mpShippingActive;
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
                $objCarrier->need_range = 1;

                if ($isFree) {
                    $objCarrier->is_free = (int) 1;
                }
                if ($objCarrier->save()) {
                    $idPsCarrier = $objCarrier->id; // First time idPsCarrier and id_reference both are same
                    $objWkMpShipping->id_seller = $mpIdSeller;
                    $objWkMpShipping->id_ps_reference = isset($oldIdReference) ? $oldIdReference : $idPsCarrier;
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

                    if (isset($oldIdReference) && $oldIdReference) {
                        $newObjCarrier = new Carrier($idPsCarrier);
                        $newObjCarrier->id_reference = (int) $oldIdReference;
                        $newObjCarrier->save();
                    }
                    WkMpAdminShipping::updatePsShippingDistributionType($idPsCarrier, 'seller');
                }
                if (isset($oldIdReference)) {
                    Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
                } else {
                    Tools::redirectAdmin(self::$currentIndex . '&conf=3&token=' . $this->token);
                }
            }
        }
        parent::postProcess();
    }

    public function processBulkStatusSelection($status)
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $bulkId) {
                $this->bulkUpdate($status, $bulkId);
            }
            Tools::redirectAdmin(self::$currentIndex . '&conf=5&token=' . $this->token);
        }

        return parent::processBulkStatusSelection($status);
    }

    public function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $bulkId) {
                $this->deleteShipping($bulkId);
            }
            Tools::redirectAdmin(self::$currentIndex . '&conf=2&token=' . $this->token);
        }

        return parent::processBulkDelete();
    }

    public function renderView()
    {
        return parent::renderView();
    }

    public function ajaxProcessUpdateCarrierToMainProducts()
    {
        $idLang = $this->context->language->id;
        $objCarr = new Carrier();
        $carrDetails = $objCarr->getCarriers($idLang, true);
        if (empty($carrDetails)) {
            $json = ['status' => 'ko', 'msg' => $this->l('No Carriers available')];
            echo json_encode($json);
        } else {
            $this->assignCarriersToMainProduct($idLang);
            $json = ['status' => 'ok', 'msg' => $this->l('Carriers assigned successfully.')];
            echo json_encode($json);
        }
        exit; // ajax close
    }

    public function assignCarriersToMainProduct($idLang)
    {
        $objShipMap = new WkMpSellerShipping();

        $start = 0;
        $limit = 0;
        $orderBy = 'id_product';
        $orderWay = 'ASC';

        $carrRef = [];
        $allPsCarriersOnly = WkMpSellerShipping::getOnlyPrestaCarriers($idLang);
        if ($allPsCarriersOnly) {
            foreach ($allPsCarriersOnly as $psCarriers) {
                $carrRef[] = $psCarriers['id_reference'];
            }
        }

        $psProdInfo = Product::getProducts($idLang, $start, $limit, $orderBy, $orderWay, false, true);
        foreach ($psProdInfo as $product) {
            if (!$objShipMap->checkMpProduct($product['id_product'])) {
                $objShipMap->setProductCarrier($product['id_product'], $carrRef);
            }
        }
    }

    public function bulkUpdate($status, $bulkId)
    {
        if ($bulkId) {
            $mpShippingId = $bulkId;
        } else {
            $mpShippingId = Tools::getValue('id_wk_mp_shipping');
        }

        $objMpShippingMet = new WkMpSellerShipping($mpShippingId);

        $idPsReference = WkMpSellerShipping::getReferenceByMpShippingId($mpShippingId);
        if ($idPsReference) {
            $objCarrier = Carrier::getCarrierByReference($idPsReference);
            if ($objCarrier->active == 1 && $status == 0) { // going to deactive
                // remove from default carrier of seller
                WkMpSellerShipping::updateDefaultShipping($mpShippingId, 0);

                $objCarrier->active = 0;
                if ($objCarrier->is_free) {
                    $objCarrier->is_free = 1;
                }
                $objCarrier->save();

                if (Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL') == 1) {
                    $objMpShippingMet->mailToSeller($objMpShippingMet->id_seller, $mpShippingId, 0);
                }

                /* When deactivate any seller shipping method then check if only this shipping method is applied on the sellers product or all deactive shippings are applied on the product then default chosen shippings by admin should be applied on those products */
                $objMpShippingMet = new WkMpSellerShipping();
                $objMpShippingMet->updateCarriersOnDeactivateOrDelete();
            /* END */
            } else { // going to active
                if ($objCarrier->is_free) {
                    $objCarrier->is_free = 1;
                }
                $objCarrier->active = 1;
                $objCarrier->save();

                if (Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL') == 1) {
                    $objMpShippingMet->mailToSeller($objMpShippingMet->id_seller, $mpShippingId, 1);
                }
            }
        }
    }

    public function toggleStatus($bulkId = false)
    {
        if ($bulkId) {
            $mpShippingId = $bulkId;
        } else {
            $mpShippingId = Tools::getValue('id_wk_mp_shipping');
        }

        $objMpShippingMet = new WkMpSellerShipping($mpShippingId);

        $idPsReference = WkMpSellerShipping::getReferenceByMpShippingId($mpShippingId);
        if ($idPsReference) {
            $objCarrier = Carrier::getCarrierByReference($idPsReference);
            if ($objCarrier->active == 1) { // going to deactive
                // remove from default carrier of seller
                WkMpSellerShipping::updateDefaultShipping($mpShippingId, 0);

                $objCarrier->active = 0;
                if ($objCarrier->is_free) {
                    $objCarrier->is_free = 1;
                }
                $objCarrier->save();

                if (Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL') == 1) {
                    $objMpShippingMet->mailToSeller($objMpShippingMet->id_seller, $mpShippingId, 0);
                }

                /* When deactivate any seller shipping method then check if only this shipping method is applied on the sellers product or all deactive shippings are applied on the product then default chosen shippings by admin should be applied on those products */
                $objMpShippingMet = new WkMpSellerShipping();
                $objMpShippingMet->updateCarriersOnDeactivateOrDelete();
            /* END */
            } else { // going to active
                $objCarrier = Carrier::getCarrierByReference($idPsReference);
                if ($objCarrier->is_free) {
                    $objCarrier->is_free = 1;
                }
                $objCarrier->active = 1;
                $objCarrier->save();

                if (Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL') == 1) {
                    $objMpShippingMet->mailToSeller($objMpShippingMet->id_seller, $mpShippingId, 1);
                }
            }
        }
    }

    public function deleteShipping($bulkId = false)
    {
        if ($bulkId) {
            $mpShippingId = $bulkId;
        } else {
            $mpShippingId = Tools::getValue('id_wk_mp_shipping');
        }

        // delete carrier all data
        $objMpShipping = new WkMpSellerShipping();
        $objMpShipping->deleteMpShipping($mpShippingId);

        /* Assign new selected shipping methods to the seller produccts which have no seller shipping methods */
        $objMpShipping->updateCarriersOnDeactivateOrDelete();
        /* END */
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        Media::addJsDef([
            'impact_confirm_msg' => $this->l('Are you sure?'),
            'allowed_file_size' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
            'wk_static_token' => Tools::getAdminToken('shipping'),
        ]);
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/shipping/style.css');
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/shipping/mpshippinglist.css');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/shipping/fieldform.js');
        $this->addJs(_MODULE_DIR_ . $this->module->name . '/views/js/shipping/addmpshipping.js');
    }
}
