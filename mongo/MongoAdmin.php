<?php
Class MongoAdmin
{
    public $connection;
    public $db;

    public function __construct($dsn) {
        if(!class_exists('MongoClient')) {
            throw new Exception("No mongo client");
        }
        $this->connection = new MongoClient($dsn);
    }

    public function dbs() {
        return $this->connection->listDBs();
    }

    public function selectDB($db) {
        $this->db = $this->connection->selectDB($db);
        return $this->db;
    }

    public function selectCollection($db, $collection) {
        return $this->selectDB($db)->selectCollection($collection);
    }
}
