<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

function getMongoDB() {
    static $client = null;
    if ($client === null) {
        $uri = getenv('MONGODB_URI') ?: 'mongodb://localhost:27017';
        $client = new MongoDB\Client($uri);
    }
    return $client->selectDatabase('vite_et_gourmand_logs');
}
