<?php 

namespace kaiocodebit\Model;

use \kaiocodebit\DB\Sql;
use \kaiocodebit\Model;

class Category extends Model {

  public static function listAll(){
    $sql = new Sql();

    return $sql->select("SELECT * FROM categories C ORDER BY C.category");
  }

  public function get($id){
    $sql = new Sql();

    $result = $sql->select("SELECT * FROM categories C WHERE C.id = :ID", array(
      ":ID" => $id
    ));
    
    if(isset($result[0])){
      $this->setData($result[0]);
    }
  }

  public function save(){
    $sql = new Sql();

    $results = $sql->select("CALL sp_categories_save(:pid, :pcategory)", 
    array(
      ":pid" => $this->getid(),
      ":pcategory"  => $this->getcategory()
    ));

    $this->setData($results[0]);

    $this->updateFile();
  }
  
  public function update(){
    $sql = new Sql();

    $result = $sql->select("UPDATE categories SET category = :CATEGORY WHERE id = :ID ", array(
      ":ID" => $this->getid(),
      ":CATEGORY" => $this->getcategory(),
    ));

    return $this->setData($result);
  }

  public function delete(){
    $sql = new Sql();
    $sql->select("DELETE FROM categories WHERE id = :ID", array(
      ":ID" => $this->getid(),
    ));

    $this->updateFile();
  }

  public static function updateFile(){
    $categories = Category::listAll();

    $html = [];
    foreach ($categories as $category) {
      array_push($html, '<li><a href="/category/'.$category["id"].'">'. $category["category"] .'</a></li> ');
    }
    file_put_contents($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR . "category-menu.html" , implode('', $html));
  }

  public function getProducts($related = true){
    $sql = new Sql();

    if($related === true){
      return $sql->select("
        SELECT * FROM products WHERE id IN (
          SELECT 
            P.id
          FROM 
            products P 
          INNER JOIN product_categories PC ON PC.id_product = P.id 
          WHERE PC.id_category = :ID
        );
        ", array(
          ":ID" => $this->getid()
        ));
    }else{
      return $sql->select("
        SELECT * FROM products WHERE id NOT IN (
          SELECT 
            P.id
          FROM 
            products P 
          INNER JOIN product_categories PC ON PC.id_product = P.id 
          WHERE PC.id_category = :ID
        );
        ", array(
          ":ID" => $this->getid()
        ));
    }
  }

  public function addProduct(Product $product){
    $sql = new Sql();

    $product = $product->getValues();

    $sql->select("INSERT INTO product_categories (id_product, id_category) VALUES (:ID_PRODUCT, :ID_CATEGORY)", array(
      ":ID_PRODUCT" => $product['id'],
      ":ID_CATEGORY" => $this->getid()
    ));
  }

  public function removeProduct(Product $product){
    $sql = new Sql();

    $product = $product->getValues();

    $sql->select("DELETE FROM product_categories PC WHERE PC.id_product = :ID_PRODUCT AND PC.id_category = :ID_CATEGORY ", array(
      ":ID_PRODUCT" => $product['id'],
      ":ID_CATEGORY" => $this->getid()
    ));
  }

  public function getProductsPage($page = 1, $per_page = 3) {
    $start = ($page - 1) * $per_page;

    $sql = new Sql();
    
    $results = $sql->select("
        SELECT P.*
        FROM products P 
        INNER JOIN product_categories PC ON PC.id_product = P.id 
        INNER JOIN categories C ON C.id  = PC.id_category 
        WHERE C.id = :IDCATEGORY
        LIMIT $start, $per_page", 
      array(
        ":IDCATEGORY" => $this->getid()
      ));

    $resultTotal = $sql->select("
      SELECT COUNT(*) AS total
      FROM products P 
      INNER JOIN product_categories PC ON PC.id_product = P.id 
      INNER JOIN categories C ON C.id  = PC.id_category 
      WHERE C.id = :IDCATEGORY
    ",array(
      ":IDCATEGORY" => $this->getid()
    ));

    return [
        "data" => Product::checkList($results),
        "total" => (int)$resultTotal[0]['total'],
        "pages" => ceil((int)$resultTotal[0]['total'] / $per_page)
      ];

  }
}
?>