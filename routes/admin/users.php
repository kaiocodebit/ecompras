<?php 

use kaiocodebit\PageAdmin;
use kaiocodebit\Model\User;

$app->get('/admin/users/create', function() {	
	User::verifyLogin();
	$page = new PageAdmin();

	$page->setTpl("users/users-create");
	
});

// Create User
$app->post('/admin/users/create', function() {	
	User::verifyLogin();
	
	$user = new User();

	$_POST["is_admin"] = (isset($_POST["is_admin"])) ? 1: 0;
	$_POST["password"] = (isset($_POST["password"])) ? password_hash($_POST["password"], PASSWORD_BCRYPT, [
		'cost' => 12,
	]) : NULL;

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");
	exit;
});

// Update Page
$app->get('/admin/users/:id/delete', function($id) {	
	User::verifyLogin();
	
	$user = new User();
	
	$user->get((int)$id);
	$user->delete();
	
	header("Location: /admin/users");
	exit;
});


// Update Page
$app->get('/admin/users/:id', function($id) {	
	User::verifyLogin();
	
	$user = new User();
	
	$user->get((int)$id);
	
	$page = new PageAdmin();
	
	$page->setTpl("users/users-update", array(
		"user" => $user->getValues()
	));
});

$app->post('/admin/users/:id', function($id) {	
	User::verifyLogin();

	$user = new User();

	$user->get((int)$id);
	
	$user->setData($_POST);
	
	$user->update();

	header("Location: /admin/users");
	exit;
});

// List Page
$app->get('/admin/users', function() {	
	User::verifyLogin();
	$users = User::listAll();
	
	$page = new PageAdmin();

	$page->setTpl("users/users", array(
		"users" => $users
	));
});

?>