<?php
/**
 * Controller for mongo options
 */
Class MongoController
{
    /**
     * @var MongoAdmin
     */
    public $mongo;

    public function __construct() {
        $dsn = App::$config['mongo'];
        $this->mongo = new MongoAdmin($dsn);
    }

    /**
     * Show the databases and their collections
     * @return array the databases
     */
    private function _dbs() {
        $result = $this->mongo->dbs();
        $databses =& $result['databases'];
        foreach ($databses as &$db) {
            $db['collections'] = $this->mongo->selectDB($db['name'])->getCollectionNames();
        }
        return $result;
    }

    /**
     * Get the collection data with database name and collection name
     * @param  string $dbName         database name
     * @param  string $collectionName collection name
     * @return array                  collection data, database name and collection name
     */
    private function _data($dbName, $collectionName) {
        $data = array();
        $collection = $this->mongo->selectCollection($dbName, $collectionName);
        $cursor = $collection->find();
        $data['documents'] = array();
        $data['keys'] = array();
        foreach ($cursor as $document) {
            $data['documents'][] = $document;
            $data['keys'] = array_unique(array_merge($data['keys'], array_keys($document)));
        }
        $data['dbName'] = $dbName;
        $data['collectionName'] = $collectionName;
        return $data;
    }

    /**
     * The databases page
     */
    public function actionIndex() {
        $this->render('/index', $this->_dbs());
    }

    /**
     * Show a collection data
     */
    public function actionCollection() {
        $dbName = $_GET['db'];
        $collectionName = $_GET['collection'];
        if(!empty($dbName) && !empty($collectionName)) {
            $dbs = $this->_dbs();
            $data = $this->_data($dbName, $collectionName);
            $this->render('/index', array_merge($data, $dbs));
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
            $dbs = $this->_dbs();
            $collection = $this->mongo->selectCollection($dbName, $collectionName);
            $collection->remove(array('_id' => new MongoId($id)));
            $data = $this->_data($dbName, $collectionName);
            $this->render('/index', array_merge($data, $dbs));
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
            $dbs = $this->_dbs();
            $data = $this->_data($dbName, $collectionName);
            $this->render('/index', array_merge($data, $dbs));
        }
    }

    /**
     * Drop a database
     */
    public function actionDropDb() {
        $dbName = $_GET['db'];
        if(!empty($dbName)) {
            $this->mongo->selectDB($dbName)->drop();
            $dbs = $this->_dbs();
            $this->render('/index', $dbs);
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

    /**
     * Send the json response
     * @param  array  $result the result for frontend
     */
    function sendResponse($result = array()) {
        echo json_encode($result);
    }

    /**
     * Render the page with params
     * @param  string $page    frontend page path
     * @param  array  $content page params
     */
    function render($page, $content) {
        // extract the page params
        extract($content);
        // include the frontend page
        include BASE_PATH . '/views' . $page . '.php';
    }
}