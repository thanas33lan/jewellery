<?php
namespace Vat\Model;

use RuntimeException;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Application\Service\CommonService;
use Zend\Db\TableGateway\TableGatewayInterface;

class VatTable
{
    private $tableGateway;

    function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($parameters)
    {
        $aColumns = ['vat_slab','vat_rate','vat_account','vat_status'];
        $orderColumns = ['vat_slab','vat_rate','vat_account','vat_status'];
        
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
        $select = new Select('vat_details');
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
            $row[] = str_replace("-"," ",$aRow->vat_slab);
            $row[] = $aRow->vat_rate;
            $row[] = ucwords(str_replace("-"," ",$aRow->vat_account));
            $row[] = ucwords($aRow->vat_status);
            $row[] ='<div class="btn-group btn-group-sm" role="group" aria-label="Small Horizontal Primary">
                        <a class="btn btn-primary" href="/vat/edit/' . base64_encode($aRow->vat_id) . '"><i class="si si-pencil"></i> Edit</a>
                        <a class="btn btn-danger" onclick="deleteVat(' . $aRow->vat_id . ');" href="javascript:void(0);"><i class="far fa-trash-alt"></i> Delete</a>
                    </div>';
            $output['aaData'][] = $row;
        }

        return $output;
    }
    
    public function getVat($id)
    {
        $vatId = (int) $id;
        $rowset = $this->tableGateway->select(['vat_id' => $vatId]);
        $row = $rowset->current();
        
        $alertContainer = new Container('alert');
        if (! $row) {
            $alertContainer->alertMsg = 'Vat not found';
            return 0;
        }

        return $row;
    }

    public function saveVat($vat)
    {
        $data = [
            'vat_slab'  => $vat->vat_slab,
            'vat_rate'  => $vat->vat_rate,
            'vat_account'  => $vat->vat_account,
            'vat_status' => $vat->vat_status
        ];
        $vatId = (int) $vat->vat_id;
        $alertContainer = new Container('alert');
        
        if ($vatId === 0) {
            $inserted = $this->tableGateway->insert($data);
            if($inserted > 0){
                $alertContainer->alertMsg = 'Vat details added successfully';
            }
            return;
        }
        $updated = $this->tableGateway->update($data, ['vat_id' => $vatId]);

        if($updated > 0){
            $alertContainer->alertMsg = 'Vat details updated successfully';
        }
    }

    public function deleteVat($params)
    {
        return $this->tableGateway->delete(['vat_id' => (int) $params->vat_id]);
    }
}