<?php

namespace micro\db;

use micro\db\QueryBuilder;

abstract class ActiveRecord
{
    
    abstract public static function tableName();
    abstract public static function dbConnection();
    
    public static function find() 
    {
        $qB = new QueryBuilder(static::dbConnection());
        $qB->select()->from(static::tableName());
        return $qB;
    }
    
    public function where($condition) 
    {
        $qb->where($condition);
        return $qb;
    }
    
    public function getRawSql() 
    {
        return $this->qB->getRawSql();
    }
    
}