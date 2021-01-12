<?php
namespace Accounts\Model;

use Model\AccountsGeneral;
use RuntimeException;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Application\Service\CommonService;
use Zend\Db\TableGateway\TableGatewayInterface;

class AccountsGeneralTable
{
    private $tableGateway3;

    function __construct(TableGatewayInterface $tableGateway3)
    {
        $this->tableGateway = $tableGateway3;
    }

    public function fetchAll($parameters)
    {
        $aColumns = ['accounts_general_connection_date','accounts_general_connection_type','accounts_general_purchase_disc','accounts_general_sales_disc','accounts_general_salesman','accounts_general_salesman_mobile','accounts_general_contact_person'];
        $orderColumns = ['accounts_general_connection_date','accounts_general_connection_type','accounts_general_purchase_disc','accounts_general_sales_disc','accounts_general_salesman','accounts_general_salesman_mobile','accounts_general_contact_person'];
        
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
        $select = new Select('accounts_general');

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
            $row[] = date('d-M-Y', strtotime($aRow->accounts_general_connection_date));
            $row[] = ucwords($aRow->accounts_general_connection_type);
            $row[] = $aRow->accounts_general_purchase_disc;
            $row[] = $aRow->accounts_general_sales_disc;
            $row[] = ucwords($aRow->accounts_general_salesman);
            $row[] = $aRow->accounts_general_salesman_mobile;
            $row[] = ucwords($aRow->accounts_general_contact_person);
            $row[] ='<div class="btn-group btn-group-sm" role="group" aria-label="Small Horizontal Primary">
                        <a class="btn btn-primary" href="/accounts/edit/' . base64_encode($aRow->accounts_id) . '"><i class="si si-pencil"></i> Edit</a>
                        <a class="btn btn-danger" onclick="deleteGeneralAccount(' . $aRow->accounts_id . ');" href="javascript:void(0);"><i class="far fa-trash-alt"></i> Delete</a>
                    </div>';
            $output['aaData'][] = $row;
        }

        return $output;
    }
    
    public function getAccounts($id)
    {
        $accountGeneralId = (int) $id;
        $rowset = $this->tableGateway->select(['accounts_id' => $accountGeneralId]);
        $row = $rowset->current();
        
        if (! $row) {
            return 0;
        }

        return $row;
    }

    public function saveAccounts($accounts,$accountsLastInsertedId)
    {
        $common = new CommonService();
        $data = [
            'accounts_general_connection_date'  => $common->dbDateFormat($accounts->accounts_general_connection_date),
            'accounts_general_connection_type'  => $accounts->accounts_general_connection_type,
            'accounts_general_deposite_amount'  => $accounts->accounts_general_deposite_amount,
            'accounts_general_rent_amount'  => $accounts->accounts_general_rent_amount,
            'accounts_general_category'  => $accounts->accounts_general_category,
            'accounts_general_work_period'  => $accounts->accounts_general_work_period,
            'accounts_general_reference'  => $accounts->accounts_general_reference,
            'accounts_general_sender_id'  => $accounts->accounts_general_sender_id,
            'accounts_general_brand'  => $accounts->accounts_general_brand,
            'accounts_general_purchase_disc'  => $accounts->accounts_general_purchase_disc,
            'accounts_general_sales_disc'  => $accounts->accounts_general_sales_disc,
            'accounts_general_wage'  => $accounts->accounts_general_wage,
            'accounts_general_price_list'  => $accounts->accounts_general_price_list,
            'accounts_general_overtime'  => $accounts->accounts_general_overtime,
            'accounts_general_receipt_charges'  => $accounts->accounts_general_receipt_charges,
            'accounts_general_salesman'  => $accounts->accounts_general_salesman,
            'accounts_general_salesman_mobile'  => $accounts->accounts_general_salesman_mobile,
            'accounts_general_contact_person'  => $accounts->accounts_general_contact_person,
            'accounts_general_permanent'  => $accounts->accounts_general_permanent,
            'accounts_general_warning_outstanding'  => $accounts->accounts_general_warning_outstanding,
            'accounts_general_invoice_outstanding'  => $accounts->accounts_general_invoice_outstanding,
        ];
        if($accountsLastInsertedId > 0){
            $data['accounts_id'] = $accountsLastInsertedId;
        }else{
            $data['accounts_id'] = $accounts->accounts_id;
        }
        $accountGeneralId = (int) $accounts->accounts_general_id;
        if ($accountGeneralId === 0) {
            return $this->tableGateway->insert($data);
        }

        return $this->tableGateway->update($data, ['accounts_general_id' => $accountGeneralId]);
    }

    public function deleteAccounts($params)
    {
        return $this->tableGateway->delete(['accounts_id' => (int) $params->accounts_id]);
    }
}