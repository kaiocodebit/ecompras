<?php 

namespace kaiocodebit;

use kaiocodebit\Model\User;
class PageAdmin extends Page{

    public function __construct($opts = array(), $tpl_dir = "/views/admin/")
    {
        
        $session = $_SESSION[User::SESSION];
        if($session){
            $opts["header_data"] = $_SESSION[User::SESSION];
        }

        parent::__construct($opts, $tpl_dir);
    }
}

?>