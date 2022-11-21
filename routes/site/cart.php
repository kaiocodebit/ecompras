<?php

use kaiocodebit\Model\Cart;
use kaiocodebit\Page;

$app->get('/cart', function() {
	$page = new Page();
	
	$cart = Cart::getFromSession();


	$page->setTpl("cart/cart");
});

?>