<?php 
require_once("vendor/autoload.php");

session_start();

use \Slim\Slim;
use kaiocodebit\Page;
use kaiocodebit\PageAdmin;
use kaiocodebit\Model\User;

$app = new Slim();

$app->config('debug', true);

$app->get('/', function() {
	$page = new Page();

	$page->setTpl("index");
});

$app->get('/admin', function() {
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");
});

$app->get('/admin/login', function() {
	User::verifyIfAuthenticated();

	$page = new PageAdmin([
			"header" => false,
			"footer" => false
		]);

	$page->setTpl("login");
});

$app->post('/admin/login', function() {	

	User::login($_POST['login'], $_POST['password']);
	
	header("Location: /admin");
	exit;
});


$app->get('/logout', function() {	

	User::logout();
	
});


$app->run();

?>