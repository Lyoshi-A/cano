<?php
class SupplierPageListModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $suppliers = Supplier::getSuppliers(true, $this->context->language->id);

        $this->context->smarty->assign([
            'meta_title' => 'Our Suppliers',
            'meta_description' => 'Discover all our suppliers.',
        ]);
        $this->context->smarty->assign([
            'suppliers' => $suppliers,
        ]);

        $this->setTemplate('module:supplierpage/views/templates/front/list.tpl');
    }
}