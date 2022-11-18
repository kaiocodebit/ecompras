<?php

use kaiocodebit\Page;

$app->get('/', function() {
	$page = new Page();

	$page->setTpl("index");
});

// Categoty
include "./routes/site/category.php"
?>