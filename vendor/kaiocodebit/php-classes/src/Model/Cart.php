<?php

namespace kaiocodebit\Model;

use \kaiocodebit\DB\Sql;
use \kaiocodebit\Model;

class Cart extends Model {

  const SESSION = "Cart";
  const SESSION_ERROR = "CartError";

  public function save(){

    $sql = new Sql();

    $results = $sql->select("CALL sp_carts_save(:pid, :psession_id, :pid_user, :zipcode, :pfreight, :pdays)", 
    array(
      ":pid" => $this->getid(),
      ":psession_id" => $this->getsession_id(),
      ":pid_user" => $this->getid_user(),
      ":zipcode" => $this->getzipcode(),
      ":pfreight" => $this->getfreight(),
      ":pdays" => $this->getdays()
    ));

    $this->setData($results[0]);
  }

  public static function getFromSession(){

    $cart = new Cart();

    if (isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['id'] > 0 ) {
      $cart->get((int) $_SESSION[Cart::SESSION]['id']);
    }else{
      $cart->getFromSessionID();

      if(!(int)$cart->getid() > 0){
        $data = [ 
          "session_id" => session_id()
        ];

        if(User::checkLogin(false)){
          $user = User::getFromSession();
          
          $data['id_user'] = $user->getid();
          
        }

        $cart->setData($data);

        $cart->save();

        $cart->setToSession();
      }
    }

    return $cart;
  }

  public function setToSession(){
    $_SESSION[Cart::SESSION] = $this->getValues();
  }

  public function get(int $id){

    $sql = new Sql();

    $results = $sql->select("SELECT * FROM carts WHERE id = :ID", array(
      ":ID" => $id
    ));
    if(count($results) > 0){
      $this->setData($results[0]);
    }
  }

  public function getFromSessionID(){
    $sql = new Sql();

    $results = $sql->select("SELECT * FROM carts WHERE session_id = :SESSION_ID", array(
      ":SESSION_ID" => session_id()
    ));

    if(count($results) > 0){
      $this->setData($results[0]);
    }
  }

  public function addProduct(Product $product){
    $sql = new Sql();

    $sql->query("INSERT INTO cart_products (id_cart, id_product) VALUES(:ID_CART, :ID_PRODUCT)", array(
      ":ID_CART" => $this->getid(),
      ":ID_PRODUCT" => $product->getid(),
    ));

    $this->getCalculateTotal();
  }

  public function removeProduct(Product $product, $all = false){
    $sql = new Sql();

    if($all) {
      $sql->query("UPDATE  cart_products SET  removed_at = NOW() WHERE 
        id_cart = :ID_CART AND id_product = :ID_PRODUCT", array(
        ":ID_CART" => $this->getid(),
        ":ID_PRODUCT" => $product->getid(),
      ));
    }else{ 
      $sql->query("UPDATE  cart_products SET  removed_at = NOW() WHERE 
        id_cart = :ID_CART AND id_product = :ID_PRODUCT AND removed_at IS NULL LIMIT 1 ", array(
      ":ID_CART" => $this->getid(),
      ":ID_PRODUCT" => $product->getid(),
    ));
    }

    $this->getCalculateTotal();
  }

  public function getProducts()
  {
    $sql = new Sql();
    
    $row = $sql->select("
      SELECT P.id, P.product, P.price, P.width, P.height, P.length, P.weight, P.url, COUNT(*) AS qtd, SUM(P.price) AS total
      FROM cart_products CP 
      INNER JOIN products P ON P.id = CP.id_product 
      WHERE CP.id_cart = :ID_CART AND CP.removed_at IS NULL 
      GROUP BY P.id, P.product, P.price, P.width, P.height, P.length, P.weight
      ORDER BY P.product
      ", array(
      ":ID_CART" => $this->getid()
    ));

    return Product::checkList($row);
  }

  public function getProductsTotals(){
    $sql = new Sql();

    $results = $sql->select("SELECT 
    SUM(P.price) as price, SUM(P.width) AS width, SUM(P.height) AS height, SUM(P.length) AS length, SUM(P.weight) AS weight, COUNT(*) AS qtd
    FROM cart_products CP 
    INNER JOIN products P ON P.id = CP.id_product
    WHERE CP.id_cart = :ID_CART
    AND CP.removed_at IS NULL 
    ORDER BY P.product", array(
      "ID_CART" => $this->getid()
    ));
    if(count($results) > 0){
      return $results[0];
    }else{
      return [];
    }
  }

  public function setFreightZipcode($zipcode)
  {
    $zipcodeFormated = isset($zipcode) ? str_replace('-', '', $zipcode) : null;
    $totals = $this->getProductsTotals();

    if($totals['qtd'] > 0 && isset($zipcodeFormated)){  
      $qs = http_build_query([
        "nCdEmpresa" => "",
        "sDsSenha" => "",
        "nCdServico" => "40010",
        "sCepOrigem" => "14407000",
        "sCepDestino" => $zipcodeFormated,
        "nVlPeso" => $totals["weight"],
        "nCdFormato" => 0,
        "nVlComprimento" => $totals["length"],
        "nVlAltura" => $totals["height"],
        "nVlLargura" => $totals["width"],
        "nVlDiametro" => "0",
        "sCdMaoPropria" => "S",
        "nVlValorDeclarado" =>$totals["price"],
        "sCdAvisoRecebimento" => "S"      
      ]);


      $xml = simplexml_load_file("http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo?". $qs);
      
      $result = $xml->Servicos->cServico;

      if($result->MsgErro != ''){
        Cart::setMsgError($result->MsgErro);
      }else{
        Cart::resetMsgError();
      }

      $this->setdays($result->PrazoEntrega);
      $this->setfreight(Cart::formatValueToDecimal($result->Valor));
      $this->setzipcode($zipcode);

      $this->save();

      return $result;

    }
  }

  public function formatValueToDecimal($value) {

    $value =str_replace(".", ".", $value);

    return str_replace(",",".", $value);
  }

  public static function setMsgError($msg){
    $_SESSION[Cart::SESSION_ERROR] = $msg;
  }

  public static function getMsgError(){
    $msg = isset($_SESSION[Cart::SESSION_ERROR]) ? $_SESSION[Cart::SESSION_ERROR] : "";

    Cart::resetMsgError();

    return $msg;
  }

  public static function resetMsgError(){
    $_SESSION[Cart::SESSION_ERROR] = NULL;
  }

  public function updateFreight(){
    if($this->getzipcode() != ''){
      $this->setFreightZipcode($this->getzipcode());
    }
  }

  public function getValues()
  {

    $this->getCalculateTotal();

    return parent::getValues();

  }

  public function getCalculateTotal()
  {
    $this->updateFreight();
    
    $totals = $this->getProductsTotals();

    $this->setsubtotal($totals['price']);
    $this->settotal($totals['price'] + $this->getfreight());
    
  }

}

?>