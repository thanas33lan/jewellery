<?php
namespace Login\Model;

use Zend\Db\Sql\Sql;
use RuntimeException;
use Zend\Session\Container;
use Application\Service\CommonService;
use Zend\Db\TableGateway\TableGatewayInterface;

class UserTable
{
    private $tableGateway;

    function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }
    
    public function getUserLogin($user)
    {
        $alertContainer = new Container('alert');
        $logincontainer = new Container('user');
        $config = new \Zend\Config\Reader\Ini();
        $configResult = $config->fromFile('config/custom.config.ini');
        $password = sha1($user->password . $configResult["password"]["salt"]);
        $rowset = $this->tableGateway->select(['username' => $user->userName,'password'=>$password]);
        $row = $rowset->current();
        $logincontainer->userId = $row->user_id;
        $logincontainer->roleId = $row->role_id;
        $logincontainer->name = $row->name;
        $logincontainer->userName = ucwords($row->username);
        if (isset($logincontainer->userId) && trim($logincontainer->userId) != ''){
            return '/accounts';
        }else{
            return '/login';
        }
    }

    public function saveUser($user)
    {
        $data = [
            'roleId' => $user->roleId,
            'name'  => $user->name,
            'userName'  => $user->userName,
            'password'  => sha1($user->Password . $configResult["password"]["salt"]),
            'phone'  => $user->phone,
            'userDob'  => $user->userDob,
            'state'  => $user->state,
            'city'  => $user->city,
            'address'  => $user->address,
            'pincode'  => $user->pincode,
            'userStatus'  => $user->userStatus,
        ];

        $id = (int) $user->userId;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getUser($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update User with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteUser($id)
    {
        $this->tableGateway->delete(['userId' => (int) $id]);
    }
}