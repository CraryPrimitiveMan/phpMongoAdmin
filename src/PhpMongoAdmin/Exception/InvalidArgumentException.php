<?php
namespace PhpMongoAdmin\Exception;

use PhpMongoAdmin\Base\Exception;

class InvalidArgumentException extends Exception {
    public function __construct($message = "") {
        return parent::__construct($message, 440);
    }
}