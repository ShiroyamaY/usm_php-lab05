<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

define('BASE_PATH', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));

require_once __DIR__ . '/../src/helpers.php';

use App\Core\Application;

$app = new Application();
$app->boot();

