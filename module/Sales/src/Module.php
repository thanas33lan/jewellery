<?php
namespace Sales;

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
                Model\SalesTable::class => function($container) {
                    $tableGateway = $container->get(Model\SalesTableGateway::class);
                    return new Model\SalesTable($tableGateway);
                },
                Model\SalesVoucherDetailsTable::class => function($container) {
                    $tableGateway = $container->get(Model\SalesVoucherDetailsTableGateway::class);
                    return new Model\SalesVoucherDetailsTable($tableGateway);
                },

                Model\SalesTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Sales);
                    return new TableGateway('sales_details', $dbAdapter, null, $resultSetPrototype);
                },
                Model\SalesVoucherDetailsTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype1 = new ResultSet();
                    $resultSetPrototype1->setArrayObjectPrototype(new Model\SalesVoucherDetails);
                    return new TableGateway('sales_voucher_details', $dbAdapter, null, $resultSetPrototype1);
                },
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\SalesController::class => function($container) {
                    return new Controller\SalesController(
                        $container->get(Model\SalesTable::class),
                        $container->get(Model\SalesVoucherDetailsTable::class)
                    );
                },
            ],
        ];
    }
}