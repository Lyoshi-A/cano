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

class AdminMpGenerateCombinationController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        if (!$this->loadObject(true)) {
            return;
        }
        parent::__construct();
    }

    public function initContent()
    {
        $this->initToolbar();
        $this->display = '';
        $this->content .= $this->renderForm();

        $this->context->smarty->assign([
            'content' => $this->content,
        ]);
        parent::initContent();
    }

    public function renderForm()
    {
        WkMpProductAttribute::assignAttributeValues();
        $this->context->smarty->assign([
            'wkself' => dirname(__FILE__),
            'attribute_js' => $this->displayAndReturnAttributeJs(),
            'backendController' => 1,
        ]);

        $this->fields_form = [
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        return parent::renderForm();
    }

    protected static function displayAndReturnAttributeJs()
    {
        if (_PS_VERSION_ >= '8.0.0') {
            $attributes = ProductAttribute::getAttributes(Context::getContext()->language->id, true);
        } else {
            $attributes = Attribute::getAttributes(Context::getContext()->language->id, true);
        }
        $attributeJs = [];
        foreach ($attributes as $attribute) {
            $attributeJs[$attribute['id_attribute_group']][$attribute['id_attribute']] = $attribute['name'];
        }

        return $attributeJs;
    }

    public function postProcess()
    {
        if (Tools::isSubmit('GenerateCombination')) {
            $this->id_mp_product = Tools::getValue('id_mp_product');
            if (!is_array(Tools::getValue('options'))) {
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminMpGenerateCombination') . '&msg=1&id_mp_product=' . $this->id_mp_product);
            } else {
                if (!Validate::isInt(Tools::getValue('quantity'))) {
                    $this->errors[] = $this->l('Quantity should be valid.');
                }
                if (Tools::getValue('reference') && !Validate::isReference(Tools::getValue('reference'))) {
                    $this->errors[] = $this->l('Reference is not valid.');
                }

                if (!count($this->errors)) {
                    $objSellerProduct = new WkMpSellerProduct($this->id_mp_product);
                    $idPsProduct = $objSellerProduct->id_ps_product;
                    $tab = array_values(Tools::getValue('options'));
                    if (count($tab) && Validate::isLoadedObject($objSellerProduct)) {
                        // Combination Attribute list
                        $this->combinations = array_values($this->createCombinations($tab));
                        // Combination Values
                        $productAttribute = array_values(array_map([$this, 'addAttribute'], $this->combinations));

                        // Delete all combination before generating combinations
                        $objProduct = new Product($idPsProduct);
                        $objProduct->deleteProductAttributes();

                        if ($productAttribute
                        && $this->combinations
                        && (count($productAttribute) == count($this->combinations))) {
                            foreach ($productAttribute as $attributeKey => $attributeValue) {
                                $idPsProductAttribute = 0; // Because we are creating
                                $idImages = [];

                                WkMpProductAttribute::saveMpProductCombination(
                                    $this->id_mp_product,
                                    $idPsProductAttribute,
                                    $this->combinations[$attributeKey],
                                    $attributeValue['mp_reference'],
                                    '', // ean
                                    '', // upc
                                    '', // isbn
                                    $attributeValue['mp_price'],
                                    0,
                                    0,
                                    $attributeValue['mp_quantity'],
                                    $attributeValue['mp_weight'],
                                    1,
                                    $attributeValue['mp_available_date'],
                                    $idImages,
                                    '',
                                    false,
                                    false,
                                    '',
                                    ''
                                );
                            }
                        }

                        Tools::redirectAdmin(
                            $this->context->link->getAdminLink('AdminSellerProductDetail') .
                            '&updatewk_mp_seller_product&conf=4&tab=wk-combination&id_mp_product=' . $this->id_mp_product
                        );
                    } else {
                        Tools::redirectAdmin(
                            $this->context->link->getAdminLink('AdminMpGenerateCombination') .
                            '&msg=2&id_mp_product=' . $this->id_mp_product
                        );
                    }
                }
            }
        }
    }

    public function addAttribute($attributes, $price = 0, $weight = 0)
    {
        foreach ($attributes as $attribute) {
            $price += (float) preg_replace('/[^0-9.-]/', '', str_replace(',', '.', Tools::getValue('price_impact_' . (int) $attribute)));
            $weight += (float) preg_replace('/[^0-9.-]/', '', str_replace(',', '.', Tools::getValue('weight_impact_' . (int) $attribute)));
        }

        if ($this->id_mp_product) {
            return [
                'mp_id_product' => (int) $this->id_mp_product,
                'mp_price' => (float) $price,
                'mp_weight' => (float) $weight,
                'mp_quantity' => (int) Tools::getValue('quantity'),
                'mp_reference' => pSQL(Tools::getValue('reference')),
                'mp_default_on' => 0,
                'mp_available_date' => '0000-00-00',
            ];
        }

        return [];
    }

    public function createCombinations($list)
    {
        if (count($list) <= 1) {
            return count($list) ? array_map(
                function ($v) {
                    return [$v];
                },
                $list[0]
            ) : $list;
        }
        $res = [];
        $first = array_pop($list);
        foreach ($first as $attribute) {
            $tab = $this->createCombinations($list);
            foreach ($tab as $toadd) {
                $res[] = is_array($toadd) ? array_merge($toadd, [$attribute]) : [$toadd, $attribute];
            }
        }

        return $res;
    }

    public function initBreadcrumbs($tabId = null, $tabs = null)
    {
        parent::initBreadcrumbs();
        $dummy = ['name' => '', 'href' => '', 'icon' => ''];
        $breadcrumbs2 = [
            'container' => $dummy,
            'tab' => $dummy,
            'action' => $dummy,
        ];

        $tabs = Tab::recursiveTab($this->id, $tabs);
        if (isset($tabs[0])) {
            $breadcrumbs2['tab']['name'] = $tabs[0]['name'];
            $breadcrumbs2['tab']['href'] = '';
        }

        $this->context->smarty->assign([
            'breadcrumbs2' => $breadcrumbs2,
            'quick_access_current_link_name' => $breadcrumbs2['tab']['name'] . (isset($breadcrumbs2['action']) ? ' - ' . $breadcrumbs2['action']['name'] : ''),
            'quick_access_current_link_icon' => $breadcrumbs2['container']['icon'],
        ]);

        /* BEGIN - Backward compatibility < 1.6.0.3 */
        $this->breadcrumbs[] = $tabs[0]['name'];
        $this->context->smarty->assign(
            'navigationPipe',
            Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>'
        );
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addCSS(_MODULE_DIR_ . $this->module->name . '/views/css/mp_global_style.css');
        $this->addJS(_MODULE_DIR_ . $this->module->name . '/views/js/generatecombination.js');
    }
}
