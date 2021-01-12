<?php

namespace Accounts\Model;

class AccountsGeneral
{
    public $accounts_general_id;
    public $accounts_id;
    public $accounts_general_connection_date;
    public $accounts_general_connection_type;
    public $accounts_general_deposite_amount;
    public $accounts_general_rent_amount;
    public $accounts_general_category;
    public $accounts_general_work_period;
    public $accounts_general_reference;
    public $accounts_general_sender_id;
    public $accounts_general_brand;
    public $accounts_general_purchase_disc;
    public $accounts_general_sales_disc;
    public $accounts_general_wage;
    public $accounts_general_price_list;
    public $accounts_general_overtime;
    public $accounts_general_receipt_charges;
    public $accounts_general_salesman;
    public $accounts_general_salesman_mobile;
    public $accounts_general_contact_person;
    public $accounts_general_permanent;
    public $accounts_general_warning_outstanding;
    public $accounts_general_invoice_outstanding;

    public function exchangeArray(array $data)
    {
        $this->accounts_general_id                      = !empty($data['accounts_general_id']) ? $data['accounts_general_id'] : null;
        $this->accounts_id                               = !empty($data['accounts_id']) ? $data['accounts_id'] : null;
        $this->accounts_general_connection_date         = !empty($data['accounts_general_connection_date']) ? $data['accounts_general_connection_date'] : null;
        $this->accounts_general_connection_type         = !empty($data['accounts_general_connection_type']) ? $data['accounts_general_connection_type'] : null;
        $this->accounts_general_deposite_amount         = !empty($data['accounts_general_deposite_amount']) ? $data['accounts_general_deposite_amount'] : null;
        $this->accounts_general_rent_amount             = !empty($data['accounts_general_rent_amount']) ? $data['accounts_general_rent_amount'] : null;
        $this->accounts_general_category                = !empty($data['accounts_general_category']) ? $data['accounts_general_category'] : null;
        $this->accounts_general_work_period             = !empty($data['accounts_general_work_period']) ? $data['accounts_general_work_period'] : null;
        $this->accounts_general_reference               = !empty($data['accounts_general_reference']) ? $data['accounts_general_reference'] : null;
        $this->accounts_general_sender_id               = !empty($data['accounts_general_sender_id']) ? $data['accounts_general_sender_id'] : null;
        $this->accounts_general_brand                   = !empty($data['accounts_general_brand']) ? $data['accounts_general_brand'] : null;
        $this->accounts_general_purchase_disc           = !empty($data['accounts_general_purchase_disc']) ? $data['accounts_general_purchase_disc'] : null;
        $this->accounts_general_sales_disc              = !empty($data['accounts_general_sales_disc']) ? $data['accounts_general_sales_disc'] : null;
        $this->accounts_general_wage                    = !empty($data['accounts_general_wage']) ? $data['accounts_general_wage'] : null;
        $this->accounts_general_price_list              = !empty($data['accounts_general_price_list']) ? $data['accounts_general_price_list'] : null;
        $this->accounts_general_overtime                = !empty($data['accounts_general_overtime']) ? $data['accounts_general_overtime'] : null;
        $this->accounts_general_receipt_charges         = !empty($data['accounts_general_receipt_charges']) ? $data['accounts_general_receipt_charges'] : null;
        $this->accounts_general_salesman                = !empty($data['accounts_general_salesman']) ? $data['accounts_general_salesman'] : null;
        $this->accounts_general_salesman_mobile         = !empty($data['accounts_general_salesman_mobile']) ? $data['accounts_general_salesman_mobile'] : null;
        $this->accounts_general_contact_person          = !empty($data['accounts_general_contact_person']) ? $data['accounts_general_contact_person'] : null;
        $this->accounts_general_permanent                = !empty($data['accounts_general_permanent']) ? $data['accounts_general_permanent'] : null;
        $this->accounts_general_warning_outstanding     = !empty($data['accounts_general_warning_outstanding']) ? $data['accounts_general_warning_outstanding'] : null;
        $this->accounts_general_invoice_outstanding     = !empty($data['accounts_general_invoice_outstanding']) ? $data['accounts_general_invoice_outstanding'] : null;
    }
}