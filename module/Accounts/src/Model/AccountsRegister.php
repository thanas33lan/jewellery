<?php

namespace Accounts\Model;

class AccountsRegister
{
    public $accounts_register_id;
    public $accounts_id;
    public $accounts_register_tin;
    public $accounts_register_cst;
    public $accounts_register_licence;
    public $accounts_register_code;
    public $accounts_register_gstin;
    public $accounts_register_delivery_name;
    public $accounts_register_address;
    public $accounts_register_area;
    public $accounts_register_city;

    public function exchangeArray(array $data)
    {
        $this->accounts_register_id             = !empty($data['accounts_register_id']) ? $data['accounts_register_id'] : null;
        $this->accounts_id                      = !empty($data['accounts_id']) ? $data['accounts_id'] : null;
        $this->accounts_register_tin            = !empty($data['accounts_register_tin']) ? $data['accounts_register_tin'] : null;
        $this->accounts_register_cst            = !empty($data['accounts_register_cst']) ? $data['accounts_register_cst'] : null;
        $this->accounts_register_licence        = !empty($data['accounts_register_licence']) ? $data['accounts_register_licence'] : null;
        $this->accounts_register_code           = !empty($data['accounts_register_code']) ? $data['accounts_register_code'] : null;
        $this->accounts_register_gstin          = !empty($data['accounts_register_gstin']) ? $data['accounts_register_gstin'] : null;
        $this->accounts_register_delivery_name  = !empty($data['accounts_register_delivery_name']) ? $data['accounts_register_delivery_name'] : null;
        $this->accounts_register_address        = !empty($data['accounts_register_address']) ? $data['accounts_register_address'] : null;
        $this->accounts_register_area           = !empty($data['accounts_register_area']) ? $data['accounts_register_area'] : null;
        $this->accounts_register_city           = !empty($data['accounts_register_city']) ? $data['accounts_register_city'] : null;
    }
}