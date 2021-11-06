<?php
namespace App\Core;

use CodeIgniter\Model;

class BaseModel extends Model
{

    protected $tableName;

    protected $primaryKey = 0;

    protected $helpers = ['url', 'form', 'infector', 'session'];

    public function __construct()
    {

        parent::__construct();

        $this->fetchTable();
        $this->fetchTablePrimaryKey();

        // $session = \Config\Services::session();
        $session = session();
        $uri = service('uri');
        $this->builder = $this->db->table($this->tableName);
        $this->sql = "";

        // $this->AdminModel = new AdminModel();
        // $this->UserModel = new UserModel();

    }

    /**
     * Guess the table name by the model name
     */

    protected function fetchTable()
    {
        if ($this->tableName == null) {
            $this->tableName = preg_replace('/(M|Model)?$/', '', get_class($this));
            $this->tableName = substr($this->tableName, 11);
            $this->tableName = preg_split('/(?=[A-Z])/',$this->tableName);
            unset($this->tableName[0]);
            $tableName = "";
            foreach($this->tableName as $row){
                $tableName .= $row . "_";
            }
            $this->tableName = substr($tableName, 0, -1);
            $this->tableName = strtolower($this->tableName);
        }
    }


    /**
     * Guess the table name by the model name + '_id'
     */

    protected function fetchTablePrimaryKey()
    {
        if ($this->primaryKey == null) {
            $this->primaryKey = preg_replace('/(M|Model)?$/', '', get_class($this));
            $this->primaryKey = substr($this->primaryKey, 11);
            $this->primaryKey = preg_split('/(?=[A-Z])/',$this->primaryKey);
            unset($this->primaryKey[0]);
            $primaryKey = "";
            foreach($this->primaryKey as $row){
                $primaryKey .= $row . "_";
            }
            $this->primaryKey = substr($primaryKey, 0, -1);
            $this->primaryKey .= "_id";
            $this->primaryKey = strtolower($this->primaryKey);
        }

    }

    public function getAll($limit = '', $page = 1, $filter = array())
    {
        // $this->builder = $this->db->table($this->tableName);
        // $this->builder->select('*');

        // $query = $this->builder->get();
        // return $query->getResultArray();

        $fields = $this->db->getFieldNames($this->tableName);

        $deleted = false;
        foreach ($fields as $row) {
            if ($row == "deleted") {
                $deleted = true;
            }
        }

        $this->builder->select('*');

        if ($deleted) {
            $this->builder->where($this->tableName . ".deleted", 0);
        }
        
        if ($limit != '') {
            $count = $this->getCount($filter);
            $offset = ($page - 1) * $limit;
            $pages = $count / $limit;
            $pages = ceil($pages);
            $pagination = $this->getPaging($limit, $offset, $page, $pages, $filter);

            return $pagination;

            // intval($limit);
            // $this->db->limit($limit, $offset);
        }

        $query = $this->builder->get();
        return $query->getResultArray();

    }

    public function getWhere($where, $limit = '', $page = 1, $filter = array())
    {
        $this->builder->select('*');
        $this->builder->where($where);

        if ($limit != '') {
            $count = $this->getCount($filter);
            $offset = ($page - 1) * $limit;
            $pages = $count / $limit;
            $pages = ceil($pages);
            $pagination = $this->getPaging($limit, $offset, $page, $pages, $filter);

            return $pagination;

            // intval($limit);
            // $this->db->limit($limit, $offset);
        }

        $query = $this->builder->get();
        return $query->getResultArray();

    }

    public function getAllWithRole($limit = '', $page = 1, $filter = array())
    {
        $this->builder->select("*, role.role AS role");
        $this->builder->from($this->table_name);
        $this->builder->join("role", $this->table_name . ".role_id = role.role_id", "left");
        $this->builder->where($this->table_name . ".deleted", 0);

        if ($limit != '') {
            $count = $this->getCount($filter);
            $offset = ($page - 1) * $limit;
            $pages = $count / $limit;
            $pages = ceil($pages);
            $pagination = $this->getPaging($limit, $offset, $page, $pages, $filter);

            return $pagination;

            // intval($limit);
            // $this->db->limit($limit, $offset);
        }

        $query = $this->builder->get();
        return $query->getResultArray();

    }

    public function getWhereWithRole($where, $limit = '', $page = 1, $filter = array())
    {
        $this->builder->select("*, role.role AS role");
        $this->builder->from($this->table_name);
        $this->builder->join("role", $this->table_name . ".role_id = role.role_id", "left");
        $this->builder->where($this->table_name . ".deleted", 0);
        $this->builder->where($where);
        $query = $this->builder->get();

        if ($limit != '') {
            $count = $this->getCount($filter);
            $offset = ($page - 1) * $limit;
            $pages = $count / $limit;
            $pages = ceil($pages);
            $pagination = $this->getPaging($limit, $offset, $page, $pages, $filter);

            return $pagination;

            // intval($limit);
            // $this->db->limit($limit, $offset);
        }

        $query = $this->builder->get();
        return $query->getResultArray();
    }

