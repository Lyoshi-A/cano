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

class WkMpLoginBlockPosition extends ObjectModel
{
    public $id_wk_mp_seller_login_block_position;
    public $id_parent;
    public $id_position;
    public $id_theme;
    public $block_name;
    public $width;
    public $block_bg_color;
    public $block_text_color;
    public $active;

    public static $definition = [
        'table' => 'wk_mp_seller_login_block_position',
        'primary' => 'id_wk_mp_seller_login_block_position',
        'fields' => [
            'id_parent' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'shop' => true],
            'id_position' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'shop' => true],
            'id_theme' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'shop' => true],
            'block_name' => ['type' => self::TYPE_STRING, 'shop' => true],
            'width' => ['type' => self::TYPE_FLOAT, 'shop' => true],
            'block_bg_color' => ['type' => self::TYPE_STRING, 'shop' => true],
            'block_text_color' => ['type' => self::TYPE_STRING, 'shop' => true],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'shop' => true],
        ],
    ];

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
        Shop::addTableAssociation(
            'wk_mp_seller_login_block_position',
            ['type' => 'shop', 'primary' => 'id_wk_mp_seller_login_block_position']
        );
    }

    public function getBlockPositionDetailByBlockName($psShopId, $blockName, $idTheme)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller_login_block_position` mlbp';
        $sql .= Shop::addSqlAssociation('wk_mp_seller_login_block_position', 'mlbp');
        $sql .= ' WHERE wk_mp_seller_login_block_position_shop.`block_name` = \'' . pSQL($blockName) . '\'
        AND wk_mp_seller_login_block_position_shop.`id_shop` = ' . (int) $psShopId . '
        AND wk_mp_seller_login_block_position_shop.`id_theme` = ' . (int) $idTheme;

        return Db::getInstance()->getRow($sql);
    }

    public function getPositionDetailByIdParent($idParent, $idTheme)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller_login_block_position` mlbp';
        $sql .= Shop::addSqlAssociation('wk_mp_seller_login_block_position', 'mlbp');
        $sql .= ' WHERE wk_mp_seller_login_block_position_shop.`id_parent` = ' . (int) $idParent . '
        AND wk_mp_seller_login_block_position_shop.`active`=1
        AND wk_mp_seller_login_block_position_shop.`id_theme`=' . (int) $idTheme . '
        ORDER BY wk_mp_seller_login_block_position_shop.id_position';

        return Db::getInstance()->executeS($sql);
    }
}
