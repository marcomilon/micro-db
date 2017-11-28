<?php 

namespace micro\test\units\db;

use atoum;

class QueryBuilder extends atoum
{
    public function testSelect() {
        $table = 'home';
        $queryBuilder = new \micro\db\QueryBuilder($table);
        $sql = $queryBuilder->select();
        $this->string($sql)->isEqualTo("SELECT * FROM `$table`");
    }
    
    public function testSelectWithColumns() {
        $table = 'home';
        $queryBuilder = new \micro\db\QueryBuilder($table);
        $columns = [
            'id',
            'name',
            'address'
        ];
        $sql = $queryBuilder->select($columns);
        $this->string($sql)->isEqualTo("SELECT `id`, `name`, `address` FROM `$table`");
    }
    
    public function testSelectWithColumnsAndConditions() {
        $table = 'home';
        $queryBuilder = new \micro\db\QueryBuilder($table);
        $columns = [
            'id',
            'name',
            'address'
        ];
        $conditions = [
            ['id', '=', '1'],
            ['and'],
            ['status', '!=', '0']
        ];
        $sql = $queryBuilder->select($columns, $conditions);
        $this->string($sql)->isEqualTo("SELECT `id`, `name`, `address` FROM `$table` WHERE `id` = '1' AND `status` != '0'");
    }
}