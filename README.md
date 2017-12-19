# Micro-db 

Micro-db is a simpre ORM library. It has 3 files:

1. ActiveRecord.php
2. Connection.php
3. QueryBuilder.php

## Installing via Composer 

## Installing Composer

First you need to install Composer. You may do so by following the instructions at [getcomposer.org](https://getcomposer.org/download/).

## How to use the ActiveRecord

First create an active record class. You need to implement two methods: tableName and dbConnection.

```php
use micro\db\ActiveRecord;

class Books extends ActiveRecord {
    
    public static function tableName() 
    {
        return 'books';
    }
    
    public static function dbConnection() 
    {
        $servername = "127.0.0.1";
        $username = "root";
        $password = "fullstack";
        $database = "mysql";
        
        return new \micro\db\Connection($servername, $username, $password, $database);
    }
}
```
Then you can instantiate the class.

```php
$book = new Books();
$book->title('This is the title of my book');
$book->save();
```

## How to use the QueryBuilder

The queryBuilder build sql query to be sent to the database. For example:

```php
$table = 'home';
$qB = new \micro\db\QueryBuilder();
$columns = [
    'id',
    'name',
    'address'
];
$sql = $qB->select($columns)->from($table)->getRawSql();
```

The variable $sql is equal to "SELECT `id`, `name`, `address` FROM `home`

#### Credits

Made in Perú. Thanks to [fullstack.pe](https://www.fullstack.pe/)
