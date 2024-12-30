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

class AdminSellerOrdersController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        parent::__construct();
        $this->toolbar_title = $this->l('Seller Orders');
        $this->identifier = 'id_mp_order';

        $this->bootstrap = true;
        $this->list_no_link = true;
        $this->table = 'wk_mp_seller_order';
        $this->className = 'WkMpSellerOrder';

        if (!Tools::getValue('mp_seller_details')
            && !Tools::getValue('mp_order_details')
            && !Tools::getValue('mp_shipping_detail')
            && !Tools::getValue('mp_seller_settlement')) {
            // unset the filter if first renderlist contain any filteration
            if (!Tools::isSubmit('wk_mp_seller_orderOrderway')) {
                unset($this->context->cookie->sellerorderswk_mp_seller_orderOrderby);
                unset($this->context->cookie->sellerorderswk_mp_seller_orderOrderway);
            }

            $this->_select = '
                a.`id_mp_order` as `temp_id1`,
                a.`seller_customer_id` as `temp_shipping1`,
                CONCAT(a.`seller_firstname`," ",a.`seller_lastname`) as seller_name,
                a.`seller_email` as email';
            $this->_where = WkMpSellerOrder::addSqlRestriction('a');
            $this->_orderBy = 'id_mp_order';
            if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
                // In case of All Shops
                $this->_select .= ',shp.`name` as wk_ps_shop_name';
                $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'shop` shp ON (shp.`id_shop` = a.`id_shop`)';
            }

            $this->fields_list = [
                'id_mp_order' => [
                    'title' => $this->l('ID'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                ],
                'seller_shop' => [
                    'title' => $this->l('Unique shop name'),
                    'align' => 'center',
                    'havingFilter' => true,
                ],
                'seller_name' => [
                    'title' => $this->l('Seller name'),
                    'align' => 'center',
                    'havingFilter' => true,
                ],
                'email' => [
                    'title' => $this->l('Seller email'),
                    'align' => 'center',
                    'havingFilter' => true,
                ],
                'count_values' => [
                    'title' => $this->l('Total orders'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                    'orderby' => false,
                    'search' => false,
                ],
            ];

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
            if (Configuration::get('WK_MP_COMMISSION_DISTRIBUTE_ON') == 1) {
                $this->fields_list['pending_count_values'] = [
                    'title' => $this->l('Pending orders'),
                    'align' => 'center',
                    'class' => 'fixed-width-xs',
                    'orderby' => false,
                    'search' => false,
                    'badge_danger' => true,
                    'hint' => $this->l('Number of orders whose payment is pending.'),
                ];
            }

            $this->fields_list['temp_id1'] = [
                'title' => $this->l('Order details'),
                'align' => 'center',
                'search' => false,
                'hint' => $this->l('View product-wise seller order details'),
                'callback' => 'viewDetailBtn',
            ];

            $this->fields_list['temp_shipping1'] = [
                'title' => $this->l('Seller shipping'),
                'align' => 'center',
                'search' => false,
                'hint' => $this->l('View seller shipping earning details'),
                'callback' => 'viewSellerShippingBtn',
            ];
        }

        $this->_conf['1'] = $this->l('Seller amount settled successfully.');
        $this->_conf['2'] = $this->l('Seller settled amount cancelled successfully.');
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function viewDetailBtn($id, $arr)
    {
        if ($id) {
            $this->context->smarty->assign([
                'callback' => 'viewDetailBtn',
                'sellerOrderCurrentIndex' => self::$currentIndex,
                'sellerOrderToken' => $this->token,
                'sellerCustomerId' => $arr['seller_customer_id'],
            ]);

            return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/wk_admin_callback.tpl');
        }
    }

    public function viewSellerShippingBtn($id)
    {
        if ($id) {
            $this->context->smarty->assign([
                'callback' => 'viewSellerShippingBtn',
                'sellerOrderCurrentIndex' => self::$currentIndex,
                'sellerOrderToken' => $this->token,
                'sellerCustomerId' => $id,
            ]);

            return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/wk_admin_callback.tpl');
        }
    }

    public function viewOrderDetail($val, $arr)
    {
        if ($val) {
            if (Tools::getValue('mp_seller_details')) {
                $val = $arr['id_order'];
            }
            $orderLink = $this->context->link->getAdminLink('AdminOrders') . '&id_order=' . $val . '&vieworder';

            $this->context->smarty->assign([
                'callback' => 'viewOrderDetail',
                'orderLink' => $orderLink,
            ]);

            return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/wk_admin_callback.tpl');
        }
    }

    public function initSellerDetail()
    {
        $idCustomerSeller = Tools::getValue('id_customer_seller');
        $this->fields_list = [
            'id_order' => [
                'title' => $this->l('Id order'),
                'align' => 'text-center',
                'havingFilter' => true,
                'class' => 'fixed-width-xs',
            ],
            'customer' => [
                'title' => $this->l('Customer'),
                'align' => 'center',
                'havingFilter' => false,
                'search' => false,
            ],
            'price_ti' => [
                'title' => $this->l('Total'),
                'align' => 'center',
                'type' => 'decimal',
                'hint' => $this->l('Total product price tax included'),
                'currency' => true,
                'callback' => 'setOrderCurrency',
            ],
            'admin_commission' => [
                'title' => $this->l('Admin commission'),
                'align' => 'center',
                'type' => 'decimal',
                'currency' => true,
                'callback' => 'setOrderCurrency',
            ],
            'admin_tax' => [
                'title' => $this->l('Admin tax'),
                'align' => 'center',
                'type' => 'decimal',
                'currency' => true,
                'callback' => 'setOrderCurrency',
            ],
            'seller_amount' => [
                'title' => $this->l('Seller amount'),
                'align' => 'center',
                'type' => 'decimal',
                'currency' => true,
                'callback' => 'setOrderCurrency',
            ],
            'seller_tax' => [
                'title' => $this->l('Seller tax'),
                'align' => 'center',
                'currency' => true,
                'callback' => 'setOrderCurrency',
            ],
        ];

        $statuses = OrderState::getOrderStates((int) $this->context->language->id);
        foreach ($statuses as $status) {
            $this->statuses_array[$status['id_order_state']] = $status['name'];
        }
        $this->fields_list['osname'] = [
            'title' => $this->l('Status'),
            'type' => 'select',
            'color' => 'color',
            'list' => $this->statuses_array,
            'filter_key' => 'os!id_order_state',
            'filter_type' => 'int',
            'order_key' => 'osname',
            'hint' => $this->l('Order payment status'),
        ];

        $this->fields_list['date_add'] = [
            'title' => $this->l('Date'),
            'type' => 'datetime',
            'align' => 'center',
            'havingFilter' => true,
        ];
        $this->addRowAction('view');

        self::$currentIndex = self::$currentIndex
        . '&mp_seller_details=1&viewwk_mp_seller_order&id_customer_seller=' . (int) $idCustomerSeller;

        $this->context->smarty->assign([
            'current' => self::$currentIndex,
        ]);
        $this->toolbar_btn['export'] = [
            'href' => self::$currentIndex . '&export' . $this->table . '&token=' . $this->token,
            'desc' => $this->trans('Export'),
        ];
    }

    public function processFilter()
    {
        parent::processFilter();

        $prefix = $this->getCookieFilterPrefix();

        if (isset($this->list_id)) {
            foreach ($_POST as $key => $value) {
                if ($value === '') {
                    unset($this->context->cookie->{$prefix . $key});
                } elseif (stripos($key, $this->list_id . 'Filter_') === 0) {
                    $this->context->cookie->{$prefix . $key} = !is_array($value) ? $value : json_encode($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : json_encode($value);
                }
            }

            foreach ($_GET as $key => $value) {
                if (stripos($key, $this->list_id . 'Filter_') === 0) {
                    $this->context->cookie->{$prefix . $key} = !is_array($value) ? $value : json_encode($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : json_encode($value);
                }
                if (stripos($key, $this->list_id . 'Orderby') === 0 && Validate::isOrderBy($value)) {
                    if ($value === '' || $value == $this->_defaultOrderBy) {
                        unset($this->context->cookie->{$prefix . $key});
                    } else {
                        $this->context->cookie->{$prefix . $key} = $value;
                    }
                } elseif (stripos($key, $this->list_id . 'Orderway') === 0 && Validate::isOrderWay($value)) {
                    if ($value === '' || $value == $this->_defaultOrderWay) {
                        unset($this->context->cookie->{$prefix . $key});
                    } else {
                        $this->context->cookie->{$prefix . $key} = $value;
                    }
                }
            }
        }

        $filters = $this->context->cookie->getFamily($prefix . $this->list_id . 'Filter_');
        $definition = false;
        if (isset($this->className) && $this->className) {
            $definition = ObjectModel::getDefinition($this->className);
        }

        foreach ($filters as $key => $value) {
            /* Extracting filters from $_POST on key filter_ */
            if ($value != null && !strncmp($key, $prefix . $this->list_id . 'Filter_', 7 + Tools::strlen($prefix . $this->list_id))) {
                $key = Tools::substr($key, 7 + Tools::strlen($prefix . $this->list_id));
                /* Table alias could be specified using a ! eg. alias!field */
                $tmp_tab = explode('!', $key);
                $filter = count($tmp_tab) > 1 ? $tmp_tab[1] : $tmp_tab[0];

                if ($field = $this->filterToField($key, $filter)) {
                    $type = (array_key_exists('filter_type', $field) ? $field['filter_type'] : (array_key_exists('type', $field) ? $field['type'] : false));
                    if (($type == 'date' || $type == 'datetime') && is_string($value)) {
                        $value = json_decode($value, true);
                    }
                    $key = isset($tmp_tab[1]) ? $tmp_tab[0] . '.`' . $tmp_tab[1] . '`' : '`' . $tmp_tab[0] . '`';

                    // Assignment by reference
                    if (array_key_exists('tmpTableFilter', $field)) {
                        $sql_filter = &$this->_tmpTableFilter;
                    } elseif (array_key_exists('havingFilter', $field)) {
                        $sql_filter = &$this->_filterHaving;
                    } else {
                        $sql_filter = &$this->_filter;
                    }

                    /* Only for date filtering (from, to) */
                    if (is_array($value)) {
                        if (isset($value[0]) && !empty($value[0])) {
                            if (!Validate::isDate($value[0])) {
                                $this->errors[] = Tools::displayError('The \'From\' date format is invalid (YYYY-MM-DD)');
                            } else {
                                $sql_filter .= ' AND ' . pSQL($key) . ' >= \'' . pSQL(Tools::dateFrom($value[0])) . '\'';
                            }
                        }

                        if (isset($value[1]) && !empty($value[1])) {
                            if (!Validate::isDate($value[1])) {
                                $this->errors[] = Tools::displayError('The \'To\' date format is invalid (YYYY-MM-DD)');
                            } else {
                                $sql_filter .= ' AND ' . pSQL($key) . ' <= \'' . pSQL(Tools::dateTo($value[1])) . '\'';
                            }
                        }
                    } else {
                        $sql_filter .= ' AND ';
                        $check_key = ($key == $this->identifier || $key == '`' . $this->identifier . '`');
                        $alias = ($definition && !empty($definition['fields'][$filter]['shop'])) ? 'sa' : 'a';

                        if ($type == 'int' || $type == 'bool') {
                            $sql_filter .= (($check_key || $key == '`active`') ? $alias . '.' : '') . pSQL($key) . ' = ' . (int) $value . ' ';
                        } elseif ($type == 'decimal') {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = ' . (float) $value . ' ';
                        } elseif ($type == 'select') {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = \'' . pSQL($value) . '\' ';
                        } elseif ($type == 'price') {
                            $value = str_replace($this->context->currency->sign, '', $value);
                            $value = str_replace(',', '.', $value);
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = ' . pSQL(trim($value)) . ' ';
                            if ($key == '`seller_amount`') {
                                $sql_filter = 'AND `seller_amount` like "%' . $value . '%"';
                            } elseif ($key == '`seller_tax`') {
                                $sql_filter = 'AND `seller_tax` like "%' . $value . '%"';
                            } elseif ($key == '`admin_commission`') {
                                $sql_filter = 'AND `admin_commission` like "%' . $value . '%"';
                            } elseif ($key == '`admin_tax`') {
                                $sql_filter = 'AND `admin_tax` like "%' . $value . '%"';
                            } elseif ($key == '`price_ti`') {
                                $sql_filter = 'AND `price_ti` like "%' . $value . '%"';
                            } elseif ($key == '`seller_earn`') {
                                $sql_filter = 'AND `seller_earn` like "%' . $value . '%"';
                            }
                        } else {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' LIKE \'%' . pSQL(trim($value)) . '%\' ';
                        }
                    }
                }
            }
        }
    }

    // Override View Link For initSellerDetail() function
    public function displayViewLink($token = null, $id, $name = null)
    {
        $objWkMpSellerOrderDetail = new WkMpSellerOrderDetail($id);
        $this->context->smarty->assign([
            'id_order' => $objWkMpSellerOrderDetail->id_order,
            'seller_customer_id' => $objWkMpSellerOrderDetail->seller_customer_id,
        ]);

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . 'marketplace/views/templates/admin/mp_order_detail_view.tpl'
        );
    }

    public function initShippingList()
    {
        if ($idCustomerSeller = Tools::getValue('seller_id_customer')) {
            $this->fields_list = [
                'order_id' => [
                    'title' => $this->l('Order ID'),
                    'align' => 'center',
                ],
                'order_reference' => [
                    'title' => $this->l('Order reference'),
                    'align' => 'center',
                ],
                'seller_earn' => [
                    'title' => $this->l('Seller shipping earning'),
                    'align' => 'center',
                    'type' => 'price',
                    'currency' => true,
                    'callback' => 'setOrderCurrency',
                ],
                'order_date' => [
                    'title' => $this->l('Order date'),
                    'type' => 'datetime',
                    'align' => 'center',
                    'havingFilter' => true,
                ],
            ];

            self::$currentIndex = self::$currentIndex . '&mp_shipping_detail=1&viewwk_mp_seller_order&seller_id_customer=' . (int) $idCustomerSeller;
        }

        $this->context->smarty->assign([
            'current' => self::$currentIndex,
            'shippingDetail' => 1,
        ]);
    }

    public function renderView()
    {
        $this->context->smarty->assign('noListHeader', 1);
        $idCustomerSeller = Tools::getValue('id_customer_seller');
        if ($idCustomerSeller) {
            $sellerRecord = WkMpSellerOrder::getSellerRecord($idCustomerSeller);
            if ($sellerRecord && Tools::getValue('mp_seller_details')) {
                // unset the filter if first renderlist contain any filteration
                if (!Tools::isSubmit('wk_mp_seller_orderOrderway')) {
                    unset($this->context->cookie->sellerorderswk_mp_seller_orderOrderby);
                    unset($this->context->cookie->sellerorderswk_mp_seller_orderOrderway);
                }

                $this->list_no_link = true;
                $this->table = 'wk_mp_seller_order_detail';
                $this->className = 'WkMpSellerOrderDetail';
                $this->identifier = 'id_mp_order_detail';

                $this->_select = '
                    os.`color`,
                    osl.`name` AS `osname`,
                    a.`id_order` as temp_order_id,
                    sum(a.`price_ti`) as price_ti,
                    sum(a.`admin_commission`) as admin_commission,
                    sum(a.`admin_tax`) as admin_tax,
                    sum(a.`seller_amount`) as seller_amount,
                    sum(a.`seller_tax`) as seller_tax,
                    CONCAT(c.`firstname`," ",c.`lastname`) as customer';

                $this->_join = 'JOIN `' . _DB_PREFIX_ . 'orders` ord ON (a.`id_order` = ord.`id_order`)';
                $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (ord.`id_customer` = c.`id_customer`) ';
                $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'wk_mp_seller_order_status` wksos ON (a.`id_order` = wksos.`id_order` AND wksos.`id_seller` = ' . (int) $sellerRecord['seller_id'] . ')';
                $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = wksos.`current_state`)';
                $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ' . (int) $this->context->language->id . ')';

                $this->_orderBy = 'id_order';
                $this->_orderWay = 'DESC';
                $this->_where = WkMpSellerOrder::addSqlRestriction('ord');
                $this->_where .= ' AND a.`seller_customer_id` = ' . (int) $sellerRecord['seller_customer_id'];
                $this->_group = 'GROUP BY a.`id_order`';

                $this->toolbar_title = $sellerRecord['seller_shop'] . ' > ' . $this->l('View');

                $this->initSellerDetail();

                $this->actions = [];
                $this->actions[0] = 'view';

                return parent::renderList();
            }
        } elseif (Tools::getValue('mp_shipping_detail')) {
            // unset the filter if first renderlist contain any filteration
            if (!Tools::isSubmit('wk_mp_seller_orderOrderway')) {
                unset($this->context->cookie->sellerorderswk_mp_seller_orderOrderby);
                unset($this->context->cookie->sellerorderswk_mp_seller_orderOrderway);
            }

            // If seller shipping distribution is avalaible
            if ($idCustomerSeller = Tools::getValue('seller_id_customer')) {
                $this->table = 'wk_mp_seller_shipping_distribution';
                $this->identifier = 'id_seller_shipping_distribution';

                $sellerRecord = WkMpSellerOrder::getSellerRecord($idCustomerSeller);
                if ($sellerRecord) {
                    $this->toolbar_title = $sellerRecord['seller_shop'] . ' > ' . $this->l('View');
                }

                $this->_select = 'a.`order_id` as `temp_oid`, ord.`id_currency`, ord.`date_add` as order_date';
                $this->_orderBy = 'id_seller_shipping_distribution';
                $this->list_no_link = true;

                $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'orders` ord ON (a.`order_id` = ord.`id_order`) ';

                $this->_where = WkMpSellerOrder::addSqlRestriction('ord');
                $this->_where .= ' AND a.`seller_customer_id` = ' . (int) $idCustomerSeller;
            }

            $this->initShippingList();

            return parent::renderList();
        }

        if ($objCustomer = new Customer($idCustomerSeller)) {
            if (!in_array($objCustomer->id_shop, Shop::getContextListShopID())) {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminSellerOrders'));
            }
        }
    }

    protected function filterToField($key, $filter)
    {
        if (Tools::getValue('mp_shipping_detail')) {
            $this->initShippingList();
        } elseif (Tools::getValue('mp_seller_details')) {
            $this->initSellerDetail();
        }

        return parent::filterToField($key, $filter);
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitResetwk_mp_seller_order') || Tools::isSubmit('submitResetwk_mp_seller_order')) {
            $this->processResetFilters();
        }
        if (Tools::isSubmit('wk_mp_seller_orderOrderway')) {
            $this->processFilter();
        }

        if (Tools::isSubmit('submitFilterwk_mp_seller_order')) {
            $this->processFilter();
        }

        if (($sellerIdCustomer = Tools::getValue('id_customer_seller'))
        && Tools::isSubmit('exportwk_mp_seller_order_detail')) {
            $this->sellerOrderExport($sellerIdCustomer);
        }

        Media::addJsDef([
            'current_url' => $this->context->link->getAdminLink('AdminSellerOrders'),
        ]);
        parent::postProcess();
    }

    public static function setOrderCurrency($val, $arr)
    {
        $val = Tools::ps_round($val, 2, PS_ROUND_DOWN);
        if (Tools::getValue('mp_shipping_detail')) {
            if (Tools::getValue('seller_id_customer')) {
                return WkMpHelper::displayPrice($val, (int) $arr['id_currency']);
            }
        } else {
            return WkMpHelper::displayPrice($val, (int) $arr['id_currency']);
        }
    }

    public function sellerOrderExport($sellerIdCustomer)
    {
        if ($sellerIdCustomer) {
            $sellerOrderDetails = new WkMpSellerOrderDetail();
            $allOrders = $sellerOrderDetails->getExportAllOrders($sellerIdCustomer);
            if (empty($allOrders)) {
                $this->errors = $this->l('No orders are available on selected date range.');

                return;
            }
            $idLang = Context::getContext()->language->id;
            $fileName = 'ordercsv_' . date('Y-m-d_H:i', time()) . '.csv';
            header('Content-Type: text/csv');
            header('Content-Type: application/force-download; charset=UTF-8');
            header('Cache-Control: no-store, no-cache');
            header('Content-Disposition: attachment; filename=' . $fileName);
            ob_end_clean();
            $output = fopen('php://output', 'w');
            fputcsv($output, [
                $this->l('Order ID'),
                $this->l('Reference'),
                $this->l('Customer'),
                $this->l('Amount'),
                $this->l('Payment status'),
                $this->l('Payment method'),
                $this->l('Date'),
            ]);
            if ($allOrders) {
                $count = 1;
                foreach ($allOrders as $eachOrderCsvData) {
                    $csvData = [];
                    $orderObj = new Order($eachOrderCsvData['id_order']);
                    $customerObj = new Customer($orderObj->id_customer);
                    $orderStateObj = new OrderState($orderObj->current_state);
                    $objCurrency = new Currency($orderObj->id_currency);
                    $csvData['ID_entry'] = $orderObj->id;
                    $csvData['shop_order_number'] = $orderObj->reference;
                    $csvData['customer'] = $customerObj->firstname . ' ' . $customerObj->lastname;
                    $csvData['amount'] = $objCurrency->symbol . $eachOrderCsvData['price_ti'];
                    $csvData['payment_status'] = $orderStateObj->name[$idLang];
                    $csvData['provider_name'] = $orderObj->payment;
                    $csvData['created_at_dat'] = $eachOrderCsvData['date_add'];
                    fputcsv($output, $csvData);
                    ++$count;
                }
            }
            fclose($output);
            exit;
        }
    }

    public function getList($idLang, $orderBy = null, $orderWay = null, $start = 0, $limit = null, $idLangShop = false)
    {
        parent::getList($idLang, $orderBy, $orderWay, $start, $limit, $idLangShop);

        // echo $this->table;
        $nb_items = count($this->_list);
        if ($this->table == 'wk_mp_seller_order') {
            for ($i = 0; $i < $nb_items; ++$i) {
                $item = &$this->_list[$i];
                $query = new DbQuery();
                $query->select('COUNT(DISTINCT mcc.`id_order`) as count_values');
                $query->from('wk_mp_seller_order_detail', 'mcc');
                $query->where('mcc.id_seller_order =' . (int) $item['id_mp_order'] .
                WkMpSellerOrder::addSqlRestriction('mcc'));
                $query->orderBy('count_values DESC');
                $item['count_values'] = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);

                // calculating pending orders
                $item['pending_count_values'] = (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
                    'SELECT COUNT(DISTINCT mcc.`id_order`) FROM `' . _DB_PREFIX_ . 'wk_mp_seller_order_detail` mcc
                    LEFT JOIN `' . _DB_PREFIX_ . 'orders` ordr on (ordr.`id_order` = mcc.`id_order`)
                    WHERE mcc.`id_seller_order` = ' . (int) $item['id_mp_order'] . '
                    AND mcc.`id_order` NOT IN (
                        SELECT `id_order` FROM `' . _DB_PREFIX_ . 'order_history` oh
                        WHERE oh.`id_order` = ordr.`id_order`
                        AND oh.`id_order_state`= ' . (int) Configuration::get('PS_OS_PAYMENT') . '
                    )' . WkMpSellerOrder::addSqlRestriction('mcc')
                );

                $item['badge_danger'] = true;
                unset($query);
            }
        }
    }

    public function ajaxProcessViewOrderDetail()
    {
        $idOrder = Tools::getValue('id_order');
        $sellerCustomerId = Tools::getValue('seller_customer_id');
        if ($idOrder) {
            $objWkMpSellerOrderDetail = new WkMpSellerOrderDetail();
            $result = $objWkMpSellerOrderDetail->getOrderCommissionDetails($idOrder, $sellerCustomerId);
            if ($result) {
                foreach ($result as $key => &$data) {
                    $mpProduct = WkMpSellerProduct::getSellerProductByPsIdProduct($data['product_id']);
                    $result[$key]['seller_amount'] = WkMpHelper::displayPrice(
                        $data['seller_amount'],
                        new Currency($data['id_currency'])
                    );
                    $result[$key]['seller_tax'] = WkMpHelper::displayPrice(
                        $data['seller_tax'],
                        new Currency($data['id_currency'])
                    );
                    $result[$key]['admin_commission'] = WkMpHelper::displayPrice(
                        $data['admin_commission'],
                        new Currency($data['id_currency'])
                    );
                    $result[$key]['admin_tax'] = WkMpHelper::displayPrice(
                        $data['admin_tax'],
                        new Currency($data['id_currency'])
                    );
                    $result[$key]['price_ti'] = WkMpHelper::displayPrice(
                        $data['price_ti'],
                        new Currency($data['id_currency'])
                    );
                    if ($mpProduct && $mpProduct['id_mp_product']) {
                        $result[$key]['product_link'] = $this->context->link->getAdminLink('AdminSellerProductDetail')
                        . '&updatewk_mp_seller_product&id_mp_product=' . (int) $mpProduct['id_mp_product'];
                    }
                }

                if (_PS_VERSION_ >= '1.7.7.0') {
                    $wkOrderLink = $this->context->link->getAdminLink(
                        'AdminOrders',
                        true,
                        ['vieworder' => 1, 'id_order' => (int) $idOrder],
                        []
                    );
                } else {
                    $wkOrderLink = $this->context->link->getAdminLink('AdminOrders')
                    . '&vieworder&id_order=' . (int) $idOrder . '#start_products';
                }

                $this->context->smarty->assign([
                    'result' => $result,
                    'orderInfo' => $objWkMpSellerOrderDetail->getSellerOrderDetail((int) $idOrder),
                    'orderlink' => $wkOrderLink,
                ]);
                $output = $this->context->smarty->fetch(
                    _PS_MODULE_DIR_ . 'marketplace/views/templates/admin/seller-product-line.tpl'
                );
                exit($output);
            }
        }
        exit; // return false;
    }

    public function ajaxProcessChangeShippingDistributionType()
    {
        if ($idPsReference = Tools::getValue('id_ps_reference')) {
            $shippingDistributeType = Tools::getValue('shipping_distribute_type');
            // Change shipping distribution type for Ps Carriers Controller
            if (WkMpAdminShipping::updatePsShippingDistributionType($idPsReference, $shippingDistributeType)) {
                exit('1');
            }
        }

        exit('0');
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJS(_MODULE_DIR_ . 'marketplace/views/js/sellertransaction.js');
    }
}
