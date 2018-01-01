<?php
// Composer
require __DIR__ . '/vendor/autoload.php';

// Whoops
$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

// .env
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// Fat Free Framework
$f3 = \Base::instance();

// The config
$f3->config(__DIR__ . '/app/config.ini');

// The DB
$db = new DB\SQL(
  'mysql:host=' . $_ENV['DB_HOST'] .
  ';port=' . $_ENV['DB_PORT'] .
  ';dbname=' . $_ENV['DB_NAME'],
  $_ENV['DB_USER'],
  $_ENV['DB_PASS']
);
$f3->set('db', $db);

// CORS and content-type
$f3->set('CORS.origin', '*');
header('Content-Type: application/json');

// Here we go
$f3->run();
