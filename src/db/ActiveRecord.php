<?php

namespace micro\db;

use micro\db\QueryBuilder;

abstract class ActiveRecord
{
    
    protected $db;
    private $columns = [];
    private $queryBuilder;
    private static $isUpdate = false;
    
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
        } else {
            $model = self::factory();
            $condition = $this->queryBuilder->getCondition();
            $rawSql = $model->queryBuilder->select()->from(static::tableName())->where($condition)->getRawSql();
            $result = $model->one();
            
            foreach($result as $k => $v) {
                $this->columns[$k] = $v;
            }
            
            return $this->columns[$name];
        }
    }
    
    public static function find() 
    {
        $model = self::factory();
        self::$isUpdate = true;
        $model->queryBuilder->select()->from(static::tableName());
        return $model;
    }
    
    public function where($condition) 
    {
        $this->queryBuilder->where($condition);
        return $this;
    }
    
    public function save() 
    {
        $model = self::factory();
        
        $rawSql = $model->queryBuilder->insert(static::tableName(), $this->columns)->getRawSql();
        
        if(self::$isUpdate) {
            $condition = $this->queryBuilder->getCondition();
            $rawSql = $model->queryBuilder->update(static::tableName(), $this->columns)->where($condition)->getRawSql();
        }
        
        $model->exec();        
    }
    
    public function delete() 
    {
        $condition = $this->queryBuilder->getCondition();
        
        if(empty($condition)) {
            throw new \Exception("Cannot delete data without condition.");
        }
        
        $model = self::factory();
        $rawSql = $model->queryBuilder->delete(static::tableName())->where($condition)->getRawSql();
        $model->exec($rawSql);
    }
    
    public function one() 
    {
        $result = $this->db->conn->query($this->queryBuilder->getRawSql());
        return $result->fetch(\PDO::FETCH_OBJ);
    }
    
    public function all() 
    {
        $result = $this->db->conn->query($this->queryBuilder->getRawSql());
        return $result->fetchAll(\PDO::FETCH_OBJ);
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
    
}