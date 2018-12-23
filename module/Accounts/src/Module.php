<?php
namespace Accounts;

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
                Model\AccountsTable::class => function($container) {
                    $tableGateway = $container->get(Model\AccountsTableGateway::class);
                    return new Model\AccountsTable($tableGateway);
                },
                Model\AccountsTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Accounts);
                    return new TableGateway('accounts', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }

    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\AccountsController::class => function($container) {
                    return new Controller\AccountsController(
                        $container->get(Model\AccountsTable::class)
                    );
                },
            ],
        ];
    }
}