<?php

namespace kaiocodebit\Model;

use \kaiocodebit\DB\Sql;
use \kaiocodebit\Model;

class Cart extends Model {

  const SESSION = "Cart";

  public function save(){

    $sql = new Sql();

    $results = $sql->select("CALL sp_carts_save(:pid, :psession_id, :pid_user, :zipcode, :pfreight, :pdays)", 
    array(
      ":pid" => $this->getid(),
      ":psession_id" => $this->getsession_id(),
      ":pid_user" => $this->getid_user(),
      ":zipcode" => $this->getzipcode(),
      ":pfreight" => $this->getfreight(),
      ":pdays" => $this->getgetdays()
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

}

?>