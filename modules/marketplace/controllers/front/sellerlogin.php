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

class MarketplaceSellerLoginModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $this->display_header = false;
        $this->display_footer = false;

        $idCustomer = $this->context->customer->id;
        if ($idCustomer) {
            $customer = new Customer($idCustomer);

            // Logout from customer account
            if (Tools::getValue('wk_logout')) {
                $customer->logout();
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerlogin'));
            }

            $objMpSeller = new WkMpSeller();
            $sellerDetail = $objMpSeller->getSellerDetailByCustomerId($idCustomer);
            if ($sellerDetail) {
                // If seller registered and active
                if ($sellerDetail['active']) {
                    Tools::redirect($this->context->link->getModuleLink('marketplace', 'dashboard'));
                } else {
                    // If seller registered but not active
                    $this->context->smarty->assign([
                        'wk_seller_request_pending' => 1,
                        'shop_approved' => $sellerDetail['shop_approved'],
                        'mpSellerShopSettings' => Configuration::get('WK_MP_SELLER_SHOP_SETTINGS'),
                    ]);
                }
            } else {
                // If customer logged in but currently not a seller
                $this->context->smarty->assign([
                    'wk_customer_logged' => 1,
                    'wk_customer_id' => $idCustomer,
                    'wk_email' => $customer->email,
                ]);
            }
        }

        $smartyArr = [];
        if ($wkError = Tools::getValue('error')) {
            $smartyArr['error'] = $error;
        }

        if ($idTheme = Configuration::get('WK_MP_SELLER_LOGIN_THEME')) {
            $objLoginConf = new WkMpLoginConfigration();
            $themeConf = $objLoginConf->getShopThemeConfigration(
                $this->context->shop->id,
                $idTheme,
                $this->context->language->id
            );
            if ($themeConf) {
                if ($themeConf['id_theme'] == '1') {
                    $themeConf['account_btn_bg_color'] = $themeConf['header_bg_color'];
                } else {
                    $themeConf['account_btn_bg_color'] = '#4cbb6c';
                }
                $smartyArr['themeConf'] = $themeConf;
            }

            $wkLogoDir = _PS_MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/';
            $wkLogoDirShop = _PS_MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/' . $this->context->shop->id . '/';
            $imgSrc = glob($wkLogoDir . 'logo.*');
            $imgSrcShop = glob($wkLogoDirShop . 'logo.*');
            if ($imgSrcShop && file_exists($imgSrcShop[0])) {
                $ext = pathinfo($imgSrcShop[0], PATHINFO_EXTENSION);

                $wkLogoUrl = _MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/' . $this->context->shop->id . '/logo.' . $ext;
                $smartyArr['wk_logo_url'] = $wkLogoUrl;
            } elseif ($imgSrc && file_exists($imgSrc[0])) {
                $ext = pathinfo($imgSrc[0], PATHINFO_EXTENSION);

                $wkLogoUrl = _MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/logo.' . $ext;
                $smartyArr['wk_logo_url'] = $wkLogoUrl;
            }

            $objParentBlock = new WkMpLoginParentBlock();
            $parentBlock = $objParentBlock->getActiveParentBlock($idTheme);
            if ($parentBlock) {
                $contentData = ['reg_title', 'termscondition', 'feature'];
                $objBlockPosition = new WkMpLoginBlockPosition();
                $objBlockContent = new WkMpLoginContent();
                foreach ($parentBlock as $key => $value) {
                    $parentBlock[$key]['sub_block'] = $objBlockPosition->getPositionDetailByIdParent(
                        $value['id_wk_mp_seller_login_parent_block'],
                        $idTheme
                    );
                    if ($parentBlock[$key]['sub_block']) {
                        foreach ($parentBlock[$key]['sub_block'] as $subKey => $subValue) {
                            if (in_array($subValue['block_name'], $contentData)) {
                                $parentBlock[$key]['sub_block'][$subKey]['data'] = $objBlockContent->getBlockContent(
                                    $subValue['id_wk_mp_seller_login_block_position'],
                                    $idTheme,
                                    $this->context->language->id
                                );
                            }
                        }
                    }
                }

                $smartyArr['parentBlock'] = $parentBlock;
            }

            $bannerImgShop = _PS_MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/banner_img/' . $this->context->shop->id . '/' . $idTheme . '.jpg';

            if (file_exists($bannerImgShop)) {
                $smartyArr['bannerImg'] = _MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/banner_img/'
                . $this->context->shop->id . '/' . $idTheme . '.jpg';
            } else {
                $smartyArr['bannerImg'] = _MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/banner_img/' . $idTheme . '.jpg';
            }
        }

        $modImgDir = _MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/';
        $smartyArr['modImgDir'] = $modImgDir;

        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
            $smartyArr['allow_multilang'] = 1;
            $currentLangID = $this->context->language->id;
        } else {
            $smartyArr['allow_multilang'] = 0;
            if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') {
                // Admin default lang
                $currentLangID = Configuration::get('PS_LANG_DEFAULT');
            } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') {
                // Seller default lang
                $currentLangID = $this->context->language->id;
            }
        }
        $currentLang = Language::getLanguage((int) $currentLangID);

        $smartyArr['MP_SELLER_COUNTRY_NEED'] = Configuration::get('WK_MP_SELLER_COUNTRY_NEED');
        $smartyArr['max_phone_digit'] = Configuration::get('WK_MP_PHONE_DIGIT');
        $smartyArr['languages'] = Language::getLanguages();
        $smartyArr['total_languages'] = count(Language::getLanguages());
        $smartyArr['current_lang'] = $currentLang;
        $smartyArr['multi_lang'] = Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE');
        $smartyArr['active_languages'] = Language::getLanguages(true, $this->context->shop->id);
        $smartyArr['wk_countries'] = Country::getCountries($this->context->language->id, true);
        $smartyArr['id_module'] = $this->module->id;
        $smartyArr['terms_and_condition_active'] = Configuration::get('WK_MP_TERMS_AND_CONDITIONS_STATUS');
        $smartyArr['genders'] = Gender::getGenders();
        if (Configuration::get('WK_MP_TERMS_AND_CONDITIONS_STATUS')) {
            // Display CMS page link
            $termCondCMS = Configuration::get('WK_MP_TERMS_AND_CONDITIONS_CMS');
            if ($termCondCMS) {
                $objCMS = new CMS($termCondCMS, $this->context->language->id);

                $linkCmsPageContent = $this->context->link->getCMSLink(
                    $objCMS,
                    $objCMS->link_rewrite,
                    Configuration::get('PS_SSL_ENABLED')
                );
                if (!strpos($linkCmsPageContent, '?')) {
                    $linkCmsPageContent .= '?content_only=1';
                } else {
                    $linkCmsPageContent .= '&content_only=1';
                }
                $smartyArr['linkCmsPageContent'] = $linkCmsPageContent;
            }
        }
        $smartyArr['seller_login_page'] = 1;
        $this->context->smarty->assign($smartyArr);

        Media::addJsDef([
            'modImgDir' => $modImgDir,
            'lang' => $currentLang['id_lang'],
        ]);

        $this->defineJSVars();
        $this->setTemplate('module:' . $this->module->name . '/views/templates/front/seller/sellerlogin.tpl');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('registrationform') || Tools::isSubmit('loginform')) {
            if (Tools::isSubmit('registrationform')) {
                $this->registrationProcess();
            } elseif (Tools::isSubmit('loginform')) {
                $this->loginProcess();
            }
        }

        parent::postProcess();
    }

    public function loginProcess()
    {
        Hook::exec('actionBeforeAuthentication');

        $passwd = trim(Tools::getValue('passwd'));
        $email = trim(Tools::getValue('email'));
        $customer = new Customer();

        if (empty($email)) {
            $this->context->smarty->assign('error', 1);
        } elseif (!Validate::isEmail($email)) {
            $this->context->smarty->assign('error', 2);
        } elseif (empty($passwd)) {
            $this->context->smarty->assign('error', 3);
        } elseif (version_compare(_PS_VERSION_, '8', '<') && !Validate::isPasswd($passwd)) {
            $this->context->smarty->assign('error', 4);
        } else {
            $authentication = $customer->getByEmail(trim($email), trim($passwd));
            if (!$authentication) {
                $this->context->smarty->assign('error', 5);
            } else {
                if (isset($authentication->active) && !$authentication->active) {
                    $this->context->smarty->assign('error', 6);
                } elseif (!$authentication || !$customer->id) {
                    $this->context->smarty->assign('error', 7);
                } else {
                    $objSellerDetail = new WkMpSeller();
                    $sellerDetail = $objSellerDetail->getSellerDetailByCustomerId($customer->id);
                    if (!$sellerDetail) {
                        $this->context->smarty->assign('error', 22);
                    } else {
                        $this->context->cookie->id_customer = (int) $customer->id;
                        $this->context->cookie->customer_lastname = $customer->lastname;
                        $this->context->cookie->customer_firstname = $customer->firstname;
                        $this->context->cookie->logged = 1;
                        $customer->logged = 1;
                        $this->context->cookie->is_guest = $customer->isGuest();
                        $this->context->cookie->passwd = $customer->passwd;
                        $this->context->cookie->email = $customer->email;

                        // Add customer to the context
                        $this->context->customer = $customer;

                        if (Configuration::get('PS_CART_FOLLOWING')
                        && (empty($this->context->cookie->id_cart)
                        || Cart::getNbProducts($this->context->cookie->id_cart) == 0)
                        && $idCart = (int) Cart::lastNoneOrderedCart($this->context->customer->id)) {
                            $this->context->cart = new Cart($idCart);
                        } else {
                            $idCarrier = (int) $this->context->cart->id_carrier;
                            $this->context->cart->id_carrier = 0;
                            $this->context->cart->setDeliveryOption(null);
                            $this->context->cart->id_address_delivery = (int) Address::getFirstCustomerAddressId(
                                (int) $customer->id
                            );
                            $this->context->cart->id_address_invoice = (int) Address::getFirstCustomerAddressId(
                                (int) $customer->id
                            );
                        }

                        $this->context->cart->id_customer = (int) $customer->id;
                        $this->context->cart->secure_key = $customer->secure_key;

                        if ($this->ajax
                        && isset($idCarrier)
                        && $idCarrier
                        && Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                            $deliveryOption = [$this->context->cart->id_address_delivery => $idCarrier . ','];
                            $this->context->cart->setDeliveryOption($deliveryOption);
                        }

                        $this->context->cart->save();
                        $this->context->cookie->id_cart = (int) $this->context->cart->id;
                        $this->context->cookie->write();
                        $this->context->cart->autosetProductAddress();
                        if (version_compare(_PS_VERSION_, '1.7.6.5', '>')) {
                            $this->context->updateCustomer($this->context->customer);
                        }
                        Hook::exec('actionAuthentication', ['customer' => $this->context->customer]);

                        // Login information have changed, so we check if the cart rules still apply
                        CartRule::autoRemoveFromCart($this->context);
                        CartRule::autoAddToCart($this->context);

                        Tools::redirect($this->context->link->getModuleLink('marketplace', 'dashboard'));
                    }
                }
            }
        }
    }

    public function registrationProcess()
    {
        $sellerFirstname = '';
        $sellerLastname = '';
        $sellerEmail = '';

        $customerId = Tools::getValue('ps_customer_id');
        if (!$customerId) {
            // First time customer creation
            Hook::exec('actionBeforeSubmitAccount');

            $sellerFirstname = Tools::getValue('firstname');
            $sellerLastname = Tools::getValue('lastname');
            $sellerEmail = Tools::getValue('email');
            $customerPass = Tools::getValue('passwd');

            if (empty($sellerFirstname)) {
                $this->context->smarty->assign('error', 8);

                return;
            } elseif (empty($sellerLastname)) {
                $this->context->smarty->assign('error', 9);

                return;
            } elseif (empty($sellerEmail) || !Validate::isEmail($sellerEmail)) {
                $this->context->smarty->assign('error', 2);

                return;
            } elseif (Customer::customerExists($sellerEmail)) {
                $this->context->smarty->assign('error', 11);

                return;
            } elseif (empty($customerPass)) {
                $this->context->smarty->assign('error', 3);

                return;
            }
        } else {
            if ($this->context->customer->id) {
                // If customer is logged in then he/she will submit only shop data
                $objCustomer = new Customer((int) $this->context->customer->id);
                $sellerFirstname = $objCustomer->firstname;
                $sellerLastname = $objCustomer->lastname;
                $sellerEmail = $objCustomer->email;
            } else {
                // If customer is not logged in but already register and then entering email and password in form
                $customerEmail = Tools::getValue('email');
                $customerPass = Tools::getValue('passwd');
                $objCustomer = new Customer();
                $customerData = $objCustomer->getByEmail($customerEmail, $customerPass);
                if ($customerData) {
                    // If email and password is correct
                    $sellerFirstname = $customerData->firstname;
                    $sellerLastname = $customerData->lastname;
                    $sellerEmail = $customerData->email;
                } else {
                    if (empty($customerPass)) {
                        $this->context->smarty->assign('error', 3); // Password is required

                        return;
                    } else {
                        $this->context->smarty->assign('error', 5); // Invalid email or password

                        return;
                    }
                }
            }
        }

        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
            $defaultLang = Tools::getValue('seller_default_lang');
        } else {
            if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') {
                $defaultLang = Configuration::get('PS_LANG_DEFAULT');
            } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') {
                $defaultLang = Tools::getValue('current_lang');
            }
        }

        $shopNameUnique = trim(Tools::getValue('mp_shop_name_unique'));
        $shopName = trim(Tools::getValue('mp_shop_name_' . $defaultLang));
        $phone = trim(Tools::getValue('mp_seller_phone'));

        if ($businessEmail = trim(Tools::getValue('mp_seller_email'))) {
            $sellerEmail = $businessEmail;
        }

        $country = trim(Tools::getValue('seller_country'));
        $state = trim(Tools::getValue('seller_state'));
        $city = trim(Tools::getValue('seller_city'));
        $postcode = Tools::getValue('seller_postcode') ? trim(Tools::getValue('seller_postcode')) : '';

        $languages = Language::getLanguages();
        $shopNameError = 0;
        foreach ($languages as $language) {
            if (!Validate::isCatalogName(Tools::getValue('shop_name_' . $language['id_lang']))) {
                $shopNameError = 1;
            }
        }

        if ($shopName == '') {
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $this->context->smarty->assign('error', 21);

                return;
            } else {
                $this->context->smarty->assign('error', 13);

                return;
            }
        } elseif ($shopNameError == 1) {
            $this->context->smarty->assign('error', 14);

            return;
        }

        if ($shopNameUnique == '') {
            $this->context->smarty->assign('error', 19);

            return;
        } elseif (!Validate::isCatalogName($shopNameUnique)) {
            $this->context->smarty->assign('error', 20);

            return;
        } elseif (WkMpSeller::isShopNameExist($shopNameUnique)) {
            $this->context->smarty->assign('error', 15);

            return;
        } elseif ($phone == '') {
            $this->context->smarty->assign('error', 16);

            return;
        } elseif (!Validate::isPhoneNumber($phone)) {
            $this->context->smarty->assign('error', 17);

            return;
        } elseif (empty($sellerEmail) || !Validate::isEmail($sellerEmail)) {
            $this->context->smarty->assign('error', 2);

            return;
        } elseif (WkMpSeller::isSellerEmailExist($sellerEmail)) {
            $this->context->smarty->assign('error', 18);

            return;
        } elseif (Configuration::get('WK_MP_SELLER_COUNTRY_NEED')) {
            if ($city == '') {
                $this->context->smarty->assign('error', 23);

                return;
            } elseif (!Validate::isName($city)) {
                $this->context->smarty->assign('error', 24);

                return;
            }
            if (!$country) {
                $this->context->smarty->assign('error', 25);

                return;
            }
            if (Tools::getValue('state_avl')) { // if state available in selected country
                if (!$state) {
                    $this->context->smarty->assign('error', 26);

                    return;
                }
            }

            $countryNeedZipCode = true;
            $countryZipCodeFormat = false;
            if ($country) {
                $objCountry = new Country($country);
                $countryNeedZipCode = $objCountry->need_zip_code;
                $countryZipCodeFormat = $objCountry->zip_code_format;
            }

            if (!$postcode && $countryNeedZipCode) {
                $this->context->smarty->assign('error', 27);

                return;
            } elseif ($countryZipCodeFormat) {
                if (!$objCountry->checkZipCode(trim($postcode))) {
                    $this->context->smarty->assign('error', 28);

                    return;
                }
            } elseif ($postcode && !Validate::isPostCode($postcode)) {
                $this->context->smarty->assign('error', 29);

                return;
            }
        }

        Hook::exec('actionBeforeAddSeller');
        if (!empty($this->context->controller->errors)) {
            $this->context->smarty->assign('hook_errors', $this->context->controller->errors);

            return;
        } elseif (!empty($this->errors)) {
            $this->context->smarty->assign('hook_errors', $this->errors);

            return;
        }

        if ($customerId) {
            // If customer is logged in OR entering correct email and password
            $customer = new Customer($customerId);
            $this->contextUpdate($customer);
            $this->context->cart->update();
        } else {
            // First time customer creation
            $customer = new Customer();
            $customer->validateController(); // Add password with Hashing
            $customer->email = Tools::getValue('email');
            $customer->id_gender = Tools::getValue('id_gender');
            $customer->lastname = Tools::getValue('lastname');
            $customer->firstname = Tools::ucwords(Tools::getValue('firstname'));
            $customer->is_guest = 0;
            $customer->active = 1;
            $customer->save();
            if ($customerId = $customer->id) {
                $this->sendConfirmationMail($customerId); // mail to customer when their account created successfully

                $this->contextUpdate($customer);
                $this->context->cart->update();

                Hook::exec('actionCustomerAccountAdd', [
                    '_POST' => $_POST,
                    'newCustomer' => $customer,
                ]);
            }
        }

        $objSellerDetail = new WkMpSeller();
        $objSellerDetail->shop_name_unique = $shopNameUnique;
        $objSellerDetail->business_email = $sellerEmail;
        $objSellerDetail->seller_firstname = $sellerFirstname;
        $objSellerDetail->seller_lastname = $sellerLastname;
        $objSellerDetail->link_rewrite = Tools::link_rewrite($shopNameUnique);
        $objSellerDetail->phone = $phone;
        if (Configuration::get('WK_MP_SELLER_COUNTRY_NEED')) {
            $objSellerDetail->city = $city;
            $objSellerDetail->id_country = $country;
            $objSellerDetail->id_state = $state;
            $objSellerDetail->postcode = $postcode;
        }
        $objSellerDetail->default_lang = Tools::getValue('seller_default_lang');

        $defaultLang = Tools::getValue('seller_default_lang');
        $shopName = trim(Tools::getValue('mp_shop_name_' . $defaultLang));

        if (Configuration::get('WK_MP_SELLER_ADMIN_APPROVE') == 0) {
            $active = 1;
        } else {
            $active = 0;
        }
        $objSellerDetail->active = $active;
        $objSellerDetail->shop_approved = $active;
        $objSellerDetail->seller_customer_id = $customerId;
        $module = Module::getInstanceByName('marketplace');
        if (!version_compare($module->version, '5.4.0', '<')) {
            $customer = new Customer((int) $customerId);
            $objSellerDetail->id_shop = $customer->id_shop;
            $objSellerDetail->id_shop_group = $customer->id_shop_group;
        }

        if (Configuration::get('WK_MP_SHOW_SELLER_DETAILS')) {
            // display all seller details for new seller
            $objSellerDetail->seller_details_access = Configuration::get('WK_MP_SELLER_DETAILS_ACCESS');
        }

        foreach (Language::getLanguages(true) as $language) {
            $shopLangId = $language['id_lang'];
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                // if shop name in other language is not available then fill with seller language same for others
                if (!Tools::getValue('mp_shop_name_' . $language['id_lang'])) {
                    $shopLangId = $defaultLang;
                }
            } else {
                // if multilang is OFF then all fields will be filled as default lang content
                $shopLangId = $defaultLang;
            }
            $objSellerDetail->shop_name[$language['id_lang']] = Tools::getValue('mp_shop_name_' . $shopLangId);
        }
        $objSellerDetail->save();
        if ($idSeller = $objSellerDetail->id) {
            // If seller default active approval is ON then mail to seller of account activation
            if ($idSeller && $objSellerDetail->active) {
                WkMpSeller::sendMail($idSeller, 1, 1);
            }

            if (Configuration::get('WK_MP_MAIL_ADMIN_SELLER_REQUEST')) {
                // Mail to Admin on seller request
                $sellerName = $sellerFirstname . ' ' . $sellerLastname;
                $objSellerDetail->mailToAdminWhenSellerRequest($sellerName, $shopName, $sellerEmail, $phone);
            }

            Hook::exec('actionAfterAddSeller', ['id_seller' => $idSeller]);

            Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerlogin'));
        }
    }

    public function sendConfirmationMail($idCustomer)
    {
        if (!Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
            return true;
        }

        $idLang = $this->context->language->id;
        $customer = new Customer($idCustomer);
        Mail::Send(
            (int) $idLang,
            'account',
            Mail::l('Welcome!', (int) $idLang),
            [
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
                '{passwd}' => Tools::getValue('passwd'),
            ],
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname,
            null,
            null,
            null,
            null,
            _PS_MAIL_DIR_,
            false,
            null,
            null
        );
    }

    public function contextUpdate(Customer $customer)
    {
        $this->context->customer = $customer;
        $this->context->smarty->assign('confirmation', 1);
        $this->context->cookie->id_customer = (int) $customer->id;
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->logged = 1;
        // if register process is in two steps, we display a message to confirm account creation
        if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE')) {
            $this->context->cookie->account_created = 1;
        }
        $customer->logged = 1;
        $this->context->cookie->email = $customer->email;
        $this->context->cookie->is_guest = !Tools::getValue('is_new_customer', 1);
        // Update cart address
        $this->context->cart->secure_key = $customer->secure_key;
        if (version_compare(_PS_VERSION_, '1.7.6.5', '>')) {
            $this->context->updateCustomer($this->context->customer);
        }
    }

    public function displayAjaxSellerLogin()
    {
        if (!$this->isTokenValid()) {
            exit('Something went wrong!');
        }
        $case = Tools::getValue('case');
        if ($case == 'checkEmailRegister') {
            $result = false;
            if ($email = Tools::getValue('user_email')) {
                $customerId = Customer::customerExists($email, true);
                if ($customerId) {
                    $objMpSeller = new WkMpSeller();
                    $sellerDetail = $objMpSeller->getSellerDetailByCustomerId((int) $customerId);
                    if ($sellerDetail) {
                        $idSeller = $sellerDetail['id_seller'];
                    } else {
                        $idSeller = 0;
                    }

                    $result = [
                        'idSeller' => $idSeller,
                        'idCustomer' => $customerId,
                    ];
                }
            }
            exit(json_encode($result));
        } elseif ($case == 'checkUniqueShopName') {
            WkMpSeller::validateSellerUniqueShopName();
        } elseif ($case == 'getSellerState') {
            WkMpSeller::displayStateByCountryId();
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        $idTheme = Configuration::get('WK_MP_SELLER_LOGIN_THEME');
        $this->registerStylesheet(
            'add_product',
            'modules/' . $this->module->name . '/views/css/mpsellerlogin/theme' . $idTheme . '.css'
        );
        $this->registerJavascript(
            'ps-validate-js',
            'js/validate.js'
        );
        $this->registerJavascript(
            'seller-wise-login-js',
            'modules/' . $this->module->name . '/views/js/mpsellerlogin/mpsellerwiselogin.js',
            [
                'media' => 'all',
                'priority' => 900,
                'position' => 'bottom',
            ]
        );
        $this->registerStylesheet(
            'marketplace_account',
            'modules/' . $this->module->name . '/views/css/marketplace_account.css'
        );
        $this->addjQueryPlugin('growl', null, false);
    }

    public function defineJSVars()
    {
        $jsVars = [
            'MP_SELLER_COUNTRY_NEED' => Configuration::get('WK_MP_SELLER_COUNTRY_NEED'),
            'emailIdError' => $this->module->l('Please change your email Id to continue from here.', 'sellerlogin'),
            'allFieldMandatoryError' => $this->module->l('All fields are mandatory.', 'sellerlogin'),
            'firstNameError' => $this->module->l('First name is not valid.', 'sellerlogin'),
            'lastNameError' => $this->module->l('Last name is not valid.', 'sellerlogin'),
            'invalidEmailIdError' => $this->module->l('Please enter valid email Id.', 'sellerlogin'),
            'passwordRequiredError' => $this->module->l('Password is required.', 'sellerlogin'),
            'passwordLengthError' => $this->module->l('Password length must be more than 4 digits.', 'sellerlogin'),
            'invalidPasswordError' => $this->module->l('Please enter valid password.', 'sellerlogin'),
            'invalidUniqueShopNameError' => $this->module->l('Invalid unique shop name.', 'sellerlogin'),
            'shopNameRequiredLang' => $this->module->l('Shop name is required in default language', 'sellerlogin'),
            'shopNameRequired' => $this->module->l('Shop name is required.', 'sellerlogin'),
            'invalidShopNameError' => $this->module->l('Invalid shop name', 'sellerlogin'),
            'phoneNumberError' => $this->module->l('Phone number is not valid.', 'sellerlogin'),
            'cityNameRequired' => $this->module->l('City is required.', 'sellerlogin'),
            'invalidCityNameError' => $this->module->l('City is not valid.', 'sellerlogin'),
            'termConditionError' => $this->module->l('Please agree the terms and condition.', 'sellerlogin'),
            'emailAlreadyExist' => $this->module->l('This email is already registered as a seller, Please login.', 'sellerlogin'),
            'shopNameAlreadyExist' => $this->module->l('Unique shop name already taken. Try another.', 'sellerlogin'),
            'shopNameError' => $this->module->l('Shop name can not contain any special character except underscrore. Try another.', 'sellerlogin'),
            'wk_static_token' => Tools::getToken(false),
            'checkCustomerAjaxUrl' => $this->context->link->getModulelink('marketplace', 'sellerlogin'),
            'validateUniquenessAjaxUrl' => $this->context->link->getModulelink('marketplace', 'validateuniqueshop'),
        ];

        Media::addJsDef($jsVars);
    }
}
