<?php
namespace PhpMongoAdmin\Controller;

use PhpMongoAdmin\Base\Controller;
use PhpMongoAdmin\Exception\InvalidArgumentException;
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
     * Execute a statement, and return the data
     * @param string $db Database name
     * @return array
     * @throws InvalidArgumentException
     */
    public function executeAction($db) {
        if (empty($db)) {
            throw new InvalidArgumentException('Database name is empty');
        }

        $code = $this->getParam('code');
        if (empty(trim($code))) {
            throw new InvalidArgumentException('Code is empty');
        }

        $result = [];
        // Split the code with semicolon
        $codes = explode(';', $code);
        foreach ($codes as $code) {
            // Skip the empty code
            if (!empty(trim($code))) {
                $code = $this->_refineCode($code);
                // execute the code
                $result[] = $this->getDatabase($db)->execute($code);
            }
        }
        return $result;
    }

    /**
     * Add the .toArray() for mongodb find statement
     * @param string $code
     * @return string
     */
    private function _refineCode($code) {
        // I don't want to refine it again.
        // If there is any problem, please use the following tool to refine it.
        // https://jex.im/regulex/
        $findRegular = '/^(.*?find\([^()]*\)(?:\.?(?!toArray\(\))[0-9a-zA-Z()]*)*)((?:(?!\.toArray\(\)).)*)$/m';
        if (preg_match_all($findRegular, $code, $matches)) {
            // Add the toArray function to get the data
            $code = $matches[1][0] . '.toArray()' . $matches[2][0];
        }
        return $code;
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