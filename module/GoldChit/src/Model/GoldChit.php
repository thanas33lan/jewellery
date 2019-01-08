<?php

namespace GoldChit\Model;

class GoldChit
{
    public $goldchit_id;
    public $goldchit_number;
    public $goldchit_date;
    public $goldchit_name;
    public $password;
    public $goldchit_address;
    public $goldchit_area;
    public $goldchit_city;
    public $goldchit_phone;
    public $goldchit_remarks;
    public $goldchit_amount;
    public $goldchit_bonus;
    public $goldchit_no_of_month;
    public $goldchit_total_amount;
    public $goldchit_scheme_name;

    public function exchangeArray(array $data)
    {
        $this->goldchit_id              = !empty($data['goldchit_id']) ? $data['goldchit_id'] : null;
        $this->goldchit_number          = !empty($data['goldchit_number']) ? $data['goldchit_number'] : null;
        $this->goldchit_date            = !empty($data['goldchit_date']) ? $data['goldchit_date'] : null;
        $this->goldchit_name            = !empty($data['goldchit_name']) ? $data['goldchit_name'] : null;
        $this->goldchit_address         = !empty($data['goldchit_address']) ? $data['goldchit_address'] : null;
        $this->goldchit_area            = !empty($data['goldchit_area']) ? $data['goldchit_area'] : null;
        $this->goldchit_city            = !empty($data['goldchit_city']) ? $data['goldchit_city'] : null;
        $this->goldchit_phone           = !empty($data['goldchit_phone']) ? $data['goldchit_phone'] : null;
        $this->goldchit_pincode         = !empty($data['goldchit_pincode']) ? $data['goldchit_pincode'] : null;
        $this->goldchit_remarks         = !empty($data['goldchit_remarks']) ? $data['goldchit_remarks'] : null;
        $this->goldchit_amount          = !empty($data['goldchit_amount']) ? $data['goldchit_amount'] : null;
        $this->goldchit_bonus           = !empty($data['goldchit_bonus']) ? $data['goldchit_bonus'] : null;
        $this->goldchit_no_of_month     = !empty($data['goldchit_no_of_month']) ? $data['goldchit_no_of_month'] : null;
        $this->goldchit_total_amount    = !empty($data['goldchit_total_amount']) ? $data['goldchit_total_amount'] : null;
        $this->goldchit_scheme_name     = !empty($data['goldchit_scheme_name']) ? $data['goldchit_scheme_name'] : null;
    }
}