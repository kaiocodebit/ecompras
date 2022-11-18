<?php

use kaiocodebit\Model\Product;
use kaiocodebit\Page;

$app->get('/', function() {
	$page = new Page();

	$products = Product::listAll();

	$page->setTpl("index", array(
		"products" => Product::checkList($products)
	));
});

// Categoty
include "./routes/site/category.php"
?>