<?php
namespace PhpMongoAdmin\Controller;

use PhpMongoAdmin\Base\Controller;
use PhpMongoAdmin\Base\Formatter;
use PhpMongoAdmin\Exception\InvalidArgumentException;

/**
 * Class RowController is used to handle mongo document
 * @package PhpMongoAdmin\Controller
 */
class DocController  extends Controller {
    /**
     * Get mongo collection document
     * Default page is 1, page size is 50.
     * @param string $db Database name
     * @param string $collection Collection name
     * @return array Collection data and the total count
     * @throws InvalidArgumentException
     */
    public function indexAction($db, $collection) {
        if (empty($db) || empty($collection)) {
            throw new InvalidArgumentException('Database or collection name is empty');
        }

        $page = $this->getQuery('page', 1);
        $pageSize = $this->getQuery('per-page', 50);
        $skipNum = ($page - 1) * $pageSize;

        $coll = $this->getCollection($db, $collection);
        $total = $coll->count();
        $cursor = $coll->find()->skip($skipNum)->limit($pageSize);
        $items = [];
        foreach ($cursor as $doc) {
            array_push($items, $doc);
        }

        return ['total' => $total, 'page' => $page, 'per-page' => $pageSize, 'items' => $items];
    }

    /**
     * Create or update a mongo document
     * @param string $db Database name
     * @param string $collection Collection name
     * @return array
     * @throws InvalidArgumentException
     */
    public function createAction($db, $collection) {
        if (empty($db) || empty($collection)) {
            throw new InvalidArgumentException('Database or collection name is empty');
        }

        $doc = $this->getParam();
        if (empty($doc)) {
            throw new InvalidArgumentException('Document is empty');
        }

        $doc = Formatter::json2Document($doc);
        $coll = $this->getCollection($db, $collection);
        return $coll->save($doc);
    }

    /**
     * Delete a document with _id field
     * @param string $db
     * @param string $collection
     * @return array
     * @throws InvalidArgumentException
     */
    public function deleteAction($db, $collection) {
        if (empty($db) || empty($collection)) {
            throw new InvalidArgumentException('Database or collection name is empty');
        }

        $id = $this->getParam('id');
        $id = Formatter::str2MongoId($id);
        if ($id === false) {
            throw new InvalidArgumentException('Document id is wrong');
        }

        return $this->getCollection($db, $collection)->remove(['_id' => $id]);
    }
}