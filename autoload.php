<?php
function __autoload($classname) {
    if (isset(App::$classMap[$classname])) {
        $file = BASE_PATH . App::$classMap[$classname];
        if(file_exists($file)) {
            include_once($file);
        } else {
            throw new Exception("$file isn't a file.");
        }
    } else {
        throw new Exception("$classname doesn't exist.");
    }
}