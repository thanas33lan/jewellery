<?php
namespace Products\Model;

use RuntimeException;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Application\Service\CommonService;
use Zend\Db\TableGateway\TableGatewayInterface;

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

    public function fetchAll($parameters)
    {
        // return $this->tableGateway->select();
        $aColumns = ['products_type_name','products_name','products_wastage','products_rate','products_vat','products_qty'];
        $orderColumns = ['products_type_name','products_name','products_wastage','products_rate','products_vat','products_qty'];
        
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
        $select->join(['pt'=>'products_type'],'p.products_type_id = pt.products_type_id',['products_type_name']);
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
            $row[] = ucfirst($aRow->products_type_name);
            $row[] = ucwords($aRow->products_name);
            $row[] = $aRow->products_wastage;
            $row[] = $aRow->products_rate;
            $row[] = $aRow->products_vat;
            $row[] = $aRow->products_qty;
            $row[] = 
                    '<div class="btn-group btn-group-sm" role="group" aria-label="Small Horizontal Primary">
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