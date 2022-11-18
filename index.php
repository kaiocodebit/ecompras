<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;

$app = new Slim();

$app->config('debug', true);

require_once("./function.php");
include "./routes/admin/index.php";
include "./routes/site/index.php";

$app->run();

?>