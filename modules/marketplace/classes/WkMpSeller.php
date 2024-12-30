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

class WkMpSeller extends ObjectModel
{
    public $shop_name_unique;
    public $link_rewrite;
    public $seller_firstname;
    public $seller_lastname;
    public $business_email;
    public $phone;
    public $fax;
    public $address;
    public $postcode;
    public $city;
    public $id_country;
    public $id_state;
    public $tax_identification_number;
    public $default_lang;
    public $facebook_id;
    public $twitter_id;
    public $youtube_id;
    public $instagram_id;
    public $profile_image;
    public $profile_banner;
    public $shop_image;
    public $shop_banner;
    public $active;
    public $shop_approved;
    public $seller_customer_id;
    public $id_shop;
    public $id_shop_group;
    public $seller_details_access;
    public $category_permission;
    public $date_add;
    public $date_upd;

    public $shop_name;
    public $about_shop;

    public static $definition = [
        'table' => 'wk_mp_seller',
        'primary' => 'id_seller',
        'multilang' => true,
        'fields' => [
            'shop_name_unique' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true],
            'link_rewrite' => ['type' => self::TYPE_STRING, 'validate' => 'isLinkRewrite', 'required' => true],
            'seller_firstname' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true],
            'seller_lastname' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true],
            'business_email' => ['type' => self::TYPE_STRING, 'validate' => 'isEmail'],
            'phone' => ['type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isPhoneNumber', 'size' => 32],
            'fax' => ['type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber'],
            'address' => ['type' => self::TYPE_STRING, 'validate' => 'isAddress'],
            'postcode' => ['type' => self::TYPE_STRING, 'validate' => 'isPostCode'],
            'city' => ['type' => self::TYPE_STRING, 'validate' => 'isCityName'],
            'id_country' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_state' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'tax_identification_number' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'default_lang' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'facebook_id' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'twitter_id' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'youtube_id' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'instagram_id' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'profile_image' => ['type' => self::TYPE_STRING],
            'profile_banner' => ['type' => self::TYPE_STRING],
            'shop_image' => ['type' => self::TYPE_STRING],
            'shop_banner' => ['type' => self::TYPE_STRING],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'shop_approved' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'seller_customer_id' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_shop_group' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'seller_details_access' => ['type' => self::TYPE_STRING],
            'category_permission' => ['type' => self::TYPE_STRING],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false],

            /* Lang fields */
            'shop_name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true],
            'about_shop' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'],
        ],
    ];

    public function toggleStatus()
    {
        return true;
    }

    public function delete()
    {
        if (!$this->actionBeforeSellerDelete($this->id) || !parent::delete()) {
            return false;
        }

        return true;
    }

    /**
     * Deleting seller from the marketplace
     *
     * @param int $idSeller Seller id which to be delete
     *
     * @return bool
     */
    public function actionBeforeSellerDelete($idSeller)
    {
        Hook::exec('actionMpSellerDelete', ['id_seller' => (int) $idSeller]);

        // Delete Seller Shipping
        if ($sellerShipping = WkMpSellerShipping::getSellerAllShippingMethod($idSeller)) {
            foreach ($sellerShipping as $shipping) {
                $objMpShipping = new WkMpSellerShipping();
                $objMpShipping->deleteMpShipping($shipping['id_wk_mp_shipping']);
            }
        }

        // Delete Manufacturers
        $objMpManufacturer = new WkMpManufacturers();
        $manufacturerMapping = $objMpManufacturer->getManufacturerInfo($idSeller);
        if ($manufacturerMapping) {
            foreach ($manufacturerMapping as $manufacturer) {
                $objProdManufdel = new WkMpManufacturers($manufacturer['id_wk_mp_manufacturers']);
                $objProdManufdel->delete();
            }
        }
        // Delete Suppliers
        $objMpSupplier = new WkMpSuppliers();
        $supplierMapping = $objMpSupplier->getSuppliersBySellerId($idSeller);
        if ($supplierMapping) {
            foreach ($supplierMapping as $supplier) {
                WkMpSuppliers::deleteSupplier($supplier['id_wk_mp_supplier']);
            }
        }
        // delete from mp customer
        $objMpSeller = new self();
        $idCustomer = $objMpSeller->getCustomerIdBySellerId($idSeller);
        $activeCustomer = true;
        if ($idCustomer) {
            // delete seller all images ie. profile image, shop image and banners
            $this->unlinkSellerImages($idSeller);

            $deletePayment = Db::getInstance()->delete('wk_mp_customer_payment_detail', 'seller_customer_id = ' . (int) $idCustomer);
            $deleteCommission = Db::getInstance()->delete('wk_mp_commision', 'seller_customer_id = ' . (int) $idCustomer);

            if (!$deletePayment
                || !$deleteCommission
            ) {
                $activeCustomer = false;
            }
        }

        // delete mp products
        $productDelete = true;

        $mpProducts = WkMpSellerProduct::getSellerProduct($idSeller);
        if ($mpProducts) {
            foreach ($mpProducts as $product) {
                $objMpProduct = new WkMpSellerProduct($product['id_mp_product']);
                if (!$objMpProduct->delete()) {
                    $productDelete = false;
                }
            }
        }

        // deleting reviews
        $deleteReview = Db::getInstance()->delete('wk_mp_seller_review', 'id_seller = ' . (int) $idSeller);
        // Delete tinymce file if exist
        $deleteTinymceFile = WkMpSeller::deleteTinymceSourceFile($idSeller);

        // mail to seller on mp seller delete by admin
        if (Configuration::get('WK_MP_MAIL_SELLER_DELETE')) {
            WkMpSeller::mailToSellerOnAccountDelete($idSeller);
        }

        if (!$activeCustomer
            || !$productDelete
            || !$deleteReview
            || !$deleteTinymceFile) {
            return false;
        }

        return true;
    }

    /**
     * Get seller information by using seller id.
     *
     * @param int $idSeller Seller ID
     *
     * @return bool|array
     */
    public static function getSeller($idSeller, $idLang = false)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller`
        WHERE `id_seller` =' . (int) $idSeller . WkMpSeller::addSqlRestriction();
        $sellerDetail = Db::getInstance()->getRow($sql);

        if (!$idLang) {
            $langDetail = self::getSellerShopLang($idSeller);
            if ($langDetail) {
                foreach ($langDetail as $detail) {
                    $sellerDetail['shop_name'][$detail['id_lang']] = $detail['shop_name'];
                    $sellerDetail['about_shop'][$detail['id_lang']] = $detail['about_shop'];
                }
            }

            return $sellerDetail;
        } else {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller` s
            LEFT JOIN `' . _DB_PREFIX_ . 'wk_mp_seller_lang` sl on (sl.`id_seller` = s.`id_seller`)
            where s.`id_seller` =' . (int) $idSeller . '
            AND sl.`id_lang` = ' . (int) $idLang . WkMpSeller::addSqlRestriction();
        }

        return Db::getInstance()->getRow($sql);
    }

    /**
     * Get information from seller lang table like shop name address and about_shop
     *
     * @param int $idSeller Seller ID
     *
     * @return bool|array
     */
    public static function getSellerShopLang($idSeller)
    {
        $result = Db::getInstance()->executeS(
            'SELECT * FROM  `' . _DB_PREFIX_ . 'wk_mp_seller_lang` WHERE `id_seller` = ' . (int) $idSeller
        );
        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Get seller's default language
     *
     * @param int $idSeller
     *
     * @return bool|array
     */
    public static function getSellerDefaultLanguage($idSeller)
    {
        if ($idSeller) {
            return Db::getInstance()->getValue(
                'SELECT `default_lang` FROM  `' . _DB_PREFIX_ . 'wk_mp_seller`
                WHERE `id_seller` = ' . (int) $idSeller . WkMpSeller::addSqlRestriction()
            );
        }

        return false;
    }

    /**
     * Get seller detail with their language like shop name about shop using prestashop customer id
     *
     * @param int $idCustomer
     * @param bool $langId
     *
     * @return bool|array
     */
    public static function getSellerByCustomerId($idCustomer, $idLang = false)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller`
        WHERE `seller_customer_id` =' . (int) $idCustomer . WkMpSeller::addSqlRestriction();
        $sellerDetail = Db::getInstance()->getRow($sql);

        if (!$idLang && $sellerDetail) {
            $langDetail = self::getSellerShopLang($sellerDetail['id_seller']);
            if ($langDetail) {
                foreach ($langDetail as $detail) {
                    $sellerDetail['shop_name'][$detail['id_lang']] = $detail['shop_name'];
                    $sellerDetail['about_shop'][$detail['id_lang']] = $detail['about_shop'];
                }
            }

            return $sellerDetail;
        } else {
            $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller` s
            LEFT JOIN `' . _DB_PREFIX_ . 'wk_mp_seller_lang` sl on (sl.`id_seller` = s.`id_seller`)
            WHERE s.`seller_customer_id` =' . (int) $idCustomer . '
            AND sl.`id_lang` = ' . (int) $idLang . WkMpSeller::addSqlRestriction();
        }

        return Db::getInstance()->getRow($sql);
    }

    /**
     * Get seller detail from transaction history like shop name about shop using prestashop customer id
     *
     * @param int $idCustomer
     *
     * @return bool|array
     */
    public static function getSellerByCustomerIdFromOrder($idCustomer)
    {
        $sql = 'SELECT
        a.`id_customer_seller` AS `seller_customer_id`,
        so.`seller_shop` AS `shop_name_unique`,
        so.`seller_id` AS `id_seller`,
        CONCAT(so.`seller_firstname`," ",so.`seller_lastname`) AS `seller_name`,
        so.`seller_email` AS `email` FROM `' . _DB_PREFIX_ . 'wk_mp_seller_transaction_history` a
        LEFT JOIN `' . _DB_PREFIX_ . 'wk_mp_seller_order` so ON (a.`id_customer_seller` = so.`seller_customer_id`)
        WHERE a.`id_customer_seller` =' . (int) $idCustomer;

        return Db::getInstance()->getRow($sql);
    }

    /**
     * Get Seller information with their shop detail by using seller id
     *
     * @deprecated use getSeller() instead
     *
     * @param int $idSeller
     * @param bool $langId optional
     *
     * @return bool|array
     */
    public function getSellerWithLangBySellerId($idSeller, $langId = false)
    {
        if (!$langId) {
            $langId = Configuration::get('PS_LANG_DEFAULT');
        }

        $sellerDetail = Db::getInstance()->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller` mpsi
            LEFT JOIN `' . _DB_PREFIX_ . 'wk_mp_seller_lang` msil ON (mpsi.`id_seller` = msil.`id_seller`)
            WHERE mpsi.`id_seller` = ' . (int) $idSeller . '
            AND msil.`id_lang` = ' . (int) $langId . WkMpSeller::addSqlRestriction()
        );
        if ($sellerDetail) {
            return $sellerDetail;
        }

        return false;
    }

    /**
     * Get customer id of any seller
     *
     * @param int $idSeller
     *
     * @return int
     */
    public function getCustomerIdBySellerId($idSeller)
    {
        return Db::getInstance()->getValue(
            'SELECT `seller_customer_id` FROM `' . _DB_PREFIX_ . 'wk_mp_seller`
            WHERE `id_seller` = ' . (int) $idSeller . WkMpSeller::addSqlRestriction()
        );
    }

    /**
     * Get seller information by customer id
     *
     * @param int $idCustomer Prestashop customer ID
     *
     * @return bool|array
     */
    public static function getSellerDetailByCustomerId($idCustomer)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller`
            WHERE `seller_customer_id` = ' . (int) $idCustomer . WkMpSeller::addSqlRestriction()
        );
    }

    /**
     * Get seller details by link rewrite, if you need to have shop details too, then paas language ID too
     *
     * @param string $linkRewrite
     * @param bool $langId
     *
     * @return bool|array
     */
    public static function getSellerByLinkRewrite($linkRewrite, $langId = false)
    {
        $sellerInfo = false;
        if ($langId) {
            $sellerInfo = Db::getInstance()->getRow(
                'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller` mpsi
                LEFT JOIN `' . _DB_PREFIX_ . 'wk_mp_seller_lang` msil ON (mpsi.`id_seller` = msil.`id_seller`)
                WHERE mpsi.`link_rewrite` = "' . pSQL($linkRewrite) . '"
                AND msil.`id_lang` = ' . (int) $langId . WkMpSeller::addSqlRestriction()
            );
        } else {
            $sellerInfo = Db::getInstance()->getRow(
                'SELECT * FROM `' . _DB_PREFIX_ . 'wk_mp_seller`
                WHERE `link_rewrite` = "' . pSQL($linkRewrite) . '"' . WkMpSeller::addSqlRestriction()
            );
        }

        return $sellerInfo;
    }

    /**
     * Check whether seller shop name exist or not, Seller ID is optional here
     *
     * @param string $name Shop link rewrite name
     * @param bool $idSeller
     *
     * @return bool
     */
    public static function isShopNameExist($name, $idSeller = false)
    {
        $mpIDSeller = Db::getInstance()->getValue(
            'SELECT `id_seller` FROM `' . _DB_PREFIX_ . 'wk_mp_seller`
            WHERE link_rewrite = "' . pSQL($name) . '"' . WkMpSeller::addSqlRestriction()
        );
        if ($idSeller) {
            if ($mpIDSeller) {
                if ($mpIDSeller == $idSeller) {
                    return false;
                }

                return true;
            }
        } else {
            if ($mpIDSeller) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all customers from the prestashop whose are not registered as seller in the website
     *
     * @return bool|array
     */
    public function getNonSellerCustomer()
    {
        $result = Db::getInstance()->executeS(
            'SELECT cus.`id_customer`, cus.`email`, cus.`id_shop`
            FROM `' . _DB_PREFIX_ . 'customer` cus
        	WHERE cus.`id_customer` NOT IN (
                SELECT `seller_customer_id` FROM `' . _DB_PREFIX_ . 'wk_mp_seller` msi
                WHERE 1 ' . WkMpSeller::addSqlRestriction() . '
            )
			AND cus.`active` = 1
            AND cus.`is_guest` = 0
            AND cus.`deleted` = 0' . WkMpSeller::addSqlRestriction('cus')
        );

        if ($result) {
            return $result;
        }

        return false;
    }

    /**
     * Get seller logo image link
     *
     * @param array $mpSellerInfo
     *
     * @return bool/url
     */
    public static function getSellerImageLink($mpSellerInfo)
    {
        if (!$mpSellerInfo) {
            return false;
        }

        if (isset($mpSellerInfo['profile_image']) && $mpSellerInfo['profile_image']
        && file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/seller_img/' . $mpSellerInfo['profile_image'])
        ) {
            return _MODULE_DIR_ . 'marketplace/views/img/seller_img/' . $mpSellerInfo['profile_image'];
        }

        return false;
    }

    /**
     * Get seller banner image link
     *
     * @param int $mpSellerInfo Seller information
     *
     * @return bool/url
     */
    public static function getSellerBannerLink($mpSellerInfo)
    {
        if (!$mpSellerInfo) {
            return false;
        }

        if (isset($mpSellerInfo['profile_banner']) && $mpSellerInfo['profile_banner']
        && file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/seller_banner/' . $mpSellerInfo['profile_banner'])
        ) {
            return _MODULE_DIR_ . 'marketplace/views/img/seller_banner/' . $mpSellerInfo['profile_banner'];
        }

        return false;
    }

    /**
     * Get seller shop image link
     *
     * @param array $mpSellerInfo Seller information
     *
     * @return bool/url
     */
    public static function getShopImageLink($mpSellerInfo)
    {
        if (!$mpSellerInfo) {
            return false;
        }

        if (isset($mpSellerInfo['shop_image']) && $mpSellerInfo['shop_image']
        && file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/shop_img/' . $mpSellerInfo['shop_image'])
        ) {
            return _MODULE_DIR_ . 'marketplace/views/img/shop_img/' . $mpSellerInfo['shop_image'];
        }

        return false;
    }

    /**
     * Get seller banner image link
     *
     * @param int $mpSellerInfo Seller information
     *
     * @return bool/url
     */
    public static function getShopBannerLink($mpSellerInfo)
    {
        if (!$mpSellerInfo) {
            return false;
        }

        if (isset($mpSellerInfo['shop_banner']) && $mpSellerInfo['shop_banner']
        && file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/shop_banner/' . $mpSellerInfo['shop_banner'])
        ) {
            return _MODULE_DIR_ . 'marketplace/views/img/shop_banner/' . $mpSellerInfo['shop_banner'];
        }

        return false;
    }

    /**
     * Unlink or delete seller all type image
     *
     * @param int $idSeller
     *
     * @return bool
     */
    public function unlinkSellerImages($idSeller)
    {
        if (!$idSeller) {
            return false;
        }

        $objMpSeller = new self($idSeller);

        if (isset($objMpSeller->profile_image) && $objMpSeller->profile_image
        && file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/seller_img/' . $objMpSeller->profile_image)
        ) {
            unlink(_PS_MODULE_DIR_ . 'marketplace/views/img/seller_img/' . $objMpSeller->profile_image);
        }

        if (isset($objMpSeller->profile_banner) && $objMpSeller->profile_banner
        && file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/seller_banner/' . $objMpSeller->profile_banner)
        ) {
            unlink(_PS_MODULE_DIR_ . 'marketplace/views/img/seller_banner/' . $objMpSeller->profile_banner);
        }

        if (isset($objMpSeller->shop_image) && $objMpSeller->shop_image
        && file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/shop_img/' . $objMpSeller->shop_image)
        ) {
            unlink(_PS_MODULE_DIR_ . 'marketplace/views/img/shop_img/' . $objMpSeller->shop_image);
        }

        if (isset($objMpSeller->shop_banner) && $objMpSeller->shop_banner
        && file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/shop_banner/' . $objMpSeller->shop_banner)
        ) {
            unlink(_PS_MODULE_DIR_ . 'marketplace/views/img/shop_banner/' . $objMpSeller->shop_banner);
        }

        return true;
    }

    /**
     * Check whether seller email address already exist or not
     *
     * @param string $sellerEmail Seller Email
     * @param bool $idSeller Seller ID
     *
     * @return bool
     */
    public static function isSellerEmailExist($sellerEmail, $idSeller = false, $customerId = false)
    {
        $sellerEmail = pSQL($sellerEmail);

        if ($idSeller && Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
            // in case of all shops in backoffice
            $objSeller = new self($idSeller);

            $currentShopContext = (int) $objSeller->id_shop;
            Shop::setContext(Shop::CONTEXT_SHOP, $currentShopContext);
        }

        $mpIdSeller = Db::getInstance()->getValue(
            'SELECT `id_seller` FROM `' . _DB_PREFIX_ . 'wk_mp_seller`
			WHERE `business_email` = \'' . pSQL($sellerEmail) . '\'' . WkMpSeller::addSqlRestriction()
            . ($customerId > 0 ? ' `seller_customer_id` = ' . (int) $customerId : '')
        );

        if ($idSeller && Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_ALL) {
            // in case of all shops in backoffice
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if ($idSeller) {
            if ($mpIdSeller) {
                if ($mpIdSeller == $idSeller) {
                    return false;
                }

                return true;
            }
        } else {
            if ($mpIdSeller) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get seller information with limit
     *
     * @param int $start Define row start from
     * @param int $limit Define number of rows
     * @param bool $idLang Language id - optional
     * @param bool $like Search with pattern
     * @param bool $all If you want to get all sellers, make it true
     * @param string $likeword Keyword to search the pattern
     *
     * @return array
     */
    public static function getAllSeller(
        $start = 0,
        $limit = 7,
        $idLang = false,
        $like = false,
        $all = true,
        $likeword = 'a'
    ) {
        if (!$idLang) {
            $idLang = Configuration::get('PS_LANG_DEFAULT');
        }

        $sql = 'SELECT msi.*, msil.*, msi.`seller_customer_id` AS id_customer
        FROM `' . _DB_PREFIX_ . 'wk_mp_seller` msi JOIN `' . _DB_PREFIX_ . 'customer` cus ON (cus.`id_customer` = msi.`seller_customer_id`) ';

        if (!$like && !$all) {
            $sql .= 'INNER JOIN `' . _DB_PREFIX_ . 'wk_mp_seller_lang` msil
            ON (msil.`id_seller` = msi.`id_seller` AND msil.`id_lang` = ' . (int) $idLang . ')
            WHERE msi.`active` = 1 ' . WkMpSeller::addSqlRestriction('msi') . ' LIMIT ' . (int) $start . ', ' . (int) $limit;
        } elseif (!$like && $all) { // get all seller with no limit
            $sql .= 'INNER JOIN `' . _DB_PREFIX_ . 'wk_mp_seller_lang` msil
            ON (msil.`id_seller` = msi.`id_seller` AND msil.`id_lang` = ' . (int) $idLang . ')
            WHERE msi.`active` = 1 ' . WkMpSeller::addSqlRestriction('msi');
        } elseif ($like && !$all) {  // get sellers with shop name with limit
            $sql .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_mp_seller_lang` msil
            ON (msil.`id_seller` = msi.`id_seller` AND msil.`id_lang` = ' . (int) $idLang . ')
            WHERE msi.`active` = 1 ' . WkMpSeller::addSqlRestriction('msi') . '
            AND LOWER(msil.`shop_name`) LIKE "' . pSQL($likeword) . '%"';
        } elseif ($like && $all) {  // get all seller with shop name
            $sql .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_mp_seller_lang` msil
            ON (msil.`id_seller` = msi.`id_seller` AND msil.`id_lang` = ' . (int) $idLang . ')
            WHERE msi.`active` = 1 ' . WkMpSeller::addSqlRestriction('msi') . '
            AND LOWER(msil.`shop_name`) LIKE \'%' . pSQL($likeword) . '%\' ';
        }

        $sellerInfo = Db::getInstance()->executeS($sql);
        if (empty($sellerInfo)) {
            return false;
        }

        return $sellerInfo;
    }

    /**
     * Use to send email to sellers for various reasons
     *
     * @param int $idSeller
     * @param string $subject Email subject
     * @param bool $mailFor Reason of sending email to seller
     * @param bool $reason If there is something to tell reason for seller
     *
     * @return bool
     */
    public static function sendMail($idSeller, $subject, $mailFor = false, $reason = false)
    {
        $sellerInfo = self::getSeller($idSeller);
        $idLang = $sellerInfo['default_lang']; // Seller's default language

        if ($mailFor == 1) {
            $mailReason = 'activated';
        } elseif ($mailFor == 2) {
            $mailReason = 'deactivated';
        } elseif ($mailFor == 3) {
            $mailReason = 'deleted';
        } else {
            $mailReason = 'activated';
        }

        $objSeller = new self($idSeller, $idLang);
        $mpSellerName = $objSeller->seller_firstname . ' ' . $objSeller->seller_lastname;
        $businessEmail = $objSeller->business_email;
        $mpShopName = $objSeller->shop_name;
        $phone = $objSeller->phone;
        if ($businessEmail == '') {
            $idCustomer = $objSeller->seller_customer_id;
            $objCustomer = new Customer($idCustomer);
            $businessEmail = $objCustomer->email;
        }

        $tempPath = _PS_MODULE_DIR_ . 'marketplace/mails/';

        $templateVars = [
            '{seller_name}' => $mpSellerName,
            '{mp_shop_name}' => $mpShopName,
            '{mail_reason}' => $mailReason,
            '{business_email}' => $businessEmail,
            '{phone}' => $phone,
        ];

        if (Configuration::get('WK_MP_SUPERADMIN_EMAIL')) {
            $adminEmail = Configuration::get('WK_MP_SUPERADMIN_EMAIL');
        } else {
            $idEmployee = WkMpHelper::getSupperAdmin();
            $employee = new Employee($idEmployee);
            $adminEmail = $employee->email;
        }

        $fromTitle = Configuration::get('WK_MP_FROM_MAIL_TITLE');

        if ($reason && $reason != '') {
            $templateVars['{reason_text}'] = $reason;
        } else {
            $objMp = new Marketplace();
            $templateVars['{reason_text}'] = $objMp->l('We found something inappropriate in your shop.', 'WkMpSeller');
        }

        if ($subject == 1) {
            // Seller Request Approved
            if (Configuration::get('WK_MP_MAIL_SELLER_REQ_APPROVE')) {
                Mail::Send(
                    $idLang,
                    'seller_active',
                    Mail::l('Seller Request Approved', $idLang),
                    $templateVars,
                    $businessEmail,
                    $mpSellerName,
                    $adminEmail,
                    $fromTitle,
                    null,
                    null,
                    $tempPath,
                    false,
                    null,
                    null
                );
            }
        } elseif ($subject == 2) {
            // Seller Request Disapproved

            if (Configuration::get('WK_MP_MAIL_SELLER_REQ_DISAPPROVE')) {
                Mail::Send(
                    $idLang,
                    'seller_deactive',
                    Mail::l('Seller Request Disapproved', $idLang),
                    $templateVars,
                    $businessEmail,
                    $mpSellerName,
                    $adminEmail,
                    $fromTitle,
                    null,
                    null,
                    $tempPath,
                    false,
                    null,
                    null
                );
            }
        } elseif ($subject == 3) {
            // add seller by admin approved
            if (Configuration::get('WK_MP_MAIL_SELLER_REQ_APPROVE')) {
                Mail::Send(
                    $idLang,
                    'seller_add_admin',
                    Mail::l('Seller Account Created', $idLang),
                    $templateVars,
                    $businessEmail,
                    $mpSellerName,
                    $adminEmail,
                    $fromTitle,
                    null,
                    null,
                    $tempPath,
                    false,
                    null,
                    null
                );
            }
        }

        return true;
    }

    /**
     * Mail to seller when admin delete seller account
     *
     * @param int $idSeller - Seller Id
     *
     * @return bool
     */
    public static function mailToSellerOnAccountDelete($idSeller)
    {
        $sellerDetail = WkMpSeller::getSeller($idSeller, Configuration::get('PS_LANG_DEFAULT'));
        if ($sellerDetail) {
            $sellerName = $sellerDetail['seller_firstname'] . ' ' . $sellerDetail['seller_lastname'];
            $sellerPhone = $sellerDetail['phone'];
            $shopName = $sellerDetail['shop_name'];
            $sellerEmail = $sellerDetail['business_email'];

            if (Configuration::get('WK_MP_SUPERADMIN_EMAIL')) {
                $adminEmail = Configuration::get('WK_MP_SUPERADMIN_EMAIL');
            } else {
                $idEmployee = WkMpHelper::getSupperAdmin();
                $employee = new Employee($idEmployee);
                $adminEmail = $employee->email;
            }

            $sellerVars = [
                '{seller_name}' => $sellerName,
                '{seller_shop}' => $shopName,
                '{seller_email_id}' => $sellerEmail,
                '{seller_phone}' => $sellerPhone,
            ];

            $templatePath = _PS_MODULE_DIR_ . 'marketplace/mails/';
            Mail::Send(
                (int) Configuration::get('PS_LANG_DEFAULT'),
                'mp_seller_delete',
                Mail::l('Seller Account Deleted', (int) Configuration::get('PS_LANG_DEFAULT')),
                $sellerVars,
                $sellerEmail,
                $sellerName,
                $adminEmail,
                null,
                null,
                null,
                $templatePath,
                false,
                null,
                null
            );
        }
    }

    /**
     * Get content of $templateName inside the folder marketplace/mails/current_iso_lang/ if found.
     *
     * @param string $templateName template name with extension
     * @param int $mailType Mail::TYPE_HTML or Mail::TYPE_TXT
     * @param array $var list send to smarty
     *
     * @return string
     */
    public function getMpEmailTemplateContent($templateName, $mailType, $var)
    {
        $emailConfiguration = Configuration::get('PS_MAIL_TYPE');
        if ($emailConfiguration != $mailType && $emailConfiguration != Mail::TYPE_BOTH) {
            return '';
        }

        $defaultMailTemplatePath = _PS_MODULE_DIR_ . 'marketplace/mails/_partials/' . $templateName;

        if (Tools::file_exists_cache($defaultMailTemplatePath)) {
            Context::getContext()->smarty->assign('list', $var);

            return Context::getContext()->smarty->fetch($defaultMailTemplatePath);
        }

        return '';
    }

    /**
     * Get accessibility of seller's information on front end
     *
     * @param int $idLang
     */
    public static function checkSellerAccessPermission($selectedDetailsBySeller)
    {
        $objMarketplace = new Marketplace();
        if ($objMarketplace->sellerDetailsView && Configuration::get('WK_MP_SHOW_SELLER_DETAILS')) {
            $selectedDetailsBySeller = json_decode($selectedDetailsBySeller);
            if ($selectedDetailsBySeller) {
                // Global configuration Admin settings
                $globalSellerAccessSettings = json_decode(Configuration::get('WK_MP_SELLER_DETAILS_ACCESS'));
                if ($globalSellerAccessSettings) {
                    foreach ($selectedDetailsBySeller as $detailsVal) {
                        // if any options is allowed by admin(globally) then display it
                        if (in_array($detailsVal, $globalSellerAccessSettings)) {
                            Context::getContext()->smarty->assign('WK_MP_SELLER_DETAILS_ACCESS_' . $detailsVal, 1);
                        }
                    }
                }
            }
        }
    }

    /**
     * Update seller default language in marketplace
     *
     * @param int $idLang
     */
    public static function updateSellerLanguage($idLang)
    {
        return Db::getInstance()->update(
            'wk_mp_seller',
            [
                'default_lang' => (int) Configuration::get('PS_LANG_DEFAULT'),
            ],
            'default_lang = ' . (int) $idLang
        );
    }

    /**
     * Change seller's product status
     *
     * @param int $idSeller - Seller Id
     * @param bool $active - set product status
     * @param bool $byLastStatus - change product status according to last status before seller deactivate
     *
     * @return bool
     */
    public static function changeSellerProductStatus($idSeller, $active = false, $byLastStatus = false)
    {
        $sellerProducts = WkMpSellerProduct::getSellerProduct($idSeller);
        if ($sellerProducts) {
            foreach ($sellerProducts as $product) {
                if ($product['id_ps_product']) {
                    // Get product status according to last status before seller deactivate
                    if ($byLastStatus) {
                        if ($product['status_before_deactivate']) {
                            $active = 1;
                        } else {
                            $active = 0;
                        }
                    }
                    $objProduct = new Product($product['id_ps_product']);
                    $objProduct->active = $active ? 1 : 0;
                    if ($objProduct->save()) {
                        $objSellerProduct = new WkMpSellerProduct($product['id_mp_product']);
                        $objSellerProduct->active = $active ? 1 : 0;
                        $objSellerProduct->update();
                    }
                }
            }
        }

        return true;
    }

    public function mailToAdminWhenSellerRequest($sellerName, $shopName, $businessEmail, $sellerPhone)
    {
        if (Configuration::get('WK_MP_SUPERADMIN_EMAIL')) {
            $adminEmail = Configuration::get('WK_MP_SUPERADMIN_EMAIL');
        } else {
            $idEmployee = WkMpHelper::getSupperAdmin();
            $employee = new Employee($idEmployee);
            $adminEmail = $employee->email;
        }

        $sellerVars = [
            '{seller_name}' => $sellerName,
            '{seller_shop}' => $shopName,
            '{seller_email_id}' => $businessEmail,
            '{seller_phone}' => $sellerPhone,
        ];

        Mail::Send(
            (int) Configuration::get('PS_LANG_DEFAULT'),
            'seller_request',
            Mail::l('New seller request', (int) Configuration::get('PS_LANG_DEFAULT')),
            $sellerVars,
            $adminEmail,
            null,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_ . 'marketplace/mails/',
            false,
            null,
            null
        );
    }

    public static function validateSellerUniqueShopName()
    {
        // check unique shop name and compare to other existing shop name unique
        $shopName = Tools::getValue('shop_name');
        $idSeller = Tools::getValue('id_seller');
        if ($shopName) {
            if (self::isShopNameExist(Tools::link_rewrite($shopName), $idSeller)) {
                exit('1');
            } else {
                exit('0');
            }
        }
    }

    public static function validateSellerEmail()
    {
        // check seller email and compare to other existing seller email
        $sellerEmail = Tools::getValue('seller_email');
        $idSeller = (int) Tools::getValue('id_seller');
        $customerId = !($idSeller) ? Tools::getValue('customerId') : 0;
        if ($sellerEmail) {
            if (self::isSellerEmailExist($sellerEmail, $idSeller, $customerId)) {
                exit('1');
            } else {
                exit('0');
            }
        }
    }

    public static function displayStateByCountryId()
    {
        // Get state by choosing country on seller request and edit profile page in both end
        $idCountry = Tools::getValue('id_country');
        $objState = new State();
        if ($idCountry) {
            $stateDetails = $objState->getStatesByIdCountry($idCountry);
            if ($stateDetails) {
                exit(json_encode($stateDetails));
            }
        }
        exit;
    }

    public static function deleteSellerImages()
    {
        $idSeller = Tools::getValue('id_seller');
        $target = Tools::getvalue('delete_img');

        $objMpSeller = new self($idSeller);
        $objMarketplace = new Marketplace();

        if ($target == 'seller_img') {
            $sellerImgPath = _PS_MODULE_DIR_ . 'marketplace/views/img/seller_img/' . $objMpSeller->profile_image;

            $objMpSeller->profile_image = ''; // remove from seller info table

            if (file_exists($sellerImgPath)) {
                if (unlink($sellerImgPath) && $objMpSeller->save()) {
                    $success = 1;
                }
            }
        } elseif ($target == 'shop_img') {
            $shopImgPath = _PS_MODULE_DIR_ . 'marketplace/views/img/shop_img/' . $objMpSeller->shop_image;

            $objMpSeller->shop_image = ''; // remove from seller info table

            if (file_exists($shopImgPath) && $objMpSeller->save()) {
                if (unlink($shopImgPath)) {
                    $success = 1;
                }
            }
        } elseif ($target == 'seller_banner') {
            $sellerBannerPath = _PS_MODULE_DIR_ . 'marketplace/views/img/seller_banner/' . $objMpSeller->profile_banner;

            $objMpSeller->profile_banner = ''; // remove from seller info table

            if (file_exists($sellerBannerPath) && $objMpSeller->save()) {
                if (unlink($sellerBannerPath)) {
                    $success = 1;
                }
            }
        } elseif ($target == 'shop_banner') {
            $shopBannerPath = _PS_MODULE_DIR_ . 'marketplace/views/img/shop_banner/' . $objMpSeller->shop_banner;

            $objMpSeller->shop_banner = ''; // remove from seller info table

            if (file_exists($shopBannerPath) && $objMpSeller->save()) {
                if (unlink($shopBannerPath)) {
                    $success = 1;
                }
            }
        }

        unset($objMpSeller); // unset for next time

        if (isset($success)) {
            exit(json_encode(['status' => 'ok', 'msg' => $objMarketplace->l('Image deleted successfully.', 'WkMpSeller')]));
        } else {
            exit(json_encode(['status' => 'ko', 'msg' => $objMarketplace->l('Something wrong while deleting image.', 'WkMpSeller')]));
        }
    }

    public static function validationSellerFormField($params)
    {
        $className = 'WkMpSeller';
        $data = ['status' => 'ok'];

        $objMp = new Marketplace();
        $phone = $params['wk_phone'];
        if (isset($params['mp_seller_id']) && $params['mp_seller_id']) {
            // Edit profile page
            $idSeller = $params['mp_seller_id'];
        } else {
            // Seller request page
            $idSeller = false;
        }
        $businessEmail = $params['business_email'];
        $shopNameUnique = $params['shop_name_unique'];
        $sellerLastName = trim($params['seller_lastname']);
        $sellerFirstName = trim($params['seller_firstname']);
        // Get default lang when multi-lang is ON/OFF
        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
            $defaultLang = $params['default_lang'];
        } else {
            if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') {// Admin default lang
                $defaultLang = Configuration::get('PS_LANG_DEFAULT');
            } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') {// Seller default lang
                $defaultLang = $params['current_lang_id'];
            }
        }
        $objLang = new Language((int) $defaultLang);
        if (!$objLang->active) {
            $defaultLang = Configuration::get('PS_LANG_DEFAULT');
        }
        $shopName = trim($params['shop_name_' . $defaultLang]);
        $sellerLang = Language::getLanguage((int) $defaultLang);

        if ($shopNameUnique == '') {
            $data = [
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'shop_name_unique',
                'msg' => $objMp->l('Unique name for shop is required field.', $className),
            ];
            exit(json_encode($data));
        } elseif (!Validate::isCatalogName($shopNameUnique) || !Tools::link_rewrite($shopNameUnique)) {
            $data = [
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'shop_name_unique',
                'msg' => $objMp->l('Invalid unique name for shop.', $className),
            ];
            exit(json_encode($data));
        } elseif (WkMpSeller::isShopNameExist(Tools::link_rewrite($shopNameUnique), $idSeller)) {
            $data = [
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'shop_name_unique',
                'msg' => $objMp->l('Unique name for shop is already taken. Try another.', $className),
            ];
            exit(json_encode($data));
        }

        if ($shopName == '') {
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $data = [
                    'status' => 'ko',
                    'tab' => 'wk-information',
                    'multilang' => '1',
                    'inputName' => 'shop_name_all',
                    'msg' => sprintf($objMp->l('Shop name is required in %s', $className), $sellerLang['name']),
                ];
                exit(json_encode($data));
            } else {
                $data = [
                    'status' => 'ko',
                    'tab' => 'wk-information',
                    'multilang' => '1',
                    'inputName' => 'shop_name_all',
                    'msg' => $objMp->l('Shop name is required.', $className),
                ];
                exit(json_encode($data));
            }
        } else {
            if (!Validate::isCatalogName($shopName)) {
                $data = [
                    'status' => 'ko',
                    'tab' => 'wk-information',
                    'multilang' => '1',
                    'inputName' => 'shop_name_all',
                    'msg' => sprintf($objMp->l('Shop name field %s is invalid.', $className), $sellerLang['name']),
                ];
                exit(json_encode($data));
            }
        }

        // Validate data
        $languages = Language::getLanguages();
        foreach ($languages as $language) {
            $languageName = '';
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $languageName = '(' . $language['name'] . ')';
            }
            if (isset($params['shop_name_' . $language['id_lang']]) && $params['shop_name_' . $language['id_lang']]) {
                if (!Validate::isCatalogName($params['shop_name_' . $language['id_lang']])) {
                    $data = [
                        'status' => 'ko',
                        'tab' => 'wk-information',
                        'multilang' => '1',
                        'inputName' => 'shop_name_all',
                        'msg' => sprintf($objMp->l('Shop name field %s is invalid.', $className), $languageName),
                    ];
                    exit(json_encode($data));
                }
            }
            if (isset($params['about_shop_' . $language['id_lang']]) && $params['about_shop_' . $language['id_lang']]) {
                if (!Validate::isCleanHtml($params['about_shop_' . $language['id_lang']], (int) Configuration::get('PS_ALLOW_HTML_IFRAME'))) {
                    $data = [
                        'status' => 'ko',
                        'tab' => 'wk-information',
                        'multilang' => '1',
                        'inputName' => 'wk_text_field_all',
                        'msg' => sprintf($objMp->l('Shop description field %s is invalid.', $className), $languageName),
                    ];
                    exit(json_encode($data));
                }
            }
        }

        if (!$sellerFirstName) {
            $data = [
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'seller_firstname',
                'msg' => $objMp->l('Seller first name is required field.', $className),
            ];
            exit(json_encode($data));
        } elseif (!Validate::isName($sellerFirstName)) {
            $data = [
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'seller_firstname',
                'msg' => $objMp->l('Invalid seller first name', $className),
            ];
            exit(json_encode($data));
        }

        if (!$sellerLastName) {
            $data = [
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'seller_lastname',
                'msg' => $objMp->l('Seller last name is required field.', $className),
            ];
            exit(json_encode($data));
        } elseif (!Validate::isName($sellerLastName)) {
            $data = [
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'seller_lastname',
                'msg' => $objMp->l('Invalid seller last name', $className),
            ];
            exit(json_encode($data));
        }

        if ($phone == '') {
            $data = [
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'wk_phone',
                'msg' => $objMp->l('Phone is required field.', $className),
            ];
            exit(json_encode($data));
        } elseif (!Validate::isPhoneNumber($phone)) {
            $data = [
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'wk_phone',
                'msg' => $objMp->l('Invalid phone number', $className),
            ];
            exit(json_encode($data));
        }

        if ($businessEmail == '') {
            $data = [
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'business_email',
                'msg' => $objMp->l('Email ID is required field.', $className),
            ];
            exit(json_encode($data));
        } elseif (!Validate::isEmail($businessEmail)) {
            $data = [
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'business_email',
                'msg' => $objMp->l('Invalid Email ID', $className),
            ];
            exit(json_encode($data));
        } elseif (WkMpSeller::isSellerEmailExist($businessEmail, $idSeller)) {
            $data = [
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'business_email',
                'msg' => $objMp->l('Email ID already exist', $className),
            ];
            exit(json_encode($data));
        }

        if (isset($params['fax']) && !Validate::isPhoneNumber($params['fax'])) {
            $data = [
                'status' => 'ko',
                'tab' => 'wk-information',
                'multilang' => '0',
                'inputName' => 'fax',
                'msg' => $objMp->l('Fax must be numeric.', $className),
            ];
            exit(json_encode($data));
        }

        if (Configuration::get('WK_MP_SELLER_TAX_IDENTIFICATION_NUMBER')
        || (Tools::getValue('controller') == 'AdminSellerInfoDetail')
        ) {
            if (isset($params['tax_identification_number'])
            && !Validate::isGenericName($params['tax_identification_number'])
            ) {
                $data = [
                    'status' => 'ko',
                    'tab' => 'wk-information',
                    'multilang' => '0',
                    'inputName' => 'tax_identification_number',
                    'msg' => $objMp->l('Tax Identification Number must be valid.', $className),
                ];
                exit(json_encode($data));
            }
        }

        if (isset($params['address']) && !Validate::isAddress($params['address'])) {
            $data = [
                'status' => 'ko',
                'tab' => 'wk-contact',
                'multilang' => '0',
                'inputName' => 'address',
                'msg' => $objMp->l('Address format is invalid.', $className),
            ];
            exit(json_encode($data));
        }

        if (Configuration::get('WK_MP_SELLER_COUNTRY_NEED')) {
            $postcode = $params['postcode'];
            $countryNeedZipCode = true;
            $countryZipCodeFormat = false;
            if ($params['id_country']) {
                $country = new Country($params['id_country']);
                $countryNeedZipCode = $country->need_zip_code;
                $countryZipCodeFormat = $country->zip_code_format;
            }

            if (!$postcode && $countryNeedZipCode) {
                $data = [
                    'status' => 'ko',
                    'tab' => 'wk-contact',
                    'multilang' => '0',
                    'inputName' => 'postcode',
                    'msg' => $objMp->l('Zip/postal code can not be empty.', $className),
                ];
                exit(json_encode($data));
            } elseif ($countryZipCodeFormat) {
                if (!$country->checkZipCode($postcode)) {
                    $data = [
                        'status' => 'ko',
                        'tab' => 'wk-contact',
                        'multilang' => '0',
                        'inputName' => 'postcode',
                        'msg' => sprintf($objMp->l('The Zip/postal code you\'ve entered is invalid. It must follow this format: %s', $className), str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $countryZipCodeFormat)))),
                    ];
                    exit(json_encode($data));
                }
            } elseif (!Validate::isPostCode($postcode)) {
                $data = [
                    'status' => 'ko',
                    'tab' => 'wk-contact',
                    'multilang' => '0',
                    'inputName' => 'postcode',
                    'msg' => $objMp->l('Invalid Zip/Postal code', $className),
                ];
                exit(json_encode($data));
            }

            $sellerCity = trim($params['city']);
            if (!$sellerCity) {
                $data = [
                    'status' => 'ko',
                    'tab' => 'wk-contact',
                    'multilang' => '0',
                    'inputName' => 'city',
                    'msg' => $objMp->l('City can not be empty.', $className),
                ];
                exit(json_encode($data));
            } elseif (!Validate::isCityName($sellerCity)) {
                $data = [
                    'status' => 'ko',
                    'tab' => 'wk-contact',
                    'multilang' => '0',
                    'inputName' => 'city',
                    'msg' => $objMp->l('Invalid city name', $className),
                ];
                exit(json_encode($data));
            }

            if (!$params['id_country']) {
                $data = [
                    'status' => 'ko',
                    'tab' => 'wk-contact',
                    'multilang' => '0',
                    'inputName' => 'id_country',
                    'msg' => $objMp->l('Country is required field.', $className),
                ];
                exit(json_encode($data));
            }

            // if state available in selected country
            if ($params['state_available']) {
                if (!$params['id_state']) {
                    $data = [
                        'status' => 'ko',
                        'tab' => 'wk-contact',
                        'multilang' => '0',
                        'inputName' => 'id_state',
                        'msg' => $objMp->l('State is required field.', $className),
                    ];
                    exit(json_encode($data));
                }
            }
        } else {
            if (isset($params['postcode']) && $params['postcode']) {
                $postcode = $params['postcode'];
                if ($params['id_country']) {
                    $country = new Country($params['id_country']);
                    if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                        $data = [
                            'status' => 'ko',
                            'tab' => 'wk-contact',
                            'multilang' => '0',
                            'inputName' => 'postcode',
                            'msg' => sprintf($objMp->l('The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s', $className), str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format)))),
                        ];
                        exit(json_encode($data));
                    }
                } elseif (!Validate::isPostCode($postcode)) {
                    $data = [
                        'status' => 'ko',
                        'tab' => 'wk-contact',
                        'multilang' => '0',
                        'inputName' => 'postcode',
                        'msg' => $objMp->l('Invalid Zip/Postal code', $className),
                    ];
                    exit(json_encode($data));
                }
            }
        }

        if (Configuration::get('WK_MP_SOCIAL_TABS') && Configuration::get('WK_MP_SELLER_FACEBOOK')) {
            if (isset($params['facebook_id']) && $params['facebook_id']
            && !Validate::isGenericName($params['facebook_id'])) {
                $data = [
                    'status' => 'ko',
                    'tab' => 'wk-social',
                    'multilang' => '0',
                    'inputName' => 'facebook_id',
                    'msg' => $objMp->l('Facebook ID is invalid.', $className),
                ];
                exit(json_encode($data));
            }
        }

        if (Configuration::get('WK_MP_SOCIAL_TABS') && Configuration::get('WK_MP_SELLER_TWITTER')) {
            if (isset($params['twitter_id']) && $params['twitter_id']
            && !Validate::isGenericName($params['twitter_id'])) {
                $data = [
                    'status' => 'ko',
                    'tab' => 'wk-social',
                    'multilang' => '0',
                    'inputName' => 'twitter_id',
                    'msg' => $objMp->l('Twitter ID is invalid.', $className),
                ];
                exit(json_encode($data));
            }
        }

        if (Configuration::get('WK_MP_SOCIAL_TABS') && Configuration::get('WK_MP_SELLER_YOUTUBE')) {
            if (isset($params['youtube_id']) && $params['youtube_id']
            && !Validate::isGenericName($params['youtube_id'])) {
                $data = [
                    'status' => 'ko',
                    'tab' => 'wk-social',
                    'multilang' => '0',
                    'inputName' => 'youtube_id',
                    'msg' => $objMp->l('Youtube ID is invalid.', $className),
                ];
                exit(json_encode($data));
            }
        }

        if (Configuration::get('WK_MP_SOCIAL_TABS') && Configuration::get('WK_MP_SELLER_INSTAGRAM')) {
            if (isset($params['instagram_id']) && $params['instagram_id']
            && !Validate::isGenericName($params['instagram_id'])) {
                $data = [
                    'status' => 'ko',
                    'tab' => 'wk-social',
                    'multilang' => '0',
                    'inputName' => 'instagram_id',
                    'msg' => $objMp->l('Instagram ID is invalid.', $className),
                ];
                exit(json_encode($data));
            }
        }

        if (isset($params['payment_detail']) && $params['payment_detail']) {
            if (!$params['payment_mode_id']) {
                $data = [
                    'status' => 'ko',
                    'tab' => 'wk-seller-payment-details',
                    'multilang' => '0',
                    'inputName' => 'payment_mode_id',
                    'msg' => $objMp->l('Payment mode is required in case of filling account details.', $className),
                ];
                exit(json_encode($data));
            } elseif (!Validate::isGenericName($params['payment_detail'])) {
                $data = [
                    'status' => 'ko',
                    'tab' => 'wk-seller-payment-details',
                    'multilang' => '0',
                    'inputName' => 'payment_detail',
                    'msg' => $objMp->l('Invalid account details', $className),
                ];
                exit(json_encode($data));
            }
        }

        if (Configuration::get('WK_MP_TERMS_AND_CONDITIONS_STATUS')
            && !$idSeller
            && (Tools::getValue('controller') == 'sellerrequest')
            && !isset($params['terms_and_conditions'])
        ) {
            $data = [
                'status' => 'ko',
                'tab' => '',
                'multilang' => '0',
                'inputName' => 'terms_and_conditions',
                'msg' => $objMp->l('Please agree the terms and condition.', $className),
            ];
            exit(json_encode($data));
        }

        exit(json_encode($data));
    }

    /**
     * Delete source file directory of seller on seller delete or module uninstall
     *
     * @param int $idMpSeller - Seller id
     *
     * @return bool
     */
    public static function deleteTinymceSourceFile($idMpSeller = false)
    {
        $sourceDeleted = true;
        if ($idMpSeller) {
            $mpSellerDirPath = _PS_MODULE_DIR_ . 'marketplace/libs/source/' . $idMpSeller;
            $sourceDeleted = WkMpSeller::deleteSellerTinymceSourceFile($mpSellerDirPath);
        } else {
            // Get source all directories
            $sourchAllDir = glob(_PS_MODULE_DIR_ . 'marketplace/libs/source/*');
            if ($sourchAllDir) {
                foreach ($sourchAllDir as $sourchEachDir) {
                    $sourceDeleted = WkMpSeller::deleteSellerTinymceSourceFile($sourchEachDir);
                    if (!$sourceDeleted) {
                        break;
                    }
                }
            }
        }

        if (!$sourceDeleted) {
            return false;
        }

        return true;
    }

    public static function deleteSellerTinymceSourceFile($mpSellerDirPath)
    {
        if (file_exists($mpSellerDirPath) && is_dir($mpSellerDirPath)) {
            foreach (glob($mpSellerDirPath . '/*.*') as $filename) {
                if (is_file($filename)) {
                    unlink($filename);
                }
            }

            if (!rmdir($mpSellerDirPath)) {
                return false;
            }
        }

        return true;
    }

    public function updateSellerInformation($sellerInfo, $customerEmail = null)
    {
        $idSeller = $sellerInfo['id_seller'];
        // delete seller all images ie. profile image, shop image and banners
        $this->unlinkSellerImages($idSeller);

        $sellerEmail = 'wk_anonymous_' . $idSeller . '@anonymous.com';
        $result = Db::getInstance()->update(
            'wk_mp_seller',
            [
                'shop_name_unique' => 'Anonymous',
                'link_rewrite' => 'Anonymous',
                'seller_firstname' => 'Anonymous',
                'seller_lastname' => 'Anonymous',
                'business_email' => pSQL($sellerEmail),
                'phone' => '',
                'fax' => '',
                'address' => '',
                'postcode' => '',
                'city' => '',
                'id_country' => '',
                'id_state' => '',
                'facebook_id' => '',
                'twitter_id' => '',
                'youtube_id' => '',
                'instagram_id' => '',
                'profile_image' => '',
                'profile_banner' => '',
                'shop_image' => '',
                'shop_banner' => '',
            ],
            'id_seller = ' . (int) $idSeller
        );
        if ($result) {
            Db::getInstance()->update(
                'wk_mp_seller_lang',
                [
                    'shop_name' => 'Anonymous',
                    'about_shop' => 'Anonymous',
                ],
                'id_seller = ' . (int) $idSeller
            );
            Db::getInstance()->update(
                'wk_mp_seller_order',
                [
                    'seller_shop' => 'Anonymous',
                    'seller_firstname' => 'Anonymous',
                    'seller_lastname' => 'Anonymous',
                    'seller_email' => pSQL($sellerEmail),
                ],
                'seller_customer_id = ' . (int) $sellerInfo['seller_customer_id']
            );
            Db::getInstance()->update(
                'wk_mp_seller_order_detail',
                [
                    'seller_name' => 'Anonymous',
                ],
                'seller_customer_id = ' . (int) $sellerInfo['seller_customer_id']
            );
            // delete seller all images ie. profile image, shop image and banners
            $this->unlinkSellerImages($idSeller);

            Db::getInstance()->update(
                'wk_mp_seller_help_desk',
                [
                    'customer_email' => 'anonymous@anonymous.com',
                ],
                'customer_email = \'' . pSQL($customerEmail) . '\''
            );

            Db::getInstance()->update(
                'wk_mp_seller_review',
                [
                    'customer_email' => 'anonymous@anonymous.com',
                ],
                'customer_email = \'' . pSQL($customerEmail) . '\''
            );
        } else {
            return false;
        }
    }

    public function exportSellerInformation($idCustomer)
    {
        $sellerInfo = $this->getSellerByCustomerId($idCustomer);
        $domain = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/marketplace/views/img/';
        if ($sellerInfo) {
            if (isset($sellerInfo['shop_image']) && $sellerInfo['shop_image']
                && file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/shop_img/' . $sellerInfo['shop_image'])
            ) {
                $shopImage = $domain . 'shop_img/' . $sellerInfo['shop_image'];
            }

            if (isset($sellerInfo['profile_banner']) && $sellerInfo['profile_banner']
            && file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/seller_banner/' . $sellerInfo['profile_banner'])
            ) {
                $profileBanner = $domain . 'seller_banner/' . $sellerInfo['profile_banner'];
            }

            if (isset($sellerInfo['shop_banner']) && $sellerInfo['shop_banner']
            && file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/shop_banner/' . $sellerInfo['shop_banner'])
            ) {
                $shopBanner = $domain . 'shop_banner/' . $sellerInfo['shop_banner'];
            }

            if (isset($sellerInfo['profile_image']) && $sellerInfo['profile_image']
            && file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/seller_img/' . $sellerInfo['profile_image'])
            ) {
                $profileImage = $domain . 'seller_img/' . $sellerInfo['profile_image'];
            }
        }
        $idLang = Context::getContext()->language->id;
        $result = Db::getInstance()->executeS(
            'SELECT
                `shop_name_unique` as UniqueShopName,
                `seller_firstname` as FirstName,
                `seller_lastname` as LastName,
                `business_email` as Email,
                `phone` as Phone,
                `fax` as Fax,
                `address` as Address,
                `postcode` as Postcode,
                `city` as City,
                s.`name` as State,
                cl.`name` as Country,
                `facebook_id` as FacebookID,
                `youtube_id` as YoutubeID,
                `instagram_id` as InstagramID,
                `profile_image`,
                `profile_banner`,
                `shop_image`,
                `shop_banner` FROM ' . _DB_PREFIX_ . 'wk_mp_seller sl
                LEFT JOIN ' . _DB_PREFIX_ . 'state s on (s.`id_state` = sl.`id_state`)
                LEFT JOIN ' . _DB_PREFIX_ . 'country_lang cl on (cl.`id_country` = sl.`id_country`)
                WHERE sl.`seller_customer_id` = ' . (int) $idCustomer . '
                AND cl.`id_lang` = ' . (int) $idLang . WkMpSeller::addSqlRestriction()
        );
        if ($result) {
            $result[0]['profile_image'] = isset($profileImage) ? $profileImage : $result[0]['profile_image'];
            $result[0]['profile_banner'] = isset($profileBanner) ? $profileBanner : $result[0]['profile_banner'];
            $result[0]['shop_image'] = isset($shopImage) ? $shopImage : $result[0]['shop_image'];
            $result[0]['shop_banner'] = isset($shopBanner) ? $shopBanner : $result[0]['shop_banner'];

            return $result;
        }

        return false;
    }

    /**
     * Delete staff data if mpsellerstaff module is disabled and staff is becoming a seller.
     *
     * @param int $idStaff Staff id which to be delete
     *
     * @return bool
     */
    public static function deleteStaffDataIfBecomeSeller($idCustomer)
    {
        // If mpsellerstaff module is installed but currently disabled and current customer was a staff then delete this customer as staff from mpsellerstaff module table. Because a customer can not be a seller and a staff both in same time.
        $staffDetails = Db::getInstance()->getRow(
            'SELECT * FROM  `' . _DB_PREFIX_ . 'wk_mp_seller_staff` WHERE `id_customer_staff` = ' . (int) $idCustomer
        );
        if ($staffDetails) {
            // If customer is already a staff
            $idStaff = $staffDetails['id_staff'];
            Hook::exec('actionBeforeMpStaffDelete', ['id_staff' => (int) $idStaff]);

            $staffData = Db::getInstance()->delete(
                'wk_mp_seller_staff',
                'id_staff = ' . (int) $idStaff
            );
            $permission = Db::getInstance()->delete(
                'wk_mp_seller_staff_permission',
                'id_staff = ' . (int) $idStaff
            );
            $specificPermission = Db::getInstance()->delete(
                'wk_mp_seller_staff_specific_permission',
                'id_staff = ' . (int) $idStaff
            );

            if ($staffData && $permission && $specificPermission) {
                return true;
            }

            return false;
        }

        return true;
    }

    /**
     * Get value field based on condition.
     *
     * @param string $fieldName - table column name
     * @param string $condition
     * @param int $id
     *
     * @return bool|array
     */
    public static function getFieldValue($fieldName, $condition, $id)
    {
        return Db::getInstance()->getValue(
            'SELECT `' . $fieldName . '` FROM  `' . _DB_PREFIX_ . 'wk_mp_seller` WHERE `' . $condition . '` = ' . (int) $id
        );
    }

    public static function getSellerLoginAllThemes()
    {
        $objModule = new Marketplace();

        return [
            '1' => $objModule->l('Theme 1', 'WkMpSeller'),
            '2' => $objModule->l('Theme 2', 'WkMpSeller'),
            '3' => $objModule->l('Theme 3', 'WkMpSeller'),
        ];
    }

    public static function addSqlRestriction($alias = null)
    {
        if ($alias) {
            return Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, $alias); // If share seller allowed (customer share)
        // return Shop::addSqlRestriction(false, $alias); //If share seller don't allow
        } else {
            return Shop::addSqlRestriction(Shop::SHARE_CUSTOMER); // If share seller allowed (customer share)
            // return Shop::addSqlRestriction(); //If share seller don't allow
        }
    }
}
