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
        $shells = preg_split('/(?<=\))[\s]*;/', $code);
        foreach ($shells as $shell) {
            // Skip the empty code
            if (!empty(trim($shell))) {
                $shell = $this->_addToArray($shell);
                // execute the code
                $result[] = $this->getDatabase($db)->execute($shell);
            }
        }
        return $result;
    }

    /**
     * Add the .toArray() for mongodb find statement
     * The default find statement will return the cursor
     * The forEach and map operate need to refine
     * It can't find the result with `forEach` and `map`, need to refine
     * @param string $shell
     * @return string
     */
    private function _addToArray($shell) {
        // Remove count/explain/forEach/hasNext/map/next/objsLeftInBatch
        $cursorOperate = 'addOption|batchSize|hint|limit|maxTimeMS|max|min|readPref|showDiskLoc|size|skip|snapshot|sort';
        // String without brackets
        $strWithoutBrackets = '[^()]*';
        // String in brackets
        $strInBrackets = '\(' . $strWithoutBrackets . '(?:\(' . $strWithoutBrackets . '\))*'. $strWithoutBrackets . '\)';
        // If there is any problem, please use the following tool to refine it.
        // https://jex.im/regulex/
        $findRegular = '/^(.*?find' . $strInBrackets . '(?:\.?(?:' . $cursorOperate . ')\(' . $strWithoutBrackets . '\))*)((?:(?!\.toArray\(\)).)*)$/m';
        if (preg_match_all($findRegular, $shell, $matches)) {
            // Add the toArray function to get the data
            $shell = $matches[1][0] . '.toArray()' . $matches[2][0];
        }
        return $shell;
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