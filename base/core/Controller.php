<?php
namespace base\core;
/**
 * The base Controller
 */
Class Controller
{
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
        $names = explode('\\', get_class($this));
        $controllerName = strtolower(str_replace('Controller', '', end($names)));

        include BASE_PATH . "/views/$controllerName/$page.php";
    }
}