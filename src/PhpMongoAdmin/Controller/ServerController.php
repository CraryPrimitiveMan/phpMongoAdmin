<?php
namespace PhpMongoAdmin\Controller;

use PhpMongoAdmin\Base\Controller;
use PhpMongoAdmin\Framework;

/**
 * Class ServerController is used to handle mongo server
 * @package PhpMongoAdmin\Controller
 */
class ServerController extends Controller {

    /**
     * Get all mongo servers
     * @return array Collection servers
     */
    public function indexAction() {
        $servers = [];
        if (!empty(Framework::$config)) {
            foreach (Framework::$config as $server) {
                array_push($servers, $server['name']);
            }
        }
        return $servers;
    }

}