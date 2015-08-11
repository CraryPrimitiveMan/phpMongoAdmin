<?php
$configPath = dirname(__FILE__) . '/main.json';
define('CONFIG_PATH', $configPath);
$content = file_get_contents(CONFIG_PATH);
return json_decode($content, true);