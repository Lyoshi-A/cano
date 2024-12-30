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
class MpSellerListAjaxSellerSearchModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $this->display_header = false;
        $this->display_footer = false;

        $key = Tools::getValue('key');
        $searchType = (int) Tools::getValue('search_type');

        $searchFor = '';
        if ($searchType == 1) {
            $searchFor = 'seller_name';
        } elseif ($searchType == 2) {
            $searchFor = 'shop_name';
        } elseif ($searchType == 3) {
            $searchFor = 'address';
        }

        $context = Context::getContext();
        $idLang = $context->language->id;

        $sql = 'SELECT DISTINCT  msi.`id_seller` AS mp_id_shop, msi.`link_rewrite` AS shop_link_rewrite, CONCAT(msi.`seller_firstname`, \' \', msi.`seller_lastname`) AS mp_seller_name, msil.`shop_name` AS shop_name, msi.`address` AS mp_shop_adr
                FROM ' . _DB_PREFIX_ . 'wk_mp_seller AS msi
                INNER JOIN ' . _DB_PREFIX_ . 'wk_mp_seller_lang AS msil ON (msil.id_seller = msi.id_seller AND msil.id_lang = ' . (int) $context->language->id . ')
                LEFT JOIN ' . _DB_PREFIX_ . 'country_lang AS cl ON (cl.`id_country` = msi.`id_country` AND cl.`id_lang` = ' . (int) $idLang . ')
                LEFT JOIN ' . _DB_PREFIX_ . 'state AS s ON (s.`id_state` = msi.`id_state`)
                WHERE msi.active = 1';
        $objMarketPlace = Module::getInstanceByName('marketplace');
        if ($objMarketPlace->version >= '5.4.0') {
            $sql .= WkMpSeller::addSqlRestriction() . ' AND ';
        } else {
            $sql .= ' AND ';
        }
        if ($searchType == 1) {
            $sql .= '(msi.`seller_firstname` LIKE "%' . pSQL($key) . '%"
                OR msi.`seller_lastname` LIKE "%' . pSQL($key) . '%"
                OR CONCAT(msi.`seller_firstname`, \' \', msi.`seller_lastname`) LIKE "%' . pSQL($key) . '%"
            )';
        } elseif ($searchType == 2) {
            $sql .= 'msil.' . $searchFor . ' LIKE "%' . pSQL($key) . '%"';
        } elseif ($searchType == 3) {
            $sql .= '(msi.' . $searchFor . ' LIKE "%' . pSQL($key) . '%"
                OR msi.`city` LIKE "%' . pSQL($key) . '%"
                OR cl.`name` LIKE "%' . pSQL($key) . '%"
                OR s.`name`  LIKE "%' . pSQL($key) . '%")';
        }
        $result = Db::getInstance()->executeS($sql);
        if ($result) {
            foreach ($result as $key => $value) {
                $shopLink = $context->link->getModuleLink(
                    'marketplace',
                    'shopstore',
                    ['mp_shop_name' => $value['shop_link_rewrite']]
                );
                $result[$key]['shop_link'] = $shopLink;
            }
            echo json_encode($result);
        } else {
            echo false;
        }
    }
}
