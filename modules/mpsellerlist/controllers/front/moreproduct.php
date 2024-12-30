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
class MpSellerListmoreproductModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $link = new Link();
        $idLang = $this->context->language->id;
        $nextid = Tools::getValue('nextid');
        $orderby = Tools::getValue('orderby');
        $orderway = Tools::getValue('orderway');

        $sellerlisthelper = new SellerListHelper();
        $usetax = $sellerlisthelper->useTax();
        $sellerProductInfo = $sellerlisthelper->findAllActiveSellerProductOrderBy($idLang, $nextid, 8, $orderby, $orderway);
        $html = '';
        if (!empty($sellerProductInfo)) {
            $viewMore = 1;
            $countAllProd = $sellerlisthelper->getAllSellerProducts(true);
            if (((int) $nextid + 8) >= $countAllProd) {
                $viewMore = 0;
            }
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
            foreach ($sellerProductInfo as $activePro) {
                $product = new Product($activePro['main_id_product'], false, $idLang);
                $productLink = $this->context->link->getProductLink($activePro['main_id_product']);
                $productName = $activePro['product_name'];
                $coverImageId = Product::getCover($product->id);
                if ($coverImageId) {
                    $ids = $product->id . '-' . $coverImageId['id_image'];
                    $prductImgLink = Tools::getShopProtocol() . $link->getImageLink($product->link_rewrite, $ids, $product->getType());
                } else {
                    $prductImgLink = _MODULE_DIR_ . 'mpsellerlist/views/img/defaultproduct.jpg';
                }

                if ($displayPriceTaxIncl) {
                    $productPriceRetail = Tools::displayPrice($product->getPriceWithoutReduct());
                    $productPrice = Tools::displayPrice($product->getPrice(true));
                } else {
                    $productPriceRetail = Tools::displayPrice($product->getPriceWithoutReduct(true));
                    $productPrice = Tools::displayPrice($product->getPrice(false));
                }
                $priceHtml = '<div class="mp-product-price">';
                if ($activePro['show_price'] && $showPriceByCustomerGroup) {
                    $priceHtml .= $productPrice;
                    if ($productPrice != $productPriceRetail) {
                        $priceHtml .= '<span class="wk_retail_price">';
                        $priceHtml .= $productPriceRetail;
                        $priceHtml .= '</span>';
                    }
                }
                $priceHtml .= '</div>';
                $html .= "<div class='col-lg-3 col-md-4 col-xs-6 thumb' id='" . $activePro['id_mp_product'] . "'>
                            <a class='thumbnail' href='" . $productLink . "'>
                                <img class='img-responsive' src='" . $prductImgLink . "' title='" . $productName . "' style='height:240px;'>
                            </a>
                            <div class='wk_seller_details'>
                                <p class='wk_seller_name'>" . $productName . '</p>
                               ' . $priceHtml . "
                                <a href='" . $productLink . "' class='btn btn-default btn_product_shop'>View</a>
                            </div>
                        </div>";
            }
            $return = [];
            $return['html'] = $html;
            $return['view_more'] = $viewMore;
            exit(json_encode($return));
        } else {
            exit('0');
        }
    }
}
