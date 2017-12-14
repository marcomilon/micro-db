<?php 

namespace micro\test\units\db;

use atoum;

class Connection extends atoum
{
    public function testConnection() {
        $db = $this->getConnection();
        $this->object($db)->isInstanceOf('micro\db\Connection');
    }
    
    public function testSelectAll() {
        $db = $this->getConnection();
        $table = 'help_category';
        $qB = new \micro\db\QueryBuilder($db);
        $row = $qB->select()->from($table)->all();
        $this->array($row);
    }
    
    public function testSelectOne() {
        $db = $this->getConnection();
        $table = 'help_category';
        $qB = new \micro\db\QueryBuilder($db);
        $row = $qB->select()->from($table)->one();
        $this->array($row)->hasSize(4);
    }
    
    public function testSelectColumnOne() {
        $db = $this->getConnection();
        $table = 'help_category';
        $columns = [
            'help_category_id',
            'name'
        ];
        $qB = new \micro\db\QueryBuilder($db);
        $row = $qB->select($columns)->from($table)->one();
        $this->array($row)->hasSize(2);
    }
    
    public function testSelectConditionOne() {
        $db = $this->getConnection();
        $table = 'help_category';
        $condition = [
            ['=', 'help_category_id', '1']
        ];
        $qB = new \micro\db\QueryBuilder($db);
        $row = $qB->select()->from($table)->where($condition)->one();
        $this->array($row)->hasSize(4);
    }
    
    public function testSelectColumnAndConditionAll() {
        $db = $this->getConnection();
        $table = 'help_category';
        $columns = [
            'help_category_id',
            'name'
        ];
        $condition = [
            ['in', 'help_category_id', ['2', '3', '4']]
        ];
        $qB = new \micro\db\QueryBuilder($db);
        $row = $qB->select($columns)->from($table)->where($condition)->all();
        $this->array($row)->hasSize(3);
    }
    
    private function getConnection() {
        $servername = "127.0.0.1";
        $username = "root";
        $password = "fullstack";
        $database = "mysql";
        
        return new \micro\db\Connection($servername, $username, $password, $database);
    }
}