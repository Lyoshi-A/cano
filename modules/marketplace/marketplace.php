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
require_once dirname(__FILE__) . '/classes/WkMpRequiredClasses.php';
if (Module::isEnabled('mpsellerstaff')) {
    include_once dirname(__FILE__) . '/../mpsellerstaff/classes/WkMpStaffRequiredClasses.php';
}
class Marketplace extends CarrierModule
{
    public static $mpController = true;
    public $id_carrier;
    public $secure_key;
    public $sellerDetailsView = [
        ['id_group' => 1],
        ['id_group' => 2],
        ['id_group' => 3],
        ['id_group' => 4],
        ['id_group' => 5],
        ['id_group' => 6],
        ['id_group' => 7],
        ['id_group' => 8],
        ['id_group' => 9],
    ];

    public function __construct()
    {
        $this->name = 'marketplace';
        $this->tab = 'market_place';
        $this->version = '7.0.1';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->secure_key = Tools::hash($this->name); // encrypt() deprecated in PS 1.7, use hash()
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->bootstrap = true;
        $this->controllers = [
            'addfeaturevalue',
            'addproduct',
            'addmpshipping',
            'allreviews',
            'dashboard',
            'editprofile',
            'managecombination',
            'mporder',
            'mporderdetails',
            'mppayment',
            'mptransaction',
            'productdetails',
            'productlist',
            'sellerprofile',
            'sellerrequest',
            'shopstore',
            'updateproduct',
            'createattribute',
            'createattributevalue',
            'productattribute',
            'viewattributegroupvalue',
            'addfeaturevalue',
            'createfeature',
            'productfeature',
            'viewfeaturevalue',
            'generatecombination',
            'manufacturerproductlist',
            'mpaddsupplier',
            'mpcreatemanufacturers',
            'mpmanufacturerlist',
            'mpshippinglist',
            'mpsupplierlist',
            'mpsupplierproductslist',
            'mpupdatesupplier',
            'productimageedit',
            'mpsupplierlist',
        ];
        parent::__construct();
        $this->displayName = $this->l('Marketplace');
        $this->description = $this->l('Turn your Prestashop store into a marketplace where sellers can add products, manage orders, manage profile, shop, product name and descriptions in multi-language.');
        $this->confirmUninstall = $this->l('Are you sure? All module data will be lost after uninstalling the module');

        $this->sellerDetailsView[0]['name'] = $this->l('Seller name');
        $this->sellerDetailsView[1]['name'] = $this->l('Seller email');
        $this->sellerDetailsView[2]['name'] = $this->l('Seller phone & fax');
        $this->sellerDetailsView[3]['name'] = $this->l('Address');
        $this->sellerDetailsView[4]['name'] = $this->l('About shop');
        $this->sellerDetailsView[5]['name'] = $this->l('Social profile');
        $this->sellerDetailsView[6]['name'] = $this->l('Contact seller link');
        $this->sellerDetailsView[7]['name'] = $this->l('Shop products');
        $this->sellerDetailsView[8]['name'] = $this->l('Shop name on product page');
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminMarketplaceGeneralSettings'));
    }

    /**
     * empty definition for registerGDPRConsent hook
     */
    public function hookRegisterGDPRConsent()
    {
        // registerGDPRConsent is a special kind of hook that doesn't need a definition.
    }

    // If customer is getting delete then we are updating seller with anonymous information
    public function hookActionDeleteGDPRCustomer($customer)
    {
        $objSeller = new WkMpSeller();
        $sellerInfo = WkMpSeller::getSellerByCustomerId($customer['id']);
        if ($sellerInfo) {
            $result = $objSeller->updateSellerInformation($sellerInfo, $customer['email']);
            if (!$result) {
                return json_encode($this->l('Unable to delete seller information.'));
            }
        }
    }

    // Showing seller information based on customer ID
    public function hookActionExportGDPRData($customer)
    {
        if (!Tools::isEmpty($customer['email']) && Validate::isEmail($customer['email'])) {
            $objSeller = new WkMpSeller();
            if ($res = $objSeller->exportSellerInformation($customer['id'])) {
                return json_encode($res);
            }

            return json_encode($this->l('Seller information not exist.'));
        }
    }

    public function hookDisplayProductPriceBlock($params)
    {
        if (Tools::getValue('controller') === 'cart' && $params['type'] === 'unit_price') {
            if ($idProduct = $params['product']['id_product']) {
                // if it is seller product
                if ($sellerProduct = WkMpSellerProduct::getSellerProductByPsIdProduct($idProduct)) {
                    $mpIdSeller = $sellerProduct['id_seller'];
                    if ($seller = WkMpSeller::getSeller($mpIdSeller, $this->context->language->id)) {
                        $this->context->smarty->assign([
                            'seller_shop_name' => $seller['shop_name'],
                            'shopstore_link' => $this->context->link->getModuleLink(
                                'marketplace',
                                'shopstore',
                                ['mp_shop_name' => $seller['link_rewrite']]
                            ),
                        ]);

                        return $this->fetch('module:marketplace/views/templates/hook/product-shop-name.tpl');
                    }
                }
            }
        }
    }

    public function sellersOrderMail(
        $mpOrderDetail,
        $customer,
        $address,
        $idCurrency,
        $idSeller,
        $idOrder
    ) {
        $order = new Order($idOrder);
        $idCurrency = (int) $order->id_currency;

        // Format price
        foreach ($mpOrderDetail['product_list'] as &$attributeList) {
            foreach ($attributeList as &$customizeList) {
                foreach ($customizeList as &$product) {
                    $product['unit_price_tax_excl'] = WkMpHelper::displayPrice($product['unit_price_tax_excl']);
                    $product['unit_price_tax_incl'] = WkMpHelper::displayPrice($product['unit_price_tax_incl']);
                    $product['total_price_tax_incl'] = WkMpHelper::displayPrice($product['total_price_tax_incl']);
                }
            }
        }

        $objMpSeller = new WkMpSeller($idSeller);
        $productHTML = $objMpSeller->getMpEmailTemplateContent(
            'mp_order_product_list.tpl',
            Mail::TYPE_HTML,
            $mpOrderDetail['product_list']
        );

        $currency = new Currency($idCurrency);
        $templateVars = [
            '{order_reference}' => $order->reference,
            '{seller_name}' => $mpOrderDetail['seller_name'],
            '{customer_name}' => $customer->firstname . ' ' . $customer->lastname,
            '{customer_email}' => $customer->email,
            '{delivery_block_html}' => AddressFormat::generateAddress($address, [], '<br>'),
            '{seller_product_total}' => WkMpHelper::displayPrice($mpOrderDetail['total_earn_ti'], $currency, false),
            '{seller_shipping}' => '',
            '{final_total_price}' => WkMpHelper::displayPrice($mpOrderDetail['total_price_tax_incl'], $currency, false),
            '{product_html}' => $productHTML,
            '{voucher_html}' => '',
        ];

        $voucherInfo = WkMpSellerOrderDetail::setVoucherDetails($idOrder, $idSeller, $idCurrency);
        if ($voucherInfo) {
            Context::getContext()->smarty->assign([
                'voucher_details_label' => $this->l('Voucher Details'),
                'discount_name_label' => $this->l('Discount Name'),
                'value_label' => $this->l('Value'),
            ]);
            $voucherHTML = $objMpSeller->getMpEmailTemplateContent(
                'mp_order_voucher_detail.tpl',
                Mail::TYPE_HTML,
                $voucherInfo
            );
            $templateVars['{voucher_html}'] = $voucherHTML;
        }

        if ($sellerShipping = WkMpAdminShipping::getSellerShippingByIdOrder(
            $idOrder,
            $objMpSeller->seller_customer_id
        )) {
            Context::getContext()->smarty->assign([
                'shipping_lable' => $this->l('Shipping'),
            ]);
            $shippingInfo = $objMpSeller->getMpEmailTemplateContent(
                'mp_shipping_detail.tpl',
                Mail::TYPE_HTML,
                WkMpHelper::displayPrice($sellerShipping, $currency, false)
            );
            if ($shippingInfo) {
                $shippingInfo['shipping_lable'] = $this->l('Shipping');
            }
            $templateVars['{seller_shipping}'] = $shippingInfo;
            $templateVars['{final_total_price}'] = WkMpHelper::displayPrice(
                $mpOrderDetail['total_price_tax_incl'] + $sellerShipping,
                $currency,
                false
            );
        }

        if (Configuration::get('WK_MP_SUPERADMIN_EMAIL')) {
            $adminEmail = Configuration::get('WK_MP_SUPERADMIN_EMAIL');
        } else {
            $idEmployee = WkMpHelper::getSupperAdmin();
            $employee = new Employee($idEmployee);
            $adminEmail = $employee->email;
        }

        $fromTitle = Configuration::get('WK_MP_FROM_MAIL_TITLE');
        $to = $mpOrderDetail['seller_email'];
        Mail::Send(
            $mpOrderDetail['seller_default_lang_id'],
            'mp_order',
            Mail::l('Order Created', $mpOrderDetail['seller_default_lang_id']),
            $templateVars,
            $to,
            $mpOrderDetail['seller_name'],
            $adminEmail,
            $fromTitle,
            null,
            null,
            _PS_MODULE_DIR_ . 'marketplace/mails/',
            false,
            null,
            null
        );
    }

    public function hookDisplayAdminOrder($params)
    {
        if (isset($params['id_order'])) {
            $idOrder = $params['id_order'];
            $mpOrderDetails = new WkMpSellerOrderDetail();
            if ($mpOrders = $mpOrderDetails->getProductsFromOrder($idOrder)) {
                $order = new Order($idOrder);
                $sellerOrderDetail = [];
                foreach ($mpOrders as &$detail) {
                    $sellerProduct = WkMpSellerProduct::getSellerProductByPsIdProduct($detail['product_id']);
                    if ($sellerProduct) {
                        $detail['id_mp_product'] = $sellerProduct['id_mp_product'];
                    }

                    $detail['tracking_url'] = WkMpSellerOrder::replaceTrackingURL(
                        $detail['tracking_number'],
                        $detail['tracking_url']
                    );
                    if (Configuration::get('WK_MP_SELLER_SHIPPING')) {
                        $objCarrier = new Carrier($order->id_carrier);
                        $objMpShipping = new WkMpSellerShipping();
                        if ($objMpShipping->isSellerShippingByIdReference($objCarrier->id_reference)) {
                            if ($shippingNumber = $order->getShippingNumber()) {
                                $this->context->smarty->assign('shipping_number', $shippingNumber);
                            }
                        }
                    }

                    $sellerOrderDetail[$detail['id_seller']][] = $detail;
                }
                $this->context->smarty->assign([
                    'wk_ps_version' => _PS_VERSION_,
                    'seller_order_details' => $mpOrders,
                    'mp_seller_order_details' => $sellerOrderDetail,
                    'link' => $this->context->link,
                    'currentState' => new OrderState($order->getCurrentState(), Configuration::get('PS_LANG_DEFAULT')),
                ]);

                return $this->display(__FILE__, 'admin-order-view-seller-details.tpl');
            }
        }
    }

    public function hookDisplayMpMenu()
    {
        $idCustomer = $this->context->customer->id;
        $seller = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
        if ($seller) {
            if ($seller['active']) {
                // Get Seller total products
                $totalSellerProducts = 0;
                $sellerProduct = WkMpSellerProduct::getSellerAllShopProduct(
                    $seller['id_seller'],
                    'all',
                    $this->context->language->id
                );
                if ($sellerProduct) {
                    $totalSellerProducts = count($sellerProduct);
                }

                $this->context->smarty->assign([
                    'name_shop' => $seller['link_rewrite'],
                    'totalSellerProducts' => $totalSellerProducts,
                ]);
            }

            $this->context->smarty->assign('is_seller', $seller['active']);
        } else {
            $this->context->smarty->assign('is_seller', -1); // Not a seller
        }

        $this->context->smarty->assign('link', $this->context->link);

        return $this->fetch('module:marketplace/views/templates/hook/mpmenu.tpl');
    }

    public function hookDisplayMPMyAccountMenu()
    {
        $seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
        if ($seller) { // check customer as seller
            $this->context->smarty->assign('is_seller', $seller['active']);
        } else {
            $this->context->smarty->assign('is_seller', -1); // Not a seller
        }

        $this->context->smarty->assign('link', $this->context->link);

        return $this->fetch('module:marketplace/views/templates/hook/mpmyaccountmenu.tpl');
    }

    /**
     * Display Sell on link on navigation bar.
     *
     * @return html An link with text Sell on shop name
     */
    public function hookDisplayNav1()
    {
        if (Configuration::get('WK_MP_LINK_ON_NAV_BAR')) {
            $this->context->smarty->assign('wk_ad_nav', 1);

            return $this->displayAdvertisementLink();
        }
    }

    /**
     * Display Sell on link on footer.
     *
     * @return html An link with text Sell on shop name
     */
    public function hookDisplayMyAccountBlock()
    {
        if (Configuration::get('WK_MP_LINK_ON_FOOTER_BAR')) {
            $this->context->smarty->assign('wk_ad_footer', 1);

            return $this->displayAdvertisementLink();
        }
    }

