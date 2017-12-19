<?php

namespace micro\test\units\Model;

require_once __DIR__ . '/../../../models/HelpCategory.php';

use atoum;

class HelpCategory extends atoum
{
    
    public function testFindOne() {
        $condition = [
            ['=', 'help_category_id', '1']
        ];
        $model = \micro\Model\HelpCategory::find()->where($condition)->one();
        $this->string($model->name)->isEqualTo("Geographic");
    }
    
    public function testFindAll() {
        $condition = [
            ['in', 'help_category_id', ['2', '3', '4']]
        ];
        $model = \micro\Model\HelpCategory::find()->where($condition)->all();
        $this->array($model)->hasSize(3);
    }
    
    public function testSave() {
        $model = new \micro\Model\HelpCategory();
        $model->help_category_id = '41';
        $model->name = "Test category";
        $model->parent_category_id = '36';
        $model->url = '';
        $model->save();
    
        $condition = [
            ['=', 'help_category_id', ['41']]
        ];
        $model = \micro\Model\HelpCategory::find()->where($condition)->one();
        $this->string($model->name)->isEqualTo("Test category");        
    }
    
    public function testUpdate() {
        $model = new \micro\Model\HelpCategory();
        $model->help_category_id = '42';
        $model->name = "Test new category";
        $model->parent_category_id = '36';
        $model->url = '';
        $model->save();
        
        $condition = [
            ['=', 'help_category_id', '42']
        ];
        $model = \micro\Model\HelpCategory::find()->where($condition);
        $model->name = "Test new category Updated";
        $model->save();
        
        $this->string($model->name)->isEqualTo("Test new category Updated");
        $this->string($model->parent_category_id)->isEqualTo("36");
        $this->string($model->help_category_id)->isEqualTo("42");
    }
    
    public function testDelete() {
        $model = new \micro\Model\HelpCategory();
        $model->help_category_id = '43';
        $model->name = "Test category delete";
        $model->parent_category_id = '36';
        $model->url = '';
        $model->save();
    
        $condition = [
            ['=', 'help_category_id', ['43']]
        ];
        $model = \micro\Model\HelpCategory::find()->where($condition);
        $model->delete();
    
        $model = \micro\Model\HelpCategory::find()->where($condition)->one();
        $this->boolean($model)->isFalse();
    }
    
    public function testDeleteNoCondition() {    
        $model = \micro\Model\HelpCategory::find();
        
        $qB = new \micro\db\QueryBuilder();
        $this->exception(
            function() use($model) {
                $model->delete();
            }
        )->hasMessage('Cannot delete data without condition.');
    }
    
    private function getConnection() {
        $servername = "127.0.0.1";
        $username = "root";
        $password = "fullstack";
        $database = "mysql";
        
        return new \micro\db\Connection($servername, $username, $password, $database);
    }
    
    public function tearDown()
    {
        $condition = [
            ['=', 'help_category_id', ['41']]
        ];
        $model = \micro\Model\HelpCategory::find()->where($condition);
        $model->delete();
        
        $condition = [
            ['=', 'help_category_id', '42']
        ];
        $model = \micro\Model\HelpCategory::find()->where($condition);
        $model->delete();
    }
    
}