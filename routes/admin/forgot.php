<?php 

use kaiocodebit\PageAdmin;

$app->get('/admin/forgot', function() {	
	
	$page = new PageAdmin([
    "header" => false,
    "footer" => false
  ]);

	$page->setTpl("forgot/forgot");
});

?>