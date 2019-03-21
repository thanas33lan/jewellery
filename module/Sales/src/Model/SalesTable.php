<?php
namespace Sales\Model;

use RuntimeException;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Application\Service\CommonService;
use Zend\Db\TableGateway\TableGatewayInterface;
use Sales\Model\SalesVoucherDetailsTable;
use PHPExcel;

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
    
    public function getActiveUsers($search)
    {
        $usersQuery = new Select('user_details');
        $usersQuery->where(['user_status'=>'active']);
        $usersQuery->where("name LIKE '%" .$search. "%'");
        $row = $this->tableGateway->selectWith($usersQuery);
        $result['search'] = $search;
        $result['row'] = $row;
        return $result;
    }
    
    public function getAllUsers()
    {
        $usersQuery = new Select('user_details');
        $usersQuery->where(['user_status'=>'active']);
        return $this->tableGateway->selectWith($usersQuery);
    }
    
    public function getLastSalesId()
    {
        $salesQuery = new Select('sales_details');
        $salesQuery->order('sales_id DESC');
        return $this->tableGateway->selectWith($salesQuery)->current();
    }
    
    public function getAllAccounts()
    {
        $salesQuery = new Select('account_type');
        $salesQuery->where(['account_type_status'=>'active']);
        return $this->tableGateway->selectWith($salesQuery);
    }
    
    public function getAllClientAccounts()
    {
        $salesQuery = new Select('accounts');
        $salesQuery->where(['account_status'=>'active']);
        return $this->tableGateway->selectWith($salesQuery);
    }

    public function exportReports($params)
    {
        if(isset($params->type) && trim($params->type) != '')
        {
            if($params->type == 'excel'){
                return $this->exportExcel();
            }else{
                
            }
        }
    }
    
    public function exportExcel()
    {
        try{
            $common = new CommonService();
            $queryContainer = new Container('query');
            $excel = new PHPExcel();
            $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array('memoryCacheSize' => '80MB');
            \PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $output = array();
            $sheet = $excel->getActiveSheet();
            
            if(isset($params['searchDate']) && trim($params['searchDate'])!=""){
                $start_date = '';
                $end_date = '';
                
                $sDate = explode("to",$params['searchDate']);
                if (isset($sDate[0]) && trim($sDate[0]) != "") {
                    $start_date = $common->dbDateFormat(trim($sDate[0]));
                }
                if (isset($sDate[1]) && trim($sDate[1]) != "") {
                    $end_date = $common->dbDateFormat(trim($sDate[1]));
                }
                $query = $query->where(array("s.invoice_date >='" . $start_date ."'", "s.invoice_date <='" . $end_date."'"));
            }
            
            $sResult = $this->tableGateway->selectWith($queryContainer->salesQuery);
            
            if(count($sResult) > 0) {
                foreach($sResult as $aRow) {
                    $row = array();
                    
                    $row[] = $aRow->sales_voucher_no;
                    $row[] = $common->humanDateFormat($aRow->sales_voucher_date);
                    $row[] = ucwords(str_replace("-"," ",$aRow->sales_voucher_account));
                    $row[] = ucwords($aRow->account_type_name);
                    $row[] = ucwords($aRow->sales_remarks);
                    $row[] = $aRow->sales_grand_total;
                    
                    $output[] = $row;
                }
            }
            $styleArray = array(
                'font' => array(
                    'bold' => true,
                    'size'=>12,
                ),
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
                'borders' => array(
                    'outline' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    ),
                )
            );
            $alignArray = array(
                'font' => array(
                    'bold' => true,
                    'size'=>12,
                ),
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PHPExcel_Style_Alignment::VERTICAL_CENTER,
                ),
            );
            
           $borderStyle = array(
                'alignment' => array(
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
                'borders' => array(
                    'outline' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    ),
                )
            );
          
            $sheet->mergeCells('A1:B1');
            $sheet->mergeCells('A2:B2');
            
            
            $sheet->setCellValue('A1', html_entity_decode('Jewellery Shop Sales Report', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
           
            $sheet->setCellValue('A3', html_entity_decode('VOUCHER NUMBER', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('B3', html_entity_decode('DATE', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('C3', html_entity_decode('ACCOUNT', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('D3', html_entity_decode('SALES ACCOUNT', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('E3', html_entity_decode('REMARKS', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('F3', html_entity_decode('GRAND TOTAL', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            
            $sheet->mergeCells('A2:B2');
           
            $sheet->getStyle('A1:B1')->getFont()->setBold(TRUE)->setSize(16);
            $sheet->getStyle('A2:B2')->getFont()->setBold(TRUE)->setSize(11);
            $sheet->getStyle('C2:D2')->getFont()->setBold(TRUE)->setSize(11);
            $sheet->getStyle('E2')->getFont()->setBold(TRUE)->setSize(11);
            
            $sheet->getStyle('A3')->applyFromArray($styleArray);
            $sheet->getStyle('B3')->applyFromArray($styleArray);
            $sheet->getStyle('C3')->applyFromArray($styleArray);
            $sheet->getStyle('D3')->applyFromArray($styleArray);
            $sheet->getStyle('E3')->applyFromArray($styleArray);
            $sheet->getStyle('F3')->applyFromArray($styleArray);
            
            foreach ($output as $rowNo => $rowData) {
                $colNo = 0;
                foreach ($rowData as $field => $value) {
                    if (!isset($value)) {
                        $value = "";
                    }
                    if (is_numeric($value)) {
                        $sheet->getCellByColumnAndRow($colNo, $rowNo + 4)->setValueExplicit(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_NUMERIC);
                    } else {
                        $sheet->getCellByColumnAndRow($colNo, $rowNo + 4)->setValueExplicit(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
                    }
                    $rRowCount = $rowNo + 4;
                    $cellName = $sheet->getCellByColumnAndRow($colNo, $rowNo + 4)->getColumn();
                    $sheet->getStyle($cellName . $rRowCount)->applyFromArray($borderStyle);
                    $sheet->getDefaultRowDimension()->setRowHeight(18);
                    $sheet->getColumnDimensionByColumn($colNo)->setWidth(20);
                    $sheet->getStyleByColumnAndRow($colNo, $rowNo + 5)->getAlignment()->setWrapText(true);
                    $colNo++;
                }
            }
            
            $writer = \PHPExcel_IOFactory::createWriter($excel, 'Excel5');
            $filename = 'Products-Report' . date('d-M-Y-H-i-s') . '.xls';
            $writer->save(TEMP_UPLOAD_PATH . DIRECTORY_SEPARATOR . $filename);
            return $filename;
        }
        catch (Exception $exc) {
            return "";
            error_log("Generate-Products-Report--" . $exc->getMessage());
            error_log($exc->getTraceAsString());
        }
    }

    public function fetchAll($parameters)
    {
        $queryContainer = new Container('query');
        $aColumns = ['sales_voucher_no','sales_voucher_date','sales_voucher_account','account_type_name','sales_remarks','sales_grand_total'];
        $orderColumns = ['sales_voucher_no','sales_voucher_date','sales_voucher_account','account_type_name','sales_remarks','sales_grand_total'];
        
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
        
        $common=new CommonService();
        if(isset($parameters['searchDate']) && $parameters['searchDate']!=''){
            $sDate = explode("to", $parameters['searchDate']);
            if (isset($sDate[0]) && trim($sDate[0]) != "") {
                $start_date = $common->dbDateFormat(trim($sDate[0]));
            }
            if (isset($sDate[1]) && trim($sDate[1]) != "") {
                $end_date = $common->dbDateFormat(trim($sDate[1]));
            }
        }
        if (isset($start_date) && trim($start_date) != "" && trim($end_date) != "") {
			$select->where(["s.sales_voucher_date >='" . $start_date ."'", "s.sales_voucher_date <='" . $end_date."'"]);
		} else if (isset($start_date) && trim($start_date) != "") {
			$select->where(["s.sales_voucher_date='" . $start_date."'"]);
		}	
        
        if(isset($parameters['searchSalesAccount']) && $parameters['searchSalesAccount']!='')
		{
			$select->where(['s.sales_voucher_sales_account'=>$parameters['searchSalesAccount']]);
		}
        
        if(isset($parameters['searchAccount']) && $parameters['searchAccount']!='')
		{
			$select->where(['s.sales_voucher_account'=>$parameters['searchAccount']]);
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
        $queryContainer->salesQuery = $select;
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
            $row[] = ucwords($aRow->sales_remarks);
            $row[] = $aRow->sales_grand_total;
            $row[] ='<div class="btn-group btn-group-sm" role="group" aria-label="Small Horizontal Primary">
                        <a class="btn btn-sm btn-hero-dark" onclick="showModal(\'/sales/pay/' .base64_encode($aRow->sales_id) . '\',880,540);" href="javascript:void(0);"><i class="fab fa-amazon-pay"></i></a>
                        <a class="btn btn-sm btn-primary" href="/sales/edit/' . base64_encode($aRow->sales_id) . '"><i class="si si-pencil"></i> Edit</a>
                        <a class="btn btn-sm btn-danger" onclick="deleteSales(' . $aRow->sales_id . ');" href="javascript:void(0);"><i class="far fa-trash-alt"></i> Delete</a>
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
            'sales_grand_total' => $sales->sales_grand_total,
            'sales_user_id' => $sales->sales_user_id,
            'sales_recived' => $sales->sales_recived,
            'sales_balance' => $sales->sales_balance,
            'sales_received_type' => $sales->sales_received_type,
            'sales_remarks' => $sales->sales_remarks
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
    
    public function saveEmiDetails($salesEmi)
    {
        $common = new CommonService();
        $data = [
            'sales_grand_total' => $salesEmi->sales_grand_total,
            'sales_recived' => $salesEmi->sales_recived,
            'sales_balance' => $salesEmi->sales_balance,
            'sales_received_type' => $salesEmi->sales_received_type,
        ];
        $salesId = (int) $salesEmi->sales_id;
        return $this->tableGateway->update($data, ['sales_id' => $salesId]);
    }
    
    public function deleteSales($params)
    {
        return $this->tableGateway->delete(['sales_id' => (int) $params->sales_id]);
    }
}