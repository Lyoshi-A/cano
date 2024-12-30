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

class MarketplaceProductListModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if ($this->context->customer->isLogged()) {
            $idCustomer = $this->context->customer->id;
            $addPermission = 1;
            $editPermission = 1;
            $deletePermission = 1;

            // Override customer id if any staff of seller want to use this controller
            if (Module::isEnabled('mpsellerstaff')) {
                $staffDetails = WkMpSellerStaff::getStaffInfoByIdCustomer($idCustomer);
                if ($staffDetails
                    && $staffDetails['active']
                    && $staffDetails['id_seller']
                    && $staffDetails['seller_status']
                ) {
                    $idTab = WkMpTabList::MP_PRODUCT_TAB; // For Product
                    $staffTabDetails = WkMpTabList::getStaffPermissionWithTabName(
                        $staffDetails['id_staff'],
                        $this->context->language->id,
                        $idTab
                    );
                    if ($staffTabDetails) {
                        $addPermission = $staffTabDetails['add'];
                        $editPermission = $staffTabDetails['edit'];
                        $deletePermission = $staffTabDetails['delete'];
                    }
                }

                $getCustomerId = WkMpSellerStaff::overrideMpSellerCustomerId($idCustomer);
                if ($getCustomerId) {
                    $idCustomer = $getCustomerId;
                }
            }

