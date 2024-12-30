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

class WkMpLoginConfigration extends ObjectModel
{
    public $id_wk_mp_seller_login_configration;
    public $id_theme;
    public $header_bg_color;
    public $body_bg_color;

    public $meta_title;
    public $meta_description;

    public static $definition = [
        'table' => 'wk_mp_seller_login_configration',
        'primary' => 'id_wk_mp_seller_login_configration',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            'id_theme' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true, 'shop' => true],
            'header_bg_color' => ['type' => self::TYPE_STRING, 'required' => true, 'shop' => true],
            'body_bg_color' => ['type' => self::TYPE_STRING, 'required' => true, 'shop' => true],
            /* Lang fields */
            'meta_title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'],
            'meta_description' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString'],
        ],
    ];

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
        Shop::addTableAssociation(
            'wk_mp_seller_login_configration',
            ['type' => 'shop', 'primary' => 'id_wk_mp_seller_login_configration']
        );
        Shop::addTableAssociation(
            'wk_mp_seller_login_configration_lang',
            ['type' => 'fk_shop', 'primary' => 'id_wk_mp_seller_login_configration']
        );
    }

    public function getShopThemeConfigration($psShopId, $idTheme, $idLang = false)
    {
        if ($idLang) {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller_login_configration` mplc
            LEFT JOIN `' . _DB_PREFIX_ . 'wk_mp_seller_login_configration_lang` mplcl
            on (mplcl.`id_wk_mp_seller_login_configration` = mplc.`id_wk_mp_seller_login_configration`)';
            $sql .= Shop::addSqlAssociation('wk_mp_seller_login_configration', 'mplc');
            $sql .= ' WHERE mplcl.`id_shop` = ' . (int) $psShopId . '
            AND mplc.`id_theme`=' . (int) $idTheme . '
            AND mplcl.`id_lang`=' . (int) $idLang;
        } else {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller_login_configration` mlc';
            $sql .= Shop::addSqlAssociation('wk_mp_seller_login_configration', 'mlc');
            $sql .= ' WHERE wk_mp_seller_login_configration_shop.`id_theme`=' . (int) $idTheme;
        }

        return Db::getInstance()->getRow($sql);
    }

    public function getShopThemeConfigrationLangInfo($id)
    {
        $context = Context::getContext();
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller_login_configration_lang` mlcl';
        $sql .= ' WHERE mlcl.`id_wk_mp_seller_login_configration` = ' . (int) $id . '
        AND mlcl.`id_shop` = ' . (int) $context->shop->id;

        return Db::getInstance()->executeS($sql);
    }

    public function getAllConfigration()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller_login_configration` mlc';
        $sql .= Shop::addSqlAssociation('wk_mp_seller_login_configration', 'mlc');

        return Db::getInstance()->executeS($sql);
    }

    public static function setMpSellerLoginConfigurationsForNewShop($idShop)
    {
        $defaultShop = Configuration::get('PS_SHOP_DEFAULT');
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'wk_mp_seller_login_block_position_shop`(`id_wk_mp_seller_login_block_position`,
        `id_shop`, `id_parent`, `id_position`, `id_theme`, `block_name`, `width`, `block_bg_color`, `block_text_color`, `active`)
        SELECT  `id_wk_mp_seller_login_block_position`, "' . (int) $idShop . '", `id_parent`, `id_position`, `id_theme`, `block_name`, `width`, `block_bg_color`, `block_text_color`, `active` FROM
        `' . _DB_PREFIX_ . 'wk_mp_seller_login_block_position_shop` WHERE `id_shop`=' . (int) $defaultShop;
        Db::getInstance()->execute($sql);
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'wk_mp_seller_login_configration_lang`(`id_wk_mp_seller_login_configration`,
        `id_shop`, `id_lang`, `meta_title`, `meta_description`)
        SELECT  `id_wk_mp_seller_login_configration`, "' . (int) $idShop . '", `id_lang`, `meta_title`, `meta_description` FROM
        `' . _DB_PREFIX_ . 'wk_mp_seller_login_configration_lang` WHERE `id_shop`=' . (int) $defaultShop;
        Db::getInstance()->execute($sql);
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'wk_mp_seller_login_configration_shop`(`id_wk_mp_seller_login_configration`,
        `id_shop`, `id_theme`, `header_bg_color`, `body_bg_color`)
        SELECT  `id_wk_mp_seller_login_configration`, "' . (int) $idShop . '", `id_theme`, `header_bg_color`, `body_bg_color` FROM
        `' . _DB_PREFIX_ . 'wk_mp_seller_login_configration_shop` WHERE `id_shop`=' . (int) $defaultShop;
        Db::getInstance()->execute($sql);
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'wk_mp_seller_login_content_lang`(`id_wk_mp_seller_login_content`,
        `id_shop`, `id_lang`, `content`)
        SELECT  `id_wk_mp_seller_login_content`, "' . (int) $idShop . '", `id_lang`, `content` FROM
        `' . _DB_PREFIX_ . 'wk_mp_seller_login_content_lang` WHERE `id_shop`=' . (int) $defaultShop;
        Db::getInstance()->execute($sql);
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'wk_mp_seller_login_content_shop`(`id_wk_mp_seller_login_content`,
        `id_shop`, `id_block`, `id_theme`)
        SELECT  `id_wk_mp_seller_login_content`, "' . (int) $idShop . '", `id_block`, `id_theme` FROM
        `' . _DB_PREFIX_ . 'wk_mp_seller_login_content_shop` WHERE `id_shop`=' . (int) $defaultShop;
        Db::getInstance()->execute($sql);
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'wk_mp_seller_login_parent_block_shop`(`id_wk_mp_seller_login_parent_block`,
        `id_shop`, `id_position`, `id_theme`, `name`, `active`)
        SELECT  `id_wk_mp_seller_login_parent_block`, "' . (int) $idShop . '", `id_position`, `id_theme`, `name`, `active` FROM
        `' . _DB_PREFIX_ . 'wk_mp_seller_login_parent_block_shop` WHERE `id_shop`=' . (int) $defaultShop;
        Db::getInstance()->execute($sql);

        return true;
    }
}
