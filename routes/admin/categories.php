<?php

use kaiocodebit\Model\Category;
use kaiocodebit\Model\Product;
use kaiocodebit\Model\User;
use kaiocodebit\PageAdmin;

$app->get('/admin/categories', function() {	
  User::verifyLogin();
  $page = new PageAdmin();

  $categories = Category::listAll();
	$page->setTpl("categories/categories", array("categories" => $categories));
});

$app->get('/admin/categories/create', function() {	
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("categories/categories-create");
});


$app->post('/admin/categories/create', function() {	
	User::verifyLogin();

	$category = new Category();

  $category->setData($_POST);

  $category->save();

	header("Location: /admin/categories");
  exit;
});



$app->get('/admin/categories/:id/delete', function($id) {	
  User::verifyLogin();

  $category = new Category();

  $category->get((int)$id);

  $category->delete();

	header("Location: /admin/categories");
  exit;
});


$app->get('/admin/categories/:id/products', function($id) {	
  User::verifyLogin();

  $page = new PageAdmin();

  $category = new Category();

  $category->get((int)$id);

  $page->setTpl("categories/categories-products", array(
    "category" => $category->getValues(),
    "productsRelated" => $category->getProducts(),
    "productsNotRelated" => $category->getProducts(false)
  ));
});


$app->get('/admin/categories/:id/products/:product_id/add', function($id, $product_id) {	
  User::verifyLogin();

  $category = new Category();

  $category->get((int)$id);

  $product = new Product();

  $product->get((int)$product_id);

  $category->addProduct($product);

  header("Location: /admin/categories/".$id."/products");
  exit;
});

$app->get('/admin/categories/:id/products/:product_id/remove', function($id, $product_id) {	
  User::verifyLogin();

  $category = new Category();

  $category->get((int)$id);

  $product = new Product();

  $product->get((int)$product_id);

  $category->removeProduct($product);

  header("Location: /admin/categories/".$id."/products");
  exit;
});

$app->get('/admin/categories/:id', function($id) {	
  User::verifyLogin();

  $page = new PageAdmin();

  $category = new Category();

  $category->get((int)$id);

  $page->setTpl("categories/categories-update", array(
    "category" => $category->getValues()
  ));
});

$app->post('/admin/categories/:id', function($id) {	
  User::verifyLogin();

  $category = new Category();

  $category->get((int)$id);

  $category->setData($_POST);

  $category->update();

  header("Location: /admin/categories");
  exit;
});

?>