<?php
namespace Accounts\Model;

use Model\AccountsRegister;
use RuntimeException;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Application\Service\CommonService;
use Zend\Db\TableGateway\TableGatewayInterface;

class AccountsRegisterTable
{
    private $tableGateway2;

    function __construct(TableGatewayInterface $tableGateway2)
    {
        $this->tableGateway = $tableGateway2;
    }

    public function fetchAll($parameters)
    {
        // return $this->tableGateway->select();
        $aColumns = ['accounts_register_tin','accounts_register_cst','accounts_register_code','accounts_register_gstin','accounts_register_delivery_name','accounts_register_address','accounts_register_city'];
        $orderColumns = ['accounts_register_tin','accounts_register_cst','accounts_register_code','accounts_register_gstin','accounts_register_delivery_name','accounts_register_address','accounts_register_city'];
        
        /* Paging */
        $sLimit = "";
        if (isset($parameters['iDisplayStart']) && $parameters['iDisplayLength'] != '-1') {
            $sOffset = $parameters['iDisplayStart'];
            $sLimit = $parameters['iDisplayLength'];
        }
        
        /* Ordering */
        $sOrder = "";
        if (isset($parameters['iSortCol_0'])) {
            for ($i = 0; $i < intval($parameters['iSortingCols']); $i++) {
                if ($parameters['bSortable_' . intval($parameters['iSortCol_' . $i])] == "true") {
                    $sOrder .= $aColumns[intval($parameters['iSortCol_' . $i])] . " " . ( $parameters['sSortDir_' . $i] ) . ",";
                }
            }
            $sOrder = substr_replace($sOrder, "", -1);
        }
        
        /*
        * Filtering
        */
        
        $sWhere = "";
        if (isset($parameters['sSearch']) && $parameters['sSearch'] != "") {
            $searchArray = explode(" ", $parameters['sSearch']);
            $sWhereSub = "";
            foreach ($searchArray as $search) {
                if ($sWhereSub == "") {
                    $sWhereSub .= "(";
                } else {
                    $sWhereSub .= " AND (";
                }
                $colSize = count($aColumns);
                
                for ($i = 0; $i < $colSize; $i++) {
                    if ($i < $colSize - 1) {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' OR ";
                    } else {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' ";
                    }
                }
                $sWhereSub .= ")";
            }
            $sWhere .= $sWhereSub;
        }

        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($parameters['bSearchable_' . $i]) && $parameters['bSearchable_' . $i] == "true" && $parameters['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere .= $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                } else {
                    $sWhere .= " AND " . $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                }
            }
        }
        
        /*
        * Get data to display
        */
        $select = new Select('accounts_register');
        if (isset($sWhere) && $sWhere != "") {
                $select->where($sWhere);
        }
        
        if (isset($sOrder) && $sOrder != "") {
                $select->order($sOrder);
        }
        
        if (isset($sLimit) && isset($sOffset)) {
                $select->limit($sLimit);
                $select->offset($sOffset);
        }
        $resultSet = $this->tableGateway->selectWith($select);
        /* Data set length after filtering */
        $select->reset('limit');
        $select->reset('offset');
        $rowTotal = $this->tableGateway->selectWith($select);
        $iFilteredTotal = count($rowTotal);
        $output = array(
                "sEcho" => intval($parameters['sEcho']),
                "iTotalRecords" => count($rowTotal),
                "iTotalDisplayRecords" => $iFilteredTotal,
                "aaData" => array()
        );
        foreach ($resultSet as $aRow) {
            $row = array();
            $row[] = $aRow->accounts_register_tin;
            $row[] = $aRow->accounts_register_cst;
            $row[] = $aRow->accounts_register_code;
            $row[] = $aRow->accounts_register_gstin;
            $row[] = ucfirst($aRow->accounts_register_delivery_name);
            $row[] = $aRow->accounts_register_address;
            $row[] = $aRow->accounts_register_city;
            $row[] ='<div class="btn-group btn-group-sm" role="group" aria-label="Small Horizontal Primary">
                        <a class="btn btn-primary" href="/accounts/edit/' . base64_encode($aRow->accounts_id) . '"><i class="si si-pencil"></i> Edit</a>
                        <a class="btn btn-danger" onclick="deleteRegisterAccount(' . $aRow->accounts_id . ');" href="javascript:void(0);"><i class="far fa-trash-alt"></i> Delete</a>
                    </div>';
            $output['aaData'][] = $row;
        }

        return $output;
    }
    
    public function getAccounts($id)
    {
        $accountRegisterId = (int) $id;
        $rowset = $this->tableGateway->select(['accounts_id' => $accountRegisterId]);
        $row = $rowset->current();
        
        if (! $row) {
            return 0;
        }

        return $row;
    }

    public function saveAccounts($accounts,$accountsLastInsertedId)
    {
        $data = [
            'accounts_register_tin'             => $accounts->accounts_register_tin,
            'accounts_register_cst'             => $accounts->accounts_register_cst,
            'accounts_register_licence'         => $accounts->accounts_register_licence,
            'accounts_register_code'            => $accounts->accounts_register_code,
            'accounts_register_gstin'           => $accounts->accounts_register_gstin,
            'accounts_register_delivery_name'   => $accounts->accounts_register_delivery_name,
            'accounts_register_address'         => $accounts->accounts_register_address,
            'accounts_register_area'            => $accounts->accounts_register_area,
            'accounts_register_city'            => $accounts->accounts_register_city
        ];
        if($accountsLastInsertedId > 0){
            $data['accounts_id'] = $accountsLastInsertedId;
        }else{
            $data['accounts_id'] = $accounts->accounts_id;
        }
        $accountRegisterId = (int) $accounts->accounts_register_id;
        if ($accountRegisterId === 0) {
            return $this->tableGateway->insert($data);
        }

        return $this->tableGateway->update($data, ['accounts_register_id' => $accountRegisterId]);
    }

    public function deleteAccounts($params)
    {
        return $this->tableGateway->delete(['accounts_id' => (int) $params->accounts_id]);
    }
}