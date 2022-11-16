<?php 

require_once("vendor/autoload.php");

$app = new \Slim\Slim();

$app->config('debug', true);

$app->get('/', function() {
	$sql = new \kaiocodebit\DB\Sql();
	$results = $sql->select("SELECT * FROM users");
    // echo 'ok';

	echo json_encode($results);
});

$app->run();

?>