<?php

namespace App\Service;

use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;
use Exception;

class LoggerService {
    private $collection = null;

    public function __construct() {
        if (!class_exists('MongoDB\Client')) {
            error_log("LoggerService: La bibliothèque MongoDB n'est pas installée.");
            return;
        }

        try {
            $uri = getenv('MONGODB_URI') ?: 'mongodb://mongo:27017';
            // On ajoute un timeout court pour ne pas bloquer le PHP si Mongo est down
            $client = new \MongoDB\Client($uri, [
                'serverSelectionTimeoutMS' => 2000
            ]);
            $this->collection = $client->selectDatabase('vite_et_gourmand_logs')->selectCollection('app_logs');
        } catch (Exception $e) {
            error_log("LoggerService: Impossible de se connecter à MongoDB. Les logs seront uniquement dans error_log.");
        }
    }

    public function log(string $action, array $data = []): void {
        $logData = [
            'action' => $action,
            'data' => $data,
            'timestamp' => new UTCDateTime()
        ];

        if ($this->collection) {
            try {
                $this->collection->insertOne($logData);
            } catch (Exception $e) {
                error_log("LoggerService Error: " . $e->getMessage());
            }
        }
        
        error_log("APP_LOG: $action - " . json_encode($data));
    }
}
