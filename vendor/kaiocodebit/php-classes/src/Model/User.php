<?php 

namespace kaiocodebit\Model;

use \kaiocodebit\DB\Sql;
use kaiocodebit\Mailer;
use \kaiocodebit\Model;

class User extends Model {
  const SESSION = "User";
  const SECRET = "PassSec2do13*2JZ";
	const SECRET_IV = "PassSec2do13*2JZ_IV";

  
  /*
    User Auth
  */


  public static function getFromSession(){

    $user = new User();

    if (isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['id'] > 0 ) { 
      $user->setData($_SESSION[User::SESSION]);
    }

    return $user;
  }
  
  public static function checkLogin($is_admin = true){

    if (
      !isset($_SESSION[User::SESSION])
      ||
      !$_SESSION[User::SESSION]
      ||
      !(int)$_SESSION[User::SESSION]['id'] > 0
    ) { 
      return false;
    }

    
    if($is_admin === true && (bool)$_SESSION[User::SESSION]['is_admin'] === true){
      return true;
    } else if ($is_admin === false){
      return true;
    } else {
      return false;
    }

  }


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
    if(User::checkLogin($is_admin)){
      header("Location: /admin/login");
      exit;
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

  public static function getForgot($email){
    $sql = new Sql();
    $users = $sql->select("
      SELECT * 
      FROM persons P
      INNER JOIN users U ON U.id_person = P.id
      WHERE email = :EMAIL", array(
      ":EMAIL" => $email
    ));

    if(count($users) === 0){
      throw new \Exception("Ocorreu um erro ao recuperar sua senha. Tente novamente mais tarde.");
    }else{
      $user =$users[0];
      $recovery = $sql->select("CALL sp_users_password_recoveries_save(:pid, :pip)", array(
        ":pid" => $user["id"],
        ":pip" => $_SERVER["REMOTE_ADDR"]
      ));
      if(count($recovery) === 0){
        throw new \Exception("Ocorreu um erro ao recuperar sua senha. Tente novamente mais tarde.");
      }else{
        $recovery = $recovery[0];
        $code = openssl_encrypt($recovery['id'], 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

				$code = base64_encode($code);
        $url = "http://www.ecompras.com.br/admin/forgot/reset?code=".$code;

        $mailer = new Mailer($user["email"], $user["name"], "Redefinir senha", "forgot", array(
          "name" => $user["name"],
          "link" => $url
        ));

        $mailer->send();

        return $user;
      }
    }
  }

  public static function validForgotDecrypt($code){
    $code = base64_decode($code);

    $id = openssl_decrypt($code, 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

    $sql = new Sql();

    $results = $sql->select("
        SELECT UR.id, P.name, U.id AS id_user
        FROM user_password_recoveries UR
        INNER JOIN users U ON U.id = UR.id_user
        INNER JOIN persons P ON P.id = U.id_person
        WHERE
          UR.id = :id
          AND
          UR.recovery_at IS NULL
          AND
          DATE_ADD(UR.created_at, INTERVAL 1 HOUR) >= NOW();
      ", array(
        ":id"=>$id
      ));

    if(count($results) === 0){
      throw new \Exception("Ocorreu algum problema");
    }else{
      return $results[0];
    }
  }

  public static function setForgotUsed($id){
    $sql = new Sql();

    $sql->select("UPDATE user_password_recoveries SET recovery_at = NOW() WHERE id = :ID", array(
      ":ID" => $id
    ));
  }

  public function setPassword($password){
    $sql = new Sql();

    $sql->select("UPDATE users SET password = :PASSWORD WHERE id = :ID", array(
      ":PASSWORD" => $password,
      ":ID" => $this->getid()
    ));

  }
}
?>