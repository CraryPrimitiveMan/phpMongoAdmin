<?php
/**
 * @param $classname
 * @throws Exception
 */
function __autoload($classname) {
    $file = BASE_PATH . '/' . str_replace('\\', '/', $classname) . '.php';

    if(file_exists($file)) {
        include_once($file);
    } else {
        throw new Exception("$file isn't a file.");
    }
}