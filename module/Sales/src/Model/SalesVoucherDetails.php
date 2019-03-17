<?php

namespace Sales\Model;

class SalesVoucherDetails
{
    public $sales_voucher_id;
    public $sales_id;
    public $sales_voucher_products_id;
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
        $this->sales_voucher_id                         = !empty($data['sales_voucher_id']) ? $data['sales_voucher_id'] : null;
        $this->sales_id                                 = !empty($data['sales_id']) ? $data['sales_id'] : null;
        $this->sales_voucher_products_id                = !empty($data['sales_voucher_products_id']) ? $data['sales_voucher_products_id'] : null;
        $this->sales_voucher_products_tag               = !empty($data['sales_voucher_products_tag']) ? $data['sales_voucher_products_tag'] : null;
        $this->sales_voucher_products_qty               = !empty($data['sales_voucher_products_qty']) ? $data['sales_voucher_products_qty'] : null;
        $this->sales_voucher_products_weight            = !empty($data['sales_voucher_products_weight']) ? $data['sales_voucher_products_weight'] : null;
        $this->sales_voucher_products_purity            = !empty($data['sales_voucher_products_purity']) ? $data['sales_voucher_products_purity'] : null;
        $this->sales_voucher_products_wastage           = !empty($data['sales_voucher_products_wastage']) ? $data['sales_voucher_products_wastage'] : null;
        $this->sales_voucher_products_wastage_weight    = !empty($data['sales_voucher_products_wastage_weight']) ? $data['sales_voucher_products_wastage_weight'] : null;
        $this->sales_voucher_products_rate              = !empty($data['sales_voucher_products_rate']) ? $data['sales_voucher_products_rate'] : null;
        $this->sales_voucher_products_additional_rate   = !empty($data['sales_voucher_products_additional_rate']) ? $data['sales_voucher_products_additional_rate'] : null;
        $this->sales_voucher_products_gross_amount      = !empty($data['sales_voucher_products_gross_amount']) ? $data['sales_voucher_products_gross_amount'] : null;
        $this->sales_voucher_products_vat               = !empty($data['sales_voucher_products_vat']) ? $data['sales_voucher_products_vat'] : null;
        $this->sales_voucher_products_vat_amount        = !empty($data['sales_voucher_products_vat_amount']) ? $data['sales_voucher_products_vat_amount'] : null;
        $this->sales_voucher_products_making_charge     = !empty($data['sales_voucher_products_making_charge']) ? $data['sales_voucher_products_making_charge'] : null;
        $this->sales_voucher_products_discount_amount   = !empty($data['sales_voucher_products_discount_amount']) ? $data['sales_voucher_products_discount_amount'] : null;
        $this->sales_voucher_products_net_amount        = !empty($data['sales_voucher_products_net_amount']) ? $data['sales_voucher_products_net_amount'] : null;
        $this->sales_voucher_products_narration         = !empty($data['sales_voucher_products_narration']) ? $data['sales_voucher_products_narration'] : null;
        $this->return_check         = !empty($data['return_check']) ? $data['return_check'] : null;
    }
}