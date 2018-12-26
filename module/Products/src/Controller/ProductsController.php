<?php
namespace Products\Controller;

use Products\Model\ProductsTable;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;

class ProductsController extends AbstractActionController
{
    private $table;

    public function __construct(ProductsTable $table)
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
            $products = $request->getPost();
            $this->table->saveProducts($products);
            return $this->redirect()->toUrl("/products");
        }
        return new ViewModel([
            'productsType' => $this->table->fetchAllProductsType()
        ]);
    }
    
    public function editAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $products = $request->getPost();
            $this->table->saveProducts($products);
            return $this->redirect()->toUrl("/products");
        }
        $id = base64_decode($this->params()->fromRoute('id'));
        return new ViewModel([
            'products' => $this->table->getProducts($id),
            'productsType' => $this->table->fetchAllProductsType()
        ]);
    }
    
    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $result = $this->table->deleteProducts($params);
            $viewModel = new ViewModel([
                'result' => $result
            ]);
            return $viewModel->setTerminal(true);
        }
    }
}