<?php
/**
 * Basic front controller
 */

// Some handy definitions
define('ROOT', dirname(__FILE__) . '/');
define('CONFIG', ROOT . '/config/');
define('LIB', ROOT . '/lib/');

// Include configurations
include(CONFIG . 'database.php');

// Include base TSH classes
include(LIB . 'TSH.php');

// Include your classes here however you wish
// eg: include(LIB . 'Local/MyClass.php');

// Main - over to you! :-)