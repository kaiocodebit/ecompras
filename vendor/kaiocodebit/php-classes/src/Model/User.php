<?php 

namespace kaiocodebit\Model;

use \kaiocodebit\DB\Sql;
use \kaiocodebit\Model;

class User extends Model {
  const SESSION = "User";

  /*
    User Auth
  */

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

      $user->get((int)$data["id"]);
      $user->setData($data);

      $_SESSION[User::SESSION] = $user->getValues();
      return $user;
    }else{
      throw new \Exception("Usuário inexistente ou senha inválida");
    }
  }

  public static function verifyLogin($is_admin = true){
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
      // exit;
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

  /*
    Model User
  */

  public static function listAll(){
    $sql = new Sql();

    return $sql->select("SELECT * FROM users U INNER JOIN persons P USING(id) ORDER BY P.name");
  }

  public function get($id){
    $sql = new Sql();

    $result = $sql->select("SELECT * FROM users U INNER JOIN persons P USING(id) WHERE U.id = :ID", array(
      ":ID" => $id
    ));

    $this->setData($result[0]);
  }

  public function save(){
    $sql = new Sql();

    $results = $sql->select("CALL sp_users_save(:pperson, :plogin, :ppassword, :pemail, :pphone, :pis_admin)", 
    array(
      ":pperson" => $this->getname(),
      ":plogin"  => $this->getlogin(),
      ":ppassword" => $this->getpassword(),
      ":pemail"  => $this->getemail(),
      ":pphone"  => $this->getphone(),
      ":pis_admin" => $this->getis_admin()
    ));

    $this->setData($results[0]);
  }
  
  public function update(){
    $sql = new Sql();

    $result = $sql->select("CALL sp_users_update_save(:pid, :pperson, :pusername, :ppassword, :pemail, :pphone, :pis_admin)", array(
      ":pid" => $this->getid(),
      ":pperson" => $this->getname(),
      ":pusername" => $this->getusername(),
      ":ppassword" => $this->getpassword(),
      ":pemail" => $this->getemail(),
      ":pphone" => $this->getphone(),
      ":pis_admin" => $this->getis_admin(),
    ));

    return $this->setData($result[0]);
  }

  public function delete(){
    $sql = new Sql();
    $sql->select("CALL sp_users_delete(:pid)", array(
      ":pid" => $this->getid(),
    ));
  }
}
?>