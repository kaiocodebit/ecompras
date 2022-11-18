<?php

use kaiocodebit\Model\Product;
use kaiocodebit\Model\User;
use kaiocodebit\PageAdmin;

$app->get('/admin/products', function() {	
  User::verifyLogin();

  $page = new PageAdmin();

  $products = Product::listAll();

	$page->setTpl("products/products", array("products" => $products));
});

$app->get('/admin/products/create', function() {	
  User::verifyLogin();

  $page = new PageAdmin();

	$page->setTpl("products/products-create");
});


$app->post('/admin/products/create', function() {	
  User::verifyLogin();

  $product = new Product();

  $product->setData($_POST);

  $product->save();

  header("Location: /admin/products");  
  exit;

});

$app->get('/admin/products/:id/delete', function($id) {	
  User::verifyLogin();

  $product = new Product();

  $product->get((int)$id);

  $product->delete();

  header("Location: /admin/products");
  exit;
});


$app->get('/admin/products/:id', function($id) {	
  User::verifyLogin();

  $page = new PageAdmin();

  $product = new Product();

  $product->get((int)$id);

	$page->setTpl("products/products-update", array(
    "product" => $product->getValues()
  ));
});


$app->post('/admin/products/:id', function($id) {	
  User::verifyLogin();

  $product = new Product();

  $product->get((int)$id);

  $product->setData($_POST);

  $product->save();

  $product->setNewPhoto($_FILES["file"]);

  header("Location: /admin/products");
  exit;
});

?>