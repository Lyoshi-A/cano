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
class SellerListHelper
{
    public function getAllSellerProducts($count = false, $active = 1)
    {
        $result = WkMpSellerProduct::getSellerProduct(false, 'all');
        if ($result) {
            if ($count) {
                return count($result);
            } else {
                return $result;
            }
        }

        return false;
    }

    public function findAllActiveSellerProductOrderBy(
        $idLang,
        $startPoint = 0,
        $limitPoint = 8,
        $orderby = false,
        $orderway = false
    ) {
        $isPrice = 0;
        if (!$orderby) {
            $orderby = 'msp.`id_mp_product`';
        } elseif ($orderby == 'product_name') {
            $orderby = 'pl.`name`';
        } elseif ($orderby == 'price') {
            $isPrice = 1;
            $orderby = 'p.`price`';
        }
        if (!$orderway) {
            $orderway = 'DESC';
        }
        $sql = 'SELECT *,  pl.`id_product` as `main_id_product`, pl.`name` as `product_name` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_product` msp
        JOIN `' . _DB_PREFIX_ . 'product` p
        ON (p.`id_product` = msp.`id_ps_product`)' . Shop::addSqlAssociation('product', 'p') . '
        JOIN `' . _DB_PREFIX_ . 'product_lang` pl
        ON (p.`id_product` = pl.`id_product`' . Shop::addSqlRestrictionOnLang('pl') . ')';
        $sql .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_mp_seller` mpsi ON (mpsi.`id_seller` = msp.`id_seller`)';
        $sql .= ' WHERE pl.`id_lang` = ' . (int) $idLang;
        $sql .= ' AND product_shop.`active` = 1';
        $objMarketPlace = Module::getInstanceByName('marketplace');
        if ($objMarketPlace->version >= '5.4.0') {
            $sql .= WkMpSeller::addSqlRestriction('mpsi');
        }
        $sql .= ' ORDER BY ' . pSQL($orderby) . ' ' . pSQL($orderway);
        $sql .= ' LIMIT ' . (int) $startPoint . ', ' . (int) $limitPoint;
        $sellerProduct = Db::getInstance()->executeS($sql);
        if ($isPrice) {
            if (!empty($sellerProduct)) {
                self::orderbyPrice($sellerProduct, $orderway);
            }
        }
        if (empty($sellerProduct)) {
            return false;
        }

        return $sellerProduct;
    }

    public static function orderbyPrice(&$array, $orderWay)
    {
        foreach ($array as &$row) {
            $row['price_tmp'] = Product::getPriceStatic($row['id_ps_product'], true, null, 2);
        }

        unset($row);

        if (Tools::strtolower($orderWay) == 'desc') {
            uasort($array, 'cmpPriceDesc');
        } else {
            uasort($array, 'cmpPriceAsc');
        }
        foreach ($array as &$row) {
            unset($row['price_tmp']);
        }
    }

    public function cmpPriceAsc($a, $b)
    {
        if ((float) $a['price_tmp'] < (float) $b['price_tmp']) {
            return -1;
        } elseif ((float) $a['price_tmp'] > (float) $b['price_tmp']) {
            return 1;
        }

        return 0;
    }

    public function cmpPriceDesc($a, $b)
    {
        if ((float) $a['price_tmp'] < (float) $b['price_tmp']) {
            return 1;
        } elseif ((float) $a['price_tmp'] > (float) $b['price_tmp']) {
            return -1;
        }

        return 0;
    }

    public function findAllActiveSellerBySearch($searchFor, $key, $idLang)
    {
        $sql = 'SELECT msi.*,msi.`id_seller` AS mp_shop_id, msi.`link_rewrite`
        AS shop_link_rewrite, CONCAT(msi.`seller_firstname`, \' \', msi.`seller_lastname`)
        AS mp_seller_name, msil.`shop_name` AS shop_name, msi.`address`
        AS mp_shop_adr, msi.`seller_customer_id` AS id_customer
                FROM `' . _DB_PREFIX_ . 'wk_mp_seller` msi
                INNER JOIN `' . _DB_PREFIX_ . 'wk_mp_seller_lang` msil
                ON (msil.`id_seller` = msi.`id_seller`
                AND msil.`id_lang` = ' . (int) $idLang . ')
                LEFT JOIN ' . _DB_PREFIX_ . 'country_lang AS cl
                ON (cl.`id_country` = msi.`id_country` AND cl.`id_lang` = ' . (int) $idLang . ')
                LEFT JOIN ' . _DB_PREFIX_ . 'state AS sl ON (sl.`id_state` = msi.`id_state`)
                WHERE msi.active = 1';
        $objMarketPlace = Module::getInstanceByName('marketplace');
        if ($objMarketPlace->version >= '5.4.0') {
            $sql .= WkMpSeller::addSqlRestriction() . ' AND ';
        } else {
            $sql .= ' AND ';
        }
        if ($searchFor == 'seller_name') {
            $sql .= '(msi.`seller_firstname` LIKE "%' . pSQL($key) . '%"
                OR msi.`seller_lastname` LIKE "%' . pSQL($key) . '%"
                OR CONCAT(msi.`seller_firstname`, \' \', msi.`seller_lastname`) LIKE "%' . pSQL($key) . '%"
            )';
        } elseif ($searchFor == 'address') {
            $sql .= '(msi.' . $searchFor . ' LIKE "%' . pSQL($key) . '%"
            OR cl.`name` LIKE "%' . pSQL($key) . '%"
            OR sl.`name` LIKE "%' . pSQL($key) . '%"
            OR msi.`city` LIKE "%' . pSQL($key) . '%")';
        } else {
            $sql .= 'msil.' . $searchFor . ' LIKE "%' . pSQL($key) . '%"';
        }

        $result = Db::getInstance()->executeS($sql);
        if ($result) {
            return $result;
        }

        return false;
    }

    public static function useTax()
    {
        $priceTax = false;
        $priceDisplay = Group::getPriceDisplayMethod(Group::getCurrent()->id);
        if (!$priceDisplay || $priceDisplay == 2) {
            $priceTax = true;
        } elseif ($priceDisplay == 1) {
            $priceTax = false;
        }

        return $priceTax;
    }
}
