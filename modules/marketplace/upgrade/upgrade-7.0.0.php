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

function upgrade_module_7_0_0($module)
{
    $wkQueries = [
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_mp_seller_shipping` (
            `id_wk_mp_shipping` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `id_seller` int(11) unsigned NOT NULL,
            `id_ps_reference` int(11) unsigned NOT NULL,
            `is_default_shipping` tinyint(1) unsigned NOT NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_wk_mp_shipping`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_mp_seller_shipping_impact` (
            `id_wk_mp_shipping_impact` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `mp_shipping_id` int(11) unsigned NOT NULL,
            `shipping_delivery_id` int(11) unsigned NOT NULL,
            `id_zone` int(11) unsigned NOT NULL,
            `id_country` int(11) unsigned NOT NULL,
            `id_state` int(11) unsigned NOT NULL DEFAULT '0',
            `impact_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_wk_mp_shipping_impact`)
        ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . "wk_mp_seller_shipping_cart` (
            `id_wk_mp_shipping_cart` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `id_ps_cart` int(11) unsigned NOT NULL,
            `id_ps_carrier` int(11) unsigned NOT NULL,
            `extra_cost` decimal(20,6) DEFAULT '0.000000',
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`id_wk_mp_shipping_cart`)
        ) ENGINE=" . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_mp_seller_login_block_position` (
            `id_wk_mp_seller_login_block_position` int(11) NOT NULL AUTO_INCREMENT,
            `id_parent` int(11) NOT NULL,
            `id_position` int(11) NOT NULL,
            `id_theme` int(11) NOT NULL,
            `block_name` text NOT NULL,
            `width` int(11) NOT NULL,
            `block_bg_color` text NOT NULL,
            `block_text_color` text NOT NULL,
            `active` tinyint(4) NOT NULL,
            PRIMARY KEY (`id_wk_mp_seller_login_block_position`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_mp_seller_login_block_position_shop` (
            `id_wk_mp_seller_login_block_position` int(11) NOT NULL AUTO_INCREMENT,
            `id_shop` int(11) unsigned NOT NULL,
            `id_parent` int(11) NOT NULL,
            `id_position` int(11) NOT NULL,
            `id_theme` int(11) NOT NULL,
            `block_name` text NOT NULL,
            `width` int(11) NOT NULL,
            `block_bg_color` text NOT NULL,
            `block_text_color` text NOT NULL,
            `active` tinyint(4) NOT NULL,
            PRIMARY KEY (`id_wk_mp_seller_login_block_position`, `id_shop`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_mp_seller_login_configration` (
            `id_wk_mp_seller_login_configration` int(11) NOT NULL AUTO_INCREMENT,
            `id_theme` int(11) NOT NULL,
            `header_bg_color` text NOT NULL,
            `body_bg_color` text NOT NULL,
            PRIMARY KEY (`id_wk_mp_seller_login_configration`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_mp_seller_login_configration_shop` (
            `id_wk_mp_seller_login_configration` int(11) NOT NULL AUTO_INCREMENT,
            `id_shop` int(11) unsigned NOT NULL,
            `id_theme` int(11) NOT NULL,
            `header_bg_color` text NOT NULL,
            `body_bg_color` text NOT NULL,
            PRIMARY KEY (`id_wk_mp_seller_login_configration`, `id_shop`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_mp_seller_login_configration_lang` (
            `id_wk_mp_seller_login_configration` int(10) unsigned NOT NULL,
            `id_shop` int(11) unsigned NOT NULL,
            `id_lang` int(10) unsigned NOT NULL,
            `meta_title` text,
            `meta_description` text,
            PRIMARY KEY (`id_wk_mp_seller_login_configration`, `id_shop`, `id_lang`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_mp_seller_login_content` (
            `id_wk_mp_seller_login_content` int(11) NOT NULL AUTO_INCREMENT,
            `id_block` int(11) NOT NULL,
            `id_theme` int(11) NOT NULL,
            PRIMARY KEY (`id_wk_mp_seller_login_content`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_mp_seller_login_content_shop` (
            `id_wk_mp_seller_login_content` int(11) NOT NULL AUTO_INCREMENT,
            `id_shop` int(11) unsigned NOT NULL,
            `id_block` int(11) NOT NULL,
            `id_theme` int(11) NOT NULL,
            PRIMARY KEY (`id_wk_mp_seller_login_content`, `id_shop`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_mp_seller_login_content_lang` (
            `id_wk_mp_seller_login_content` int(10) unsigned NOT NULL,
            `id_shop` int(11) unsigned NOT NULL,
            `id_lang` int(10) unsigned NOT NULL,
            `content` text NOT NULL,
            PRIMARY KEY (`id_wk_mp_seller_login_content`, `id_shop`, `id_lang`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_mp_seller_login_parent_block` (
            `id_wk_mp_seller_login_parent_block` int(11) NOT NULL AUTO_INCREMENT,
            `id_position` int(11) NOT NULL,
            `id_theme` int(11) NOT NULL,
            `name` text NOT NULL,
            `active` tinyint(4) NOT NULL,
            PRIMARY KEY (`id_wk_mp_seller_login_parent_block`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8',
        'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'wk_mp_seller_login_parent_block_shop` (
            `id_wk_mp_seller_login_parent_block` int(11) NOT NULL AUTO_INCREMENT,
            `id_shop` int(11) unsigned NOT NULL,
            `id_position` int(11) NOT NULL,
            `id_theme` int(11) NOT NULL,
            `name` text NOT NULL,
            `active` tinyint(4) NOT NULL,
            PRIMARY KEY (`id_wk_mp_seller_login_parent_block`, `id_shop`)
        ) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8',
    ];

    $mpDatabaseInstance = Db::getInstance();
    $mpSuccess = true;
    foreach ($wkQueries as $mpQuery) {
        $mpSuccess &= $mpDatabaseInstance->execute(trim($mpQuery));
    }
    if ($mpSuccess) {
        if (Module::isEnabled('mpshipping')) {
            Module::disableAllByName('mpshipping');
        }
        if (Module::isEnabled('mpsellerwiselogin')) {
            Module::disableAllByName('mpsellerwiselogin');
        }
        $module->installTab('AdminMpSellerShipping', 'Carriers', 'AdminMarketplaceManagement');
        // Login controller
        $module->installTab('AdminMpCustomizeLogin', 'Seller Login Configuration', 'AdminManageConfiguration');
        $module->registerHook([
            'actionObjectCarrierUpdateAfter',
            'displayProductPriceBlock',
        ]);
        $module->setMpSellerLoginConfigurations();
    }

    return true;
}
