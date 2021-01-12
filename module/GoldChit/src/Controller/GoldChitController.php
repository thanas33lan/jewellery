<?php
namespace GoldChit\Controller;

use GoldChit\Model\GoldChitTable;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;

class GoldChitController extends AbstractActionController
{
    private $table;

    public function __construct(GoldChitTable $table)
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
            $GoldChit = $request->getPost();
            $this->table->saveGoldChit($GoldChit);
            return $this->redirect()->toUrl("/gold-chit");
        }
        return new ViewModel();
    }
    
    public function editAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $GoldChit = $request->getPost();
            $this->table->saveGoldChit($GoldChit);
            return $this->redirect()->toUrl("/gold-chit");
        }
        $id = base64_decode($this->params()->fromRoute('id'));
        $checkId = $this->table->getGoldChit($id);
        if(isset($checkId)){
            return new ViewModel([
                'goldChit' => $this->table->getGoldChit($id)
            ]);
        }else{
            return $this->redirect()->toUrl("/gold-chit");
        }
    }
    
    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $params = $request->getPost();
            $result = $this->table->deleteGoldChit($params);
            $viewModel = new ViewModel(['result' => $result]);
            return $viewModel->setTerminal(true);
        }
    }
}