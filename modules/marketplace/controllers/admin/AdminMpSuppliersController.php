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

class AdminMpSuppliersController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'wk_mp_suppliers';
        $this->className = 'WkMpSuppliers';
        $this->bootstrap = true;
        $this->context = Context::getContext();
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'wk_mp_seller` mpsi ON (mpsi.`id_seller` = a.`id_seller`)';
        $this->_join .= 'LEFT JOIN `' . _DB_PREFIX_ . 'supplier` s ON (a.`id_ps_supplier` = s.id_supplier)' .
        Shop::addSqlAssociation('supplier', 's');
        $this->_select = 'CONCAT(mpsi.`seller_firstname`, \' \', mpsi.`seller_lastname`) as `seller_name`,
        mpsi.`shop_name_unique`, a.`id_wk_mp_supplier` as `no_of_products`, s.`name`,  s.`active`,
        a.`id_wk_mp_supplier` as `id_seller_supplier`';
        $this->identifier = 'id_wk_mp_supplier';
        parent::__construct();
        $this->toolbar_title = $this->l('Suppliers');
        if (Shop::isFeatureActive() && Shop::getContext() !== Shop::CONTEXT_SHOP) {
            // In case of All Shops
            $this->_select .= ',shp.`name` as wk_ps_shop_name';
            $this->_join .= 'JOIN `' . _DB_PREFIX_ . 'shop` shp ON (shp.`id_shop` = supplier_shop.`id_shop`)';
        }

        $this->fields_list = [];
        $this->fields_list['id_wk_mp_supplier'] = [
            'title' => $this->l('ID'),
            'align' => 'center',
        ];

        $this->fields_list['id_ps_supplier'] = [
            'title' => $this->l('Prestashop supplier ID'),
            'align' => 'center',
            'callback' => 'prestashopSuppId',
            'havingFilter' => true,
        ];

        $this->fields_list['id_seller_supplier'] = [
            'title' => $this->l('Logo'),
            'callback' => 'displaySupplierImage',
            'search' => false,
            'havingFilter' => true,
        ];

        $this->fields_list['name'] = [
            'title' => $this->l('Supplier name'),
            'align' => 'center',
            'havingFilter' => true,
            'maxlength' => '64',
        ];

        $this->fields_list['no_of_products'] = [
            'title' => $this->l('Products'),
            'align' => 'center',
            'callback' => 'getNoOfProducts',
            'search' => false,
        ];

        $this->fields_list['seller_name'] = [
            'title' => $this->l('Seller name'),
            'havingFilter' => true,
        ];

        $this->fields_list['shop_name_unique'] = [
            'title' => $this->l('Unique shop name'),
            'havingFilter' => true,
        ];

        $this->fields_list['active'] = [
            'title' => $this->l('Status'),
            'align' => 'center',
            'active' => 'status',
            'type' => 'bool',
            'orderby' => false,
            'filter_key' => 's!active',
        ];
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE')) {
            if (Shop::isFeatureActive() && Shop::getContext() !== Shop::CONTEXT_SHOP) {
                // In case of All Shops
                $this->fields_list['wk_ps_shop_name'] = [
                    'title' => $this->l('Shop'),
                    'havingFilter' => true,
                    'orderby' => false,
                ];
            }
        }

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?'),
            ],
        ];
    }

    public function displaySupplierImage($idMpSupplier, $rowData)
    {
        $imageLink = _MODULE_DIR_ . 'marketplace/views/img/mpsuppliers/default_supplier.png';
        if ($idMpSupplier) {
            $supplierImg = 'marketplace/views/img/mpsuppliers/' . $idMpSupplier . '.jpg';
            if (file_exists(_PS_MODULE_DIR_ . $supplierImg)) {
                $imageLink = _MODULE_DIR_ . $supplierImg;
            }
        }

        $this->context->smarty->assign([
            'callback' => 'displaySupplierImage',
            'image_link' => $imageLink,
        ]);

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/wk_admin_callback.tpl');
    }

    public function initToolbar()
    {
        $allCustomers = WkMpSeller::getAllSeller();
        if ($allCustomers) {
            parent::initToolbar();
            if (!Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') || Shop::getContext() == Shop::CONTEXT_SHOP) {
                $this->page_header_toolbar_btn['new'] = [
                    'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                    'desc' => $this->l('Add new supplier'),
                ];
            }
        }

        unset($allCustomers);
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('view');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function getNoOfProducts($value)
    {
        $no_products = WkMpSuppliers::getNoOfProductsByMpSupplierId($value);
        if ($no_products) {
            return $no_products;
        } else {
            return 0;
        }
    }

    public function prestashopSuppId($value)
    {
        if ($value == 0) {
            return '-';
        } else {
            return $value;
        }
    }

    public function postProcess()
    {
        if (Tools::isSubmit('status' . $this->table)) {
            $mpSupplierId = Tools::getValue('id_wk_mp_supplier');
            if ($mpSupplierId) {
                $status = 0;
                $objMpSupplier = new WkMpSuppliers();
                if ($supplierInfo = $objMpSupplier->getMpSupplierAllDetails($mpSupplierId)) {
                    $suppStatus = $supplierInfo['active'];
                    if (!$suppStatus) {
                        $status = 1;
                    }
                }
                WkMpSuppliers::sendSupplierMailToSeller($mpSupplierId, $status);

                Tools::redirectAdmin(self::$currentIndex . '&conf=5&token=' . $this->token);
            }

            if (!Tools::isSubmit('submitAdd' . $this->table . 'AndAssignStay')) {
                Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
            }
        } elseif (Tools::isSubmit('delete' . $this->table)) {
            WkMpSuppliers::deleteSupplier(Tools::getValue('id_wk_mp_supplier'));
            Tools::redirectAdmin(self::$currentIndex . '&conf=2&token=' . $this->token);
        }

        parent::postProcess();
    }

    protected function processBulkDelete()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                WkMpSuppliers::deleteSupplier($id);
            }
        }

        if (is_array($this->boxes) && !empty($this->boxes)) {
            Tools::redirectAdmin(self::$currentIndex . '&conf=2&token=' . $this->token);
        }
        parent::processBulkDelete();
    }

    protected function processBulkEnableSelection()
    {
        return $this->processBulkStatusSelection(1);
    }

    protected function processBulkDisableSelection()
    {
        return $this->processBulkStatusSelection(0);
    }

    protected function processBulkStatusSelection($active)
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $mpSupplierId) {
                WkMpSuppliers::sendSupplierMailToSeller($mpSupplierId, $active);
            }

            Tools::redirectAdmin(self::$currentIndex . '&conf=5&token=' . $this->token);
        }

        if (is_array($this->boxes) && !empty($this->boxes)) {
            Tools::redirectAdmin(self::$currentIndex . '&conf=5&token=' . $this->token);
        } else {
            $this->errors[] = $this->l('You must select at least one item to perform a bulk action.');
        }
    }

    public function processSave()
    {
        $mp_supplier_id = Tools::getValue('id_wk_mp_supplier'); // if edit
        $suppname = Tools::getValue('suppname');
        $suppphone = Tools::getValue('suppphone');
        $suppmobile = Tools::getValue('suppmobile');
        $suppaddress = Tools::getValue('suppaddress');
        $suppzip = Tools::getValue('suppzip');
        $suppcity = Tools::getValue('suppcity');
        $suppcountry = Tools::getValue('suppcountry');
        $suppstate = Tools::getValue('suppstate');
        $selected_products = Tools::getValue('selected_products');
        $default_lang = Tools::getValue('choosedLangId');
        $customerId = Tools::getValue('mp_customer_id');

        // data validation
        if ($suppname == '') {
            $this->errors[] = $this->l('Supplier name is required.');
        } elseif (!Validate::isGenericName($suppname)) {
            $this->errors[] = $this->l('Supplier name is not valid.');
        }

        // Check fields sizes
        $className = 'WkMpSuppliers';
        $rules = call_user_func([$className, 'getValidationRules'], $className);
        foreach (Language::getLanguages() as $language) {
            $languageName = '';
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $languageName = '(' . $language['name'] . ')';
            }

            if (Tools::getValue('description_' . $language['id_lang'])) {
                if (!Validate::isCleanHtml(
                    Tools::getValue('description_' . $language['id_lang']),
                    (int) Configuration::get('PS_ALLOW_HTML_IFRAME')
                )) {
                    $this->errors[] = sprintf(
                        $this->l('Product description field %s is invalid'),
                        $languageName
                    );
                }
            }
            if (Tools::getValue('meta_title_' . $language['id_lang'])) {
                if (!Validate::isGenericName(Tools::getValue('meta_title_' . $language['id_lang']))) {
                    $this->errors[] = sprintf(
                        $this->l('Meta title field %s is invalid'),
                        $languageName
                    );
                } elseif (Tools::strlen(Tools::getValue('meta_title_' . $language['id_lang'])) > 128) {
                    $this->errors[] = sprintf(
                        $this->l('Meta title field is too long (%2$d chars max).'),
                        128
                    );
                }
            }
            if (Tools::getValue('meta_desc_' . $language['id_lang'])) {
                if (!Validate::isGenericName(Tools::getValue('meta_desc_' . $language['id_lang']))) {
                    $this->errors[] = sprintf(
                        $this->l('Meta description field %s is invalid'),
                        $languageName
                    );
                } elseif (Tools::strlen(Tools::getValue('meta_desc_' . $language['id_lang'])) > 255) {
                    $this->errors[] = sprintf(
                        $this->l('Meta description field is too long (%2$d chars max).'),
                        call_user_func([$className, 'displayFieldName'], $className),
                        255
                    );
                }
            }
            if (Tools::getValue('meta_key_' . $language['id_lang'])) {
                if (!Validate::isGenericName(Tools::getValue('meta_key_' . $language['id_lang']))) {
                    $this->errors[] = sprintf(
                        $this->l('Meta key field %s is invalid'),
                        $languageName
                    );
                }
            }
        }

        if (!Validate::isPhoneNumber($suppphone)) {
            $this->errors[] = $this->l('Phone number is invalid.');
        }
        if (!Validate::isPhoneNumber($suppmobile)) {
            $this->errors[] = $this->l('Mobile phone number is invalid.');
        }

        if (trim($suppaddress) == '') {
            $this->errors[] = $this->l('Address is required.');
        } elseif (!Validate::isAddress($suppaddress)) {
            $this->errors[] = $this->l('Invalid address.');
        }

        if ($suppzip) {
            if (!Validate::isPostCode($suppzip)) {
                $this->errors[] = $this->l('Invaid zip/postal Code.');
            }
        }

        if ($suppcity == '') {
            $this->errors[] = $this->l('City is required.');
        } elseif (!Validate::isCityName($suppcity)) {
            $this->errors[] = $this->l('City name is invalid.');
        }

        if (!$suppcountry) {
            $this->errors[] = $this->l('Country is required field.');
        } elseif (Address::dniRequired($suppcountry)) {
            if (Tools::getValue('dni') == '') {
                $this->errors[] = $this->l('DNI is required');
            } elseif (!Validate::isDniLite('dni')) {
                $this->errors[] = $this->l('Invalid DNI');
            } else {
                $addressDNI = Tools::getValue('dni');
            }
        }

        if (!empty($_FILES['supplier_logo']['name'])
        && $_FILES['supplier_logo']['size'] > 0
        && $_FILES['supplier_logo']['tmp_name'] != '') {
            if ($errorMsg = ImageManager::validateUpload($_FILES['supplier_logo'])) {
                $this->errors[] = $errorMsg;
            }
        }

        if ($customerId) {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId($customerId);
            if (empty($mpSeller)) {
                $this->errors[] = $this->l('Seller is not exist.');
            } else {
                $sellerid = $mpSeller['id_seller'];
            }
        }

        if (empty($this->errors)) {
            $mpIdSupplier = Tools::getValue('id');
            if ($mpIdSupplier) {
                $mpSupplierInfo = WkMpSuppliers::getMpSupplierAllDetails($mpIdSupplier);
                $psIdSupplier = $mpSupplierInfo['id_ps_supplier'];
                $sellerid = $mpSupplierInfo['id_seller'];
                $psIdSupplierAddress = $mpSupplierInfo['id_ps_supplier_address'];
                $objmpsupp = new WkMpSuppliers($mpIdSupplier); // edit supplier
                $objpssupp = new Supplier($psIdSupplier);
            } else {
                $objmpsupp = new WkMpSuppliers(); // add manufacturer
                $objpssupp = new Supplier();
                if (Configuration::get('WK_MP_PRODUCT_SUPPLIER_APPROVED')) {
                    $objpssupp->active = 0; // need to approved by admin
                } else {
                    $objpssupp->active = 1; // automatically approved
                }
                $psIdSupplier = $psIdSupplierAddress = 0;
            }
            $objpssupp->name = $suppname;
            foreach (Language::getLanguages(true) as $language) {
                if (Tools::getValue('description_' . $language['id_lang'])) {
                    $objpssupp->description[$language['id_lang']] = Tools::getValue(
                        'description_' . $language['id_lang']
                    );
                } else {
                    $objpssupp->description[$language['id_lang']] = Tools::getValue(
                        'description_' . $default_lang
                    );
                }

                if (Tools::getValue('meta_title_' . $language['id_lang'])) {
                    $objpssupp->meta_title[$language['id_lang']] = Tools::getValue(
                        'meta_title_' . $language['id_lang']
                    );
                } else {
                    $objpssupp->meta_title[$language['id_lang']] = Tools::getValue(
                        'meta_title_' . $default_lang
                    );
                }

                if (Tools::getValue('meta_desc_' . $language['id_lang'])) {
                    $objpssupp->meta_description[$language['id_lang']] = Tools::getValue(
                        'meta_desc_' . $language['id_lang']
                    );
                } else {
                    $objpssupp->meta_description[$language['id_lang']] = Tools::getValue(
                        'meta_desc_' . $default_lang
                    );
                }

                if (Tools::getValue('meta_key_' . $language['id_lang'])) {
                    $objpssupp->meta_keywords[$language['id_lang']] = Tools::getValue(
                        'meta_key_' . $language['id_lang']
                    );
                } else {
                    $objpssupp->meta_keywords[$language['id_lang']] = Tools::getValue(
                        'meta_key_' . $default_lang
                    );
                }
            }
            $objpssupp->save();
            if (!$psIdSupplier) {
                $psIdSupplier = $objpssupp->id;
            }
            // ***start*** save to mp supplier
            $objmpsupp->id_seller = (int) $sellerid;
            $objmpsupp->id_ps_supplier = (int) $psIdSupplier;
            $objmpsupp->id_ps_supplier_address = (int) $psIdSupplierAddress;
            $objmpsupp->save();

            if (!$mpIdSupplier) {
                $mpIdSupplier = $objmpsupp->id;
            }
            // Upload Supplier Logo
            if ($mpIdSupplier) {
                if (!empty($_FILES['supplier_logo'])) {
                    $logo = $_FILES['supplier_logo'];
                    if ($logo['size'] > 0) {
                        $logoName = $mpIdSupplier . '.jpg';
                        $mpImageDir = _PS_MODULE_DIR_ . 'marketplace/views/img/mpsuppliers/';
                        $uploaded = ImageManager::resize($logo['tmp_name'], $mpImageDir . $logoName, 45, 45);
                        if ($uploaded) {
                            $objmpsupp->uploadSupplierLogoToPs($psIdSupplier, $mpIdSupplier, $mpImageDir);
                        }
                    }
                }

                if ($psIdSupplierAddress) {
                    $address = new Address($psIdSupplierAddress);
                } else {
                    $address = new Address();
                }
                $address->alias = pSQL('supplier');
                $address->lastname = pSQL('supplier');
                $address->firstname = pSQL('supplier');
                $address->address1 = pSQL($suppaddress);
                if (isset($addressDNI)) {
                    $address->dni = pSQL($addressDNI);
                }
                $address->postcode = pSQL($suppzip);
                $address->phone = pSQL($suppphone);
                $address->phone_mobile = pSQL($suppmobile);
                $address->id_country = (int) $suppcountry;
                $address->id_state = (int) $suppstate;
                $address->city = pSQL($suppcity);
                $address->id_supplier = (int) $psIdSupplier;
                $address->save();
                $psIdSupplierAddress = $address->id;
                if ($psIdSupplierAddress) {
                    // Update supplier table with psIdManuf
                    $objmpsupp->updateMpSupplierDetails($psIdSupplier, $mpIdSupplier, $psIdSupplierAddress);
                }

                if ($selected_products = Tools::getValue('selected_products')) {
                    WkMpSuppliers::updateSupplierProducts(
                        $mpIdSupplier,
                        $psIdSupplier,
                        $selected_products
                    );
                }
            }
            if (Tools::isSubmit('submitAddwk_mp_suppliersAndStay')) {
                Tools::redirectAdmin(
                    self::$currentIndex . '&updatewk_mp_suppliers=&id_wk_mp_supplier=' . (int) $objmpsupp->id .
                    '&conf=4&token=' . $this->token
                );
            } else {
                Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
            }
        } else {
            if ($mp_supplier_id) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
    }

    public function renderView()
    {
        $idLang = $this->context->language->id;
        $mp_supplier_id = Tools::getValue('id_wk_mp_supplier');
        if ($mp_supplier_id) {
            $objMpSupplier = new WkMpSuppliers();
            $supplierInfo = $objMpSupplier->getMpSupplierAllDetails($mp_supplier_id);
            $product_list = WkMpSuppliers::getProductListByMpSupplierIdAndIdSeller(
                $mp_supplier_id,
                $supplierInfo['id_seller'],
                $idLang
            );
            if ($product_list) {
                $this->context->smarty->assign('product_list', $product_list);
            }
            $this->context->smarty->assign('supplierInfo', $supplierInfo);
        } else {
            Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
        }

        return parent::renderView();
    }

    public function renderForm()
    {
        if ((($this->display == 'edit') && (Shop::getContext() != Shop::CONTEXT_SHOP))
            || ($this->display == 'add' && (Shop::getContext() != Shop::CONTEXT_SHOP))) {
            $shopWarning = $this->l('You can not add/edit in this shop context.');
            $shopWarning .= $this->l(' Select a shop instead of a group of shops.');
            $this->warnings[] = $shopWarning;

            return;
        }

        $this->context->smarty->assign('is_front_controller', 0);
        if ($this->display == 'add') {
            $all_active_sellers = WkMpSeller::getAllSeller();
            if ($all_active_sellers) {
                $this->context->smarty->assign('seller_list', $all_active_sellers);

                // get first seller from the list
                $first_seller_details = $all_active_sellers[0];
                $mp_id_seller = $first_seller_details['id_seller'];
            } else {
                $mp_id_seller = 0;
            }
        } elseif ($this->display == 'edit') {
            $mp_supplier_id = Tools::getValue('id_wk_mp_supplier');
            if ($mp_supplier_id) {
                $objMpSupplier = new WkMpSuppliers($mp_supplier_id);

                // Delete logo
                if (Tools::getValue('delete_logo')) {
                    // Delete from MP
                    if (file_exists(_PS_MODULE_DIR_ . 'marketplace/views/img/mpsuppliers/' . $mp_supplier_id . '.jpg')) {
                        unlink(_PS_MODULE_DIR_ . 'marketplace/views/img/mpsuppliers/' . $mp_supplier_id . '.jpg');
                    }
                    // Delete from PS
                    if (file_exists(_PS_IMG_DIR_ . 'su/' . $objMpSupplier->id_ps_supplier . '.jpg')) {
                        unlink(_PS_IMG_DIR_ . 'su/' . $objMpSupplier->id_ps_supplier . '.jpg');
                    }

                    Tools::redirectAdmin(
                        $this->context->link->getAdminLink('AdminMpSuppliers') . '&updatewk_mp_suppliers=&id_wk_mp_supplier=' . (int) $mp_supplier_id . '&conf=7'
                    );
                }
                $imageName = file_exists(
                    _PS_MODULE_DIR_ . 'marketplace/views/img/mpsuppliers/' . $mp_supplier_id . '.jpg'
                ) ? $mp_supplier_id . '.jpg' : 'default_supplier.png';
                $image = __PS_BASE_URI__ . 'modules/marketplace/views/img/mpsuppliers/' . $imageName;
                $this->context->smarty->assign('supplier_image', $image);
                $objMpSeller = new WkMpSeller($objMpSupplier->id_seller);
                if (!in_array($objMpSeller->id_shop_group, Shop::getContextListShopID())) {
                    // For shop group
                    $this->errors[] = $this->l('You can not add or edit a suppliers in this shop context: select a shop instead of a group of shops.');

                    return;
                }
                $mp_id_seller = $objMpSupplier->id_seller;
                $supplierInfo = $objMpSupplier->getMpSupplierAllDetails($mp_supplier_id);
                $this->context->smarty->assign('supplier_info', $supplierInfo);
                if ($supplierInfo['active']) {
                    $product_list = WkMpSuppliers::getProductsForUpdateSupplierBySellerIdAndPsSupplierId(
                        $objMpSupplier->id_seller,
                        $objMpSupplier->id_ps_supplier,
                        $this->context->language->id
                    );
                    if ($product_list) {
                        $this->context->smarty->assign('product_list', $product_list);
                    }
                }
                if ($imageName != 'default_supplier.png') {
                    $this->context->smarty->assign([
                        'wk_delete_logo_path' => $this->context->link->getAdminLink('AdminMpSuppliers') . '&updatewk_mp_suppliers=&id_wk_mp_supplier=' . (int) $mp_id_seller . '&delete_logo=1',
                        'confirm_msg' => $this->l('Are you sure?'),
                    ]);
                }
            }
        }

        // Set default lang at every form according to configuration multi-language
        WkMpHelper::assignDefaultLang($mp_id_seller);
        WkMpHelper::defineGlobalJSVariables(); // Define global js variable on js file

        $this->context->smarty->assign(
            [
                'countryinfo' => Country::getCountries($this->context->language->id),
                'path_css' => _THEME_CSS_DIR_,
                'ad' => __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_),
                'autoload_rte' => true,
                'lang' => true,
                'iso' => $this->context->language->iso_code,
                'mp_module_dir' => _MODULE_DIR_,
                'ps_module_dir' => _PS_MODULE_DIR_,
                'ps_img_dir' => _PS_IMG_ . 'l/',
                'self' => dirname(__FILE__),
            ]
        );

        $this->fields_form = [
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        return parent::renderForm();
    }

    public function ajaxProcessGetStateByCountry()
    {
        if (Tools::getValue('fun') == 'get_state') {
            $result = [];
            $result['status'] = 'fail';
            $countryId = Tools::getValue('countryid');
            $states = State::getStatesByIdCountry($countryId);
            if ($states) {
                $result['status'] = 'success';
                $result['info'] = $states;
            }
            $result = json_encode($result);
            echo $result;
        }

        exit; // ajax close
    }

    public function ajaxProcessGetSupplierByCustomerId()
    {
        $idCustomer = Tools::getValue('selected_id_customer');
        $result = [];
        $result['status'] = 0;
        if ($idCustomer) {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId($idCustomer);
            if ($mpSeller) {
                $objMpSupplier = new WkMpSuppliers();
                $suppliers = $objMpSupplier->getSuppliersForProductBySellerId($mpSeller['id_seller']);
                if ($suppliers) {
                    $result['status'] = 1;
                    $result['info'] = $suppliers;
                }
            }
        }

        $data = json_encode($result);
        exit($data);
    }

    public function displayAjaxDniRequired()
    {
        if ($id_country = Tools::getValue('id_country')) {
            $resp = Address::dniRequired($id_country);
            exit($resp);
        }

        exit; // ajax close
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        if ((($this->display == 'edit') && (Shop::getContext() != Shop::CONTEXT_SHOP))
            || ($this->display == 'add' && (Shop::getContext() != Shop::CONTEXT_SHOP))) {
            return;
        }

        $this->addJqueryPlugin('tagify');
        // tinymce
        $this->addJS(_PS_JS_DIR_ . 'tiny_mce/tiny_mce.js');
        if (version_compare(_PS_VERSION_, '1.6.0.11', '>')) {
            $this->addJS(_PS_JS_DIR_ . 'admin/tinymce.inc.js');
        } else {
            $this->addJS(_PS_JS_DIR_ . 'tinymce.inc.js');
        }

        if (Tools::getValue('addwk_mp_suppliers') !== false
        || Tools::getValue('updatewk_mp_suppliers') !== false) {
            Media::addJsDef(
                [
                    'req_suppname' => $this->l('Supplier name is required'),
                    'inv_suppname' => $this->l('Invalid supplier name'),
                    'inv_suppphone' => $this->l('Invalid phone number'),
                    'inv_suppmobile' => $this->l('Invalid mobile phone number'),
                    'req_suppaddress' => $this->l('Address is required'),
                    'inv_suppaddress' => $this->l('Invalid address'),
                    'req_suppzip' => $this->l('Zip/Postal code is required'),
                    'inv_suppzip' => $this->l('Invalid zip/postal code'),
                    'req_suppcity' => $this->l('City is required'),
                    'inv_suppcity' => $this->l('City name is invalid'),
                    'allow_tagify' => 1,
                    'addkeywords' => $this->l('Add keywords'),
                    'languages' => Language::getLanguages(),
                    'inv_language_title' => $this->l('Invalid meta title'),
                    'inv_language_desc' => $this->l('Invalid meta description'),
                    'invalid_logo' => $this->l('Invalid image extensions, only jpg, jpeg and png are allowed.'),
                    'static_token' => Tools::getValue('token'),
                    'allowed_file_size' => Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'),
                    'allowed_file_size_error' => sprintf(
                        $this->l('Uploaded file size must be less than %s MB.'),
                        Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')
                    ),
                ]
            );
            $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/suppliers/supplier_form_validation.js');
            $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/addmanufacturer.css');
        }
    }
}
