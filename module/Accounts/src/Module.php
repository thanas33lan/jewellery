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
                Model\AccountsRegisterTable::class => function($container) {
                    $tableGateway2 = $container->get(Model\AccountsRegisterTableGateway::class);
                    return new Model\AccountsRegisterTable($tableGateway2);
                },
                Model\AccountsGeneralTable::class => function($container) {
                    $tableGateway3 = $container->get(Model\AccountsGeneralTableGateway::class);
                    return new Model\AccountsGeneralTable($tableGateway3);
                },

                Model\AccountsTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Accounts);
                    return new TableGateway('accounts', $dbAdapter, null, $resultSetPrototype);
                },
                Model\AccountsRegisterTableGateway::class => function ($container) {
                    $dbAdapter1 = $container->get(AdapterInterface::class);
                    $resultSetPrototype2 = new ResultSet();
                    $resultSetPrototype2->setArrayObjectPrototype(new Model\AccountsRegister);
                    return new TableGateway('accounts_register', $dbAdapter1, null, $resultSetPrototype2);
                },
                Model\AccountsGeneralTableGateway::class => function ($container) {
                    $dbAdapter2 = $container->get(AdapterInterface::class);
                    $resultSetPrototype3 = new ResultSet();
                    $resultSetPrototype3->setArrayObjectPrototype(new Model\AccountsGeneral);
                    return new TableGateway('accounts_general', $dbAdapter2, null, $resultSetPrototype3);
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
                        $container->get(Model\AccountsTable::class),
                        $container->get(Model\AccountsRegisterTable::class),
                        $container->get(Model\AccountsGeneralTable::class)
                    );
                },
            ],
        ];
    }
}