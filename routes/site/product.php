<?php
use kaiocodebit\Model\Product;
use kaiocodebit\Page;

$app->get('/product/:id', function($id) {
	$page = new Page();

	$product = new Product();
  $product->get((int)$id);
  $product->getProductDetails();

	$page->setTpl("product/product-detail", array(
		"product" => $product->getValues(),
    "categories" => $product->getCategories()
	));
});
?>