    public function displayAdvertisementLink()
    {
        if ($this->context->customer->id) {
            $isSellerExist = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if ($isSellerExist && $isSellerExist['active']) {
                return;
            } else {
                if (Module::isEnabled('mpsellerstaff')) {
                    $staffDetails = WkMpSellerStaff::getStaffInfoByIdCustomer($this->context->customer->id);
                    if ($staffDetails) {
                        return;  // If current customer is any seller's staff, can't register as seller
                    }
                }
            }
        }
        $this->context->smarty->assign(
            'sellerLink',
            $this->context->link->getModuleLink('marketplace', 'sellerlogin')
        );

        return $this->fetch('module:marketplace/views/templates/hook/advertisement.tpl');
    }

    /**
     * Add custom CSS and Ad link on pages.
     *
     * If you dont want custom CSS, Define Marketplace::$mpController = false,
     * in init() function in your front controller,
     */
    public function hookDisplayHeader()
    {
        // Apply custome CSS for all MP and MP addons controllers
        if (isset($this->context->controller->module)) {
            if (($this->context->controller->module->name == $this->name
                || in_array($this->name, $this->context->controller->module->dependencies))
                && self::$mpController) {
                if (Configuration::get('WK_MP_ALLOW_CUSTOM_CSS')) {
                    $this->context->controller->registerStylesheet(
                        'module-marketplace-custom-style-css',
                        'modules/' . $this->name . '/views/css/mp_custom_style.css'
                    );
                }

                // Assign data for header if seller
                if ($this->context->customer->isLogged()) {
                    $idCustomer = $this->context->customer->id;
                    // Override customer id if any staff of seller want to use this controller
                    if (Module::isEnabled('mpsellerstaff')) {
                        $getCustomerId = WkMpSellerStaff::overrideMpSellerCustomerId($idCustomer);
                        if ($getCustomerId) {
                            $idCustomer = $getCustomerId;
                        }
                    }

                    $seller = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
                    if ($seller && $seller['active']) {
                        WkMpHelper::assignHeaderData($seller);
                    }
                }

                // Assign Global vars from Marketplace to add MP front controllers
                WkMpHelper::assignGlobalVariables();
                WkMpHelper::defineGlobalJSVariables();
            }
        }
        if (Configuration::get('WK_MP_LINK_ON_POP_UP') && Tools::getValue('controller') != 'sellerlogin') {
            $this->context->smarty->assign('wk_ad_footer_pop', 1);
            $this->context->smarty->assign('cms_content_only', Tools::getValue('content_only'));
            if (isset($this->context->cookie->no_advertisement)) {
                $this->context->smarty->assign('no_advertisement', $this->context->cookie->no_advertisement);
            }

            return $this->displayAdvertisementLink();
        }
    }

    /**
     * Display Sold by with seller's shop name and seller rating and also display edit product button on seller login
     *
     * @return html
     */
    public function hookDisplayProductButtons()
    {
        $idProduct = Tools::getValue('id_product');
        if ($sellerProduct = WkMpSellerProduct::getSellerProductByPsIdProduct($idProduct)) {
            if ($sellerProduct['id_mp_shop_default'] != Context::getContext()->shop->id) {
                // Seller details will not show on product page if this product is not for same shop and seller
                return false;
            }

            if (Configuration::get('WK_MP_SHOW_SELLER_DETAILS')) {
                $this->context->smarty->assign('showDetail', Configuration::get('WK_MP_SHOW_SELLER_DETAILS'));
            }

            $mpIdSeller = $sellerProduct['id_seller'];

            // Display seller rating on product page
            if ($sellerRating = WkMpSellerReview::getSellerAvgRating($mpIdSeller)) {
                if ($totalReviewData = WkMpSellerReview::getSellerReviewByIdSeller($mpIdSeller)) {
                    $totalReview = count($totalReviewData);
                } else {
                    $totalReview = 0;
                }

                // Get seller rating full summary
                $sellerRatingDetail = WkMpSellerReview::getSellerRatingSummary($mpIdSeller, $totalReview);

                $this->context->smarty->assign(
                    [
                        'sellerRating' => $sellerRating,
                        'sellerRatingDetail' => $sellerRatingDetail,
                        'totalReview' => $totalReview,
                    ]
                );

                Media::addJsDef([
                    'sellerRating' => $sellerRating,
                    'rating_start_path' => _MODULE_DIR_ . $this->name . '/views/img/',
                    'totalReview' => $totalReview,
                ]);
            }

            // Get seller info details
            if ($seller = WkMpSeller::getSeller($mpIdSeller, $this->context->language->id)) {
                // Check Access display Edit product link on seller product page for Seller or their staff
                $allowProductEdit = false;
                if ($this->context->customer->id == $seller['seller_customer_id']) {
                    $allowProductEdit = true; // allow seller to edit their product
                }

                $mpShopImage = $seller['shop_image'];
                if ($mpShopImage && file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/shop_img/' . $mpShopImage)) {
                    $this->context->smarty->assign(
                        'shop_logo_path',
                        _MODULE_DIR_ . 'marketplace/views/img/shop_img/' . $mpShopImage
                    );
                } else {
                    $this->context->smarty->assign(
                        'shop_logo_path',
                        _MODULE_DIR_ . 'marketplace/views/img/shop_img/defaultshopimage.jpg'
                    );
                }

                // If Mp seller staff module enabled
                if (Module::isEnabled('mpsellerstaff')) {
                    // If Mp staff is enable then check - Is this staff is able to edit seller permission
                    $staffDetails = WkMpSellerStaff::getStaffInfoByIdCustomer($this->context->customer->id);
                    if ($staffDetails
                        && $staffDetails['active']
                        && $staffDetails['id_seller']
                        && $staffDetails['seller_status']
                    ) {
                        $idTab = WkMpTabList::MP_PRODUCT_TAB; // For Product
                        $productPermission = WkMpSellerStaffPermission::getStaffPermission(
                            $staffDetails['id_staff'],
                            $idTab
                        );
                        if ($productPermission && $productPermission['view'] && $productPermission['edit']) {
                            $allowProductEdit = true; // allow staff to view and edit seller product
                        }
                    }
                }

                if ($allowProductEdit) {
                    // allow seller or staff to edit seller product from product page
                    $this->context->smarty->assign('wk_mp_product_link', $this->context->link->getModuleLink(
                        'marketplace',
                        'updateproduct',
                        ['id_mp_product' => $sellerProduct['id_mp_product']]
                    ));
                }

                $objMpSupplier = new WkMpSuppliers();
                $suppliersInfo = $objMpSupplier->getSuppliersBySellerId($seller['id_seller']);
                $supplierInfo = [];
                foreach ($suppliersInfo as $singleSupplier) {
                    if ($singleSupplier['id_wk_mp_supplier'] === $sellerProduct['id_supplier']) {
                        if (file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/mpsuppliers/' . $singleSupplier['id_wk_mp_supplier'] . '.jpg')) {
                            $singleSupplier['image'] = _MODULE_DIR_ . 'marketplace/views/img/mpsuppliers/' .
                                $singleSupplier['id_wk_mp_supplier'] . '.jpg';
                        } else {
                            $singleSupplier['image'] = _MODULE_DIR_ . 'marketplace/views/img/mpsuppliers/default_supplier.png';
                        }
                        $supplierInfo = $singleSupplier;
                    }
                }

                $showPriceByCustomerGroup = 1;
                $mpProduct = WkMpSellerProduct::getSellerProductWithPs($seller['id_seller'], true);
                if ($mpProduct) {
                    $activeProduct = [];
                    foreach ($mpProduct as $productDetails) {
                        if (($productDetails['visibility'] == 'both')
                            || ($productDetails['visibility'] == 'catalog')
                        ) {
                            $product = new Product(
                                $productDetails['id_ps_product'],
                                true,
                                $this->context->language->id
                            );
                            $productDetails['retail_price'] = WkMpHelper::displayPrice(
                                $product->getPriceWithoutReduct(true, $product->getWsDefaultCombination())
                            );
                            $productDetails['price'] = WkMpHelper::displayPrice($product->getPrice(false));

                            $activeProduct[] = $productDetails;
                        }
                    }


                    if ($activeProduct) {
                        $this->context->smarty->assign('mp_shop_product', $activeProduct);
                    }
                }

//                $productList = WkMpSuppliers::getProductListByMpSupplierAndSeller(
//                    $sellerProduct['id_supplier'],
//                    $seller['id_seller'],
//                    $this->context->language->id
//                );
                // END of Edit product button section

                $this->context->smarty->assign([
                    'product_page' => 1,
                    'showPriceByCustomerGroup' => $showPriceByCustomerGroup,
                    'seller_product' => $sellerProduct,
                    'mp_seller_info' => $seller,
                    'supplier' => $supplierInfo,
                    'suppliers' => $suppliersInfo,
                    'sellerprofile_link' => $this->context->link->getModuleLink(
                        'marketplace',
                        'sellerprofile',
                        ['mp_shop_name' => $seller['link_rewrite']]
                    ),
                    'shopstore_link' => $this->context->link->getModuleLink(
                        'marketplace',
                        'shopstore',
                        ['mp_shop_name' => $seller['link_rewrite']]
                    ),
                    'call_ajax' => Tools::getValue('action') ? Tools::getValue('action') : Tools::getValue('ajax'),
                ]);
                WkMpSeller::checkSellerAccessPermission($seller['seller_details_access']);

                return $this->fetch('module:marketplace/views/templates/hook/mp_soldby.tpl');
            }
        }
    }

    /**
     * Display Sold by with seller's shop name and seller rating and also display edit product button on seller login
     *
     * @return html
     */
    public function hookDisplayProductAdditionalInfo()
    {
        return $this->hookDisplayProductButtons();
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        if ($currentController = Tools::getValue('controller')) {
            $configurationControllers = [
                'AdminMarketplaceGeneralSettings',
                'AdminMarketplaceApprovalSettings',
            ];
            if (in_array($currentController, $configurationControllers)) {
                Media::addJsDef([
                    'module_dir' => _MODULE_DIR_,
                    'wkModuleAddonKey' => $this->module_key,
                    'wkModuleAddonsId' => 8057,
                    'wkModuleTechName' => $this->name,
                    'wkModuleDoc' => file_exists(_PS_MODULE_DIR_ . $this->name . '/docs/doc_en.pdf'),
                ]);
                $this->context->controller->addJs(
                    'https://prestashop.webkul.com/crossselling/wkcrossselling.min.js?t=' . time()
                );
            }
        }

        if ('AdminCarriers' === Tools::getValue('controller')) {
            // Change shipping distribution type of admin carriers
            $jsDef = [
                'path_admin_mp_shipping' => $this->context->link->getAdminLink('AdminSellerOrders'),
                'success_msg' => $this->l('Updated successfully'),
                'error_msg' => $this->l('Some error occured...'),
            ];

            Media::addJsDef($jsDef);

            $this->context->controller->addJs($this->_path . 'views/js/mp_shipping_distribution.js');
        }

        if (Tools::getValue('controller') == 'AdminSellerProductDetail') {
            Media::addJsDef([
                'isAdmin' => 1,
                'languages' => Language::getLanguages(),
                'addtag' => $this->l('Add tag'),
                'mppack_module_dir' => _MODULE_DIR_ . 'mppackproducts/',
                'invalid_product_name' => $this->l('Please enter valid product name'),
                'invalid_quantity' => $this->l('Please enter valid product quantity'),
                'noMatchesFound' => $this->l('No matches found'),
                'mp_pack_ajax' => $this->context->link->getAdminLink('AdminSellerProductDetail'),
                'module_secure_key' => $this->secure_key,
                'mp_related_ajax' => $this->context->link->getModuleLink('marketplace', 'addproduct'),
                'module_secure_key' => $this->secure_key,
                'noMatchesFound' => $this->l('No matches found'),
                'allowed_file_size' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
                'allowed_file_size_error' => sprintf(
                    $this->l('Uploaded file size must be less than %s MB.'),
                    Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')
                ),
                'attachment_success' => $this->l('Attachment uploaded successfully.'),
                'attachment_error' => $this->l('Something went wrong.'),
                'attachment_file_error' => $this->l('Please select a attachment file.'),
                'attachment_name_error' => $this->l('Attachment file title should not be blank.'),
                'attachment_name_length_error' => $this->l('Attachment file name is too short. It should greater than equals to 3 characters.'),
                'attachment_name_maxlength_error' => $this->l('Attachment file name is too long. It should less than equals to 32 characters.'),
                'allowed_file_size' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
                'allowed_file_size_error' => sprintf(
                    $this->l('Uploaded file size must be less than %s MB.'),
                    Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')
                ),
            ]);

            if ($idMpProduct = Tools::getValue('id_mp_product')) {
                $objMpPack = new WkMpPackProduct();
                $objMpSeller = new WkMpSellerProduct($idMpProduct);
                Media::addJsDef([
                    'id_seller' => $objMpSeller->id_seller,
                    'id_mp_pack_product' => $idMpProduct,
                    'pack_stock_type' => Configuration::get('PS_PACK_STOCK_TYPE'),
                    'product_stock_type' => $objMpPack->getPackedProductStockType($idMpProduct),
                ]);
            }

            $this->context->controller->addCSS($this->_path . 'views/css/mp_pack_products.css');
            $this->context->controller->addJS($this->_path . 'views/js/mp_pack_products.js');

            $this->context->controller->addCSS($this->_path . 'views/css/mp_virtual_product.css');
            $this->context->controller->addJS($this->_path . 'views/js/mp_virtual_product.js');
            $this->context->controller->addJS($this->_path . 'views/js/mp_virtual_product_file.js');

            $this->assignMpSpecificJSVaribales();
            $this->context->controller->addJS($this->_path . '/views/js/mp_specific_pricing.js');
            $this->context->controller->addCSS($this->_path . '/views/css/mp_specific_pricing.css');

            $this->context->controller->addJqueryPlugin('tagify');
            $this->context->controller->addJS($this->_path . 'views/js/mp_product_tags.js');
            $this->context->controller->addCSS($this->_path . 'views/css/mp_product_tags.css');

            $this->supplierSetMedia(0);

            $this->context->controller->addCSS($this->_path . 'views/css/mp_seller_search_products.css');
            $this->context->controller->addCSS($this->_path . 'views/css/mp_attachments.css');
        }
    }

