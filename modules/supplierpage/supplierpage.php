<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class SupplierPage extends Module
{
    public function __construct()
    {
        $this->name = 'supplierpage';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'YourName';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Supplier Page');
        $this->description = $this->l('Creates a dynamic page for individual suppliers.');
    }

    public function install()
    {
        return parent::install() && $this->registerHook('moduleRoutes');
    }

    public function hookModuleRoutes($params)
    {
        return [
            'module-supplierpage-details' => [
                'controller' => 'details',
                'rule' => 'supplier/{id_supplier}',
                'keywords' => [
                    'id_supplier' => ['regexp' => '[0-9]+', 'param' => 'id_supplier'],
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'supplierpage',
                ],
            ],
            'module-supplierpage-list' => [
                'controller' => 'list',
                'rule' => 'suppliers',
                'params' => [
                    'fc' => 'module',
                    'module' => 'supplierpage',
                ],
            ],
        ];
    }
}