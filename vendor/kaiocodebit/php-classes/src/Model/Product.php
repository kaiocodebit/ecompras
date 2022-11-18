<?php 

namespace kaiocodebit\Model;

use \kaiocodebit\DB\Sql;
use \kaiocodebit\Model;

class Product extends Model {

  public static function listAll(){
    $sql = new Sql();

    return $sql->select("SELECT * FROM products P ORDER BY P.product");
  }

  public static function checkList($list){
    foreach($list as &$row){
      $p = new Product();
      $p->setData($row);

      $row = $p->getValues();
    }

    return $list;
  }
  public function get($id){
    $sql = new Sql();

    $result = $sql->select("SELECT * FROM products P WHERE P.id = :ID", array(
      ":ID" => $id
    ));

    if(isset($result[0])){
      $this->setData($result[0]);
    }
  }

  public function save(){
    $sql = new Sql();

    $results = $sql->select("CALL sp_products_save(:pid, :pproduct, :pprice, :pwidth, :pheight, :plength, :pweight, :purl)", 
    array(
      ":pid" => $this->getid(),
      ":pproduct" => $this->getproduct(),
      ":pprice" => $this->getprice(),
      ":pwidth" => $this->getwidth(),
      ":pheight" => $this->getheight(),
      ":plength" => $this->getlength(),
      ":pweight" => $this->getweight(),
      ":purl"   => $this->geturl()
    ));
    $this->setData($results[0]);
  }

  public function delete(){
    $sql = new Sql();
    $sql->select("DELETE FROM products WHERE id = :ID", array(
      ":ID" => $this->getid(),
    ));
  }

  public function checkPhoto(){
    if(file_exists(
      $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . 
      "res" . DIRECTORY_SEPARATOR .
      "site" . DIRECTORY_SEPARATOR .
      "img"  . DIRECTORY_SEPARATOR .
      "products"  . DIRECTORY_SEPARATOR .
      $this->getid() . ".jpg"
    )) {
      $url =  "/res/site/img/products/" . $this->getid() . ".jpg";
    }else{
      $url =  "/res/site/img/products/product.jpg";
    }

    return $this->setphoto($url);
  }

  public function getValues() {

    $this->checkPhoto();

    $values = parent::getValues();

    return $values;
  }

  public function setNewPhoto($file){

    $extension = explode('.', $file['name']);
    $extension = end($extension);
    switch($extension){

      case "jpg":
      case "jpeg":
        $image = imagecreatefromjpeg($file['tmp_name']);
      break;

      case "gif";
        $image = imagecreatefromgif($file['tmp_name']);
      break;

      case "png":
        $image = imagecreatefrompng($file['tmp_name']);
      break;

    }

    $dist = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . 
    "res" . DIRECTORY_SEPARATOR .
    "site" . DIRECTORY_SEPARATOR .
    "img"  . DIRECTORY_SEPARATOR .
    "products"  . DIRECTORY_SEPARATOR .
    $this->getid() . ".jpg";

    if(isset($image)){
      imagejpeg($image, $dist);

      imagedestroy($image);
    }

    $this->checkPhoto();
  }

}
?>