    public function hookActionFrontControllerSetMedia()
    {
        if ('product' === $this->context->controller->php_self) {
            // To display seller rating
            if ($sellerProduct = WkMpSellerProduct::getSellerProductByPsIdProduct(Tools::getValue('id_product'))) {
                if ($sellerRating = WkMpSellerReview::getSellerAvgRating($sellerProduct['id_seller'])) {
                    Media::addJsDef([
                        'sellerRating' => $sellerRating,
                        'rating_start_path' => _MODULE_DIR_ . $this->name . '/views/img/',
                    ]);

                    $this->context->controller->registerJavascript(
                        'module-marketplace-raty-js',
                        'modules/' . $this->name . '/views/js/libs/jquery.raty.min.js',
                        ['position' => 'bottom', 'priority' => 999]
                    );

                    // Display seller rating on product page through contactseller.js file
                    $this->context->controller->registerJavascript(
                        'module-marketplace-contactseller-js',
                        'modules/' . $this->name . '/views/js/contactseller.js',
                        ['position' => 'bottom', 'priority' => 999]
                    );
                }
            }
        }

        $this->context->controller->registerStylesheet(
            'module-marketplace-seller-rating-css',
            'modules/' . $this->name . '/views/css/mp_seller_rating.css',
            ['position' => 'bottom', 'priority' => 999]
        );

        $this->context->controller->registerStylesheet(
            'module-marketplace-mpheader-css',
            'modules/' . $this->name . '/views/css/mp_header.css',
            ['position' => 'bottom', 'priority' => 999]
        );
        $this->context->controller->registerJavascript(
            'module-marketplace-mpheader-js',
            'modules/' . $this->name . '/views/js/mp_header.js',
            ['position' => 'bottom', 'priority' => 999]
        );

        $this->context->controller->registerJavascript(
            'bxslider',
            'modules/' . $this->name . '/views/js/jquery.bxslider.js'
        );

        $this->context->controller->registerJavascript(
            'mp-jquery-raty-min',
            'modules/' . $this->name . '/views/js/libs/jquery.raty.min.js'
        );

        // mp product slider
        $this->context->controller->registerStylesheet(
            'ps_gray',
            'modules/' . $this->name . '/views/css/product_slider_pager/ps_gray.css'
        );
        $this->context->controller->registerJavascript(
            'mp_product_slider-js',
            'modules/' . $this->name . '/views/js/mp_product_slider.js'
        );

        if (Tools::getValue('controller') == 'addproduct' || Tools::getValue('controller') == 'updateproduct') {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if ($mpSeller) {
                $jsFinalDef = [
                    'id_seller' => $mpSeller['id_seller'],
                    'path_addproduct' => $this->context->link->getModuleLink($this->name, 'addproduct'),
                ];
                if (Configuration::get('WK_MP_VIRTUAL_PRODUCT')) {
                    $jsDef = [
                        'checkcontroller' => 1,
                        'ajaxurl_virtual' => $this->context->link->getModuleLink('marketplace', 'getFile'),
                        'confirmation_msg' => $this->l('Are you sure?'),
                        'allowed_file_size' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
                        'allowed_file_size_error' => sprintf(
                            $this->l('Uploaded file size must be less than %s MB.'),
                            Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')
                        ),
                    ];
                    $jsFinalDef = array_merge($jsDef, $jsFinalDef);
                    $this->context->controller->registerJavascript('modules-mpvirtualproduct-product-js', 'modules/' . $this->name . '/views/js/mp_virtual_product_file.js');
                    $this->context->controller->registerStylesheet('modules-mpvirtualproduct-css', 'modules/' . $this->name . '/views/css/mp_virtual_product.css');
                    $this->context->controller->registerJavascript('modules-mpvirtualproduct-main', 'modules/' . $this->name . '/views/js/mp_virtual_product.js');
                }
                if (Configuration::get('WK_MP_PACK_PRODUCTS')) {
                    $jsDef = [
                        'id_seller' => $mpSeller['id_seller'],
                        'mppack_module_dir' => _MODULE_DIR_ . 'mppackproducts/',
                        'id_mp_pack_product' => Tools::getValue('id_mp_product'),
                        'invalid_product_name' => $this->l('Please enter valid product name'),
                        'invalid_quantity' => $this->l('Please enter valid product quantity'),
                        'noMatchesFound' => $this->l('No matches found'),
                        'mp_pack_ajax' => $this->context->link->getModuleLink('marketplace', 'addproduct'),
                        'module_secure_key' => $this->secure_key,
                    ];
                    $jsFinalDef = array_merge($jsDef, $jsFinalDef);

                    $this->context->controller->registerStylesheet('modules-mppackproducts-css', 'modules/' . $this->name . '/views/css/mp_pack_products.css');
                    $this->context->controller->registerJavascript('modules-mppackproducts-main', 'modules/' . $this->name . '/views/js/mp_pack_products.js');
                }
                if (Configuration::get('WK_MP_PRODUCT_SPECIFIC_RULE')) {
                    $this->assignMpSpecificJSVaribales();
                    $this->context->controller->registerStylesheet(
                        'timepicker-css',
                        'modules/' . $this->name . '/views/js/jquerydatepicker/jquery-ui-timepicker-addon.css'
                    );

                    $this->context->controller->registerStylesheet(
                        'mp_specific_pricing-style',
                        'modules/' . $this->name . '/views/css/mp_specific_pricing.css'
                    );

                    $this->context->controller->registerJavascript(
                        'timepicker',
                        'modules/' . $this->name . '/views/js/jquerydatepicker/jquery-ui-timepicker-addon.js',
                        ['position' => 'bottom', 'priority' => 1000]
                    );

                    $this->context->controller->registerJavascript(
                        'module-mp_specific_pricing-js',
                        'modules/' . $this->name . '/views/js/mp_specific_pricing.js',
                        ['position' => 'bottom', 'priority' => 1000]
                    );
                }

                if (Configuration::get('WK_MP_PRODUCT_TAGS')) {
                    $jsDef = [
                        'languages' => Language::getLanguages(),
                        'addtag' => $this->l('Add tag'),
                    ];
                    $jsFinalDef = array_merge($jsDef, $jsFinalDef);

                    $this->context->controller->addJqueryPlugin('tagify');
                    $this->context->controller->registerStylesheet(
                        'mpproducttags-css',
                        'modules/' . $this->name . '/views/css/mp_product_tags.css'
                    );
                    $this->context->controller->registerJavascript(
                        'mpproducttags-js',
                        'modules/' . $this->name . '/views/js/mp_product_tags.js'
                    );
                }

                if (Configuration::get('WK_MP_PRODUCT_MANUFACTURER')) {
                    $jsDef = [
                        'add_manufacturer' => $this->context->link->getModuleLink('marketplace', 'mpcreatemanufacturers'),
                        'lose_allunsaved_data' => $this->l('You will lose all unsaved modifications. Are you sure that you want to proceed?'),
                    ];
                    $jsFinalDef = array_merge($jsDef, $jsFinalDef);

                    $this->context->controller->registerJavascript(
                        'manuf-confirm-js',
                        'modules/' . $this->name . '/views/js/manufacturers/manufacturer_confirm.js'
                    );
                }

                if (Configuration::get('WK_MP_PRODUCT_SUPPLIER')) {
                    $this->supplierSetMedia(1);
                }

                if (Configuration::get('WK_MP_RELATED_PRODUCT')) {
                    $jsDef = [
                        'mp_related_ajax' => $this->context->link->getModuleLink('marketplace', 'addproduct'),
                        'module_secure_key' => $this->secure_key,
                        'noMatchesFound' => $this->l('No matches found'),
                    ];
                    $jsFinalDef = array_merge($jsDef, $jsFinalDef);
                    $this->context->controller->registerStylesheet(
                        'mpsearchproduct-css',
                        'modules/' . $this->name . '/views/css/mp_seller_search_products.css'
                    );
                }

                if (Configuration::get('WK_MP_PRODUCT_IMAGE_CAPTION')) {
                    $jsDef = [
                        'languages' => Language::getLanguages(),
                    ];
                    $jsFinalDef = array_merge($jsDef, $jsFinalDef);
                }

                if (Configuration::get('WK_MP_PRODUCT_ATTACHMENT')) {
                    $jsDef = [
                        'id_seller' => $mpSeller['id_seller'],
                        'allowed_file_size' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
                        'allowed_file_size_error' => sprintf(
                            $this->l('Uploaded file size must be less than %s MB.'),
                            Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')
                        ),
                        'attachment_success' => $this->l('Attachment uploaded successfully.'),
                        'attachment_error' => $this->l('Something went wrong.'),
                        'attachment_file_error' => $this->l('Please select a attachment file.'),
                        'attachment_name_error' => $this->l('Attachment file title should not be blank.'),
                        'attachment_name_length_error' => $this->l('Attachment file name is too short. It should greater than equals to 3 characters.'),
                        'attachment_name_maxlength_error' => $this->l('Attachment file name is too long. It should less than equals to 32 characters.'),
                    ];
                    $jsFinalDef = array_merge($jsDef, $jsFinalDef);
                    $this->context->controller->registerStylesheet(
                        'mpattachment-css',
                        'modules/' . $this->name . '/views/css/mp_attachments.css'
                    );
                }

                Media::addJsDef($jsFinalDef);
            }
        // For MP Shipping Start
        } elseif ('productlist' === Tools::getValue('controller')) {
            $this->context->controller->registerStylesheet(
                'assignshipping-css',
                'modules/' . $this->name . '/views/css/shipping/assignshipping.css'
            );

            $jsDef = [
                'check_msg' => $this->l('Please select any shipping method first.'),
                'success_msg' => $this->l('Shipping assigned successfully to all the products.'),
                'error_msg' => $this->l('Some error occurs, try again later.'),
            ];

            Media::addJsDef($jsDef);

            $this->context->controller->registerJavascript(
                'assignshipping-js',
                'modules/' . $this->name . '/views/js/shipping/assignshipping.js'
            );
        } elseif ('mporderdetails' === Tools::getValue('controller')) {
            if (Tools::getValue('update_tracking_success') || Tools::getValue('invalid_tracking_number')) {
                $this->context->controller->registerJavascript(
                    'mporder-tracking-js',
                    'modules/' . $this->name . '/views/js/shipping/mporder-tracking.js'
                );
            }
        }
        // For MP Shipping end
    }

    public function supplierSetMedia($front)
    {
        Media::addJsDef([
            'front' => $front,
            'add_supplier' => $this->context->link->getModuleLink('marketplace', 'mpaddsupplier'),
            'admin_ajax_link' => $this->context->link->getAdminLink('AdminMpSuppliers'),
            'no_supplier' => $this->l('You did not create any supplier'),
            'confirm_msg' => $this->l('You will lose all unsaved modifications. Are you sure that you want to proceed?'),
        ]);

        if (!$front) {
            if (Tools::getValue('addwk_mp_seller_product') !== false) {
                $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/views/js/suppliers/admin_add_product.js');
            }
            $this->context->controller->addJS(_MODULE_DIR_ . $this->name . '/views/js/suppliers/supplier_confirm.js');
        } else {
            $this->context->controller->registerJavascript(
                'module-marketplace-supplier_confirm-js',
                'modules/' . $this->name . '/views/js/suppliers/supplier_confirm.js',
                ['position' => 'bottom', 'priority' => 999]
            );
        }
    }

