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
     * Gets "parameter" value form query.
     *
     * This method is mainly useful for libraries that want to provide some flexibility.
     * If $key is null, it will return all parameters.
     *
     * Order of precedence: GET, PATH, POST
     *
     * @param string  $key     the key
     * @param mixed   $default the default value, only using when $key is not null
     * @param Boolean $deep    is parameter deep in multidimensional array, only using when $key is not null
     *
     * @return mixed
     */
    public function getQuery($key = null, $default = null, $deep = false) {
        $params = null;

        if (is_null($key)) {
            // get all query parameters without $key
            $params = $this->request->query->all();
        } else {
            $params = $this->request->query->get($key, $default, $deep);
        }

        return $params;
    }

    /**
     * Gets "parameter" value form rawbody.
     *
     * This method is mainly useful for libraries that want to provide some flexibility.
     * If $key is null, it will return all parameters.
     *
     * @param string  $key     the key
     * @param mixed   $default the default value, only using when $key is not null
     *
     * @return mixed
     */
    public function getParam($key = null, $default = null) {
        $params = $this->request->getContent();

        if (!empty($params)) {
            $params = json_decode($params, true);
        }

        if (!is_null($key)) {
            $params = array_key_exists($key, $params) ? $params[$key] : $default;
        }

        return $params;
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