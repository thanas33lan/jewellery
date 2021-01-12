<?php

namespace Sales\Model;

class Price
{
    public $price_id;
    public $gold_rate;
    public $silver_rate;
    public $added_on;
    public $flag_filter;
    
    public function exchangeArray(array $data)
    {
        if(isset($data['price_id']) && trim($data['price_id']) != ''){
            $this->price_id  = !empty($data['price_id']) ? $data['price_id'] : null;
        }
        if(isset($data['gold_rate']) && trim($data['gold_rate']) != ''){
            $this->gold_rate  = !empty($data['gold_rate']) ? $data['gold_rate'] : null;
        }
        if(isset($data['silver_rate']) && trim($data['silver_rate']) != ''){
            $this->silver_rate  = !empty($data['silver_rate']) ? $data['silver_rate'] : null;
        }
        if(isset($data['added_on']) && trim($data['added_on']) != ''){
            $this->added_on  = !empty($data['added_on']) ? $data['added_on'] : null;
        }
        if(isset($data['flag_filter']) && trim($data['flag_filter']) != ''){
            $this->flag_filter  = !empty($data['flag_filter']) ? $data['flag_filter'] : null;
        }
    }
}