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

class MarketplaceProductDetailsModuleFrontController extends ModuleFrontController
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
            if ($seller && $seller['active']) {
                $mpIdProduct = Tools::getValue('id_mp_product');
                $sellerProduct = WkMpSellerProduct::getSellerProductByIdProduct(
                    $mpIdProduct,
                    $this->context->language->id
                );
                if ($sellerProduct && ($sellerProduct['id_seller'] == $seller['id_seller'])) {
                    if ($sellerProduct['active']) {
                        $idProduct = $sellerProduct['id_ps_product'];
                        $product = new Product($idProduct, false, $this->context->language->id);

                        $idPsProdAttr = Product::getDefaultAttribute((int) $idProduct);
                        $this->context->smarty->assign([
                            'link_rewrite' => $product->link_rewrite,
                            'id_product' => $idProduct,
                            'obj_product' => $product,
                            'product_link' => $this->context->link->getProductLink($product, null, null, null, null, null, $idPsProdAttr),
                            'is_approved' => 1,
                        ]);
                    }

                    // Assign and display product active/inactive images
                    WkMpSellerProductImage::getProductImageDetails($mpIdProduct);

                    WkMpHelper::assignDefaultLang($seller['id_seller']);

                    // show admin commission on product base price for seller
                    if (Configuration::get('WK_MP_SHOW_ADMIN_COMMISSION')) {
                        $objMpCommission = new WkMpCommission();
                        $adminCommission = $objMpCommission->finalCommissionSummaryForSeller($seller['id_seller']);
                        if ($adminCommission) {
                            $this->context->smarty->assign('admin_commission', $adminCommission);
                        }
                    }

                    $conversionRate = 1;
                    $idCurrencyDefault = Configuration::get('PS_CURRENCY_DEFAULT');
                    if ($idCurrencyDefault != $this->context->currency->id) {
                        $currencyOrigin = new Currency((int) $idCurrencyDefault);
                        $conversionRate /= $currencyOrigin->conversion_rate;
                        $currencySelect = new Currency((int) $this->context->currency->id);
                        $conversionRate *= $currencySelect->conversion_rate;
                    }

                    $sellerProduct['price'] = WkMpHelper::displayPrice($sellerProduct['price'] * $conversionRate);

                    $this->context->smarty->assign([
                        'product' => $sellerProduct,
                        'logic' => 3,
                        'is_seller' => 1,
                        'static_token' => Tools::getToken(false),
                    ]);
                    $idPsProduct = $sellerProduct['id_ps_product'];
                    if (Configuration::get('WK_MP_PACK_PRODUCTS')
                        && isset($idPsProduct)
                        && Pack::isPack($idPsProduct)) {
                        $packProducts = Pack::getItems($idPsProduct, Context::getContext()->language->id);
                        foreach ($packProducts as $key => &$value) {
                            $psIdProd = $value->id;
                            if ($psIdProd) {
                                $product_obj = new Product($psIdProd, false, $this->context->language->id);
                                $value->link_rewrite = $product_obj->link_rewrite;
                                $psProdAttrId = $value->id_pack_product_attribute;
                                $value->image_link = $this->getProductImageIdInPack($psIdProd, $psProdAttrId);
                            }
                        }

                        $this->context->smarty->assign([
                            'isPackProduct' => 1,
                            'packProducts' => $packProducts,
                        ]);
                    }

                    $this->defineJsVars();
                    $this->setTemplate('module:marketplace/views/templates/front/product/productdetails.tpl');
                } else {
                    $this->context->smarty->assign([
                        'logic' => 3,
                        'mp_error_message' => $this->module->l('Product not found. Something went wrong.', 'productdetails'),
                    ]);
                    $this->setTemplate('module:marketplace/views/templates/front/product/productdetails.tpl');
                }
            } else {
                Tools::redirect($this->context->link->getModuleLink('marketplace', 'sellerrequest'));
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function getProductImageIdInPack($idProduct, $idProductAttribute)
    {
        $idImage = 0;
        $objProduct = new Product($idProduct, true, $this->context->language->id);
        if ($idProductAttribute) {
            $idImageArr = Product::getCombinationImageById($idProductAttribute, (int) $this->context->cookie->id_lang);
            $idImage = $idImageArr['id_image'];
            if (!$idImage) {
                $cover = Product::getCover($idProduct);
                $idImage = $cover['id_image'];
            }
        } else {
            $cover = Product::getCover($idProduct);
            $idImage = $cover['id_image'];
        }
        $objImage = new Image($idImage);
        if (Validate::isLoadedObject($objImage)) {
            $productImg = $this->context->link->getImageLink(
                $objProduct->link_rewrite,
                $idProduct . '-' . $idImage,
                ImageType::getFormattedName('home')
            );
        } else {
            $productImg =
            _PS_IMG_ . 'p/' . $this->context->language->iso_code . '-default-' .
            ImageType::getFormattedName('home') . '.jpg';
        }

        return $productImg;
    }

    public function defineJsVars()
    {
        $jsVars = [
            'ajax_urlpath' => $this->context->link->getModuleLink('marketplace', 'productimageedit'),
            'image_drag_drop' => 1,
            'space_error' => $this->module->l('Space is not allowed.', 'productdetails'),
            'confirm_delete_msg' => $this->module->l('Are you sure you want to delete this image?', 'productdetails'),
            'delete_msg' => $this->module->l('Deleted.', 'productdetails'),
            'error_msg' => $this->module->l('An error occurred.', 'productdetails'),
            'update_success' => $this->module->l('Updated successfully.', 'productdetails'),
            'languages' => Language::getLanguages(),
            'ImageCaptionLangError' => $this->module->l('Image caption field is invalid in', 'productdetails'),
        ];

        Media::addJsDef($jsVars);
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = [];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Marketplace', 'productdetails'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        ];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Products', 'productdetails'),
            'url' => $this->context->link->getModuleLink('marketplace', 'productlist'),
        ];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Product details', 'productdetails'),
            'url' => '',
        ];

        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addjQueryPlugin('growl', null, false);
        $this->registerJavascript(
            'mp-imageedit',
            'modules/' . $this->module->name . '/views/js/imageedit.js'
        );
        $this->registerStylesheet(
            'marketplace_account',
            'modules/' . $this->module->name . '/views/css/marketplace_account.css'
        );
        $this->registerStylesheet(
            'mp-product-details',
            'modules/' . $this->module->name . '/views/css/mpproductdetails.css'
        );
        $this->registerJavascript(
            'mp-table-dnd',
            'modules/' . $this->module->name . '/views/js/table-dnd.js'
        );
    }
}
