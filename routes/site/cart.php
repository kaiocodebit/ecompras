<?php

use kaiocodebit\Model\Cart;
use kaiocodebit\Model\Product;
use kaiocodebit\Page;

$app->get('/cart', function() {
	$page = new Page();
	
	$cart = Cart::getFromSession();
	$cart->getProducts();

	$page->setTpl("cart/cart", [
		"cart" => $cart->getValues(),
		"products" =>$cart->getProducts()
	]);
});

$app->get('/cart/:product_id/add', function($product_id) {

	$product = new Product();
	$qtd = isset($_GET['qtd']) ? $_GET['qtd'] : 1;


	$product->get((int)$product_id);

	$cart = Cart::getFromSession();

	for ($i=0; $i < $qtd; $i++) { 
		$cart->addProduct($product);
	}

	header("Location: /cart");
	exit;
});



$app->get('/cart/:product_id/minus', function($product_id) {

	$product = new Product();

	$product->get((int)$product_id);

	$cart = Cart::getFromSession();

	$cart->removeProduct($product);

	header("Location: /cart");
	exit;
});

$app->get('/cart/:product_id/remove', function($product_id) {

	$product = new Product();

	$product->get((int)$product_id);

	$cart = Cart::getFromSession();

	$cart->removeProduct($product, true);

	header("Location: /cart");
	exit;
});




?>