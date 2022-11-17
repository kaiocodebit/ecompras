<?php 

use kaiocodebit\PageAdmin;
use kaiocodebit\Model\User;

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
?>