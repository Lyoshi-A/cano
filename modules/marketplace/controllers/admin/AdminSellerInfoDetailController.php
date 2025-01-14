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

class AdminSellerInfoDetailController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->table = 'wk_mp_seller';
        $this->className = 'WkMpSeller';
        $this->identifier = 'id_seller';
        parent::__construct();
        $this->toolbar_title = $this->l('Seller Profile');
        $this->_select = 'CONCAT(a.`seller_firstname`, " ",
        a.`seller_lastname`) as seller_name,
        a.`id_seller` as temp_seller_id';

        $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`seller_customer_id`)';
        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            // In case of All Shops
            $this->_select .= ',shp.`name` as wk_ps_shop_name';
            $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'shop` shp ON (shp.`id_shop` = a.`id_shop`)';
        }
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_mp_seller_lang` msil ON (msil.`id_seller` = a.`id_seller`)';

        $this->_where = WkMpSeller::addSqlRestriction('a');
        $this->_where .= ' AND msil.`id_lang` = ' . (int) $this->context->language->id;

        $hookResponse = Hook::exec('displayAdminSellerInfoJoin', [], null, true);
        if ($hookResponse) {
            foreach ($hookResponse as $key => $value) {
                $this->_join .= $hookResponse[$key]['join'];
                $this->_select .= $hookResponse[$key]['select'];
            }
        }

        $this->fields_list = [];
        $this->fields_list['id_seller'] = [
            'title' => $this->l('ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
        ];

        $this->fields_list['seller_customer_id'] = [
            'title' => $this->l('Customer ID'),
            'align' => 'center',
            'callback' => 'checkCustomerId',
        ];

        $this->fields_list['seller_name'] = [
            'title' => $this->l('Seller name'),
            'havingFilter' => true,
        ];

        $this->fields_list['business_email'] = [
            'title' => $this->l('Business email'),
            'havingFilter' => true,
        ];

        $this->fields_list['shop_name_unique'] = [
            'title' => $this->l('Unique shop name'),
            'havingFilter' => true,
        ];

        $this->fields_list['phone'] = [
            'title' => $this->l('Phone'),
            'align' => 'center',
            'havingFilter' => true,
        ];

        $this->fields_list['default_lang'] = [
            'title' => $this->l('Default language'),
            'align' => 'center',
            'callback' => 'callSellerLanguage',
            'search' => false,
        ];

        $this->fields_list['date_add'] = [
            'title' => $this->l('Registration'),
            'type' => 'date',
            'align' => 'text-right',
            'havingFilter' => true,
        ];

        if ($hookResponse) {
            foreach ($hookResponse as $key => $value) {
                $this->fields_list[$value['column_name']] = [
                    'title' => $value['field_name'],
                ];
                if (isset($value['attributes'])) {
                    foreach ($value['attributes'] as $key1 => $value1) {
                        $this->fields_list[$value['column_name']][$key1] = $value1;
                    }
                }
            }
        }

        if (WkMpHelper::isMultiShopEnabled()) {
            if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
                // In case of All Shops
                $this->fields_list['wk_ps_shop_name'] = [
                    'title' => $this->l('Shop'),
                    'havingFilter' => true,
                    'orderby' => false,
                ];
            }
        }

        $this->fields_list['active'] = [
            'title' => $this->l('Status'),
            'active' => 'status',
            'align' => 'center',
            'type' => 'bool',
            'orderby' => false,
        ];

        $this->fields_list['temp_seller_id'] = [
            'title' => $this->l('View profile'),
            'align' => 'center',
            'search' => false,
            'remove_onclick' => true,
            'hint' => $this->l('View profile of active sellers'),
            'callback' => 'previewProfile',
            'orderby' => false,
        ];

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ],
            'enableSelection' => [
                'text' => $this->l('Enable selection'),
                'icon' => 'icon-power-off text-success',
            ],
            'disableSelection' => [
                'text' => $this->l('Disable selection'),
                'icon' => 'icon-power-off text-danger',
            ],
        ];
    }

    public function callSellerLanguage($idLang)
    {
        $language = Language::getLanguage((int) $idLang);

        return $language['name'];
    }

    public function previewProfile($idSeller)
    {
        if ($idSeller) {
            $sellerData = WkMpSeller::getSeller($idSeller);
            if ($sellerData && $sellerData['active']) {
                $sellerProfileLink = $this->context->link->getModuleLink(
                    'marketplace',
                    'sellerprofile',
                    ['mp_shop_name' => $sellerData['link_rewrite']],
                    null,
                    null,
                    (int) $sellerData['id_shop']
                );

                $this->context->smarty->assign([
                    'callback' => 'previewProfile',
                    'sellerProfileLink' => $sellerProfileLink,
                ]);

                return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/wk_admin_callback.tpl');
            }
        }
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('view');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = [
            'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
            'desc' => $this->l('Add new seller'),
        ];
    }

    public function postProcess()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        Media::addJsDef([
            'back_end' => 1,
            'is_need_reason' => Configuration::get('WK_MP_SELLER_PROFILE_DEACTIVATE_REASON'),
            'no_image_path' => _MODULE_DIR_ . $this->module->name . '/views/img/home-default.jpg',
        ]);

        $this->addJS(_PS_JS_DIR_ . 'tiny_mce/tiny_mce.js');
        if (version_compare(_PS_VERSION_, '1.6.0.11', '>')) {
            $this->addJS(_PS_JS_DIR_ . 'admin/tinymce.inc.js');
        } else {
            $this->addJS(_PS_JS_DIR_ . 'tinymce.inc.js');
        }

        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/mp_global_style.css');

        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/libs/jquery.raty.min.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/mp_form_validation.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/sellerprofile.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/change_multilang.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/getstate.js');

        // send reason for deactivating product
        if ($idSellerForReason = Tools::getValue('actionId_for_reason')) {
            $this->makeSellerPartner($idSellerForReason, Tools::getValue('reason_text'));
            Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token . '&conf=5');
        }

        if (Tools::isSubmit('statuswk_mp_seller')) {
            $this->makeSellerPartner();
        }

        parent::postProcess();
    }

    public function renderView()
    {
        $idSeller = Tools::getValue('id_seller');

        $mpSeller = WkMpSeller::getSeller($idSeller, $this->context->language->id);
        if ($mpSeller && is_array($mpSeller) && $mpSeller['seller_customer_id']) {
            $idCustomer = $mpSeller['seller_customer_id'];
            $objCustomer = new Customer($idCustomer);

            $objMpCustomerPayment = new WkMpCustomerPayment();
            if ($paymentDetail = $objMpCustomerPayment->getPaymentDetailByIdCustomer($idCustomer)) {
                $this->context->smarty->assign('payment_detail', $paymentDetail);
            }

            if ($gender = new Gender($objCustomer->id_gender, $this->context->language->id)) {
                $this->context->smarty->assign('gender', $gender);
            }

            // Check if seller image exist
            $sellerImagePath = WkMpSeller::getSellerImageLink($mpSeller);
            if ($sellerImagePath) {
                $this->context->smarty->assign('seller_img_path', $sellerImagePath);
            } else {
                $this->context->smarty->assign('seller_default_img_path', _MODULE_DIR_ . $this->module->name . '/views/img/seller_img/defaultimage.jpg');
            }

            // Check if shop image exist
            $shopImagePath = WkMpSeller::getShopImageLink($mpSeller);
            if ($shopImagePath) {
                $this->context->smarty->assign('shop_img_path', $shopImagePath);
            } else {
                $this->context->smarty->assign('shop_default_img_path', _MODULE_DIR_ . $this->module->name . '/views/img/shop_img/defaultshopimage.jpg');
            }

            // Review Details
            if ($avgRating = WkMpSellerReview::getSellerAvgRating($idSeller)) {
                $this->context->smarty->assign('avg_rating', $avgRating);
            }

            if (empty($objCustomer->id)) {
                $this->context->smarty->assign('customer_id', 0);
            }

            $mpSeller['mp_shop_rewrite'] = $mpSeller['link_rewrite'];
            $sellerLangaugeData = Language::getLanguage((int) $mpSeller['default_lang']);
            $mpSeller['default_lang'] = $sellerLangaugeData['name'];

            if ($mpSeller['id_country']) {
                $mpSeller['country'] = Country::getNameById($this->context->language->id, $mpSeller['id_country']);
            }
            if ($mpSeller['id_state']) {
                $mpSeller['state'] = State::getNameById($mpSeller['id_state']);
            }

            $this->context->smarty->assign(
                [
                    'timestamp' => WkMpHelper::getTimestamp(),
                    'mp_seller' => $mpSeller,
                    'modules_dir' => _MODULE_DIR_,
                ]
            );
        } else {
            Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
        }

        return parent::renderView();
    }

    public function renderForm()
    {
        $getCurrentLanguage = $this->context->language->id;
        $sellerInfo = new WkMpSeller();

        if ($this->display == 'add') {
            $customerInfo = $sellerInfo->getNonSellerCustomer();
            if ($customerInfo) {
                if (WkMpHelper::isMultiShopEnabled()) {
                    if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP) {
                        $this->context->smarty->assign('all_shop', 0);
                    } else {
                        foreach ($customerInfo as &$customer) {
                            $objShop = new Shop($customer['id_shop']);
                            $customer['ps_shop_name'] = $objShop->name;
                        }
                        $this->context->smarty->assign('all_shop', 1);
                    }
                }
                $this->context->smarty->assign('customer_info', $customerInfo);
            }
        } elseif ($this->display == 'edit') {
            if (Tools::getValue('id_seller')) {
                $mpIdSeller = Tools::getValue('id_seller');
            } else {
                $mpIdSeller = Tools::getValue('mp_seller_id');
            }

            $mpSellerInfo = WkMpSeller::getSeller($mpIdSeller);
            if (isset($mpSellerInfo['id_seller']) && $mpSellerInfo['id_seller']) {
                $mpSellerLangInfo = $sellerInfo->getSellerShopLang($mpIdSeller);
                if ($mpSellerLangInfo) {
                    foreach ($mpSellerLangInfo as $mpSellerInfoVal) {
                        $mpSellerInfo['shop_name'][$mpSellerInfoVal['id_lang']] = $mpSellerInfoVal['shop_name'];
                        $mpSellerInfo['about_shop'][$mpSellerInfoVal['id_lang']] = $mpSellerInfoVal['about_shop'];
                    }
                }
                // category restriction
                if (Configuration::get('WK_MP_PRODUCT_CATEGORY_RESTRICTION')) {
                    $mpSellerInfo['category_permission'] = json_decode($mpSellerInfo['category_permission']);
                }

                $this->context->smarty->assign([
                    'edit' => 1,
                    'mp_seller_info' => $mpSellerInfo,
                    'seller_default_img_path' => _MODULE_DIR_ . $this->module->name . '/views/img/seller_img/defaultimage.jpg',
                    'shop_default_img_path' => _MODULE_DIR_ . $this->module->name . '/views/img/shop_img/defaultshopimage.jpg',
                    'no_image_path' => _MODULE_DIR_ . $this->module->name . '/views/img/home-default.jpg',
                    'timestamp' => WkMpHelper::getTimestamp(), // timestamp to stop image caching
                ]);

                // Check if seller image exist
                $sellerImagePath = WkMpSeller::getSellerImageLink($mpSellerInfo);
                if ($sellerImagePath) {
                    $this->context->smarty->assign('seller_img_path', $sellerImagePath);
                }

                // Check if seller banner exist
                $sellerBannerPath = WkMpSeller::getSellerBannerLink($mpSellerInfo);
                if ($sellerBannerPath) {
                    $this->context->smarty->assign('seller_banner_path', $sellerBannerPath);
                }

                // Check if shop image exist
                $shopImagePath = WkMpSeller::getShopImageLink($mpSellerInfo);
                if ($shopImagePath) {
                    $this->context->smarty->assign('shop_img_path', $shopImagePath);
                }

                // Check if shop banner exist
                $shopBannerPath = WkMpSeller::getShopBannerLink($mpSellerInfo);
                if ($shopBannerPath) {
                    $this->context->smarty->assign('shop_banner_path', $shopBannerPath);
                }

                $getCurrentLanguage = WkMpSeller::getSellerDefaultLanguage($mpIdSeller);

                if (isset($mpSellerInfo['seller_details_access']) && $mpSellerInfo['seller_details_access']) {
                    $this->context->smarty->assign(
                        'selectedDetailsBySeller',
                        json_decode($mpSellerInfo['seller_details_access'])
                    );
                }

                // get seller selected payment
                if (isset($mpSellerInfo['seller_customer_id']) && $mpSellerInfo['seller_customer_id']) {
                    $mpPayment = new WkMpCustomerPayment();
                    if ($sellerPayments = $mpPayment->getPaymentDetailByIdCustomer($mpSellerInfo['seller_customer_id'])) {
                        $this->context->smarty->assign('seller_payment_details', $sellerPayments);
                    }
                }
            } else {
                Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
            }
        }

        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
            $this->context->smarty->assign('allow_multilang', 1);
            $currentLang = $getCurrentLanguage;
        } else {
            $this->context->smarty->assign('allow_multilang', 0);
            if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') { // Admin default lang
                $currentLang = Configuration::get('PS_LANG_DEFAULT');
            } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') { // Seller default lang
                if (isset($mpIdSeller)) {
                    $currentLang = WkMpSeller::getSellerDefaultLanguage($mpIdSeller);
                } else {
                    $currentLang = $getCurrentLanguage;
                }
            }
        }

        $objLang = new Language((int) $currentLang);
        if (!$objLang->active) {
            $currentLang = Configuration::get('PS_LANG_DEFAULT');
        }

        // Settings (Permission) for seller to display selected details of seller
        $selectedDetailsByAdmin = [];
        $sellerDetailsAccess = json_decode(Configuration::get('WK_MP_SELLER_DETAILS_ACCESS'));
        if ($sellerDetailsAccess) {
            $objMarketplace = new Marketplace();
            if ($objMarketplace->sellerDetailsView) {
                foreach ($objMarketplace->sellerDetailsView as $sellerDetailsVal) {
                    if ($sellerDetailsAccess && in_array($sellerDetailsVal['id_group'], $sellerDetailsAccess)) {
                        $selectedDetailsByAdmin[] = [
                            'id_group' => $sellerDetailsVal['id_group'],
                            'name' => $sellerDetailsVal['name'],
                        ];
                    }
                }
            }
        }

        if (isset($mpSellerInfo['category_permission']) && !empty($mpSellerInfo['category_permission'])) {
            $idCategories = $mpSellerInfo['category_permission'];
        } else {
            $rootIdCategory = Category::getRootCategory()->id;
            $categories = Category::getAllCategoriesName();
            foreach ($categories as $category) {
                if ($rootIdCategory != $category) {
                    $idCategories[] = $category['id_category'];
                }
            }
        }
        if (!is_array($idCategories)) {
            $idCategories = [];
        }

        $categoryTree = new HelperTreeCategories('plan-categories');
        $categoryTree->setAttribute('is_category_filter', (bool) '1')
            ->setInputName('wk_seller_id_categories')
            ->setRootCategory(Category::getRootCategory()->id)
            ->setSelectedCategories($idCategories)
            ->setUseCheckBox(true);

        // get all admin payment option
        if ($adminPaymentOption = WkMpSellerPaymentMode::getPaymentMode()) {
            $this->context->smarty->assign('mp_payment_option', $adminPaymentOption);
        }
        WkMpHelper::defineGlobalJSVariables(); // Define global js variable on js file
        // tinymce setup
        $this->context->smarty->assign(
            [
                'path_css' => _THEME_CSS_DIR_,
                'ad' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_),
                'autoload_rte' => true,
                'lang' => true,
                'active_tab' => Tools::getValue('tab'),
                'selectedDetailsByAdmin' => $selectedDetailsByAdmin,
                'iso' => $this->context->language->iso_code,
                'context_language' => $this->context->language->id,
                'languages' => Language::getLanguages(),
                'total_languages' => count(Language::getLanguages()),
                'current_lang' => Language::getLanguage((int) $currentLang),
                'max_phone_digit' => Configuration::get('WK_MP_PHONE_DIGIT'),
                'multi_lang' => Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE'),
                'wk_country' => Country::getCountries($this->context->language->id, true),
                'modules_dir' => _MODULE_DIR_,
                'img_ps_dir' => _MODULE_DIR_ . $this->module->name . '/views/img/',
                'product_img_path' => _MODULE_DIR_ . $this->module->name . '/views/img/uploadimage/',
                'wkself' => dirname(__FILE__),
                'img_module_dir' => _MODULE_DIR_ . $this->module->name . '/views/img/',
                'ps_img_tmp_dir' => _PS_IMG_DIR_,
                'ps_img_dir' => _PS_IMG_ . 'l/',
                'category_tree' => $categoryTree->render(),
            ]
        );

        $this->fields_form = [
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'button',
            ]];

        if ((Shop::getContext() !== Shop::CONTEXT_SHOP) && (Shop::getContext() !== Shop::CONTEXT_ALL)) {
            // For shop group
            $this->errors[] = $this->l('You can not add or edit a seller in this shop context: select a shop instead of a group of shops.');
        } else {
            return parent::renderForm();
        }
    }

    public function processSave()
    {
        $mpIdSeller = Tools::getValue('mp_seller_id');
        $shopNameUnique = trim(Tools::getValue('shop_name_unique'));
        $sellerFirstName = trim(Tools::getValue('seller_firstname'));
        $sellerLastName = trim(Tools::getValue('seller_lastname'));
        $businessEmail = Tools::getValue('business_email');
        $sellerPhone = Tools::getValue('wk_phone');
        $fax = Tools::getValue('fax');
        $postcode = Tools::getValue('postcode');

        $facebookId = trim(Tools::getValue('facebook_id'));
        $twitterId = trim(Tools::getValue('twitter_id'));
        $youtubeId = trim(Tools::getValue('youtube_id'));
        $instagramId = trim(Tools::getValue('instagram_id'));

        $paymentMode = Tools::getValue('payment_mode_id');
        $paymentDetail = Tools::getValue('payment_detail');

        if (!$mpIdSeller) {
            // if add the seller
            $idCustomer = Tools::getValue('shop_customer');
            if (!$idCustomer) {
                $this->errors[] = $this->l('Customer is required field');
            }
        }

        // If multi-lang is OFF then PS default lang will be default lang for seller
        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
            $defaultLang = Tools::getValue('default_lang');
        } else {
            if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') { // For admin default language
                $defaultLang = Configuration::get('PS_LANG_DEFAULT');
            } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') { // for seller
                $defaultLang = Tools::getValue('current_lang_id');
            }
        }

        $objLang = new Language((int) $defaultLang);
        if (!$objLang->active) {
            $defaultLang = Configuration::get('PS_LANG_DEFAULT');
        }

        $shopName = trim(Tools::getValue('shop_name_' . $defaultLang));
        $sellerLangaugeData = Language::getLanguage((int) $defaultLang);

        if ($shopNameUnique == '') {
            $this->errors[] = $this->l('Unique name for shop is required field.');
        } elseif (!Validate::isCatalogName($shopNameUnique)) {
            $this->errors[] = $this->l('Invalid unique name for shop');
        } elseif (WkMpSeller::isShopNameExist($shopNameUnique, $mpIdSeller)) {
            $this->errors[] = $this->l('Unique name for shop is already taken. Try another.');
        }

        if ($shopName == '') {
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $this->errors[] = $this->l('Shop name is required in ') . $sellerLangaugeData['name'];
            } else {
                $this->errors[] = $this->l('Shop name is required');
            }
        }

        // Validate data
        $languages = Language::getLanguages();
        foreach ($languages as $language) {
            $languageName = '';
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $languageName = '(' . $language['name'] . ')';
            }
            if (Tools::getValue('shop_name_' . $language['id_lang'])) {
                if (!Validate::isCatalogName(Tools::getValue('shop_name_' . $language['id_lang']))) {
                    $this->errors[] = sprintf($this->l('Shop name field %s is invalid.'), $languageName);
                }
            }
            if (Tools::getValue('about_shop_' . $language['id_lang'])) {
                if (!Validate::isCleanHtml(Tools::getValue('about_shop_' . $language['id_lang']), (int) Configuration::get('PS_ALLOW_HTML_IFRAME'))) {
                    $this->errors[] = sprintf($this->l('Shop description field %s is invalid.'), $languageName);
                }
            }
        }

        if (!$sellerFirstName) {
            $this->errors[] = $this->l('Seller first name is required field.');
        } elseif (!Validate::isName($sellerFirstName)) {
            $this->errors[] = $this->l('Invalid seller first name.');
        }

        if (!$sellerLastName) {
            $this->errors[] = $this->l('Seller last name is required field.');
        } elseif (!Validate::isName($sellerLastName)) {
            $this->errors[] = $this->l('Invalid seller last name.');
        }

        if (!Validate::isEmail($businessEmail)) {
            $this->errors[] = $this->l('Invalid email ID.');
        } elseif (WkMpSeller::isSellerEmailExist($businessEmail, $mpIdSeller)) {
            $this->errors[] = $this->l('Email ID already exist.');
        }

        if ($sellerPhone == '') {
            $this->errors[] = $this->l('Phone is requried field and must be numeric.');
        } elseif (!Validate::isPhoneNumber($sellerPhone)) {
            $this->errors[] = $this->l('Phone number must be numeric.');
        }

        if ($fax && !Validate::isPhoneNumber($fax)) {
            $this->errors[] = $this->l('Fax must be numeric.');
        }

        $TINnumber = Tools::getValue('tax_identification_number');
        if ($TINnumber && !Validate::isGenericName($TINnumber)) {
            $this->errors[] = $this->l('Tax Identification Number must be valid.');
        }

        $address = Tools::getValue('address');
        if ($address && !Validate::isAddress($address)) {
            $this->errors[] = $this->l('Address format is invalid.');
        }

        if ($postcode = Tools::getValue('postcode')) {
            if (Tools::getValue('id_country')) {
                $country = new Country(Tools::getValue('id_country'));
                if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                    $this->errors[] = sprintf($this->l('The zip/postal code you\'ve entered is invalid. It must follow this format: %s'), str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))));
                }
            } elseif (!Validate::isPostCode($postcode)) {
                $this->errors[] = $this->l('Invalid zip/postal code');
            }
        }

        $sellerCity = Tools::getValue('city');
        if ($sellerCity != '') {
            if (!Validate::isCityName($sellerCity)) {
                $this->errors[] = $this->l('Invalid city name.');
            }
        }

        // if state available in selected country
        if (Tools::getValue('state_available')) {
            if (!Tools::getValue('id_state')) {
                $this->errors[] = $this->l('State is required field.');
            }
        }

        if ($facebookId && (!Validate::isGenericName($facebookId) || (strpos($facebookId, "'") !== false))) {
            $this->errors[] = $this->l('Facebook ID is invalid.');
        }
        if ($twitterId && (!Validate::isGenericName($twitterId) || (strpos($twitterId, "'") !== false))) {
            $this->errors[] = $this->l('Twitter ID is invalid.');
        }
        if ($youtubeId && (!Validate::isGenericName($youtubeId) || (strpos($youtubeId, "'") !== false))) {
            $this->errors[] = $this->l('Youtube ID is invalid.');
        }
        if ($instagramId && (!Validate::isGenericName($instagramId) || (strpos($instagramId, "'") !== false))) {
            $this->errors[] = $this->l('Instagram ID is invalid.');
        }

        if ($paymentDetail = Tools::getValue('payment_detail')) {
            if (!$paymentMode) {
                $this->errors[] = $this->l('Payment mode is required in case of filling account details.');
            } elseif (!Validate::isGenericName($paymentDetail)) {
                $this->errors[] = $this->l('Invalid account details');
            }
        }

        if (Configuration::get('WK_MP_PRODUCT_CATEGORY_RESTRICTION')) {
            $selectedCategories = Tools::getValue('wk_seller_id_categories');

            if ($mpIdSeller) {
                if ($mappedProducts = WkMpSellerProduct::checkCategoryMappedWithProduct(
                    $selectedCategories,
                    $mpIdSeller
                )) {
                    $this->context->smarty->assign([
                        'mappedProducts' => $mappedProducts,
                    ]);

                    $productName = $this->context->smarty->fetch(
                        _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/seller_info_detail/_partials/product_restriction_error.tpl'
                    );
                    $restrictionError = $this->l('Restriction category is already mapped with seller product.') . ' ';
                    $restrictionError .= $this->l('Please remove these product from category.');
                    $this->errors[] = $restrictionError . $productName;
                }
            }
        }

        if ($mpIdSeller) { // if edit
            Hook::exec('actionBeforeUpdateSeller', ['id_seller' => $mpIdSeller]);
        } else { // if add
            Hook::exec('actionBeforeAddSeller', ['id_customer' => Tools::getValue('shop_customer')]);
        }

        if (empty($this->errors)) {
            if ($mpIdSeller) { // if edit
                $objSellerInfo = new WkMpSeller($mpIdSeller);

                $sellerDetailsAccess = '';
                if (Tools::getValue('groupBox')) {
                    $sellerDetailsAccess = json_encode(Tools::getValue('groupBox'));
                }

                $objSellerInfo->seller_details_access = $sellerDetailsAccess;
            } else { // if add
                $objSellerInfo = new WkMpSeller();

                $sellerDetailsAccess = '';
                if (Tools::getValue('groupBox')) {
                    $sellerDetailsAccess = json_encode(Tools::getValue('groupBox'));
                }

                $objSellerInfo->seller_details_access = $sellerDetailsAccess;
            }

            if (Configuration::get('WK_MP_PRODUCT_CATEGORY_RESTRICTION')) {
                $objSellerInfo->category_permission = json_encode($selectedCategories);
            }
            $objSellerInfo->shop_name_unique = $shopNameUnique; // Removed pSQL to store valid data
            $objSellerInfo->link_rewrite = pSQL(Tools::link_rewrite($shopNameUnique));
            $objSellerInfo->seller_firstname = $sellerFirstName; // Removed pSQL to store valid data
            $objSellerInfo->seller_lastname = $sellerLastName; // Removed pSQL to store valid data
            $objSellerInfo->business_email = pSQL($businessEmail);
            $objSellerInfo->phone = pSQL($sellerPhone);
            $objSellerInfo->fax = pSQL(Tools::getValue('fax'));
            $objSellerInfo->tax_identification_number = pSQL(trim(Tools::getValue('tax_identification_number')));
            $objSellerInfo->postcode = pSQL($postcode);
            $objSellerInfo->city = trim(Tools::getValue('city'));  // Removed pSQL to store valid data
            $objSellerInfo->id_country = (int) Tools::getValue('id_country');
            $objSellerInfo->id_state = (int) Tools::getValue('id_state');
            $objSellerInfo->default_lang = (int) Tools::getValue('default_lang');
            $objSellerInfo->facebook_id = pSQL($facebookId);
            $objSellerInfo->twitter_id = pSQL($twitterId);
            $objSellerInfo->youtube_id = pSQL($youtubeId);
            $objSellerInfo->instagram_id = pSQL($instagramId);

            if (!$mpIdSeller) {
                // only for add seller page
                $sellerActive = Tools::getValue('seller_active');
                $idCustomer = Tools::getValue('shop_customer');
                $customer = new Customer($idCustomer);

                $objSellerInfo->active = (int) $sellerActive;
                $objSellerInfo->shop_approved = (int) $sellerActive;
                $objSellerInfo->seller_customer_id = (int) $idCustomer;
                $objSellerInfo->id_shop = (int) $customer->id_shop;
                $objSellerInfo->id_shop_group = (int) $customer->id_shop_group;
            }

            foreach (Language::getLanguages(false) as $language) {
                $shopLangId = $language['id_lang'];
                $aboutShopLangId = $language['id_lang'];

                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    // if shop name in other language is not available then fill with seller language same for others
                    if (!Tools::getValue('shop_name_' . $language['id_lang'])) {
                        $shopLangId = $defaultLang;
                    }
                    if (!Tools::getValue('about_shop_' . $language['id_lang'])) {
                        $aboutShopLangId = $defaultLang;
                    }
                } else {
                    // if multilang is OFF then all fields will be filled as default lang content
                    $shopLangId = $defaultLang;
                    $aboutShopLangId = $defaultLang;
                }

                $objSellerInfo->shop_name[$language['id_lang']] = Tools::getValue('shop_name_' . $shopLangId);

                $objSellerInfo->about_shop[$language['id_lang']] = Tools::getValue('about_shop_' . $aboutShopLangId);
            }
            $objSellerInfo->address = Tools::getValue('address'); // pSQL removed to resolve line break issue
            $objSellerInfo->save();
            $sellerCustomerId = $objSellerInfo->seller_customer_id;
            if ($mpIdSeller) {
                // if edit seller - update seller details in seller order table
                WkMpSellerOrder::updateSellerDetailsInOrder(
                    $sellerCustomerId,
                    $shopNameUnique,
                    $sellerFirstName,
                    $sellerLastName,
                    $businessEmail
                );

                $mpPayment = new WkMpCustomerPayment();
                if ($sellerPayments = $mpPayment->getPaymentDetailByIdCustomer($sellerCustomerId)) {
                    $mpPayment = new WkMpCustomerPayment($sellerPayments['id_customer_payment']);
                }

                if ($paymentMode) {
                    $mpPayment->seller_customer_id = (int) $sellerCustomerId;
                    $mpPayment->payment_mode_id = (int) $paymentMode;
                    $mpPayment->payment_detail = $paymentDetail;
                    $mpPayment->save();
                } else {
                    $mpPayment->delete();
                }

                Hook::exec('actionAfterUpdateSeller', ['id_seller' => $mpIdSeller]);
            } else {
                // if add seller
                $idSeller = $objSellerInfo->id;
                if ($idSeller) {
                    if ($sellerActive) {
                        WkMpSeller::sendMail($idSeller, 3, 1); // mail to seller of account activation
                    }

                    // If mpsellerstaff module is installed but currently disabled and current customer was a staff then delete this customer as staff from mpsellerstaff module table. Because a customer can not be a seller and a staff both in same time.
                    if (Module::isInstalled('mpsellerstaff') && !Module::isEnabled('mpsellerstaff')) {
                        WkMpSeller::deleteStaffDataIfBecomeSeller($sellerCustomerId);
                    }
                }

                if ($paymentMode) {
                    $mpPayment = new WkMpCustomerPayment();
                    $mpPayment->seller_customer_id = (int) $sellerCustomerId;
                    $mpPayment->payment_mode_id = (int) $paymentMode;
                    $mpPayment->payment_detail = $paymentDetail;
                    $mpPayment->save();
                }

                Hook::exec('actionAfterAddSeller', ['id_seller' => $idSeller]);
            }

            if (empty($this->errors)) {
                if (Tools::isSubmit('submitAdd' . $this->table . 'AndStay')) {
                    if ($mpIdSeller) {
                        Tools::redirectAdmin(self::$currentIndex . '&id_seller=' . (int) $mpIdSeller . '&update' . $this->table . '&conf=4&tab=' . Tools::getValue('active_tab') . '&token=' . $this->token);
                    } else {
                        Tools::redirectAdmin(self::$currentIndex . '&id_seller=' . (int) $idSeller . '&update' . $this->table . '&conf=3&tab=' . Tools::getValue('active_tab') . '&token=' . $this->token);
                    }
                } else {
                    if ($mpIdSeller) {
                        Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
                    } else {
                        Tools::redirectAdmin(self::$currentIndex . '&conf=3&token=' . $this->token);
                    }
                }
            }
        } else {
            if ($mpIdSeller) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
    }

    public function makeSellerPartner($idSeller = false, $reasonText = false)
    {
        if (!$idSeller) {
            $idSeller = Tools::getValue('id_seller');
        }

        $objSellerInfo = new WkMpSeller($idSeller);
        if ($objSellerInfo) {
            $is_seller = $objSellerInfo->shop_approved;
            if ($objSellerInfo->active == 0) {
                if (!$objSellerInfo->shop_approved) {
                    // First time new seller going to active
                    $objSellerInfo->shop_approved = 1;
                }
                // seller is deactive, make it active
                $objSellerInfo->active = 1;
                Hook::exec('actionMPSellerActive', ['id_seller' => $idSeller]);
                // activate or deactive seller all products according to last status
                WkMpSeller::changeSellerProductStatus($idSeller, false, 1);
                WkMpSeller::sendMail($idSeller, 1, 1); // activation mail to seller
            } else {
                // seller is active, make it deactive
                $objSellerInfo->active = 0;
                // deactive seller all products
                WkMpSeller::changeSellerProductStatus($idSeller, 0);
                WkMpSeller::sendMail($idSeller, 2, 2, $reasonText); // deactivation mail to seller
            }
            $objSellerInfo->save();
            Hook::exec('actionToogleSellerStatus', ['id_seller' => $idSeller, 'is_seller' => $is_seller, 'status' => $objSellerInfo->active]);
        }
    }

    protected function processBulkEnableSelection()
    {
        return $this->processBulkStatusSelection(1);
    }

    protected function processBulkDisableSelection()
    {
        return $this->processBulkStatusSelection(0);
    }

    protected function processBulkStatusSelection($status)
    {
        if ($status == 1) {
            if (is_array($this->boxes) && !empty($this->boxes)) {
                foreach ($this->boxes as $id) {
                    $mpSeller = WkMpSeller::getSeller($id);
                    if ($mpSeller) {
                        if ($mpSeller['active'] == 0) {
                            $this->makeSellerPartner($id);
                        }
                    }
                }
            }
        } elseif ($status == 0) {
            if (is_array($this->boxes) && !empty($this->boxes)) {
                foreach ($this->boxes as $id) {
                    $mpSeller = WkMpSeller::getSeller($id);
                    if ($mpSeller) {
                        if ($mpSeller['active'] == 1) {
                            $this->makeSellerPartner($id);
                        }
                    }
                }
            }
        }

        if (is_array($this->boxes) && !empty($this->boxes)) {
            Tools::redirectAdmin(self::$currentIndex . '&conf=5&token=' . $this->token);
        } else {
            $this->errors[] = $this->l('You must select at least one item to perform a bulk action.');
        }
    }

    public function checkCustomerId($id)
    {
        $customer = new Customer($id);
        if (!empty($customer->id)) {
            return $customer->id;
        } else {
            return '--';
        }
    }

    public function ajaxProcessDeleteSellerImage()
    {
        // delete seller images
        WkMpSeller::deleteSellerImages();
    }

    public function ajaxProcessCheckUniqueShopName()
    {
        // check unique shop name and compare to other existing shop name unique
        WkMpSeller::validateSellerUniqueShopName();
    }

    public function ajaxProcessCheckUniqueSellerEmail()
    {
        // check seller email and compare to other existing seller email
        WkMpSeller::validateSellerEmail();
    }

    public function ajaxProcessCheckZipCodeByCountry()
    {
        // Display zip code field on the basis of country
        $countryNeedZipCode = true;
        if (Tools::getValue('id_country')) {
            $country = new Country(Tools::getValue('id_country'));
            $countryNeedZipCode = $country->need_zip_code;
        }

        if ($countryNeedZipCode) {
            exit('1');
        } else {
            exit('0');
        }
    }

    public function ajaxProcessGetSellerState()
    {
        // Get state by choosing country
        WkMpSeller::displayStateByCountryId();
    }

    public function ajaxProcessUploadimage()
    {
        if (Tools::getValue('action') == 'uploadimage') {
            if (Tools::getValue('actionIdForUpload')) {
                $actionIdForUpload = Tools::getValue('actionIdForUpload'); // it will be Product Id OR Seller Id
                $adminupload = Tools::getValue('adminupload'); // if uploaded by Admin from backend
                $finalData = WkMpSellerProductImage::uploadImage($_FILES, $actionIdForUpload, $adminupload);
                echo json_encode($finalData);
            }
        }

        exit; // ajax close
    }

    public function ajaxProcessValidateMpSellerForm()
    {
        $params = [];
        parse_str(Tools::getValue('formData'), $params);
        if (!empty($params)) {
            WkMpSeller::validationSellerFormField($params);
        } else {
            exit('1');
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        if ($this->display == 'edit') {
            // Upload images
            $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/uploadimage-css/jquery.filer.css');
            $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/uploadimage-css/jquery.filer-dragdropbox-theme.css');
            $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/uploadimage-css/uploadphoto.css');
            $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/uploadimage-js/jquery.filer.js');
            $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/uploadimage-js/uploadimage.js');
            $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/imageedit.js');
        }
    }
}
