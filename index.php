<?php 
require_once("vendor/autoload.php");

session_start();

use \Slim\Slim;


$app = new Slim();

$app->config('debug', true);

// Includes Admin routes
include "./routes/admin/index.php";

// Includes Site routes
include "./routes/site/index.php";
$app->run();

?>