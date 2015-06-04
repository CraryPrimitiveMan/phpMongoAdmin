<?php
namespace PhpMongoAdmin\Controller;

use PhpMongoAdmin\Base\Controller;
use PhpMongoAdmin\Exception\InvalidArgumentException;

/**
 * Class CollectionController is used to handle mongo collection
 * @package PhpMongoAdmin\Controller
 */
class CollectionController extends Controller {
    /**
     * Get all mongo collection name
     * @param string $db Database name
     * @return array All collection names
     * @throws InvalidArgumentException
     */
    public function indexAction($db) {
        if (empty($db)) {
            throw new InvalidArgumentException('Database name is empty');
        }

        return $this->getDatabase($db)->getCollectionNames();
    }
    /**
     * Create a mongo collection
     * @param string $db Database name
     * @param string $collection Collection name
     * @return \MongoCollection
     * @throws InvalidArgumentException
     */
    public function createAction($db, $collection) {
        if (empty($db) || empty($collection)) {
            throw new InvalidArgumentException('Database or collection name is empty');
        }

        return $this->getDatabase($db)->createCollection($collection);
    }

    /**
     * Drop a mongo collection
     * @param string $db Database name
     * @param string $collection Collection name
     * @return array
     * @throws InvalidArgumentException
     */
    public function deleteAction($db, $collection) {
        if (empty($db) || empty($collection)) {
            throw new InvalidArgumentException('Database or collection name is empty');
        }

        return $this->getCollection($db, $collection)->drop();
    }
}