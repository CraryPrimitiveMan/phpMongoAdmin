<?php
/**
 * The project base path
 */
defined('BASE_PATH') or define('BASE_PATH',dirname(__FILE__));
/**
 * The project base class
 */
Class App
{
    /**
     * The app object for global
     * @var App
     */
    private static $_app = null;

    /**
     * Define the app config
     * @var array
     */
    public static $config;

    /**
     * Define the class map
     * @var array
     */
    public static $classMap = array(
        'MongoAdmin' => '/mongo/MongoAdmin.php',
        'MongoController' => '/controllers/MongoController.php'
    );

    /**
     * Get the app. It's a singleton.
     * @param  array $config  The default config
     * @return App
     */
    public static function getApp($config) {
        if (self::$_app === null) {
            self::$config = include_once($config);
            self::$_app = new App();
        }
        return self::$_app;
    }

    /**
     * Run the application with the action
     */
    public function run()
    {
        // Add the default action index
        $action = (!isset($_GET['action']) || empty($_GET['action'])) ? 'index' : $_GET['action'];
        $controller = new MongoController();
        $action = 'action' . ucwords($action);

        if(method_exists($controller, $action)) {
            $controller->$action();
        } else {
            throw new Exception("$action doesn't exist.");
        }
    }
}