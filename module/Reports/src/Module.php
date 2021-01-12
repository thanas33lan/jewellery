<?php
namespace Reports;

// Add these import statements:
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    const VERSION = '3.0.3-dev';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                \Sales\Model\SalesTable::class => function($container) {
                    $tableGateway = $container->get(\Sales\Model\SalesTableGateway::class);
                    return new \Sales\Model\SalesTable($tableGateway);
                },
                \Products\Model\ProductsTable::class => function($container) {
                    $tableGateway = $container->get(\Products\Model\ProductsTableGateway::class);
                    return new \Products\Model\ProductsTable($tableGateway);
                },

                \Sales\Model\SalesTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new \Sales\Model\Sales);
                    return new TableGateway('sales_details', $dbAdapter, null, $resultSetPrototype);
                },
                \Products\Model\ProductsTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new \Products\Model\Products);
                    return new TableGateway('products', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\ReportsSalesController::class => function($container) {
                    return new Controller\ReportsSalesController(
                        $container->get(\Sales\Model\SalesTable::class)
                    );
                },
                Controller\ReportsProductsController::class => function($container) {
                    return new Controller\ReportsProductsController(
                        $container->get(\Sales\Model\SalesTable::class),
                        $container->get(\Products\Model\ProductsTable::class)
                    );
                },
            ],
        ];
    }
}