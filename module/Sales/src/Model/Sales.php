<?php

namespace Sales\Model;

class Sales
{
    public $products_id;
    public $products_tag;
    public $products_name;
    public $products_wastage;
    public $products_charge;
    public $products_rate;
    public $products_vat;
    public $products_vat_rate;
    public $products_qty;
    public $products_status;
    
    public $account_type_id;
    public $account_type_name;
    public $account_type_status;
    
    // Table fields
    public $sales_id;
    public $sales_voucher_no;
    public $sales_voucher_date;
    public $sales_voucher_account;
    public $sales_voucher_sales_account;
    public $sales_voucher_remarks;
    public $sales_grand_total;
    
    public function exchangeArray(array $data)
    {
        $this->sales_id  = !empty($data['sales_id']) ? $data['sales_id'] : null;
        $this->sales_voucher_no  = !empty($data['sales_voucher_no']) ? $data['sales_voucher_no'] : null;
        $this->sales_voucher_date  = !empty($data['sales_voucher_date']) ? $data['sales_voucher_date'] : null;
        $this->sales_voucher_account  = !empty($data['sales_voucher_account']) ? $data['sales_voucher_account'] : null;
        $this->sales_voucher_sales_account  = !empty($data['sales_voucher_sales_account']) ? $data['sales_voucher_sales_account'] : null;
        $this->sales_voucher_remarks  = !empty($data['sales_voucher_remarks']) ? $data['sales_voucher_remarks'] : null;
        $this->sales_grand_total  = !empty($data['sales_grand_total']) ? $data['sales_grand_total'] : null;

        if(isset($data['products_id']) && trim($data['products_id']) != ''){
            $this->products_id  = !empty($data['products_id']) ? $data['products_id'] : null;
        }
        if(isset($data['products_tag']) && trim($data['products_tag']) != ''){
            $this->products_tag  = !empty($data['products_tag']) ? $data['products_tag'] : null;
        }
        if(isset($data['products_name']) && trim($data['products_name']) != ''){
            $this->products_name  = !empty($data['products_name']) ? $data['products_name'] : null;
        }
        if(isset($data['products_wastage']) && trim($data['products_wastage']) != ''){
            $this->products_wastage  = !empty($data['products_wastage']) ? $data['products_wastage'] : null;
        }
        if(isset($data['products_charge']) && trim($data['products_charge']) != ''){
            $this->products_charge  = !empty($data['products_charge']) ? $data['products_charge'] : null;
        }
        if(isset($data['products_rate']) && trim($data['products_rate']) != ''){
            $this->products_rate  = !empty($data['products_rate']) ? $data['products_rate'] : null;
        }
        if(isset($data['products_vat']) && trim($data['products_vat']) != ''){
            $this->products_vat  = !empty($data['products_vat']) ? $data['products_vat'] : null;
        }
        if(isset($data['products_vat_rate']) && trim($data['products_vat_rate']) != ''){
            $this->products_vat_rate  = !empty($data['products_vat_rate']) ? $data['products_vat_rate'] : null;
        }
        if(isset($data['products_qty']) && trim($data['products_qty']) != ''){
            $this->products_qty  = !empty($data['products_qty']) ? $data['products_qty'] : null;
        }
        if(isset($data['products_status']) && trim($data['products_status']) != ''){
            $this->products_status  = !empty($data['products_status']) ? $data['products_status'] : null;
        }
        
        if(isset($data['account_type_id']) && trim($data['account_type_id']) != ''){
            $this->account_type_id  = !empty($data['account_type_id']) ? $data['account_type_id'] : null;
        }
        if(isset($data['account_type_name']) && trim($data['account_type_name']) != ''){
            $this->account_type_name  = !empty($data['account_type_name']) ? $data['account_type_name'] : null;
        }
        if(isset($data['account_type_status']) && trim($data['account_type_status']) != ''){
            $this->account_type_status  = !empty($data['account_type_status']) ? $data['account_type_status'] : null;
        }
    }
}