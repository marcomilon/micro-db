<?php 

namespace micro\test\units\db;

use atoum;

class Connection extends atoum
{
    public function testConnection() {
        $db = $this->getConnection();
        $this->object($db)->isInstanceOf('micro\db\Connection');
    }
    
    private function getConnection() {
        $servername = "127.0.0.1";
        $username = "root";
        $password = "";
        $database = "mysql";
        
        return new \micro\db\Connection($servername, $username, $password, $database);
    }
}