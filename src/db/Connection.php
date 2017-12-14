<?php

namespace micro\db;

class Connection
{
    
    public $conn;
    
    public function __construct($servername, $username, $password, $database) 
    {
        $this->conn = new \PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
        $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    
}