<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
session_start();

require_once __DIR__ ."/requirements.php";

$di = new DependencyInjector();
$di->set('config', new Config());
$di->set('database', new Database($di));
$di->set('hash', new Hash());
$di->set('util', new Util($di));
$di->set('errorHandler', new ErrorHandler());
$di->set('validator', new Validator($di));

$di->set('category', new Category($di));



require_once __DIR__ ."/constants.php";