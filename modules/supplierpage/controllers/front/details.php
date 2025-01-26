<?php
class SupplierPageDetailsModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $id_supplier = (int)Tools::getValue('id_supplier');
        if (!$id_supplier) {
            Tools::redirect('index.php');
        }

        $supplier = new Supplier($id_supplier, $this->context->language->id);

        if (!Validate::isLoadedObject($supplier)) {
            Tools::redirect('index.php');
        }
//        $this->context->smarty->assign('meta_title', $supplier->name);
        $this->context->smarty->assign([
            'id' => $id_supplier,
            'supplier' => $supplier,
        ]);

        $this->setTemplate('module:supplierpage/views/templates/front/details.tpl');
    }
}