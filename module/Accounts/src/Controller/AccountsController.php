<?php
namespace Accounts\Controller;

use Zend\Json\Json;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Accounts\Model\AccountsTable;
use Accounts\Model\AccountsGeneralTable;
use Accounts\Model\AccountsRegisterTable;
use Zend\Mvc\Controller\AbstractActionController;

class AccountsController extends AbstractActionController
{
    private $table;

    public function __construct(AccountsTable $table,AccountsRegisterTable $tableRegister,AccountsGeneralTable $tableGeneral)
    {
        $this->table = $table;
        $this->tableRegister = $tableRegister;
        $this->tableGeneral = $tableGeneral;
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
    
    public function registerAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $parameters = $request->getPost();
            $result = $this->tableRegister->fetchAll($parameters);
            return $this->getResponse()->setContent(Json::encode($result));
        }
    }
    
    public function generalAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $parameters = $request->getPost();
            $result = $this->tableGeneral->fetchAll($parameters);
            return $this->getResponse()->setContent(Json::encode($result));
        }
    }
    
    public function addAction()
    {
        $alertContainer = new Container('alert');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $accounts = $request->getPost();
            $lastId = $this->table->saveAccounts($accounts);
            $insertedRegister = $this->tableRegister->saveAccounts($accounts,$lastId);
            $insertedGeneral = $this->tableGeneral->saveAccounts($accounts,$lastId);
            if($lastId > 0 || $insertedRegister > 0 || $insertedGeneral > 0){
                $alertContainer->alertMsg = 'Products details added successfully';
            }
            return $this->redirect()->toUrl("/accounts");
        }
        return new ViewModel([
            'accountType' => $this->table->fetchAllAccountType()
        ]);
    }
    
    public function editAction()
    {
        $alertContainer = new Container('alert');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $accounts = $request->getPost();
            $updatedId = $this->table->saveAccounts($accounts);
            $updatedRegister = $this->tableRegister->saveAccounts($accounts,0);
            $updatedGeneral = $this->tableGeneral->saveAccounts($accounts,0);
            if($updatedId > 0 || $updatedRegister > 0 || $updatedGeneral > 0){
                $alertContainer->alertMsg = 'User details updated successfully';
            }
            return $this->redirect()->toUrl("/accounts");
        }
        $id = base64_decode($this->params()->fromRoute('id'));
        return new ViewModel([
            'accounts' => $this->table->getAccounts($id),
            'accountsRegister' => $this->tableRegister->getAccounts($id),
            'accountsGeneral' => $this->tableGeneral->getAccounts($id),
            'accountType' => $this->table->fetchAllAccountType()
        ]);
    }
    
    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $result = $this->table->deleteAccounts($params);
            $viewModel = new ViewModel([
                'result' => $result
            ]);
            return $viewModel->setTerminal(true);
        }
    }
    
    public function deleteRegisterAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $result = $this->tableRegister->deleteAccounts($params);
            $viewModel = new ViewModel([
                'result' => $result
            ]);
            return $viewModel->setTerminal(true);
        }
    }
    
    public function deleteGeneralAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $result = $this->tableGeneral->deleteAccounts($params);
            $viewModel = new ViewModel([
                'result' => $result
            ]);
            return $viewModel->setTerminal(true);
        }
    }
}