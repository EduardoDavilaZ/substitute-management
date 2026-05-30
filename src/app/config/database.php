<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();
//Connection Substitution
define ("DB_CONNECTION",    $_ENV['DB_CONNECTION']);
define ("DB_HOST",          $_ENV['DB_HOST']);
define ("DB_DATABASE",      $_ENV['DB_DATABASE']);
define ("DB_USERNAME",      $_ENV['DB_USERNAME']);
define ("DB_PASSWORD",      $_ENV['DB_PASSWORD']);
//Connection Intra

define ("DB_CONNECTIONINTRA",    $_ENV['DB_CONNECTIONINTRA']);
define ("DB_HOSTINTRA",          $_ENV['DB_HOSTINTRA']);
define ("DB_DATABASEINTRA",      $_ENV['DB_DATABASEINTRA']);
define ("DB_USERNAMEINTRA",      $_ENV['DB_USERNAMEINTRA']);
define ("DB_PASSWORDINTRA",      $_ENV['DB_PASSWORDINTRA']);