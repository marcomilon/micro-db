<?php

namespace micro\db;

class QueryBuilder
{
    
    private $sql;
    private $table;
    
    public function __construct($table)
    {
        set_error_handler([$this, 'handleError']);
        error_reporting(E_ALL | E_STRICT);   
        
        $this->table = $table;
    }
    
    public function select($columns = '*') 
    {
        $this->sql = 'SELECT';
        
        if(is_array($columns)) {
            foreach($columns as $column) {
                $this->sql .= ' `' . $column . '`,';
            }
            
            $this->sql = trim($this->sql, ',') . ' ';
        } else {
            $this->sql .= ' * ';
        }
        
        $this->sql .= 'FROM `' . $this->table . '`';
        
        return $this;
    }
    
    public function where($condition) 
    {
        $this->sql .= ' WHERE ' . $this->buildCondition($condition);
        
        return $this;
    }
    
    public function orderBy($order) 
    {
        
        $this->sql .= ' ORDER BY ';
        
        foreach($order as $column) {
            $this->sql .= '`' . $column[0] . '` ' . $column[1]. ', ';
        }
        
        $this->sql = trim($this->sql, ', ');
        
        return $this;
    }
    
    public function groupBy($group) 
    {
        
        $this->sql .= ' GROUP BY ';
        
        if(is_array($group)) {
            foreach($group as $column) {
                $this->sql .= '`' . $column . '`, ';
            }
            
            $this->sql = trim($this->sql, ', ');
        } else {
            $this->sql .= '`' . $group . '`';
        }
        
        return $this;
    }
    
    public function limit($from, $to = '') 
    {
        $this->sql .= ' LIMIT ' . $from;
        
        if(is_numeric($to)) {
            $this->sql .= ', ' . $to;
        }
        
        return $this;
    }
    
    public function getSql() 
    {
        return $this->sql;
    }
    
    
    private function buildCondition($condition) 
    {
        
        $where = '';
        
        foreach($condition as $filter) {
            if(count($filter) == 3) {
                $where .= '`' . $filter[0] . '` ' . $filter[1] . ' \'' . $filter[2] . '\'';
            }
            
            if(count($filter) == 1) {
                $where .= ' ' . strtoupper($filter[0]) . ' ';
            }
        }
        
        return $where;
    }
    
    public function handleError($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno)) {
            // This error code is not included in error_reporting, so let it fall
            // through to the standard PHP error handler
            return false;
        }
        
        echo $errfile."[".$errline."]: ". $errstr;
        /* Don't execute PHP internal error handler */
        return true;
    }
    
}