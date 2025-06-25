<?php

use Illuminate\Support\Str;

// Decode PLATFORM_RELATIONSHIPS
$relationships = json_decode(base64_decode(env('PLATFORM_RELATIONSHIPS', '')), true);

// Extract services if available
$mysqlConfig        = $relationships['mariadb'][0] ?? null;
dd($mysqlConfig);
$redisCacheConfig   = $relationships['rediscache'][0] ?? null;
$redisSessionConfig = $relationships['redissession'][0] ?? null;

return [

    // Default DB connection
    'default' => env('DB_CONNECTION', 'mysql'),

    'connections' => [

        // SQLite
        'sqlite' => [
            'driver'                  => 'sqlite',
            'url'                     => env('DB_URL'),
            'database'                => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix'                  => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        // MySQL (fallback to PLATFORM_RELATIONSHIPS if set)
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => env('DB_HOST', $mysqlConfig['host'] ?? '127.0.0.1'),
            'port'        => env('DB_PORT', $mysqlConfig['port'] ?? 3306),
            'database'    => env('DB_DATABASE', $mysqlConfig['path'] ?? 'laravel', '/'),
            'username'    => env('DB_USERNAME', $mysqlConfig['username'] ?? 'root'),
            'password'    => env('DB_PASSWORD', $mysqlConfig['password'] ?? ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset'     => env('DB_CHARSET', 'utf8mb4'),
            'collation'   => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
            'options'     => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // Alias mariadb
        'mariadb' => [
            'driver'      => 'mysql',
            'host'        => env('DB_HOST', $mysqlConfig['host'] ?? '127.0.0.1'),
            'port'        => env('DB_PORT', $mysqlConfig['port'] ?? 3306),
            'database'    => env('DB_DATABASE', ltrim($mysqlConfig['path'] ?? 'laravel', '/')),
            'username'    => env('DB_USERNAME', $mysqlConfig['username'] ?? 'root'),
            'password'    => env('DB_PASSWORD', $mysqlConfig['password'] ?? ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset'     => env('DB_CHARSET', 'utf8mb4'),
            'collation'   => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
            'options'     => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        // PostgreSQL
        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => env('DB_HOST', '127.0.0.1'),
            'port'     => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
            'sslmode'  => 'prefer',
        ],

        // SQL Server
        'sqlsrv' => [
            'driver'   => 'sqlsrv',
            'host'     => env('DB_HOST', 'localhost'),
            'port'     => env('DB_PORT', '1433'),
            'database' => env('DB_DATABASE', 'laravel'),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset'  => env('DB_CHARSET', 'utf8'),
            'prefix'   => '',
        ],

    ],

    // Migration table
    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    // Redis
    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster'    => env('REDIS_CLUSTER', 'redis'),
            'prefix'     => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_database_'),
            'persistent' => env('REDIS_PERSISTENT', false),
        ],

        'default' => [
            'host'     => env('REDIS_HOST', $redisSessionConfig['host'] ?? '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', $redisSessionConfig['password'] ?? null),
            'port'     => env('REDIS_PORT', $redisSessionConfig['port'] ?? 6379),
            'database' => env('REDIS_DB', 0),
        ],

        'cache' => [
            'host'     => env('REDIS_HOST', $redisCacheConfig['host'] ?? '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', $redisCacheConfig['password'] ?? null),
            'port'     => env('REDIS_PORT', $redisCacheConfig['port'] ?? 6379),
            'database' => env('REDIS_CACHE_DB', 1),
        ],

    ],

];
