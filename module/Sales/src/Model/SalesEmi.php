<?php

namespace Sales\Model;

class SalesEmi
{
    public $sales_emi_id;
    public $sales_id;
    public $sales_emi_date;
    public $sales_emi_type;
    public $sales_emi_amount;
    public $sales_emi_remarks;
    // Sales Details Table
    public $sales_voucher_no;
    
    public function exchangeArray(array $data)
    {
        $this->sales_emi_id  = !empty($data['sales_emi_id']) ? $data['sales_emi_id'] : null;
        $this->sales_id  = !empty($data['sales_id']) ? $data['sales_id'] : null;
        $this->sales_emi_date  = !empty($data['sales_emi_date']) ? $data['sales_emi_date'] : null;
        $this->sales_emi_type  = !empty($data['sales_emi_type']) ? $data['sales_emi_type'] : null;
        $this->sales_emi_amount  = !empty($data['sales_emi_amount']) ? $data['sales_emi_amount'] : null;
        $this->sales_emi_remarks  = !empty($data['sales_emi_remarks']) ? $data['sales_emi_remarks'] : null;
        // Sales Details Table
        $this->sales_voucher_no  = !empty($data['sales_voucher_no']) ? $data['sales_voucher_no'] : null;
    }
}