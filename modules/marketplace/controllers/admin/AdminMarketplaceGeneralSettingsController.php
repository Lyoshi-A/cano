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

class AdminMarketplaceGeneralSettingsController extends ModuleAdminController
{
    public $current_config_tab;
    public $tabList;

    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->table = 'configuration';

        parent::__construct();
        $this->toolbar_title = $this->l('General Settings');

        if (!Tools::getIsset('current_config_tab')) {
            $this->current_config_tab = 'general_configuration';
        } else {
            $this->current_config_tab = Tools::getValue('current_config_tab');
        }
    }

    public function initContent()
    {
        parent::initContent();
        $this->initToolbar();
        $this->display = '';

        $this->content .= $this->renderForm();
        $this->context->smarty->assign([
            'content' => $this->content,
        ]);
        if (!empty($this->context->cookie->marketplace_seller_rewrite_error_messages)) {
            $this->errors = json_decode($this->context->cookie->marketplace_seller_rewrite_error_messages);
            unset($this->context->cookie->marketplace_seller_rewrite_error_messages);
        }
    }

    public function tabConfig()
    {
        $this->tabList = [
            [
                'tab_name' => 'general_configuration',
                'label' => $this->l('General'),
                'icon' => 'icon-cogs',
            ],
            [
                'tab_name' => 'rewrite_url_settings',
                'label' => $this->l('Rewrite URL'),
                'icon' => 'icon-anchor',
            ],
            [
                'tab_name' => 'advertisement_settings',
                'label' => $this->l('Advertisement'),
                'icon' => 'icon-picture',
            ],
            [
                'tab_name' => 'seller_login_theme',
                'label' => $this->l('Seller Login Theme'),
                'icon' => 'icon-desktop',
            ],
            [
                'tab_name' => 'theme_settings',
                'label' => $this->l('Content Theme'),
                'icon' => 'icon-paint-brush',
            ],
        ];
    }

    public function renderForm()
    {
        $this->context->smarty->assign('current_config_tab', $this->current_config_tab);

        $wkSwitchOptions = [
            [
                'id' => 'active_on',
                'value' => 1,
                'label' => $this->l('Enabled'),
            ],
            [
                'id' => 'active_off',
                'value' => 0,
                'label' => $this->l('Disabled'),
            ],
        ];

        $form = [];
        $form['general_configuration'] = $this->generalConfiguration($wkSwitchOptions);
        $form['rewrite_url_settings'] = $this->rewriteUrlSettings($wkSwitchOptions);
        $form['advertisement_settings'] = $this->advertisementSettings($wkSwitchOptions);
        $form['theme_settings'] = $this->themeSettings($wkSwitchOptions);

        $this->assignSellerLoginThemeData();
        $form['seller_login_theme'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/seller_login_theme.tpl'
        );

        $this->tabConfig();

        $this->context->smarty->assign([
            'form' => $form,
            'tab_name' => $this->tabList,
        ]);

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/configuration_tablist.tpl'
        );
    }

    public function assignSellerLoginThemeData()
    {
        $smartyVars = [];
        $smartyVars['all_themes'] = WkMpSeller::getSellerLoginAllThemes();

        $activeThemeID = Configuration::get('WK_MP_SELLER_LOGIN_THEME');
        $smartyVars['active_theme_id'] = $activeThemeID;

        $previewImgDir = _MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/theme_preview/';
        $smartyVars['preview_img_dir'] = $previewImgDir;

        $prevImg = $previewImgDir . 'theme' . $activeThemeID . '.jpg';
        $smartyVars['prev_img'] = $prevImg;

        $this->context->smarty->assign($smartyVars);
    }

    public function generalConfiguration($wkSwitchOptions)
    {
        $orderStatusType = [
            ['id' => '1', 'name' => $this->l('Payment accepted')],
            ['id' => '2', 'name' => $this->l('Order confirmation')],
        ];

        $listDefaultLang = [
            ['id' => '1', 'name' => $this->l('Prestashop default language')],
            ['id' => '2', 'name' => $this->l('Seller default language')],
        ];

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('General configuration'),
                'icon' => 'icon-cogs',
            ],
            'input' => [
                [
                    'label' => $this->l('Superadmin email'),
                    'name' => 'WK_MP_SUPERADMIN_EMAIL',
                    'hint' => $this->l('All marketplace mails related to admin will be sent to this Email.'),
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                    'required' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Marketplace multilanguage'),
                    'name' => 'WK_MP_MULTILANG_ADMIN_APPROVE',
                    'hint' => $this->l('If Yes, Seller can use multi-language in Marketplace'),
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'default' => '1',
                    'values' => $wkSwitchOptions,
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Choose default language'),
                    'name' => 'WK_MP_MULTILANG_DEFAULT_LANG',
                    'desc' => $this->l('Note : If sellers update their product or edit their profile then product and profile data of all the languages will be filled same as the data in selected default language.'),
                    'options' => [
                        'query' => $listDefaultLang,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'hint' => $this->l('When multi language is off then selected language will be default language.'),
                    'form_group_class' => 'multilang_def_lang',
                ],
                [
                    'label' => $this->l('Earnings will display on the basis of'),
                    'name' => 'WK_MP_COMMISSION_DISTRIBUTE_ON',
                    'type' => 'select',
                    'options' => [
                        'query' => $orderStatusType,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'hint' => $this->l('Admin/Seller can view their earnings of payment accepted orders or of confirmed orders on transaction page. This settings will work if prestashop full order is payment accepted.'),
                ],
                [
                    'label' => $this->l('Seller dashboard graph will display on the basis of'),
                    'name' => 'WK_MP_DASHBOARD_GRAPH',
                    'type' => 'select',
                    'options' => [
                        'query' => $orderStatusType,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'hint' => $this->l('Seller can view graph of only payment accepted orders or of confirmed orders on dashboard page. This settings will work if prestashop full order is payment accepted.'),
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'submitGeneralconfiguration',
            ],
        ];

        $this->fields_value = [
            'WK_MP_SUPERADMIN_EMAIL' => Tools::getValue('WK_MP_SUPERADMIN_EMAIL', Configuration::get('WK_MP_SUPERADMIN_EMAIL')),
            'WK_MP_MULTILANG_ADMIN_APPROVE' => Tools::getValue('WK_MP_MULTILANG_ADMIN_APPROVE', Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')),
            'WK_MP_MULTILANG_DEFAULT_LANG' => Tools::getValue('WK_MP_MULTILANG_DEFAULT_LANG', Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG')),
            'WK_MP_COMMISSION_DISTRIBUTE_ON' => Tools::getValue('WK_MP_COMMISSION_DISTRIBUTE_ON', Configuration::get('WK_MP_COMMISSION_DISTRIBUTE_ON')),
            'WK_MP_DASHBOARD_GRAPH' => Tools::getValue('WK_MP_DASHBOARD_GRAPH', Configuration::get('WK_MP_DASHBOARD_GRAPH')),
        ];

        return parent::renderForm();
    }

    public function themeSettings($wkSwitchOptions)
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Content settings'),
                'icon' => 'icon-paint-brush',
            ],
            'input' => [
                [
                    'label' => $this->l('Allow custom css in front-end'),
                    'name' => 'WK_MP_ALLOW_CUSTOM_CSS',
                    'hint' => $this->l('If Yes, All seller pages will use custom CSS.'),
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'default' => '1',
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Page title background color '),
                    'name' => 'WK_MP_TITLE_BG_COLOR',
                    'hint' => $this->l('Background color will display in seller panel page title.'),
                    'type' => 'color',
                    'size' => 3,
                    'required' => true,
                ],
                [
                    'label' => $this->l('Page title text color '),
                    'name' => 'WK_MP_TITLE_TEXT_COLOR',
                    'hint' => $this->l('Text color will display in seller panel page title.'),
                    'type' => 'color',
                    'size' => 3,
                    'required' => true,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'submitThemeSettings',
            ],
        ];
        $this->fields_value = [
            'WK_MP_ALLOW_CUSTOM_CSS' => Tools::getValue('WK_MP_ALLOW_CUSTOM_CSS', Configuration::get('WK_MP_ALLOW_CUSTOM_CSS')),
            'WK_MP_TITLE_BG_COLOR' => Tools::getValue('WK_MP_TITLE_BG_COLOR', Configuration::get('WK_MP_TITLE_BG_COLOR')),
            'WK_MP_TITLE_TEXT_COLOR' => Tools::getValue('WK_MP_TITLE_TEXT_COLOR', Configuration::get('WK_MP_TITLE_TEXT_COLOR')),
        ];

        return parent::renderForm();
    }

    public function rewriteUrlSettings($wkSwitchOptions)
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Rewrite URL settings'),
                'icon' => 'icon-anchor',
            ],
            'input' => [
                [
                    'label' => $this->l('Marketplace SEO URL'),
                    'name' => 'WK_MP_URL_REWRITE_ADMIN_APPROVE',
                    'hint' => $this->l('If Yes, Seller\'s profile page, shop page and all reviews page url will be seo compatible.'),
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'default' => '1',
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Seller profile'),
                    'name' => 'WK_MP_SELLER_PROFILE_PREFIX',
                    'hint' => $this->l('Rewritten URL for seller\'s profile page'),
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                    'form_group_class' => 'mp_url_rewrite',
                    'required' => true,
                    'values' => '',
                ],
                [
                    'label' => $this->l('Seller shop'),
                    'name' => 'WK_MP_SELLER_SHOP_PREFIX',
                    'hint' => $this->l('Rewritten URL for seller\'s shop page'),
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                    'form_group_class' => 'mp_url_rewrite',
                    'required' => true,
                    'values' => '',
                ],
                [
                    'label' => $this->l('Seller reviews'),
                    'name' => 'WK_MP_SELLER_REVIEWS_PREFIX',
                    'hint' => $this->l('Rewritten URL for seller\'s all reviews page'),
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                    'form_group_class' => 'mp_url_rewrite',
                    'required' => true,
                    'values' => '',
                ],
                [
                    'label' => $this->l('Seller login'),
                    'name' => 'WK_MP_SELLER_LOGIN_PREFIX',
                    'hint' => $this->l('Rewritten URL for seller\'s login or sign up page'),
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                    'form_group_class' => 'mp_url_rewrite',
                    'required' => true,
                    'values' => '',
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'submitRewriteSetting',
            ],
        ];

        if (trim(Configuration::get('WK_MP_SELLER_LOGIN_PREFIX')) == 'login') {
            $this->errors[] = $this->l('Please enter unique re-write rule for seller login.');
        }
        if (empty($this->errors)) {
            $this->fields_value = [
                'WK_MP_URL_REWRITE_ADMIN_APPROVE' => Tools::getValue('WK_MP_URL_REWRITE_ADMIN_APPROVE', Configuration::get('WK_MP_URL_REWRITE_ADMIN_APPROVE')),
                'WK_MP_SELLER_PROFILE_PREFIX' => trim(Tools::getValue('WK_MP_SELLER_PROFILE_PREFIX', Configuration::get('WK_MP_SELLER_PROFILE_PREFIX'))),
                'WK_MP_SELLER_SHOP_PREFIX' => trim(Tools::getValue('WK_MP_SELLER_SHOP_PREFIX', Configuration::get('WK_MP_SELLER_SHOP_PREFIX'))),
                'WK_MP_SELLER_REVIEWS_PREFIX' => trim(Tools::getValue('WK_MP_SELLER_REVIEWS_PREFIX', Configuration::get('WK_MP_SELLER_REVIEWS_PREFIX'))),
                'WK_MP_SELLER_LOGIN_PREFIX' => trim(Tools::getValue('WK_MP_SELLER_LOGIN_PREFIX', Configuration::get('WK_MP_SELLER_LOGIN_PREFIX'))),
            ];
        }

        return parent::renderForm();
    }

    public function advertisementSettings($wkSwitchOptions)
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Advertisement settings'),
                'icon' => 'icon-picture',
            ],
            'input' => [
                [
                    'label' => $this->l('Display "Become a Seller" option in navigation bar'),
                    'name' => 'WK_MP_LINK_ON_NAV_BAR',
                    'hint' => $this->l('If Yes, A link with "Become a seller" option will be displayed in navigation bar.'),
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'default' => '1',
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Display "Become a Seller" option in footer bar'),
                    'name' => 'WK_MP_LINK_ON_FOOTER_BAR',
                    'hint' => $this->l('If Yes, A link with "Become a seller" option will be displayed in footer bar.'),
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'default' => '1',
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Display "Become a Seller" option in bottom Info Bar'),
                    'name' => 'WK_MP_LINK_ON_POP_UP',
                    'hint' => $this->l('If Yes, Info bar of "Become a seller" option will be displayed at bottom of your site.'),
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'default' => '1',
                    'values' => $wkSwitchOptions,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'submitAdvertisementSettings',
            ],
        ];

        $this->fields_value = [
            'WK_MP_LINK_ON_NAV_BAR' => Tools::getValue(
                'WK_MP_LINK_ON_NAV_BAR', Configuration::get('WK_MP_LINK_ON_NAV_BAR')
            ),
            'WK_MP_LINK_ON_FOOTER_BAR' => Tools::getValue(
                'WK_MP_LINK_ON_FOOTER_BAR', Configuration::get('WK_MP_LINK_ON_FOOTER_BAR')
            ),
            'WK_MP_LINK_ON_POP_UP' => Tools::getValue(
                'WK_MP_LINK_ON_POP_UP', Configuration::get('WK_MP_LINK_ON_POP_UP')
            ),
        ];

        return parent::renderForm();
    }

    public static function isColor($color)
    {
        return preg_match('/^#[a-f0-9]{6}$/i', $color);
    }

    public function saveGeneralconfiguration($idShop = null)
    {
        Configuration::updateValue(
            'WK_MP_SUPERADMIN_EMAIL',
            Tools::getValue('WK_MP_SUPERADMIN_EMAIL'),
            false,
            null,
            (int) $idShop
        );
        Configuration::updateValue(
            'WK_MP_MULTILANG_ADMIN_APPROVE',
            Tools::getValue('WK_MP_MULTILANG_ADMIN_APPROVE'),
            false,
            null,
            (int) $idShop
        );
        Configuration::updateValue(
            'WK_MP_MULTILANG_DEFAULT_LANG',
            Tools::getValue('WK_MP_MULTILANG_DEFAULT_LANG'),
            false,
            null,
            (int) $idShop
        );
        Configuration::updateValue(
            'WK_MP_COMMISSION_DISTRIBUTE_ON',
            Tools::getValue('WK_MP_COMMISSION_DISTRIBUTE_ON'),
            false,
            null,
            (int) $idShop
        );
        Configuration::updateValue(
            'WK_MP_DASHBOARD_GRAPH',
            Tools::getValue('WK_MP_DASHBOARD_GRAPH'),
            false,
            null,
            (int) $idShop
        );
    }

    public function saveRewriteSetting($idShop = null)
    {
        Configuration::updateValue(
            'WK_MP_URL_REWRITE_ADMIN_APPROVE',
            trim(Tools::getValue('WK_MP_URL_REWRITE_ADMIN_APPROVE')),
            false,
            null,
            (int) $idShop
        );
        Configuration::updateValue(
            'WK_MP_SELLER_PROFILE_PREFIX',
            trim(Tools::getValue('WK_MP_SELLER_PROFILE_PREFIX')),
            false,
            null,
            (int) $idShop
        );
        Configuration::updateValue(
            'WK_MP_SELLER_SHOP_PREFIX',
            trim(Tools::getValue('WK_MP_SELLER_SHOP_PREFIX')),
            false,
            null,
            (int) $idShop
        );
        Configuration::updateValue(
            'WK_MP_SELLER_REVIEWS_PREFIX',
            trim(Tools::getValue('WK_MP_SELLER_REVIEWS_PREFIX')),
            false,
            null,
            (int) $idShop
        );
        Configuration::updateValue(
            'WK_MP_SELLER_LOGIN_PREFIX',
            trim(Tools::getValue('WK_MP_SELLER_LOGIN_PREFIX')),
            false,
            null,
            (int) $idShop
        );

        Configuration::updateValue(
            'PS_ROUTE_module-marketplace-sellerprofile',
            trim(Tools::getValue('WK_MP_SELLER_PROFILE_PREFIX')) . '/{:mp_shop_name}',
            false,
            null,
            (int) $idShop
        );
        Configuration::updateValue(
            'PS_ROUTE_module-marketplace-shopstore',
            trim(Tools::getValue('WK_MP_SELLER_SHOP_PREFIX')) . '/{:mp_shop_name}',
            false,
            null,
            (int) $idShop
        );
        Configuration::updateValue(
            'PS_ROUTE_module-marketplace-allreviews',
            trim(Tools::getValue('WK_MP_SELLER_REVIEWS_PREFIX')) . '/{:mp_shop_name}',
            false,
            null,
            (int) $idShop
        );
        Configuration::updateValue(
            'PS_ROUTE_module-marketplace-sellerlogin',
            trim(Tools::getValue('WK_MP_SELLER_LOGIN_PREFIX')),
            false,
            null,
            (int) $idShop
        );
    }

    public function saveAdvertisementSettings($idShop = null)
    {
        Configuration::updateValue(
            'WK_MP_LINK_ON_NAV_BAR',
            Tools::getValue('WK_MP_LINK_ON_NAV_BAR'),
            false,
            null,
            (int) $idShop
        );
        Configuration::updateValue(
            'WK_MP_LINK_ON_FOOTER_BAR',
            Tools::getValue('WK_MP_LINK_ON_FOOTER_BAR'),
            false,
            null,
            (int) $idShop
        );
        Configuration::updateValue(
            'WK_MP_LINK_ON_POP_UP',
            Tools::getValue('WK_MP_LINK_ON_POP_UP'),
            false,
            null,
            (int) $idShop
        );
    }

    public function saveThemeSettings($idShop = null)
    {
        Configuration::updateValue(
            'WK_MP_ALLOW_CUSTOM_CSS',
            Tools::getValue('WK_MP_ALLOW_CUSTOM_CSS'),
            false,
            null,
            (int) $idShop
        );
        Configuration::updateValue(
            'WK_MP_TITLE_BG_COLOR',
            Tools::getValue('WK_MP_TITLE_BG_COLOR'),
            false,
            null,
            (int) $idShop
        );
        Configuration::updateValue(
            'WK_MP_TITLE_TEXT_COLOR',
            Tools::getValue('WK_MP_TITLE_TEXT_COLOR'),
            false,
            null,
            (int) $idShop
        );
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitGeneralconfiguration')) {
            if (!Validate::isEmail(Tools::getValue('WK_MP_SUPERADMIN_EMAIL'))) {
                $this->errors[] = $this->l('Superadmin email is required or invalid email.');
            }

            if (empty($this->errors)) {
                if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
                    // In case of All Shops
                    $this->saveGeneralconfiguration();
                    foreach (Shop::getShops() as $shop) {
                        $this->saveGeneralconfiguration((int) $shop['id_shop']);
                    }
                } else {
                    $this->saveGeneralconfiguration($this->context->shop->id);
                }
                Tools::redirectAdmin(
                    self::$currentIndex . '&conf=4&current_config_tab=general_configuration&token=' . $this->token
                );
            }
        } elseif (Tools::isSubmit('submitRewriteSetting')) {
            if (Tools::getValue('WK_MP_URL_REWRITE_ADMIN_APPROVE')) {
                $mpSellerProfilePrefix = Tools::getValue('WK_MP_SELLER_PROFILE_PREFIX');
                $mpSellerShopPrefix = Tools::getValue('WK_MP_SELLER_SHOP_PREFIX');
                $mpSellerReviewPrefix = Tools::getValue('WK_MP_SELLER_REVIEWS_PREFIX');
                $mpSellerLoginPrefix = Tools::getValue('WK_MP_SELLER_LOGIN_PREFIX');

                if ($mpSellerProfilePrefix == '') {
                    $this->errors[] = $this->l('Seller\'s profile page prefix is required field.');
                } elseif ($mpSellerProfilePrefix
                && (!Tools::link_rewrite($mpSellerProfilePrefix) || !Validate::isCatalogName($mpSellerProfilePrefix))) {
                    $this->errors[] = $this->l('Seller\'s profile page prefix is invalid.');
                }

                if ($mpSellerShopPrefix == '') {
                    $this->errors[] = $this->l('Seller\'s shop page prefix is required field.');
                } elseif ($mpSellerShopPrefix
                && (!Tools::link_rewrite($mpSellerShopPrefix) || !Validate::isCatalogName($mpSellerShopPrefix))) {
                    $this->errors[] = $this->l('Seller\'s shop page prefix is invalid.');
                }

                if ($mpSellerReviewPrefix == '') {
                    $this->errors[] = $this->l('Seller\'s reviews page prefix is required field.');
                } elseif ($mpSellerReviewPrefix
                && (!Tools::link_rewrite($mpSellerReviewPrefix) || !Validate::isCatalogName($mpSellerReviewPrefix))) {
                    $this->errors[] = $this->l('Seller\'s reviews page prefix is invalid.');
                }

                if ($mpSellerLoginPrefix == '') {
                    $this->errors[] = $this->l('Seller\'s login page prefix is required field.');
                } elseif ($mpSellerLoginPrefix
                && (!Tools::link_rewrite($mpSellerLoginPrefix) || !Validate::isCatalogName($mpSellerLoginPrefix))) {
                    $this->errors[] = $this->l('Seller\'s login page prefix is invalid.');
                }

                if ($mpSellerProfilePrefix && $mpSellerShopPrefix && $mpSellerReviewPrefix) {
                    $wkAllPrefix = [
                        Tools::getValue('WK_MP_SELLER_PROFILE_PREFIX'),
                        Tools::getValue('WK_MP_SELLER_SHOP_PREFIX'),
                        Tools::getValue('WK_MP_SELLER_REVIEWS_PREFIX'),
                        Tools::getValue('WK_MP_SELLER_LOGIN_PREFIX'),
                    ];
                    if (count(array_unique($wkAllPrefix)) != 4) { // If all prefix are not same it will return 3
                        $this->errors[] = $this->l('All prefix for rewrite URL must have different name.');
                    }
                }
            }

            if (empty($this->errors)) {
                if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
                    // In case of All Shops
                    $this->saveRewriteSetting();
                    foreach (Shop::getShops() as $shop) {
                        $this->saveRewriteSetting((int) $shop['id_shop']);
                    }
                } else {
                    $this->saveRewriteSetting($this->context->shop->id);
                }

                Tools::redirectAdmin(
                    self::$currentIndex . '&conf=4&current_config_tab=rewrite_url_settings&token=' . $this->token
                );
            } else {
                $this->context->cookie->marketplace_seller_rewrite_error_messages = json_encode($this->errors);
                Tools::redirectAdmin(
                    self::$currentIndex . '&current_config_tab=rewrite_url_settings&token=' . $this->token
                );
            }
        } elseif (Tools::isSubmit('submitAdvertisementSettings')) {
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
                // In case of All Shops
                $this->saveAdvertisementSettings();
                foreach (Shop::getShops() as $shop) {
                    $this->saveAdvertisementSettings((int) $shop['id_shop']);
                }
            } else {
                $this->saveAdvertisementSettings($this->context->shop->id);
            }

            Tools::redirectAdmin(
                self::$currentIndex . '&conf=4&current_config_tab=advertisement_settings&token=' . $this->token
            );
        } elseif (Tools::isSubmit('submitThemeSettings')) {
            $titleBgColor = Tools::getValue('WK_MP_TITLE_BG_COLOR');
            $titleTextColor = Tools::getValue('WK_MP_TITLE_TEXT_COLOR');

            if (!self::isColor($titleBgColor)) {
                $this->errors[] = $this->l('Page title background color is invalid.');
            }
            if (!self::isColor($titleTextColor)) {
                $this->errors[] = $this->l('Page title text color is invalid.');
            }

            if (empty($this->errors)) {
                if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
                    // In case of All Shops
                    $this->saveThemeSettings();
                    foreach (Shop::getShops() as $shop) {
                        $this->saveThemeSettings((int) $shop['id_shop']);
                    }
                } else {
                    $this->saveThemeSettings($this->context->shop->id);
                }

                Tools::redirectAdmin(
                    self::$currentIndex . '&conf=4&current_config_tab=theme_settings&token=' . $this->token
                );
            } else {
                $this->context->cookie->marketplace_seller_rewrite_error_messages = json_encode($this->errors);
                Tools::redirectAdmin(
                    self::$currentIndex . '&current_config_tab=theme_settings&token=' . $this->token
                );
            }
        } elseif (Tools::isSubmit('submitLoginTheme')) {
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
                // In case of All Shops
                Configuration::updateValue(
                    'WK_MP_SELLER_LOGIN_THEME',
                    Tools::getValue('login_theme'),
                    false,
                    null,
                    null
                );
                foreach (Shop::getShops() as $shop) {
                    Configuration::updateValue(
                        'WK_MP_SELLER_LOGIN_THEME',
                        Tools::getValue('login_theme'),
                        false,
                        null,
                        (int) $shop['id_shop']
                    );
                    $objLoginConf = new WkMpLoginConfigration();
                    if (!$objLoginConf->getShopThemeConfigration($shop['id_shop'], Tools::getValue('login_theme'))) {
                        WkMpLoginConfigration::setMpSellerLoginConfigurationsForNewShop($shop['id_shop']);
                    }
                }
            } else {
                Configuration::updateValue(
                    'WK_MP_SELLER_LOGIN_THEME',
                    Tools::getValue('login_theme'),
                    false,
                    null,
                    (int) $this->context->shop->id
                );
                $objLoginConf = new WkMpLoginConfigration();
                if (!$objLoginConf->getShopThemeConfigration($this->context->shop->id, Tools::getValue('login_theme'))) {
                    WkMpLoginConfigration::setMpSellerLoginConfigurationsForNewShop($this->context->shop->id);
                }
            }

            Tools::redirectAdmin(
                self::$currentIndex . '&conf=4&current_config_tab=seller_login_theme&token=' . $this->token
            );
        }

        parent::postProcess();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        Media::addJSDef([
            'color_picker_custom' => 1,
            'current_config_tab' => $this->current_config_tab,
            'moduleAdminLink' => $this->context->link->getAdminLink('AdminMarketplaceGeneralSettingsController'),
        ]);
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/mp_admin_config.js');
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/adminconfig.css');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/vue.min.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/mpsellerlogin/admin_sellerlogin.js');
    }
}
