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

class AdminMpCustomizeLoginController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'wk_mp_seller_login_content';
        $this->className = 'WkMpLoginContent';
        parent::__construct();
        $this->toolbar_title = $this->l('Seller Login Configuration');

        $this->width = [
            ['id_value' => '1', 'name' => $this->l('1/12 of parent width')],
            ['id_value' => '2', 'name' => $this->l('2/12 of parent width')],
            ['id_value' => '3', 'name' => $this->l('3/12 of parent width')],
            ['id_value' => '4', 'name' => $this->l('4/12 of parent width')],
            ['id_value' => '5', 'name' => $this->l('5/12 of parent width')],
            ['id_value' => '6', 'name' => $this->l('6/12 of parent width')],
            ['id_value' => '7', 'name' => $this->l('7/12 of parent width')],
            ['id_value' => '8', 'name' => $this->l('8/12 of parent width')],
            ['id_value' => '9', 'name' => $this->l('9/12 of parent width')],
            ['id_value' => '10', 'name' => $this->l('10/12 of parent width')],
            ['id_value' => '11', 'name' => $this->l('11/12 of parent width')],
            ['id_value' => '12', 'name' => $this->l('12/12 of parent width')],
        ];
        $this->two_block_position = [
            ['id' => '1', 'name' => '1'],
            ['id' => '2', 'name' => '2'],
        ];

        $this->id_theme = false;
        $this->reg_pos = [];
        $this->content_pos = [];
        $this->id_theme = Configuration::get('WK_MP_SELLER_LOGIN_THEME');

        if ($subBlockReg = WkMpLoginParentBlock::getNoOfSubBlocks('registration', $this->id_theme)) {
            for ($i = 1; $i <= $subBlockReg; ++$i) {
                $this->reg_pos[$i] = ['id' => $i, 'name' => $i];
            }
        }

        if ($subBlockCont = WkMpLoginParentBlock::getNoOfSubBlocks('content', $this->id_theme)) {
            for ($i = 1; $i <= $subBlockCont; ++$i) {
                $this->content_pos[$i] = ['id' => $i, 'name' => $i];
            }
        }
    }

    public function initContent()
    {
        $this->initToolbar();
        $this->initPageHeaderToolbar();

        $this->renderList();

        $this->content = $this->renderForm();
        $this->context->smarty->assign([
            'content' => $this->content,
            'url_post' => self::$currentIndex . '&token=' . $this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn,
        ]);
    }

    public function renderList()
    {
        Hook::exec('actionSellerWiseLoginRenderList');

        return parent::renderList();
    }

    public function renderForm()
    {
        if (Shop::getContext() != Shop::CONTEXT_SHOP) {
            return $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/_partials/shop_warning.tpl'
            );
        }

        $idShop = $this->context->shop->id;
        $smartyArr = [];
        $smartyArr = [
            'width' => $this->width,
            'reg_pos' => $this->reg_pos,
            'content_pos' => $this->content_pos,
            'two_block_position' => $this->two_block_position,
            'active_theme' => WkMpSeller::getSellerLoginAllThemes()[Configuration::get('WK_MP_SELLER_LOGIN_THEME')],
        ];

        if ($this->id_theme) {
            $objLoginConf = new WkMpLoginConfigration();
            if ($themeConf = $objLoginConf->getShopThemeConfigration($idShop, $this->id_theme)) {
                $themeConfLangArr = $objLoginConf->getShopThemeConfigrationLangInfo($themeConf['id_wk_mp_seller_login_configration']);
                if ($themeConfLangArr) {
                    foreach ($themeConfLangArr as $themeConfLang) {
                        $themeConf['meta_title'][$themeConfLang['id_lang']] = $themeConfLang['meta_title'];
                        $themeConf['meta_description'][$themeConfLang['id_lang']] = $themeConfLang['meta_description'];
                    }
                }
                $smartyArr['themeConfig'] = $themeConf;
            }

            $wkLogoDir = _PS_MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/';
            $wkLogoDirShop = _PS_MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/' . $this->context->shop->id . '/';
            $imgSrc = glob($wkLogoDir . 'logo.*');
            $imgSrcShop = glob($wkLogoDirShop . 'logo.*');
            if ($imgSrcShop && file_exists($imgSrcShop[0])) {
                $ext = pathinfo($imgSrcShop[0], PATHINFO_EXTENSION);

                $wkLogoUrl = _MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/' . $this->context->shop->id . '/logo.' . $ext;
                $smartyArr['wk_logo_url'] = $wkLogoUrl;
            } elseif ($imgSrc && file_exists($imgSrc[0])) {
                $ext = pathinfo($imgSrc[0], PATHINFO_EXTENSION);

                $wkLogoUrl = _MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/logo.' . $ext;
                $smartyArr['wk_logo_url'] = $wkLogoUrl;
            }

            $contentPosition = WkMpLoginParentBlock::getParentBlockPosition('content', $this->id_theme);
            $smartyArr['contentPosition'] = $contentPosition;

            $contentPBlockActive = WkMpLoginParentBlock::isParentBlockActive('content', $this->id_theme);
            $smartyArr['contentPBlockActive'] = $contentPBlockActive;

            $objBlockPosition = new WkMpLoginBlockPosition();
            $blockFeatureDetail = $objBlockPosition->getBlockPositionDetailByBlockName($idShop, 'feature', $this->id_theme);

            $objBlockContent = new WkMpLoginContent();
            if ($blockFeatureDetail) {
                $smartyArr['blockFeatureDetail'] = $blockFeatureDetail;
                $blockContent = $objBlockContent->getBlockContent(
                    $blockFeatureDetail['id_wk_mp_seller_login_block_position'],
                    $this->id_theme
                );
                if ($blockContent) {
                    $blockContentLang = $objBlockContent->getBlockLangContentById(
                        $blockContent['id_wk_mp_seller_login_content']
                    );
                    $blockLangContent = [];
                    foreach ($blockContentLang as $content) {
                        $blockLangContent['content'][$content['id_lang']] = $content['content'];
                    }
                    $smartyArr['blockLangContent'] = $blockLangContent;
                }
            }

            $regBannerPosition = WkMpLoginParentBlock::getParentBlockPosition('registration', $this->id_theme);
            $smartyArr['regBannerPosition'] = $regBannerPosition;

            $bannerImgUrl = _PS_MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/banner_img/' . $this->id_theme . '.jpg';
            $bannerImgUrlShop = _PS_MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/banner_img/' . $this->context->shop->id . '/'
            . $this->id_theme . '.jpg';
            if (file_exists($bannerImgUrlShop)) {
                $bannerImgUrlShop = _MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/banner_img/' . $this->context->shop->id . '/'
                . $this->id_theme . '.jpg';
                $smartyArr['bannerImgUrl'] = $bannerImgUrlShop;
            } elseif (file_exists($bannerImgUrl)) {
                $bannerImgUrl = _MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/banner_img/' . $this->id_theme . '.jpg';
                $smartyArr['bannerImgUrl'] = $bannerImgUrl;
            }

            $regPBlockActive = WkMpLoginParentBlock::isParentBlockActive('registration', $this->id_theme);
            $smartyArr['regPBlockActive'] = $regPBlockActive;

            $regBlockTitleDetails = $objBlockPosition->getBlockPositionDetailByBlockName(
                $idShop,
                'reg_title',
                $this->id_theme
            );
            if ($regBlockTitleDetails) {
                $regTitle = $objBlockContent->getBlockContent(
                    $regBlockTitleDetails['id_wk_mp_seller_login_block_position'],
                    $this->id_theme
                );
                if ($regTitle) {
                    $regTitleLineLang = $objBlockContent->getBlockLangContentById(
                        $regTitle['id_wk_mp_seller_login_content']
                    );
                    if ($regTitleLineLang) {
                        $regTitleLine = [];
                        foreach ($regTitleLineLang as $regTitle) {
                            $regTitleLine['content'][$regTitle['id_lang']] = $regTitle['content'];
                        }
                        $smartyArr['regTitleLine'] = $regTitleLine;
                    }
                }
                $smartyArr['regBlockTitleDetails'] = $regBlockTitleDetails;
            }

            $regBlockDetails = $objBlockPosition->getBlockPositionDetailByBlockName(
                $idShop,
                'reg_block',
                $this->id_theme
            );
            if ($regBlockDetails) {
                $smartyArr['regBlockDetails'] = $regBlockDetails;
            }

            $termsConditionDetails = $objBlockPosition->getBlockPositionDetailByBlockName(
                $idShop,
                'termscondition',
                $this->id_theme
            );
            if ($termsConditionDetails) {
                $tcBlock = $objBlockContent->getBlockContent(
                    $termsConditionDetails['id_wk_mp_seller_login_block_position'],
                    $this->id_theme
                );
                if ($tcBlock) {
                    $tcBlockContentLang = $objBlockContent->getBlockLangContentById($tcBlock['id_wk_mp_seller_login_content']);
                    if ($tcBlockContentLang) {
                        $tcBlockContent = [];
                        foreach ($tcBlockContentLang as $regTitle) {
                            $tcBlockContent['content'][$regTitle['id_lang']] = $regTitle['content'];
                        }
                        $smartyArr['tcBlockContent'] = $tcBlockContent;
                    }
                    $smartyArr['termsConditionDetails'] = $termsConditionDetails;
                }
            }
        }

        $smartyArr['tinymce'] = true;
        $iso = $this->context->language->iso_code;
        $smartyArr['iso'] = file_exists(_PS_CORE_DIR_ . '/js/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en';
        $smartyArr['path_css'] = _THEME_CSS_DIR_;
        $smartyArr['ad'] = __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_);
        $smartyArr['languages'] = Language::getLanguages();
        $smartyArr['total_languages'] = count(Language::getLanguages());
        $smartyArr['current_lang'] = Language::getLanguage((int) Configuration::get('PS_LANG_DEFAULT'));
        $smartyArr['multi_lang'] = Configuration::get('MP_MULTILANG_ADMIN_APPROVE');
        $smartyArr['multi_def_lang_off'] = Configuration::get('MP_MULTILANG_DEFAULT_LANG');
        $this->context->smarty->assign($smartyArr);

        $this->multiple_fieldsets = true;

        $this->fields_form = [
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        return parent::renderForm();
    }

    public function postProcess()
    {
        $sourceIndex = _PS_MODULE_DIR_ . 'marketplace/index.php';
        $objParentBlock = new WkMpLoginParentBlock();

        // Theme settings
        if (Tools::isSubmit('submit_1')) {
            $headerBgColor = Tools::getValue('header_bg_color');
            $bodyBgColor = Tools::getValue('body_bg_color');

            if (!$headerBgColor) {
                $this->errors[] = $this->l('Header background color value is required.');
            } elseif (!preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $headerBgColor)) {
                $this->errors[] = $this->l('Header background color value is invalid.');
            } else {
                if (!Validate::isColor($headerBgColor)) {
                    $this->errors[] = $this->l('Header background color value is not valid.');
                }
            }
            if (!$bodyBgColor) {
                $this->errors[] = $this->l('Body background color value is required.');
            } elseif (!preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $bodyBgColor)) {
                $this->errors[] = $this->l('Body background color value is invalid.');
            } else {
                if (!Validate::isColor($bodyBgColor)) {
                    $this->errors[] = $this->l('Body background color value is not valid.');
                }
            }

            if ($_FILES['wk_logo']['size']) {
                $imginfo = getimagesize($_FILES['wk_logo']['tmp_name']);

                if (!ImageManager::isRealImage($_FILES['wk_logo']['tmp_name'], $_FILES['wk_logo']['type'])
                || !ImageManager::isCorrectImageFileExt($_FILES['wk_logo']['name'])
                || preg_match('/\%00/', $_FILES['wk_logo']['name'])
                ) {
                    $this->errors[] = $this->l('Image format not recognized, allowed formats are: .gif, .jpg, .png, .webp');
                }
            }

            if (empty($this->errors)) {
                $objLoginConf = new WkMpLoginConfigration();
                $themeConf = $objLoginConf->getShopThemeConfigration($this->context->shop->id, $this->id_theme);
                if ($themeConf) {
                    $objLoginConf = new WkMpLoginConfigration($themeConf['id_wk_mp_seller_login_configration']);
                } else {
                    $objLoginConf->id_theme = $this->id_theme;
                }

                $objLoginConf->header_bg_color = $headerBgColor;
                $objLoginConf->body_bg_color = $bodyBgColor;

                foreach (Language::getLanguages(false) as $language) {
                    $langTitle = Tools::getValue('metaTitle_' . $language['id_lang']);
                    $objLoginConf->meta_title[$language['id_lang']] = $langTitle;
                    $langDesc = Tools::getValue('metaDescription_' . $language['id_lang']);
                    $objLoginConf->meta_description[$language['id_lang']] = $langDesc;
                }
                $objLoginConf->save();

                if ($_FILES['wk_logo']['size']) {
                    // Upload logo
                    $wkLogoDirShop = _PS_MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/' .
                    $this->context->shop->id;

                    if (!file_exists($wkLogoDirShop)) {
                        @mkdir($wkLogoDirShop, 0777, true);
                        @copy($sourceIndex, $wkLogoDirShop . '/index.php');
                    }

                    $imgSrcShop = glob($wkLogoDirShop . '/logo.*');

                    if ($imgSrcShop && file_exists($imgSrcShop[0])) {
                        unlink($imgSrcShop[0]);
                    }
                    $ext = pathinfo($_FILES['wk_logo']['name'], PATHINFO_EXTENSION);
                    $imgSrcShop = $wkLogoDirShop . '/logo.' . $ext;
                    // Logo size must be 130*50
                    ImageManager::resize($_FILES['wk_logo']['tmp_name'], $imgSrcShop, 130, 50, $ext);
                }

                Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
            }
        }

        // Registration block configuration
        if (Tools::isSubmit('submit_2')) {
            if ($_FILES['banner_img']['size']) {
                if (!ImageManager::isRealImage($_FILES['banner_img']['tmp_name'], $_FILES['banner_img']['type'])
                || !ImageManager::isCorrectImageFileExt($_FILES['banner_img']['name'])
                || preg_match('/\%00/', $_FILES['banner_img']['name'])) {
                    $this->errors[] = $this->l('Image format not recognized, allowed formats are: .gif, .jpg, .png, .webp');
                } else {
                    $imgSrcShop = _PS_MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/banner_img/'
                    . $this->context->shop->id;
                    if (!file_exists($imgSrcShop)) {
                        @mkdir($imgSrcShop, 0777, true);
                        @copy($sourceIndex, $imgSrcShop . '/index.php');
                    }
                    $imgSrc = _PS_MODULE_DIR_ . $this->module->name . '/views/img/mpsellerlogin/banner_img/' . $this->context->shop->id . '/'
                    . $this->id_theme . '.jpg';
                    if (file_exists($imgSrc)) {
                        unlink($imgSrc);
                    }
                    ImageManager::resize($_FILES['banner_img']['tmp_name'], $imgSrc, 0, 0, 'jpg');
                }
            }

            if (Tools::getValue('regPBlockActive')) {
                if (!Tools::getValue('regTitleTextColor')) {
                    $this->errors[] = $this->l('Title text color value is required.');
                } elseif (!preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', Tools::getValue('regTitleTextColor'))) {
                    $this->errors[] = $this->l('Title text color value is invalid.');
                } else {
                    if (!Validate::isColor(Tools::getValue('regTitleTextColor'))) {
                        $this->errors[] = $this->l('Title text color value is not valid.');
                    }
                }
                if (!Tools::getValue('regBgColor')) {
                    $this->errors[] = $this->l('Registration block background color value is required.');
                } elseif (!preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', Tools::getValue('regBgColor'))) {
                    $this->errors[] = $this->l('Registration block background color value is invalid.');
                } else {
                    if (!Validate::isColor(Tools::getValue('regBgColor'))) {
                        $this->errors[] = $this->l('Registration block background color value is not valid.');
                    }
                }
                if (!Tools::getValue('regBlockTextColor')) {
                    $this->errors[] = $this->l('Registration block text color value is required.');
                } elseif (!preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', Tools::getValue('regBlockTextColor'))) {
                    $this->errors[] = $this->l('Registration block text color value is invalid.');
                } else {
                    if (!Validate::isColor(Tools::getValue('regBlockTextColor'))) {
                        $this->errors[] = $this->l('Registration block text color value is not valid.');
                    }
                }
            }

            if (empty($this->errors)) {
                $parentBlockName = 'registration';
                $this->saveParentBlockSetting($parentBlockName, 'regBannerPosition', 'regPBlockActive');

                $parentBlock = $objParentBlock->getBlockIdByThemeId($parentBlockName, $this->id_theme);
                if ($parentBlock && $parentBlock['id_wk_mp_seller_login_parent_block']) {
                    $blockPositionId = $this->saveBlockPositionDetails(
                        'reg_title',
                        $parentBlock['id_wk_mp_seller_login_parent_block'],
                        'regTitleBlockPos',
                        'regTitleBlockWidth',
                        'regBgColor',
                        'regTitleTextColor',
                        'regTitleBlockActive'
                    );
                    $this->saveBlockContent($blockPositionId, 'regTitleLine_');

                    $this->saveBlockPositionDetails(
                        'reg_block',
                        $parentBlock['id_wk_mp_seller_login_parent_block'],
                        'regBlockPosition',
                        'regBlockWidth',
                        'regBgColor',
                        'regBlockTextColor',
                        'regBlockActive'
                    );
                }

                Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
            }
        }

        // Content block configuration
        if (Tools::isSubmit('submit_3')) {
            $this->saveParentBlockSetting('content', 'contentPosition', 'contentPBlockActive');
            Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
        }

        // Feature block configuration
        if (Tools::isSubmit('submit_4')) {
            if (Tools::getValue('featureBlockActive')) {
                if (!Tools::getValue('featureBgColor')) {
                    $this->errors[] = $this->l('Feature block background color value is required.');
                } elseif (!preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', Tools::getValue('featureBgColor'))) {
                    $this->errors[] = $this->l('Feature block background color value is invalid.');
                } else {
                    if (!Validate::isColor(Tools::getValue('featureBgColor'))) {
                        $this->errors[] = $this->l('Feature block background color value is not valid.');
                    }
                }
                if (!Tools::getValue('featureTextColor')) {
                    $this->errors[] = $this->l('Feature block text color value is required.');
                } elseif (!preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', Tools::getValue('featureTextColor'))) {
                    $this->errors[] = $this->l('Feature block text color value is invalid.');
                } else {
                    if (!Validate::isColor(Tools::getValue('featureTextColor'))) {
                        $this->errors[] = $this->l('Feature block text color value is not valid.');
                    }
                }
            }
            if (empty($this->errors)) {
                $parentBlock = $objParentBlock->getBlockIdByThemeId('content', $this->id_theme);
                $blockPositionId = $this->saveBlockPositionDetails(
                    'feature',
                    $parentBlock['id_wk_mp_seller_login_parent_block'],
                    'featureBlockPosition',
                    'featureBlockWidth',
                    'featureBgColor',
                    'featureTextColor',
                    'featureBlockActive'
                );
                $this->saveBlockContent($blockPositionId, 'featureContent_');
                Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
            }
        }

        // Terms and conditions
        if (Tools::isSubmit('submit_5')) {
            foreach (Language::getLanguages(false) as $language) {
                $cleanHtmlContent = Tools::getDescriptionClean(Tools::getValue('tcBlockContent_' . $language['id_lang']));
                $cleanHtmlContent = str_replace(chr(0xC2) . chr(0xA0), '', $cleanHtmlContent);
                if ((!Validate::isCleanHtml(Tools::getValue('tcBlockContent_' . $language['id_lang']))
                || $this->checkScriptInHtml($cleanHtmlContent))
                && trim($cleanHtmlContent)
                ) {
                    $this->errors[] = $this->l('T&C content is invalid.');
                }
            }
            if (Tools::getValue('tcBlockActive')) {
                if (!Tools::getValue('tcBgColor')) {
                    $this->errors[] = $this->l('Terms & conditions block background color value is required.');
                } elseif (!preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', Tools::getValue('tcBgColor'))) {
                    $this->errors[] = $this->l('Terms & conditions block color value is invalid.');
                } else {
                    if (!Validate::isColor(Tools::getValue('tcBgColor'))) {
                        $this->errors[] = $this->l('Terms & conditions block background color value is not valid.');
                    }
                }
                if (!Tools::getValue('tcTextColor')) {
                    $this->errors[] = $this->l('Terms & conditions block text color value is required.');
                } elseif (!preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', Tools::getValue('tcTextColor'))) {
                    $this->errors[] = $this->l('Terms & conditions block text color value is invalid.');
                } else {
                    if (!Validate::isColor(Tools::getValue('tcTextColor'))) {
                        $this->errors[] = $this->l('Terms & conditions block text color value is not valid.');
                    }
                }
            }
            if (empty($this->errors)) {
                $parentBlock = $objParentBlock->getBlockIdByThemeId('content', $this->id_theme);
                $blockPositionId = $this->saveBlockPositionDetails(
                    'termscondition',
                    $parentBlock['id_wk_mp_seller_login_parent_block'],
                    'tcBlockPosition',
                    'tcBlockWidth',
                    'tcBgColor',
                    'tcTextColor',
                    'tcBlockActive'
                );
                $this->saveBlockContent($blockPositionId, 'tcBlockContent_');

                Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
            }
        }

        Hook::exec('actionBlockDataSave');
        parent::postProcess();
    }

    public function checkScriptInHtml($html)
    {
        if (trim($html)) {
            $dom = new DOMDocument();
            $dom->loadHTML(htmlspecialchars_decode($html));
            $script = $dom->getElementsByTagName('script');

            return $script->length;
        } else {
            return 0;
        }
    }

    public function saveParentBlockSetting($parentBlockName, $parentBlockPosition, $parentBlockActive = 0)
    {
        $objParentBlock = new WkMpLoginParentBlock();
        $pblkDetail = $objParentBlock->getParentBlockDetails($parentBlockName, $this->id_theme);
        if ($pblkDetail) {
            $objParentBlock = new WkMpLoginParentBlock($pblkDetail['id_wk_mp_seller_login_parent_block']);
        }
        $objParentBlock->id_position = Tools::getValue($parentBlockPosition);
        if ($parentBlockActive) {
            $objParentBlock->active = Tools::getValue($parentBlockActive);
        }
        $objParentBlock->save();

        return $objParentBlock->id;
    }

    public function saveBlockPositionDetails(
        $blockName,
        $idParent,
        $blockPosition,
        $blockWidth,
        $blockBgColor,
        $blockTextColor,
        $blockActive = 0
    ) {
        $idShop = $this->context->shop->id;
        $objBlockPosition = new WkMpLoginBlockPosition();
        $blockDetail = $objBlockPosition->getBlockPositionDetailByBlockName($idShop, $blockName, $this->id_theme);
        if ($blockDetail) {
            $objBlockPosition = new WkMpLoginBlockPosition($blockDetail['id_wk_mp_seller_login_block_position']);
        }
        $objBlockPosition->id_parent = $idParent;
        $objBlockPosition->id_position = Tools::getValue($blockPosition);
        $objBlockPosition->block_name = $blockName;
        $objBlockPosition->width = Tools::getValue($blockWidth);
        $objBlockPosition->block_bg_color = Tools::getValue($blockBgColor);
        $objBlockPosition->block_text_color = Tools::getValue($blockTextColor);
        if ($blockActive) {
            $objBlockPosition->active = Tools::getValue($blockActive);
        }
        $objBlockPosition->save();

        return $objBlockPosition->id;
    }

    public function saveBlockContent($blockPositionId, $blockContentName)
    {
        $objBlockContent = new WkMpLoginContent();
        $blockContent = $objBlockContent->getBlockContent($blockPositionId, $this->id_theme);
        if ($blockContent) {
            $objBlockContent = new WkMpLoginContent($blockContent['id_wk_mp_seller_login_content']);
        }
        $objBlockContent->id_block = $blockPositionId;
        foreach (Language::getLanguages(false) as $language) {
            $objBlockContent->content[$language['id_lang']] = Tools::getValue($blockContentName . $language['id_lang']);
        }
        $objBlockContent->save();

        return $objBlockContent->id;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJqueryPlugin('tagify');
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/mpsellerlogin/admin_sellerlogin.js');
        $this->addJS(_PS_MODULE_DIR_ . $this->module->name . '/views/js/mpsellerlogin/jquery.colorpicker.js');
        $this->addJS(_PS_JS_DIR_ . 'tiny_mce/tiny_mce.js');
        if (version_compare(_PS_VERSION_, '1.6.0.11', '>')) {
            $this->addJS(_PS_JS_DIR_ . 'admin/tinymce.inc.js');
        } else {
            $this->addJS(_PS_JS_DIR_ . 'tinymce.inc.js');
        }
    }
}
