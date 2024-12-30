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

class WkMpLoginContent extends ObjectModel
{
    public $id_wk_mp_seller_login_content;
    public $id_block;
    public $id_theme;
    public $content;

    public static $definition = [
        'table' => 'wk_mp_seller_login_content',
        'multilang' => true,
        'multilang_shop' => true,
        'primary' => 'id_wk_mp_seller_login_content',
        'fields' => [
            'id_block' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'shop' => true],
            'id_theme' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'shop' => true],
            /* Lang fields */
            'content' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
        ],
    ];

    public function __construct($id = null, $idLang = null, $idShop = null)
    {
        parent::__construct($id, $idLang, $idShop);
        Shop::addTableAssociation(
            'wk_mp_seller_login_content',
            ['type' => 'shop', 'primary' => 'id_wk_mp_seller_login_content']
        );
        Shop::addTableAssociation(
            'wk_mp_seller_login_content_lang',
            ['type' => 'fk_shop', 'primary' => 'id_wk_mp_seller_login_content']
        );
    }

    public function getBlockLangContentById($id)
    {
        $context = Context::getContext();
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller_login_content_lang` mlcl';
        $sql .= Shop::addSqlAssociation('wk_mp_seller_login_content', 'mlcl');
        $sql .= ' WHERE mlcl.`id_shop` = ' . (int) $context->shop->id . '
        AND mlcl.`id_wk_mp_seller_login_content` = ' . (int) $id;

        return Db::getInstance()->executeS($sql);
    }

    public function getBlockContent($idBlock, $idTheme, $idLang = false)
    {
        if ($idLang) {
            $context = Context::getContext();
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller_login_content` mpln
            LEFT JOIN `' . _DB_PREFIX_ . 'wk_mp_seller_login_content_lang` mplnl
            ON (mpln.`id_wk_mp_seller_login_content` = mplnl.`id_wk_mp_seller_login_content`)';
            $sql .= Shop::addSqlAssociation('wk_mp_seller_login_content', 'mpln');
            $sql .= ' WHERE mpln.`id_block` = ' . (int) $idBlock . '
            AND mpln.`id_theme` = ' . (int) $idTheme . '
            AND mplnl.`id_shop` = ' . (int) $context->shop->id . '
            AND mplnl.`id_lang` = ' . (int) $idLang;

            return Db::getInstance()->getRow($sql);
        } else {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller_login_content` mlc';
            $sql .= Shop::addSqlAssociation('wk_mp_seller_login_content', 'mlc');
            $sql .= ' WHERE wk_mp_seller_login_content_shop.`id_block` = ' . (int) $idBlock . '
            AND wk_mp_seller_login_content_shop.`id_theme` = ' . (int) $idTheme;

            return Db::getInstance()->getRow($sql);
        }
    }

    public function getAllLoginContent()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller_login_content`';

        return Db::getInstance()->executeS($sql);
    }
}
