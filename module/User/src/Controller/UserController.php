<?php
namespace User\Controller;

use User\Model\UserTable;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;

class UserController extends AbstractActionController
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
            $parameters = $request->getPost();
            $result = $this->table->fetchAll($parameters);
            return $this->getResponse()->setContent(Json::encode($result));
        }
    }
    
    public function addAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = $request->getPost();
            $this->table->saveUser($user);
            return $this->redirect()->toUrl("/user");
        }
        return new ViewModel([
            'roles' => $this->table->fetchAllRoles(),
            'states' => $this->table->fetchAllState()
        ]);
    }
    
    public function editAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = $request->getPost();
            $this->table->saveUser($user);
            return $this->redirect()->toUrl("/user");
        }
        $id = base64_decode($this->params()->fromRoute('id'));
        return new ViewModel([
            'user' => $this->table->getUser($id)
        ]);
    }
    
    public function deleteAction()
    {
        $id = base64_decode($this->params()->fromRoute('id'));
        $this->table->deleteUser($id);
        return $this->redirect()->toUrl("/user");
    }
}