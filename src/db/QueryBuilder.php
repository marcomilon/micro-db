<?php
/**
* Licensed under the MIT license.
*
* For the full copyright and license information, please view the LICENSE file.
*
* @author Marco Milon <marco.milon@gmail.com>
* @link https://github.com/marcomilon/micro-db
*/

namespace micro\db;

class QueryBuilder
{
    /**
    * @var array the conditions to be applied in the query
    *
    * A condition is an array used to build the where statement. *Elements of 
    * the array will form the where condition. If an element of the array 
    * has only one item  is consider a logicalOperatos. If and element of 
    * the array has more than one element it consider a comparison Operator.
    *
    * For example
    *
    * A conditional array will be like this:
    *    $condition = [
    *        ['=', 'id', '1'],
    *        ['and'],
    *        ['!=', 'status', '0']
    *    ];
    */
    private $condition = [];
    
    private $parameters = [];
    
    /**
    * @var array of valid logic operators
    */
    private $logicalOperators = [
        'AND',
        'OR',
        'XOR'
    ];
    
    /**
    * @var array of valid comparison operators
    */
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
    
    /**
    * Generate a select statement.
    *
    * @param array $columns is an array of the selected columns
    *
    * @return object $this
    */
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
    
    /**
    * Generate a from statement.
    *
    * @param array $table is the name of the table 
    *
    * @return object $this
    */
    public function from($table) 
    {    
        $this->sql .= 'FROM ' . $this->quoteTableName($table);
        
        return $this;
    }
    
    /**
    * Generate a where statement.
    *
    * @param array $condition array with a condition
    *
    * @return object $this
    */
    public function where($condition) 
    {       
        $this->condition = $condition;
        $this->sql .= ' WHERE ' . $this->buildCondition($condition);
        
        return $this;
    }
    
    /**
    * Generate a orderby statement.
    *
    * @param array $order the of the columns to order by
    *
    * @return object $this
    */
    public function orderBy($order) 
    {
        
        $this->sql .= ' ORDER BY ';
        
        foreach($order as $column) {
            $this->sql .= $this->quoteColumnName($column[0]) .' '. $column[1] . ', ';
        }
        
        $this->sql = trim($this->sql, ', ');
        
        return $this;
    }
    
    /**
    * Generate a orderby statement.
    *
    * @param array $group the of the column to group by
    *
    * @return object $this
    */
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
    
    /**
    * Generate a limit statement.
    *
    * @param int $from
    * @param int $to
    *
    * @return object $this
    */
    public function limit($from, $to = '') 
    {
        $this->sql .= ' LIMIT ' . $from;
        
        if(is_numeric($to)) {
            $this->sql .= ', ' . $to;
        }
        
        return $this;
    }
    
    /**
    * Generate a select statement.
    *
    * @param string $table is the table name
    * @param array $columns are the columns name associated with the value to be inserted
    *
    * @return object $this
    */
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
    
    /**
    * Generate an update statement.
    *
    * @param string $table is the table name
    * @param array $columns are the columns name associated with the value to be updated
    *
    * @return object $this
    */
    public function update($table, $columns) 
    {
        $this->sql = 'UPDATE ' . $this->quoteTableName($table) . ' SET ';
        foreach($columns as $k => $v) {
            $this->sql .= $this->quoteColumnName($k) .' = '. $this->quoteValue($v) . ', ';
        }
        
        $this->sql = trim($this->sql, ', ');
        
        return $this;
    }
    
    /**
    * Generate a delete statement.
    * Warning no condition is attached to the stament by default. Use with care
    *
    * @param string $table is the table name
    *
    * @return object $this
    */
    public function delete($table) {
        $this->sql = 'DELETE FROM ' . $this->quoteTableName($table);
        
        return $this;
    }
    
    /**
    * Return the raw sql statement as a string.
    *
    * @return string $sqlStatement
    */
    public function getRawSql() 
    {
        return $this->sql;
    }
    
    public function getParameters() 
    {
        return $this->parameters;
    }
    
    /**
    * Builds a where condition from the array parameter.
    *
    * Example
    *    $condition = [
    *        ['=', 'id', '1'],
    *        ['and'],
    *        ['!=', 'status', '0']
    *    ];    
    *
    * @param array $condition is an array
    *
    * @return string $where
    */
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
    
    /**
    * Quotes values.
    *
    * @return string $quotedValue
    */
    public function quoteValue($value) 
    {
        return '\'' . addslashes($value) . '\'';
    }
    
    /**
    * Quotes table name using the backtick char.
    *
    * @return string $quotedTableName
    */
    public function quoteTableName($table) 
    {
        return $this->quoteBacktick($table);
    }
    
    /**
    * Quotes column name using the backtick char.
    *
    * @return string $quotedColumnName
    */
    public function quoteColumnName($column) 
    {
        return $this->quoteBacktick($column);
    }
    
    /**
    * Gets the condition to apply
    *
    * @return string $condition
    */
    public function getCondition() 
    {
        return $this->condition;
    }
    
    /**
    * Quotes the backtick char itself.
    *
    * @return string $quotedString
    */
    private function quoteBacktick($value) 
    {
        return '`' . str_replace('`', '``' ,$value) . '`';
    }
    
    /**
    * Handles the thrid parameter in the condition array
    *
    * For example
    * $condition = [
    *    ['in', 'lastname', ['test1', 'test2', 'test3']]
    * ];
    *
    * @return string $condition
    */
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
    
    /**
    * Validate if the logicalOperator is supported.
    *
    * @throws Exception 
    * @return bool $operator
    */
    private function validateLogicalOperator($value) 
    {
        $operator = strtoupper(trim($value));
        $isValid = in_array($operator, $this->logicalOperators);
        
        if($isValid === true) {
            return $operator;
        } 
        
        throw new \Exception("Logical operator $value is not supported.");
    }
    
    /**
    * Validate if the comparisonOperator is supported.
    *
    * @throws Exception 
    * @return bool $operator
    */
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