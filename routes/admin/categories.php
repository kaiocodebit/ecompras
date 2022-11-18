<?php

use kaiocodebit\Model\Category;
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