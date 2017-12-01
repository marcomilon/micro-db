<?php

namespace micro\db;

class Connection
{
    
    private $conn;
    
    public function __construct($servername, $username, $password, $database) 
    {
        $this->conn = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    
    public function quoteValue($value) {
        return $this->conn->quote($value);
    }
    
    public function quoteTableName($table) {
        return $this->quoteBacktick($table);
    }
    
    public function quoteColumnName($column) {
        return $this->quoteBacktick($column);
    }
    
    public function execute() {
        
    }
    
    public function query() {
        
    }
    
    private function quoteBacktick($value) {
        return '`' . $value . '`';
    }
}