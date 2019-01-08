<?php
namespace User\Model;

use RuntimeException;
use Zend\Db\Sql\Select;
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

    public function fetchAllRoles()
    {
        $role = new Select('roles');
        return $this->tableGateway->selectWith($role);
    }
    
    public function fetchAllState()
    {
        $state = new Select('state');
        return $this->tableGateway->selectWith($state);
    }

    public function fetchAll($parameters)
    {
        $aColumns = ['role_name','name','username','phone','user_status'];
        $orderColumns = ['role_name','name','username','phone','user_status'];
        
        /* Paging */
        $sLimit = "";
        if (isset($parameters['iDisplayStart']) && $parameters['iDisplayLength'] != '-1') {
            $sOffset = $parameters['iDisplayStart'];
            $sLimit = $parameters['iDisplayLength'];
        }
        
        /* Ordering */
        $sOrder = "";
        if (isset($parameters['iSortCol_0'])) {
            for ($i = 0; $i < intval($parameters['iSortingCols']); $i++) {
                if ($parameters['bSortable_' . intval($parameters['iSortCol_' . $i])] == "true") {
                    $sOrder .= $aColumns[intval($parameters['iSortCol_' . $i])] . " " . ( $parameters['sSortDir_' . $i] ) . ",";
                }
            }
            $sOrder = substr_replace($sOrder, "", -1);
        }
        
        /*
        * Filtering
        */
        
        $sWhere = "";
        if (isset($parameters['sSearch']) && $parameters['sSearch'] != "") {
            $searchArray = explode(" ", $parameters['sSearch']);
            $sWhereSub = "";
            foreach ($searchArray as $search) {
                if ($sWhereSub == "") {
                    $sWhereSub .= "(";
                } else {
                    $sWhereSub .= " AND (";
                }
                $colSize = count($aColumns);
                
                for ($i = 0; $i < $colSize; $i++) {
                    if ($i < $colSize - 1) {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' OR ";
                    } else {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search ) . "%' ";
                    }
                }
                $sWhereSub .= ")";
            }
            $sWhere .= $sWhereSub;
        }

        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($parameters['bSearchable_' . $i]) && $parameters['bSearchable_' . $i] == "true" && $parameters['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere .= $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                } else {
                    $sWhere .= " AND " . $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                }
            }
        }
        
        /*
        * Get data to display
        */
        $select = new Select([ 'ud' => 'user_details' ]);
        $select->join(['r' => 'roles'], 'ud.role_id = r.role_id', ['role_name']);
        if (isset($sWhere) && $sWhere != "") {
                $select->where($sWhere);
        }
        
        if (isset($sOrder) && $sOrder != "") {
                $select->order($sOrder);
        }
        
        if (isset($sLimit) && isset($sOffset)) {
                $select->limit($sLimit);
                $select->offset($sOffset);
        }
        $resultSet = $this->tableGateway->selectWith($select);
        /* Data set length after filtering */
        $select->reset('limit');
        $select->reset('offset');
        $rowTotal = $this->tableGateway->selectWith($select);
        $iFilteredTotal = count($rowTotal);
        $output = [
                "sEcho" => intval($parameters['sEcho']),
                "iTotalRecords" => count($rowTotal),
                "iTotalDisplayRecords" => $iFilteredTotal,
                "aaData" => array()
        ];
        foreach ($resultSet as $aRow) {
            $row = array();
            $row[] = ucwords($aRow->role_name);
            $row[] = ucwords($aRow->name);
            $row[] = $aRow->username;
            $row[] = $aRow->phone;
            $row[] = ucwords($aRow->user_status);
            $row[] ='<div class="btn-group btn-group-sm" role="group" aria-label="Small Horizontal Primary">
                        <a class="btn btn-primary" href="/user/edit/' . base64_encode($aRow->user_id) . '"><i class="si si-pencil"></i> Edit</a>
                        <a class="btn btn-danger" onclick="deleteUser(' . $aRow->user_id . ');" href="javascript:void(0);"><i class="far fa-trash-alt"></i> Delete</a>
                    </div>';
            $output['aaData'][] = $row;
        }

        return $output;
    }
    
    public function getUser($id)
    {
        $userId = (int) $id;
        $rowset = $this->tableGateway->select(['user_id' => $userId]);
        $row = $rowset->current();
        
        $alertContainer = new Container('alert');
        if (! $row) {
            $alertContainer->alertMsg = 'User not found';
            return 0;
        }

        return $row;
    }

    public function saveUser($user)
    {
        $common = new CommonService();
        $config = new \Zend\Config\Reader\Ini();
        $configResult = $config->fromFile('config/custom.config.ini');
        $data = [
            'role_id'  => $user->role_id,
            'name'  => $user->name,
            'username'  => $user->username,
            'phone'  => $user->phone,
            'user_dob'  => $common->dbDateFormat($user->user_dob),
            'state'  => $user->state,
            'city'  => $user->city,
            'address'  => $user->address,
            'pincode'  => $user->pincode,
            'user_status' => $user->user_status
        ];
        if(isset($user->password) && trim($user->password) != ''){
            $password = sha1($user->password . $configResult["password"]["salt"]);
            $data['password'] = $password;
        }
        $userId = (int) $user->user_id;
        $alertContainer = new Container('alert');
        
        if ($userId === 0) {
            $inserted = $this->tableGateway->insert($data);
            if($inserted > 0){
                $alertContainer->alertMsg = 'User details added successfully';
            }
            return;
        }
        $updated = $this->tableGateway->update($data, ['user_id' => $userId]);

        if($updated > 0){
            $alertContainer->alertMsg = 'User details updated successfully';
        }
    }

    public function deleteUser($params)
    {
        return $this->tableGateway->delete(['user_id' => (int) $params->user_id]);
    }
}