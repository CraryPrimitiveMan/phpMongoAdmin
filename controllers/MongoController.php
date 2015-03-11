<?php
namespace controllers;

use base\core\Controller;
use base\mongo\Connection;
use App;
use MongoId;
use base\mongo\Shell;
/**
 * Controller for mongo options
 */
Class MongoController extends Controller
{
    public $mongo;

    public $servers;

    public function __construct() {
        $mongoConfigs = App::$config['mongo'];
        $server = $_GET['server'];

        foreach ($mongoConfigs as $mongoConfig) {
            if ($server === $mongoConfig['name']) {
                $config = $mongoConfig;
                break;
            }
        }
        if (empty($config)) {
            $config = $mongoConfigs[0];
        }

        $this->mongo = new Connection($config['dsn'], $config['name']);
        $this->servers = $mongoConfigs;
    }

    /**
     * The databases page
     */
    public function actionIndex() {
        $data = array(
            'databases'     => $this->mongo->getDatabases(),
            'serverName'    => $this->mongo->serverName,
            'servers'       => $this->servers,
            'shell'         => Shell::queryDb()
        );
        $this->render('/index', $data);
    }

    /**
     * Drop a database
     */
    public function actionDropDb() {
        $dbName = $_GET['db'];
        if (!empty($dbName)) {
            $result = $this->mongo->selectDatabase($dbName)->drop();
            $dbs = $this->mongo->getDatabases();
            $data = array(
                'databases'     => $dbs,
                'serverName'    => $this->mongo->serverName,
                'servers'       => $this->servers,
                'shell'         => Shell::dropDb($dbName)
            );
            $this->render('/index', $data);
        }
    }

    /**
     * Show a collection data
     */
    public function actionCollection() {
        $dbName = $_GET['db'];
        $collectionName = $_GET['collection'];
        if (!empty($dbName) && !empty($collectionName)) {
            $dbs = $this->mongo->getDatabases();
            $data = $this->mongo->findAll($dbName, $collectionName);
            $data['databases'] = $dbs;
            $data['servers'] = $this->servers;
            $data['shell'] = Shell::query($collectionName);
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
        if (!empty($dbName) && !empty($collectionName) && !empty($id)) {
            $dbs = $this->mongo->getDatabases();
            $collection = $this->mongo->selectCollection($dbName, $collectionName);
            $collection->remove(array('_id' => new MongoId($id)));
            $data = $this->mongo->findAll($dbName, $collectionName);
            $data['databases'] = $dbs;
            $data['servers'] = $this->servers;
            $data['shell'] = Shell::delete($collectionName, $id);
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
        if (!empty($dbName) && !empty($collectionName) && !empty($id)) {
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
        if (!empty($dbName) && !empty($collectionName)) {
            $collection = $this->mongo->selectCollection($dbName, $collectionName);
            $content = $this->mongo->convert2Document($content);
            $oldData = array();
            if (isset($content['_id']) && $content['_id'] instanceof \MongoId) {
                $oldData = $collection->findOne(array('_id' => $content['_id']));
            } else {
                $content['_id'] = new \MongoId();
            }
            $result = $collection->save($content);
            $newData = $collection->findOne(array('_id' => $content['_id']));
            $result['shell'] = Shell::update($collectionName, $newData, $oldData);
            $this->sendResponse($result);
        }
    }
}