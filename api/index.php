<?php
require_once __DIR__ . '/../src/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing;
use PhpMongoAdmin\Framework;

$request = Request::createFromGlobals();
$config = require __DIR__ . '/../config/main.php';
$framework = new Framework($config);
$response = $framework->handle($request);
$response->headers->set('Content-Type', 'application/json');
$response->send();
