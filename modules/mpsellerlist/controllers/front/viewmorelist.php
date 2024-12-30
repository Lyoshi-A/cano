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
class MpSellerListviewmorelistModuleFrontController extends ModuleFrontController
{
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Marketplace Seller List', 'viewmorelist'),
            'url' => $this->context->link->getModuleLink('mpsellerlist', 'sellerlist'),
        ];

        $breadcrumb['links'][] = [
            'title' => $this->module->l('All Sellers', 'viewmorelist'),
            'url' => '',
        ];

        return $breadcrumb;
    }

    public function initContent()
    {
        parent::initContent();
        $link = new Link();
        $objSeller = new WkMpSeller();
        $viewmorelistLink = $link->getModuleLink('mpsellerlist', 'viewmorelist');
        $alp = Tools::getValue('alp');
        $orderby = trim(Tools::getValue('orderby'));
        $key = trim(Tools::getValue('name'));
        $allActiveSeller = [];
        if ($alp) {
            $this->context->smarty->assign('alph', $alp);
            $allActiveSeller = $objSeller->getAllSeller(false, false, $this->context->language->id, true, false, $alp);
        } else {
            if ($orderby && $key) {
                if ($orderby == 'address' || $orderby == 'shop_name' || $orderby == 'seller_name') {
                    $sellerlisthelper = new SellerListHelper();
                    $allActiveSeller = $sellerlisthelper->findAllActiveSellerBySearch($orderby, $key, $this->context->language->id);
                    $this->context->smarty->assign('alph', '0');
                } else {
                    Tools::redirect($viewmorelistLink);
                }
            } else {
                $allActiveSeller = $objSeller->getAllSeller(false, false, $this->context->language->id, true, true, '');
                $this->context->smarty->assign('alph', '0');
            }
        }
        if ($allActiveSeller && count($allActiveSeller) > 0) {
            $totalActiveSeller = count($allActiveSeller);
            foreach ($allActiveSeller as &$actSeller) {
                if ($actSeller['shop_image'] && file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/shop_img/' . $actSeller['shop_image'])) {
                    $actSeller['shop_logo'] = _MODULE_DIR_ . 'marketplace/views/img/shop_img/' . $actSeller['shop_image'];
                } else {
                    $actSeller['shop_logo'] = _MODULE_DIR_ . 'marketplace/views/img/shop_img/defaultshopimage.jpg';
                }
            }
            $this->context->smarty->assign('all_active_seller', $allActiveSeller);
        } else {
            $totalActiveSeller = 0;
        }
        $wkCapAlphabet = ['a' => 'A', 'b' => 'B', 'c' => 'C', 'd' => 'D', 'e' => 'E', 'f' => 'F', 'g' => 'G', 'h' => 'H', 'i' => 'I', 'j' => 'J', 'k' => 'K', 'l' => 'L', 'm' => 'M', 'n' => 'N', 'o' => 'O', 'p' => 'P', 'q' => 'Q', 'r' => 'R', 's' => 'S', 't' => 'T', 'u' => 'U', 'v' => 'V', 'w' => 'W', 'x' => 'X', 'y' => 'Y', 'z' => 'Z'];

        $this->context->smarty->assign([
            'viewmorelist_link' => $viewmorelistLink,
            'total_active_seller' => $totalActiveSeller,
            'wkCapAlphabet' => $wkCapAlphabet,
            'friendly_url' => Configuration::get('PS_REWRITING_SETTINGS'),
        ]);

        $this->defineJSVars();
        $this->setTemplate('module:mpsellerlist/views/templates/front/viewmorelist.tpl');
    }

    public function defineJSVars()
    {
        $jsVars = [
            'ajaxsearch_url' => $this->context->link->getModuleLink('mpsellerlist', 'ajaxsellersearch'),
            'viewmorelist_link' => $this->context->link->getModuleLink('mpsellerlist', 'viewmorelist'),
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
