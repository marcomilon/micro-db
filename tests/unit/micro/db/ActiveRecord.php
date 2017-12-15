<?php 

namespace micro\test\units\Model;

require_once __DIR__ . '/../../../models/HelpCategory.php';

use atoum;

class HelpCategory extends atoum
{
    public function testFindRawSql() {
        $table = 'help_category';
        
        $helpCategory = \micro\Model\HelpCategory::find();
        $sql = $helpCategory->getRawSql();
        $this->string($sql)->isEqualTo("SELECT * FROM `$table`");
    }
    
    public function testFindOne() {
        $table = 'help_category';        
        $condition = [
            ['=', 'help_category_id', '1']
        ];
        $row = \micro\Model\HelpCategory::find()->where($condition)->one();
        $this->array($row)->hasSize(4);
    }
    
    public function testFindAll() {
        $table = 'help_category';        
        $condition = [
            ['in', 'help_category_id', ['2', '3', '4']]
        ];
        $row = \micro\Model\HelpCategory::find()->where($condition)->all();
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