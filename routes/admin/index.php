<?php 

use kaiocodebit\PageAdmin;
use kaiocodebit\Model\User;

$app->get('/admin', function() {
	User::checkLogin();

	$page = new PageAdmin();

	$page->setTpl("index");
});

// Authentication
include "./routes/admin/auth.php";

// Users
include "./routes/admin/users.php";

// Products
include "./routes/admin/products.php";

// Categories
include "./routes/admin/categories.php";

// Forgot Password
include "./routes/admin/forgot.php";


?>