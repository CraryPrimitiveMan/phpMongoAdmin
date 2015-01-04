<?php
// change the following paths if necessary
$app = dirname(__FILE__) . '/App.php';
$autoload = dirname(__FILE__) . '/autoload.php';
$config = dirname(__FILE__) . '/config/main.php';

require_once($app);
require_once($autoload);

App::getApp($config)->run();