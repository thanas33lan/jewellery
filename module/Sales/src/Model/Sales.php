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
    public $products_type_code;
    
    public $products_type_id;
    public $products_type_name;
    
    public $account_type_id;
    public $account_type_name;
    public $account_type_status;
    
    public $account_id;
    public $account_name_tamil;
    public $account_email;
    public $account_mobile;
    public $account_address;
    public $account_area;
    public $account_city;
    public $account_pincode;
    // Table fields
    public $sales_id;
    public $sales_voucher_no;
    public $sales_voucher_date;
    public $sales_voucher_account;
    public $sales_voucher_sales_account;
    public $sales_grand_total;
    public $sales_user_id;
    public $sales_recived;
    public $sales_balance;
    public $sales_received_type;
    public $sales_remarks;
    public $sales_added_by;
    public $sales_added_on;
    // User Details Table
    public $user_id;
    public $name;
    public $user_status;
    // Sales Voucher Detials
    public $sales_voucher_id;
    public $sales_voucher_products_tag;
    public $sales_voucher_products_qty;
    public $sales_voucher_products_weight;
    public $sales_voucher_products_purity;
    public $sales_voucher_products_wastage;
    public $sales_voucher_products_wastage_weight;
    public $sales_voucher_products_rate;
    public $sales_voucher_products_additional_rate;
    public $sales_voucher_products_gross_amount;
    public $sales_voucher_products_vat;
    public $sales_voucher_products_vat_amount;
    public $sales_voucher_products_making_charge;
    public $sales_voucher_products_discount_amount;
    public $sales_voucher_products_net_amount;
    public $sales_voucher_products_narration;
    public $return_check;
    
    public function exchangeArray(array $data)
    {
        $this->sales_id                     = !empty($data['sales_id']) ? $data['sales_id'] : null;
        $this->sales_voucher_no             = !empty($data['sales_voucher_no']) ? $data['sales_voucher_no'] : null;
        $this->sales_voucher_date           = !empty($data['sales_voucher_date']) ? $data['sales_voucher_date'] : null;
        $this->sales_voucher_account        = !empty($data['sales_voucher_account']) ? $data['sales_voucher_account'] : null;
        $this->sales_voucher_sales_account  = !empty($data['sales_voucher_sales_account']) ? $data['sales_voucher_sales_account'] : null;
        $this->sales_grand_total            = !empty($data['sales_grand_total']) ? $data['sales_grand_total'] : null;
        $this->sales_user_id                = !empty($data['sales_user_id']) ? $data['sales_user_id'] : null;
        $this->sales_recived                = !empty($data['sales_recived']) ? $data['sales_recived'] : null;
        $this->sales_balance                = !empty($data['sales_balance']) ? $data['sales_balance'] : null;
        $this->sales_received_type          = !empty($data['sales_received_type']) ? $data['sales_received_type'] : null;
        $this->sales_remarks                = !empty($data['sales_remarks']) ? $data['sales_remarks'] : null;
        $this->sales_added_by               = !empty($data['sales_remarks']) ? $data['sales_remarks'] : null;
        $this->sales_added_on               = !empty($data['sales_remarks']) ? $data['sales_remarks'] : null;
        
        
        if(isset($data['products_type_id']) && trim($data['products_type_id']) != ''){
            $this->products_type_id  = !empty($data['products_type_id']) ? $data['products_type_id'] : null;
        }
        if(isset($data['products_type_name']) && trim($data['products_type_name']) != ''){
            $this->products_type_name  = !empty($data['products_type_name']) ? $data['products_type_name'] : null;
        }
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
        if(isset($data['products_type_code']) && trim($data['products_type_code']) != ''){
            $this->products_type_code  = !empty($data['products_type_code']) ? $data['products_type_code'] : null;
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
        
        if(isset($data['account_name_tamil']) && trim($data['account_name_tamil']) != ''){
            $this->account_name_tamil  = !empty($data['account_name_tamil']) ? $data['account_name_tamil'] : null;
        }
        if(isset($data['account_email']) && trim($data['account_email']) != ''){
            $this->account_email  = !empty($data['account_email']) ? $data['account_email'] : null;
        }
        if(isset($data['account_mobile']) && trim($data['account_mobile']) != ''){
            $this->account_mobile  = !empty($data['account_mobile']) ? $data['account_mobile'] : null;
        }
        if(isset($data['account_address']) && trim($data['account_address']) != ''){
            $this->account_address  = !empty($data['account_address']) ? $data['account_address'] : null;
        }
        if(isset($data['account_area']) && trim($data['account_area']) != ''){
            $this->account_area  = !empty($data['account_area']) ? $data['account_area'] : null;
        }
        if(isset($data['account_city']) && trim($data['account_city']) != ''){
            $this->account_city  = !empty($data['account_city']) ? $data['account_city'] : null;
        }
        if(isset($data['account_pincode']) && trim($data['account_pincode']) != ''){
            $this->account_pincode  = !empty($data['account_pincode']) ? $data['account_pincode'] : null;
        }
        if(isset($data['account_id']) && trim($data['account_id']) != ''){
            $this->account_id  = !empty($data['account_id']) ? $data['account_id'] : null;
        }

        if(isset($data['user_id']) && trim($data['user_id']) != ''){
            $this->user_id  = !empty($data['user_id']) ? $data['user_id'] : null;
        }
        if(isset($data['name']) && trim($data['name']) != ''){
            $this->name  = !empty($data['name']) ? $data['name'] : null;
        }
        if(isset($data['user_status']) && trim($data['user_status']) != ''){
            $this->user_status  = !empty($data['user_status']) ? $data['user_status'] : null;
        }

        if(isset($data['sales_voucher_id']) && trim($data['sales_voucher_id']) != ''){
            $this->user_status  = !empty($data['sales_voucher_id']) ? $data['sales_voucher_id'] : null;
        }
        if(isset($data['sales_voucher_products_tag']) && trim($data['sales_voucher_products_tag']) != ''){
            $this->user_status  = !empty($data['sales_voucher_products_tag']) ? $data['sales_voucher_products_tag'] : null;
        }
        if(isset($data['sales_voucher_products_qty']) && trim($data['sales_voucher_products_qty']) != ''){
            $this->user_status  = !empty($data['sales_voucher_products_qty']) ? $data['sales_voucher_products_qty'] : null;
        }
        if(isset($data['sales_voucher_products_weight']) && trim($data['sales_voucher_products_weight']) != ''){
            $this->user_status  = !empty($data['sales_voucher_products_weight']) ? $data['sales_voucher_products_weight'] : null;
        }
        if(isset($data['sales_voucher_products_purity']) && trim($data['sales_voucher_products_purity']) != ''){
            $this->user_status  = !empty($data['sales_voucher_products_purity']) ? $data['sales_voucher_products_purity'] : null;
        }
        if(isset($data['sales_voucher_products_wastage']) && trim($data['sales_voucher_products_wastage']) != ''){
            $this->user_status  = !empty($data['sales_voucher_products_wastage']) ? $data['sales_voucher_products_wastage'] : null;
        }
        if(isset($data['sales_voucher_products_wastage_weight']) && trim($data['sales_voucher_products_wastage_weight']) != ''){
            $this->user_status  = !empty($data['sales_voucher_products_wastage_weight']) ? $data['sales_voucher_products_wastage_weight'] : null;
        }
        if(isset($data['sales_voucher_products_rate']) && trim($data['sales_voucher_products_rate']) != ''){
            $this->user_status  = !empty($data['sales_voucher_products_rate']) ? $data['sales_voucher_products_rate'] : null;
        }
        if(isset($data['sales_voucher_products_additional_rate']) && trim($data['sales_voucher_products_additional_rate']) != ''){
            $this->user_status  = !empty($data['sales_voucher_products_additional_rate']) ? $data['sales_voucher_products_additional_rate'] : null;
        }
        if(isset($data['sales_voucher_products_gross_amount']) && trim($data['sales_voucher_products_gross_amount']) != ''){
            $this->user_status  = !empty($data['sales_voucher_products_gross_amount']) ? $data['sales_voucher_products_gross_amount'] : null;
        }
        if(isset($data['sales_voucher_products_vat']) && trim($data['sales_voucher_products_vat']) != ''){
            $this->user_status  = !empty($data['sales_voucher_products_vat']) ? $data['sales_voucher_products_vat'] : null;
        }
        if(isset($data['sales_voucher_products_vat_amount']) && trim($data['sales_voucher_products_vat_amount']) != ''){
            $this->user_status  = !empty($data['sales_voucher_products_vat_amount']) ? $data['sales_voucher_products_vat_amount'] : null;
        }
        if(isset($data['sales_voucher_products_making_charge']) && trim($data['sales_voucher_products_making_charge']) != ''){
            $this->user_status  = !empty($data['sales_voucher_products_making_charge']) ? $data['sales_voucher_products_making_charge'] : null;
        }
        if(isset($data['sales_voucher_products_discount_amount']) && trim($data['sales_voucher_products_discount_amount']) != ''){
            $this->user_status  = !empty($data['sales_voucher_products_discount_amount']) ? $data['sales_voucher_products_discount_amount'] : null;
        }
        if(isset($data['sales_voucher_products_net_amount']) && trim($data['sales_voucher_products_net_amount']) != ''){
            $this->user_status  = !empty($data['sales_voucher_products_net_amount']) ? $data['sales_voucher_products_net_amount'] : null;
        }
        if(isset($data['sales_voucher_products_narration']) && trim($data['sales_voucher_products_narration']) != ''){
            $this->user_status  = !empty($data['sales_voucher_products_narration']) ? $data['sales_voucher_products_narration'] : null;
        }
        if(isset($data['return_check']) && trim($data['return_check']) != ''){
            $this->user_status  = !empty($data['return_check']) ? $data['return_check'] : null;
        }
    }
}