<?php

namespace Login\Model;

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
    public $address;
    public $pincode;
    public $user_status;

    public function exchangeArray(array $data)
    {
        $this->user_id= !empty($data['user_id']) ? $data['user_id'] : null;
        $this->role_id = !empty($data['role_id']) ? $data['role_id'] : null;
        $this->name  = !empty($data['name']) ? $data['name'] : null;
        $this->username  = !empty($data['username']) ? $data['username'] : null;
        $this->password  = !empty($data['password']) ? $data['password'] : null;
        $this->phone  = !empty($data['phone']) ? $data['phone'] : null;
        $this->user_dob  = !empty($data['user_dob']) ? $data['user_dob'] : null;
        $this->state  = !empty($data['state']) ? $data['state'] : null;
        $this->city  = !empty($data['city']) ? $data['city'] : null;
        $this->address  = !empty($data['address']) ? $data['address'] : null;
        $this->pincode  = !empty($data['pincode']) ? $data['pincode'] : null;
        $this->user_status  = !empty($data['user_status']) ? $data['user_status'] : null;
    }
}