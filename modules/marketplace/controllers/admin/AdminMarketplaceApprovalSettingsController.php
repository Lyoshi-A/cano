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

class AdminMarketplaceApprovalSettingsController extends ModuleAdminController
{
    public $current_config_tab;
    public $tabList;

    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->table = 'configuration';

        parent::__construct();
        $this->toolbar_title = $this->l('Approval Settings');

        if (!Tools::getIsset('current_config_tab')) {
            $this->current_config_tab = 'seller_approval_settings';
        } else {
            $this->current_config_tab = Tools::getValue('current_config_tab');
        }

        if ((_PS_VERSION_ < '1.7.3.0') && Configuration::get('WK_MP_PRODUCT_DELIVERY_TIME')) {
            // Delivery time feature is not added in PS V1.7.3.0 and above versions
            Configuration::updateValue('WK_MP_PRODUCT_DELIVERY_TIME', 0);
        }

        $this->context->smarty->assign(
            'shipping_commission_link',
            $this->context->link->getAdminLink('AdminMpShippingCommission')
        );
    }

    public function initContent()
    {
        $this->show_toolbar = false;
        $this->show_form_cancel_button = false;
        $this->display = 'edit';

        parent::initContent();
    }

    public function tabConfig()
    {
        $this->tabList = [
            [
                'tab_name' => 'seller_approval_settings',
                'label' => $this->l('Seller'),
                'icon' => 'icon-user',
            ],
            [
                'tab_name' => 'product_approval_settings',
                'label' => $this->l('Product'),
                'icon' => 'icon-list',
            ],
            [
                'tab_name' => 'customer_settings',
                'label' => $this->l('Customer'),
                'icon' => 'icon-user',
            ],
            [
                'tab_name' => 'carrier_settings',
                'label' => $this->l('Carrier'),
                'icon' => 'icon-exchange',
            ],
            [
                'tab_name' => 'order_settings',
                'label' => $this->l('Order'),
                'icon' => 'icon-shopping-cart',
            ],
            [
                'tab_name' => 'mail_settings',
                'label' => $this->l('Mail'),
                'icon' => 'icon-envelope',
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

        $adminDefShipping = [];
        if (Configuration::get('MP_SHIPPING_ADMIN_DEFAULT')) {
            $adminDefShipping = json_decode(Configuration::get('MP_SHIPPING_ADMIN_DEFAULT'));
        }
        $this->context->smarty->assign([
            'all_ps_carriers_arr' => WkMpSellerShipping::getOnlyPrestaCarriers($this->context->language->id),
            'admin_def_shipping' => $adminDefShipping,
        ]);

        $form = [];
        $form['seller_approval_settings'] = $this->sellerApprovalSettings($wkSwitchOptions);
        $form['product_approval_settings'] = $this->productApprovalSettings($wkSwitchOptions);
        $form['customer_settings'] = $this->customerSettings($wkSwitchOptions);
        $form['carrier_settings'] = $this->carrierSettings($wkSwitchOptions) . $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/carrier_settings.tpl'
        );
        $form['order_settings'] = $this->orderSettings($wkSwitchOptions);
        $form['mail_settings'] = $this->mailSettings($wkSwitchOptions);

        $this->tabConfig();

        $this->context->smarty->assign([
            'form' => $form,
            'tab_name' => $this->tabList,
        ]);

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/configuration_tablist.tpl'
        );
    }

    public function sellerApprovalSettings($wkSwitchOptions)
    {
        $listCMSPages = [
            ['id_cms' => '', 'meta_title' => $this->l('--- Select CMS page ---')],
        ];

        $cmsPages = CMS::getCMSPages($this->context->language->id, null, true, $this->context->shop->id);
        if ($cmsPages) {
            foreach ($cmsPages as $cpage) {
                $listCMSPages[] = $cpage;
            }
        }

        $objMarketplace = new Marketplace();

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Seller settings'),
                'icon' => 'icon-user',
            ],
            'input' => [
                [
                    'label' => $this->l('Sellers need to be approved by admin'),
                    'name' => 'WK_MP_SELLER_ADMIN_APPROVE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                    'hint' => $this->l('If No, Marketplace Seller request will be automatically approved.'),
                ],
                [
                    'label' => $this->l('Sellers need to agree terms and conditions'),
                    'name' => 'WK_MP_TERMS_AND_CONDITIONS_STATUS',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                    'hint' => $this->l('Sellers have to agree to the terms and conditions while registering.'),
                ],
                [
                    'label' => $this->l('CMS page'),
                    'hint' => $this->l('CMS page link will display on seller request page.'),
                    'name' => 'WK_MP_TERMS_AND_CONDITIONS_CMS',
                    'type' => 'select',
                    'form_group_class' => 'wk_mp_termsncond',
                    'options' => [
                        'query' => $listCMSPages,
                        'id' => 'id_cms',
                        'name' => 'meta_title',
                    ],
                    'required' => true,
                ],
                [
                    'label' => $this->l('Seller phone maximum digit'),
                    'hint' => $this->l('Maximum number of digits that a seller can enter in phone number.'),
                    'name' => 'WK_MP_PHONE_DIGIT',
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                    'required' => true,
                ],
                [
                    'label' => $this->l('Seller reviews to be approved by admin '),
                    'hint' => $this->l('If No, Marketplace Seller review will be automatically approved.'),
                    'name' => 'WK_MP_REVIEWS_ADMIN_APPROVE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Seller profile deactivation needs reason'),
                    'hint' => $this->l('If Yes, Admin needs to provide a reason for deactivating seller\'s profile.'),
                    'name' => 'WK_MP_SELLER_PROFILE_DEACTIVATE_REASON',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can activate/deactivate their shop'),
                    'hint' => $this->l('Sellers can enable and disable their shop.'),
                    'name' => 'WK_MP_SELLER_SHOP_SETTINGS',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers need to provide their city, country and zip/postal code'),
                    'hint' => $this->l('If Yes, Seller/Admin has to fill city, country and zip/postal code in seller address. Zip/postal code will be enable on the basis of country settings.'),
                    'name' => 'WK_MP_SELLER_COUNTRY_NEED',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can provide fax number'),
                    'hint' => $this->l('If Yes, Seller will be able to add fax in their profile.'),
                    'name' => 'WK_MP_SELLER_FAX',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can provide tax identification number'),
                    'hint' => $this->l('If Yes, Seller will be able to add tax identification number in their profile.'),
                    'name' => 'WK_MP_SELLER_TAX_IDENTIFICATION_NUMBER',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can contact admin via Email'),
                    'hint' => $this->l('If Yes, Seller can contact admin via Email from Edit profile page.'),
                    'name' => 'WK_MP_SHOW_ADMIN_DETAILS',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can manage attributes and their values'),
                    'hint' => $this->l('If Yes, Sellers can add, edit and delete prestashop attributes and their values.'),
                    'name' => 'WK_MP_PRESTA_ATTRIBUTE_ACCESS',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can manage features and their values'),
                    'hint' => $this->l('If Yes, Sellers can add, edit and delete prestashop features and their values.'),
                    'name' => 'WK_MP_PRESTA_FEATURE_ACCESS',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can provide their social profile links'),
                    'hint' => $this->l('If Yes, Sellers will able to add their social IDS like Facebook ID, Twitter ID, Youtube ID and Instagram ID'),
                    'name' => 'WK_MP_SOCIAL_TABS',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Facebook'),
                    'hint' => $this->l('If Yes, Sellers will be able to add their facebook id.'),
                    'name' => 'WK_MP_SELLER_FACEBOOK',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                    'form_group_class' => 'wk_mp_social_tab',
                ],
                [
                    'label' => $this->l('Twitter'),
                    'hint' => $this->l('If Yes, Sellers will be able to add their twitter id.'),
                    'name' => 'WK_MP_SELLER_TWITTER',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                    'form_group_class' => 'wk_mp_social_tab',
                ],
                [
                    'label' => $this->l('Youtube'),
                    'hint' => $this->l('If Yes, Sellers will be able to add their Youtube id.'),
                    'name' => 'WK_MP_SELLER_YOUTUBE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                    'form_group_class' => 'wk_mp_social_tab',
                ],
                [
                    'label' => $this->l('Instagram'),
                    'hint' => $this->l('If Yes, Sellers will be able to add their instagram id.'),
                    'name' => 'WK_MP_SELLER_INSTAGRAM',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                    'form_group_class' => 'wk_mp_social_tab',
                ],
                [
                    'label' => $this->l('Sellers can manage their display settings'),
                    'hint' => $this->l('If Yes, Seller will be able to change the display settings as per the options provided by the admin in default Settings.'),
                    'name' => 'WK_MP_SELLER_DETAILS_PERMISSION',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can export products and orders CSV'),
                    'hint' => $this->l('If Yes, Sellers can export products and orders CSV.'),
                    'name' => 'WK_MP_SELLER_EXPORT',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Display seller details'),
                    'name' => 'WK_MP_SHOW_SELLER_DETAILS',
                    'required' => true,
                    'is_bool' => true,
                    'default' => '1',
                    'values' => $wkSwitchOptions,
                    'hint' => $this->l('If Yes, Seller details can be displayed on seller\'s shop page and profile page.'),
                ],
                [
                    'type' => 'group',
                    'label' => $this->l('Customize details'),
                    'name' => 'groupBox',
                    'values' => $objMarketplace->sellerDetailsView,
                    'col' => '8',
                    'form_group_class' => 'wk_mp_seller_details',
                    'hint' => $this->l('Select the details that will be available to seller for displaying it on seller\'s shop page and profile page.'),
                    'required' => true,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'submitSellerApprovalSettings',
            ],
        ];

        $this->fields_value = [
            'WK_MP_SELLER_ADMIN_APPROVE' => Tools::getValue('WK_MP_SELLER_ADMIN_APPROVE', Configuration::get('WK_MP_SELLER_ADMIN_APPROVE')),
            'WK_MP_TERMS_AND_CONDITIONS_STATUS' => Tools::getValue('WK_MP_TERMS_AND_CONDITIONS_STATUS', Configuration::get('WK_MP_TERMS_AND_CONDITIONS_STATUS')),
            'WK_MP_TERMS_AND_CONDITIONS_CMS' => Tools::getValue('WK_MP_TERMS_AND_CONDITIONS_CMS', Configuration::get('WK_MP_TERMS_AND_CONDITIONS_CMS')),
            'WK_MP_PHONE_DIGIT' => Tools::getValue('WK_MP_PHONE_DIGIT', Configuration::get('WK_MP_PHONE_DIGIT')),
            'WK_MP_REVIEWS_ADMIN_APPROVE' => Tools::getValue('WK_MP_REVIEWS_ADMIN_APPROVE', Configuration::get('WK_MP_REVIEWS_ADMIN_APPROVE')),
            'WK_MP_SELLER_PROFILE_DEACTIVATE_REASON' => Tools::getValue('WK_MP_SELLER_PROFILE_DEACTIVATE_REASON', Configuration::get('WK_MP_SELLER_PROFILE_DEACTIVATE_REASON')),
            'WK_MP_SELLER_SHOP_SETTINGS' => Tools::getValue('WK_MP_SELLER_SHOP_SETTINGS', Configuration::get('WK_MP_SELLER_SHOP_SETTINGS')),
            'WK_MP_SELLER_COUNTRY_NEED' => Tools::getValue('WK_MP_SELLER_COUNTRY_NEED', Configuration::get('WK_MP_SELLER_COUNTRY_NEED')),
            'WK_MP_SELLER_FAX' => Tools::getValue('WK_MP_SELLER_FAX', Configuration::get('WK_MP_SELLER_FAX')),
            'WK_MP_SELLER_TAX_IDENTIFICATION_NUMBER' => Tools::getValue('WK_MP_SELLER_TAX_IDENTIFICATION_NUMBER', Configuration::get('WK_MP_SELLER_TAX_IDENTIFICATION_NUMBER')),
            'WK_MP_SHOW_ADMIN_DETAILS' => Tools::getValue('WK_MP_SHOW_ADMIN_DETAILS', Configuration::get('WK_MP_SHOW_ADMIN_DETAILS')),
            'WK_MP_PRESTA_ATTRIBUTE_ACCESS' => Tools::getValue('WK_MP_PRESTA_ATTRIBUTE_ACCESS', Configuration::get('WK_MP_PRESTA_ATTRIBUTE_ACCESS')),
            'WK_MP_PRESTA_FEATURE_ACCESS' => Tools::getValue('WK_MP_PRESTA_FEATURE_ACCESS', Configuration::get('WK_MP_PRESTA_FEATURE_ACCESS')),
            'WK_MP_SOCIAL_TABS' => Tools::getValue('WK_MP_SOCIAL_TABS', Configuration::get('WK_MP_SOCIAL_TABS')),
            'WK_MP_SELLER_FACEBOOK' => Tools::getValue('WK_MP_SELLER_FACEBOOK', Configuration::get('WK_MP_SELLER_FACEBOOK')),
            'WK_MP_SELLER_TWITTER' => Tools::getValue('WK_MP_SELLER_TWITTER', Configuration::get('WK_MP_SELLER_TWITTER')),
            'WK_MP_SELLER_YOUTUBE' => Tools::getValue('WK_MP_SELLER_YOUTUBE', Configuration::get('WK_MP_SELLER_YOUTUBE')),
            'WK_MP_SELLER_INSTAGRAM' => Tools::getValue('WK_MP_SELLER_INSTAGRAM', Configuration::get('WK_MP_SELLER_INSTAGRAM')),
            'WK_MP_SELLER_DETAILS_PERMISSION' => Tools::getValue('WK_MP_SELLER_DETAILS_PERMISSION', Configuration::get('WK_MP_SELLER_DETAILS_PERMISSION')),
            'WK_MP_SELLER_EXPORT' => Tools::getValue('WK_MP_SELLER_EXPORT', Configuration::get('WK_MP_SELLER_EXPORT')),
            'WK_MP_SHOW_SELLER_DETAILS' => Tools::getValue(
                'WK_MP_SHOW_SELLER_DETAILS', Configuration::get('WK_MP_SHOW_SELLER_DETAILS')
            ),
        ];

        if ($objMarketplace->sellerDetailsView) {
            $i = 1;
            $sellerDetailsAccess = json_decode(Configuration::get('WK_MP_SELLER_DETAILS_ACCESS'));
            foreach ($objMarketplace->sellerDetailsView as $sellerDetailsVal) {
                if ($sellerDetailsAccess && in_array($sellerDetailsVal['id_group'], $sellerDetailsAccess)) {
                    $groupVal = 1;
                } else {
                    $groupVal = '';
                }
                $this->fields_value['groupBox_' . $i] = $groupVal;
                ++$i;
            }
        }

        return parent::renderForm();
    }

    public function productApprovalSettings($wkSwitchOptions)
    {
        if (Module::isEnabled('wkcombinationcustomize')) {
            $wkCombinationCustomize = 1;
        } else {
            $wkCombinationCustomize = 0;
            Configuration::updateValue('WK_MP_PRODUCT_COMBINATION_CUSTOMIZE', 0);
        }

        if (_PS_VERSION_ < '1.7.7.0') {
            $wkProductMPN = 0;
            Configuration::updateValue('WK_MP_PRODUCT_MPN', 0);
        } else {
            $wkProductMPN = 1;
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Product settings'),
                'icon' => 'icon-list',
            ],
            'input' => [
                [
                    'label' => $this->l('Products need to be approved by admin'),
                    'hint' => $this->l('If No, Marketplace Seller Product will be automatically approved.'),
                    'name' => 'WK_MP_PRODUCT_ADMIN_APPROVE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Seller products deactivation needs reason'),
                    'hint' => $this->l('If Yes, Admin needs to provide a reason for deactivating seller\'s product.'),
                    'name' => 'WK_MP_SELLER_PRODUCTS_DEACTIVATE_REASON',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can activate/deactivate their products'),
                    'hint' => $this->l("If Yes, Sellers can enable and disable their products when seller's products are created in catalog."),
                    'name' => 'WK_MP_SELLER_PRODUCTS_SETTINGS',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Updated products has to be approved by admin'),
                    'hint' => $this->l('If Yes, Product need to be approved by admin after updated by seller'),
                    'name' => 'WK_MP_PRODUCT_UPDATE_ADMIN_APPROVE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Show admin commission to seller'),
                    'hint' => $this->l('Display admin commission to seller on add/update product and product details page.'),
                    'name' => 'WK_MP_SHOW_ADMIN_COMMISSION',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can apply tax rule on their products'),
                    'hint' => $this->l('If Yes, Seller will be able to apply tax rule on product.'),
                    'name' => 'WK_MP_SELLER_APPLIED_TAX_RULE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add SEO on their products'),
                    'hint' => $this->l('If Yes, Seller will be able to add SEO on product.'),
                    'name' => 'WK_MP_SELLER_PRODUCT_SEO',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can set product visibility options on their products'),
                    'hint' => $this->l('If Yes, Seller will be able to change product visibility of product.'),
                    'name' => 'WK_MP_SELLER_PRODUCT_VISIBILITY',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can select availability preferences for their products'),
                    'hint' => $this->l('If Yes, Seller will be able to select availability preference for their out of stock products.'),
                    'name' => 'WK_MP_SELLER_PRODUCT_AVAILABILITY',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add Reference on their products'),
                    'hint' => $this->l('If Yes, Seller will be able to add reference on product.'),
                    'name' => 'WK_MP_SELLER_PRODUCT_REFERENCE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add MPN on their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to add MPN on their products.'),
                    'name' => 'WK_MP_PRODUCT_MPN',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                    'disabled' => ($wkProductMPN ? false : true),
                    'desc' => (!$wkProductMPN ? $this->l('Your Prestashop version must be greater than or equal to 1.7.7.0') : ''),
                ],
                [
                    'label' => $this->l('Sellers can add UPC barcode on their products'),
                    'hint' => $this->l('If Yes, Seller will be able to add UPC barcode on product.'),
                    'name' => 'WK_MP_SELLER_PRODUCT_UPC',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add EAN-13 or JAN barcode on their products'),
                    'hint' => $this->l('If Yes, Seller will be able to add EAN-13 or JAN barcode on product.'),
                    'name' => 'WK_MP_SELLER_PRODUCT_EAN',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add ISBN on their products'),
                    'hint' => $this->l('If Yes, Seller will be able to add ISBN on product.'),
                    'name' => 'WK_MP_SELLER_PRODUCT_ISBN',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can create combinations for their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to create combinations for their products using admin added attributes and values.'),
                    'name' => 'WK_MP_SELLER_PRODUCT_COMBINATION',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can activate/deactivate their combinations'),
                    'hint' => $this->l('If Yes, Seller can activate/deactivate their combinations through Prestashop Combination Activate/Deactivate Module.'),
                    'disabled' => ($wkCombinationCustomize ? false : true),
                    'desc' => (!$wkCombinationCustomize ? $this->l('Our module Prestashop combination activate/deactivate must be enable.') : ''),
                    'name' => 'WK_MP_PRODUCT_COMBINATION_CUSTOMIZE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                    'form_group_class' => 'wk_mp_combination_customize',
                ],
                [
                    'label' => $this->l('Sellers can apply admin shipping on their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to assign admin added shipping methods to their products.'),
                    'name' => 'WK_MP_SELLER_ADMIN_SHIPPING',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add delivery time on their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to add delivery time for their in-stock and out-of-stock products.'),
                    'disabled' => (_PS_VERSION_ < '1.7.3.0' ? true : false),
                    'desc' => (_PS_VERSION_ < '1.7.3.0' ? $this->l('Your Prestashop version must be greater than or equal to 1.7.3.0') : $this->l('When shipping tab is visible only then, delivery time can be managed by seller.')),
                    'name' => 'WK_MP_PRODUCT_DELIVERY_TIME',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can apply additional shipping costs on their products'),
                    'hint' => $this->l('If Yes, Sellers would be able to apply additional shipping costs for their products.'),
                    'desc' => $this->l('When shipping tab is visible only then, additional shipping cost can be managed by seller.'),
                    'name' => 'WK_MP_PRODUCT_ADDITIONAL_FEES',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add features on their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to add admin added features to their products.'),
                    'name' => 'WK_MP_PRODUCT_FEATURE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can change condition of their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to change condition of their products.'),
                    'name' => 'WK_MP_PRODUCT_CONDITION',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add minimum quantity on their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to add minimum quantity to their products.'),
                    'name' => 'WK_MP_PRODUCT_MIN_QTY',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add low stock level on their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to get notification on the basis of low stock level on their products.'),
                    'name' => 'WK_MP_PRODUCT_LOW_STOCK_ALERT',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add cost price on their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to add cost price to their products.'),
                    'name' => 'WK_MP_PRODUCT_WHOLESALE_PRICE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add price per unit on their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to add price per unit to their products.'),
                    'name' => 'WK_MP_PRODUCT_PRICE_PER_UNIT',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can display "On sale!" flag on their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to display "On sale!" flag on their products.'),
                    'name' => 'WK_MP_PRODUCT_ON_SALE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can duplicate their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to duplicate their products.'),
                    'name' => 'WK_MP_PRODUCT_ALLOW_DUPLICATE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Duplicate product without stock'),
                    'hint' => $this->l('If Yes, Duplicate product will be created with zero quantity otherwise original product quantity will be set.'),
                    'name' => 'WK_MP_PRODUCT_DUPLICATE_QUANTITY',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Prefix title for duplicate product name'),
                    'hint' => $this->l('This title will be added as prefix in duplicate product name, if it is not already added.'),
                    'desc' => $this->l('Leave blank if you do not want to add any prefix title'),
                    'name' => 'WK_MP_PRODUCT_DUPLICATE_TITLE',
                    'type' => 'text',
                    'lang' => true,
                ],
                [
                    'label' => $this->l('Sellers can add stock location on their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to add stock location on their products.'),
                    'name' => 'WK_MP_PRODUCT_STOCK_LOCATION',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can set page redirection on their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to set page redirection on their products.'),
                    'name' => 'WK_MP_PRODUCT_PAGE_REDIRECTION',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add image caption on their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to add image caption on their products.'),
                    'name' => 'WK_MP_PRODUCT_IMAGE_CAPTION',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add related products on their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to add related products on their products.'),
                    'name' => 'WK_MP_RELATED_PRODUCT',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add tags for products'),
                    'hint' => $this->l('If Yes, Sellers will be able to add tags for products.'),
                    'name' => 'WK_MP_PRODUCT_TAGS',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add customizable products'),
                    'hint' => $this->l('If Yes, Sellers will be able add customizable products.'),
                    'name' => 'WK_MP_PRODUCT_CUSTOMIZATION',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add attachments products'),
                    'hint' => $this->l('If Yes, Sellers will be able to add attachments products.'),
                    'name' => 'WK_MP_PRODUCT_ATTACHMENT',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add specific rules on their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to create specific rules on their products.'),
                    'name' => 'WK_MP_PRODUCT_SPECIFIC_RULE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add pack products'),
                    'hint' => $this->l('If Yes, Sellers will be able to create pack products.'),
                    'name' => 'WK_MP_PACK_PRODUCTS',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can add virtual products'),
                    'hint' => $this->l('If Yes, Sellers will be able to create virtual products.'),
                    'name' => 'WK_MP_VIRTUAL_PRODUCT',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can manage brands on their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to manage brands on their products.'),
                    'name' => 'WK_MP_PRODUCT_MANUFACTURER',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Seller can assign admin brands'),
                    'hint' => $this->l('If Yes, Show admin brands in the list on manage product page while adding a product.'),
                    'name' => 'WK_MP_PRODUCT_MANUFACTURER_ADMIN',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers brand need to be approved by admin'),
                    'hint' => $this->l('If No, all brands are automatically approved.'),
                    'name' => 'WK_MP_PRODUCT_MANUFACTURER_APPROVED',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers can manage suppliers on their products'),
                    'hint' => $this->l('If Yes, Sellers will be able to manage suppliers on their products.'),
                    'name' => 'WK_MP_PRODUCT_SUPPLIER',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Seller can assign admin suppliers'),
                    'hint' => $this->l('If Yes, Show admin suppliers in the list on add product page while adding a product.'),
                    'name' => 'WK_MP_PRODUCT_SUPPLIER_ADMIN',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Sellers supplier need to be approved by admin'),
                    'hint' => $this->l('If No, all suppliers are automatically approved.'),
                    'name' => 'WK_MP_PRODUCT_SUPPLIER_APPROVED',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Allow category restriction on seller\'s product'),
                    'hint' => $this->l('If Yes, Seller can attach product to allowed categories only.'),
                    'name' => 'WK_MP_PRODUCT_CATEGORY_RESTRICTION',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'submitProductApprovalSettings',
            ],
        ];

        $this->fields_value = [
            'WK_MP_PRODUCT_ADMIN_APPROVE' => Tools::getValue('WK_MP_PRODUCT_ADMIN_APPROVE', Configuration::get('WK_MP_PRODUCT_ADMIN_APPROVE')),
            'WK_MP_SELLER_PRODUCTS_DEACTIVATE_REASON' => Tools::getValue('WK_MP_SELLER_PRODUCTS_DEACTIVATE_REASON', Configuration::get('WK_MP_SELLER_PRODUCTS_DEACTIVATE_REASON')),
            'WK_MP_SELLER_PRODUCTS_SETTINGS' => Tools::getValue('WK_MP_SELLER_PRODUCTS_SETTINGS', Configuration::get('WK_MP_SELLER_PRODUCTS_SETTINGS')),
            'WK_MP_PRODUCT_UPDATE_ADMIN_APPROVE' => Tools::getValue('WK_MP_PRODUCT_UPDATE_ADMIN_APPROVE', Configuration::get('WK_MP_PRODUCT_UPDATE_ADMIN_APPROVE')),
            'WK_MP_SHOW_ADMIN_COMMISSION' => Tools::getValue('WK_MP_SHOW_ADMIN_COMMISSION', Configuration::get('WK_MP_SHOW_ADMIN_COMMISSION')),
            'WK_MP_SELLER_APPLIED_TAX_RULE' => Tools::getValue('WK_MP_SELLER_APPLIED_TAX_RULE', Configuration::get('WK_MP_SELLER_APPLIED_TAX_RULE')),
            'WK_MP_SELLER_PRODUCT_SEO' => Tools::getValue('WK_MP_SELLER_PRODUCT_SEO', Configuration::get('WK_MP_SELLER_PRODUCT_SEO')),
            'WK_MP_SELLER_PRODUCT_VISIBILITY' => Tools::getValue('WK_MP_SELLER_PRODUCT_VISIBILITY', Configuration::get('WK_MP_SELLER_PRODUCT_VISIBILITY')),
            'WK_MP_SELLER_PRODUCT_AVAILABILITY' => Tools::getValue('WK_MP_SELLER_PRODUCT_AVAILABILITY', Configuration::get('WK_MP_SELLER_PRODUCT_AVAILABILITY')),
            'WK_MP_SELLER_PRODUCT_REFERENCE' => Tools::getValue('WK_MP_SELLER_PRODUCT_REFERENCE', Configuration::get('WK_MP_SELLER_PRODUCT_REFERENCE')),
            'WK_MP_PRODUCT_MPN' => Tools::getValue('WK_MP_PRODUCT_MPN', Configuration::get('WK_MP_PRODUCT_MPN')),
            'WK_MP_SELLER_PRODUCT_UPC' => Tools::getValue('WK_MP_SELLER_PRODUCT_UPC', Configuration::get('WK_MP_SELLER_PRODUCT_UPC')),
            'WK_MP_SELLER_PRODUCT_EAN' => Tools::getValue('WK_MP_SELLER_PRODUCT_EAN', Configuration::get('WK_MP_SELLER_PRODUCT_EAN')),
            'WK_MP_SELLER_PRODUCT_ISBN' => Tools::getValue('WK_MP_SELLER_PRODUCT_ISBN', Configuration::get('WK_MP_SELLER_PRODUCT_ISBN')),
            'WK_MP_SELLER_PRODUCT_COMBINATION' => Tools::getValue('WK_MP_SELLER_PRODUCT_COMBINATION', Configuration::get('WK_MP_SELLER_PRODUCT_COMBINATION')),
            'WK_MP_PRODUCT_COMBINATION_CUSTOMIZE' => Tools::getValue('WK_MP_PRODUCT_COMBINATION_CUSTOMIZE', Configuration::get('WK_MP_PRODUCT_COMBINATION_CUSTOMIZE')),
            'WK_MP_SELLER_ADMIN_SHIPPING' => Tools::getValue('WK_MP_SELLER_ADMIN_SHIPPING', Configuration::get('WK_MP_SELLER_ADMIN_SHIPPING')),
            'WK_MP_PRODUCT_DELIVERY_TIME' => Tools::getValue('WK_MP_PRODUCT_DELIVERY_TIME', Configuration::get('WK_MP_PRODUCT_DELIVERY_TIME')),
            'WK_MP_PRODUCT_ADDITIONAL_FEES' => Tools::getValue('WK_MP_PRODUCT_ADDITIONAL_FEES', Configuration::get('WK_MP_PRODUCT_ADDITIONAL_FEES')),
            'WK_MP_PRODUCT_FEATURE' => Tools::getValue('WK_MP_PRODUCT_FEATURE', Configuration::get('WK_MP_PRODUCT_FEATURE')),
            'WK_MP_PRODUCT_CONDITION' => Tools::getValue('WK_MP_PRODUCT_CONDITION', Configuration::get('WK_MP_PRODUCT_CONDITION')),
            'WK_MP_PRODUCT_MIN_QTY' => Tools::getValue('WK_MP_PRODUCT_MIN_QTY', Configuration::get('WK_MP_PRODUCT_MIN_QTY')),
            'WK_MP_PRODUCT_LOW_STOCK_ALERT' => Tools::getValue('WK_MP_PRODUCT_LOW_STOCK_ALERT', Configuration::get('WK_MP_PRODUCT_LOW_STOCK_ALERT')),
            'WK_MP_PRODUCT_WHOLESALE_PRICE' => Tools::getValue('WK_MP_PRODUCT_WHOLESALE_PRICE', Configuration::get('WK_MP_PRODUCT_WHOLESALE_PRICE')),
            'WK_MP_PRODUCT_PRICE_PER_UNIT' => Tools::getValue('WK_MP_PRODUCT_PRICE_PER_UNIT', Configuration::get('WK_MP_PRODUCT_PRICE_PER_UNIT')),
            'WK_MP_PRODUCT_ON_SALE' => Tools::getValue('WK_MP_PRODUCT_ON_SALE', Configuration::get('WK_MP_PRODUCT_ON_SALE')),
            'WK_MP_PRODUCT_ALLOW_DUPLICATE' => Tools::getValue('WK_MP_PRODUCT_ALLOW_DUPLICATE', Configuration::get('WK_MP_PRODUCT_ALLOW_DUPLICATE')),
            'WK_MP_PRODUCT_DUPLICATE_QUANTITY' => Tools::getValue('WK_MP_PRODUCT_DUPLICATE_QUANTITY', Configuration::get('WK_MP_PRODUCT_DUPLICATE_QUANTITY')),
            'WK_MP_PRODUCT_STOCK_LOCATION' => Tools::getValue('WK_MP_PRODUCT_STOCK_LOCATION', Configuration::get('WK_MP_PRODUCT_STOCK_LOCATION')),
            'WK_MP_PRODUCT_PAGE_REDIRECTION' => Tools::getValue('WK_MP_PRODUCT_PAGE_REDIRECTION', Configuration::get('WK_MP_PRODUCT_PAGE_REDIRECTION')),
            'WK_MP_PRODUCT_IMAGE_CAPTION' => Tools::getValue('WK_MP_PRODUCT_IMAGE_CAPTION', Configuration::get('WK_MP_PRODUCT_IMAGE_CAPTION')),
            'WK_MP_RELATED_PRODUCT' => Tools::getValue('WK_MP_RELATED_PRODUCT', Configuration::get('WK_MP_RELATED_PRODUCT')),
            'WK_MP_PRODUCT_TAGS' => Tools::getValue('WK_MP_PRODUCT_TAGS', Configuration::get('WK_MP_PRODUCT_TAGS')),
            'WK_MP_PRODUCT_CUSTOMIZATION' => Tools::getValue('WK_MP_PRODUCT_CUSTOMIZATION', Configuration::get('WK_MP_PRODUCT_CUSTOMIZATION')),
            'WK_MP_PRODUCT_ATTACHMENT' => Tools::getValue('WK_MP_PRODUCT_ATTACHMENT', Configuration::get('WK_MP_PRODUCT_ATTACHMENT')),
            'WK_MP_PRODUCT_SPECIFIC_RULE' => Tools::getValue('WK_MP_PRODUCT_SPECIFIC_RULE', Configuration::get('WK_MP_PRODUCT_SPECIFIC_RULE')),
            'WK_MP_PACK_PRODUCTS' => Tools::getValue('WK_MP_PACK_PRODUCTS', Configuration::get('WK_MP_PACK_PRODUCTS')),
            'WK_MP_VIRTUAL_PRODUCT' => Tools::getValue('WK_MP_VIRTUAL_PRODUCT', Configuration::get('WK_MP_VIRTUAL_PRODUCT')),
            'WK_MP_PRODUCT_MANUFACTURER' => Tools::getValue('WK_MP_PRODUCT_MANUFACTURER', Configuration::get('WK_MP_PRODUCT_MANUFACTURER')),
            'WK_MP_PRODUCT_MANUFACTURER_ADMIN' => Tools::getValue('WK_MP_PRODUCT_MANUFACTURER_ADMIN', Configuration::get('WK_MP_PRODUCT_MANUFACTURER_ADMIN')),
            'WK_MP_PRODUCT_MANUFACTURER_APPROVED' => Tools::getValue('WK_MP_PRODUCT_MANUFACTURER_APPROVED', Configuration::get('WK_MP_PRODUCT_MANUFACTURER_APPROVED')),
            'WK_MP_PRODUCT_SUPPLIER' => Tools::getValue('WK_MP_PRODUCT_SUPPLIER', Configuration::get('WK_MP_PRODUCT_SUPPLIER')),
            'WK_MP_PRODUCT_SUPPLIER_ADMIN' => Tools::getValue('WK_MP_PRODUCT_SUPPLIER_ADMIN', Configuration::get('WK_MP_PRODUCT_SUPPLIER_ADMIN')),
            'WK_MP_PRODUCT_SUPPLIER_APPROVED' => Tools::getValue('WK_MP_PRODUCT_SUPPLIER_APPROVED', Configuration::get('WK_MP_PRODUCT_SUPPLIER_APPROVED')),
            'WK_MP_PRODUCT_CATEGORY_RESTRICTION' => Tools::getValue('WK_MP_PRODUCT_CATEGORY_RESTRICTION', Configuration::get('WK_MP_PRODUCT_CATEGORY_RESTRICTION')),
        ];

        $duplicateTitleValues = [];
        foreach (Language::getLanguages(false) as $language) {
            $duplicateTitleValues[$language['id_lang']] = Tools::getValue('WK_MP_PRODUCT_DUPLICATE_TITLE_' . $language['id_lang'], Configuration::get('WK_MP_PRODUCT_DUPLICATE_TITLE', $language['id_lang']));
        }
        $this->fields_value['WK_MP_PRODUCT_DUPLICATE_TITLE'] = $duplicateTitleValues;

        return parent::renderForm();
    }

    public function customerSettings($wkSwitchOptions)
    {
        $listReviewShow = [
            ['id' => '1', 'name' => $this->l('Sort by most recent review')],
            ['id' => '2', 'name' => $this->l('Sort by most helpful review')],
        ];

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Customer settings'),
                'icon' => 'icon-user',
            ],
            'input' => [
                [
                    'label' => $this->l('Only registered customers can contact with seller'),
                    'hint' => $this->l('If Yes, Visitors have to login as customer for contacting to a particular seller from profile and shop page.'),
                    'name' => 'WK_MP_CONTACT_SELLER_SETTINGS',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Customer can write a review or view seller rating and review'),
                    'hint' => $this->l('If Yes, Customer can give a review and can view ratings and reviews on seller profile page. Also customer can view rating on product page.'),
                    'name' => 'WK_MP_REVIEW_SETTINGS',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Display review in order'),
                    'hint' => $this->l('Review will display according to selected option on seller profile page.'),
                    'name' => 'WK_MP_REVIEW_DISPLAY_SORT',
                    'type' => 'select',
                    'options' => [
                        'query' => $listReviewShow,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'identifier' => 'id',
                    'form_group_class' => 'mp_review_settings',
                ],
                [
                    'label' => $this->l('Number of reviews on seller profile page'),
                    'hint' => $this->l('Given number of reviews will display on seller profile page after that view all button will be display.'),
                    'name' => 'WK_MP_REVIEW_DISPLAY_COUNT',
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                    'form_group_class' => 'mp_review_settings',
                    'required' => true,
                ],
                [
                    'label' => $this->l('Customer can give feedback on seller review'),
                    'hint' => $this->l('If Yes, Customer can give feedback on seller review that review is helpful or not.'),
                    'name' => 'WK_MP_REVIEW_HELPFUL_SETTINGS',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                    'form_group_class' => 'mp_review_settings',
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'submitCustomerSettings',
            ],
        ];

        $this->fields_value = [
            'WK_MP_CONTACT_SELLER_SETTINGS' => Tools::getValue('WK_MP_CONTACT_SELLER_SETTINGS', Configuration::get('WK_MP_CONTACT_SELLER_SETTINGS')),
            'WK_MP_REVIEW_SETTINGS' => Tools::getValue('WK_MP_REVIEW_SETTINGS', Configuration::get('WK_MP_REVIEW_SETTINGS')),
            'WK_MP_REVIEW_DISPLAY_SORT' => Tools::getValue('WK_MP_REVIEW_DISPLAY_SORT', Configuration::get('WK_MP_REVIEW_DISPLAY_SORT')),
            'WK_MP_REVIEW_DISPLAY_COUNT' => Tools::getValue('WK_MP_REVIEW_DISPLAY_COUNT', Configuration::get('WK_MP_REVIEW_DISPLAY_COUNT')),
            'WK_MP_REVIEW_HELPFUL_SETTINGS' => Tools::getValue('WK_MP_REVIEW_HELPFUL_SETTINGS', Configuration::get('WK_MP_REVIEW_HELPFUL_SETTINGS')),
        ];

        return parent::renderForm();
    }

    public function carrierSettings($wkSwitchOptions)
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Carrier settings'),
                'icon' => 'icon-exchange',
            ],
            'input' => [
                [
                    'label' => $this->l('Sellers can manage carriers'),
                    'hint' => $this->l('If Yes, Sellers can add, edit and delete carriers.'),
                    'name' => 'WK_MP_SELLER_SHIPPING',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Carriers need to be approved by admin'),
                    'hint' => $this->l('If No, carrier request will be automatically approved.'),
                    'name' => 'MP_SHIPPING_ADMIN_APPROVE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Allow shipping distribution'),
                    'hint' => $this->l('If Yes, Shipping distribution feature will be enabled.'),
                    'name' => 'WK_MP_SHIPPING_DISTRIBUTION_ALLOW',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Distribute shipping between admin and seller both'),
                    'hint' => $this->l('If Yes, Shipping will be distributed between admin and seller.'),
                    'desc' => $this->l('If admin product exists with any seller product in same order and that order carrier distribution is set as seller or both then shipping will be distributed between admin and seller on the basis of product price or weight.') .
                    $this->context->smarty->fetch(
                        _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/shipping_commission_link.tpl'
                    ),
                    'form_group_class' => 'mp_shipping_distribution',
                    'name' => 'WK_MP_SHIPPING_ADMIN_DISTRIBUTION',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'submitCarrierSettings',
            ],
        ];

        $this->fields_value = [
            'WK_MP_SELLER_SHIPPING' => Tools::getValue('WK_MP_SELLER_SHIPPING', Configuration::get('WK_MP_SELLER_SHIPPING')),
            'MP_SHIPPING_ADMIN_APPROVE' => Tools::getValue('MP_SHIPPING_ADMIN_APPROVE', Configuration::get('MP_SHIPPING_ADMIN_APPROVE')),
            'WK_MP_SHIPPING_DISTRIBUTION_ALLOW' => Tools::getValue('WK_MP_SHIPPING_DISTRIBUTION_ALLOW', Configuration::get('WK_MP_SHIPPING_DISTRIBUTION_ALLOW')),
            'WK_MP_SHIPPING_ADMIN_DISTRIBUTION' => Tools::getValue('WK_MP_SHIPPING_ADMIN_DISTRIBUTION', Configuration::get('WK_MP_SHIPPING_ADMIN_DISTRIBUTION')),
        ];

        return parent::renderForm();
    }

    public function orderSettings()
    {
        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        asort($statuses);
        foreach ($statuses as $key => $status) {
            $this->statuses_array[$key]['id_group'] = $status['id_order_state'];
            $this->statuses_array[$key]['name'] = $status['name'];
        }

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Order settings'),
                'icon' => 'icon-shopping-cart',
            ],
            'input' => [
                [
                    'type' => 'switch',
                    'label' => $this->l('Sellers can add tracking details on their order'),
                    'name' => 'WK_MP_SELLER_ORDER_TRACKING_ALLOW',
                    'required' => false,
                    'is_bool' => true,
                    'values' => [
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
                    ],
                    'hint' => $this->l('If Yes, Sellers can add tracking number and url on their order for sending mail to customer.'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Update tracking number on Prestashop order tracking number'),
                    'name' => 'WK_MP_TRACKING_PS_UPDATE_ALLOW',
                    'required' => false,
                    'form_group_class' => 'wk_mp_tracking_ps_update',
                    'is_bool' => true,
                    'values' => [
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
                    ],
                    'hint' => $this->l('If Yes, seller can update tracking number on prestashop order tracking number.'),
                    'desc' => $this->l('Tracking number will update only if that order has products of single seller.'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Display tracking details to customer'),
                    'name' => 'WK_MP_TRACKING_ORDER_HISTORY_ALLOW',
                    'required' => false,
                    'form_group_class' => 'wk_mp_tracking_ps_update',
                    'is_bool' => true,
                    'values' => [
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
                    ],
                    'hint' => $this->l('If Yes, tracking number and url will display to customer on order history.'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Display tracking number in tracking url'),
                    'name' => 'WK_MP_TRACKING_NUMBER_IN_URL',
                    'required' => false,
                    'form_group_class' => 'wk_mp_tracking_ps_update',
                    'is_bool' => true,
                    'values' => [
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
                    ],
                    'hint' => $this->l('If Yes, tracking number will display in place of @ in tracking url.'),
                ],
                [
                    'type' => 'switch',
                    'label' => $this->l('Sellers can change their order status'),
                    'name' => 'WK_MP_SELLER_ORDER_STATUS_CHANGE',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => [
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
                    ],
                    'hint' => $this->l('If Yes, Seller will able to change their order status for only their products.'),
                ],
                [
                    'type' => 'group',
                    'label' => $this->l('Order status'),
                    'name' => 'groupBoxStatus',
                    'values' => $this->statuses_array,
                    'form_group_class' => 'wk_mp_seller_order_status',
                    'hint' => $this->l('Select the order status that will be available to seller for changing their order status.'),
                    'required' => true,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'submitOrderSettings',
            ],
        ];

        $this->fields_value = [
            'WK_MP_SELLER_ORDER_TRACKING_ALLOW' => Tools::getValue(
                'WK_MP_SELLER_ORDER_TRACKING_ALLOW',
                Configuration::get('WK_MP_SELLER_ORDER_TRACKING_ALLOW')
            ),
            'WK_MP_TRACKING_PS_UPDATE_ALLOW' => Tools::getValue(
                'WK_MP_TRACKING_PS_UPDATE_ALLOW',
                Configuration::get('WK_MP_TRACKING_PS_UPDATE_ALLOW')
            ),
            'WK_MP_TRACKING_NUMBER_IN_URL' => Tools::getValue(
                'WK_MP_TRACKING_NUMBER_IN_URL',
                Configuration::get('WK_MP_TRACKING_NUMBER_IN_URL')
            ),
            'WK_MP_TRACKING_ORDER_HISTORY_ALLOW' => Tools::getValue(
                'WK_MP_TRACKING_ORDER_HISTORY_ALLOW',
                Configuration::get('WK_MP_TRACKING_ORDER_HISTORY_ALLOW')
            ),
            'WK_MP_SELLER_ORDER_STATUS_CHANGE' => Tools::getValue(
                'WK_MP_SELLER_ORDER_STATUS_CHANGE',
                Configuration::get('WK_MP_SELLER_ORDER_STATUS_CHANGE')
            ),
        ];

        if ($this->statuses_array) {
            $sellerOrderStatus = json_decode(Configuration::get('WK_MP_SELLER_ORDER_STATUS_ACCESS'));
            foreach ($this->statuses_array as $sellerOrderStatusVal) {
                if ($sellerOrderStatus && in_array($sellerOrderStatusVal['id_group'], $sellerOrderStatus)) {
                    $groupVal = 1;
                } else {
                    $groupVal = '';
                }

                $this->fields_value['groupBox_' . $sellerOrderStatusVal['id_group']] = $groupVal;
            }
        }

        return parent::renderForm();
    }

    public function mailSettings($wkSwitchOptions)
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Mail settings'),
                'icon' => 'icon-envelope',
            ],
            'input' => [
                [
                    'label' => $this->l('"From" title for seller mail'),
                    'name' => 'WK_MP_FROM_MAIL_TITLE',
                    'type' => 'text',
                    'class' => 'fixed-width-xxl',
                ],
                [
                    'label' => $this->l('Mail to admin on seller request'),
                    'name' => 'WK_MP_MAIL_ADMIN_SELLER_REQUEST',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Mail to seller on seller request approval or seller created by Admin'),
                    'name' => 'WK_MP_MAIL_SELLER_REQ_APPROVE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Mail to seller on seller disapproval'),
                    'name' => 'WK_MP_MAIL_SELLER_REQ_DISAPPROVE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Mail to seller when admin delete seller account'),
                    'name' => 'WK_MP_MAIL_SELLER_DELETE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Mail to admin when seller add new product'),
                    'name' => 'WK_MP_MAIL_ADMIN_PRODUCT_ADD',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Mail to seller on product approval'),
                    'name' => 'WK_MP_MAIL_SELLER_PRODUCT_APPROVE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Mail to seller on product disapproval'),
                    'name' => 'WK_MP_MAIL_SELLER_PRODUCT_DISAPPROVE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Mail to seller on product assign'),
                    'name' => 'WK_MP_MAIL_SELLER_PRODUCT_ASSIGN',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Mail to seller on product sold'),
                    'name' => 'WK_MP_MAIL_SELLER_PRODUCT_SOLD',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Mail to admin or seller on product delete'),
                    'hint' => $this->l('If admin delete product, mail will go to seller and if seller delete product, mail will go to admin.'),
                    'name' => 'WK_MP_MAIL_PRODUCT_DELETE',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Mail to admin when seller add new carrier'),
                    'name' => 'MP_MAIL_ADMIN_SHIPPING_ADDED',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
                [
                    'label' => $this->l('Mail to seller on carrier approval or disapproval'),
                    'name' => 'MP_MAIL_SELLER_SHIPPING_APPROVAL',
                    'type' => 'switch',
                    'required' => false,
                    'is_bool' => true,
                    'values' => $wkSwitchOptions,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'name' => 'submitMailSettings',
            ],
        ];

        $this->fields_value = [
            'WK_MP_FROM_MAIL_TITLE' => Tools::getValue('WK_MP_FROM_MAIL_TITLE', Configuration::get('WK_MP_FROM_MAIL_TITLE')),
            'WK_MP_MAIL_SELLER_REQ_APPROVE' => Tools::getValue('WK_MP_MAIL_SELLER_REQ_APPROVE', Configuration::get('WK_MP_MAIL_SELLER_REQ_APPROVE')),
            'WK_MP_MAIL_SELLER_REQ_DISAPPROVE' => Tools::getValue('WK_MP_MAIL_SELLER_REQ_DISAPPROVE', Configuration::get('WK_MP_MAIL_SELLER_REQ_DISAPPROVE')),
            'WK_MP_MAIL_SELLER_DELETE' => Tools::getValue('WK_MP_MAIL_SELLER_DELETE', Configuration::get('WK_MP_MAIL_SELLER_DELETE')),
            'WK_MP_MAIL_SELLER_PRODUCT_APPROVE' => Tools::getValue('WK_MP_MAIL_SELLER_PRODUCT_APPROVE', Configuration::get('WK_MP_MAIL_SELLER_PRODUCT_APPROVE')),
            'WK_MP_MAIL_SELLER_PRODUCT_DISAPPROVE' => Tools::getValue('WK_MP_MAIL_SELLER_PRODUCT_DISAPPROVE', Configuration::get('WK_MP_MAIL_SELLER_PRODUCT_DISAPPROVE')),
            'WK_MP_MAIL_SELLER_PRODUCT_ASSIGN' => Tools::getValue('WK_MP_MAIL_SELLER_PRODUCT_ASSIGN', Configuration::get('WK_MP_MAIL_SELLER_PRODUCT_ASSIGN')),
            'WK_MP_MAIL_SELLER_PRODUCT_SOLD' => Tools::getValue('WK_MP_MAIL_SELLER_PRODUCT_SOLD', Configuration::get('WK_MP_MAIL_SELLER_PRODUCT_SOLD')),
            'WK_MP_MAIL_PRODUCT_DELETE' => Tools::getValue('WK_MP_MAIL_PRODUCT_DELETE', Configuration::get('WK_MP_MAIL_PRODUCT_DELETE')),
            'WK_MP_MAIL_ADMIN_SELLER_REQUEST' => Tools::getValue('WK_MP_MAIL_ADMIN_SELLER_REQUEST', Configuration::get('WK_MP_MAIL_ADMIN_SELLER_REQUEST')),
            'WK_MP_MAIL_ADMIN_PRODUCT_ADD' => Tools::getValue('WK_MP_MAIL_ADMIN_PRODUCT_ADD', Configuration::get('WK_MP_MAIL_ADMIN_PRODUCT_ADD')),
            'MP_MAIL_ADMIN_SHIPPING_ADDED' => Tools::getValue('MP_MAIL_ADMIN_SHIPPING_ADDED', Configuration::get('MP_MAIL_ADMIN_SHIPPING_ADDED')),
            'MP_MAIL_SELLER_SHIPPING_APPROVAL' => Tools::getValue('MP_MAIL_SELLER_SHIPPING_APPROVAL', Configuration::get('MP_MAIL_SELLER_SHIPPING_APPROVAL')),
        ];

        return parent::renderForm();
    }

    public function saveSellerApprovalSettings($idShop = null)
    {
        Configuration::updateValue('WK_MP_SELLER_ADMIN_APPROVE', Tools::getValue('WK_MP_SELLER_ADMIN_APPROVE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_TERMS_AND_CONDITIONS_STATUS', Tools::getValue('WK_MP_TERMS_AND_CONDITIONS_STATUS'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_TERMS_AND_CONDITIONS_CMS', Tools::getValue('WK_MP_TERMS_AND_CONDITIONS_CMS'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PHONE_DIGIT', Tools::getValue('WK_MP_PHONE_DIGIT'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_REVIEWS_ADMIN_APPROVE', Tools::getValue('WK_MP_REVIEWS_ADMIN_APPROVE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_PROFILE_DEACTIVATE_REASON', Tools::getValue('WK_MP_SELLER_PROFILE_DEACTIVATE_REASON'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_SHOP_SETTINGS', Tools::getValue('WK_MP_SELLER_SHOP_SETTINGS'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_COUNTRY_NEED', Tools::getValue('WK_MP_SELLER_COUNTRY_NEED'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_FAX', Tools::getValue('WK_MP_SELLER_FAX'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_TAX_IDENTIFICATION_NUMBER', Tools::getValue('WK_MP_SELLER_TAX_IDENTIFICATION_NUMBER'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SHOW_ADMIN_DETAILS', Tools::getValue('WK_MP_SHOW_ADMIN_DETAILS'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRESTA_ATTRIBUTE_ACCESS', Tools::getValue('WK_MP_PRESTA_ATTRIBUTE_ACCESS'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRESTA_FEATURE_ACCESS', Tools::getValue('WK_MP_PRESTA_FEATURE_ACCESS'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SOCIAL_TABS', Tools::getValue('WK_MP_SOCIAL_TABS'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_FACEBOOK', Tools::getValue('WK_MP_SELLER_FACEBOOK'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_TWITTER', Tools::getValue('WK_MP_SELLER_TWITTER'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_YOUTUBE', Tools::getValue('WK_MP_SELLER_YOUTUBE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_INSTAGRAM', Tools::getValue('WK_MP_SELLER_INSTAGRAM'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_DETAILS_PERMISSION', Tools::getValue('WK_MP_SELLER_DETAILS_PERMISSION'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_EXPORT', Tools::getValue('WK_MP_SELLER_EXPORT'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SHOW_SELLER_DETAILS', Tools::getValue('WK_MP_SHOW_SELLER_DETAILS'), false, null, (int) $idShop);

        // save seller details access details
        $sellerDetailsAccess = Tools::getValue('groupBox');
        if ($sellerDetailsAccess) {
            Configuration::updateValue('WK_MP_SELLER_DETAILS_ACCESS', json_encode($sellerDetailsAccess), false, null, (int) $idShop);
        } else {
            Configuration::updateValue('WK_MP_SELLER_DETAILS_ACCESS', '', false, null, (int) $idShop);
            Configuration::updateValue('WK_MP_SHOW_SELLER_DETAILS', 0, false, null, (int) $idShop);
        }

        // If no social tab is active and disbled whole social tabs
        if (!Tools::getValue('WK_MP_SELLER_FACEBOOK')
        && !Tools::getValue('WK_MP_SELLER_TWITTER')
        && !Tools::getValue('WK_MP_SELLER_YOUTUBE')
        && !Tools::getValue('WK_MP_SELLER_INSTAGRAM')) {
            Configuration::updateValue('WK_MP_SOCIAL_TABS', 0, false, null, (int) $idShop);
        }
    }

    public function saveProductApprovalSettings($idShop = null)
    {
        Configuration::updateValue('WK_MP_PRODUCT_ADMIN_APPROVE', Tools::getValue('WK_MP_PRODUCT_ADMIN_APPROVE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_PRODUCTS_DEACTIVATE_REASON', Tools::getValue('WK_MP_SELLER_PRODUCTS_DEACTIVATE_REASON'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_PRODUCTS_SETTINGS', Tools::getValue('WK_MP_SELLER_PRODUCTS_SETTINGS'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_UPDATE_ADMIN_APPROVE', Tools::getValue('WK_MP_PRODUCT_UPDATE_ADMIN_APPROVE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SHOW_ADMIN_COMMISSION', Tools::getValue('WK_MP_SHOW_ADMIN_COMMISSION'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_APPLIED_TAX_RULE', Tools::getValue('WK_MP_SELLER_APPLIED_TAX_RULE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_PRODUCT_SEO', Tools::getValue('WK_MP_SELLER_PRODUCT_SEO'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_PRODUCT_VISIBILITY', Tools::getValue('WK_MP_SELLER_PRODUCT_VISIBILITY'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_PRODUCT_AVAILABILITY', Tools::getValue('WK_MP_SELLER_PRODUCT_AVAILABILITY'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_PRODUCT_REFERENCE', Tools::getValue('WK_MP_SELLER_PRODUCT_REFERENCE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_MPN', Tools::getValue('WK_MP_PRODUCT_MPN'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_PRODUCT_UPC', Tools::getValue('WK_MP_SELLER_PRODUCT_UPC'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_PRODUCT_EAN', Tools::getValue('WK_MP_SELLER_PRODUCT_EAN'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_PRODUCT_ISBN', Tools::getValue('WK_MP_SELLER_PRODUCT_ISBN'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_PRODUCT_COMBINATION', Tools::getValue('WK_MP_SELLER_PRODUCT_COMBINATION'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_COMBINATION_CUSTOMIZE', Tools::getValue('WK_MP_PRODUCT_COMBINATION_CUSTOMIZE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_ADMIN_SHIPPING', Tools::getValue('WK_MP_SELLER_ADMIN_SHIPPING'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_DELIVERY_TIME', Tools::getValue('WK_MP_PRODUCT_DELIVERY_TIME'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_ADDITIONAL_FEES', Tools::getValue('WK_MP_PRODUCT_ADDITIONAL_FEES'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_FEATURE', Tools::getValue('WK_MP_PRODUCT_FEATURE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_CONDITION', Tools::getValue('WK_MP_PRODUCT_CONDITION'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_MIN_QTY', Tools::getValue('WK_MP_PRODUCT_MIN_QTY'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_LOW_STOCK_ALERT', Tools::getValue('WK_MP_PRODUCT_LOW_STOCK_ALERT'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_WHOLESALE_PRICE', Tools::getValue('WK_MP_PRODUCT_WHOLESALE_PRICE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_PRICE_PER_UNIT', Tools::getValue('WK_MP_PRODUCT_PRICE_PER_UNIT'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_ON_SALE', Tools::getValue('WK_MP_PRODUCT_ON_SALE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_ALLOW_DUPLICATE', Tools::getValue('WK_MP_PRODUCT_ALLOW_DUPLICATE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_DUPLICATE_QUANTITY', Tools::getValue('WK_MP_PRODUCT_DUPLICATE_QUANTITY'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_STOCK_LOCATION', Tools::getValue('WK_MP_PRODUCT_STOCK_LOCATION'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_PAGE_REDIRECTION', Tools::getValue('WK_MP_PRODUCT_PAGE_REDIRECTION'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_IMAGE_CAPTION', Tools::getValue('WK_MP_PRODUCT_IMAGE_CAPTION'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_RELATED_PRODUCT', Tools::getValue('WK_MP_RELATED_PRODUCT'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_TAGS', Tools::getValue('WK_MP_PRODUCT_TAGS'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_CUSTOMIZATION', Tools::getValue('WK_MP_PRODUCT_CUSTOMIZATION'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_ATTACHMENT', Tools::getValue('WK_MP_PRODUCT_ATTACHMENT'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_SPECIFIC_RULE', Tools::getValue('WK_MP_PRODUCT_SPECIFIC_RULE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PACK_PRODUCTS', Tools::getValue('WK_MP_PACK_PRODUCTS'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_VIRTUAL_PRODUCT', Tools::getValue('WK_MP_VIRTUAL_PRODUCT'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_MANUFACTURER', Tools::getValue('WK_MP_PRODUCT_MANUFACTURER'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_MANUFACTURER_ADMIN', Tools::getValue('WK_MP_PRODUCT_MANUFACTURER_ADMIN'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_MANUFACTURER_APPROVED', Tools::getValue('WK_MP_PRODUCT_MANUFACTURER_APPROVED'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_SUPPLIER', Tools::getValue('WK_MP_PRODUCT_SUPPLIER'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_SUPPLIER_ADMIN', Tools::getValue('WK_MP_PRODUCT_SUPPLIER_ADMIN'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_SUPPLIER_APPROVED', Tools::getValue('WK_MP_PRODUCT_SUPPLIER_APPROVED'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_PRODUCT_CATEGORY_RESTRICTION', Tools::getValue('WK_MP_PRODUCT_CATEGORY_RESTRICTION'), false, null, (int) $idShop);

        $wkMpDuplicateTitle = [];
        foreach (Language::getLanguages(false) as $language) {
            // Terms and condition content
            if (Tools::getValue('WK_MP_PRODUCT_DUPLICATE_TITLE_' . $language['id_lang'])) {
                $wkMpDuplicateTitle[$language['id_lang']] = Tools::getValue('WK_MP_PRODUCT_DUPLICATE_TITLE_' . $language['id_lang']);
            } else {
                $wkMpDuplicateTitle[$language['id_lang']] = Tools::getValue('WK_MP_PRODUCT_DUPLICATE_TITLE_' . Configuration::get('PS_LANG_DEFAULT'));
            }
        }

        if ($wkMpDuplicateTitle) {
            Configuration::updateValue('WK_MP_PRODUCT_DUPLICATE_TITLE', $wkMpDuplicateTitle, true, null, (int) $idShop);
        }
    }

    public function saveCustomerSettings($idShop = null)
    {
        Configuration::updateValue('WK_MP_CONTACT_SELLER_SETTINGS', Tools::getValue('WK_MP_CONTACT_SELLER_SETTINGS'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_REVIEW_SETTINGS', Tools::getValue('WK_MP_REVIEW_SETTINGS'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_REVIEW_DISPLAY_SORT', Tools::getValue('WK_MP_REVIEW_DISPLAY_SORT'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_REVIEW_DISPLAY_COUNT', Tools::getValue('WK_MP_REVIEW_DISPLAY_COUNT'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_REVIEW_HELPFUL_SETTINGS', Tools::getValue('WK_MP_REVIEW_HELPFUL_SETTINGS'), false, null, (int) $idShop);
    }

    public function saveCarrierSettings($idShop = null)
    {
        Configuration::updateValue('WK_MP_SELLER_SHIPPING', Tools::getValue('WK_MP_SELLER_SHIPPING'), false, null, (int) $idShop);
        Configuration::updateValue('MP_SHIPPING_ADMIN_APPROVE', Tools::getValue('MP_SHIPPING_ADMIN_APPROVE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SHIPPING_DISTRIBUTION_ALLOW', Tools::getValue('WK_MP_SHIPPING_DISTRIBUTION_ALLOW'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SHIPPING_ADMIN_DISTRIBUTION', Tools::getValue('WK_MP_SHIPPING_ADMIN_DISTRIBUTION'), false, null, (int) $idShop);
    }

    public function saveOrderSettings($idShop = null)
    {
        Configuration::updateValue('WK_MP_SELLER_ORDER_TRACKING_ALLOW', Tools::getValue('WK_MP_SELLER_ORDER_TRACKING_ALLOW'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_TRACKING_PS_UPDATE_ALLOW', Tools::getValue('WK_MP_TRACKING_PS_UPDATE_ALLOW'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_TRACKING_NUMBER_IN_URL', Tools::getValue('WK_MP_TRACKING_NUMBER_IN_URL'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_TRACKING_ORDER_HISTORY_ALLOW', Tools::getValue('WK_MP_TRACKING_ORDER_HISTORY_ALLOW'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_SELLER_ORDER_STATUS_CHANGE', Tools::getValue('WK_MP_SELLER_ORDER_STATUS_CHANGE'), false, null, (int) $idShop);

        $sellerOrderStatus = Tools::getValue('groupBox');
        if ($sellerOrderStatus) {
            Configuration::updateValue('WK_MP_SELLER_ORDER_STATUS_ACCESS', json_encode($sellerOrderStatus), false, null, (int) $idShop);
        } else {
            Configuration::updateValue('WK_MP_SELLER_ORDER_STATUS_ACCESS', '', false, null, (int) $idShop);
            Configuration::updateValue('WK_MP_SELLER_ORDER_STATUS_CHANGE', 0, false, null, (int) $idShop);
        }
    }

    public function saveMailSettings($idShop = null)
    {
        Configuration::updateValue('WK_MP_FROM_MAIL_TITLE', Tools::getValue('WK_MP_FROM_MAIL_TITLE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_MAIL_SELLER_REQ_APPROVE', Tools::getValue('WK_MP_MAIL_SELLER_REQ_APPROVE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_MAIL_SELLER_REQ_DISAPPROVE', Tools::getValue('WK_MP_MAIL_SELLER_REQ_DISAPPROVE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_MAIL_SELLER_DELETE', Tools::getValue('WK_MP_MAIL_SELLER_DELETE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_MAIL_SELLER_PRODUCT_APPROVE', Tools::getValue('WK_MP_MAIL_SELLER_PRODUCT_APPROVE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_MAIL_SELLER_PRODUCT_DISAPPROVE', Tools::getValue('WK_MP_MAIL_SELLER_PRODUCT_DISAPPROVE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_MAIL_SELLER_PRODUCT_ASSIGN', Tools::getValue('WK_MP_MAIL_SELLER_PRODUCT_ASSIGN'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_MAIL_SELLER_PRODUCT_SOLD', Tools::getValue('WK_MP_MAIL_SELLER_PRODUCT_SOLD'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_MAIL_PRODUCT_DELETE', Tools::getValue('WK_MP_MAIL_PRODUCT_DELETE'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_MAIL_ADMIN_SELLER_REQUEST', Tools::getValue('WK_MP_MAIL_ADMIN_SELLER_REQUEST'), false, null, (int) $idShop);
        Configuration::updateValue('WK_MP_MAIL_ADMIN_PRODUCT_ADD', Tools::getValue('WK_MP_MAIL_ADMIN_PRODUCT_ADD'), false, null, (int) $idShop);
        Configuration::updateValue('MP_MAIL_ADMIN_SHIPPING_ADDED', Tools::getValue('MP_MAIL_ADMIN_SHIPPING_ADDED'), false, null, (int) $idShop);
        Configuration::updateValue('MP_MAIL_SELLER_SHIPPING_APPROVAL', Tools::getValue('MP_MAIL_SELLER_SHIPPING_APPROVAL'), false, null, (int) $idShop);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitSellerApprovalSettings')) {
            if (Tools::getValue('WK_MP_TERMS_AND_CONDITIONS_STATUS')) {
                if (!Tools::getValue('WK_MP_TERMS_AND_CONDITIONS_CMS')) {
                    $this->errors[] = $this->l('Choose atleast one CMS page');
                }
            }
            if (trim(Tools::getValue('WK_MP_PHONE_DIGIT')) == 0) {
                $this->errors[] = $this->l('Seller phone maximum digit is required.');
            } elseif (!Validate::isUnsignedInt(Tools::getValue('WK_MP_PHONE_DIGIT'))) {
                $this->errors[] = $this->l('Seller phone maximum digit is invalid.');
            }

            $sellerDetailsAccess = Tools::getValue('groupBox');
            if (Tools::getValue('WK_MP_SHOW_SELLER_DETAILS') && !$sellerDetailsAccess) {
                $this->errors[] = $this->l('Please select atleast one customize detail to be displayed.');
            }

            if (empty($this->errors)) {
                if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
                    // In case of All Shops
                    $this->saveSellerApprovalSettings();
                    foreach (Shop::getShops() as $shop) {
                        $this->saveSellerApprovalSettings((int) $shop['id_shop']);
                    }
                } else {
                    $this->saveSellerApprovalSettings($this->context->shop->id);
                }

                Tools::redirectAdmin(
                    self::$currentIndex . '&conf=6&current_config_tab=seller_approval_settings&token=' . $this->token
                );
            } else {
                $this->current_config_tab = 'seller_approval_settings';
                Media::addJsDef([
                    'current_config_tab' => $this->current_config_tab,
                ]);
            }
        } elseif (Tools::isSubmit('submitProductApprovalSettings')) {
            foreach (Language::getLanguages(false) as $language) {
                if (Tools::getValue('WK_MP_PRODUCT_DUPLICATE_TITLE_' . $language['id_lang'])) {
                    if (!Validate::isCatalogName(Tools::getValue('WK_MP_PRODUCT_DUPLICATE_TITLE_' . $language['id_lang']))
                    ) {
                        $this->errors[] = $this->l('Prefix title for duplicate product name is invalid in ') . $language['name'];
                    }
                }
            }

            if (empty($this->errors)) {
                if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
                    // In case of All Shops
                    $this->saveProductApprovalSettings();
                    foreach (Shop::getShops() as $shop) {
                        $this->saveProductApprovalSettings((int) $shop['id_shop']);
                    }
                } else {
                    $this->saveProductApprovalSettings($this->context->shop->id);
                }

                Tools::redirectAdmin(
                    self::$currentIndex . '&conf=6&current_config_tab=product_approval_settings&token=' . $this->token
                );
            } else {
                $this->current_config_tab = 'product_approval_settings';
                Media::addJsDef([
                    'current_config_tab' => $this->current_config_tab,
                ]);
            }
        } elseif (Tools::isSubmit('submitCustomerSettings')) {
            if (!Tools::getValue('WK_MP_REVIEW_DISPLAY_COUNT')) {
                $this->errors[] = $this->l('Number of reviews in customer settings is required field.');
            } else {
                if (!Validate::isUnsignedInt(Tools::getValue('WK_MP_REVIEW_DISPLAY_COUNT'))) {
                    $this->errors[] = $this->l('Number of reviews in customer settings must be valid.');
                }
            }

            if (empty($this->errors)) {
                if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
                    // In case of All Shops
                    $this->saveCustomerSettings();
                    foreach (Shop::getShops() as $shop) {
                        $this->saveCustomerSettings((int) $shop['id_shop']);
                    }
                } else {
                    $this->saveCustomerSettings($this->context->shop->id);
                }

                Tools::redirectAdmin(
                    self::$currentIndex . '&conf=6&current_config_tab=customer_settings&token=' . $this->token
                );
            } else {
                $this->current_config_tab = 'customer_settings';
                Media::addJsDef([
                    'current_config_tab' => $this->current_config_tab,
                ]);
            }
        } elseif (Tools::isSubmit('submitCarrierSettings')) {
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
                // In case of All Shops
                $this->saveCarrierSettings();
                foreach (Shop::getShops() as $shop) {
                    $this->saveCarrierSettings((int) $shop['id_shop']);
                }
            } else {
                $this->saveCarrierSettings($this->context->shop->id);
            }

            Tools::redirectAdmin(
                self::$currentIndex . '&conf=6&current_config_tab=carrier_settings&token=' . $this->token
            );
        } elseif (Tools::isSubmit('submitOrderSettings')) {
            // save order settings
            $sellerOrderStatus = Tools::getValue('groupBox');
            if (Tools::getValue('WK_MP_SELLER_ORDER_STATUS_CHANGE') && !$sellerOrderStatus) {
                $this->errors[] = $this->l('Please select atleast one order status.');
            }
            if (empty($this->errors)) {
                if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
                    // In case of All Shops
                    $this->saveOrderSettings();
                    foreach (Shop::getShops() as $shop) {
                        $this->saveOrderSettings((int) $shop['id_shop']);
                    }
                } else {
                    $this->saveOrderSettings($this->context->shop->id);
                }

                Tools::redirectAdmin(
                    self::$currentIndex . '&conf=4&current_config_tab=order_settings&token=' . $this->token
                );
            } else {
                $this->current_config_tab = 'order_settings';
                Media::addJsDef([
                    'current_config_tab' => $this->current_config_tab,
                ]);
            }
        } elseif (Tools::isSubmit('submitMailSettings')) {
            if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
                // In case of All Shops
                $this->saveMailSettings();
                foreach (Shop::getShops() as $shop) {
                    $this->saveMailSettings((int) $shop['id_shop']);
                }
            } else {
                $this->saveMailSettings($this->context->shop->id);
            }

            Tools::redirectAdmin(
                self::$currentIndex . '&conf=6&current_config_tab=mail_settings&token=' . $this->token
            );
        }

        if (Tools::isSubmit('submit_admin_default_shipping')) {
            if (!Tools::getValue('default_shipping')) {
                $this->errors[] = $this->l('Choose atleast one shipping method.');
            }
            if (!count($this->errors)) {
                $adminDefShipping = json_encode(Tools::getValue('default_shipping'));
                if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
                    // In case of All Shops
                    Configuration::updateValue(
                        'MP_SHIPPING_ADMIN_DEFAULT',
                        $adminDefShipping,
                        false,
                        null,
                        null
                    );
                    foreach (Shop::getShops() as $shop) {
                        Configuration::updateValue(
                            'MP_SHIPPING_ADMIN_DEFAULT',
                            $adminDefShipping,
                            false,
                            null,
                            (int) $shop['id_shop']
                        );
                    }
                } else {
                    Configuration::updateValue(
                        'MP_SHIPPING_ADMIN_DEFAULT',
                        $adminDefShipping,
                        false,
                        null,
                        (int) $this->context->shop->id
                    );
                }

                // Assign new selected shipping methods to the seller products which have no seller shipping methods
                $objMpShippingMet = new WkMpSellerShipping();
                $objMpShippingMet->updateCarriersOnDeactivateOrDelete();

                Tools::redirectAdmin(
                    self::$currentIndex . '&conf=6&current_config_tab=carrier_settings&token=' . $this->token
                );
            }
        }

        parent::postProcess();
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

        $objShipMap = new WkMpSellerShipping();
        if ($psProdInfo = Product::getProducts($idLang, $start, $limit, $orderBy, $orderWay, false, true)) {
            foreach ($psProdInfo as $product) {
                if (!$objShipMap->checkMpProduct($product['id_product'])) {
                    $objShipMap->setProductCarrier($product['id_product'], $carrRef);
                }
            }
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        Media::addJsDef([
            'module_dir' => _MODULE_DIR_,
            'current_config_tab' => $this->current_config_tab,
            'ajaxurl_approval_settings_url' => $this->context->link->getAdminLink('AdminMarketplaceApprovalSettings'),
            'moduleAdminLink' => $this->context->link->getAdminLink('AdminMarketplaceApprovalSettings'),
        ]);
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/mp_admin_config.js');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/vue.min.js');
        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/adminconfig.css');
    }
}