    public function assignMpSpecificJSVaribales()
    {
        $addJsDefFront = [
            'modules_dir' => _MODULE_DIR_,
            'Choose' => $this->l('Choose'),
            'invalid_qty' => $this->l('Invalid quantity.'),
            'wrong_id' => $this->l('Something went wrong.'),
            'invalid_range' => $this->l('Invalid date range.'),
            'success' => $this->l('Specific price added successfully.'),
            'update_success' => $this->l('Specific price updated successfully.'),
            'sp_quantity_empty' => $this->l('Please set quantity.'),
            'no_customers_found' => $this->l('No customers found.'),
            'date_invalid' => $this->l('The from/to date is invalid.'),
            'invalid_price' => $this->l('Invalid price/discount amount.'),
            'sp_reduction_err' => $this->l('Please set reduction amount.'),
            'delete_successs' => $this->l('Specific price delete successfully.'),
            'no_reduction' => $this->l('No reduction value has been submitted.'),
            'reduction_range' => $this->l('Submitted reduction value (0-100) is out-of-range.'),
            'already_exist' => $this->l('A specific price already exists for these parameters.'),
            'select_dis_type' => $this->l('Please select a discount type (amount or percentage).'),
            'delete_err' => $this->l('An error occurred while attempting to delete the specific price.'),
            'conf_delete' => $this->l('This will delete the specific price. Do you wish to proceed?'),
            'conf_delete_customer' => $this->l('This will delete the specific price customer. Do you wish to proceed?'),
        ];

        $addJsDefAdmin = [
            'mpspecificadmin_datepicker' => 0,
            'wkmpspecific_ajax' => $this->context->link->getModuleLink($this->name, 'addproduct'),
        ];
        $controller = Tools::getValue('controller');
        if ('AdminSellerProductDetail' === $controller) {
            $addJsDefAdmin = [
                'mpspecificadmin_datepicker' => 1,
                'wkmpspecific_ajax' => $this->context->link->getAdminLink('AdminSellerProductDetail'),
            ];
        }

        Media::addJsDef(array_merge($addJsDefFront, $addJsDefAdmin));
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        $idOrder = $params['id_order'];
        $idOrderState = $params['newOrderStatus']->id;

        $order = new Order($idOrder);

        $objMpOrderDetail = new WkMpSellerOrderDetail();
        // if order commission not calculated
        if (!$objMpOrderDetail->getOrderCommissionDetails($idOrder)) {
            $params['cart'] = new Cart((int) $order->id_cart);
            $customer = new Customer((int) $order->id_customer);
            $address = new Address((int) $order->id_address_delivery, (int) $order->id_lang);
            $idCurrency = (int) $order->id_currency;

            $objMpSplit = new WkMpSellerPaymentSplit();
            $splitAmount = $objMpSplit->sellerWiseSplitedAmount($params, true);
            if ($splitAmount) {
                // adding shipping to admin's table when any order have seller's product
                WkMpAdminShipping::addingAdminShipping($idOrder, $splitAmount, $params['cart']);
                foreach ($splitAmount as $idCustomerSeller => $mpProduct) {
                    if ($idCustomerSeller != 'admin') {
                        $idSellerOrder = WkMpSellerOrder::updateSellerOrder($idCustomerSeller, $mpProduct);
                        $mpSeller = WkMpSeller::getSellerDetailByCustomerId($idCustomerSeller);
                        if ($idSellerOrder) {
                            // save seller's shipping cost seller wise
                            // product list of current seller
                            foreach ($mpProduct['product_list'] as $idProduct => $productAttribute) {
                                foreach ($productAttribute as $idProductAttribute => $productCustomization) {
                                    foreach ($productCustomization as $idProductCustomization => $product) {
                                        // Creating product list for order detail of marketplace
                                        $objMpOrderDetails = new WkMpSellerOrderDetail();
                                        $objMpOrderDetails->id_seller_order = (int) $idSellerOrder;
                                        $objMpOrderDetails->id_shop = (int) $product['id_shop'];
                                        $objMpOrderDetails->product_id = (int) $idProduct;
                                        $objMpOrderDetails->product_attribute_id = (int) $idProductAttribute;
                                        $objMpOrderDetails->id_customization = (int) $idProductCustomization;
                                        $objMpOrderDetails->seller_customer_id = (int) $product['id_customer'];
                                        $objMpOrderDetails->seller_name = $product['firstname'] . ' ' . $product['lastname'];
                                        $objMpOrderDetails->product_name = $product['product_name'];
                                        $objMpOrderDetails->quantity = $product['product_quantity'];
                                        $objMpOrderDetails->price_ti = Tools::ps_round(
                                            $product['total_price_tax_incl'],
                                            6
                                        );
                                        $objMpOrderDetails->price_te = Tools::ps_round(
                                            $product['total_price_tax_excl'],
                                            6
                                        );
                                        $objMpOrderDetails->admin_commission = Tools::ps_round(
                                            $product['admin_commission'],
                                            6
                                        );
                                        if ($product['admin_tax'] < 0) {
                                            $product['admin_tax'] = 0;
                                        }
                                        $objMpOrderDetails->admin_tax = Tools::ps_round($product['admin_tax'], 6);
                                        $objMpOrderDetails->seller_amount = Tools::ps_round(
                                            (float) $product['seller_amount'],
                                            6
                                        );
                                        $objMpOrderDetails->seller_tax = Tools::ps_round(
                                            (float) $product['seller_tax'],
                                            6
                                        );
                                        $objMpOrderDetails->id_order = (int) $idOrder;
                                        $objMpOrderDetails->commission_type = pSQL($product['commission_type']);
                                        $objMpOrderDetails->commission_rate = (float) $product['commission_rate'];
                                        $objMpOrderDetails->commission_amt = (float) $product['commission_amt'];
                                        $objMpOrderDetails->commission_tax_amt = (float) $product['commission_tax_amt'];
                                        $objMpOrderDetails->tax_distribution_type = Configuration::get(
                                            'WK_MP_PRODUCT_TAX_DISTRIBUTION'
                                        );
                                        $objMpOrderDetails->id_currency = (int) $idCurrency;
                                        if ($objMpOrderDetails->save()) {
                                            $idMpOrderDetail = $objMpOrderDetails->id;

                                            // Creating transaction history (product wise)
                                            $objTransactionHistory = new WkMpSellerTransactionHistory();
                                            $idMpTransaction = $objTransactionHistory->saveSellerTransactionData(
                                                $product['id_customer'],
                                                $idCurrency,
                                                $idMpOrderDetail,
                                                Tools::ps_round($product['seller_amount'], 6),
                                                Tools::ps_round($product['seller_tax'], 6),
                                                false,
                                                false,
                                                false,
                                                Tools::ps_round($product['admin_commission'], 6),
                                                Tools::ps_round($product['admin_tax'], 6),
                                                false,
                                                false,
                                                $order->payment,
                                                WkMpSellerTransactionHistory::MP_SELLER_ORDER,
                                                $idOrder,
                                                $this->l('Seller product sold'),
                                                WkMpSellerTransactionHistory::MP_SELLER_ORDER_STATUS,
                                                (int) $product['id_shop']
                                            );

                                            if ($idMpTransaction) {
                                                $mpIdProduct = WkMpSellerProduct::getMpIdProductByPsIdProduct(
                                                    $idProduct
                                                );
                                                if ($mpIdProduct) {
                                                    // Send mail of out of Stock
                                                    // if quantity is less than or equal low stock level
                                                    if ($idProductAttribute) {
                                                        $objCombination = new Combination($idProductAttribute);
                                                        if ($objCombination->low_stock_alert) {
                                                            // Send only in combination product case
                                                            $wkProdQty = StockAvailable::getQuantityAvailableByProduct(
                                                                $idProduct,
                                                                $idProductAttribute,
                                                                Context::getContext()->shop->id
                                                            );
                                                            if ($wkProdQty <= $objCombination->low_stock_threshold) {
                                                                // Send out of stock mail to seller
                                                                WkMpSellerProduct::sendMail(
                                                                    $mpIdProduct,
                                                                    4,
                                                                    false,
                                                                    false,
                                                                    $idProductAttribute
                                                                );
                                                            }
                                                        }
                                                    } else {
                                                        $objProduct = new Product($idProduct);
                                                        if ($objProduct->low_stock_alert) {
                                                            // Send only in standard product case
                                                            $wkProdQty = StockAvailable::getQuantityAvailableByProduct(
                                                                $idProduct,
                                                                null,
                                                                Context::getContext()->shop->id
                                                            );
                                                            if ($wkProdQty <= $objProduct->low_stock_threshold) {
                                                                // Send out of stock mail to seller
                                                                WkMpSellerProduct::sendMail(
                                                                    $mpIdProduct,
                                                                    4
                                                                );
                                                            }
                                                        }
                                                    }

                                                    Hook::exec(
                                                        'actionSellerProductQtyUpdateAfterOrder',
                                                        [
                                                            'id_order' => $idOrder,
                                                            'ordered_product_qty' => $product['product_quantity'],
                                                            'id_mp_product' => $mpIdProduct,
                                                            'id_ps_product_attribute' => $idProductAttribute,
                                                            'update_mode' => 1,
                                                        ]
                                                    );
                                                }
                                            }
                                        }

                                        unset($objMpOrderDetails);
                                    }
                                }
                            }

                            // Manage Seller Order Status In Mp table
                            $objOrderStatus = new WkMpSellerOrderStatus();
                            $objOrderStatus->processSellerOrderStatus(
                                $idOrder,
                                $mpSeller['id_seller'],
                                $idOrderState
                            );

                            Hook::exec(
                                'actionSellerPaymentTransaction',
                                [
                                    'id_order' => $idOrder,
                                    'id_seller' => $mpSeller['id_seller'],
                                    'id_currency' => $idCurrency,
                                    'seller_cart_product_data' => $mpProduct,
                                ]
                            );

                            // order mail to every seller if his/her product in the cart
                            if (Configuration::get('WK_MP_MAIL_SELLER_PRODUCT_SOLD')) {
                                $this->sellersOrderMail(
                                    $mpProduct,
                                    $customer,
                                    $address,
                                    $idCurrency,
                                    $mpSeller['id_seller'],
                                    $idOrder
                                );
                            }
                        }
                    }
                }
            }
        }

        // Manage Seller Order Status in MP table, in case admin update seller's order status
        // For seller, processSellerOrderStatus function is already calling on mporderdetails page
        if (Tools::getValue('controller') !== 'mporderdetails') {
            $sellerArray = [];
            if ($products = $order->getProducts()) {
                foreach ($products as $prod) {
                    $seller = WkMpSellerOrderDetail::getSellerFromOrderProduct($idOrder, $prod['product_id']);
                    if ($seller) {
                        $sellerArray[$seller['seller_id']] = $prod['product_id'];
                    }
                }
            }
            if ($sellerArray && count($sellerArray) > 0) {
                // if admin change order status then change on each seller order
                foreach ($sellerArray as $idSeller => $prod) {
                    if ($prod) {
                        $objOrderStatus = new WkMpSellerOrderStatus();
                        $objOrderStatus->processSellerOrderStatus($idOrder, $idSeller, $idOrderState);
                    }
                }
            }
        }

        // If Admin change whole order status as cancelled
        if ($idOrderState == Configuration::get('PS_OS_CANCELED')) {
            $transactionData = $this->l('#Cancel');
            $remark = $this->l('Order Cancel');

            // Manage seller transaction only if seller order is cancelled or refunded
            $objTransactionHistory = new WkMpSellerTransactionHistory();
            $getOrderTransactionRows = $objTransactionHistory->getOrderTransactionHistoryByOrderId($idOrder);
            if ($getOrderTransactionRows) {
                // Increase product qty if order status updated as Cancelled (for whole ps order)
                if ($products = $order->getProducts()) {
                    foreach ($products as $orderProduct) {
                        $mpIdProduct = WkMpSellerProduct::getMpIdProductByPsIdProduct(
                            $orderProduct['product_id']
                        );
                        if ($mpIdProduct) {
                            if ($orderProduct['product_attribute_id']) {
                                $objCombination = new Combination($orderProduct['product_attribute_id']);
                                if ($objCombination->low_stock_alert) {
                                    // Send only in combination product case
                                    $wkProdQty = StockAvailable::getQuantityAvailableByProduct(
                                        $orderProduct['product_id'],
                                        $orderProduct['product_attribute_id'],
                                        Context::getContext()->shop->id
                                    );
                                    if ($wkProdQty <= $objCombination->low_stock_threshold) {
                                        // Send out of stock mail to seller
                                        WkMpSellerProduct::sendMail(
                                            $mpIdProduct,
                                            4,
                                            false,
                                            false,
                                            $orderProduct['product_attribute_id']
                                        );
                                    }
                                }
                            }
                            Hook::exec(
                                'actionSellerProductQtyUpdateAfterOrder',
                                [
                                    'id_order' => $idOrder,
                                    'ordered_product_qty' => $orderProduct['product_quantity'],
                                    'id_mp_product' => $mpIdProduct,
                                    'id_ps_product_attribute' => $orderProduct['product_attribute_id'],
                                    'update_mode' => 2,
                                ]
                            );
                        }
                    }
                }

                foreach ($getOrderTransactionRows as $orderTransaction) {
                    $sellerRefundedAmount = $orderTransaction['seller_amount'] + $orderTransaction['seller_tax'] + $orderTransaction['seller_shipping'];
                    $adminRefundedAmount = $orderTransaction['admin_commission'] + $orderTransaction['admin_tax'] + $orderTransaction['admin_shipping'];

                    $idMpTransaction = $objTransactionHistory->saveSellerTransactionData(
                        $orderTransaction['id_customer_seller'],
                        $orderTransaction['id_currency'],
                        false,
                        Tools::ps_round($orderTransaction['seller_amount'], 6),
                        Tools::ps_round($orderTransaction['seller_tax'], 6),
                        Tools::ps_round($orderTransaction['seller_shipping'], 6),
                        Tools::ps_round($sellerRefundedAmount, 6),
                        false,
                        Tools::ps_round($orderTransaction['admin_commission'], 6),
                        Tools::ps_round($orderTransaction['admin_tax'], 6),
                        Tools::ps_round($orderTransaction['admin_shipping'], 6),
                        Tools::ps_round($adminRefundedAmount, 6),
                        'N/A',
                        WkMpSellerTransactionHistory::MP_ORDER_CANCEL,
                        $orderTransaction['id_transaction'] . $transactionData,
                        $remark,
                        WkMpSellerTransactionHistory::MP_ORDER_CANCEL_STATUS
                    );
                    if ($idMpTransaction) {
                        $objTransaction = new WkMpSellerTransactionHistory(
                            $orderTransaction['id_seller_transaction_history']
                        );
                        $objTransaction->status = WkMpSellerTransactionHistory::MP_ORDER_CANCEL_STATUS;
                        $objTransaction->update();
                    }
                }
            }
        }
    }

