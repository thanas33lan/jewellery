<?php
namespace Vat;

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
                Model\VatTable::class => function($container) {
                    $tableGateway = $container->get(Model\VatTableGateway::class);
                    return new Model\VatTable($tableGateway);
                },
                Model\VatTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Vat);
                    return new TableGateway('vat_details', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\VatController::class => function($container) {
                    return new Controller\VatController(
                        $container->get(Model\VatTable::class)
                    );
                },
            ],
        ];
    }
}