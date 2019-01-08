<?php
namespace Sales\Model;

use RuntimeException;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Application\Service\CommonService;
use Zend\Db\TableGateway\TableGatewayInterface;
use Sales\Model\SalesVoucherDetailsTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\ResultSet\HydratingResultSet;

class SalesTable 
{
    private $tableGateway;

    function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getAllProducts()
    {
        $productsQuery = new Select('products');
        $productsQuery->where(['products_status'=>'active']);
        return $this->tableGateway->selectWith($productsQuery);
    }
    
    public function getLastSalesId()
    {
        $productsQuery = new Select('sales_details');
        $productsQuery->order('sales_id DESC');
        return $this->tableGateway->selectWith($productsQuery)->current();
    }
    
    public function getAllAccounts()
    {
        $productsQuery = new Select('account_type');
        $productsQuery->where(['account_type_status'=>'active']);
        return $this->tableGateway->selectWith($productsQuery);
    }

    public function fetchAll($parameters)
    {
        $aColumns = ['sales_voucher_no','sales_voucher_date','sales_voucher_account','account_type_name','sales_voucher_remarks','sales_grand_total'];
        $orderColumns = ['sales_voucher_no','sales_voucher_date','sales_voucher_account','account_type_name','sales_voucher_remarks','sales_grand_total'];
        
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
        $select = new Select([ 's' => 'sales_details' ]);
        $select->join(['at' => 'account_type'], 's.sales_voucher_sales_account = at.account_type_id', ['account_type_name']);
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
        $common = new CommonService();
        foreach ($resultSet as $aRow) {
            $row = array();
            $row[] = $aRow->sales_voucher_no;
            $row[] = $common->humanDateFormat($aRow->sales_voucher_date);
            $row[] = ucwords(str_replace("-"," ",$aRow->sales_voucher_account));
            $row[] = ucwords($aRow->account_type_name);
            $row[] = ucwords($aRow->sales_voucher_remarks);
            $row[] = $aRow->sales_grand_total;
            $row[] ='<div class="btn-group btn-group-sm" role="group" aria-label="Small Horizontal Primary">
                        <a class="btn btn-primary" href="/sales/edit/' . base64_encode($aRow->sales_id) . '"><i class="si si-pencil"></i> Edit</a>
                        <a class="btn btn-danger" onclick="deleteSales(' . $aRow->sales_id . ');" href="javascript:void(0);"><i class="far fa-trash-alt"></i> Delete</a>
                    </div>';
            $output['aaData'][] = $row;
        }

        return $output;
    }
    
    public function getSales($id)
    {
        $salesId = (int) $id;
        $rowset = $this->tableGateway->select(['sales_id' => $salesId]);
        $row = $rowset->current();
        
        $alertContainer = new Container('alert');
        if (! $row) {
            $alertContainer->alertMsg = 'Sales not found';
            return 0;
        }

        return $row;
    }

    public function saveSales($sales)
    {
        $common = new CommonService();
        $data = [
            'sales_id' => $sales->sales_id,
            'sales_voucher_no' => $sales->sales_voucher_no,
            'sales_voucher_date' => $common->dbDateFormat($sales->sales_voucher_date),
            'sales_voucher_account' => $sales->sales_voucher_account,
            'sales_voucher_sales_account' => $sales->sales_voucher_sales_account,
            'sales_voucher_remarks' => $sales->sales_voucher_remarks,
            'sales_grand_total' => $sales->sales_grand_total
        ];
        $salesId = (int) $sales->sales_id;
        $alertContainer = new Container('alert');
        
        if ($salesId === 0) {
            $inserted = $this->tableGateway->insert($data);
            $lastInsertedId = $this->tableGateway->lastInsertValue;
            if($inserted > 0){
                $alertContainer->alertMsg = 'Sales details added successfully';
            }
            return $lastInsertedId;
        }
        $updated = $this->tableGateway->update($data, ['sales_id' => $salesId]);

        if($updated > 0){
            $alertContainer->alertMsg = 'Sales details updated successfully';
        }
    }

    public function deleteSales($params)
    {
        return $this->tableGateway->delete(['sales_id' => (int) $params->sales_id]);
    }
}