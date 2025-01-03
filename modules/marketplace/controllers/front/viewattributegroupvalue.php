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

class MarketplaceViewAttributeGroupValueModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        if ($this->context->customer->isLogged()) {
            $mpSeller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
            if ($mpSeller['active'] && Configuration::get('WK_MP_PRESTA_ATTRIBUTE_ACCESS')) {
                $idGroup = Tools::getValue('id_group');
                if (!$idGroup) {
                    Tools::redirect(__PS_BASE_URI__ . 'pagenotfound');
                }

                // Delete Attribute Value
                if (Tools::getValue('delete_attribute_value')) {
                    $deleteSuccess = 0;
                    if ($idAttribute = Tools::getValue('id_attribute')) {
                        if (!WkMpProductAttribute::checkCombinationByAttribute($idAttribute)) {
                            if (_PS_VERSION_ >= '8.0.0') {
                                $objAttribute = new ProductAttribute($idAttribute);
                            } else {
                                $objAttribute = new Attribute($idAttribute);
                            }
                            if ($objAttribute->delete()) {
                                // code for texture image
                                $deletePath = _PS_IMG_DIR_ . 'co/' . $idAttribute . '.jpg';
                                if (file_exists($deletePath)) {
                                    unlink($deletePath);
                                }
                                $deleteSuccess = 1;
                            }

                            Tools::redirect(
                                $this->context->link->getModuleLink(
                                    'marketplace',
                                    'viewattributegroupvalue',
                                    ['id_group' => $idGroup, 'deleted' => 1]
                                )
                            );
                        }
                    }

                    if (!$deleteSuccess) {
                        $this->errors[] = $this->module->l('This Attribute value is already in use you cannot edit or delete it.', 'productattribute');
                    }
                }

                $objAttributeGroup = new AttributeGroup($idGroup, $this->context->language->id);
                if ($objAttributeGroup->id) {
                    $this->context->smarty->assign('attribute_group_name', $objAttributeGroup->name);
                }

                // Attrbute Value List
                $groupAttribute = AttributeGroup::getAttributes($this->context->language->id, $idGroup);
                if ($groupAttribute) {
                    $i = 0;
                    $valueSet = [];
                    foreach ($groupAttribute as $groupAttributeEach) {
                        $valueSet[$i]['id'] = $groupAttributeEach['id_attribute'];
                        $valueSet[$i]['name'] = $groupAttributeEach['name'];
                        if (WkMpProductAttribute::ifColorAttributegroup($idGroup)) {
                            /* code for color texture */
                            $valueSet[$i]['color'] = $groupAttributeEach['color'];
                            $valueSet[$i]['imageTextureExists'] = file_exists(
                                _PS_IMG_DIR_ . 'co/' . $groupAttributeEach['id_attribute'] . '.jpg'
                            );
                        }
                        if (WkMpProductAttribute::checkCombinationByAttribute($groupAttributeEach['id_attribute'])) {
                            $valueSet[$i]['editable'] = 0;
                        } else {
                            $valueSet[$i]['editable'] = $groupAttributeEach['id_attribute'];
                        }
                        ++$i;
                    }

                    $this->context->smarty->assign('value_set', $valueSet);
                }

                if (WkMpProductAttribute::ifColorAttributegroup($idGroup)) {
                    $this->context->smarty->assign('is_color', 1);
                }

                $this->context->smarty->assign([
                    'id_group' => $idGroup,
                    'logic' => 'mp_prod_attribute',
                    'img_col_dir' => _THEME_COL_DIR_,
                ]);
                $this->defineJSVars();
                $this->setTemplate(
                    'module:marketplace/views/templates/front/product/combination/viewattributegroupvalue.tpl'
                );
            } else {
                Tools::redirect(__PS_BASE_URI__ . 'pagenotfound');
            }
        } else {
            Tools::redirect($this->context->link->getPageLink('my-account'));
        }
    }

    public function defineJSVars()
    {
        $jsVars = [
            'error_msg2' => $this->module->l('This Attribute is already in use you cannot edit or delete it.', 'viewattributegroupvalue'),
            'confirm_delete' => $this->module->l('Are you sure?', 'viewattributegroupvalue'),
            'display_name' => $this->module->l('Display', 'viewattributegroupvalue'),
            'records_name' => $this->module->l('records per page', 'viewattributegroupvalue'),
            'no_product' => $this->module->l('No data found', 'viewattributegroupvalue'),
            'show_page' => $this->module->l('Showing page', 'viewattributegroupvalue'),
            'show_of' => $this->module->l('of', 'viewattributegroupvalue'),
            'no_record' => $this->module->l('No records available', 'viewattributegroupvalue'),
            'filter_from' => $this->module->l('filtered from', 'viewattributegroupvalue'),
            't_record' => $this->module->l('total records', 'viewattributegroupvalue'),
            'search_item' => $this->module->l('Search', 'viewattributegroupvalue'),
            'p_page' => $this->module->l('Previous', 'viewattributegroupvalue'),
            'n_page' => $this->module->l('Next', 'viewattributegroupvalue'),
        ];

        Media::addJsDef($jsVars);
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = [];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Marketplace', 'viewattributegroupvalue'),
            'url' => $this->context->link->getModuleLink('marketplace', 'dashboard'),
        ];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Product Attribute', 'viewattributegroupvalue'),
            'url' => $this->context->link->getModuleLink('marketplace', 'productattribute'),
        ];
        $breadcrumb['links'][] = [
            'title' => $this->module->l('Attribute Value', 'viewattributegroupvalue'),
            'url' => '',
        ];

        return $breadcrumb;
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->registerStylesheet(
            'mp-marketplace_account',
            'modules/' . $this->module->name . '/views/css/marketplace_account.css'
        );
        $this->registerJavascript(
            'mp-productattribute',
            'modules/' . $this->module->name . '/views/js/productattribute.js'
        );

        // data table file included
        $this->registerStylesheet(
            'datatable_bootstrap',
            'modules/' . $this->module->name . '/views/css/datatable_bootstrap.css'
        );
        $this->registerJavascript(
            'mp-jquery-dataTables',
            'modules/' . $this->module->name . '/views/js/jquery.dataTables.min.js'
        );
        $this->registerJavascript(
            'mp-dataTables.bootstrap',
            'modules/' . $this->module->name . '/views/js/dataTables.bootstrap.js'
        );
        $this->registerJavascript(
            'wk-mp-dataTables',
            'modules/' . $this->module->name . '/views/js/wk_mp_datatables.js'
        );
    }
}
