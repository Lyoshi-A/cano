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

class MarketplaceMpShippingListModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $link = new Link();
        $idCustomer = $this->context->customer->id;
        if ($idCustomer) {
            $mpCustomerInfo = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($mpCustomerInfo && $mpCustomerInfo['active'] && Configuration::get('WK_MP_SELLER_SHIPPING')) {
                $idSeller = $mpCustomerInfo['id_seller'];
                $objMpShipping = new WkMpSellerShipping();
                // Only active shipping methods
                $mpShippingActive = $objMpShipping->getMpShippingMethods($idSeller);
                if ($mpShippingActive) {
                    $this->context->smarty->assign('mp_shipping_active', $mpShippingActive);
                }
                // Get other shipping method before deleting
                $deleteAction = Tools::getValue('delete_action');
                if ($deleteAction) {
                    $mpShippingId = Tools::getValue('mpshipping_id');
                    $returnArr = [];
                    if ($mpShippingActive) {
                        $returnArr = array_filter($mpShippingActive, function ($mSA) use ($mpShippingId) {
                            return (int) $mSA['id'] !== (int) $mpShippingId;
                        });
                    }
                    exit(json_encode(array_values($returnArr)));
                }

                // if shipping is assigned on product and going to delete
                if (Tools::isSubmit('submit_extra_shipping')) {
                    $oldShippingId = Tools::getValue('delete_shipping_id');
                    $objMpShippingNew = new WkMpSellerShipping($oldShippingId);
                    if ($objMpShippingNew->id_seller == $idSeller) {
                        $newShippingId = Tools::getValue('extra_shipping');
                        $objCarrier = new Carrier((int) $objMpShippingNew->id_ps_reference);
                        $mpProdMap = $objMpShippingNew->getMpShippingForProducts((int) $objMpShippingNew->id_ps_reference);
                        $carrierArr = [];
                        if (isset($newShippingId) && $newShippingId) {
                            // Assign other selected carrier on products
                            $carrierArr[] = WkMpSellerShipping::getReferenceByMpShippingId($newShippingId);
                            if ($mpProdMap) {
                                foreach ($mpProdMap as $mpProd) {
                                    if ($psProductId = $mpProd['id_product']) {
                                        $objProduct = new Product((int) $psProductId);
                                        $objProduct->setCarriers($carrierArr);
                                    }
                                }
                            }
                            $objMpShippingNew->deleteMpShipping($oldShippingId); // delete shipping all data
                        } else {
                            // If no carrier available then admin default first carrier will assign
                            if ($mpProdMap) {
                                $adminDefShipping = json_decode(Configuration::get('MP_SHIPPING_ADMIN_DEFAULT'));
                                if ($adminDefShipping) {
                                    $carrierArr[] = $adminDefShipping[0]; // first admin shipping
                                    foreach ($mpProdMap as $mpProd) {
                                        if ($psProductId = $mpProd['id_product']) {
                                            $objProduct = new Product((int) $psProductId);
                                            $objProduct->setCarriers($carrierArr);
                                        }
                                    }
                                }
                            }
                            $objMpShippingNew->deleteMpShipping($oldShippingId); // delete shipping all data
                        }
                        Tools::redirect(
                            $this->context->link->getModuleLink(
                                'marketplace',
                                'mpshippinglist',
                                ['delete_success' => 1]
                            )
                        );
                    } else {
                        Tools::redirect($this->context->link->getModuleLink('marketplace', 'mpshippinglist'));
                    }
                }

                // Delete Shipping method from front
                $deleteShipping = Tools::getValue('delete_shipping');
                if ($deleteShipping) {
                    if ($mpShippingId = Tools::getValue('mpshipping_id')) {
                        $objMpShippingNew = new WkMpSellerShipping($mpShippingId);
                        if ($objMpShippingNew->id_seller == $idSeller) {
                            $objMpShippingNew->deleteMpShipping($mpShippingId);
                            Tools::redirect(
                                $this->context->link->getModuleLink(
                                    'marketplace',
                                    'mpshippinglist',
                                    ['delete_success' => 1]
                                )
                            );
                        } else {
                            Tools::redirect($this->context->link->getModuleLink('marketplace', 'mpshippinglist'));
                        }
                    }
                }

                // Only default shipping methods
                $mpShippingDefault = $objMpShipping->getDefaultMpShippingMethods($idSeller);
                if ($mpShippingDefault) {
                    $defaultShippingName = '';
                    foreach ($mpShippingDefault as $defkey => $mpShippingDef) {
                        $shippingName = $mpShippingDef['name'] . ' (' . $mpShippingDef['id'] . ')';
                        if ($defkey == 0) {
                            $defaultShippingName = $shippingName;
                        } else {
                            $defaultShippingName = $shippingName . ', ' . $defaultShippingName;
                        }
                    }

                    $this->context->smarty->assign('default_shipping_name', $defaultShippingName);
                }

                // show all shipping method which was not deleted in shipping list
                $mpShippingDetail = $objMpShipping->getAllShippingMethodNotDelete($idSeller, 0);
                if ($mpShippingDetail) {
                    $k = 0;
                    foreach ($mpShippingDetail as $mpShipping) {
                        $carrierLogo = _PS_SHIP_IMG_DIR_ . '/' . (int) $mpShipping['id_carrier'] . '.jpg';
                        if (file_exists($carrierLogo)) {
                            $mpShippingDetail[$k]['image_exist'] = 1;
                        } else {
                            $mpShippingDetail[$k]['image_exist'] = 0;
                        }

                        $mpShippingProdMap = $objMpShipping->getMpShippingForProducts($mpShipping['id_ps_reference']);
                        if ($mpShippingProdMap) {
                            $mpShippingDetail[$k]['shipping_on_product'] = 1;
                        } else {
                            $mpShippingDetail[$k]['shipping_on_product'] = 0;
                        }

                        $objShop = new Shop((int) $mpShipping['id_shop']);
                        $mpShippingDetail[$k]['ps_shop_name'] = $objShop->name;
                        unset($objShop);

                        ++$k;
                    }

                    $this->context->smarty->assign('mp_shipping_detail', $mpShippingDetail);
                }

                $shareCustomerEnabled = false;
                if ($this->context->shop->id_shop_group) {
                    $objShopGroup = new ShopGroup((int) $this->context->shop->id_shop_group);
                    $shareCustomerEnabled = $objShopGroup->share_customer;
                }
                $this->context->smarty->assign('shareCustomerEnabled', $shareCustomerEnabled);
                $this->context->smarty->assign('isMultiShopEnabled', Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE'));
                $this->context->smarty->assign('currentShopId', Context::getContext()->shop->id);
                $this->context->smarty->assign('updatempshipping_success', Tools::getValue('updatempshipping_success'));
                $this->context->smarty->assign('default_shipping_link', $link->getModuleLink('marketplace', 'updatedefaultShipping'));
                $this->context->smarty->assign('logic', 'mp_carriers');
                $this->context->smarty->assign('title_text_color', Configuration::get('WK_MP_TITLE_TEXT_COLOR'));
                $this->context->smarty->assign('title_bg_color', Configuration::get('WK_MP_TITLE_BG_COLOR'));

                $this->defineJSVars();
                $this->setTemplate('module:marketplace/views/templates/front/shipping/mpshippinglist.tpl');
            } else {
                Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect('index.php?controller=authentication&back=' . urlencode($this->context->link->getModuleLink('marketplace', 'mpshippinglist')));
        }
    }

    public function displayAjaxUpdatedefaultShipping()
    {
        if (!$this->isTokenValid()) {
            exit('Something went wrong!');
        }
        $idCustomer = $this->context->customer->id;
        if ($idCustomer) {
            $defaultShipping = Tools::getValue('default_shipping');
            if ($defaultShipping) {
                $mpCustomerInfo = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
                if ($mpCustomerInfo && $mpCustomerInfo['active']) {
                    $idMpSeller = $mpCustomerInfo['id_seller'];
                    $objMpShipping = new WkMpSellerShipping();
                    $alreadyDefaultShipping = $objMpShipping->getDefaultMpShippingMethods($idMpSeller);
                    if ($alreadyDefaultShipping) {
                        foreach ($alreadyDefaultShipping as $mpShipping) {
                            WkMpSellerShipping::updateDefaultShipping($mpShipping['id'], 0);
                        }
                    }

                    foreach ($defaultShipping as $defaultShippingId) {
                        $shippingSellerId = WkMpSellerShipping::getSellerIdByMpShippingId($defaultShippingId);
                        if ($shippingSellerId == $idMpSeller) {
                            WkMpSellerShipping::updateDefaultShipping($defaultShippingId, 1);
                        }
                    }
                }
                exit('1');
            } else {
                exit('2');
            }
        }
    }

    public function displayAjaxAssignShipping()
    {
        if (!$this->isTokenValid()) {
            exit('Something went wrong!');
        }
        $mpShippingMethods = Tools::getValue('shipping_method');
        $mpIdSeller = Tools::getValue('mp_id_seller');
        $mpSellerProducts = WkMpSellerProduct::getSellerAllShopProduct($mpIdSeller);
        $mpSeller = WkMpSeller::getSellerDetailByCustomerId(Context::getContext()->customer->id);
        $contextSellerId = $mpSeller['id_seller'];
        if ($contextSellerId != $mpIdSeller) {
            exit('2');
        }
        $error = [];
        if ($mpSellerProducts && $mpShippingMethods) {
            foreach ($mpSellerProducts as $mpPro) {
                $carriers = [];
                foreach ($mpShippingMethods as $idPsReference) {
                    $mpShippingId = (int) WkMpSellerShipping::getMpShippingId($idPsReference);
                    if ($mpShippingId) {
                        $shippingSellerId = WkMpSellerShipping::getSellerIdByMpShippingId($mpShippingId);
                        if ($shippingSellerId == $mpIdSeller) {
                            $carriers[] = $idPsReference;
                        }
                    } else {
                        $carriers[] = $idPsReference;
                    }
                }

                $objProduct = new Product((int) $mpPro['id_ps_product']);
                $objProduct->setCarriers($carriers);
            }
        }

        if (empty($error)) {
            echo 1;
        } else {
            echo 0;
        }
        exit; // ajax close
    }

    public function displayAjaxBulkDeleteCarrier()
    {
        if (!$this->isTokenValid()) {
            exit('Something went wrong!');
        }
        if (Tools::getValue('remove_bulk_carrier')) {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId(Context::getContext()->customer->id);
            $mpSellerId = $mpSeller['id_seller'];
            $mpShippingIds = json_decode(Tools::getValue('mp_shipping_IDs'));
            foreach ($mpShippingIds as $mpShippingID) {
                $objMpShipping = new WkMpSellerShipping($mpShippingID);
                if ($mpSellerId == $objMpShipping->id_seller) {
                    $objMpShipping->deleteMpShipping($mpShippingID);
                }
            }
            exit(json_encode(1));
        }
        exit('');
    }

    public function defineJSVars()
    {
        $jsVars = [
            'ajaxurl_shipping_extra' => $this->context->link->getModuleLink('marketplace', 'mpshippinglist'),
            'wk_dataTables' => 1,
            'static_token' => Tools::getToken(false),
            'confirm_msg' => $this->module->l('Are you sure?', 'mpshippinglist'),
            'display_name' => $this->module->l('Display', 'mpshippinglist'),
            'records_name' => $this->module->l('records per page', 'mpshippinglist'),
            'no_product' => $this->module->l('No shipping found', 'mpshippinglist'),
            'show_page' => $this->module->l('Showing page', 'mpshippinglist'),
            'show_of' => $this->module->l('of', 'mpshippinglist'),
            'no_record' => $this->module->l('No records', 'mpshippinglist'),
            'filter_from' => $this->module->l('filtered from', 'mpshippinglist'),
            't_record' => $this->module->l('total records', 'mpshippinglist'),
            'search_item' => $this->module->l('Search', 'mpshippinglist'),
            'p_page' => $this->module->l('Previous', 'mpshippinglist'),
            'n_page' => $this->module->l('Next', 'mpshippinglist'),
            'updated_default_shipping' => $this->module->l('Default shipping updated successfully.', 'mpshippinglist'),
            'no_shipping' => $this->module->l('No shipping found.', 'mpshippinglist'),
        ];

        Media::addJsDef($jsVars);
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = [];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Marketplace', 'mpshippinglist'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        ];

        $breadcrumb['links'][] = [
            'title' => $this->module->l('Carriers', 'mpshippinglist'),
            'url' => '',
        ];

        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryPlugin('fancybox');
        $this->addjQueryPlugin('growl', null, false);
        $this->registerStylesheet('marketplace_account', 'modules/' . $this->module->name . '/views/css/marketplace_account.css');
        $this->registerStylesheet('mpshippinglistcss', 'modules/' . $this->module->name . '/views/css/shipping/mpshippinglist.css');
        $this->registerJavascript('mpshippinglistjs', 'modules/' . $this->module->name . '/views/js/shipping/mpshippinglist.js');

        // data table file included
        $this->registerStylesheet('datatable_bootstrap', 'modules/' . $this->module->name . '/views/css/datatable_bootstrap.css');
        $this->registerJavascript('mp-jquery-dataTables', 'modules/' . $this->module->name . '/views/js/jquery.dataTables.min.js');
        $this->registerJavascript('mp-dataTables.bootstrap', 'modules/' . $this->module->name . '/views/js/dataTables.bootstrap.js');

        return true;
    }
}
