<?php
namespace Accounts\Model;

use RuntimeException;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Application\Service\CommonService;
use Zend\Db\TableGateway\TableGatewayInterface;

class AccountsTable
{
    private $tableGateway;

    function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAllAccountType()
    {
        $select = new Select('account_type');
        return $this->tableGateway->selectWith($select);
    }

    public function fetchAll($parameters)
    {
        // return $this->tableGateway->select();
        $aColumns = ['account_type_name','account_name_tamil','account_city_tamil','account_area','account_mobile','account_status'];
        $orderColumns = ['account_type_name','account_name_tamil','account_city_tamil','account_area','account_mobile','account_status'];
        
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
        $select = new Select(['a'=>'accounts']);
        $select->join(['at'=>'account_type'],'a.account_type_id = at.account_type_id',['account_type_id','account_type_name']);
        // \Zend\Debug\Debug::dump($select);die;
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
            $row[] = ucfirst($aRow->account_type_name);
            $row[] = ucwords($aRow->account_name_tamil);
            $row[] = ucwords($aRow->account_city);
            $row[] = ucwords($aRow->account_area);
            $row[] = $aRow->account_mobile;
            $row[] = ucwords($aRow->account_status);
            $row[] = 
                    '<div class="btn-group btn-group-sm" role="group" aria-label="Small Horizontal Primary">
                        <a class="btn btn-primary" href="/accounts/edit/' . base64_encode($aRow->account_id) . '"><i class="si si-pencil"></i> Edit</a>
                        <a class="btn btn-danger" onclick="deleteAccount(' . $aRow->account_id . ');" href="javascript:void(0);"><i class="far fa-trash-alt"></i> Delete</a>
                    </div>';
            $output['aaData'][] = $row;
        }

        return $output;
    }
    
    public function getAccounts($id)
    {
        $accountId = (int) $id;
        $rowset = $this->tableGateway->select(['account_id' => $accountId]);
        $row = $rowset->current();
        
        $alertContainer = new Container('alert');
        if (! $row) {
            $alertContainer->alertMsg = 'User not found';
            return 0;
        }

        return $row;
    }

    public function saveAccounts($accounts)
    {
        $data = [
            'account_type' => $accounts->type,
            'account_address'  => $accounts->address,
            'account_area'  => $accounts->area,
            'account_city'  => $accounts->city,
            'account_pincode'  => $accounts->pincode,
            'account_mobile'  => $accounts->mobile,
            'account_email'  => $accounts->email,
            'account_name_tamil'  => $accounts->name,
            'account_city_tamil'  => $accounts->cityTamil,
            'account_status'  => $accounts->accountStatus,
        ];

        $accountId = (int) $accounts->accountId;
        $alertContainer = new Container('alert');

        if ($accountId === 0) {
            $inserted = $this->tableGateway->insert($data);
            if($inserted > 0){
                $alertContainer->alertMsg = 'Products details added successfully';
            }
            return;
        }

        $updated = $this->tableGateway->update($data, ['account_id' => $accountId]);
        if($updated > 0){
            $alertContainer->alertMsg = 'User details updated successfully';
        }
    }

    public function deleteAccounts($params)
    {
        return $this->tableGateway->delete(['account_id' => (int) $params->account_id]);
    }
}