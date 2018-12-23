<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Session\Container;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

class Module
{
    const VERSION = '3.0.3-dev';

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        //no need to call presetter if request is from CLI
        if (php_sapi_name() != 'cli') {
            $eventManager->attach('dispatch', array($this, 'preSetter'), 100);
            $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'dispatchError'), -999);
        }
    }

    public function dispatchError(MvcEvent $event) 
    {
        $error = $event->getError();
        $baseModel = new ViewModel();
        $baseModel->setTemplate('layout/layout');
        $baseModel->setTerminal(true);
    }
    
    public function preSetter(MvcEvent $e) 
    {   
        if (($e->getRouteMatch()->getParam('controller') != 'Login\Controller\IndexController') || ($e->getRouteMatch()->getParam('controller') == '')) {
            $tempName=explode('Controller',$e->getRouteMatch()->getParam('controller'));
            if(substr($tempName[0], 0, -1) == 'Application' || substr($tempName[0], 0, -1) || 'Accounts' || substr($tempName[0], 0, -1) || 'Login'){
                $session = new Container('user');
                if (!isset($session->userId) || $session->userId == "") {
                    $url = $e->getRouter()->assemble(array(), ['name' => 'login']);
                    $response = $e->getResponse();
                    $response->getHeaders()->addHeaderLine('Location', $url);
                    $response->setStatusCode(302);
                    $response->sendHeaders();
                    // we can attach a listener for Event Route with a high priority
                    $stopCallBack = function($event) use ($response) {
                        $event->stopPropagation();
                        return $response;
                    };
                    //Attach the "break" as a listener with a high priority
                    $e->getApplication()->getEventManager()->attach(MvcEvent::EVENT_ROUTE, $stopCallBack, -10000);
                    return $response;
                }
            }else{
                if ($e->getRequest()->isXmlHttpRequest()) {
                    return;
                }
            }
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
