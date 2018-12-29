<?php
namespace Vat\Controller;

use Vat\Model\VatTable;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;

class VatController extends AbstractActionController
{
    private $table;

    public function __construct(VatTable $table)
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
            $vat = $request->getPost();
            $this->table->saveVat($vat);
            return $this->redirect()->toUrl("/vat");
        }
        return new ViewModel();
    }
    
    public function editAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $vat = $request->getPost();
            $this->table->saveVat($vat);
            return $this->redirect()->toUrl("/vat");
        }
        $id = base64_decode($this->params()->fromRoute('id'));
        $checkId = $this->table->getVat($id);
        if(isset($checkId)){
            return new ViewModel([
                'vat' => $this->table->getVat($id)
            ]);
        }else{
            return $this->redirect()->toUrl("/vat");
        }
    }
    
    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $result = $this->table->deleteVat($params);
            $viewModel = new ViewModel([
                'result' => $result
            ]);
            return $viewModel->setTerminal(true);
        }
    }
}