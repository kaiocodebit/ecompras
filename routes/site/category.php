<?php 

use kaiocodebit\Model\Category;
use kaiocodebit\Page;

$app->get('/category/:id', function($id) {
	$page = new Page();

	$category = new Category();
	
	$category->get((int)$id);

	if($category->getValues()){		
		$page->setTpl("category/category", array(
			"category" => $category->getValues(),
			"products" => []
		));
	}else{
		$page->setTpl("index");
	}
});

?>