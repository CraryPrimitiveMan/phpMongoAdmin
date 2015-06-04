<?php
namespace PhpMongoAdmin\Controller;

use PhpMongoAdmin\Base\Controller;
use PhpMongoAdmin\Framework;

/**
 * Class CollectionController is used to handle mongo database
 * @package PhpMongoAdmin\Controller
 */
class DatabaseController extends Controller {

    /**
     * Get all database list
     * @return array Database list
     */
    public function indexAction() {
        $dbs = [];
        try {
            $listDbs = $this->getMongoClient()->listDBs();
            foreach ($listDbs['databases'] as $db) {
                array_push($dbs, $db['name']);
            }
        } catch(\Exception $e) {
            $dbs = $this->_parseDsn();
        }
        return $dbs;
    }

    /**
     * Parse the dsn to get database name
     * @return array
     */
    private function _parseDsn() {
        $dsn = Framework::$server['dsn'];
        $parts = parse_url($dsn);

        // Get the database from the 'path' part of the URI
        $databases = [];
        if (isset($parts['path'])) {
            array_push($databases, $parts['path']);
        }

        return $databases;
    }
}