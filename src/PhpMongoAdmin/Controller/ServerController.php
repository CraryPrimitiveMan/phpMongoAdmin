<?php
namespace PhpMongoAdmin\Controller;

use PhpMongoAdmin\Base\Controller;
use PhpMongoAdmin\Framework;
use PhpMongoAdmin\Exception\ServerException;
use PhpMongoAdmin\Exception\InvalidArgumentException;

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
        return Framework::$config;
    }

    /**
     * Update mongo servers config
     * @return array
     * @throws InvalidArgumentException
     * @throws ServerException
     */
    public function updateAction() {
        $config = $this->getParam('config');

        if (empty($config)) {
            throw new InvalidArgumentException('Config is empty');
        }

        $result = file_put_contents(CONFIG_PATH, json_encode($config));
        if (!$result) {
            throw new ServerException('Save config failed');
        }
        return ['ok' => 1];
    }

}