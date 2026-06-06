<?php

require_once __DIR__ . '/../config/mongodb.php';

class MongoLogger {
    private $collection;

    public function __construct(string $collectionName) {
        $db = getMongoDB();
        $this->collection = $db->selectCollection($collectionName);
    }

    public function log(string $action, array $data = []) {
        $this->collection->insertOne([
            'action' => $action,
            'data' => $data,
            'timestamp' => new MongoDB\BSON\UTCDateTime()
        ]);
    }
}
