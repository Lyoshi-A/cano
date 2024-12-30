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

class AdminPaymentModeController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->table = 'wk_mp_payment_mode';
        $this->className = 'WkMpSellerPaymentMode';
        $this->identifier = 'id_mp_payment';
        parent::__construct();
        $this->toolbar_title = $this->l('Payment Modes');

        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            // In case of All Shops
            $this->_select = 'shp.`name` as wk_ps_shop_name';
            $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'shop` shp ON (shp.`id_shop` = a.`id_shop`)';
        } else {
            $this->_where .= ' AND a.`id_shop` = ' . (int) $this->context->shop->id;
        }

        $this->fields_list = [
            'id_mp_payment' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'payment_mode' => [
                'title' => $this->l('Payment mode'),
                'width' => '100',
            ],
        ];
        if (WkMpHelper::isMultiShopEnabled()) {
            if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
                // In case of All Shops
                $this->fields_list['wk_ps_shop_name'] = [
                    'title' => $this->l('Shop'),
                    'havingFilter' => true,
                ];
            }
        }
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ],
        ];
    }

    public function initToolbar()
    {
        parent::initToolbar();
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $this->page_header_toolbar_btn['new'] = [
                'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                'desc' => $this->l('Add payment mode'),
            ];
        } else {
            $this->informations[] = $this->l('You can not add/edit in this shop context. Select a shop instead of all shop or group of shops.');
            unset($this->toolbar_btn['new']);
        }
    }

    public function renderList()
    {
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $this->addRowAction('edit');
        }
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {
        if ((Shop::getContext() !== Shop::CONTEXT_SHOP) && (Shop::getContext() !== Shop::CONTEXT_ALL)) {
            // For shop group
            $this->errors[] = $this->l('You can not add or edit a payment mode in this shop context: select a shop instead of a group of shops.');
        } else {
            $this->fields_form = [
                'legend' => [
                    'title' => $this->l('Manage Payment Mode'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'name' => 'payment_mode',
                        'label' => $this->l('Payment mode'),
                        'required' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submitPaymentMode',
                ],
            ];
        }

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (empty($this->display)) {
            parent::postProcess();
        }

        if (Tools::isSubmit('submitPaymentMode')) {
            $idPaymentMode = Tools::getValue('id_mp_payment');
            $wkPaymentMode = trim(Tools::getValue('payment_mode'));
            if ($wkPaymentMode == '') {
                $this->errors[] = $this->l('Payment mode is required.');
            } elseif (!Validate::isCatalogName($wkPaymentMode) || (strpos($wkPaymentMode, "'") !== false)) {
                $this->errors[] = $this->l('Payment mode is invalid.');
            }
            if (empty($this->errors)) {
                if ($idPaymentMode) {
                    $objPaymentMode = new WkMpSellerPaymentMode((int) $idPaymentMode);
                } else {
                    $objPaymentMode = new WkMpSellerPaymentMode();
                }
                $objPaymentMode->id_shop = (int) $this->context->shop->id;
                $objPaymentMode->payment_mode = pSQL($wkPaymentMode);
                $objPaymentMode->save();

                if ($idPaymentMode) {
                    Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
                } else {
                    Tools::redirectAdmin(self::$currentIndex . '&conf=3&token=' . $this->token);
                }
            } else {
                if ($idPaymentMode) {
                    $this->display = 'edit';
                } else {
                    $this->display = 'add';
                }
            }
        }
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/mphelper.js');
    }
}
