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

class WkMpSellerHelpDesk extends ObjectModel
{
    public $id_product;
    public $id_customer;
    public $id_seller;
    public $subject;
    public $description;
    public $customer_email;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = [
        'table' => 'wk_mp_seller_help_desk',
        'primary' => 'id_mp_help_desk',
        'fields' => [
            'id_product' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'id_seller' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'subject' => ['type' => self::TYPE_STRING],
            'description' => ['type' => self::TYPE_STRING],
            'customer_email' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isEmail',
                'required' => true,
                'size' => 128,
            ],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false],
        ],
    ];
}
