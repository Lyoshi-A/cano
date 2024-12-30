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
require_once 'classes/sellerlisthelper.php';
if (Module::isInstalled('marketplace') && Module::isEnabled('marketplace')) {
    include_once dirname(__FILE__) . '/../marketplace/classes/WkMpRequiredClasses.php';
}
class MpSellerList extends Module
{
    private $html = '';

    public function __construct()
    {
        $this->name = 'mpsellerlist';
        $this->tab = 'front_office_features';
        $this->version = '5.1.2';
        $this->author = 'Webkul';
        $this->need_instance = 1;
        $this->bootstrap = true;
        $this->dependencies = ['marketplace'];
        $this->controllers = [
            'sellerlist',
            'viewmorelist',
            'viewmoreproduct',
            'moreproduct',
            'ajaxsellersearch',
        ];
        parent::__construct();
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => _PS_VERSION_];
        $this->displayName = $this->l('Marketplace Seller List');
        $this->description = $this->l('Listing seller in your shop related to marketplace.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module.');
    }

    private function postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $message = [];
            foreach (Language::getLanguages(false) as $language) {
                if (!Validate::isCleanHtml(Tools::getValue('MP_SELLER_TEXT_' . $language['id_lang']))) {
                    $this->context->controller->errors[] = sprintf(
                        $this->l('Message on seller list page must be valid in %s (%s).'),
                        $language['name'],
                        $language['language_code']
                    );
                }
                $message[$language['id_lang']] =
                htmlentities(Tools::getValue('MP_SELLER_TEXT_' . $language['id_lang']), ENT_QUOTES);
            }
            if (empty($this->context->controller->errors)) {
                Configuration::updateValue('MP_SELLER_TEXT', $message);
                $moduleConfig = $this->context->link->getAdminLink('AdminModules');
                Tools::redirectAdmin(
                    $moduleConfig . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&conf=4'
                );
            }
        }
    }

    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $this->postProcess();
        } else {
            $this->html .= '<br />';
        }
        $this->html .= $this->renderForm();
        // Cross Selling Banner
        Media::addJsDef([
            'wkModuleAddonKey' => $this->module_key,
            'wkModuleAddonsId' => '',
            'wkModuleTechName' => $this->name,
            'wkModuleDoc' => file_exists(_PS_MODULE_DIR_ . $this->name . '/docs/doc_en.pdf'),
        ]);
        $this->context->controller->addJs('https://prestashop.webkul.com/crossselling/wkcrossselling.min.js?t=' . time());

        return $this->html;
    }

    public function renderForm()
    {
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');

        $fieldsForm = [];
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Configuration'),
                'icon' => 'icon-cogs',
            ],
            'input' => [
                [
                    'type' => 'textarea',
                    'label' => $this->l('Message on seller list page'),
                    'name' => 'MP_SELLER_TEXT',
                    'lang' => true,
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->submit_action = 'btnSubmit';
        $helper->table = $this->table;
        $helper->identifier = $this->identifier;
        // Language
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        // $this->fields_form = array();
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm($fieldsForm);
    }

    public function getConfigFieldsValues()
    {
        $configVars = [];
        foreach ($this->context->controller->getLanguages() as $language) {
            $configVars['MP_SELLER_TEXT'][$language['id_lang']] =
            Tools::getValue(
                'MP_SELLER_TEXT_' . $language['id_lang'],
                html_entity_decode(
                    Configuration::get('MP_SELLER_TEXT', $language['id_lang']),
                    ENT_QUOTES
                )
            );
        }

        return $configVars;
    }

    public function hookTop()
    {
        if (Module::isEnabled('marketplace')) {
            if (_PS_VERSION_ >= '1.7.8.0') {
                $newVersion = 1;
            } else {
                $newVersion = 0;
            }
            $this->context->smarty->assign(
                [
                    'seller_listlink' => $this->context->link->getModuleLink('mpsellerlist', 'sellerlist'),
                    'is_new_version' => $newVersion,
                ]
            );

            return $this->display(__FILE__, 'mplink.tpl');
        }
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'module-mpsellerlist-mpheader-css',
            'modules/' . $this->name . '/views/css/hook_style.css',
            ['position' => 'bottom', 'priority' => 999]
        );
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        if (!parent::install()
            || !$this->registerHook('top')
            || !$this->registerHook('actionFrontControllerSetMedia')
            || !$this->installConfiguration()
        ) {
            return false;
        }

        return true;
    }

    private function installConfiguration()
    {
        $mpSellerText = "There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.";

        $message = [];
        foreach (Language::getLanguages(false) as $language) {
            $message[$language['id_lang']] =
                htmlentities($mpSellerText, ENT_QUOTES);
        }

        if (!Configuration::updateValue('MP_SELLER_TEXT', $message)) {
            return false;
        }

        return true;
    }
}
