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

class WkMpSellerShippingCart extends ObjectModel
{
    public $id_wk_mp_shipping_cart;
    public $id_ps_cart;
    public $id_ps_carrier;
    public $extra_cost;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'wk_mp_seller_shipping_cart',
        'primary' => 'id_wk_mp_shipping_cart',
        'fields' => [
            'id_ps_cart' => ['type' => self::TYPE_INT, 'required' => true],
            'id_ps_carrier' => ['type' => self::TYPE_INT, 'required' => true],
            'extra_cost' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
        ],
    ];

    public function isAvailable($idPsCarrier, $idPsCart)
    {
        $isAvailable = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller_shipping_cart` WHERE `id_ps_carrier` = ' . (int) $idPsCarrier . ' AND id_ps_cart = ' . (int) $idPsCart);

        if (empty($isAvailable)) {
            return false;
        } else {
            return $isAvailable;
        }
    }
}
