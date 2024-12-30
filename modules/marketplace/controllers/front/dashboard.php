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

class MarketplaceDashboardModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
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
            // if seller is approved/active
            if ($seller && $seller['active']) {
                // Predefined range for graph as Month
                $dateFrom = date('Y-m-01');
                $dateTo = date('Y-m-t');
                $preselectDateRange = 2;

                // Get Seller's Recent Orders
                $recentOrders = [];
                $objMpOrder = new WkMpSellerOrder();
                $objOrderStatus = new WkMpSellerOrderStatus();
                if ($recentOrders = $objMpOrder->getSellerOrders($this->context->language->id, $idCustomer, true)) {
                    // Get recent 5 orders
                    foreach ($recentOrders as &$order) {
                        $objOrder = new Order($order['id_order']);
                        if (!$objMpOrder->checkSellerOrder($objOrder, $seller['id_seller'])) {
                            $idOrderState = $objOrderStatus->getCurrentOrderState($order['id_order'], $seller['id_seller']);
                            if ($idOrderState) {
                                $state = new OrderState($idOrderState, $this->context->language->id);
                                $order['order_status'] = $state->name;
                            }
                        }
                        $order['buyer_info'] = new Customer($order['buyer_id_customer']);
                        if ($sellerOrderTotal = $objMpOrder->getTotalOrder($order['id_order'], $idCustomer)) {
                            // Add shipping amount in total orders
                            if ($sellerShippingEarning = WkMpAdminShipping::getSellerShippingByIdOrder($order['id_order'], $idCustomer)) {
                                $sellerOrderTotal += $sellerShippingEarning;
                            }

                            $order['total_paid'] = WkMpHelper::displayPrice($sellerOrderTotal, (int) $order['id_currency']);
                        }
                    }
                }

                $totalOrdersCount = 0;
                if ($totalOrders = $objMpOrder->getSellerOrders($this->context->language->id, $idCustomer)) {
                    $totalOrdersCount = count($totalOrders);
                }

                $this->context->smarty->assign([
                    'seller_name' => $seller['seller_firstname'] . ' ' . $seller['seller_lastname'],
                    'preselectDateRange' => $preselectDateRange,
                    'userFriendlyDateFrom' => date('d-m-Y', strtotime($dateFrom)),
                    'userFriendlyDateTo' => date('d-m-Y', strtotime($dateTo)),
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                    'recentOrders' => $recentOrders,
                    'totalOrdersCount' => $totalOrdersCount,
                    'DASHPRODUCT_NBR_SHOW_LAST_ORDER' => (int) Configuration::get('DASHPRODUCT_NBR_SHOW_LAST_ORDER') ? (int) Configuration::get('DASHPRODUCT_NBR_SHOW_LAST_ORDER') : 10,
                    'DASHPRODUCT_NBR_SHOW_BEST_SELLER' => Configuration::get('DASHPRODUCT_NBR_SHOW_BEST_SELLER', 10),
                    'logic' => '1',
                ]);

                Media::addJsDef([
                    'userFriendlyDateFrom' => date('d-m-Y', strtotime($dateFrom)),
                    'userFriendlyDateTo' => date('d-m-Y', strtotime($dateTo)),
                    'currentDate' => date('d-m-Y'),
                ]);

                $this->defineJSVars();
                $this->setTemplate('module:marketplace/views/templates/front/dashboard/dashboard.tpl');
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect('index.php?controller=authentication&back=' . urlencode($this->context->link->getModuleLink('marketplace', 'dashboard')));
        }
    }

    public function defineJSVars()
    {
        $objDefaultCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

        $dateRangeJs = [];
        $dateRangeJs['months'] = [
            $this->module->l('January', 'dashboard'),
            $this->module->l('February', 'dashboard'),
            $this->module->l('March', 'dashboard'),
            $this->module->l('April', 'dashboard'),
            $this->module->l('May', 'dashboard'),
            $this->module->l('June', 'dashboard'),
            $this->module->l('July', 'dashboard'),
            $this->module->l('August', 'dashboard'),
            $this->module->l('September', 'dashboard'),
            $this->module->l('October', 'dashboard'),
            $this->module->l('November', 'dashboard'),
            $this->module->l('December', 'dashboard'),
        ];
        $dateRangeJs['monthsShort'] = [
            $this->module->l('Jan', 'dashboard'),
            $this->module->l('Feb', 'dashboard'),
            $this->module->l('Mar', 'dashboard'),
            $this->module->l('Apr', 'dashboard'),
            $this->module->l('May', 'dashboard'),
            $this->module->l('Jun', 'dashboard'),
            $this->module->l('Jul', 'dashboard'),
            $this->module->l('Aug', 'dashboard'),
            $this->module->l('Sept', 'dashboard'),
            $this->module->l('Oct', 'dashboard'),
            $this->module->l('Nov', 'dashboard'),
            $this->module->l('Dec', 'dashboard'),
        ];
        $dateRangeJs['weekDaysShort'] = [
            $this->module->l('Su', 'dashboard'),
            $this->module->l('Mo', 'dashboard'),
            $this->module->l('Tu', 'dashboard'),
            $this->module->l('We', 'dashboard'),
            $this->module->l('Th', 'dashboard'),
            $this->module->l('Fr', 'dashboard'),
            $this->module->l('Sa', 'dashboard'),
        ];
        $jsVars = [
            'order_text' => $this->module->l('order', 'dashboard'),
            'value_text' => $this->module->l('value', 'dashboard'),
            'income_text' => $this->module->l('Premium income', 'dashboard'),
            'dashboard_link' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
            'mporderdetails_link' => $this->context->link->getModuleLink('marketplace', 'mporderdetails'),
            'friendly_url' => Configuration::get('PS_REWRITING_SETTINGS'),
            'currency_format' => $objDefaultCurrency->format,
            'currency_sign' => $objDefaultCurrency->sign,
            'currency_blank' => $objDefaultCurrency->blank,
            'priceDisplayPrecision' => _PS_PRICE_DISPLAY_PRECISION_,
            'dateRangeJs' => $dateRangeJs,
            'dateRangeApply' => $this->module->l('Apply', 'dashboard'),
            'dateRangeCancel' => $this->module->l('Cancel', 'dashboard'),
        ];
        Media::addJsDef($jsVars);
    }

    public function getSelectedDateRange($rangeIndicator)
    {
        if (!$rangeIndicator) {
            return 0;
        }

        if ($rangeIndicator == 1) {
            $dateFrom = date('Y-m-d');
            $dateTo = date('Y-m-d');
        } elseif ($rangeIndicator == 2) {
            $dateFrom = date('Y-m-01');
            $dateTo = date('Y-m-t');
        } elseif ($rangeIndicator == 3) {
            $dateFrom = date('Y-01-01');
            $dateTo = date('Y-12-31');
        } elseif ($rangeIndicator == 4) {
            $yesterday = time() - 60 * 60 * 24;
            $dateFrom = date('Y-m-d', $yesterday);
            $dateTo = date('Y-m-d', $yesterday);
        } elseif ($rangeIndicator == 5) {
            $m = (date('m') == 1 ? 12 : date('m') - 1);
            $y = ($m == 12 ? date('Y') - 1 : date('Y'));
            $dateFrom = $y . '-' . $m . '-01';
            $dateTo = $y . '-' . $m . date('-t', mktime(12, 0, 0, $m, 15, $y));
        } elseif ($rangeIndicator == 6) {
            $dateFrom = (date('Y') - 1) . date('-01-01');
            $dateTo = (date('Y') - 1) . date('-12-31');
        }

        return [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ];
    }

    public function displayAjaxLoadSellerDashboard()
    {
        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-t');
        if ($preselectDateRange = Tools::getValue('preselectDateRange')) { // For day, month, year button
            $dateRange = $this->getSelectedDateRange($preselectDateRange);
            if ($dateRange) {
                $dateFrom = $dateRange['dateFrom'];
                $dateTo = $dateRange['dateTo'];
            }
        } else { // For datepicker
            $dateFrom = Tools::getValue('dateFrom');
            $dateTo = Tools::getValue('dateTo');
        }

        $params = [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ];

        exit(json_encode(['dashtrends' => $this->sellerDashboardData($params)]));
    }

    protected function getOrdersData($dateFrom, $dateTo)
    {
        $idCustomer = $this->context->customer->id;
        // Override customer id if any staff of seller want to use this controller
        if (Module::isEnabled('mpsellerstaff')) {
            $getCustomerId = WkMpSellerStaff::overrideMpSellerCustomerId($idCustomer);
            if ($getCustomerId) {
                $idCustomer = $getCustomerId;
            }
        }

        // We need the following figures to calculate our stats
        $tmpData = [
            'orders' => WkMpSellerOrder::getSellerTotalOrders($idCustomer, $dateFrom, $dateTo),
            'total_paid_tax_excl' => WkMpSellerOrder::getSellerTotalSales($idCustomer, $dateFrom, $dateTo),
        ];

        return $tmpData;
    }

    protected function getFilteredData($dateFrom, $dateTo, $grossData)
    {
        $refinedData = [];
        $from = strtotime($dateFrom . ' 00:00:00');
        $to = min(time(), strtotime($dateTo . ' 23:59:59'));
        for ($date = $from; $date <= $to; $date = strtotime('+1 day', $date)) {
            $refinedData['sales'][$date] = 0;
            if (isset($grossData['total_paid_tax_excl'][$date])) {
                $refinedData['sales'][$date] += $grossData['total_paid_tax_excl'][$date];
            }

            $refinedData['orders'][$date] = isset($grossData['orders'][$date]) ? $grossData['orders'][$date] : 0;
        }

        if (empty($refinedData)) {
            $refinedData['sales'][0] = 0;
            $refinedData['orders'][0] = 0;
        }

        return $refinedData;
    }

    protected function addOrdersData($data)
    {
        $summing = [
            'sales' => array_sum($data['sales']),
            'orders' => array_sum($data['orders']),
        ];

        return $summing;
    }

    protected function addTaxSuffix()
    {
        return $this->context->smarty->fetch('module:' . $this->module->name . '/views/templates/front/dashboard/_partials/tax-suffix.tpl');
    }

    public function getDashboardChartTrends()
    {
        $chartData = [];
        $chartDataCompare = [];
        foreach (array_keys($this->dashboardData) as $chartKey) {
            $chartData[$chartKey] = $chartDataCompare[$chartKey] = [];

            if (!count($this->dashboardData[$chartKey])) {
                continue;
            }

            foreach ($this->dashboardData[$chartKey] as $key => $value) {
                $chartData[$chartKey][] = [$key, $value];
            }
        }

        $charts = [
            'sales' => $this->module->l('Sales', 'dashboard'),
            'orders' => $this->module->l('Orders', 'dashboard'),
        ];

        $gfxColor = ['#00aff0', '#72c279'];

        $i = 0;
        $data = [
            'chart_type' => 'line_chart_trends',
            'date_format' => $this->context->language->date_format_lite,
        ];

        foreach ($charts as $key => $title) {
            $data['data'][] = [
                'id' => $key,
                'key' => $title,
                'color' => $gfxColor[$i],
                'values' => $chartData[$key],
                'disabled' => ($key == 'sales' ? false : true),
            ];
            ++$i;
        }

        return $data;
    }

    public function sellerDashboardData($params)
    {
        $tmpData = $this->getOrdersData($params['dateFrom'], $params['dateTo']);
        $this->dashboardData = $this->getFilteredData($params['dateFrom'], $params['dateTo'], $tmpData);
        $this->dashboardDataSum = $this->addOrdersData($this->dashboardData);

        $objDefaultCurrency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

        return [
            'data_value' => [
                'sales_score' => WkMpHelper::displayPrice(
                    $this->dashboardDataSum['sales'],
                    $objDefaultCurrency
                ) . $this->addTaxSuffix(),
                'orders_score' => Tools::displayNumber(
                    $this->dashboardDataSum['orders'],
                    $objDefaultCurrency
                ),
            ],
            'data_chart' => ['dash_trends_chart1' => $this->getDashboardChartTrends()],
        ];
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = [];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Marketplace', 'dashboard'),
            'url' => '',
        ];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Dashboard', 'dashboard'),
            'url' => '',
        ];

        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryUI('ui.datepicker');
        $this->registerStylesheet('marketplace_account', 'modules/' . $this->module->name . '/views/css/marketplace_account.css');
        $this->registerStylesheet('mp_global_style', 'modules/' . $this->module->name . '/views/css/mp_global_style.css');
        $this->registerStylesheet('dashboard', 'modules/' . $this->module->name . '/views/css/dashboard.css');
        $this->registerJavascript('mpdashboard', 'modules/' . $this->module->name . '/views/js/mpdashboard.js');

        // Include Required Prerequisites
        $this->registerJavascript('moment-js', '//cdn.jsdelivr.net/momentjs/latest/moment.min.js', ['server' => 'remote']);

        // Include Date Range Picker
        $this->registerJavascript('datepicker-js', '//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js', ['server' => 'remote']);
        $this->registerStylesheet('datepicker-css', '//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css', ['server' => 'remote']);
    }
}
