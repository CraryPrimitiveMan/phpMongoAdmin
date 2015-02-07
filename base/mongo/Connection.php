<?php
namespace base\mongo;

use Exception;
use base\mongo\Convert;
/**
 * Connection represents a connection to a MongoDb server.
 **/
class Connection
{
    /**
     * @var string host:port
     *
     * Correct syntax is:
     * mongodb://[username:password@]host1[:port1][,host2[:port2:],...][/dbname]
     * For example:
     * mongodb://localhost:27017
     * mongodb://developer:password@localhost:27017
     * mongodb://developer:password@localhost:27017/mydatabase
     */
    public $dsn;

    /**
     * @var string name of a server
     */
    public $serverName;

    /**
     * @var string name of the Mongo database to use by default.
     * If this field left blank, connection instance will attempt to determine it from
     * [[options]] and [[dsn]] automatically, if needed.
     */
    public $defaultDatabaseName;

    /**
     * @var \MongoClient Mongo client instance.
     */
    public $mongoClient;

    /**
     * @var Database[] list of Mongo databases name
     */
    private $_databases = array();

    public function __construct($dsn, $server) {
        $this->dsn = $dsn;
        $this->serverName = $server;
        $this->open();
    }

    /**
     * Returns the Mongo collections.
     * @return Database[]
     */
    public function getDatabases() {
        $database = array();
        $databases = array();
        foreach ($this->_databases as $db) {
            $database['name'] = $db;
            $database['collections'] = $this->selectDatabase($db)->getCollectionNames();
            $databases[] = $database;
        }
        return $databases;
    }

    /**
     * Closes the currently active DB connection.
     * It does nothing if the connection is already closed.
     */
    public function close()
    {
        if ($this->mongoClient !== null) {
            $this->mongoClient = null;
            $this->_databases = array();
        }
    }

    /**
     * Selects the database with given name.
     * @param string $name database name.
     * @return Database database instance.
     */
    public function selectDatabase($name)
    {
        $this->open();
        return $this->mongoClient->selectDB($name);
    }

    /**
     * Selects the collection with given name.
     * @param string $db database name.
     * @param string $collection collection name.
     * @return Database database instance.
     */
    public function selectCollection($db, $collection)
    {
        $this->open();
        return $this->mongoClient->selectDB($db)->selectCollection($collection);
    }

    /**
     * Get the collection data with database name and collection name
     * @param  string $dbName         database name
     * @param  string $collectionName collection name
     * @return array                  collection data, database name and collection name
     */
    public function findAll($dbName, $collectionName) {
        $data = array();
        $collection = $this->selectCollection($dbName, $collectionName);
        $cursor = $collection->find();
        $data['documents'] = array();
        foreach ($cursor as $document) {
            $data['documents'][] = array('content' => $this->convert2Str($document), 'id' => $document['_id'] . '');
        }
        $data['dbName'] = $dbName;
        $data['collectionName'] = $collectionName;
        $data['serverName'] = $this->serverName;
        return $data;
    }

    public function convert2Str($document) {
        $jsonData = Convert::document2Json($document);
        return Convert::Json2Str($jsonData);
    }

    public function convert2Document($str) {
        $jsonData = Convert::str2Json($str);
        return Convert::Json2Document($jsonData);
    }
    /**
     * Establishes a Mongo connection.
     * It does nothing if a Mongo connection has already been established.
     * @throws Exception if connection fails
     */
    public function open()
    {
        if ($this->mongoClient === null) {
            if (empty($this->dsn)) {
                throw new Exception('dsn cannot be empty.');
            }

            try {
                $this->mongoClient = new \MongoClient($this->dsn);
            } catch (Exception $e) {
                throw new Exception('connect failed.');
            }
            try {
                $databases = $this->mongoClient->listDBs();

                if(!empty($databases['databases'][0])) {
                    $this->defaultDatabaseName = $this->_databases['databases'][0]['name'];
                    foreach ($databases['databases'] as $db) {
                        $this->_databases[] = $db['name'];
                    }
                }
            } catch (Exception $e) {
                if (preg_match('/^mongodb:\\/\\/.+\\/([^?&]+)/s', $this->dsn, $matches)) {
                    $this->defaultDatabaseName = $matches[1];
                    $this->_databases[] = $matches[1];
                } else {
                    throw new Exception('connect database list failed.');
                }
            }
        }
    }
}