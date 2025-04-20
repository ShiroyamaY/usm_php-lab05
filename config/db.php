<?php

return [
    'driver' => $_ENV['DB_DRIVER'] ?? 'mysql',
    'host'     => $_ENV['MYSQL_HOST']     ?? '127.0.0.1',
    'port'     => $_ENV['MYSQL_PORT']     ?? '3306',
    'name'   => $_ENV['MYSQL_DATABASE'] ?? 'db',
    'username' => $_ENV['MYSQL_USER']     ?? 'user',
    'password' => $_ENV['MYSQL_PASSWORD'] ?? 'password',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];