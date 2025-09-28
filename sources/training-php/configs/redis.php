<?php

use Predis\Client;

require_once __DIR__ . '/../vendor/autoload.php';

function createRedisClient(): Client {
    $host = getenv('REDIS_HOST') ?: '127.0.0.1';
    $port = getenv('REDIS_PORT') ?: '6379';
    $username = getenv('REDIS_USERNAME') ?: null;
    $password = getenv('REDIS_PASSWORD') ?: null;
    $database = getenv('REDIS_DATABASE') ?: 0;

    $parameters = [
        'host' => $host,
        'port' => (int)$port,
        'database' => (int)$database,
    ];

    if ($username !== null && $username !== '') {
        $parameters['username'] = $username;
    }
    if ($password !== null && $password !== '') {
        $parameters['password'] = $password;
    }

    return new Client($parameters);
}



