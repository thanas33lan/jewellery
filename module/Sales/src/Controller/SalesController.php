<?php
namespace Sales\Controller;

use Zend\Json\Json;
use Zend\Session\Container;
use Sales\Model\SalesTable;
use Zend\View\Model\ViewModel;
use Sales\Model\SalesVoucherDetailsTable;
use Zend\Mvc\Controller\AbstractActionController;

class SalesController extends AbstractActionController
{
    private $table;

    public function __construct(SalesTable $table,SalesVoucherDetailsTable $tableVoucher)
    {
        $this->table = $table;
        $this->tableVoucher = $tableVoucher;
    }

    public function indexAction()
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
            return $this->redirect()->toUrl("/sales/view-voucher");
        }
        return new ViewModel([
            'productList' => $this->table->getAllProducts(),
            'accountsList' => $this->table->getAllAccounts(),
            'salesLastId' => $this->table->getLastSalesId()
        ]);
    }
    
    public function addAction()
    {
        $alertContainer = new Container('alert');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $sales = $request->getPost();
            // \Zend\Debug\Debug::dump($sales);die;
            $lastId = $this->table->saveSales($sales);
            $insertedVoucher = $this->tableVoucher->saveSales($sales,$lastId);
            if($lastId > 0 || $insertedVoucher > 0){
                $alertContainer->alertMsg = 'Sales details added successfully';
            }
            return $this->redirect()->toUrl("/sales/view-voucher");
        }
        return new ViewModel([
            'productList' => $this->table->getAllProducts(),
            'accountsList' => $this->table->getAllAccounts(),
            'salesLastId' => $this->table->getLastSalesId()
        ]);
    }
    
    public function viewVoucherAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $parameters = $request->getPost();
            $result = $this->table->fetchAll($parameters);
            return $this->getResponse()->setContent(Json::encode($result));
        }
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
            return $this->redirect()->toUrl("/sales/view-voucher");
        }
        $id = base64_decode($this->params()->fromRoute('id'));
        return new ViewModel([
            'sales' => $this->table->getSales($id),
            'salesVoucher' => $this->tableVoucher->getSales($id),
            'productList' => $this->table->getAllProducts(),
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
}