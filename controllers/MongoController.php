<?php
namespace controllers;

use base\core\Controller;
use base\mongo\Connection;
use App;
use MongoId;
use MongoDate;
/**
 * Controller for mongo options
 */
Class MongoController extends Controller
{
    public $mongo;

    public function __construct() {
        $mongoConfigs = App::$config['mongo'];
        $server = $_GET['server'];

        foreach ($mongoConfigs as $mongoConfig) {
            if ($server === $mongoConfig['server']) {
                $dsn = $mongoConfig['dsn'];
                break;
            }
        }

        if (empty($dsn)) {
            $dsn = $mongoConfigs[0]['dsn'];
        }

        $this->mongo = new Connection($dsn);
    }

    /**
     * The databases page
     */
    public function actionIndex() {
        $this->render('/index', array('databases' => $this->mongo->getDatabases()));
    }

    /**
     * Drop a database
     */
    public function actionDropDb() {
        $dbName = $_GET['db'];
        if(!empty($dbName)) {
            $this->mongo->selectDatabase($dbName)->drop();
            $dbs = $this->mongo->getDatabases();
            $this->render('/index', array('databases' => $dbs));
        }
    }

    /**
     * Show a collection data
     */
    public function actionCollection() {
        $dbName = $_GET['db'];
        $collectionName = $_GET['collection'];
        if(!empty($dbName) && !empty($collectionName)) {
            $dbs = $this->mongo->getDatabases();
            $data = $this->mongo->findAll($dbName, $collectionName);
            $data['databases'] = $dbs;
            $this->render('/index', $data);
        }
    }

    /**
     * Delete a row in collection
     */
    public function actionDelete() {
        $dbName = $_GET['db'];
        $collectionName = $_GET['collection'];
        $id = $_GET['id'];
        if(!empty($dbName) && !empty($collectionName) && !empty($id)) {
            $dbs = $this->mongo->getDatabases();
            $collection = $this->mongo->selectCollection($dbName, $collectionName);
            $collection->remove(array('_id' => new MongoId($id)));
            $data = $this->mongo->findAll($dbName, $collectionName);
            $data['databases'] = $dbs;
            $this->render('/index', $data);
        }
    }

    /**
     * Get a row with id
     */
    public function actionView() {
        $dbName = $_GET['db'];
        $collectionName = $_GET['collection'];
        $id = $_GET['id'];
        if(!empty($dbName) && !empty($collectionName) && !empty($id)) {
            $collection = $this->mongo->selectCollection($dbName, $collectionName);
            $row = $collection->findOne(array('_id' => new MongoId($id)));
            $this->sendResponse($row);
        }
    }


    /**
     * Update and create a row
     */
    public function actionUpdate() {
        $dbName = $_GET['db'];
        $collectionName = $_GET['collection'];
        $content = $_POST['content'];
        if(!empty($dbName) && !empty($collectionName)) {
            $collection = $this->mongo->selectCollection($dbName, $collectionName);
            $content = json_decode($content, true);
            $content = $this->_formateData($content);

            $result = $collection->save($content);
            $dbs = $this->mongo->getDatabases();
            $data = $this->mongo->findAll($dbName, $collectionName);
            $data['databases'] = $dbs;
            $this->render('/index', $data);
        }
    }


    /**
     * Formate the json data to mongo data
     * @param  array $content  the json data
     * @return array           the mongo data
     */
    private function _formateData($content) {
        foreach ($content as &$value) {
            if (is_array($value)) {
                if(isset($value['$id'])) {
                    // A MongoId
                    $value = new MongoId($value['$id']);
                } else if (isset($value['sec']) && isset($value['usec'])) {
                    // A MongoDate
                    $value = new MongoDate($value['sec'], $value['usec']);
                } else {
                    // Normal array
                    $value = $this->_formateData($value);
                }
            }
        }
        return $content;
    }
}