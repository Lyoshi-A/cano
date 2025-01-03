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

class MarketplaceMpManufacturerListModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if ($this->context->customer->isLogged()) {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if ($mpSeller && $mpSeller['active']) {
                if (!Configuration::get('WK_MP_PRODUCT_MANUFACTURER')) {
                    Tools::redirect(__PS_BASE_URI__ . 'pagenotfound');
                }

                $mpSellerId = $mpSeller['id_seller'];
                $objMpManuf = new WkMpManufacturers();
                $manufInfo = $objMpManuf->getManufacturerInfo($mpSellerId);
                if ($manufInfo) {
                    foreach ($manufInfo as &$data) {
                        $data['id'] = $data['id_wk_mp_manufacturers'];
                        $data['id_ps_manuf'] = $data['id_ps_manuf'];

                        $manufImg = 'marketplace/views/img/mpmanufacturers/' . $data['id_wk_mp_manufacturers'] . '.jpg';
                        if (file_exists(_PS_MODULE_DIR_ . $manufImg)) {
                            $data['image'] = _MODULE_DIR_ . $manufImg;
                        } else {
                            $data['image'] = _MODULE_DIR_ . 'marketplace/views/img/mpmanufacturers/default_img.png';
                        }

                        $data['product_num'] = WkMpManufacturers::countProductsByPsManufId(
                            $data['id_wk_mp_manufacturers']
                        );
                    }

                    $this->context->smarty->assign('manufinfo', $manufInfo);
                }

                $this->context->smarty->assign('currentShopId', $this->context->shop->id);
                $this->context->smarty->assign('id_lang', $this->context->language->id);
                $this->context->smarty->assign('logic', 'mpmanufacturerlist');
                $this->defineJSVars();
                $this->setTemplate(
                    'module:marketplace/views/templates/front/product/manufacturers/manufacturerslist.tpl'
                );
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect('index.php?controller=authentication&back=' .
            urlencode($this->context->link->getModuleLink('marketplace', 'mpmanufacturerlist')));
        }
    }

    public function defineJSVars()
    {
        $jsVars = [
            'delete_manufacturer' => $this->context->link->getModuleLink('marketplace', 'mpmanufacturerlist'),
            'module_dir' => _MODULE_DIR_,
            'wk_dataTables' => 1,
            'confirm_msg' => $this->module->l('Are you sure?', 'mpmanufacturerlist'),
            'display_name' => $this->module->l('Display', 'mpmanufacturerlist'),
            'records_name' => $this->module->l('records per page', 'mpmanufacturerlist'),
            'no_product' => $this->module->l('No order found', 'mpmanufacturerlist'),
            'show_page' => $this->module->l('Showing page', 'mpmanufacturerlist'),
            'show_of' => $this->module->l('of', 'mpmanufacturerlist'),
            'no_record' => $this->module->l('No records available', 'mpmanufacturerlist'),
            'filter_from' => $this->module->l('filtered from', 'mpmanufacturerlist'),
            't_record' => $this->module->l('total records', 'mpmanufacturerlist'),
            'search_item' => $this->module->l('Search', 'mpmanufacturerlist'),
            'p_page' => $this->module->l('Previous', 'mpmanufacturerlist'),
            'n_page' => $this->module->l('Next', 'mpmanufacturerlist'),
            'static_token' => Tools::getToken(false),
        ];

        Media::addJsDef($jsVars);
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = [];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Marketplace', 'mpmanufacturerlist'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        ];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Brands', 'mpmanufacturerlist'),
            'url' => '',
        ];

        return $breadcrumb;
    }

    // Delete Brand Product from Brand Product list
    public function displayAjaxDeleteManufacturerProduct()
    {
        if (!$this->isTokenValid()) {
            exit('Something went wrong!');
        }
        if (Tools::getValue('removemanufprod')) {
            $manufProductId = Tools::getValue('manufproductid');
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId(Context::getContext()->customer->id);
            $mpSellerId = $mpSeller['id_seller'];
            $manufProdData = WkMpManufacturers::getSellerProductByManufProdId($manufProductId);
            if ($manufProdData) {
                if ($mpSellerId == $manufProdData['id_seller']) {
                    WkMpManufacturers::updateProductManufByPsIdProduct(
                        $manufProductId
                    );
                    exit(json_encode(1));
                }
            }
        }
        exit('');
    }

    // Delete Bulk Product Brand from Brand Product list
    public function displayAjaxBulkDeleteManufacturerProduct()
    {
        if (!$this->isTokenValid()) {
            exit('Something went wrong!');
        }
        if (Tools::getValue('remove_bulk_manuf_prod')) {
            $idProds = Tools::getValue('idProds');
            $idProds = json_decode($idProds);
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId(Context::getContext()->customer->id);
            $mpSellerId = $mpSeller['id_seller'];
            foreach ($idProds as $id) {
                $manufProdData = WkMpManufacturers::getSellerProductByManufProdId($id);
                if ($manufProdData) {
                    if ($mpSellerId == $manufProdData['id_seller']) {
                        WkMpManufacturers::updateProductManufByPsIdProduct($id);
                    }
                }
            }
            exit(json_encode(1));
        }
        exit('');
    }

    // Delete Brand from Brand list
    public function displayAjaxDeleteManufacturer()
    {
        if (!$this->isTokenValid()) {
            exit('Something went wrong!');
        }
        if (Tools::getValue('remove_manuf')) {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId(Context::getContext()->customer->id);
            $mpSellerId = $mpSeller['id_seller'];
            $objMpManuf = new WkMpManufacturers();
            $mpManufId = Tools::getValue('mp_manuf_id');
            $objMpManuf = new WkMpManufacturers($mpManufId);
            if ($mpSellerId == $objMpManuf->id_seller) {
                $objMpManuf->delete();
                exit(json_encode(1));
            } else {
                exit(json_encode(0));
            }
        }
        exit('');
    }

    // Delete Bulk Brand from Brand list
    public function displayAjaxBulkDeleteManufacturer()
    {
        if (!$this->isTokenValid()) {
            exit('Something went wrong!');
        }
        if (Tools::getValue('remove_bulk_manuf')) {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId(Context::getContext()->customer->id);
            $mpSellerId = $mpSeller['id_seller'];
            $objMpManuf = new WkMpManufacturers();
            $mpManufIds = json_decode(Tools::getValue('manufIds'));
            foreach ($mpManufIds as $mpManufId) {
                $objMpManuf = new WkMpManufacturers($mpManufId);
                if ($mpSellerId == $objMpManuf->id_seller) {
                    $objMpManuf->delete();
                }
            }
            exit(json_encode(1));
        }
        exit('');
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->registerJavascript(
            'validate_manufacturer-js',
            'modules/' . $this->module->name . '/views/js/manufacturers/validate_manufacturer.js'
        );
        $this->registerStylesheet(
            'addmanufacturer-css',
            'modules/' . $this->module->name . '/views/css/addmanufacturer.css'
        );
        $this->registerStylesheet('marketplace_account', 'modules/marketplace/views/css/marketplace_account.css');

        // data table file included
        $this->registerStylesheet('datatables-css', 'modules/marketplace/views/css/datatable_bootstrap.css');
        $this->registerStylesheet('datatables-css', 'modules/marketplace/views/css/datatable_bootstrap.css');
        $this->registerJavascript('datatables-min-js', 'modules/marketplace/views/js/jquery.dataTables.min.js');
        $this->registerJavascript('datatables-boot-js', 'modules/marketplace/views/js/dataTables.bootstrap.js');
    }
}
