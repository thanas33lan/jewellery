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

class PriceTable 
{
    private $tableGateway;

    function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function getPrice()
    {
        $priceQuery = new Select('today_price');
        $priceQuery->order('price_id DESC');
        $rowset = $this->tableGateway->selectWith($priceQuery);
        $row = $rowset->current();
        $result['gold_rate'] = $row->gold_rate;
        $result['silver_rate'] = $row->silver_rate;
        $result['price_id'] = $row->price_id;
        $result['flag_filter'] = $row->flag_filter;
        $result['added_on'] = $row->added_on;
        return $result;
    }
    
    public function savePrice($price)
    {
        $common = new CommonService();
        $data = [
            'gold_rate' => $price->gold_rate,
            'silver_rate' => $price->silver_rate,
            'added_on' => $common->getDate(),
            'flag_filter' => '1',
        ];
        $priceData = $this->getPrice();
        $priceId = (int) $price->price_id;
        if($priceData['added_on'] != $common->getDate()){
            $inserted = $this->tableGateway->insert($data);
            if($inserted > 0){
                return $inserted;
            }
        }
        if($priceData['added_on'] == $common->getDate()){
            $updated = $this->tableGateway->update($data, ['price_id' => $priceId]);
            if($updated > 0){
                return $updated;
            }
        }

    }
}