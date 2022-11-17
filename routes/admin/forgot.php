<?php

use kaiocodebit\Model\User;
use kaiocodebit\PageAdmin;

$app->get('/admin/forgot', function() {	
	
	$page = new PageAdmin([
    "header" => false,
    "footer" => false
  ]);

	$page->setTpl("forgot/forgot");
});

$app->get('/admin/forgot/sent', function() {	
	
	$page = new PageAdmin([
    "header" => false,
    "footer" => false
  ]);

	$page->setTpl("forgot/forgot-sent");
});

$app->get('/admin/forgot/reset', function() {	
	
  $user = User::validForgotDecrypt($_GET["code"]);
  
	$page = new PageAdmin([
    "header" => false,
    "footer" => false
  ]);

	$page->setTpl("forgot/forgot-reset", array(
    "name" => $user["name"],
    "code" => $_GET["code"]
  ));
});

$app->post('/admin/forgot/reset', function() {	
	
  $forgot = User::validForgotDecrypt($_POST["code"]);
  User::setForgotUsed($forgot["id"]);

  $user = new User();
  
  $user->get((int)$forgot["id_user"]);

  $password = password_hash($_POST["password"], PASSWORD_BCRYPT, [
		'cost' => 12,
	]);
  $user->setPassword($password);

  header("Location: /admin/forgot/success");
  exit;
});

$app->get('/admin/forgot/success', function() {	
	
	$page = new PageAdmin([
    "header" => false,
    "footer" => false
  ]);

	$page->setTpl("forgot/forgot-reset-success");
});


$app->post('/admin/forgot', function() {	

  $user = new User();

  $user->getForgot($_POST['email']);
  
  header("Location: /admin/forgot/sent");
  exit;
});

?>