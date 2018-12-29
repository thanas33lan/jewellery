<?php

namespace Vat\Model;

class Vat
{
    public $vat_id;
    public $vat_slab;
    public $vat_rate;
    public $vat_account;
    public $vat_status;

    public function exchangeArray(array $data)
    {
        $this->vat_id      = !empty($data['vat_id']) ? $data['vat_id'] : null;
        $this->vat_slab    = !empty($data['vat_slab']) ? $data['vat_slab'] : null;
        $this->vat_rate    = !empty($data['vat_rate']) ? $data['vat_rate'] : null;
        $this->vat_account = !empty($data['vat_account']) ? $data['vat_account'] : null;
        $this->vat_status  = !empty($data['vat_status']) ? $data['vat_status'] : null;
    }
}