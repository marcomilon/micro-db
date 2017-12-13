<?php 

namespace micro\test\units\db;

use atoum;

class QueryBuilder extends atoum
{
    
    public function testSelect() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $sql = $qB->select()->from($table)->getRawSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `$table`");
    }
    
    public function testSelectAllColumns() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $sql = $qB->select('*')->from($table)->getRawSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `$table`");
    }
    
    public function testSelectWithColumns() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $columns = [
            'id',
            'name',
            'address'
        ];
        $sql = $qB->select($columns)->from($table)->getRawSql();
        $this->string($sql)->isEqualTo("SELECT `id`, `name`, `address` FROM `$table`");
    }
    
    public function testSelectWithConditions() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $condition = [
            ['=', 'id', '1'],
            ['and'],
            ['!=', 'status', '0']
        ];
        $sql = $qB->select()->from($table)->where($condition)->getRawSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `$table` WHERE `id` = '1' AND `status` != '0'");
    }
    
    public function testSelectWithMixedConditions() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $condition = [
            ['=', 'id', '1'],
            ['and'],
            ['!=', 'status', '0'],
            ['or'],
            ['>=', 'age', '18']
        ];
        $sql = $qB->select()->from($table)->where($condition)->getRawSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `$table` WHERE `id` = '1' AND `status` != '0' OR `age` >= '18'");
    }
    
    public function testSelectWithColumnsAndConditions() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $columns = [
            'id',
            'name',
            'address'
        ];
        $condition = [
            ['=', 'id', '1'],
            ['and'],
            ['!=', 'status', '0']
        ];
        $sql = $qB->select($columns)->from($table)->where($condition)->getRawSql();
        $this->string($sql)->isEqualTo("SELECT `id`, `name`, `address` FROM `$table` WHERE `id` = '1' AND `status` != '0'");
    }
    
    public function testSelectGroupBy() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $sql = $qB->select()->from($table)->groupBy('status')->getRawSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `$table` GROUP BY `status`");
    }
    
    public function testSelectMultipleGroupBy() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $group = [
            'id',
            'name',
            'address'
        ];
        $sql = $qB->select()->from($table)->groupBy($group)->getRawSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `$table` GROUP BY `id`, `name`, `address`");
    }
    
    public function testSelectOrderBy() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $orderBy = [
            ['id', 'ASC']
        ];        
        $sql = $qB->select()->from($table)->orderBy($orderBy)->getRawSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `$table` ORDER BY `id` ASC");
    }
    
    public function testSelectMultipleOrderBy() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $orderBy = [
            ['id', 'ASC'],
            ['status', 'DESC']
        ];        
        $sql = $qB->select()->from($table)->orderBy($orderBy)->getRawSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `$table` ORDER BY `id` ASC, `status` DESC");
    }
    
    public function testSelectLimit() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $sql = $qB->select()->from($table)->limit('10')->getRawSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `$table` LIMIT 10");
    }
    
    public function testSelectLimitFromTO() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $sql = $qB->select()->from($table)->limit('10', '100')->getRawSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `$table` LIMIT 10, 100");
    }
    
    public function testComplexSelect() {
        $columns = [
            'id',
            'name',
            'address'
        ];
        $condition = [
            ['=', 'id', '1'],
            ['and'],
            ['!=', 'status', '0']
        ];
        
        $group = [
            'id',
            'name',
            'address'
        ];
        
        $orderBy = [
            ['id', 'ASC']
        ]; 
        
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $sql = $qB->select($columns)
        ->from($table)
        ->where($condition)
        ->groupBy($group)
        ->orderBy($orderBy)
        ->limit(10, 100)
        ->getRawSql();
        $expectedSql = "SELECT `id`, `name`, `address` FROM `$table` WHERE `id` = '1' AND `status` != '0' GROUP BY `id`, `name`, `address` ORDER BY `id` ASC LIMIT 10, 100";
        $this->string($sql)->isEqualTo($expectedSql);        
    }
    
    public function testComplexSelectMore() {
        $columns = [
            'id',
            'name',
            'address'
        ];
        $condition = [
            ['=', 'id', '1'],
            ['and'],
            ['!=', 'status', '0']
        ];
        
        $group = [
            'id',
            'name',
            'address'
        ];
        
        $orderBy = [
            ['id', 'ASC'],
            ['status', 'DESC']
        ]; 
        
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $sql = $qB->select($columns)
        ->from($table)
        ->where($condition)
        ->groupBy($group)
        ->orderBy($orderBy)
        ->limit(10, 100)
        ->getRawSql();
        $expectedSql = "SELECT `id`, `name`, `address` FROM `$table` WHERE `id` = '1' AND `status` != '0' GROUP BY `id`, `name`, `address` ORDER BY `id` ASC, `status` DESC LIMIT 10, 100";
        $this->string($sql)->isEqualTo($expectedSql);        
    }
    
    
    public function testSelectEscapeValues() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $condition = [
            ['=', 'id', "Bobby';DROP TABLE users; -- "],
            ['and'],
            ['=', 'status', '" or ""="']
        ];
        $sql = $qB->select()->from($table)->where($condition)->getRawSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `home` WHERE `id` = 'Bobby\';DROP TABLE users; -- ' AND `status` = '\\\" or \\\"\\\"=\\\"'");
    }
    
    public function testSelectBetween() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $condition = [
            ['between', 'age', '18', '50']
        ];
        $sql = $qB->select()->from($table)->where($condition)->getRawSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `$table` WHERE `age` BETWEEN '18' AND '50'");
    }
    
    public function testSelectIn() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $condition = [
            ['in', 'lastname', ['test1', 'test2', 'test3']]
        ];
        $sql = $qB->select()->from($table)->where($condition)->getRawSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `$table` WHERE `lastname` IN ('test1', 'test2', 'test3')");
    }
    
}