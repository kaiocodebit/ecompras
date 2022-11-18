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

}
?>