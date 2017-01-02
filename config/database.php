<?php
/**
 * -----------------------------------
 * File  : database.php
 * User  : Mohamed Helal
 * Email : mohamedhelal123456@gmail.com
 * Site  : {URL}
 * -----------------------------------
 */
return [
    /**
     * default connection driver
     */
    'default' => 'mysql',
    /**
     * all connection drivers
     */
    'drivers' => [
        'mysql' => [
            'driver'    => ArPHP\Databases\Drivers\Mysql\Connection::class,
            'host'      => 'localhost',
            'port'      => false,
            'database'  => 'arphp',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'fetchMode' => PDO::FETCH_OBJ
        ]
    ]
];