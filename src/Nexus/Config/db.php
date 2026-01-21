<?php

use Nexus\Core\Environment\Env;

return [

    'default' => Env::get('DB_CONNECTION', 'mysql'),

    // DB Connection
    'connections' => [
        'mysql' => [
            'driver' => 'PDO',
            'host' => Env::get('DB_HOST'),
            'port' => Env::get('DB_PORT', 3306),
            'read' => [],
            'write' => [],

            'database' => Env::get('DB_DATABASE', 'sample'),
            'username' => Env::get('DB_USERNAME', 'sample'),
            'password' => Env::get('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options' => array_merge(Env::getOptionDB('mysql'), Env::getOptionPDO())
        ]
    ]
];
