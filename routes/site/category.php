<?php 

use kaiocodebit\Model\Category;
use kaiocodebit\Model\Product;
use kaiocodebit\Page;

$app->get('/category/:id', function($id) {
	
	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
	
	$category = new Category();
	
	$category->get((int)$id);
	
	$pagination = $category->getProductsPage($page);

	$pages = [];

	for ($i=1; $i <= $pagination['pages'] ; $i++) { 
		array_push($pages, [
			'link' => '/category/' . $category->getid() . '?page='.$i,
			'page' => $i
		]);
	}

	$page = new Page();

	if($category->getValues()){		
		$page->setTpl("category/category", array(
			"category" => $category->getValues(),
			"products" => $pagination['data'],
			"pages" => $pages
		));
	}else{
		$page->setTpl("index");
	}
});

?>