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
                Model\PriceTable::class => function($container) {
                    $tableGateway = $container->get(Model\PriceTableGateway::class);
                    return new Model\PriceTable($tableGateway);
                },
                Model\SalesEmiTable::class => function($container) {
                    $tableGateway = $container->get(Model\SalesEmiTableGateway::class);
                    return new Model\SalesEmiTable($tableGateway);
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
                Model\PriceTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype2 = new ResultSet();
                    $resultSetPrototype2->setArrayObjectPrototype(new Model\Price);
                    return new TableGateway('today_price', $dbAdapter, null, $resultSetPrototype2);
                },
                Model\SalesEmiTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype3 = new ResultSet();
                    $resultSetPrototype3->setArrayObjectPrototype(new Model\SalesEmi);
                    return new TableGateway('sales_emi', $dbAdapter, null, $resultSetPrototype3);
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
                        $container->get(Model\SalesVoucherDetailsTable::class),
                        $container->get(Model\PriceTable::class),
                        $container->get(Model\SalesEmiTable::class)
                    );
                },
            ],
        ];
    }
}