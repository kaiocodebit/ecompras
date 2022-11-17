<?php 

use kaiocodebit\PageAdmin;
use kaiocodebit\Model\User;

$app->get('/admin', function() {
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");
});

// Authentication
include "./routes/admin/auth.php";

// Users
include "./routes/admin/users.php";

// Forgot Password
include "./routes/admin/forgot.php";


?>