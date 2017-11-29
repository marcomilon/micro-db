<?php 

namespace micro\test\units\db;

use atoum;

class QueryBuilder extends atoum
{
    public function testSelect() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder($table);
        $sql = $qB->select()->getSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `$table`");
    }
    
    public function testSelectAllColumns() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder($table);
        $sql = $qB->select('*')->getSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `$table`");
    }
    
    public function testSelectWithColumns() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder($table);
        $columns = [
            'id',
            'name',
            'address'
        ];
        $sql = $qB->select($columns)->getSql();
        $this->string($sql)->isEqualTo("SELECT `id`, `name`, `address` FROM `$table`");
    }
    
    public function testSelectWithColumnsAndConditions() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder($table);
        $columns = [
            'id',
            'name',
            'address'
        ];
        $condition = [
            ['id', '=', '1'],
            ['and'],
            ['status', '!=', '0']
        ];
        $sql = $qB->select($columns)->where($condition)->getSql();
        $this->string($sql)->isEqualTo("SELECT `id`, `name`, `address` FROM `$table` WHERE `id` = '1' AND `status` != '0'");
    }
}