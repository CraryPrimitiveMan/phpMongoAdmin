<?php
namespace PhpMongoAdmin\Exception;

use PhpMongoAdmin\Base\Exception;

class NotFoundException extends Exception {
    public function __construct($message = "") {
        return parent::__construct($message, 404);
    }
}