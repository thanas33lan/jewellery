<?php

namespace User\Model;

class User
{
    public $user_id;
    public $role_id;
    public $name;
    public $username;
    public $password;
    public $phone;
    public $user_dob;
    public $state;
    public $city;
    public $pincode;
    public $user_status;
    
    public $role_name;
    public $state_id;
    public $state_name;

    public function exchangeArray(array $data)
    {
        if(isset($data['password']) && trim($data['password']) != ''){
            $this->password = $data['password'];
        }

        $this->user_id      = !empty($data['user_id']) ? $data['user_id'] : null;
        $this->role_id      = !empty($data['role_id']) ? $data['role_id'] : null;
        $this->name         = !empty($data['name']) ? $data['name'] : null;
        $this->username     = !empty($data['username']) ? $data['username'] : null;
        $this->phone        = !empty($data['phone']) ? $data['phone'] : null;
        $this->user_dob     = !empty($data['user_dob']) ? $data['user_dob'] : null;
        $this->state        = !empty($data['state']) ? $data['state'] : null;
        $this->city         = !empty($data['city']) ? $data['city'] : null;
        $this->address      = !empty($data['address']) ? $data['address'] : null;
        $this->pincode      = !empty($data['pincode']) ? $data['pincode'] : null;
        $this->user_status  = !empty($data['user_status']) ? $data['user_status'] : null;
        if(isset($data['role_name']) && trim($data['role_name']) != ''){
            $this->role_name      = !empty($data['role_name']) ? $data['role_name'] : null;
        }
        if(isset($data['state_id']) && trim($data['state_id']) != ''){
            $this->state_id      = !empty($data['state_id']) ? $data['state_id'] : null;
        }
        if(isset($data['state_name']) && trim($data['state_name']) != ''){
            $this->state_name      = !empty($data['state_name']) ? $data['state_name'] : null;
        }
    }
}