<?php
namespace Login\Controller;

use Login\Model\UserTable;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;

class IndexController extends AbstractActionController
{
    private $table;

    public function __construct(UserTable $table)
    {
        $this->table = $table;
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = $request->getPost();
            $redirect = $this->table->getUserLogin($user);
            return $this->redirect()->toUrl($redirect);
        }
        $view = new ViewModel();
        $view->setTerminal(true);
        return $view;
    }

    public function logoutAction()
    {
        $logincontainer = new Container('user');
        $logincontainer->roleId = "";
        $logincontainer->roleCode = ""; 
        $logincontainer->userId = ""; 
        $logincontainer->name = ""; 
        $logincontainer->userName = ""; 

        $logincontainer->getManager()->getStorage()->clear('user');
        return $this->redirect()->toUrl("/login");
    }
}