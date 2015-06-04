<?php
namespace PhpMongoAdmin\Base;

use PhpMongoAdmin\Framework;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Controller is a base controller
 * @package PhpMongoAdmin\Base
 */
class Controller extends Component {

    /**
     * @var Request the request object in controller
     */
    protected $request;

    /**
     * Init the controller
     * This method is meant to be overwritten to init controller.
     * If you override this method, your code should look like the following:
     *
     * ```php
     * public function init($request)
     * {
     *     parent::init($request)
     *     // your custom code here
     * }
     * ```
     *
     * @param Request $request
     * @return bool|void
     */
    public function init(Request $request) {
        $this->request = $request;
    }

    /**
     * Get mongoDB client
     * @return MongoClient
     */
    public function getMongoClient() {
    	return Framework::$mongo;
    }

    /**
     * Get all databases
     * @param $db
     * @return mixed
     */
    public function getDatabase($db) {
    	return static::getMongoClient()->selectDB($db);

    }

    /**
     * Get all collections
     * @param $db
     * @param $collection
     * @return mixed
     */
    public function getCollection($db, $collection) {
    	return static::getDatabase($db)->selectCollection($collection);
    }
}