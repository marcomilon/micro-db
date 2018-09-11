<?php

namespace micro\db;

use micro\db\QueryBuilder;

abstract class ActiveRecord
{
    
    protected $db;
    private $columns = [];
    
    private $condition = [];
    private $parameters = [];
    
    private $queryBuilder;
    private static $isUpdate = false;
    
    private $model;
    
    abstract public static function tableName();
    abstract public static function dbConnection();
    
    public function __set($name, $value) 
    {
        $this->columns[$name] = $value;
    }
    
    public function __get($name) 
    {
        if(isset($this->columns[$name])) {
            return $this->columns[$name];
        } elseif(isset($this->queryBuilder)) {
            $this->queryBuilder->select()->from(static::tableName())->where($this->condition);
            $result = $this->one();
            foreach($result as $k => $v) {
                $this->columns[$k] = $v;
            }
            
            return $this->columns[$name];
        }
    }
    
    public static function find($columns = null) 
    {
        $model = self::factory();
        self::$isUpdate = true;
        $model->queryBuilder->select($columns)->from(static::tableName());
        return $model;
    }
    
    public function where($condition) 
    {
        foreach($condition as $filter) {
            switch(count($filter)) {
                case 3:
                $this->setParametersValue($filter[1], $filter[2]);
                $this->condition[] = [$filter[0], $filter[1], $this->getParamaterName($filter[1], $filter[2])];
                break;
                default:
                $this->condition[] = $filter;
            }
        }
        
        $this->queryBuilder->where($this->condition);
        
        return $this;
    }
    
    public function with($joinTable, $on) 
    {
        $this->queryBuilder->join($joinTable, $on);
        
        return $this;
    }
    
    public function limit($from, $to = '') 
    {
        $this->queryBuilder->limit($from, $to);
        return $this;
    }
    
    public function save() 
    {        
        
        if(self::$isUpdate) {
            
            $this->update();
            
        } else {
            
            $model = self::factory();
            $columnsToInsert = [];
            
            foreach($this->columns as $key => $value) {
                $model->setParametersValue($key, $value, false);
                $columnsToInsert[$key] = ':i_'.$key;
            }
            
            $rawSql = $model->queryBuilder->insert(static::tableName(), $columnsToInsert)->getRawSql();        
            $sql = str_replace('\'', '', $rawSql);
            $stmt = $model->db->conn->prepare($sql);
            foreach($model->parameters as $key => &$value) {
                $stmt->bindParam($key, $value);
            }
            
            $stmt->execute(); 
            $model->parameters = $model->cleanInputParameters($model->parameters);
        }
        
    }
    
    public function delete() 
    {
        $condition = $this->queryBuilder->getCondition();
        
        if(empty($condition)) {
            throw new \Exception("Cannot delete model without condition.");
        }
        
        $rawSql = $this->queryBuilder->delete(static::tableName())->where($condition)->getRawSql();
        $sql = str_replace('\'', '', $rawSql);
        $stmt = $this->db->conn->prepare($sql);
        foreach($this->parameters as $key => &$value) {
            $stmt->bindParam($key, $value);
        }
        $stmt->execute();
    }
    
    public function one() 
    {
        $sql = str_replace('\'', '', $this->queryBuilder->getRawSql());
        $stmt = $this->db->conn->prepare($sql);
        foreach($this->parameters as $key => &$value) {
            $stmt->bindParam($key, $value);
        }
        
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ);
    }
    
    public function all() 
    {
        $sql = str_replace('\'', '', $this->queryBuilder->getRawSql());
        $stmt = $this->db->conn->prepare($sql);
        foreach($this->parameters as $key => &$value) {
            $stmt->bindParam($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
    
    public function exec() 
    {
        return $this->db->conn->exec($this->queryBuilder->getRawSql());
    }
    
    private static function factory() 
    {
        $className = get_called_class();
        $model = new $className;
        $model->db = static::dbConnection();
        $model->queryBuilder = new QueryBuilder();
        
        return $model;
    }
    
    private function setParametersValue($key, $value, $isCondition = true) {
        $result = "";
        $key = str_replace('.', '_', $key);
        
        if(is_array($value)) {
            for($i=0;$i<count($value);$i++) {
                $parameterName = $isCondition ? ':c_'.$key.'_'.$i : ':i_'.$key.'_'.$i;
                $this->parameters[$parameterName] = $value[$i];
            }
        } else {
            $parameterName = $isCondition ? ':c_'.$key : ':i_'.$key;
            $this->parameters[$parameterName] = $value; 
        }
    }
    
    private function getParamaterName($key, $value, $isCondition = true) {
        
        $result = "";
        $key = str_replace('.', '_', $key);
        
        if(is_array($value)) {
            $result = '(';
            
            for($i=0;$i<count($value);$i++) {
                $parameterName = $isCondition ? ':c_'.$key.'_'.$i : ':i_'.$key.'_'.$i;
                $result .= $parameterName . ', ';
            }
            
            $result = trim($result, ', ') . ')';
        } else {
            $parameterName = $isCondition ? ':c_'.$key : ':i_'.$key;
            $result = $parameterName;
        }        
        
        return $result;
    }
    
    private function update() 
    {
        $columnsToInsert = [];
        
        foreach($this->columns as $key => $value) {
            $this->setParametersValue($key, $value, false);
            $columnsToInsert[$key] = ':i_'.$key;
        }
        
        $rawSql = $this->queryBuilder->update(static::tableName(), $columnsToInsert)->where($this->condition)->getRawSql();
        
        $sql = str_replace('\'', '', $rawSql);
        $stmt = $this->db->conn->prepare($sql);
        foreach($this->parameters as $key => &$value) {
            $stmt->bindParam($key, $value);
        }
        
        $stmt->execute(); 
        
        $this->parameters = $this->cleanInputParameters($this->parameters);
    }
    
    private function cleanInputParameters($parameters) {
        $result = [];
        foreach($parameters as $key => $value) {
            if(strpos($key, ':i_') === false) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
    
}