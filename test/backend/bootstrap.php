<?php
/**
 * (c) Łukasz Pietrek.
 */

declare(strict_types = 1);
require __DIR__ . '/../../config/database.test.php';

/** @var Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__ . '/../../vendor/autoload.php';

$loader->addPsr4('TSH\\Local\\Test\\', __DIR__ . '/Test');
$loader->addPsr4('TSH\\Local\\TestUtil\\', __DIR__ . '/TestUtil');
