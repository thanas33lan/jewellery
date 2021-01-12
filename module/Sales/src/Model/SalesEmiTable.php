<?php
namespace Sales\Model;

use RuntimeException;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Application\Service\CommonService;
use Zend\Db\TableGateway\TableGatewayInterface;
use Sales\Model\SalesVoucherDetailsTable;
use PHPExcel;

class SalesEmiTable 
{
    private $tableGateway;

    function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getSalesEmi($id)
    {
        $salesId = (int) $id;
        $rowset = $this->tableGateway->select(['sales_id' => $salesId]);
        $row = $rowset->toArray();
        
        $alertContainer = new Container('alert');
        if (! $row) {
            $alertContainer->alertMsg = 'Sales not found';
            return 0;
        }
        return $row;
    }

    public function saveEmiDetails($salesEmi,$lastId)
    {
        $common = new CommonService();
        $oldAmt = $salesEmi->sales_recived_old;
        $newAmt = $salesEmi->sales_recived;
        $data = [
            'sales_emi_date' => $common->getDate(),
            'sales_emi_type' => $salesEmi->sales_received_type,
            'sales_emi_amount' => abs($oldAmt - $newAmt),
            'sales_emi_remarks' => !empty($salesEmi->sales_emi_remarks) ? $salesEmi->sales_emi_remarks : $salesEmi->sales_remarks
        ];
        if(isset($salesEmi->sales_id) && trim($salesEmi->sales_id) != ''){
            $data['sales_id'] = $salesEmi->sales_id;
        }else{
            $data['sales_id'] = $lastId;
        }
        $salesId = (int) !empty($salesEmi->sales_emi_id) ? $salesEmi->sales_emi_id : 0;
        $alertContainer = new Container('alert');
        
        if ($salesId === 0) {
            $inserted = $this->tableGateway->insert($data);
            $lastInsertedId = $this->tableGateway->lastInsertValue;
            if($inserted > 0){
                $alertContainer->alertMsg = 'Sales details added successfully';
            }
            return $lastInsertedId;
        }
        $updated = $this->tableGateway->update($data, ['sales_emi_id' => $salesId]);
        if($updated > 0){
            $alertContainer->alertMsg = 'Sales details updated successfully';
        }
        return $updated;
    }

    public function deleteSalesEmi($params)
    {
        return $this->tableGateway->delete(['sales_id' => (int) $params->sales_id]);
    }

    public function fetchAllEmiById($parameters)
    {
        // \Zend\Debug\Debug::dump($parameters);die;
        $aColumns = ['sales_voucher_no','sales_emi_date','sales_emi_type','sales_emi_amount','sales_emi_remarks'];
        $orderColumns = ['sales_voucher_no','sales_emi_date','sales_emi_type','sales_emi_amount','sales_emi_remarks'];
        
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
        $select = new Select([ 'se' => 'sales_emi' ]);
        $select->join(['sd' => 'sales_details'], 'se.sales_id = sd.sales_id', ['sales_voucher_no']);
        if(isset($parameters['sales_id']) && $parameters['sales_id']!='')
        {
            $select->where(['se.sales_id'=> $parameters['sales_id']]);
        }

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
        $selectCount = new Select([ 'se' => 'sales_emi' ]);
        $selectCount->join(['sd' => 'sales_details'], 'se.sales_id = sd.sales_id', ['sales_voucher_no']);
        $selectCount->reset('limit');
        $selectCount->reset('offset');
        $rowTotal = $this->tableGateway->selectWith($selectCount);
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
            $row[] = $common->humanDateFormat($aRow->sales_emi_date);
            $row[] = ucwords(str_replace("-"," ",$aRow->sales_emi_type));
            $row[] = $aRow->sales_emi_amount;
            $row[] = $aRow->sales_emi_remarks;
           
            $output['aaData'][] = $row;
        }

        return $output;
    }
}