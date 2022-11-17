<?php 
require_once("vendor/autoload.php");

session_start();

use \Slim\Slim;

$app = new Slim();

$app->config('debug', true);

include "./routes/admin/index.php";
include "./routes/site/index.php";

$app->run();

?>