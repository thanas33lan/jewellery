<?php
namespace Sales\Model;

use RuntimeException;
use Zend\Db\Sql\Select;
use Zend\Session\Container;
use Application\Service\CommonService;
use Zend\Db\TableGateway\TableGatewayInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Driver\ResultInterface;

class SalesVoucherDetailsTable
{
    private $tableGateway3;

    function __construct(TableGatewayInterface $tableGateway3)
    {
        $this->tableGateway = $tableGateway3;
    }
    
    public function getSales($id)
    {
        $salesVoucherId = (int) $id;
        $rowset = $this->tableGateway->select(['sales_id' => $salesVoucherId]);
        $row = $rowset->getArrayObjectPrototype();
        
        $alertContainer = new Container('alert');
        if (! $row) {
            $alertContainer->alertMsg = 'Sales voucher not found';
            return 0;
        }
       
        return $rowset;
    }

    public function saveSales($sales,$lastId)
    {
        // \Zend\Debug\Debug::dump($sales);die;
        $lastInsertedId = 0;
        $n = count($sales->sales_voucher_products_id);
        for($i=0;$i<$n;$i++){
            $data = [
                'sales_id' => ($lastId >0)? $lastId:$sales->sales_id,
                'sales_voucher_products_id' => $sales->sales_voucher_products_id[$i],
                'sales_voucher_products_tag' => $sales->sales_voucher_products_tag[$i],
                'sales_voucher_products_qty' => $sales->sales_voucher_products_qty[$i],
                'sales_voucher_products_weight' => $sales->sales_voucher_products_weight[$i],
                'sales_voucher_products_purity' => $sales->sales_voucher_products_purity[$i],
                'sales_voucher_products_wastage' => $sales->sales_voucher_products_wastage[$i],
                'sales_voucher_products_wastage_weight' => $sales->sales_voucher_products_wastage_weight[$i],
                'sales_voucher_products_rate' => $sales->sales_voucher_products_rate[$i],
                'sales_voucher_products_additional_rate' => $sales->sales_voucher_products_additional_rate[$i],
                'sales_voucher_products_gross_amount' => $sales->sales_voucher_products_gross_amount[$i],
                'sales_voucher_products_vat' => $sales->sales_voucher_products_vat[$i],
                'sales_voucher_products_vat_amount' => $sales->sales_voucher_products_vat_amount[$i],
                'sales_voucher_products_making_charge' => $sales->sales_voucher_products_making_charge[$i],
                'sales_voucher_products_discount_amount' => $sales->sales_voucher_products_discount_amount[$i],
                'sales_voucher_products_net_amount' => $sales->sales_voucher_products_net_amount[$i],
                'sales_voucher_products_narration' => $sales->sales_voucher_products_narration[$i],
                'return_check' => $sales->return_check[$i]
            ];
            if (0 == (int) $sales->sales_voucher_id[$i]) {
                $this->tableGateway->insert($data);
                $lastInsertedId = $this->tableGateway->lastInsertValue;
            }
            if (0 != (int) $sales->sales_voucher_id[$i]) {
                $updateResult = $this->tableGateway->update($data, [ 'sales_voucher_id' => $sales->sales_voucher_id[$i] ]);
            }
        }
        $updateResult = 0;
        if(isset($sales->delete_id) && trim($sales->delete_id) != ''){
            $deleteIds = explode(',',$sales->delete_id);
            foreach($deleteIds as $delete){
                $updateResult = $this->tableGateway->delete(['sales_voucher_id' => (int) $delete]);
            }
        }
        if($lastInsertedId > 0 || $updateResult > 0){
            return 1;
        }
    }

    public function deleteSales($params)
    {
        return $this->tableGateway->delete(['sales_id' => (int) $params->sales_id]);
    }
}