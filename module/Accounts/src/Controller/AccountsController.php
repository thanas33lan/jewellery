<?php
namespace Accounts\Controller;

use Accounts\Model\AccountsTable;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;

class AccountsController extends AbstractActionController
{
    private $table;

    public function __construct(AccountsTable $table)
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
            $accounts = $request->getPost();
            $this->table->saveAccounts($accounts);
            return $this->redirect()->toUrl("/accounts");
        }
        return new ViewModel([
            'accountType' => $this->table->fetchAllAccountType()
        ]);
    }
    
    public function editAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $accounts = $request->getPost();
            $this->table->saveAccounts($accounts);
            return $this->redirect()->toUrl("/accounts");
        }
        $id = base64_decode($this->params()->fromRoute('id'));
        return new ViewModel([
            'accounts' => $this->table->getAccounts($id),
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
}