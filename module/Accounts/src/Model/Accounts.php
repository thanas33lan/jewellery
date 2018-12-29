<?php

namespace Accounts\Model;

class Accounts
{
    public $account_id;
    public $account_type_id;
    public $account_address;
    public $account_area;
    public $account_city;
    public $account_pincode;
    public $account_mobile;
    public $account_email;
    public $account_name_tamil;
    public $account_city_tamil;
    public $account_status;
    
    public $account_type_name;

    public function exchangeArray(array $data)
    {
        $this->account_id           = !empty($data['account_id']) ? $data['account_id'] : null;
        $this->account_type_id      = !empty($data['account_type_id']) ? $data['account_type_id'] : null;
        $this->account_address      = !empty($data['account_address']) ? $data['account_address'] : null;
        $this->account_area         = !empty($data['account_area']) ? $data['account_area'] : null;
        $this->account_city         = !empty($data['account_city']) ? $data['account_city'] : null;
        $this->account_pincode      = !empty($data['account_pincode']) ? $data['account_pincode'] : null;
        $this->account_mobile       = !empty($data['account_mobile']) ? $data['account_mobile'] : null;
        $this->account_email        = !empty($data['account_email']) ? $data['account_email'] : null;
        $this->account_name_tamil   = !empty($data['account_name_tamil']) ? $data['account_name_tamil'] : null;
        $this->account_city_tamil   = !empty($data['account_city_tamil']) ? $data['account_city_tamil'] : null;
        $this->account_status       = !empty($data['account_status']) ? $data['account_status'] : null;
        
        $this->account_type_name    = !empty($data['account_type_name']) ? $data['account_type_name'] : null;
    }
}