    public function hookDisplayCustomerAccount()
    {
        if ('myaccount' === Tools::getValue('controller')) {
            if (Module::isEnabled('mpsellerstaff')) {
                $staffDetails = WkMpSellerStaff::getStaffInfoByIdCustomer($this->context->customer->id);
                if (!$staffDetails) {
                    // If customer is not any seller's staff, not even active/deactive then can access MP Shop Panel
                    return $this->fetch('module:marketplace/views/templates/hook/customeraccount.tpl');
                }
            } else {
                return $this->fetch('module:marketplace/views/templates/hook/customeraccount.tpl');
            }
        }
    }

    public function hookActionObjectCustomerDeleteAfter($params)
    {
        $customer = $params['object'];
        if ($customer) {
            $deleteCustomers = Tools::getValue('delete_customers');
            if ($deleteCustomers) {
                $deleteMethod = $deleteCustomers['delete_method'];
                if ($deleteMethod == 'allow_registration_after') {
                    $sellerInfo = WkMpSeller::getSellerByCustomerId($customer->id);
                    if ($sellerInfo) {
                        $objSeller = new WkMpSeller((int) $sellerInfo['id_seller']);
                        $objSeller->delete();
                    }
                }
            }
        }
    }

    /**
     * Display restriction message on header
     *
     * @param array $params controller data
     */
    public function hookDisplayAdminAfterHeader($params)
    {
        if ('AdminProducts' === Tools::getValue('controller')) {
            if (Tools::getValue('notallow')) {
                $this->context->smarty->assign('seller_not_active', 1);

                return $this->display(__FILE__, 'product-restrict-message.tpl');
            }
        }
    }

    /**
     * Restrict product to activate if seller is deactive
     *
     * @param array $params Product details
     */
    public function hookActionObjectProductUpdateBefore($params)
    {
        if (isset($params['object']->id) && Tools::getValue('controller') == '') {
            $idProduct = $params['object']->id;
            if ($mpProduct = WkMpSellerProduct::getSellerProductByPsIdProduct($idProduct)) {
                $objMpSeller = new WkMpSeller($mpProduct['id_seller']);
                if (!$objMpSeller->active) { // if seller is not active
                    Tools::redirectAdmin(
                        $this->context->link->getAdminLink('AdminProducts', true, ['notallow' => 1])
                    );
                }
            }
        }
    }

    public function hookActionProductDelete($params)
    {
        if ($psIdProduct = $params['id_product']) {
            if ($mpIdProduct = WkMpSellerProduct::getMpIdProductByPsIdProduct($psIdProduct)) {
                // delete seller
                $objSellerProduct = new WkMpSellerProduct($mpIdProduct);
                $objSellerProduct->delete();
            }
        }
    }

    public function hookActionAdminCarriersListingFieldsModifier($list)
    {
        // Display shipping distribution section in carriers page
        if (Configuration::get('WK_MP_SHIPPING_DISTRIBUTION_ALLOW')) {
            $optionsDistributeType = [
                'admin' => $this->l('Admin'),
                'seller' => $this->l('Seller'),
                'both' => $this->l('Both (on the basis of commission on shipping)'),
            ];

            if (isset($list['select'])) {
                // By default Admin will selected
                $list['select'] .= ', IF(mcdt.`type` != "", mcdt.`type`, "admin") AS `distribute_type`';
            }
            if (isset($list['join'])) {
                $list['join'] .= ' LEFT JOIN `' . _DB_PREFIX_ . 'wk_mp_carrier_distributor_type` mcdt
                ON (mcdt.`id_ps_reference` = a.`id_reference`)';
            }

            $list['fields']['distribute_type'] = [
                'title' => 'Shipping Distribute To',
                'align' => 'text-center',
                'orderby' => false,
                'remove_onclick' => true,
                'type' => 'select',
                'hint' => $this->l('Distribution will applicable only on Marketplace Seller Product'),
                'list' => $optionsDistributeType,
                'filter_key' => 'mcdt!type',
                'callback' => 'callCarrierDistribution',
                'callback_object' => Module::getInstanceByName($this->name),
            ];
        }
    }

