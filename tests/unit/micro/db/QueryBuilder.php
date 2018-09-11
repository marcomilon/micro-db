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
    
    
    public function testInsert() {
        $table = 'admin';
        $qB = new \micro\db\QueryBuilder();
        $sql = $qB->insert($table, [
            'name' => 'Marco',
            'lastname' => 'Milon',
            'email' => 'marco.milon@gmail.com'
        ])->getRawSql();
        $this->string($sql)->isEqualTo("INSERT INTO `admin` (`name`, `lastname`, `email`) VALUES ('Marco', 'Milon', 'marco.milon@gmail.com')");
    }
    
    public function testUpdate() {
        $table = 'admin';
        $qB = new \micro\db\QueryBuilder();
        $sql = $qB->update($table, [
            'name' => 'Valentin',
            'lastname' => 'Milon Juarez'
        ])->getRawSql();
        $this->string($sql)->isEqualTo("UPDATE `admin` SET `name` = 'Valentin', `lastname` = 'Milon Juarez'");
    }
    
    public function testUpdateWitdhCondition() {
        $table = 'admin';
        $qB = new \micro\db\QueryBuilder();
        $sql = $qB->update($table, [
            'name' => 'Valentin',
            'lastname' => 'Milon Juarez'
        ])->where([
            ['=', 'id', '1']
        ])
        ->getRawSql();
        $this->string($sql)->isEqualTo("UPDATE `admin` SET `name` = 'Valentin', `lastname` = 'Milon Juarez' WHERE `id` = '1'");
    }
    
    
    public function testDelete() {
        $table = 'admin';
        $qB = new \micro\db\QueryBuilder();
        $sql = $qB->delete($table)->getRawSql();
        $this->string($sql)->isEqualTo("DELETE FROM `admin`");
    }
    
    public function testDeleteWithConditions() {
        $table = 'admin';
        $qB = new \micro\db\QueryBuilder();
        $sql = $qB->delete($table)
        ->where([
            ['=', 'id', '1']
        ])
        ->getRawSql();
        $this->string($sql)->isEqualTo("DELETE FROM `admin` WHERE `id` = '1'");
    }
    
    public function testSelectLogicalOperatorNotSupported() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $this->exception(
            function() use($qB) {
                $table = 'home';
                $condition = [
                    ['=', 'id', '1'],
                    ['ands'],
                    ['!=', 'status', '0']
                ];
                $qB->select()->from($table)->where($condition)->getRawSql();
            }
        )->hasMessage('Logical operator ands is not supported.');
    }
    
    public function testSelectComparisonOperatorNotSupported() {
        $table = 'home';
        $qB = new \micro\db\QueryBuilder();
        $this->exception(
            function() use($qB) {
                $table = 'home';
                $condition = [
                    ['=!', 'id', '1'],
                    ['ands'],
                    ['=', 'status', '0']
                ];
                $qB->select()->from($table)->where($condition)->getRawSql();
            }
        )->hasMessage('Comparison operator =! is not supported.');
    }
    
    public function testSelectJoin() {        
        $qB = new \micro\db\QueryBuilder();
        $sql = $qB->select()->from('login')->join('profile', ['=', 'id', 'login_id'])->getRawSql();
        
        $this->string($sql)->isEqualTo("SELECT * FROM `login` JOIN `profile` ON `login`.`id` = `profile`.`login_id`");
    }
    
    public function testSelectJoinWithWhere() {        
        $qB = new \micro\db\QueryBuilder();
        $condition = [
            ['=', 'id', '1'],
            ['and'],
            ['!=', 'status', '0']
        ];
        $sql = $qB->select()->from('login')->join('profile', ['=', 'id', 'login_id'])->where($condition)->getRawSql();
        
        $this->string($sql)->isEqualTo("SELECT * FROM `login` JOIN `profile` ON `login`.`id` = `profile`.`login_id` WHERE `id` = '1' AND `status` != '0'");
    }
    
    public function testSelectWithJoinLimit() {
        $qB = new \micro\db\QueryBuilder();
        $sql = $qB->select()->from('help_category')->join('help_keyword', ['=', 'help_category_id', 'help_keyword_id'])->limit(1)->getRawSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `help_category` JOIN `help_keyword` ON `help_category`.`help_category_id` = `help_keyword`.`help_keyword_id` LIMIT 1");
    }
    
    public function testSelectWithAlias() {
        $qB = new \micro\db\QueryBuilder();
        $condition = [
            ['=', 'help_category.help_category_id', '1']
        ];
        $sql = $qB->select(["help_category.name"])->from('help_category')->join('help_keyword', ['=', 'help_category_id', 'help_keyword_id'])->where($condition)->limit(1)->getRawSql();
        $this->string($sql)->isEqualTo("SELECT `help_category`.`name` FROM `help_category` JOIN `help_keyword` ON `help_category`.`help_category_id` = `help_keyword`.`help_keyword_id` WHERE `help_category`.`help_category_id` = '1' LIMIT 1");
    }
    
    public function testSelectWithInvalidAlias() {
        $qB = new \micro\db\QueryBuilder();
        $this->exception(
            function() use($qB) {
                $table = 'home';
                $condition = [
                    ['=!', 'id', '1'],
                    ['ands'],
                    ['=', 'status', '0']
                ];
                $qB->select(["test.test.test"])->from($table)->where($condition)->getRawSql();
            }
        )->hasMessage('Columns name test.test.test is not valid.');
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
    
}