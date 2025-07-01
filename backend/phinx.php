<?php
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'production',
        'production' => [
            'adapter' => 'mysql',
            "host" => $_SERVER['DB_HOST'],
            "name" => $_SERVER['DB_NAME'],
            "user" => $_SERVER['DB_USER'],
            "pass" => $_SERVER['DB_PASSWORD'],
            "port" => $_SERVER['DB_PORT'],
            "charset" => $_SERVER['DB_CHARSET']
        ]
    ],
    'version_order' => 'creation'
];