            $seller = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($seller && $seller['active']) {
                if (($mpIdSeller = $seller['id_seller']) && Configuration::get('WK_MP_SELLER_SHIPPING')) {
                    $objWkMpShipping = new WkMpSellerShipping();
                    $mpShippingData = $objWkMpShipping->getMpShippingMethods($mpIdSeller);
                    if ($mpShippingData) {
                        foreach ($mpShippingData as $key => $value) {
                            $mpShippingData[$key]['id_reference'] = (int) $value['id_ps_reference'];
                            if ($carrierCurrent = Carrier::getCarrierByReference($value['id_ps_reference'])) {
                                $mpShippingData[$key]['id_carrier'] = (int) $carrierCurrent->id;
                            }
                        }
                    }

                    if (Configuration::get('WK_MP_SELLER_ADMIN_SHIPPING')) {
                        $allPsCarriersArr = WkMpSellerShipping::getOnlyPrestaCarriers($this->context->language->id);
                        if ($allPsCarriersArr) {
                            foreach ($allPsCarriersArr as $key => $value) {
                                $allPsCarriersArr[$key]['id'] = 0; // default in place of id index [mp shipping id]
                                $allPsCarriersArr[$key]['mp_shipping_name'] = $value['name'];
                            }
                        }

                        if (!$mpShippingData) {
                            $mpShippingData = $allPsCarriersArr;
                        } else {
                            $mpShippingData = array_merge($mpShippingData, $allPsCarriersArr);
                        }
                    }
                    if ($mpShippingData) {
                        // Assign shipping button will display only if atleast one shipping will be active
                        $this->context->smarty->assign('shipping_method', $mpShippingData);
                        $this->context->smarty->assign(
                            'ajax_link',
                            $this->context->link->getModuleLink('marketplace', 'mpshippinglist')
                        );
                        $this->context->smarty->assign('modules_dir', _MODULE_DIR_);
                        $this->context->smarty->assign('mp_id_seller', $mpIdSeller);
                    }
                }

                // delete selected checkbox process
                if ($selectedProducts = Tools::getValue('mp_product_selected')) {
                    $this->deleteSelectedProducts($selectedProducts, $seller['id_seller']);
                }

                // change product status if seller can activate/deactivate their product
                if (Tools::getValue('mp_product_status')
                && Configuration::get('WK_MP_SELLER_PRODUCTS_SETTINGS')) {
                    $this->changeProductStatus($seller['id_seller']);
                }

                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    $idLang = $this->context->language->id;
                } else {
                    if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') {
                        $idLang = Configuration::get('PS_LANG_DEFAULT');
                    } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') {
                        $idLang = $seller['default_lang'];
                    }
                }
                $objLang = new Language((int) $idLang);
                if (!$objLang->active) {
                    $idLang = Configuration::get('PS_LANG_DEFAULT');
                }

                $sellerProduct = WkMpSellerProduct::getSellerAllShopProduct(
                    $seller['id_seller'],
                    'all',
                    $idLang
                );
                if (!$sellerProduct) {
                    $sellerProduct = [];
                }

                $shareCustomerEnabled = false;
                if ($this->context->shop->id_shop_group) {
                    $objShopGroup = new ShopGroup((int) $this->context->shop->id_shop_group);
                    $shareCustomerEnabled = $objShopGroup->share_customer;
                }

                $this->context->smarty->assign([
                    'products_status' => Configuration::get('WK_MP_SELLER_PRODUCTS_SETTINGS'),
                    'imageediturl' => $this->context->link->getModuleLink('marketplace', 'productimageedit'),
                    'product_lists' => $sellerProduct,
                    'is_seller' => $seller['active'],
                    'logic' => 3,
                    'static_token' => Tools::getToken(false),
                    'add_permission' => $addPermission,
                    'edit_permission' => $editPermission,
                    'delete_permission' => $deletePermission,
                    'isMultiShopEnabled' => WkMpHelper::isMultiShopEnabled(),
                    'currentShopId' => Context::getContext()->shop->id,
                    'shareCustomerEnabled' => $shareCustomerEnabled,
                ]);

                $this->defineJSVars();
                $this->setTemplate('module:marketplace/views/templates/front/product/productlist.tpl');
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect(
                'index.php?controller=authentication&back=' .
                urlencode($this->context->link->getModuleLink('marketplace', 'productlist'))
            );
        }
    }

    public function postProcess()
    {
        if (Configuration::get('WK_MP_SELLER_EXPORT')
        && (Tools::isSubmit('mp_csv_product_export') || Tools::getValue('export_all'))) {
            $fromExportDate = Tools::getValue('from_export_date');
            $toExportDate = Tools::getValue('to_export_date');
            $fromExportDate = date('Y-m-d', strtotime($fromExportDate));
            $toExportDate = date('Y-m-d', strtotime($toExportDate));
            $exportAll = false;
            if (Tools::getValue('export_all')) {
                $exportAll = true;
            }
            if (!$exportAll) {
                if ($fromExportDate == '') {
                    $this->errors[] = $this->module->l('Export from date is required.', 'productlist');
                } elseif (!Validate::isDateFormat($fromExportDate)) {
                    $this->errors[] = $this->module->l('Export from date is not valid.', 'productlist');
                }
                if ($toExportDate == '') {
                    $this->errors[] = $this->module->l('Export to date is required.', 'productlist');
                } elseif (!Validate::isDateFormat($toExportDate)) {
                    $this->errors[] = $this->module->l('Export to date is not valid.', 'productlist');
                }
            }

            if (empty($this->errors)) {
                if ($idCustomer = $this->context->customer->id) {
                    if ($mpSeller = WkMpSeller::getSellerDetailByCustomerId($idCustomer)) {
                        $this->exportProductsCSV($mpSeller, $fromExportDate, $toExportDate, $exportAll);
                    }
                }
            }
        }

        parent::postProcess();
    }

    public function exportProductsCSV($seller, $fromExportDate, $toExportDate, $exportAll)
    {
        if ($fromExportDate && $toExportDate && $seller) {
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $idLang = $this->context->language->id;
            } else {
                if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') {
                    $idLang = Configuration::get('PS_LANG_DEFAULT');
                } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') {
                    $idLang = $seller['default_lang'];
                }
            }
            $objLang = new Language((int) $idLang);
            if (!$objLang->active) {
                $idLang = Configuration::get('PS_LANG_DEFAULT');
            }
            if ($exportAll) {
                $csvDataArr = WkMpSellerProduct::getSellerAllShopProduct(
                    $seller['id_seller'],
                    'all',
                    $idLang
                );
                if (empty($csvDataArr)) {
                    $this->errors = $this->module->l('No products are available.', 'productlist');

                    return;
                }
            } else {
                $csvDataArr = WkMpSellerProduct::getSellerAllShopProduct(
                    $seller['id_seller'],
                    'all',
                    $idLang,
                    $fromExportDate,
                    $toExportDate
                );
                if (empty($csvDataArr)) {
                    $this->errors = $this->module->l('No products are available on selected date range.', 'productlist');

                    return;
                }
            }

            // Export products in CSV
            $idLang = Context::getContext()->language->id;
            $fileName = 'product_csv_' . date('Y-m-d_H:i', time()) . '.csv';
            header('Content-Type: text/csv');
            header('Content-Type: application/force-download; charset=UTF-8');
            header('Cache-Control: no-store, no-cache');
            header('Content-Disposition: attachment; filename=' . $fileName);
            ob_end_clean();
            $output = fopen('php://output', 'w');
            fputcsv($output, [
                $this->module->l('Product ID', 'productlist'),
                $this->module->l('Name', 'productlist'),
                $this->module->l('Price', 'productlist'),
                $this->module->l('Quantity', 'productlist'),
                $this->module->l('Status', 'productlist'),
                $this->module->l('Date', 'productlist'),
            ]);
            if ($csvDataArr) {
                $count = 1;
                foreach ($csvDataArr as $eachProductCsvData) {
                    $csvData = [];
                    $csvData['product_id'] = $eachProductCsvData['id_mp_product'];
                    $csvData['name'] = $eachProductCsvData['name'];
                    $csvData['price'] = $eachProductCsvData['price'];
                    $csvData['quantity'] = $eachProductCsvData['quantity'];
                    $csvData['status'] =
                    ($eachProductCsvData['active']) ? $this->module->l('Active', 'productlist') : $this->module->l('Pending', 'productlist');
                    $csvData['date_add'] = $eachProductCsvData['date_add']; // Seller prod creation or assign prod date
                    fputcsv($output, $csvData);
                    ++$count;
                }
            }
            fclose($output);
            exit;
        }
    }

    public function defineJSVars()
    {
        $jsVars = [
            'productlist_link' => $this->context->link->getModuleLink('marketplace', 'productlist'),
            'ajax_urlpath' => $this->context->link->getModuleLink('marketplace', 'productimageedit'),
            'image_drag_drop' => 1,
            'space_error' => $this->module->l('Space is not allowed.', 'productlist'),
            'confirm_delete_msg' => $this->module->l('Are you sure you want to delete?', 'productlist'),
            'confirm_duplicate_msg' => $this->module->l('Are you sure you want to duplicate?', 'productlist'),
            'delete_msg' => $this->module->l('Deleted.', 'productlist'),
            'error_msg' => $this->module->l('An error occurred.', 'productlist'),
            'checkbox_select_warning' => $this->module->l('You must select at least one element.', 'productlist'),
            'display_name' => $this->module->l('Display', 'productlist'),
            'records_name' => $this->module->l('records per page', 'productlist'),
            'no_product' => $this->module->l('No product found', 'productlist'),
            'show_page' => $this->module->l('Showing page', 'productlist'),
            'show_of' => $this->module->l('of', 'productlist'),
            'no_record' => $this->module->l('No records available', 'productlist'),
            'filter_from' => $this->module->l('filtered from', 'productlist'),
            't_record' => $this->module->l('total records', 'productlist'),
            'search_item' => $this->module->l('Search', 'productlist'),
            'p_page' => $this->module->l('Previous', 'productlist'),
            'n_page' => $this->module->l('Next', 'productlist'),
            'update_success' => $this->module->l('Updated successfully.', 'productlist'),
            'empty_from_date' => $this->module->l('Please select from date.', 'productlist'),
            'empty_to_date' => $this->module->l('Please select to date.', 'productlist'),
            'compare_date_error' => $this->module->l('To date must be greater than from date.', 'productlist'),
        ];
        if (Configuration::get('PS_REWRITING_SETTINGS')) {
            $jsVars['friendly_url'] = 1;
        } else {
            $jsVars['friendly_url'] = 0;
        }
        Media::addJsDef($jsVars);
    }

    public function changeProductStatus($idSeller)
    {
        $idProduct = Tools::getValue('id_product');
        $sellerProduct = WkMpSellerProduct::getSellerProductByPsIdProduct($idProduct);
        if ($sellerProduct && ($sellerProduct['id_seller'] == $idSeller)) {
            $mpIdProduct = $sellerProduct['id_mp_product'];
            Hook::exec('actionBeforeToggleMPProductStatus', ['id_mp_product' => $mpIdProduct]);
            if (!count($this->errors)) {
                $objMpProduct = new WkMpSellerProduct($mpIdProduct);
                if ($psProductId = $objMpProduct->id_ps_product) {
                    $objPsProduct = new Product($psProductId);
                    if ($objPsProduct->active) {
                        $objMpProduct->status_before_deactivate = 0;
                        $objMpProduct->save();

                        // Update on ps
                        $objPsProduct->active = 0;
                        $objPsProduct->save();
                    } else {
                        $objMpProduct->status_before_deactivate = 1;
                        $objMpProduct->save();

                        // Update on ps
                        $objPsProduct->active = 1;
                        $objPsProduct->save();

                        Hook::exec(
                            'actionToogleMPProductActive',
                            ['id_mp_product' => $mpIdProduct, 'active' => $objPsProduct->active]
                        );
                    }

                    Hook::exec(
                        'actionAfterToggleMPProductStatus',
                        ['id_product' => $idProduct, 'active' => $objPsProduct->active]
                    );
                    Tools::redirect(
                        $this->context->link->getModuleLink('marketplace', 'productlist', ['status_updated' => 1])
                    );
                }
            }
        }
    }

    public function deleteSelectedProducts($mpIdProducts, $idSeller)
    {
        $mpDelete = true;
        foreach ($mpIdProducts as $idMpProduct) {
            $objMpProduct = new WkMpSellerProduct($idMpProduct);
            if ($objMpProduct->id_seller == $idSeller) {
                if (!$objMpProduct->delete()) {
                    $mpDelete = false;
                }
            }
            unset($objMpProduct);
        }

        if ($mpDelete) {
            Tools::redirect(
                $this->context->link->getModuleLink('marketplace', 'productlist', ['deleted' => 1])
            );
        }
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = [];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Marketplace', 'productlist'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        ];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Products', 'productlist'),
            'url' => '',
        ];

        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();

        Media::addJsDef([
            'languages' => Language::getLanguages(),
            'ImageCaptionLangError' => $this->module->l('Image caption field is invalid in', 'productlist'),
            'no_products_error' => $this->module->l('Please create product first.', 'productlist'),
        ]);
        $this->addjQueryPlugin('growl', null, false);
        $this->registerStylesheet(
            'marketplace_account',
            'modules/' . $this->module->name . '/views/css/marketplace_account.css'
        );
        $this->registerJavascript(
            'mp-imageedit-js',
            'modules/' . $this->module->name . '/views/js/imageedit.js'
        );

        // data table file included
        $this->registerStylesheet(
            'datatable_bootstrap',
            'modules/' . $this->module->name . '/views/css/datatable_bootstrap.css'
        );
        $this->registerJavascript(
            'mp-jquery-dataTables',
            'modules/' . $this->module->name . '/views/js/jquery.dataTables.min.js'
        );
        $this->registerJavascript(
            'mp-dataTables.bootstrap',
            'modules/' . $this->module->name . '/views/js/dataTables.bootstrap.js'
        );
        $this->context->controller->registerJavascript(
            'datepicker-i18n.js',
            'js/jquery/ui/i18n/jquery-ui-i18n.js',
            ['position' => 'bottom', 'priority' => 999]
        );
        $this->registerJavascript(
            'mp-table-dnd',
            'modules/' . $this->module->name . '/views/js/table-dnd.js'
        );
    }
}