    public function callCarrierDistribution($distributeType, $list)
    {
        $this->context->smarty->assign([
            'options_distribute_type' => [
                'admin' => $this->l('Admin'),
                'seller' => $this->l('Seller'),
                'both' => $this->l('Both (on the basis of commission on shipping)'),
            ],
            'wk_distribute_type' => $distributeType,
            'wk_carrier_list' => $list,
        ]);

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/carrier_distribution_type.tpl'
        );
    }

    /**
     * If admin add any language then an entry will add in defined $lang_tables array's lang table same as presta do.
     *
     * @param array $params
     */
    public function hookActionObjectLanguageAddAfter($params)
    {
        if ($params['object']->id) {
            $newIdLang = $params['object']->id;

            // Assign all lang's main table in an ARRAY
            $langTables = ['wk_mp_seller'];

            // If Admin update new language when we do entry in module all lang tables.
            WkMpHelper::updateIdLangInLangTables($newIdLang, $langTables);
        }
    }

    /**
     * If admin disable any langauge and that langauge is used in any seller's default lang
     * then ps default lang will be set as default lang for that seller.
     *
     * @param array $params
     */
    public function hookActionObjectLanguageUpdateAfter($params)
    {
        if ($params['object']->id) {
            if (!$params['object']->active) { // going to deactivate seller default lang
                $changedIdLang = $params['object']->id;
                WkMpSeller::updateSellerLanguage($changedIdLang);
            }
        }
    }

    /**
     * If admin delete any langauge and that langauge is used in any seller's default lang
     * then ps default lang will be set as default lang for that seller].
     *
     * @param array $params
     */
    public function hookActionObjectLanguageDeleteAfter($params)
    {
        if ($params['object']->id) {
            $deletedIdLang = $params['object']->id;
            WkMpSeller::updateSellerLanguage($deletedIdLang);
        }
    }

    /**
     * Make user pages url friendly.
     */
    public function hookModuleRoutes()
    {
        if (Configuration::get('WK_MP_URL_REWRITE_ADMIN_APPROVE')) {
            $wkProfileRewrite = Tools::link_rewrite(Configuration::get('WK_MP_SELLER_PROFILE_PREFIX'));
            $wkShopRewrite = Tools::link_rewrite(Configuration::get('WK_MP_SELLER_SHOP_PREFIX'));

            if (Configuration::get('WK_MP_SELLER_REVIEWS_PREFIX')) {
                $wkReviewsPrefix = Configuration::get('WK_MP_SELLER_REVIEWS_PREFIX');
            } else {
                // We have added 'else' condition so that if client update marketplace files over old version(V5.2.1)
                // And if didn't fill this condition then default value can be used.
                $wkReviewsPrefix = 'reviews';
            }
            $wkReviewsRewrite = Tools::link_rewrite($wkReviewsPrefix);
            $sellerLoginRewrite = Tools::link_rewrite(Configuration::get('WK_MP_SELLER_LOGIN_PREFIX'));

            if ($wkProfileRewrite) {
                $wkProfileRewrite .= '/';
            }
            if ($wkShopRewrite) {
                $wkShopRewrite .= '/';
            }
            if ($wkReviewsRewrite) {
                $wkReviewsRewrite .= '/';
            }

            return [
                'module-marketplace-sellerprofile' => [
                    'controller' => 'sellerprofile',
                    'rule' => "$wkProfileRewrite{:mp_shop_name}",
                    'keywords' => [
                        'mp_shop_name' => [
                            'regexp' => '[_a-zA-Z0-9-\pL\pS-]*',
                            'param' => 'mp_shop_name',
                        ],
                    ],
                    'params' => [
                        'fc' => 'module',
                        'module' => 'marketplace',
                        'controller' => 'sellerprofile',
                    ],
                ],
                'module-marketplace-shopstore' => [
                    'controller' => 'shopstore',
                    'rule' => "$wkShopRewrite{:mp_shop_name}",
                    'keywords' => [
                        'mp_shop_name' => [
                            'regexp' => '[_a-zA-Z0-9\pL\pS-]*',
                            'param' => 'mp_shop_name',
                        ],
                    ],
                    'params' => [
                        'fc' => 'module',
                        'module' => 'marketplace',
                        'controller' => 'shopstore',
                    ],
                ],
                'module-marketplace-allreviews' => [
                    'controller' => 'allreviews',
                    'rule' => "$wkReviewsRewrite{:mp_shop_name}",
                    'keywords' => [
                        'mp_shop_name' => [
                            'regexp' => '[_a-zA-Z0-9\pL\pS-]*',
                            'param' => 'mp_shop_name',
                        ],
                    ],
                    'params' => [
                        'fc' => 'module',
                        'module' => 'marketplace',
                        'controller' => 'allreviews',
                    ],
                ],
                'module-marketplace-sellerlogin' => [
                    'controller' => 'sellerlogin',
                    'rule' => "$sellerLoginRewrite",
                    'keywords' => [
                    ],
                    'params' => [
                        'fc' => 'module',
                        'module' => 'marketplace',
                        'controller' => 'sellerlogin',
                    ],
                ],
            ];
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        if ((Tools::getValue('controller') == 'AdminLanguages') && Module::isEnabled('marketplace')) {
            $this->context->controller->warnings[] = $this->l('When you deactivate or delete any language and if that language is set as default language for any seller in Marketplace module then Admin default language will set as that seller default langauge.');
        }
    }

    public function hookDisplayOrderDetail()
    {
        if (Configuration::get('WK_MP_SELLER_ORDER_TRACKING_ALLOW')
        && Configuration::get('WK_MP_TRACKING_ORDER_HISTORY_ALLOW')) {
            // Display tracking number and url to the customer
            $idOrder = Tools::getValue('id_order');
            $order = new Order($idOrder);
            if ($products = $order->getProducts()) {
                $sellerTrackingData = [];
                $objOrderStatus = new WkMpSellerOrderStatus();
                foreach ($products as $prod) {
                    $seller = WkMpSellerProduct::getSellerProductByPsIdProduct($prod['product_id']);
                    if ($seller) {
                        $trackingData = $objOrderStatus->isOrderExist($idOrder, $seller['id_seller']);
                        if ($trackingData && $trackingData['tracking_number'] && $trackingData['tracking_url']) {
                            $sellerDetails = WkMpSeller::getSeller($seller['id_seller'], $this->context->language->id);
                            if ($sellerDetails) {
                                $sellerTrackingData[$seller['id_seller']]['shop_name'] = $sellerDetails['shop_name'];
                                $sellerTrackingData[$seller['id_seller']]['shopstore_link'] = $this->context->link->getModuleLink(
                                    'marketplace',
                                    'shopstore',
                                    ['mp_shop_name' => $sellerDetails['link_rewrite']]
                                );
                                $sellerTrackingData[$seller['id_seller']]['tracking_number'] = $trackingData['tracking_number'];

                                $wkTrackingURL = WkMpSellerOrder::replaceTrackingURL(
                                    $trackingData['tracking_number'],
                                    $trackingData['tracking_url']
                                );

                                $sellerTrackingData[$seller['id_seller']]['tracking_url'] = $wkTrackingURL;
                            }
                        }
                    }
                }

                if ($sellerTrackingData) {
                    $this->context->smarty->assign('sellerTrackingData', $sellerTrackingData);

                    return $this->fetch('module:marketplace/views/templates/hook/tracking-order-detail.tpl');
                }
            }
        }
    }

    public function getOrderShippingCost($cart, $shippingCost)
    {
        return $shippingCost;
    }

    public function getOrderShippingCostExternal($params)
    {
        $this->getOrderShippingCost($params, 0);
    }

    /**
     * [getOrderShippingCost To send shipping cost of carriers made by the sellers after adding impact price.
     *
     * @param [Objec]t $cart          [Object of the current cart]
     * @param [Float]  $shippingCost [previous shipping cost calculated in the Cart.php]
     *
     * @return [Float|false] [Shipping cost of the carrier after adding impact price]
     */
    public function getPackageShippingCost($cart, $shippingCost, $products)
    {
        $objMpshipProd = new WkMpSellerShipping();
        $sellerProdSum = [];
        $adminCurrDef = Configuration::get('PS_CURRENCY_DEFAULT');
        foreach ($products as $valProd) {
            $mpProdId = $objMpshipProd->checkMpProduct($valProd['id_product']);
            $mpSellerID = (new WkMpSellerProduct($mpProdId))->id_seller;
            if (!isset($sellerProdSum[$mpSellerID]['price'])) {
                if (isset($valProd['price_with_reduction'])) {
                    $sellerProdSum[$mpSellerID]['price'] = $valProd['price_with_reduction'] * $valProd['quantity'];
                }
                if (isset($valProd['weight'])) {
                    $sellerProdSum[$mpSellerID]['weight'] = $valProd['weight'] * $valProd['quantity'];
                }
            } else {
                if (isset($valProd['price_with_reduction'])) {
                    $sellerProdSum[$mpSellerID]['price'] += $valProd['price_with_reduction'] * $valProd['quantity'];
                }
                if (isset($valProd['weight'])) {
                    $sellerProdSum[$mpSellerID]['weight'] += $valProd['weight'] * $valProd['quantity'];
                }
            }
        }
        $impactPrice = 0;
        $objCarrier = new Carrier($this->id_carrier);
        $psCarrierRef = $objCarrier->id_reference;
        $objMpShipMthod = new WkMpSellerShipping();
        $mpShippingId = $objMpShipMthod->getMpShippingId($psCarrierRef);
        if ($mpShippingId) {
            $deliveryAddObj = new Address($cart->id_address_delivery);
            if (isset($deliveryAddObj->id_country) && $deliveryAddObj->id_country) {
                $objMpShipMthod = new WkMpSellerShipping($mpShippingId);
                $shippingIdSeller = $objMpShipMthod->id_seller;
                if (isset($sellerProdSum[$shippingIdSeller]['price'])) {
                    $sellerProductTotal = Tools::convertPriceFull($sellerProdSum[$shippingIdSeller]['price'], Currency::getCurrencyInstance((int) $this->context->cart->id_currency), Currency::getCurrencyInstance((int) $adminCurrDef));
                }

                if (isset($sellerProductTotal)) {
                    $shipMthod = $objCarrier->shipping_method;

                    $byPrice = 0;
                    if ($shipMthod == 2) {
                        $byPrice = 1;
                    }

                    if ($byPrice) {
                        $totlProdCart = $sellerProductTotal;
                    } else {
                        $totlProdCart = $sellerProdSum[$shippingIdSeller]['weight'];
                    }
                    $chkIdState = $deliveryAddObj->id_state;
                    $chkIdCountry = $deliveryAddObj->id_country;
                    $chkIdZone = Country::getIdZone($chkIdCountry);
                    $objImpactCrr = new WkMpSellerShippingImpact();
                    if (!$chkIdState) {
                        $impactPrice = $objImpactCrr->getImpactPriceWithRange($totlProdCart, $mpShippingId, $this->id_carrier, $chkIdZone, $chkIdCountry, $chkIdState, $byPrice, 1);
                    } else {
                        $impactPrice = $objImpactCrr->getImpactPriceWithRange($totlProdCart, $mpShippingId, $this->id_carrier, $chkIdZone, $chkIdCountry, $chkIdState, $byPrice, 0);
                    }
                }
            }
        }
        if ($impactPrice) {
            $idCarrier = $this->id_carrier;
            $idCart = $cart->id;
            $objMpCart = new WkMpSellerShippingCart();
            $isAvailable = $objMpCart->isAvailable($idCarrier, $idCart);

            if (!$isAvailable) {
                $objMpCart->id_ps_carrier = $idCarrier;
                $objMpCart->id_ps_cart = $idCart;
                $objMpCart->extra_cost = $impactPrice;
                $objMpCart->save();
            } else {
                if ($impactPrice != $isAvailable['extra_cost']) {
                    $objMpCart = new WkMpSellerShippingCart($isAvailable['id_mp_shipping_cart']);
                    $objMpCart->id_ps_carrier = $idCarrier;
                    $objMpCart->id_ps_cart = $idCart;
                    $objMpCart->extra_cost = $impactPrice;
                    $objMpCart->save();
                }
            }
        }
        $impactPrice = Tools::convertPrice($impactPrice, Currency::getCurrencyInstance((int) $this->context->cart->id_currency));
        $totalShipping = $shippingCost + $impactPrice;

        if ($totalShipping > 0) {
            return $totalShipping;
        } else {
            return 0;
        }
    }

    /**
     * [hookactionObjectCarrierUpdateAfter::  Runs when a prestashop carrier is updated changes status of mp shipping method as status changes form prestashop carries toggle and deactivetes the mp carrier when the carrier is deleted form the prestashop end.].
     *
     * @param [type] $params [description]
     *
     * @return [type] [description]
     */
    public function hookactionObjectCarrierUpdateAfter($params)
    {
        $statusCrr = Tools::getValue('statuscarrier');
        if (isset($statusCrr)) {
            $mpShippingId = WkMpSellerShipping::getMpShippingId($params['object']->id_reference);
            if ($mpShippingId) {
                if ($params['object']->active == 0) {
                    $objMpShippingMet = new WkMpSellerShipping($mpShippingId);
                    $objMpShippingMet->updateCarriersOnDeactivateOrDelete();
                }
            } else {
                // if deactive any carrier from carrier list and if that carrier is exist in admin default shipping then remove this carrier from default list
                if (!$params['object']->active) {
                    // Only for admin carriers if deactivated
                    $adminDefaultShipping = json_decode(Configuration::get('MP_SHIPPING_ADMIN_DEFAULT'));

                    if (is_array($adminDefaultShipping) && ($key = array_search($params['object']->id_reference, $adminDefaultShipping)) !== false) {
                        // remove deactivated carrier from admin default shipping
                        unset($adminDefaultShipping[$key]);
                    }

                    if ($adminDefaultShipping) {
                        // if any other admin exist then just remove deleted carrier from default carrier
                        Configuration::updateValue('MP_SHIPPING_ADMIN_DEFAULT', json_encode($adminDefaultShipping));
                    } else {
                        // if no more carrier exist
                        // $objCarrier = new Carrier(Configuration::get('PS_CARRIER_DEFAULT'));
                        // $adminDefaultShipping = array($objCarrier->id_reference);
                        $adminDefaultShipping = [];
                        Configuration::updateValue('MP_SHIPPING_ADMIN_DEFAULT', json_encode($adminDefaultShipping));
                    }

                    $objMpShippingMet = new WkMpSellerShipping();
                    $objMpShippingMet->updateCarriersOnDeactivateOrDelete();
                }
            }
        }
        if ($params['object']->id_reference && $params['object']->deleted) {
            $mpShippingId = WkMpSellerShipping::getMpShippingId($params['object']->id_reference);
            if ($mpShippingId) {
                $objMpShippingMet = new WkMpSellerShipping($mpShippingId);
                $objMpShippingMet->updateCarriersOnDeactivateOrDelete();
            }
        }
    }

    public function hookActionBeforeAddMPProduct($params)
    {
        return $this->restrictProductToAddUpdate($params);
    }

    public function hookActionBeforeUpdateMPProduct($params)
    {
        return $this->restrictProductToAddUpdate($params);
    }

    public function restrictProductToAddUpdate($params)
    {
        if (!Tools::getValue('carriers') && empty(json_decode(Configuration::get('MP_SHIPPING_ADMIN_DEFAULT')))) {
            // Seller doesn't select any shipping and admin doesn't set any default shipping
            $this->context->controller->errors[] = $this->l('Admin default shipping is not available so you can not save this product.');
        }
    }

    public function setMpShippingConfigurationVariables()
    {
        // Assign Admin first shipping as Default shipping of Admin
        $idLang = $this->context->language->id;
        $allPsCarriersArr = WkMpSellerShipping::getOnlyPrestaCarriers($idLang);
        $carrRef = [];
        if ($allPsCarriersArr) {
            foreach ($allPsCarriersArr as $carrier) {
                // first element of array for first shipping
                $carrRef[] = $carrier['id_reference'];
            }
        }
        if (!empty($carrRef)) {
            $adminDefShipping = json_encode($carrRef);
        } else {
            $adminDefShipping = 0;
        }

        if (!Configuration::updateValue('MP_SHIPPING_ADMIN_DEFAULT', $adminDefShipping)
            || !Configuration::updateValue('WK_MP_SELLER_SHIPPING', 1)
            || !Configuration::updateValue('MP_SHIPPING_ADMIN_APPROVE', 1)
            || !Configuration::updateValue('MP_MAIL_ADMIN_SHIPPING_ADDED', 1)
            || !Configuration::updateValue('MP_MAIL_SELLER_SHIPPING_APPROVAL', 1)
            || !Configuration::updateValue('MP_SHIPPING_DISTRIBUTION_ALLOW', 1)
        ) {
            return false;
        }

        return true;
    }

    public function deleteCarriersFromPS()
    {
        $objMpShipping = new WkMpSellerShipping();
        $idPsReference = $objMpShipping->getAllReferenceId();
        if ($idPsReference) {
            foreach ($idPsReference as $carrRef) {
                $idPsCarrier = WkMpSellerShipping::getCarrierIdByReference($carrRef['id_ps_reference']);
                $objCarrier = new Carrier($idPsCarrier);
                $isDeleted = $objCarrier->delete();
                if (!$isDeleted) {
                    return false;
                }
            }
        }

        return true;
    }

    public function setMpSellerLoginConfigurations()
    {
        // Save seller login default theme
        Configuration::updateValue('WK_MP_SELLER_LOGIN_THEME', 1);

        for ($i = 1; $i <= 3; ++$i) {
            for ($j = 1; $j <= 4; ++$j) {
                Tools::copy(
                    _PS_MODULE_DIR_ . $this->name . '/views/img/mpsellerlogin/slices/theme' . $i . '/icon-' . $j . '.png',
                    _PS_IMG_DIR_ . 'cms/wktheme' . $i . '-icon' . $j . '.png'
                );
            }
        }

        $idParents = [
            1 => [
                1 => 2,
                2 => 2,
                3 => 1,
                4 => 1,
            ],
            2 => [
                1 => 4,
                2 => 4,
                3 => 3,
                4 => 3,
            ],
            3 => [
                1 => 6,
                2 => 6,
                3 => 5,
                4 => 5,
            ],
        ];
        $idPostions = [
            1 => 2,
            2 => 1,
            3 => 2,
            4 => 1,
        ];
        $blockNames = [
            1 => 'termscondition',  // 1, 5, 9
            2 => 'feature',         // 2, 6, 10
            3 => 'reg_block',       // 3, 7, 11
            4 => 'reg_title',       // 4, 8, 12
        ];
        // Value has repeated data of $blockNames (only for termscondition, feature, reg_title)
        $idBlocks = [
            1 => [
                0 => 1, // termscondition
                1 => 2, // feature
                2 => 4, // reg_title
            ],
            2 => [
                0 => 5, // termscondition
                1 => 6, // feature
                2 => 8, // reg_title
            ],
            3 => [
                0 => 9, // termscondition
                1 => 10, // feature
                2 => 12, // reg_title
            ],
        ];
        $widths = [
            1 => '12',
            2 => '12',
            3 => '4',
            4 => '8',
        ];
        $blockBgColors = [
            1 => '#555555',
            2 => '#ffffff',
            3 => '#ffffff',
            4 => '#ffffff',
        ];
        $blockTextColors = [
            1 => [
                1 => '#ffffff',
                2 => '#555555',
                3 => '#555555',
                4 => '#ffffff',
            ],
            2 => [
                1 => '#ffffff',
                2 => '#555555',
                3 => '#555555',
                4 => '#ffffff',
            ],
            3 => [
                1 => '#ffffff',
                2 => '#555555',
                3 => '#555555',
                4 => '#ffffff',
            ],
        ];

        for ($i = 1; $i <= 3; ++$i) {
            $loginConfigObj = new WkMpLoginConfigration();
            $loginConfigObj->id_theme = $i;
            if ($i == 1) {
                $loginConfigObj->header_bg_color = '#0F337A';
            } elseif ($i == 3) {
                $loginConfigObj->header_bg_color = '#CACACA';
            } else {
                $loginConfigObj->header_bg_color = '#252525';
            }
            $loginConfigObj->body_bg_color = '#f7f7ff';
            foreach (Language::getLanguages(false) as $language) {
                $loginConfigObj->meta_title[$language['id_lang']] = $this->l('Seller Login');

                $loginConfigObj->meta_description[$language['id_lang']] =
                $this->l('If you want to sell your products online then from this page you can login, upload products and sell online');
            }
            $loginConfigObj->save();

            for ($j = 1; $j <= 4; ++$j) {
                $loginBlockPositionObj = new WkMpLoginBlockPosition();
                $loginBlockPositionObj->id_parent = $idParents[$i][$j];
                $loginBlockPositionObj->id_position = $idPostions[$j];
                $loginBlockPositionObj->id_theme = $i;
                $loginBlockPositionObj->block_name = $blockNames[$j];
                $loginBlockPositionObj->width = $widths[$j];
                $loginBlockPositionObj->block_bg_color = $blockBgColors[$j];
                $loginBlockPositionObj->block_text_color = $blockTextColors[$i][$j];
                $loginBlockPositionObj->active = 1;
                $loginBlockPositionObj->save();
            }

            for ($k = 0; $k <= 1; ++$k) {
                $loginParentBlockObj = new WkMpLoginParentBlock();
                $loginParentBlockObj->id_position = $k;
                $loginParentBlockObj->id_theme = $i;
                if ($k == 0) {
                    $loginParentBlockObj->name = 'registration';
                } elseif ($k == 1) {
                    $loginParentBlockObj->name = 'content';
                }
                $loginParentBlockObj->active = 1;
                $loginParentBlockObj->save();
            }

            for ($k = 0; $k <= 2; ++$k) {
                $loginContentObj = new WkMpLoginContent();
                $loginContentObj->id_block = $idBlocks[$i][$k];
                $loginContentObj->id_theme = $i;
                foreach (Language::getLanguages(false) as $language) {
                    if ($loginContentObj->id_block == 1
                    || $loginContentObj->id_block == 5
                    || $loginContentObj->id_block == 9) {
                        $this->context->smarty->assign(
                            [
                                'content' => 1,
                                'id_theme' => $i,
                            ]
                        );
                        $loginContentObj->content[$language['id_lang']] = $this->context->smarty->fetch(
                            _PS_MODULE_DIR_ . 'marketplace/views/templates/admin/_partials/content.tpl'
                        );
                    } elseif ($loginContentObj->id_block == 2) {
                        $this->context->smarty->assign(
                            [
                                'content' => 2,
                                'wk_ps_img' => Tools::getShopProtocol() . Tools::getHttpHost() . _PS_IMG_,
                            ]
                        );
                        $loginContentObj->content[$language['id_lang']] = $this->context->smarty->fetch(
                            _PS_MODULE_DIR_ . 'marketplace/views/templates/admin/_partials/content.tpl'
                        );
                    } elseif ($loginContentObj->id_block == 6) {
                        $this->context->smarty->assign(
                            [
                                'content' => 3,
                                'wk_ps_img' => Tools::getShopProtocol() . Tools::getHttpHost() . _PS_IMG_,
                            ]
                        );
                        $loginContentObj->content[$language['id_lang']] = $this->context->smarty->fetch(
                            _PS_MODULE_DIR_ . 'marketplace/views/templates/admin/_partials/content.tpl'
                        );
                    } elseif ($loginContentObj->id_block == 10) {
                        $this->context->smarty->assign(
                            [
                                'content' => 4,
                                'wk_ps_img' => Tools::getShopProtocol() . Tools::getHttpHost() . _PS_IMG_,
                            ]
                        );
                        $loginContentObj->content[$language['id_lang']] = $this->context->smarty->fetch(
                            _PS_MODULE_DIR_ . 'marketplace/views/templates/admin/_partials/content.tpl'
                        );
                    } elseif ($loginContentObj->id_block == 4
                    || $loginContentObj->id_block == 8
                    || $loginContentObj->id_block == 12) {
                        $loginContentObj->content[$language['id_lang']] = $this->l('Take Your First Step Towards Online Selling');
                    }
                }
                $loginContentObj->save();
            }
        }

        return true;
    }

    public function deleteConfigKeys()
    {
        $var = [
            'WK_MP_SUPERADMIN_EMAIL', 'WK_MP_SELLER_ADMIN_APPROVE', 'WK_MP_PRODUCT_ADMIN_APPROVE',
            'WK_MP_MULTILANG_ADMIN_APPROVE', 'WK_MP_MULTILANG_DEFAULT_LANG',
            'WK_MP_SHOW_SELLER_DETAILS', 'WK_MP_GLOBAL_COMMISSION_TYPE', 'WK_MP_GLOBAL_COMMISSION',
            'WK_MP_PRODUCT_TAX_DISTRIBUTION', 'WK_MP_REVIEWS_ADMIN_APPROVE', 'WK_MP_GLOBAL_TAX_FIXED_COMMISSION',
            'WK_MP_TITLE_BG_COLOR', 'WK_MP_TITLE_TEXT_COLOR', 'WK_MP_PHONE_DIGIT',
            'WK_MP_SELLER_SHOP_SETTINGS', 'WK_MP_SELLER_PRODUCTS_SETTINGS', 'WK_MP_SELLER_COUNTRY_NEED',
            'WK_MP_FROM_MAIL_TITLE', 'WK_MP_MAIL_SELLER_REQ_APPROVE',
            'WK_MP_MAIL_SELLER_REQ_DISAPPROVE', 'WK_MP_MAIL_SELLER_PRODUCT_APPROVE',
            'WK_MP_MAIL_SELLER_PRODUCT_DISAPPROVE', 'WK_MP_MAIL_SELLER_PRODUCT_SOLD',
            'WK_MP_MAIL_ADMIN_PRODUCT_ADD', 'WK_MP_MAIL_PRODUCT_DELETE', 'WK_MP_MAIL_SELLER_DELETE',
            'WK_MP_MAIL_ADMIN_SELLER_REQUEST', 'WK_MP_MAIL_SELLER_PRODUCT_ASSIGN',
            'WK_MP_SELLER_PRODUCTS_DEACTIVATE_REASON', 'WK_MP_SELLER_PROFILE_DEACTIVATE_REASON',
            'WK_MP_SHOW_ADMIN_COMMISSION', 'WK_MP_TERMS_AND_CONDITIONS_CMS',
            'WK_MP_TERMS_AND_CONDITIONS_STATUS', 'WK_MP_SELLER_DETAILS_ACCESS',
            'WK_MP_COMMISSION_DISTRIBUTE_ON', 'WK_MP_URL_REWRITE_ADMIN_APPROVE',
            'WK_MP_SELLER_PROFILE_PREFIX', 'WK_MP_SELLER_SHOP_PREFIX', 'WK_MP_SELLER_APPLIED_TAX_RULE',
            'WK_MP_LINK_ON_NAV_BAR', 'WK_MP_LINK_ON_FOOTER_BAR', 'WK_MP_LINK_ON_POP_UP',
            'WK_MP_PRODUCT_UPDATE_ADMIN_APPROVE', 'WK_MP_SELLER_ADMIN_SHIPPING',
            'WK_MP_SELLER_PRODUCT_ISBN', 'WK_MP_SELLER_PRODUCT_UPC', 'WK_MP_SELLER_PRODUCT_EAN',
            'WK_MP_SELLER_PRODUCT_REFERENCE', 'WK_MP_SELLER_PRODUCT_COMBINATION', 'WK_MP_PRODUCT_FEATURE',
            'WK_MP_SOCIAL_TABS', 'WK_MP_SELLER_FACEBOOK', 'WK_MP_SELLER_TWITTER',
            'WK_MP_SELLER_YOUTUBE', 'WK_MP_SELLER_INSTAGRAM', 'WK_MP_ALLOW_CUSTOM_CSS',
            'WK_MP_SELLER_PRODUCT_AVAILABILITY', 'WK_MP_SELLER_PRODUCT_VISIBILITY', 'WK_MP_SELLER_PRODUCT_SEO',
            'WK_MP_DASHBOARD_GRAPH', 'WK_MP_PRESTA_FEATURE_ACCESS', 'WK_MP_PRESTA_ATTRIBUTE_ACCESS',
            'WK_MP_PRODUCT_MIN_QTY', 'WK_MP_PRODUCT_CONDITION', 'WK_MP_PRODUCT_WHOLESALE_PRICE',
            'WK_MP_PRODUCT_PRICE_PER_UNIT', 'WK_MP_SHOW_ADMIN_DETAILS', 'WK_MP_SELLER_FAX',
            'WK_MP_SELLER_DETAILS_PERMISSION', 'WK_MP_SELLER_ORDER_STATUS_CHANGE', 'WK_MP_CONTACT_SELLER_SETTINGS',
            'WK_MP_PRODUCT_ON_SALE', 'WK_MP_PRODUCT_DELIVERY_TIME', 'WK_MP_PRODUCT_ADDITIONAL_FEES',
            'WK_MP_PRODUCT_LOW_STOCK_ALERT', 'WK_MP_REVIEW_DISPLAY_SORT', 'WK_MP_REVIEW_HELPFUL_SETTINGS',
            'WK_MP_REVIEW_SETTINGS', 'WK_MP_SELLER_TAX_IDENTIFICATION_NUMBER', 'WK_MP_PRODUCT_ALLOW_DUPLICATE',
            'WK_MP_PRODUCT_DUPLICATE_TITLE', 'WK_MP_SELLER_REVIEWS_PREFIX', 'WK_MP_REVIEW_DISPLAY_COUNT',
            'WK_MP_PRODUCT_DUPLICATE_QUANTITY', 'WK_MP_SHIPPING_DISTRIBUTION_ALLOW',
            'WK_MP_SHIPPING_ADMIN_DISTRIBUTION', 'WK_MP_SELLER_ORDER_TRACKING_ALLOW', 'WK_MP_TRACKING_PS_UPDATE_ALLOW',
            'WK_MP_TRACKING_ORDER_HISTORY_ALLOW', 'WK_MP_GLOBAL_COMMISSION_AMOUNT', 'WK_MP_TRACKING_NUMBER_IN_URL',
            'WK_MP_GLOBAL_SHIPPING_COMMISSION', 'WK_MP_PACK_PRODUCTS', 'WK_MP_VIRTUAL_PRODUCT',
            'WK_MP_PRODUCT_SPECIFIC_RULE', 'WK_MP_PRODUCT_TAGS', 'WK_MP_PRODUCT_MANUFACTURER',
            'WK_MP_PRODUCT_MANUFACTURER_ADMIN', 'WK_MP_PRODUCT_MANUFACTURER_APPROVED', 'WK_MP_PRODUCT_CUSTOMIZATION',
            'WK_MP_PRODUCT_SUPPLIER', 'WK_MP_PRODUCT_SUPPLIER_ADMIN', 'WK_MP_PRODUCT_SUPPLIER_APPROVED',
            'WK_MP_RELATED_PRODUCT', 'WK_MP_PRODUCT_STOCK_LOCATION', 'WK_MP_PRODUCT_MPN',
            'WK_MP_PRODUCT_PAGE_REDIRECTION', 'WK_MP_PRODUCT_IMAGE_CAPTION', 'WK_MP_PRODUCT_ATTACHMENT',
            'WK_MP_PRODUCT_CATEGORY_RESTRICTION', 'WK_MP_SELLER_EXPORT', 'WK_MP_SELLER_SHIPPING',
            'MP_SHIPPING_ADMIN_DEFAULT', 'MP_SHIPPING_DISTRIBUTION_ALLOW',
            'MP_SHIPPING_ADMIN_APPROVE', 'MP_MAIL_ADMIN_SHIPPING_ADDED', 'MP_MAIL_SELLER_SHIPPING_APPROVAL',
            'WK_MP_SELLER_LOGIN_PREFIX',
        ];
        foreach ($var as $key) {
            if (!Configuration::deleteByName($key)) {
                return false;
            }
        }

        return true;
    }

    public function uninstallTab()
    {
        $moduleTabs = Tab::getCollectionFromModule($this->name);
        if (!empty($moduleTabs)) {
            foreach ($moduleTabs as $moduleTab) {
                $moduleTab->delete();
            }
        }

        return true;
    }

    public function uninstall($keep = true)
    {
        $objMpInstall = new WkMpInstall();

        if (!parent::uninstall()
            || !$this->deleteCarriersFromPS()
            || ($keep && !$objMpInstall->deleteMpTables())
            || !WkMpSeller::deleteTinymceSourceFile()
            || !$this->uninstallTab()
            || !$this->deleteConfigKeys()) {
            return false;
        }

        return true;
    }

    public function reset()
    {
        if (!$this->uninstall(false)) {
            return false;
        }
        if (!$this->install(false)) {
            return false;
        }

        return true;
    }

    public function registerMpHook()
    {
        return $this->registerHook([
            'displayCustomerAccount', 'actionFrontControllerSetMedia', 'displayMPMyAccountMenu',
            'actionProductDelete', 'displayBackOfficeHeader',
            'displayAdminOrder', 'actionObjectLanguageAddAfter',
            'actionObjectLanguageUpdateAfter', 'actionObjectLanguageDeleteAfter',
            'moduleRoutes', 'displayNav1', 'displayHeader',
            'displayProductButtons', 'displayProductAdditionalInfo', 'actionOrderStatusPostUpdate',
            'displayMpMenu', 'actionObjectProductUpdateBefore', 'registerGDPRConsent',
            'displayMyAccountBlock', 'actionDeleteGDPRCustomer',
            'actionExportGDPRData', 'displayAdminAfterHeader', 'actionAdminCarriersListingFieldsModifier',
            'actionAdminControllerSetMedia', 'displayOrderDetail', 'actionObjectCarrierUpdateAfter',
            'displayProductPriceBlock', 'actionObjectCustomerDeleteAfter',
        ]);
    }

    public function installTab($className, $tabName, $tabParentName = false)
    {
        $tabParentId = 0; // Tab will display in Back-End
        if ($tabParentName) {
            $this->createMarketplaceModuleTab($className, $tabName, $tabParentId, $tabParentName);
        } else {
            $this->createMarketplaceModuleTab($className, $tabName, $tabParentId);
        }
    }

    public function installHiddenTab($className, $tabName)
    {
        // Tab will not display in Back-End, only we can use as an admin controller
        $tabParentId = -1;
        $this->createMarketplaceModuleTab($className, $tabName, $tabParentId);
    }

    public function createMarketplaceModuleTab($className, $tabName, $tabParentId, $tabParentName = false)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $className;
        $tab->name = [];

        if ($className == 'AdminMarketplaceManagement') { // Tab name for which you want to add icon
            $tab->icon = 'shopping_cart'; // Material Icon name
        }

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tabName;
        }

        if ($tabParentName) {
            $tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
        } else {
            $tab->id_parent = $tabParentId;
        }

        $tab->module = $this->name;

        return $tab->add();
    }

    public function callInstallTab()
    {
        $this->installTab('AdminMarketplace', 'Marketplace');
        $this->installTab('AdminMarketplaceManagement', 'Marketplace', 'AdminMarketplace');

        // configuration controller
        $this->installTab('AdminManageConfiguration', 'Configuration', 'AdminMarketplaceManagement');
        $this->installTab('AdminMarketplaceGeneralSettings', 'General Settings', 'AdminManageConfiguration');
        $this->installTab('AdminMarketplaceApprovalSettings', 'Approval Settings', 'AdminManageConfiguration');
        $this->installTab('AdminMpCustomizeLogin', 'Seller Login Configuration', 'AdminManageConfiguration');
        $this->installTab('AdminCustomerCommision', 'Commission  Settings', 'AdminManageConfiguration');
        $this->installTab('AdminPaymentMode', 'Payment Modes', 'AdminManageConfiguration');

        // seller controller
        $this->installTab('AdminManageSellerDetails', 'Sellers', 'AdminMarketplaceManagement');
        $this->installTab('AdminSellerInfoDetail', 'Seller Profile', 'AdminManageSellerDetails');
        $this->installTab('AdminSellerReviews', 'Seller Reviews', 'AdminManageSellerDetails');

        // product controller
        $this->installTab('AdminManageSellerProduct', 'Products', 'AdminMarketplaceManagement');
        $this->installTab('AdminSellerProductDetail', 'Seller Product', 'AdminManageSellerProduct');

        // order controller
        $this->installTab('AdminManageSellerOrders', 'Orders', 'AdminMarketplaceManagement');
        $this->installTab('AdminSellerOrders', 'Seller Orders', 'AdminManageSellerOrders');

        // Transaction controller
        $this->installTab('AdminManageSellerTransactions', 'Transactions', 'AdminMarketplaceManagement');
        $this->installTab('AdminSellerTransactions', 'Seller Transactions', 'AdminManageSellerTransactions');

        // Mp Combination Tab that will not display as a Tab in backend
        $this->installHiddenTab('AdminMpAttributeManage', 'Product Combination');
        $this->installHiddenTab('AdminMpGenerateCombination', 'Attribute Generator');
        $this->installHiddenTab('AdminMpShippingCommission', 'Shipping Commission Settings');

        // Manufacturer controller
        $this->installTab('AdminManufacturerDetail', 'Brands', 'AdminMarketplaceManagement');

        // Supplier controller
        $this->installTab('AdminMpSuppliers', 'Suppliers', 'AdminMarketplaceManagement');

        // Shipping controller
        $this->installTab('AdminMpSellerShipping', 'Carriers', 'AdminMarketplaceManagement');

        return true;
    }

    public function install()
    {
        $objMpInstall = new WkMpInstall();

        if (Shop::isFeatureActive()) { // For multishop
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install()
            || !$objMpInstall->createMpTables()
            || !$this->registerMpHook()
            || !$this->callInstallTab()
            || !$this->setMpSellerConfigurationAsApproved()
            || !$this->setMpMailConfigurationAsApproved()
            || !$this->setMpUrlRewriteConfigurationAsApproved()
            || !$this->setMpAdvertisementConfiguration()
            || !$this->setMpSocialTabConfiguration()
            || !$this->setMpShippingConfigurationVariables()
            || !$this->setMpSellerLoginConfigurations()
            || !$this->createMailLangDirectoryWithFiles()
        ) {
            return false;
        }

        return true;
    }

    /**
     * Ps all imported language's Mail directory will be created with all files in module's mails folder
     *
     * @return tpl
     */
    public function createMailLangDirectoryWithFiles()
    {
        $mailEnDir = _PS_MODULE_DIR_ . 'marketplace/mails/en/';
        if (is_dir($mailEnDir)) {
            $allFiles = scandir($mailEnDir);
            $allLanguages = Language::getLanguages(false, $this->context->shop->id);
            if ($allLanguages) {
                $moduleMailDir = _PS_MODULE_DIR_ . 'marketplace/mails/';
                foreach ($allLanguages as $language) {
                    $langISO = $language['iso_code'];
                    // Ignore 'en' and 'fr' directory because we already have this in our module folder
                    if ($langISO != 'en' && $langISO != 'fr') {
                        // create lang dir if not exist in module mails directory
                        if (!file_exists($moduleMailDir . $langISO)) {
                            @mkdir($moduleMailDir . $langISO, 0777, true);
                        }

                        // Now if lang dir is exist or created by above code
                        if (is_dir($moduleMailDir . $langISO)) {
                            foreach ($allFiles as $fileName) {
                                if ($fileName != '.' && $fileName != '..') {
                                    $source = $mailEnDir . $fileName;
                                    $destination = $moduleMailDir . $langISO . '/' . $fileName;
                                    // if file not exist in desti directory then create that file
                                    if (!file_exists($destination) && file_exists($source)) {
                                        Tools::copy($source, $destination);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    public function setMpSellerConfigurationAsApproved()
    {
        if (!Configuration::updateValue('WK_MP_SELLER_ADMIN_APPROVE', 1)
            || !Configuration::updateValue('WK_MP_PRODUCT_ADMIN_APPROVE', 1)
            || !Configuration::updateValue('WK_MP_SELLER_APPLIED_TAX_RULE', 1)
            || !Configuration::updateValue('WK_MP_SELLER_PRODUCT_SEO', 1)
            || !Configuration::updateValue('WK_MP_SELLER_PRODUCT_VISIBILITY', 1)
            || !Configuration::updateValue('WK_MP_SELLER_PRODUCT_AVAILABILITY', 1)
            || !Configuration::updateValue('WK_MP_SELLER_PRODUCT_REFERENCE', 1)
            || !Configuration::updateValue('WK_MP_PRODUCT_MPN', 1)
            || !Configuration::updateValue('WK_MP_SELLER_PRODUCT_UPC', 1)
            || !Configuration::updateValue('WK_MP_SELLER_PRODUCT_EAN', 1)
            || !Configuration::updateValue('WK_MP_SELLER_PRODUCT_ISBN', 1)
            || !Configuration::updateValue('WK_MP_SELLER_PRODUCT_COMBINATION', 1)
            || !Configuration::updateValue('WK_MP_SELLER_ADMIN_SHIPPING', 1)
            || !Configuration::updateValue('WK_MP_PRODUCT_DELIVERY_TIME', 1)
            || !Configuration::updateValue('WK_MP_PRODUCT_FEATURE', 1)
            || !Configuration::updateValue('WK_MP_PRODUCT_CONDITION', 1)
            || !Configuration::updateValue('WK_MP_PRODUCT_MIN_QTY', 1)
            || !Configuration::updateValue('WK_MP_PRODUCT_WHOLESALE_PRICE', 1)
            || !Configuration::updateValue('WK_MP_PRODUCT_PRICE_PER_UNIT', 1)
            || !Configuration::updateValue('WK_MP_PRODUCT_TAGS', 1)
            || !Configuration::updateValue('WK_MP_PRODUCT_CUSTOMIZATION', 1)
            || !Configuration::updateValue('WK_MP_PRODUCT_ATTACHMENT', 1)
            || !Configuration::updateValue('WK_MP_PRODUCT_SPECIFIC_RULE', 1)
            || !Configuration::updateValue('WK_MP_REVIEWS_ADMIN_APPROVE', 1)
            || !Configuration::updateValue('WK_MP_SHOW_SELLER_DETAILS', 1)
            || !Configuration::updateValue('WK_MP_SELLER_ORDER_STATUS_CHANGE', 1)
            || !Configuration::updateValue('WK_MP_SELLER_DETAILS_PERMISSION', 1)
            || !Configuration::updateValue('WK_MP_MULTILANG_ADMIN_APPROVE', 1)
            || !Configuration::updateValue('WK_MP_MULTILANG_DEFAULT_LANG', 1)
            || !Configuration::updateValue('WK_MP_COMMISSION_DISTRIBUTE_ON', 2)
            || !Configuration::updateValue('WK_MP_ALLOW_CUSTOM_CSS', 1)
            || !Configuration::updateValue('WK_MP_TITLE_BG_COLOR', '#ffffff')
            || !Configuration::updateValue('WK_MP_TITLE_TEXT_COLOR', '#232323')
            || !Configuration::updateValue('WK_MP_PHONE_DIGIT', 10)
            || !Configuration::updateValue('WK_MP_GLOBAL_COMMISSION_TYPE', WkMpCommission::WK_COMMISSION_PERCENTAGE)
            || !Configuration::updateValue('WK_MP_GLOBAL_COMMISSION', 10)
            || !Configuration::updateValue('WK_MP_PRODUCT_TAX_DISTRIBUTION', 'admin')// default tax distribute to admin
            || !Configuration::updateValue('WK_MP_GLOBAL_TAX_FIXED_COMMISSION', 0)
            || !Configuration::updateValue('WK_MP_DASHBOARD_GRAPH', '2')
            || !Configuration::updateValue('WK_MP_REVIEW_SETTINGS', '1')
            || !Configuration::updateValue('WK_MP_REVIEW_DISPLAY_SORT', '1')
            || !Configuration::updateValue('WK_MP_REVIEW_DISPLAY_COUNT', '2')
            || !Configuration::updateValue('WK_MP_SELLER_ORDER_TRACKING_ALLOW', 1)
            || !Configuration::updateValue('WK_MP_GLOBAL_SHIPPING_COMMISSION', 10)
        ) {
            return false;
        }

        if ($idEmployee = WkMpHelper::getSupperAdmin()) {
            $employee = new Employee($idEmployee);
            Configuration::updateValue('WK_MP_SUPERADMIN_EMAIL', $employee->email);
        }

        if ($this->sellerDetailsView) {
            $sellerDetailsAccess = [];
            foreach ($this->sellerDetailsView as $sellerDetailsViewVal) {
                $sellerDetailsAccess[] = $sellerDetailsViewVal['id_group'];
            }

            Configuration::updateValue('WK_MP_SELLER_DETAILS_ACCESS', json_encode($sellerDetailsAccess));
        }

        $sellerOrderStatus = [];
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        asort($statuses);
        foreach ($statuses as $status) {
            $sellerOrderStatus[] = $status['id_order_state'];
        }
        Configuration::updateValue('WK_MP_SELLER_ORDER_STATUS_ACCESS', json_encode($sellerOrderStatus));

        return true;
    }

    public function setMpMailConfigurationAsApproved()
    {
        if (!Configuration::updateValue('WK_MP_MAIL_SELLER_REQ_APPROVE', 1)
            || !Configuration::updateValue('WK_MP_MAIL_SELLER_REQ_DISAPPROVE', 1)
            || !Configuration::updateValue('WK_MP_MAIL_SELLER_PRODUCT_APPROVE', 1)
            || !Configuration::updateValue('WK_MP_MAIL_SELLER_PRODUCT_DISAPPROVE', 1)
            || !Configuration::updateValue('WK_MP_MAIL_SELLER_PRODUCT_ASSIGN', 1)
            || !Configuration::updateValue('WK_MP_MAIL_SELLER_PRODUCT_SOLD', 1)
            || !Configuration::updateValue('WK_MP_MAIL_ADMIN_SELLER_REQUEST', 1)
            || !Configuration::updateValue('WK_MP_MAIL_ADMIN_PRODUCT_ADD', 1)
            || !Configuration::updateValue('WK_MP_MAIL_SELLER_DELETE', 1)
            || !Configuration::updateValue('WK_MP_MAIL_PRODUCT_DELETE', 1)
        ) {
            return false;
        }

        return true;
    }

    public function setMpUrlRewriteConfigurationAsApproved()
    {
        if (!Configuration::updateValue('WK_MP_URL_REWRITE_ADMIN_APPROVE', 1)
        || !Configuration::updateValue('WK_MP_SELLER_PROFILE_PREFIX', 'profile')
        || !Configuration::updateValue('PS_ROUTE_module-marketplace-sellerprofile', 'profile/{:mp_shop_name}')
        || !Configuration::updateValue('WK_MP_SELLER_SHOP_PREFIX', 'shop')
        || !Configuration::updateValue('PS_ROUTE_module-marketplace-shopstore', 'shop/{:mp_shop_name}')
        || !Configuration::updateValue('WK_MP_SELLER_REVIEWS_PREFIX', 'reviews')
        || !Configuration::updateValue('PS_ROUTE_module-marketplace-allreviews', 'reviews/{:mp_shop_name}')
        || !Configuration::updateValue('WK_MP_SELLER_LOGIN_PREFIX', 'sellerlogin')
        || !Configuration::updateValue('PS_ROUTE_module-marketplace-sellerlogin', 'sellerlogin')
        ) {
            return false;
        }

        return true;
    }

    public function setMpAdvertisementConfiguration()
    {
        if (!Configuration::updateValue('WK_MP_LINK_ON_NAV_BAR', 1)
            || !Configuration::updateValue('WK_MP_LINK_ON_FOOTER_BAR', 1)
        ) {
            return false;
        }

        return true;
    }

    public function setMpSocialTabConfiguration()
    {
        if (!Configuration::updateValue('WK_MP_SOCIAL_TABS', 1)
            || !Configuration::updateValue('WK_MP_SELLER_FACEBOOK', 1)
            || !Configuration::updateValue('WK_MP_SELLER_TWITTER', 1)
            || !Configuration::updateValue('WK_MP_SELLER_YOUTUBE', 1)
            || !Configuration::updateValue('WK_MP_SELLER_INSTAGRAM', 1)
        ) {
            return false;
        }

        return true;
    }
}