    public function insertNew($data)
    {

        $db = db_connect('default'); 
        $this->builder = $this->db->table($this->tableName);
        $this->builder->insert($data);

        return $db->insertID();

    }

    public function updateWhere($where, $data)
    {
        $this->builder = $this->db->table($this->tableName);
        $this->builder->where($where);
        $this->builder->update($data);
    }

    public function softDelete($primaryKey)
    {
        $this->builder = $this->db->table($this->tableName);

        $data = array(
            "deleted" => 1,
        );

        $this->builder->where($this->primaryKey, $primaryKey);
        $this->builder->update($data);

    }

    public function login($username, $password)
    {
        $this->builder = $this->db->table($this->tableName);
        $this->builder->select('*');
        $this->builder->join("role", $this->tableName . '.role_id = role.role_id', 'left');
        $this->builder->where("username = ", $username);
        $this->builder->where("password = SHA2(CONCAT(salt,'" . $password . "'),512)");

        $query = $this->builder->get();
        return $query->getResultArray();
    }

    public function loginWithEmail($email, $password)
    {
        $this->builder = $this->db->table($this->tableName);
        $this->builder->select('*');
        $this->builder->join("role", $this->tableName . '.role_id = role.role_id', 'left');
        $this->builder->where("email = ", $email);
        $this->builder->where("password = SHA2(CONCAT(salt,'" . $password . "'),512)");

        $query = $this->builder->get();
        return $query->getResultArray();
    }
    public function getWhereAndPrimaryIsNot($where, $primaryKey)
    {
        $this->builder = $this->db->table($this->tableName);
        $this->builder->select("*");
        $this->builder->where($this->primaryKey . "!=", $primaryKey);
        $this->builder->where($where);

        $query = $this->builder->get();
        return $query->getResultArray();
    }

    public function debug($data)
    {
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
        die();
    }

    public function checkExists($email){
        $this->builder = $this->db->table($this->tableName);
        $this->builder->select("*");
        $this->builder->where("email" . "=", $email);
        $query = $this->builder->get();
        $result = $query->getResultArray();
        if(!empty($result)){
            return true;
        }else{
            return false;

        }
    }
    public function getCount($filter)
    {
        
        $temp_builder = $this->builder;
        if(!empty($filter)){
            foreach($filter as $key => $row){
                $temp_builder->like($key, $row);
            }
        }

        $this->sql = $this->builder->getCompiledSelect(false);

        $result = $temp_builder->get()->getResultArray(false);

        return count($result);
    }

    public function getPaging($limit, $offset, $page, $pages, $filter)
    {

        $showing_from = $page - 2;
        $showing_to = $page + 2;
        
        $sql = $this->sql;
        $sql .= " LIMIT " . $limit . " OFFSET " . $offset;


        $result = $this->db->query($sql)->getResultArray();
        if($pages == 0 OR $pages == 1){
            $pagination = "";
        } else{
            $pagination = '<nav aria-label="..." class="float-right">';
            $pagination .= '<ul class="pagination">';
            if ($page > 1) {
                $pagination .= '<li class="page-item">';
                $pagination .= '<span class="page-link" data-page="' . ($page - 1) . '">Previous</span>';
                $pagination .= '</li>';    
            }
            if ($page == 1) {
                $pagination .= '<li class="page-item active"><a class="page-link" data-page="#">1</a></li>';
            } else {
                $pagination .= '<li class="page-item"><a class="page-link" data-page="1">1</a></li>';
            }
            if ($showing_from > 1) {
                $pagination .= '<li class="page-item" disabled><span class="page-link">...</span></li>';
            }
            for ($i = 2; $i <= ($pages - 1); $i++) {
                if ($i == $page) {
                    $pagination .= '<li class="page-item active"><a class="page-link" data-page="#">' . $i . '</a></li>';
                } else if($i < $showing_to AND $i > $showing_from) {
                    $pagination .= '<li class="page-item"><a class="page-link" data-page="' . $i . '">' . $i . '</a></li>';
                }
            }
            if ($showing_to < $pages) {
                $pagination .= '<li class="page-item" disabled><span class="page-link">...</span></li>';
            }
            if ($page == $pages) {
                $pagination .= '<li class="page-item active"><a class="page-link" data-page="#">' . $pages . '</a></li>';
            } else {
                $pagination .= '<li class="page-item"><a class="page-link" data-page="' . $pages . '">' . $pages . '</a></li>';
            }
            if ($page < $pages) {
            $pagination .= '<li class="page-item">';
            $pagination .= '<a class="page-link" data-page="' . ($page + 1) . '">Next</a>';
            $pagination .= '</li>';
            }
            $pagination .= '</ul>';
            $pagination .= '</nav>';
        }

        $data = array(
            "result" => $result,
            "pagination" => $pagination,
            "start_no" => 1 + $offset,
        );
        return $data;
        // $this->debug($sql);
    }

}
