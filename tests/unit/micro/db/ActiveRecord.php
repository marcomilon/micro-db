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
    
    public function testFindOneWithSelect() {
        $condition = [
            ['=', 'help_category_id', '1']
        ];
        $model = \micro\Model\HelpCategory::find(["name"])->where($condition)->one();
        $this->boolean(isset($model->help_category_id))->isFalse();
    }
    
    public function testFindOneWithSelectInvalidColumn() {
        $condition = [
            ['=', 'help_category_id', '1']
        ];
        $model = \micro\Model\HelpCategory::find(["help_category_id"])->where($condition)->one();
        $this->string($model->help_category_id)->isEqualTo("1");
    }
    
    public function testFindAll() {
        $condition = [
            ['in', 'help_category_id', ['2', '3', '4']]
        ];
        $model = \micro\Model\HelpCategory::find()->where($condition)->all();
        $this->array($model)->hasSize(3);
    }
    
    public function testFindAllForEach() {
        $expectedResult = ['2' => 'Polygon properties', '3' => 'Numeric Functions', '4' => 'WKT'];
        $condition = [
            ['in', 'help_category_id', ['2', '3', '4']]
        ];
        $models = \micro\Model\HelpCategory::find()->where($condition)->all();
        foreach($models as $model) {
            $this->string($model->name)->isEqualTo($expectedResult[$model->help_category_id]);  
        }
    }
    
    public function testFindWithJoin() {        
        $condition = [
            ['=', 'help_category.help_category_id', '1']
        ];
        $model = \micro\Model\HelpCategory::find(['help_category.name'])->with('help_keyword', ['=', 'help_category_id', 'help_keyword_id'])->where($condition)->limit(1)->one();
        $this->string($model->name)->isEqualTo("Geographic");
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
        )->hasMessage('Cannot delete model without condition.');
    }
    
    public function testCustomTitle() {
        $model = new \micro\Model\HelpCategory();
        $this->string($model->customTitle)->isEqualTo("The custom title");
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
