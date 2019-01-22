<?php
namespace Sales\Controller;

use Zend\Json\Json;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;
use Sales\Model\SalesTable;
use Sales\Model\SalesVoucherDetailsTable;
use Sales\Model\PriceTable;
use Zend\Mvc\Controller\AbstractActionController;

class SalesController extends AbstractActionController
{
    private $table;

    public function __construct(SalesTable $table,SalesVoucherDetailsTable $tableVoucher,PriceTable $priceTable)
    {
        $this->table = $table;
        $this->tableVoucher = $tableVoucher;
        $this->priceTable = $priceTable;
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
        $alertContainer = new Container('alert');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $sales = $request->getPost();
            $lastId = $this->table->saveSales($sales);
            $insertedVoucher = $this->tableVoucher->saveSales($sales,$lastId);
            if($lastId > 0 || $insertedVoucher > 0){
                $alertContainer->alertMsg = 'Sales details added successfully';
            }
            return $this->redirect()->toUrl("sales");
        }
        return new ViewModel([
            'productList' => $this->table->getAllProducts(),
            'usersList' => $this->table->getAllUsers(),
            'accountsList' => $this->table->getAllAccounts(),
            'salesLastId' => $this->table->getLastSalesId()
        ]);
    }
    

    public function editAction()
    {
        $alertContainer = new Container('alert');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $sales = $request->getPost();
            $lastId =$this->table->saveSales($sales);
            $updatedVoucher = $this->tableVoucher->saveSales($sales,$lastId);
            if($lastId > 0 || $updatedVoucher > 0){
                $alertContainer->alertMsg = 'Sales details updated successfully';
            }
            return $this->redirect()->toUrl("sales");
        }
        $id = base64_decode($this->params()->fromRoute('id'));
        return new ViewModel([
            'sales' => $this->table->getSales($id),
            'salesVoucher' => $this->tableVoucher->getSales($id),
            'productList' => $this->table->getAllProducts(),
            'usersList' => $this->table->getAllUsers(),
            'accountsList' => $this->table->getAllAccounts(),
            'salesLastId' => $this->table->getLastSalesId()
        ]);
    }
    
    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $resultFirst = $this->tableVoucher->deleteSales($params);
            if($resultFirst > 0){
                $result = $this->table->deleteSales($params);
            }
            $viewModel = new ViewModel(['result' => $result]);
            return $viewModel->setTerminal(true);
        }
    }
    
    public function priceAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $price = $request->getPost();
            $result = $this->priceTable->getPrice();
            $viewModel = new ViewModel(['result' => $result]);
            return $viewModel->setTerminal(true);
        }
    }
    
    public function priceUpdateAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $price = $request->getPost();
            $result = $this->priceTable->savePrice($price);
            $viewModel = new ViewModel(['result' => $result]);
            return $viewModel->setTerminal(true);
        }
    }
    
    public function getUserListAction()
    {
        $search = $_GET['q'];
        $viewModel = new ViewModel([
            'salesUsers' => $this->table->getActiveUsers($search),
        ]);
        return $viewModel->setTerminal(true);
    }
}