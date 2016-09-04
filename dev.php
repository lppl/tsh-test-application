<?php
/**
 * Basic front controller
 */

// Some handy definitions
define('ROOT', __DIR__ . '/');
define('CONFIG', ROOT . '/config/');
define('LIB', ROOT . '/lib/');

// Include configurations
require CONFIG . 'database.php';
$config = require CONFIG . 'config.php';

// Setup autoloader
require ROOT . 'vendor/autoload.php';


/** @var \Silex\Application $app */
$app = require  __DIR__ . '/app.php';

$app['debug'] = true;

$app->run();