# Micro-db 

[![Latest Stable Version](https://poser.pugx.org/fullstackpe/micro-db/v/stable)](https://packagist.org/packages/fullstackpe/micro-db) [![Build Status](https://travis-ci.org/marcomilon/micro-db.svg?branch=master)](https://travis-ci.org/marcomilon/micro-db)

Micro-db is a lightweight ORM library. 

### Installation

First you need to install Composer. You may do so by following the instructions 
at [getcomposer.org](https://getcomposer.org/download/). 
Then run

> composer require fullstackpe/micro-db

If you prefer you can create a composer.json in your project folder.

```json
{
    "require": {
        "fullstackpe/micro-db": "^1.1"
    }
}
```

Then run the command 

> composer install

### The ActiveRecord Class

If you have a table called `book`. You need to create an active record Class 
called `Book` that extends the Class `micro\db\ActiveRecord`. The class `Book` 
needs to implement two methods: tableName() and dbConnection().

#### Example

```php
use micro\db\ActiveRecord;

class Book extends ActiveRecord {
    
    public static function tableName() 
    {
        return 'book';
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

#### Example

```php
// Create a new book
$book = new Book();
$book->title('This is the title of my book');
$book->save();

// fetchs all books
$books = Book::find()->all();
foreach($books as $book) {
    echo $book->title;
}

// search for one book
$condition = [
    ['=', 'id', '1']
];
$book = Book::find()->where($condition)->one();
echo $book->title
```

### The QueryBuilder Class

The queryBuilder class builds a Sql statement. 

#### Example

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

The variable `$sql` is equal to the string "SELECT `id`, `name`, `address` FROM `home`".

### Contribution

Feel free to contribute! Just create a new issue or a new pull request.

### License

This library is released under the MIT License.
