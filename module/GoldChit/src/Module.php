<?php
namespace GoldChit;

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
                Model\GoldChitTable::class => function($container) {
                    $tableGateway = $container->get(Model\GoldChitTableGateway::class);
                    return new Model\GoldChitTable($tableGateway);
                },
                Model\GoldChitTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\GoldChit);
                    return new TableGateway('goldchit_details', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\GoldChitController::class => function($container) {
                    return new Controller\GoldChitController(
                        $container->get(Model\GoldChitTable::class)
                    );
                },
            ],
        ];
    }
}