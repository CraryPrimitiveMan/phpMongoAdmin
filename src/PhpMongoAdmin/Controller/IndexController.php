<?php
namespace PhpMongoAdmin\Controller;

use PhpMongoAdmin\Base\Controller;
use PhpMongoAdmin\Exception\InvalidArgumentException;

/**
 * Class CollectionController is used to handle mongo database index
 * @package PhpMongoAdmin\Controller
 */
class IndexController extends Controller {

    /**
     * Get all mongo collection indexes
     * @param string $db Database name
     * @param string $collection Collection name
     * @return array Collection indexes
     * @throws InvalidArgumentException
     */
    public function indexAction($db, $collection) {
        if (empty($db) || empty($collection)) {
            throw new InvalidArgumentException('Database or collection name is empty');
        }

        return $this->getCollection($db, $collection)->getIndexInfo();
    }

    public function createAction($name) {
//        $db = $this->request->query->get('db');
//        $collection = $this->request->query->get('collection');
//
//        if (empty($db) || empty($collection)) {
//            throw new InvalidArgumentException('Database or collection name is empty');
//        }
//
//        return $this->getCollection($db, $collection)->ensureIndex($name);
    }
}