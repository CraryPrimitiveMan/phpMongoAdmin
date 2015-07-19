<?php
namespace PhpMongoAdmin\Exception;

use PhpMongoAdmin\Base\Exception;

class BadMethodCallException extends Exception {
    public function __construct($message = "") {
        return parent::__construct($message, 405);
    }
}