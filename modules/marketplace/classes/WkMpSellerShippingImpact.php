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

class WkMpSellerShippingImpact extends ObjectModel
{
    public $id_wk_mp_shipping_impact;
    public $mp_shipping_id;
    public $shipping_delivery_id;
    public $id_zone;
    public $id_country;
    public $id_state;
    public $impact_price;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'wk_mp_seller_shipping_impact',
        'primary' => 'id_wk_mp_shipping_impact',
        'fields' => [
            'mp_shipping_id' => ['type' => self::TYPE_INT, 'required' => true],
            'shipping_delivery_id' => ['type' => self::TYPE_INT, 'required' => true],
            'id_zone' => ['type' => self::TYPE_INT, 'required' => true],
            'id_country' => ['type' => self::TYPE_INT, 'required' => true],
            'id_state' => ['type' => self::TYPE_INT],
            'impact_price' => ['type' => self::TYPE_FLOAT, 'required' => true],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
        ],
    ];

    public function getImpactPriceWithRange($totalOrderPriceWt, $mpShippingId, $idCarrier, $idZone, $idCountry, $idState, $byPrice = 0, $noState = 0)
    {
        if ($byPrice) {
            $shipDelId = Db::getInstance()->getValue(
                'SELECT `id_range_price` FROM `' . _DB_PREFIX_ . 'range_price` r
                INNER JOIN `' . _DB_PREFIX_ . 'carrier_zone` cz ON (r.`id_carrier`=cz.`id_carrier`)
                WHERE r.`id_carrier`=' . (int) $idCarrier . ' AND cz.`id_zone`=' . (int) $idZone . ' AND `delimiter1`<=' . (float) $totalOrderPriceWt . ' AND `delimiter2`>' . (float) $totalOrderPriceWt
            );
        } else {
            $shipDelId = Db::getInstance()->getValue(
                'SELECT `id_range_weight` FROM `' . _DB_PREFIX_ . 'range_weight` r
                INNER JOIN `' . _DB_PREFIX_ . 'carrier_zone` cz ON (r.`id_carrier`=cz.`id_carrier`)
                WHERE r.`id_carrier`=' . (int) $idCarrier . ' AND cz.`id_zone`=' . (int) $idZone . ' AND `delimiter1`<=' . (float) $totalOrderPriceWt . ' AND `delimiter2`>' . (float) $totalOrderPriceWt
            );
        }
        if ($noState) {
            $sql = 'SELECT `impact_price` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping_impact` WHERE `mp_shipping_id`=' . (int) $mpShippingId . ' AND `id_zone`=' . (int) $idZone . ' AND `id_country`=' . (int) $idCountry . ' AND `shipping_delivery_id`=' . (int) $shipDelId;
        } else {
            $curIdState = Db::getInstance()->getValue('SELECT `id_state` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping_impact` WHERE `mp_shipping_id`=' . (int) $mpShippingId . ' AND `id_zone`=' . (int) $idZone . ' AND `id_country`=' . (int) $idCountry . ' AND `id_state`=' . (int) $idState . ' AND `shipping_delivery_id`=' . (int) $shipDelId);

            if ($curIdState && $curIdState == $idState) {
                $sql = 'SELECT `impact_price` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping_impact` WHERE `mp_shipping_id`=' . (int) $mpShippingId . ' AND `id_zone`=' . (int) $idZone . ' AND `id_country`=' . (int) $idCountry . ' AND `id_state`=' . (int) $idState . ' AND `shipping_delivery_id`=' . (int) $shipDelId;
            } else {
                $sql = 'SELECT `impact_price` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping_impact` WHERE `mp_shipping_id`=' . (int) $mpShippingId . ' AND `id_zone`=' . (int) $idZone . ' AND `id_country`=' . (int) $idCountry . ' AND `id_state`=0 AND `shipping_delivery_id`=' . (int) $shipDelId;
            }
        }
        $result = Db::getInstance()->getValue($sql);
        if ($result) {
            return $result;
        }

        return 0;
    }

    public function getCountriesByZoneId($idZone, $idLang)
    {
        $sql = 'SELECT DISTINCT c.id_country, cl.name
				FROM `' . _DB_PREFIX_ . 'country` c
				' . Shop::addSqlAssociation('country', 'c', false) . '
				LEFT JOIN `' . _DB_PREFIX_ . 'state` s ON (s.`id_country` = c.`id_country`)
				LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` cl ON (c.`id_country` = cl.`id_country`)
				WHERE (c.`id_zone` = ' . (int) $idZone . ' OR s.`id_zone` = ' . (int) $idZone . ')
				AND cl.`id_lang` = ' . (int) $idLang . ' AND c.`active` = 1';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    public function getStatesByIdCountry($idCountry)
    {
        if (empty($idCountry)) {
            exit(Tools::displayError());
        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT s.`id_state`, s.`name` FROM `' . _DB_PREFIX_ . 'state` s WHERE s.`id_country` = ' . (int) $idCountry . ' AND s.`active` = 1');
    }

    public function isAllReadyInImpact($mpShippingId, $shippingDeliveryId, $idZone, $idCountry, $idState)
    {
        $isExistImpact = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'wk_mp_seller_shipping_impact WHERE mp_shipping_id = ' . (int) $mpShippingId . ' AND shipping_delivery_id = ' . (int) $shippingDeliveryId . ' AND id_zone=' . $idZone . ' AND id_country = ' . (int) $idCountry . ' AND id_state = ' . (int) $idState);

        if (empty($isExistImpact)) {
            return false;
        } else {
            return $isExistImpact;
        }
    }

    public static function getAllImpactPriceByMpshippingid($mpShippingId)
    {
        $allImpactPrice = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT *, `id_wk_mp_shipping_impact` AS `id` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping_impact` WHERE `mp_shipping_id` = ' . (int) $mpShippingId
        );

        if ($allImpactPrice) {
            return $allImpactPrice;
        } else {
            return false;
        }
    }

    public static function getZonenameByZoneid($idZone)
    {
        $allZone = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'zone` WHERE `id_zone` = ' . (int) $idZone);

        if ($allZone) {
            return $allZone;
        } else {
            return false;
        }
    }
}
