<?php
namespace Products\Model;

use RuntimeException;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Application\Service\CommonService;
use Zend\Db\TableGateway\TableGatewayInterface;
use PHPExcel;

class ProductsTable
{
    private $tableGateway;

    function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAllProductsType()
    {
        $select = new Select('products_type');
        return $this->tableGateway->selectWith($select);
    }
    
    public function exportReports($params)
    {
        if(isset($params->type) && trim($params->type) != '')
        {
            if($params->type == 'excel'){
                return $this->exportExcel();
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
            
            $sResult = $this->tableGateway->selectWith($queryContainer->productQuery);
            
            if(count($sResult) > 0) {
                foreach($sResult as $aRow) {
                    $row = array();
                    
                    $row[] = $aRow->products_tag;
                    $row[] = ucfirst($aRow->products_type_name);
                    $row[] = ucwords($aRow->products_name);
                    $row[] = $aRow->products_wastage;
                    $row[] = $aRow->products_rate;
                    $row[] = $aRow->products_vat;
                    $row[] = $aRow->products_qty;
                    
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
            
            
            $sheet->setCellValue('A1', html_entity_decode('Jewellery Shop Products Report', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
           
            $sheet->setCellValue('A3', html_entity_decode('TAG', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('B3', html_entity_decode('TYPE NAME', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('C3', html_entity_decode('PRODUCT NAME', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('D3', html_entity_decode('WASTAGE', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('E3', html_entity_decode('RATE', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('F3', html_entity_decode('VAT', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            $sheet->setCellValue('G3', html_entity_decode('QTY', ENT_QUOTES, 'UTF-8'), \PHPExcel_Cell_DataType::TYPE_STRING);
            
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
            $sheet->getStyle('G3')->applyFromArray($styleArray);
            
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
        $aColumns = ['products_tag','products_type_name','products_name','products_wastage','products_rate','products_vat','products_qty'];
        
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
        $select = new Select(['p'=>'products']);
        $select->join(['pt'=>'products_type'],'p.products_type_id = pt.products_type_code',['products_type_name']);
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
        if(isset($parameters['productById']) && $parameters['productById']!='')
		{
			$select->where(['p.products_type_id'=>$parameters['productById']]);
		}
        if(isset($parameters['productByTag']) && $parameters['productByTag']!='')
		{
			$select->where(['p.products_tag'=>$parameters['productByTag']]);
		}
        $resultSet = $this->tableGateway->selectWith($select);
        /* Data set length after filtering */
        $select->reset('limit');
        $select->reset('offset');
        $queryContainer->productQuery = $select;
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
            $row[] = $aRow->products_tag;
            $row[] = ucfirst($aRow->products_type_name);
            $row[] = ucwords($aRow->products_name);
            $row[] = $aRow->products_wastage;
            $row[] = $aRow->products_rate;
            $row[] = $aRow->products_vat;
            $row[] = $aRow->products_qty;
            $row[] ='<div class="btn-group btn-group-sm" role="group" aria-label="Small Horizontal Primary">
                        <a class="btn btn-primary" href="/products/edit/' . base64_encode($aRow->products_id) . '"><i class="si si-pencil"></i> Edit</a>
                        <a class="btn btn-danger" onclick="deleteAccount(' . $aRow->products_id . ');" href="javascript:void(0);"><i class="far fa-trash-alt"></i> Delete</a>
                    </div>';
            $output['aaData'][] = $row;
        }

        return $output;
    }
    
    public function getProducts($id)
    {
        $productsId = (int) $id;
        $rowset = $this->tableGateway->select(['products_id' => $productsId]);
        $row = $rowset->current();
        
        $alertContainer = new Container('alert');
        if (! $row) {
            $alertContainer->alertMsg = 'Products not found';
            return 0;
        }

        return $row;
    }

    public function saveProducts($products)
    {
        $data = [
            'products_type_id' => $products->products_type_id,
            'products_tag'  => $products->products_tag,
            'products_name'  => $products->products_name,
            'products_short_name'  => $products->products_short_name,
            'products_wastage'  => $products->products_wastage,
            'products_charge'  => $products->products_charge,
            'products_rate'  => $products->products_rate,
            'products_description'  => $products->products_description,
            'products_addition_rate_g'  => $products->products_addition_rate_g,
            'products_vat'  => $products->products_vat,
            'products_vat_rate'  => $products->products_vat_rate,
            'products_qty'  => $products->products_qty,
            'products_status'  => $products->products_status,
        ];

        $productsId = (int) $products->products_id;
        $alertContainer = new Container('alert');

        if ($productsId === 0) {
            $inserted = $this->tableGateway->insert($data);
            if($inserted > 0){
                $alertContainer->alertMsg = 'Products details added successfully';
            }
            return;
        }
        $updated = $this->tableGateway->update($data, ['products_id' => $productsId]);
        if($updated > 0){
            $alertContainer->alertMsg = 'Products details updated successfully';
        }
    }

    public function deleteProducts($params)
    {
        return $this->tableGateway->delete(['products_id' => (int) $params->products_id]);
    }
}