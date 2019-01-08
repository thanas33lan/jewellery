<?php
namespace GoldChit\Model;

use RuntimeException;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Application\Service\CommonService;
use Zend\Db\TableGateway\TableGatewayInterface;

class GoldChitTable
{
    private $tableGateway;

    function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($parameters)
    {
        $aColumns = ['goldchit_number','goldchit_name','goldchit_remarks','goldchit_amount','goldchit_bonus','goldchit_no_of_month','goldchit_total_amount','goldchit_scheme_name'];
        $orderColumns = ['goldchit_number','goldchit_name','goldchit_remarks','goldchit_amount','goldchit_bonus','goldchit_no_of_month','goldchit_total_amount','goldchit_scheme_name'];
        
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
        $select = new Select('goldchit_details');
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
        $output = [
                "sEcho" => intval($parameters['sEcho']),
                "iTotalRecords" => count($rowTotal),
                "iTotalDisplayRecords" => $iFilteredTotal,
                "aaData" => array()
        ];
        foreach ($resultSet as $aRow) {
            $row = array();
            $row[] = $aRow->goldchit_number;
            $row[] = ucwords($aRow->goldchit_name);
            $row[] = ucwords($aRow->goldchit_remarks);
            $row[] = $aRow->goldchit_amount;
            $row[] = $aRow->goldchit_bonus;
            $row[] = $aRow->goldchit_no_of_month;
            $row[] = $aRow->goldchit_total_amount;
            $row[] = ucwords($aRow->goldchit_scheme_name);
            $row[] ='<div class="btn-group btn-group-sm" role="group" aria-label="Small Horizontal Primary">
                        <a class="btn btn-primary" href="/gold-chit/edit/' . base64_encode($aRow->goldchit_id) . '"><i class="si si-pencil"></i> Edit</a>
                        <a class="btn btn-danger" onclick="deleteGoldChit(' . $aRow->goldchit_id . ');" href="javascript:void(0);"><i class="far fa-trash-alt"></i> Delete</a>
                    </div>';
            $output['aaData'][] = $row;
        }

        return $output;
    }
    
    public function getGoldChit($id)
    {
        $goldchitId = (int) $id;
        $rowset = $this->tableGateway->select(['goldchit_id' => $goldchitId]);
        $row = $rowset->current();
        
        $alertContainer = new Container('alert');
        if (! $row) {
            $alertContainer->alertMsg = 'GoldChit not found';
            return 0;
        }
        return $row;
    }

    public function saveGoldChit($goldchit)
    {
        $common = new CommonService();
        $data = [
            'goldchit_number'  => $goldchit->goldchit_number,
            'goldchit_date'  => $common->dbDateFormat($goldchit->goldchit_date),
            'goldchit_name'  => $goldchit->goldchit_name,
            'goldchit_address'  => $goldchit->goldchit_address,
            'goldchit_area'  => $goldchit->goldchit_area,
            'goldchit_city'  => $goldchit->goldchit_city,
            'goldchit_phone'  => $goldchit->goldchit_phone,
            'goldchit_pincode'  => $goldchit->goldchit_pincode,
            'goldchit_remarks' => $goldchit->goldchit_remarks,
            'goldchit_amount' => $goldchit->goldchit_amount,
            'goldchit_bonus' => $goldchit->goldchit_bonus,
            'goldchit_no_of_month' => $goldchit->goldchit_no_of_month,
            'goldchit_total_amount' => $goldchit->goldchit_total_amount,
            'goldchit_scheme_name' => $goldchit->goldchit_scheme_name
        ];
        
        $goldchitId = (int) $goldchit->goldchit_id;
        $alertContainer = new Container('alert');
        if ($goldchitId === 0) {
            $inserted = $this->tableGateway->insert($data);
            if($inserted > 0){
                $alertContainer->alertMsg = 'GoldChit details added successfully';
            }
            return;
        }
        $updated = $this->tableGateway->update($data, ['goldchit_id' => $goldchitId]);

        if($updated > 0){
            $alertContainer->alertMsg = 'GoldChit details updated successfully';
        }
    }

    public function deleteGoldChit($params)
    {
        return $this->tableGateway->delete(['goldchit_id' => (int) $params->goldchit_id]);
    }
}