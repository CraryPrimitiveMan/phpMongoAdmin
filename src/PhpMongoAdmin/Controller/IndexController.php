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

    /**
     * Create a new index
     * @param string $db Database name
     * @param string $collection Collection name
     * @return array
     * @throws InvalidArgumentException
     */
    public function createAction($db, $collection) {
        $keys = $this->getParam('keys');
        $options = $this->getParam('options', []);

        if (empty($db) || empty($collection)) {
            throw new InvalidArgumentException('Database or collection name is empty');
        }

        if (empty($keys)) {
            throw new InvalidArgumentException('Index keys is empty');
        }

        $keys = $this->initKeys($keys);
        // Creating index with background options
        if (!array_key_exists('background', (array)$options)) {
            $options['background'] = true;
        }

        return $this->getCollection($db, $collection)->ensureIndex($keys, $options);
    }

    /**
     * Delete a index with index keys
     * @param string $db Database name
     * @param string $collection Collection name
     * @return array
     * @throws InvalidArgumentException
     */
    public function deleteAction($db, $collection) {
        $keys = $this->getParam('keys');

        if (empty($db) || empty($collection)) {
            throw new InvalidArgumentException('Database or collection name is empty');
        }

        if (empty($keys)) {
            throw new InvalidArgumentException('Index keys is empty');
        }

        $keys = $this->initKeys($keys);

        return $this->getCollection($db, $collection)->deleteIndex($keys);
    }

    /**
     * Init the index keys
     * If there is only one key, get the first value
     * @param $keys
     * @return array|string
     */
    protected function initKeys($keys) {
        // If only one key, use string
        if (is_array($keys) && count($keys) === 1) {
            $keys = $keys[0];
        }

        return $keys;
    }
}