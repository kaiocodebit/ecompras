<?php 

use kaiocodebit\Model\Category;
use kaiocodebit\Model\Product;
use kaiocodebit\Page;

$app->get('/category/:id', function($id) {
	$page = new Page();

	$category = new Category();

	$category->get((int)$id);

	$products = $category->getProducts();

	if($category->getValues()){		
		$page->setTpl("category/category", array(
			"category" => $category->getValues(),
			"products" => Product::checkList($products)
		));
	}else{
		$page->setTpl("index");
	}
});

?>