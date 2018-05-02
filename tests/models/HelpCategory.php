<?php 

namespace micro\Model;

use micro\db\ActiveRecord;

class HelpCategory extends ActiveRecord {
    
    public $customTitle = "The custom title";
    
    public static function tableName() 
    {
        return 'help_category';
    }
    
    public static function dbConnection() 
    {
        $servername = "127.0.0.1";
        $username = "root";
        $password = "";
        $database = "mysql";
        
        return new \micro\db\Connection($servername, $username, $password, $database);
    }
}