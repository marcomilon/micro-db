<?php

namespace micro\db;

class QueryBuilder
{
  
    private $sql;
    private $condition;
    
    private $logicalOperators = [
        'AND',
        'OR',
        'XOR'
    ];
    
    private $comparisonOperators  = [
        '=',
        '>',
        '>=',
        '<',
        '<=',
        '!=',
        'BETWEEN',
        'IN'
    ];
    
    public function __construct()
    {    
        set_error_handler([$this, 'handleError']);
        error_reporting(E_ALL | E_STRICT);        
    }
    
    public function select($columns = '*') 
    {
        $this->sql = 'SELECT ';
        
        if(is_array($columns)) {
            foreach($columns as $column) {
                $this->sql .= $this->quoteColumnName($column) . ', ';
            }
            
            $this->sql = trim($this->sql, ', ') . ' ';
        } else {
            $this->sql .= '* ';
        }
        
        return $this;
    }
    
    public function from($table) 
    {    
        $this->sql .= 'FROM ' . $this->quoteTableName($table);
        
        return $this;
    }
    
    public function where($condition) 
    {
        $this->condition = $condition;        
        $this->sql .= ' WHERE ' . $this->buildCondition($this->condition);
        
        return $this;
    }
    
    public function orderBy($order) 
    {
        
        $this->sql .= ' ORDER BY ';
        
        foreach($order as $column) {
            $this->sql .= $this->quoteColumnName($column[0]) .' '. $column[1] . ', ';
        }
        
        $this->sql = trim($this->sql, ', ');
        
        return $this;
    }
    
    public function groupBy($group) 
    {
        
        $this->sql .= ' GROUP BY ';
        
        if(is_array($group)) {
            foreach($group as $column) {
                $this->sql .= $this->quoteColumnName($column) . ', ';
            }
            
            $this->sql = trim($this->sql, ', ');
        } else {
            $this->sql .= $this->quoteColumnName($group);
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
    
    public function insert($table, $columns) 
    {
        $this->sql = 'INSERT INTO ' . $this->quoteTableName($table) . ' ';

        $columnNames = '(';
        $columnValues = '(';
        foreach($columns as $k => $v) {
            $columnNames .= $this->quoteColumnName($k) . ', ';
            $columnValues .= $this->quoteValue($v) . ', ';
        }
        
        $this->sql .= trim($columnNames, ', ') . ')';
        $this->sql .= ' VALUES ';
        $this->sql .= trim($columnValues, ', ') . ')';
        
        return $this;        
    }
    
    public function update($table, $columns) 
    {
        $this->sql = 'UPDATE ' . $this->quoteTableName($table) . ' SET ';
        foreach($columns as $k => $v) {
            $this->sql .= $this->quoteColumnName($k) .' = '. $this->quoteValue($v) . ', ';
        }
        
        $this->sql = trim($this->sql, ', ');
        
        return $this;
    }
    
    public function delete($table) {
        $this->sql = 'DELETE FROM ' . $this->quoteTableName($table);
        
        return $this;
    }
    
    public function getRawSql() 
    {
        return $this->sql;
    }
    
    private function buildCondition($condition) 
    {
        $where = '';
        
        foreach($condition as $filter) {
            switch(count($filter)) {
                case 1:
                    $where .= ' ' . $this->validateLogicalOperator(($filter[0])) . ' ';
                break;
                case 3:
                    $where .= $this->quoteColumnName($filter[1]) .' '. $this->validateComparisonOperators($filter[0]) .' '. $this->handleThridParameter($filter[2]);
                break;
                case 4:
                    $where .= $this->quoteColumnName($filter[1]) .' '. $this->validateComparisonOperators($filter[0]) .' '. $this->quoteValue($filter[2]) . ' AND ' . $this->quoteValue($filter[3]);
                break;
            }
        }
        
        return $where;
    }
    
    public function quoteValue($value) 
    {
        return '\'' . addslashes($value) . '\'';
    }
    
    public function quoteTableName($table) 
    {
        return $this->quoteBacktick($table);
    }
    
    public function quoteColumnName($column) 
    {
        return $this->quoteBacktick($column);
    }
    
    public function getCondition() 
    {
        return $this->condition;
    }
    
    private function quoteBacktick($value) 
    {
        return '`' . str_replace('`', '``' ,$value) . '`';
    }
    
    private function handleThridParameter($param) 
    {
        $result = '';
        if(is_array($param)) {
            $result = '(';
            foreach($param as $v) {
                $result .= $this->quoteValue($v) . ', ';
            }
            $result = trim($result, ', ') . ')';
        } else {
            $result = $this->quoteValue($param);
        }
        
        return $result;
    }
    
    private function validateLogicalOperator($value) 
    {
        $operator = strtoupper(trim($value));
        $isValid = in_array($operator, $this->logicalOperators);
        
        if($isValid === true) {
            return $operator;
        } 
        
        throw new \Exception("Logical operator $value is not supported.");
    }
    
    private function validateComparisonOperators($value) 
    {
        $operator = strtoupper(trim($value));
        $isValid = in_array($operator, $this->comparisonOperators);
        
        if($isValid === true) {
            return $operator;
        } 
        
        throw new \Exception("Comparison operator $value is not supported.");
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