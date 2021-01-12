<?php
namespace Reports\Controller;

use Products\Model\ProductsTable;
use Sales\Model\SalesTable;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;

class ReportsProductsController extends AbstractActionController
{
    private $table;

    public function __construct(SalesTable $salesTable, ProductsTable $table)
    {
        $this->salesTable = $salesTable;
        $this->table = $table;
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) 
        {
            $parameters = $request->getPost();
            $result = $this->table->fetchAll($parameters);
            return $this->getResponse()->setContent(Json::encode($result));
        }
        return new ViewModel([
            'productList' => $this->salesTable->getAllProducts()
        ]);
    }
    
    public function salesReportsAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) 
        {
            $parameters = $request->getPost();
            $result = $this->table->fetchAll($parameters);
            return $this->getResponse()->setContent(Json::encode($result));
        }
    }
    
    public function exportAction()
    {
        $request = $this->getRequest();
        if($request->isPost())
        {
            $params = $request->getPost();
            $result = $this->table->exportReports($params);
            $viewModel = new ViewModel();
            $viewModel->setVariables(array('result' =>$result));
            $viewModel->setTerminal(true);
            return $viewModel;
        }
    }
}