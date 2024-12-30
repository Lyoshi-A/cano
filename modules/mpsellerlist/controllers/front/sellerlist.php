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
class MpSellerListsellerlistModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $link = new Link();
        $idLang = $this->context->language->id;
        $idCustomer = $this->context->cookie->id_customer;
        $objSeller = new WkMpSeller();
        if ($idCustomer) {
            $mpShopInfo = $objSeller->getSellerDetailByCustomerId($idCustomer);
            if ($mpShopInfo && $mpShopInfo['active']) {
                $this->context->smarty->assign('gotoshop_link', $link->getModuleLink('marketplace', 'dashboard'));
            }
        }

        $allActiveSeller = $objSeller->getAllSeller(0, 8, $idLang, false, false);
        if ($allActiveSeller) {
            $totalActiveSeller = count($allActiveSeller);
            foreach ($allActiveSeller as &$actSeller) {
                if ($actSeller['shop_image'] && file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/shop_img/' . $actSeller['shop_image'])) {
                    $actSeller['shop_logo'] = _MODULE_DIR_ . 'marketplace/views/img/shop_img/' . $actSeller['shop_image'];
                } else {
                    $actSeller['shop_logo'] = _MODULE_DIR_ . 'marketplace/views/img/shop_img/defaultshopimage.jpg';
                }
            }

            $this->context->smarty->assign([
                'all_active_seller' => $allActiveSeller,
            ]);
        } else {
            $totalActiveSeller = 0;
        }

        $objSellerList = new SellerListHelper();
        $allActiveProducts = $objSellerList->getAllSellerProducts();
        if (!$allActiveProducts) {
            $allActiveProducts = [];
        }
        $sellerProductInfo = $objSellerList->findAllActiveSellerProductOrderBy($idLang, 0, 8, false, 'desc');
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
                $activePro['link_rewrite'] = $product->link_rewrite;
                $activePro['lang_iso'] = Context::getContext()->language->iso_code;

                $coverImageId = Product::getCover($product->id);
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
            $this->context->smarty->assign('seller_product_info', $sellerProductInfo);
        } else {
            $activeSellerProduct = 0;
        }

        $this->context->smarty->assign([
            'mp_seller_text' => nl2br(Configuration::get('MP_SELLER_TEXT', $this->context->language->id)),
            'active_seller_product' => $activeSellerProduct,
            'count_all_active_product' => count($allActiveProducts),
            'default_product' => _MODULE_DIR_ . 'mpsellerlist/views/img/defaultproduct.jpg',
            'showPriceByCustomerGroup' => $showPriceByCustomerGroup,
            'total_active_seller' => $totalActiveSeller,
            'viewmorelist_link' => $link->getModuleLink('mpsellerlist', 'viewmorelist'),
            'viewmoreproduct_link' => $link->getModuleLink('mpsellerlist', 'viewmoreproduct'),
        ]);

        $this->defineJSVars();
        $this->setTemplate('module:mpsellerlist/views/templates/front/mpsellerlist.tpl');
    }

    public function defineJSVars()
    {
        $jsVars = [
            'viewmorelist_link' => $this->context->link->getModuleLink('mpsellerlist', 'viewmorelist'),
        ];
        Media::addJsDef($jsVars);
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->registerStylesheet('sellerlist', 'modules/' . $this->module->name . '/views/css/sellerlist.css');

        return true;
    }
}
