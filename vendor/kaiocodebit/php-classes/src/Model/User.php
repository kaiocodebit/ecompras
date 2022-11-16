<?php 

namespace kaiocodebit\Model;

use \kaiocodebit\DB\Sql;
use \kaiocodebit\Model;

class User extends Model {
  const SESSION = "User";

  public static function login($login, $password){
    $sql = new Sql();
    $results = $sql->select("SELECT * FROM users WHERE username = :LOGIN", array(
      ":LOGIN" => $login
    ));

    if(count($results) === 0){
      throw new \Exception("Usuário inexistente ou senha inválida");
    }

    $data = $results[0];

    if(password_verify($password, $data['password']) === true){
      $user = new User();

      $user->setData($data);

      $_SESSION[User::SESSION] = $user->getValues();
      // var_dump($_SESSION[User::SESSION]);
      // exit();
      return $user;
    }else{
      throw new \Exception("Usuário inexistente ou senha inválida");
    }
  }

  public static function verifyLogin($is_admin = true){
    // print_r($_SESSION[User::SESSION]);
    // exit;
    if(
      !isset($_SESSION[User::SESSION])
      ||
      !$_SESSION[User::SESSION]
      ||
      !(int)$_SESSION[User::SESSION]['id'] > 0
      ||
      (bool)$_SESSION[User::SESSION]['is_admin'] !== $is_admin
    ){
      header("Location: /admin/login");
      exit;
    }else{
      header("Location: /admin");
    }
  }

  public static function verifyIfAuthenticated(){
    if(isset($_SESSION[User::SESSION])){
      header("Location: /admin");
      exit;
    }
  }

  public static function logout(){
    if(isset($_SESSION[User::SESSION])){
      $_SESSION[User::SESSION] = NULL;
      header("Location: /admin/login");
      exit;
    }
  }

}
?>