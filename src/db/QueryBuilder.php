<?php

namespace micro\db;

class QueryBuilder
{
    private $table;
    
    public function __construct($table)
    {
        set_error_handler([$this, 'handleError']);
        error_reporting(E_ALL | E_STRICT);
        
        $this->table = $table;    
    }
    
    public function select($columns = [], $conditions = [], $options = []) 
    {
        $query = 'SELECT';
        
        if(!empty($columns)) {
            foreach($columns as $column) {
                $query .= ' `' . $column . '`,';
            }
            
            $query = trim($query, ',') . ' ';
        } else {
            $query .= ' * ';
        }
        
        $query .= 'FROM `' . $this->table . '`';
        
        if(!empty($conditions)) {
            $query .= ' WHERE ' . $this->buildCondition($conditions);
        }
        
        return $query;
    }
    
    private function buildCondition($conditions) {
        
        $where = '';
        
        foreach($conditions as $condition) {
            if(count($condition) == 3) {
                $where .= '`' . $condition[0] . '` ' . $condition[1] . ' \'' . $condition[2] . '\'';
            }
            
            if(count($condition) == 1) {
                $where .= ' ' . strtoupper($condition[0]) . ' ';
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