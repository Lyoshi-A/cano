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

function upgrade_module_5_3_0($module)
{
    $wkQueries = [
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_mp_seller`
        DROP COLUMN `google_id`',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_mp_seller`
        ADD COLUMN `youtube_id` varchar(255) character set utf8 NOT NULL AFTER `twitter_id`',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_mp_seller_product`
        ADD COLUMN `id_mp_duplicate_product_parent` int(10) unsigned DEFAULT 0 AFTER `id_ps_product`',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_mp_carrier_distributor_type` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `id_ps_reference` int(11) unsigned NOT NULL,
            `type` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_mp_shipping_commission` (
            `id_wk_mp_shipping_commission` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `id_seller` int(10) NOT NULL,
            `commission_rate` decimal(20,2) NOT NULL DEFAULT '0.00',
            PRIMARY KEY (`id_wk_mp_shipping_commission`)
        ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_mp_seller_order_status`
        ADD COLUMN `tracking_number` varchar(64) DEFAULT NULL AFTER `current_state`',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_mp_seller_order_status`
        ADD COLUMN `tracking_url` varchar(255) DEFAULT NULL AFTER `tracking_number`',
        'ALTER TABLE `' . _DB_PREFIX_ . 'wk_mp_commision`
        ADD COLUMN `id_seller` int(10) NOT NULL AFTER `id_wk_mp_commision`',
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_mp_commision`
        ADD COLUMN `commision_type` varchar(64) DEFAULT 'percentage' AFTER `id_seller`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_mp_commision`
        ADD COLUMN `commision_amt` decimal(20,6) NOT NULL DEFAULT '0.000000' AFTER `commision_rate`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_mp_commision`
        ADD COLUMN `commision_tax_amt` decimal(20,6) NOT NULL DEFAULT '0.000000' AFTER `commision_amt`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_mp_seller_order_detail`
        ADD COLUMN `id_customization` int(10) NOT NULL DEFAULT '0' AFTER `product_attribute_id`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_mp_seller_order_detail`
        ADD COLUMN `commission_type` varchar(64) DEFAULT 'percentage' AFTER `id_order`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_mp_seller_order_detail`
        ADD COLUMN `commission_amt` decimal(20,6) NOT NULL DEFAULT '0.000000' AFTER `commission_rate`",
        'ALTER TABLE `' . _DB_PREFIX_ . "wk_mp_seller_order_detail`
        ADD COLUMN `commission_tax_amt` decimal(20,6) NOT NULL DEFAULT '0.000000' AFTER `commission_amt`",
    ];

    $mpDatabaseInstance = Db::getInstance();
    $mpSuccess = true;
    foreach ($wkQueries as $mpQuery) {
        $mpSuccess &= $mpDatabaseInstance->execute(trim($mpQuery));
    }
    if ($mpSuccess) {
        return $module->registerHook('actionAdminCarriersListingFieldsModifier')
            && $module->registerHook('actionAdminControllerSetMedia')
            && $module->registerHook('displayOrderDetail')
            && $module->createMarketplaceModuleTab('AdminMpShippingCommission', 'Shipping Commission Settings', '-1')
            && Configuration::updateValue('WK_MP_GLOBAL_COMMISSION_TYPE', 'percentage')
            && Configuration::updateValue('WK_MP_GLOBAL_SHIPPING_COMMISSION', 10)
            && WkMpCommission::updateSellerIdInAllCommission()
        ;
    }

    return true;
}
