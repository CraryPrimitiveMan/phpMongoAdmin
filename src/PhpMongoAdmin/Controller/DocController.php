<?php
namespace PhpMongoAdmin\Controller;

use PhpMongoAdmin\Base\Controller;
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

}