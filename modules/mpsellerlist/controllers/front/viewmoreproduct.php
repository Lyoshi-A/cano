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
class MpSellerListviewmoreproductModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Marketplace Seller List', 'viewmoreproduct'),
            'url' => $this->context->link->getModuleLink('mpsellerlist', 'sellerlist'),
        ];

        $breadcrumb['links'][] = [
            'title' => $this->module->l('All Products', 'viewmoreproduct'),
            'url' => '',
        ];

        return $breadcrumb;
    }

    public function initContent()
    {
        parent::initContent();

        $sellerlisthelper = new SellerListHelper();
        $usetax = $sellerlisthelper->useTax();

        $idLang = $this->context->language->id;

        $sortby = trim(Tools::getValue('orderby'));
        $orderby = trim(Tools::getValue('orderway'));

        if ($sortby == 'price' && $orderby == 'asc') {
            $sortOrderBy = '1';
        } elseif ($sortby == 'price' && $orderby == 'desc') {
            $sortOrderBy = '2';
        } elseif ($sortby == 'name' && $orderby == 'asc') {
            $sortby = 'product_name';
            $sortOrderBy = '3';
        } elseif ($sortby == 'name' && $orderby == 'desc') {
            $sortby = 'product_name';
            $sortOrderBy = '4';
        } else {
            $sortOrderBy = '0';
        }
        $allActiveProducts = $sellerlisthelper->getAllSellerProducts();
        if (!$allActiveProducts) {
            $allActiveProducts = [];
        }
        $sellerProductInfo = $sellerlisthelper->findAllActiveSellerProductOrderBy($idLang, 0, 16, $sortby, $orderby);
        // Display price tax Incl or excl and price hide/show according to customer group settings
        $displayPriceTaxIncl = 1;
        $showPriceByCustomerGroup = 1;
        if ($groupAccess = Group::getCurrent()) {
            if (isset($groupAccess->price_display_method) && $groupAccess->price_display_method) {
                $displayPriceTaxIncl = 0; // Display tax incl price
            }
            if (empty($groupAccess->show_prices)) {
                $showPriceByCustomerGroup = 0; // Don't display product price
            }
        }
        if ($sellerProductInfo) {
            $activeSellerProduct = count($sellerProductInfo);
            foreach ($sellerProductInfo as &$activePro) {
                $product = new Product($activePro['main_id_product'], false, $idLang);
                $coverImageId = Product::getCover($product->id);
                $activePro['link_rewrite'] = $product->link_rewrite;
                $activePro['lang_iso'] = Context::getContext()->language->iso_code;
                if ($coverImageId) {
                    $ids = $product->id . '-' . $coverImageId['id_image'];
                    $activePro['image'] = $ids;
                } else {
                    $activePro['image'] = 0;
                }
                if ($displayPriceTaxIncl) {
                    $activePro['retail_price'] = Tools::displayPrice($product->getPriceWithoutReduct());
                    $activePro['price'] = Tools::displayPrice($product->getPrice(true));
                } else {
                    $activePro['retail_price'] = Tools::displayPrice($product->getPriceWithoutReduct(true));
                    $activePro['price'] = Tools::displayPrice($product->getPrice(false));
                }
            }
            $this->context->smarty->assign([
                'seller_product_info' => $sellerProductInfo,
                'module_dir' => _MODULE_DIR_,
            ]);
        } else {
            $activeSellerProduct = 0;
        }

        $this->context->smarty->assign([
            'active_seller_product' => $activeSellerProduct,
            'default_product' => _MODULE_DIR_ . 'mpsellerlist/views/img/defaultproduct.jpg',
            'showPriceByCustomerGroup' => $showPriceByCustomerGroup,
            'count_all_active_product' => count($allActiveProducts),
            'orderby' => $sortOrderBy,
            'sortby' => $sortby,
            'orderway' => $orderby, ]);

        $this->defineJSVars();
        $this->setTemplate('module:mpsellerlist/views/templates/front/viewmoreproduct.tpl');
    }

    public function defineJSVars()
    {
        $jsVars = [
            'PS_REWRITING_SETTINGS' => Configuration::get('PS_REWRITING_SETTINGS'),
            'ajaxsort_url' => $this->context->link->getModuleLink('mpsellerlist', 'viewmoreproduct'),
            'viewmore_url' => $this->context->link->getModuleLink('mpsellerlist', 'moreproduct'),
            'no_more_prod' => $this->module->l('No more products', 'viewmoreproduct'),
        ];

        Media::addJsDef($jsVars);
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->registerStylesheet('sellerlist', 'modules/' . $this->module->name . '/views/css/sellerlist.css');
        $this->registerJavascript('sellerlistjs', 'modules/' . $this->module->name . '/views/js/sellerlist.js');

        return true;
    }
}
