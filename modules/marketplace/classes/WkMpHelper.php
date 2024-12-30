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

class WkMpHelper extends ObjectModel
{
    /**
     * Get random name
     *
     * @param int $length length of the string
     *
     * @return string
     */
    public static function randomImageName($length = 6)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $rand = '';

        for ($i = 0; $i < $length; ++$i) {
            $rand = $rand . $characters[mt_rand(0, Tools::strlen($characters) - 1)];
        }

        return $rand;
    }

    /**
     * Upload Seller Product Images or any other images by using this function
     *
     * @param string $dir Path where to upload
     * @param float $width Image width
     * @param float $height Image height
     *
     * @return bool|int
     */
    public static function uploadMpImages($image, $dirAbsPath, $width = false, $height = false)
    {
        if (!$image) {
            return false;
        }

        if ($image['error']) {
            return $image['error'];
        }

        if (!$width) {
            $width = 200;
        }

        if (!$height) {
            $height = 200;
        }

        if (!ImageManager::isCorrectImageFileExt($image['name'])) {
            return 2;
        }

        return ImageManager::resize($image['tmp_name'], $dirAbsPath, $width, $height);
    }

    /**
     * Ureate with new row with default lang's value when admin add new language
     *
     * @param int $newIdLang New Language ID
     * @param string $lang_tables Table names
     *
     * @return bool
     */
    public static function updateIdLangInLangTables($newIdLang, $langTables, $primaryKey = false)
    {
        if ($langTables) {
            foreach ($langTables as $tables) {
                if ($primaryKey) {
                    $id = $primaryKey;
                } else {
                    if ($tables == 'wk_mp_seller') {
                        $id = 'id_seller';
                    } else {
                        $id = 'id';
                    }
                }
                $tableIds = Db::getInstance()->executeS('SELECT ' . $id . ' FROM `' . _DB_PREFIX_ . $tables . '`');
                if ($tableIds) {
                    foreach ($tableIds as $tableId) {
                        $tableLangs = Db::getInstance()->getRow('SELECT * FROM `' . _DB_PREFIX_ . $tables . '_lang`
                            WHERE ' . $id . ' = ' . pSQL($tableId[$id]) . '
                            AND `id_lang` = ' . (int) Configuration::get('PS_LANG_DEFAULT'));

                        if ($tableLangs) {
                            $tableValue = '';
                            foreach ($tableLangs as $key => $value) {
                                if ($key == $id) {
                                    $tableValue = "'" . $value . "'";
                                } elseif ($key == 'id_lang') {
                                    $tableValue = $tableValue . ', ' . "'" . $newIdLang . "'";
                                } else {
                                    $content = str_replace("'", "\'", $value);
                                    $tableValue = $tableValue . ', ' . "'" . $content . "'";
                                }
                            }
                        }

                        Db::getInstance()->execute(
                            'INSERT INTO `' . _DB_PREFIX_ . $tables . '_lang` VALUES (' . $tableValue . ')'
                        );
                    }
                }
            }
        }
    }

    /**
     * Set default lang at every form of module according to configuration multi-lang
     *
     * @param int $idSeller Seller ID
     *
     * @return bool
     */
    public static function assignDefaultLang($idSeller)
    {
        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
            Context::getContext()->smarty->assign('allow_multilang', 1);
            $currentLang = WkMpSeller::getSellerDefaultLanguage($idSeller);
        } else {
            Context::getContext()->smarty->assign('allow_multilang', 0);

            if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') {
                $currentLang = Configuration::get('PS_LANG_DEFAULT');
            } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') {
                $currentLang = WkMpSeller::getSellerDefaultLanguage($idSeller);
            }
        }
        $objLang = new Language((int) $currentLang);
        if (!$objLang->active) {
            $currentLang = Configuration::get('PS_LANG_DEFAULT');
        }
        if ($idSeller) {
            $mpSeller = new WkMpSeller($idSeller);
            $sellerDefaultLang = $mpSeller->default_lang;
            $objLang = new Language((int) $sellerDefaultLang);
            if (!$objLang->active) {
                $sellerDefaultLang = Configuration::get('PS_LANG_DEFAULT');
            }
            Context::getContext()->smarty->assign('default_lang', $sellerDefaultLang);
        }

        // assign image max size limit
        WkMpHelper::assignPsFileMaxSize();

        if (_PS_VERSION_ >= '1.7.3.0') {
            // Prestashop added this feature in PS V1.7.3.0 and above
            Context::getContext()->smarty->assign('deliveryTimeAllowed', 1);
        }

        Context::getContext()->smarty->assign('languages', Language::getLanguages());
        Context::getContext()->smarty->assign('total_languages', count(Language::getLanguages()));
        Context::getContext()->smarty->assign('current_lang', Language::getLanguage((int) $currentLang));
        Context::getContext()->smarty->assign('multi_lang', Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE'));
        Context::getContext()->smarty->assign('multi_def_lang_off', Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG'));
    }

    /**
     * Assign Prestashop Default Max Size Length For Images and Files to be uploaded
     *
     * @return bool
     */
    public static function assignPsFileMaxSize()
    {
        $objUploader = new Uploader();
        $psUploaderSize = $objUploader->getPostMaxSizeBytes();

        Context::getContext()->smarty->assign('psUploaderSize', $psUploaderSize);
        Context::getContext()->smarty->assign('post_max_size', ini_get('post_max_size'));
    }

    /**
     * Get Super Admin Of Prestashop
     *
     * @return int
     */
    public static function getSupperAdmin()
    {
        $data = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'employee` ORDER BY `id_employee`');
        if ($data) {
            foreach ($data as $emp) {
                $employee = new Employee($emp['id_employee']);
                if ($employee->isSuperAdmin()) {
                    return $emp['id_employee'];
                }
            }
        }

        return false;
    }

    /**
     * To avoid caching of image
     *
     * @return int
     */
    public static function getTimestamp()
    {
        $date = new DateTime();

        return $date->getTimestamp();
    }

    /**
     * Get Seller Default Language from form according to config settings when seller add product or update product
     *
     * @param int $sellerDefaultLanguage seller current default language
     *
     * @return int
     */
    public static function getDefaultLanguageBeforeFormSave($sellerDefaultLanguage)
    {
        // If multi-lang is OFF then PS default lang will be default lang for seller
        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
            $defaultLang = $sellerDefaultLanguage;
        } else {
            if (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '1') {// Admin default lang
                $defaultLang = Configuration::get('PS_LANG_DEFAULT');
            } elseif (Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG') == '2') {// Seller default lang
                $defaultLang = $sellerDefaultLanguage;
            }
        }

        $objLang = new Language((int) $defaultLang);
        if (!$objLang->active) {
            $defaultLang = Configuration::get('PS_LANG_DEFAULT');
        }

        return $defaultLang;
    }

    /**
     * Assign global static variable on tpl
     *
     * @return assign
     */
    public static function assignGlobalVariables()
    {
        $objProduct = new Product();
        $context = Context::getContext();
        $context->smarty->assign([
            'mp_image_dir' => _MODULE_DIR_ . 'marketplace/views/img/',
            'module_dir' => _MODULE_DIR_,
            'img_ps_dir' => _PS_IMG_DIR_,
            'id_customer' => $context->customer->id,
            'link' => $context->link,
            'logged' => $context->customer->isLogged(),
            'title_text_color' => Configuration::get('WK_MP_TITLE_TEXT_COLOR'),
            'title_bg_color' => Configuration::get('WK_MP_TITLE_BG_COLOR'),
            'defaultTaxRuleGroup' => $objProduct->getIdTaxRulesGroup(),
            'ps_img_dir' => _PS_IMG_ . 'l/',
        ]);
    }

    /**
     * Define global js variable on js file
     *
     * @return defined
     */
    public static function defineGlobalJSVariables()
    {
        $context = Context::getContext();
        $objMp = new Marketplace();
        $jsVars = [
            'mp_image_dir' => _MODULE_DIR_ . 'marketplace/views/img/',
            'module_dir' => _MODULE_DIR_,
            'img_dir_l' => _PS_IMG_ . 'l/',
            'multi_lang' => Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE'),
            'iso' => $context->language->iso_code,
            'mp_tinymce_path' => _MODULE_DIR_ . 'marketplace/libs',
            'mp_tinymce_file_manager' => $objMp->l('File manager', 'WkMpHelper'),
            'id_lang' => $context->language->id,
        ];

        Media::addJsDef($jsVars);
    }

    /**
     * Create a new row with default lang value when admin add new language - used in mp addons
     *
     * @param [int] $newLangId - new language id
     * @param [string] $langTables - lang tables
     *
     * @return bool
     */
    public static function insertLangIdinAllTables($newLangId, $langTables)
    {
        $langId = Configuration::get('PS_LANG_DEFAULT');
        if ($langTables) {
            foreach ($langTables as $tables) {
                $tableIdData = Db::getInstance()->executeS('SELECT `id` FROM `' . _DB_PREFIX_ . $tables . '` ');
                if ($tableIdData) {
                    foreach ($tableIdData as $tabledata) {
                        $tableLangData = Db::getInstance()->getRow(
                            'SELECT * FROM `' . _DB_PREFIX_ . $tables . '_lang` WHERE `id` = ' . $tabledata['id']
                            . ' AND `id_lang` = ' . (int) $langId
                        );

                        if ($tableLangData) {
                            $tableAllVal = '';
                            foreach ($tableLangData as $table_key => $tableVal) {
                                if ($table_key == 'id') {
                                    $tableAllVal = "'" . $tableVal . "'";
                                } elseif ($table_key == 'id_lang') {
                                    $tableAllVal = $tableAllVal . ', ' . "'" . $newLangId . "'";
                                } else {
                                    $content = str_replace("'", "\'", $tableVal);
                                    $tableAllVal = $tableAllVal . ', ' . "'" . $content . "'";
                                }
                            }
                        }

                        Db::getInstance()->execute(
                            'INSERT INTO `' . _DB_PREFIX_ . $tables . '_lang` VALUES (' . $tableAllVal . ')'
                        );
                    }
                }
            }
        }
    }

    public static function setSellerAccessOnly()
    {
        if (!Context::getContext()->customer->id) {
            Tools::redirect(__PS_BASE_URI__ . 'pagenotfound');
        } else {
            $seller = WkMpSeller::getSellerDetailByCustomerId(Context::getContext()->customer->id);
            if (!$seller) {
                Tools::redirect(__PS_BASE_URI__ . 'pagenotfound');
            }
        }
    }

    public static function setStaffHook($idCustomer, $controllerName, $relatedId, $action)
    {
        // To manage staff log (changes add/update/delete)
        Hook::exec('actionAfterStaffUpdation', [
            'id_customer_staff' => $idCustomer,
            'controller_name' => $controllerName,
            'related_id' => $relatedId,
            'action' => $action,  // 3 for Delete action
        ]);
    }

    public static function productTabPermission()
    {
        $tabData = ['view' => 1, 'add' => 1, 'edit' => 1, 'delete' => 1];
        $permissionData = [
            'combinationPermission' => $tabData,
            'featuresPermission' => $tabData,
            'shippingPermission' => $tabData,
            'seoPermission' => $tabData,
            'optionsPermission' => $tabData,
        ];

        return $permissionData;
    }

    public static function isMultiShopEnabled()
    {
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            return true;
        } else {
            return false;
        }
    }

    public static function getAllMimeType()
    {
        return [
            'ez' => 'application/andrew-inset',
            'hqx' => 'application/mac-binhex40',
            'cpt' => 'application/mac-compactpro',
            'doc' => 'application/msword',
            'oda' => 'application/oda',
            'pdf' => 'application/pdf',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            'smi' => 'application/smil',
            'smil' => 'application/smil',
            'wbxml' => 'application/vnd.wap.wbxml',
            'wmlc' => 'application/vnd.wap.wmlc',
            'wmlsc' => 'application/vnd.wap.wmlscriptc',
            'bcpio' => 'application/x-bcpio',
            'vcd' => 'application/x-cdlink',
            'pgn' => 'application/x-chess-pgn',
            'cpio' => 'application/x-cpio',
            'csh' => 'application/x-csh',
            'dcr' => 'application/x-director',
            'dir' => 'application/x-director',
            'dxr' => 'application/x-director',
            'dvi' => 'application/x-dvi',
            'spl' => 'application/x-futuresplash',
            'gtar' => 'application/x-gtar',
            'hdf' => 'application/x-hdf',
            'js' => 'application/x-javascript',
            'skp' => 'application/x-koan',
            'skd' => 'application/x-koan',
            'skt' => 'application/x-koan',
            'skm' => 'application/x-koan',
            'latex' => 'application/x-latex',
            'nc' => 'application/x-netcdf',
            'cdf' => 'application/x-netcdf',
            'sh' => 'application/x-sh',
            'shar' => 'application/x-shar',
            'swf' => 'application/x-shockwave-flash',
            'sit' => 'application/x-stuffit',
            'sv4cpio' => 'application/x-sv4cpio',
            'sv4crc' => 'application/x-sv4crc',
            'tar' => 'application/x-tar',
            'tcl' => 'application/x-tcl',
            'tex' => 'application/x-tex',
            'texinfo' => 'application/x-texinfo',
            'texi' => 'application/x-texinfo',
            't' => 'application/x-troff',
            'tr' => 'application/x-troff',
            'roff' => 'application/x-troff',
            'man' => 'application/x-troff-man',
            'me' => 'application/x-troff-me',
            'ms' => 'application/x-troff-ms',
            'ustar' => 'application/x-ustar',
            'src' => 'application/x-wais-source',
            'xhtml' => 'application/xhtml+xml',
            'xht' => 'application/xhtml+xml',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar',
            'au' => 'audio/basic',
            'snd' => 'audio/basic',
            'mid' => 'audio/midi',
            'midi' => 'audio/midi',
            'kar' => 'audio/midi',
            'mpga' => 'audio/mpeg',
            'mp2' => 'audio/mpeg',
            'mp3' => 'audio/mpeg',
            'aif' => 'audio/x-aiff',
            'aiff' => 'audio/x-aiff',
            'aifc' => 'audio/x-aiff',
            'm3u' => 'audio/x-mpegurl',
            'ram' => 'audio/x-pn-realaudio',
            'rm' => 'audio/x-pn-realaudio',
            'rpm' => 'audio/x-pn-realaudio-plugin',
            'ra' => 'audio/x-realaudio',
            'wav' => 'audio/x-wav',
            'pdb' => 'chemical/x-pdb',
            'xyz' => 'chemical/x-xyz',
            'bmp' => 'image/bmp',
            'gif' => 'image/gif',
            'ief' => 'image/ief',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'jpe' => 'image/jpeg',
            'png' => 'image/png',
            'tiff' => 'image/tiff',
            'tif' => 'image/tif',
            'djvu' => 'image/vnd.djvu',
            'djv' => 'image/vnd.djvu',
            'wbmp' => 'image/vnd.wap.wbmp',
            'ras' => 'image/x-cmu-raster',
            'pnm' => 'image/x-portable-anymap',
            'pbm' => 'image/x-portable-bitmap',
            'pgm' => 'image/x-portable-graymap',
            'ppm' => 'image/x-portable-pixmap',
            'rgb' => 'image/x-rgb',
            'xbm' => 'image/x-xbitmap',
            'xpm' => 'image/x-xpixmap',
            'xwd' => 'image/x-windowdump',
            'igs' => 'model/iges',
            'iges' => 'model/iges',
            'msh' => 'model/mesh',
            'mesh' => 'model/mesh',
            'silo' => 'model/mesh',
            'wrl' => 'model/vrml',
            'vrml' => 'model/vrml',
            'css' => 'text/css',
            'html' => 'text/html',
            'htm' => 'text/html',
            'asc' => 'text/plain',
            'txt' => 'text/plain',
            'rtx' => 'text/richtext',
            'rtf' => 'text/rtf',
            'sgml' => 'text/sgml',
            'sgm' => 'text/sgml',
            'tsv' => 'text/tab-seperated-values',
            'wml' => 'text/vnd.wap.wml',
            'wmls' => 'text/vnd.wap.wmlscript',
            'etx' => 'text/x-setext',
            'xml' => 'text/xml',
            'xsl' => 'text/xml',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'mpe' => 'video/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'mxu' => 'video/vnd.mpegurl',
            'avi' => 'video/x-msvideo',
            'movie' => 'video/x-sgi-movie',
            'ice' => 'x-conference-xcooltalk',
            'epub' => 'application/epub+zip',
        ];
    }

    public static function assignHeaderData($seller)
    {
        if ($seller) {
            $objLoginConf = new WkMpLoginConfigration();
            $themeConf = $objLoginConf->getShopThemeConfigration(
                Context::getContext()->shop->id,
                Configuration::get('WK_MP_SELLER_LOGIN_THEME'),
                Context::getContext()->language->id
            );
            if ($themeConf) {
                Context::getContext()->smarty->assign('themeConf', $themeConf);
            }

            $module = Module::getInstanceByName('marketplace');
            $wkLogoDir = _PS_MODULE_DIR_ . $module->name . '/views/img/mpsellerlogin/';
            $wkLogoDirShop = _PS_MODULE_DIR_ . $module->name . '/views/img/mpsellerlogin/'
            . Context::getContext()->shop->id . '/';
            $imgSrc = glob($wkLogoDir . 'logo.*');
            $imgSrcShop = glob($wkLogoDirShop . 'logo.*');
            if ($imgSrcShop && file_exists($imgSrcShop[0])) {
                $ext = pathinfo($imgSrcShop[0], PATHINFO_EXTENSION);

                $wkLogoUrl = _MODULE_DIR_ . $module->name . '/views/img/mpsellerlogin/'
                . Context::getContext()->shop->id . '/logo.' . $ext;
                Context::getContext()->smarty->assign('wk_logo_url', $wkLogoUrl);
            } elseif ($imgSrc && file_exists($imgSrc[0])) {
                $ext = pathinfo($imgSrc[0], PATHINFO_EXTENSION);

                $wkLogoUrl = _MODULE_DIR_ . $module->name . '/views/img/mpsellerlogin/logo.' . $ext;
                Context::getContext()->smarty->assign('wk_logo_url', $wkLogoUrl);
            }

            if ($sellerProfileImg = WkMpSeller::getSellerImageLink($seller)) {
                Context::getContext()->smarty->assign('wk_profile_image', $sellerProfileImg);
            } else {
                Context::getContext()->smarty->assign(
                    'wk_profile_image',
                    _MODULE_DIR_ . $module->name . '/views/img/seller_img/defaultimage.jpg'
                );
            }

            Context::getContext()->smarty->assign('mp_shop_name', $seller['link_rewrite']);
            Context::getContext()->smarty->assign(
                'seller_name',
                $seller['seller_firstname'] . ' ' . $seller['seller_lastname']
            );
        }
    }

    public static function displayPrice($price, $currency = null)
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '<')) {
            return Tools::displayPrice($price, $currency = null);
        }
        if (!is_numeric($price)) {
            return $price;
        }

        $context = Context::getContext();
        $currency = $currency ?: $context->currency;

        if (is_int($currency)) {
            $currency = Currency::getCurrencyInstance($currency);
        }

        $locale = Tools::getContextLocale($context);
        $currencyCode = is_array($currency) ? $currency['iso_code'] : $currency->iso_code;

        return $locale->formatPrice($price, $currencyCode);
    }

    public static function checkScriptInHtml($html)
    {
        if (trim($html)) {
            $dom = new DOMDocument();
            @$dom->loadHTML(htmlspecialchars_decode($html));
            $script = $dom->getElementsByTagName('script');

            return $script->length;
        } else {
            return 0;
        }
    }
}
