<?php
namespace PhpMongoAdmin;

use Exception;
use MongoClient;
use PhpMongoAdmin\Base\Component;
use PhpMongoAdmin\Base\Formatter;
use PhpMongoAdmin\Exception\NotFoundException;
use PhpMongoAdmin\Exception\ServerException;
use PhpMongoAdmin\Exception\BadMethodCallException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Framework is a helper class serving common framework functionalities.
 * @author Harry Sun
 */
class Framework extends Component
{
    /**
     * @var string the default action
     */
    public static $defaultAction = 'index';
    /**
     * @var string the default controller
     */
    public static $defaultController = 'default';
    /**
     * @var array the mongo config
     */
    public static $config;
    /**
     * @var array the selected mongo server config
     */
    public static $server;
    /**
     * @var MongoClient
     */
    public static $mongo;

    /**
     * @var array
     */
    private static $actionMap = [
        'create' => ['POST'],
        'update' => ['PUT'],
        'view' => ['GET'],
        'index' => ['GET'],
        'delete' => ['DELETE'],
    ];

    /**
     * Init the framework
     * @param $config
     * @return void
     */
    public function init($config)
    {
        static::$config = $config;
    }

    /**
     * Handle the Request
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request)
    {
        $path = $request->query->get('r');
        $request->query->remove('r');

        try {
            // parse the path to controllerName/actionName/server/db and collection
            list($controllerName, $actionName, $server, $db, $collection) = $this->parsePath($path);
            $this->generateMongo($server);
            $this->checkMethod($actionName, $request);
            $controller = new $controllerName($request);
            // call the action
            $data = call_user_func_array([$controller, $actionName . 'Action'], [$db, $collection]);
            // change MongoId to string, like "ObjectId(\"54c9f4f32736e7c8048b456b\")"
            // change MongoDate to string, like "ISODate(\"2015-01-29T08:53:07.450Z\")"
            $data = Formatter::document2Json($data);
            if (!is_string($data)) {
                $data = json_encode($data);
            }
            $response = new Response($data);
        } catch (Exception $e) {
            // catch all exception and return it
            $response = new Response($e->getMessage(), $e->getCode());
        }

        return $response;
    }

    /**
     * Check the request method
     * @param $action
     * @param Request $request
     * @throws BadMethodCallException
     */
    protected function checkMethod($action, Request $request)
    {
        // check the request method
        if (!empty(static::$actionMap[$action])) {
            $methods = static::$actionMap[$action];
            $method = $request->getMethod();
            if (!in_array($method, $methods)) {
                throw new BadMethodCallException('Bad method');
            }
        }
    }

    /**
     * Parse the path
     * @param $path
     * @return array
     * @throws NotFoundException
     */
    protected function parsePath($path)
    {
        list($controller, $action, $server, $db, $collection) = explode('/', $path);

        if (empty($controller)) {
            // use the default controller
            $controller = static::$defaultController;
        }
        if (empty($action)) {
            // use the default action
            $action = static::$defaultAction;
        }

        $ucController = ucfirst($controller);
        $controller = __NAMESPACE__ . '\\Controller\\' . $ucController . 'Controller';

        // $action = $action . 'Action';
        // controller file path
        $path = __DIR__ . '/Controller/' . $ucController . 'Controller.php';

        if (!is_file($path)) {
            throw new NotFoundException('Not Found');
        }
        return [$controller, $action, $server, $db, $collection];
    }

    /**
     * Generate the mongo client
     * @param $serverName
     * @throws ServerException
     */
    private function generateMongo($serverName)
    {
        if (empty($serverName) && empty(static::$config[0])) {
            throw new ServerException('No config info');
        }

        $dsn = '';
        foreach (static::$config as $config) {
            // find the right dsn using server name
            if ($config['name'] == $serverName) {
                $dsn = $config['dsn'];
            }
        }

        if (empty($dsn)) {
            // use the default dsn
            $dsn = static::$config[0]['dsn'];
        }

        static::$mongo = new MongoClient($dsn);
    }
}