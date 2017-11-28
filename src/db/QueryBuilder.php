<?php

namespace micro\db;

class QueryBuilder
{
    private $table;
    private $query;
    
    public function __construct($table)
    {
        set_error_handler([$this, 'handleError']);
        error_reporting(E_ALL | E_STRICT);
        
        $this->table = $table;    
    }
    
    public function select($columns = '*') 
    {
        $this->query = 'SELECT';
        
        if(is_array($columns)) {
            foreach($columns as $column) {
                $this->query .= ' `' . $column . '`,';
            }
            
            $this->query = trim($query, ',') . ' ';
        } else {
            $this->query .= ' * ';
        }
        
        return $this;
    }
    
    public function query() {
        return $this->query;
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