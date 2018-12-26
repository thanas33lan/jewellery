<?php

namespace Products\Model;

class Products
{
    public $products_id;
    public $products_type_id;
    public $products_name;
    public $products_short_name;
    public $products_wastage;
    public $products_charge;
    public $products_rate;
    public $products_addition_rate_g;
    public $products_description;
    public $products_vat;
    public $products_vat_rate;
    public $products_qty;
    public $products_status;
    
    public $products_type_name;

    public function exchangeArray(array $data)
    {
        $this->products_id     = !empty($data['products_id']) ? $data['products_id'] : null;
        $this->products_type_id = !empty($data['products_type_id']) ? $data['products_type_id'] : null;
        $this->products_name  = !empty($data['products_name']) ? $data['products_name'] : null;
        $this->products_short_name  = !empty($data['products_short_name']) ? $data['products_short_name'] : null;
        $this->products_wastage  = !empty($data['products_wastage']) ? $data['products_wastage'] : null;
        $this->products_charge  = !empty($data['products_charge']) ? $data['products_charge'] : null;
        $this->products_rate  = !empty($data['products_rate']) ? $data['products_rate'] : null;
        $this->products_addition_rate_g  = !empty($data['products_addition_rate_g']) ? $data['products_addition_rate_g'] : null;
        $this->products_description  = !empty($data['products_description']) ? $data['products_description'] : null;
        $this->products_vat  = !empty($data['products_vat']) ? $data['products_vat'] : null;
        $this->products_vat_rate  = !empty($data['products_vat_rate']) ? $data['products_vat_rate'] : null;
        $this->products_qty  = !empty($data['products_qty']) ? $data['products_qty'] : null;
        $this->products_status  = !empty($data['products_status']) ? $data['products_status'] : null;
        
        $this->products_type_name = !empty($data['products_type_name']) ? $data['products_type_name'] : null;
    }
}