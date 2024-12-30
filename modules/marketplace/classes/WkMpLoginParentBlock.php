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

class WkMpLoginParentBlock extends ObjectModel
{
    public $id_wk_mp_seller_login_parent_block;
    public $id_position;
    public $id_theme;
    public $name;
    public $active;

    public static $definition = [
        'table' => 'wk_mp_seller_login_parent_block',
        'primary' => 'id_wk_mp_seller_login_parent_block',
        'fields' => [
            'id_position' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'shop' => true],
            'id_theme' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'shop' => true],
            'name' => ['type' => self::TYPE_STRING, 'shop' => true],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'shop' => true],
        ],
    ];

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
        Shop::addTableAssociation(
            'wk_mp_seller_login_parent_block',
            ['type' => 'shop', 'primary' => 'id_wk_mp_seller_login_parent_block']
        );
    }

    public static function getNoOfSubBlocks($blockName, $idTheme)
    {
        $context = Context::getContext();
        $sql = 'SELECT COUNT(mlbps.`id_wk_mp_seller_login_block_position`) AS noofblock
        FROM ' . _DB_PREFIX_ . 'wk_mp_seller_login_parent_block_shop AS mlpb
        INNER JOIN ' . _DB_PREFIX_ . 'wk_mp_seller_login_block_position AS mlbp
        ON (mlbp.id_parent = mlpb.id_wk_mp_seller_login_parent_block AND mlbp.id_theme=' . (int) $idTheme . ')
        INNER JOIN ' . _DB_PREFIX_ . 'wk_mp_seller_login_block_position_shop AS mlbps
        ON (mlbp.`id_wk_mp_seller_login_block_position` = mlbps.`id_wk_mp_seller_login_block_position`)
        WHERE mlbps.`id_shop` = ' . (int) $context->shop->id . ' AND mlpb.`id_shop` = ' . (int) $context->shop->id . '
        AND mlpb.`name` = \'' . pSQL($blockName) . '\'';

        return Db::getInstance()->getValue(
            $sql
        );
    }

    public static function getParentBlockPosition($blockName, $idTheme)
    {
        $sql = 'SELECT wk_mp_seller_login_parent_block_shop.`id_position`
        FROM `' . _DB_PREFIX_ . 'wk_mp_seller_login_parent_block` mlpb';
        $sql .= Shop::addSqlAssociation('wk_mp_seller_login_parent_block', 'mlpb');
        $sql .= ' WHERE wk_mp_seller_login_parent_block_shop.`name` = \'' . pSQL($blockName) . '\'
        AND wk_mp_seller_login_parent_block_shop.`id_theme`=' . (int) $idTheme;

        return Db::getInstance()->getValue(
            $sql
        );
    }

    public static function isParentBlockActive($blockName, $idTheme)
    {
        $sql = 'SELECT wk_mp_seller_login_parent_block_shop.`active`
        FROM `' . _DB_PREFIX_ . 'wk_mp_seller_login_parent_block` mlpb';
        $sql .= Shop::addSqlAssociation('wk_mp_seller_login_parent_block', 'mlpb');
        $sql .= ' WHERE wk_mp_seller_login_parent_block_shop.`name` = \'' . pSQL($blockName) . '\'
            AND wk_mp_seller_login_parent_block_shop.`id_theme`=' . (int) $idTheme;

        return Db::getInstance()->getValue(
            $sql
        );
    }

    public function getParentBlockDetails($blockName, $idTheme)
    {
        $sql = 'SELECT * from `' . _DB_PREFIX_ . 'wk_mp_seller_login_parent_block` mlpb';
        $sql .= Shop::addSqlAssociation('wk_mp_seller_login_parent_block', 'mlpb');
        $sql .= ' WHERE wk_mp_seller_login_parent_block_shop.`name` = \'' . pSQL($blockName) . '\'
        AND wk_mp_seller_login_parent_block_shop.`id_theme`=' . (int) $idTheme;

        return Db::getInstance()->getRow(
            $sql
        );
    }

    public function getActiveParentBlock($idTheme)
    {
        $temp = 'header';
        $sql = 'SELECT * from `' . _DB_PREFIX_ . 'wk_mp_seller_login_parent_block` mlpb';
        $sql .= Shop::addSqlAssociation('wk_mp_seller_login_parent_block', 'mlpb');
        $sql .= ' WHERE wk_mp_seller_login_parent_block_shop.`active` = 1
        AND wk_mp_seller_login_parent_block_shop.`name` != \'' . pSQL($temp) . '\'
        AND wk_mp_seller_login_parent_block_shop.`id_theme`=' . (int) $idTheme . '
        ORDER BY wk_mp_seller_login_parent_block_shop.`id_position`';

        return Db::getInstance()->executeS(
            $sql
        );
    }

    public function getBlockIdByThemeId($blockName, $idTheme)
    {
        $sql = 'SELECT * from `' . _DB_PREFIX_ . 'wk_mp_seller_login_parent_block` AS mlpb';
        $sql .= Shop::addSqlAssociation('wk_mp_seller_login_parent_block', 'mlpb');
        $sql .= ' WHERE wk_mp_seller_login_parent_block_shop.`name` = \'' . pSQL($blockName) . '\'
        AND wk_mp_seller_login_parent_block_shop.`id_theme`=' . (int) $idTheme;

        return Db::getInstance()->getRow(
            $sql
        );
    }
}
