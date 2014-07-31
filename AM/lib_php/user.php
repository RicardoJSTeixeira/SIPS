<?php

class UserLogin {

    protected $_username;    // using protected so they can be accessed
    protected $_password; // and overidden if necessary
    protected $_db;       // stores the database handler
    protected $_user;     // stores the user data

    public function __construct(PDO $db) {
        $this->_db = $db;
        session_start();
    }

    public function login($username, $password) {
        $this->_username = $username;
        $this->_password = $password;
        $user = $this->_checkCredentials();
        if ($user) {
            $this->_user = $user; // store it so it can be accessed later
            $_SESSION['user'] = $user;
            $_SESSION['status'] = 'authorized';
            $_SESSION['created'] = time();
            return $user->user;
        }
        return false;
    }

    protected function _checkCredentials() {
        $stmt = $this->_db->prepare('SELECT `user`, `pass`, a.`user_group`, `user_level`, `full_name`, `allowed_campaigns`, `siblings` from vicidial_users a left join vicidial_user_groups b on a.user_group=b.user_group WHERE user=:user and active=:active');
        $stmt->execute(array(":user" => $this->_username, ":active" => 'Y'));
        if ($stmt->rowCount()) {
            $user = $stmt->fetch(PDO::FETCH_OBJ);
            $submitted_pass = $this->_password;
            if ($submitted_pass == $user->pass) {
                return $user;
            }
        }
        return false;
    }

    public function getUser() {
        $camp = preg_replace("/-ALL-CAMPAIGNS-/", '', $this->_user->allowed_campaigns);
        $camp = explode(" ", trim(rtrim($camp, " -")));
        $camp = $camp[0];
        $stmt = $this->_db->prepare("Select list_id From vicidial_lists WHERE campaign_id=:id limit 1");
        $stmt->execute(array(":id" => $camp));
        $lists = $stmt->fetchAll(PDO::FETCH_OBJ);
        $list = $lists[0]->list_id;
        $siblings = json_decode($this->_user->siblings);
        $siblings[] = $this->_user->user;
        return (object) array("name" => $this->_user->full_name, "username" => $this->_user->user, "campaign" => $camp, "list_id" => $list, "user_level" => $this->_user->user_level, "user_group" => $this->_user->user_group, "siblings" => $siblings);
    }

    public function logout() {
        if (isset($_SESSION['status'])) {
            unset($_SESSION['status']);

            if (isset($_COOKIE['session_name'])) {
                setcookie(session_name(), '', time() - 1000);
                session_destroy();
            }
        }
    }

    public function confirm_login() {
        /* if (time() - $_SESSION['created'] < 1800) {
          session_regenerate_id(true);
          $_SESSION['created'] = time();
          } else {
          $this->logout();
          header("location: login.php");
          } */

        if ($_SESSION['status'] == 'authorized') {
            $this->_username = $_SESSION['user']->user;
            $this->_password = $_SESSION['user']->pass;
            $user = $this->_checkCredentials();
            if ($user) {
                $this->_user = $user; // store it so it can be accessed later
            } else {
                $this->logout();
                header("location: login.php");
            }
            return true;
        }
        return false;
    }

}

class UserControler {

    private $_db;
    private $_user;
    private $_ulevel;
    private $_ugroup;

    //User Levels
    const ASSIST = 1;
    const DISP = 2;
    const UMRA = 3;
    const ASM = 5;
    const SUPBO = 6;
    const ADMBO = 7;
    const ADMMKT = 8;
    const ADM = 9;

    public $_ULalias = array(
        "1" => "Assistente",
        "2" => "Técnico",
        "3" => "UMRA",
        "5" => "Supervisores Áreas",
        "6" => "Supervisor Pedidos BackOfice",
        "7" => "Administrador Pedidos do BackOffice",
        "8" => "Administrador Pedidos do Marketing",
        "9" => "Administradores Gerais");

    public function __construct(PDO $db, $user) {
        $this->_db = $db;
        $this->_user = $user->username;
        $this->_ulevel = $user->user_level;
        $this->_ugroup = $user->user_group;
    }

    public function get($username) {
        $query = "SELECT `user`, `pass`, `full_name`, `user_level`, `active`, `siblings` FROM `vicidial_users` WHERE user_group=:user_group and user=:username;";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":user_group" => $this->_ugroup, ":username" => $username));
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return (object) array("user" => $row->user, "pass" => $row->pass, "full_name" => $row->full_name, "user_level" => $row->user_level, "active" => $row->active, "siblings" => json_decode($row->siblings));
    }

    public function getAll() {
        $query = "SELECT `user`, `full_name`, `user_level`, `active` FROM `vicidial_users` WHERE user_group=:user_group;";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":user_group" => $this->_ugroup));
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getActive() {
        $query = "SELECT `user`, `full_name`, `user_level`, `active` FROM `vicidial_users` WHERE user_group=:user_group and active='Y';";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":user_group" => $this->_ugroup));
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function getTypes(){
        return $this->_ULalias;
    }

    public function set($username, $pass, $desc, $ulevel) {
        $query = "INSERT INTO `vicidial_users` (`user`, `pass`, `full_name`, `user_level`, `user_group`, `active`, `siblings`) VALUES (:username, :pass, :desc, :ulevel, :user_group, 'Y', '[]');";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":username" => $username, ":pass" => $pass, ":desc" => $desc, ":ulevel" => $ulevel, ":user_group" => $this->_ugroup));
    }

    public function edit($username, $pass, $desc, $ulevel, $active, $siblings) {
        $query = "UPDATE `vicidial_users` SET `pass`=:pass, `full_name`=:desc, `user_level`=:ulevel, `active`=:active, `siblings`=:siblings WHERE `user`=:username;";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":username" => $username, ":pass" => $pass, ":desc" => $desc, ":ulevel" => $ulevel, ":active" => $active, ":siblings" => json_encode($siblings)));
    }

    public function editPass($username, $pass) {
        $query = "UPDATE `vicidial_users` SET `pass`=:pass WHERE `user`=:username;";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":username" => $username, ":pass" => $pass));
    }

    public function editActive($username, $active) {
        $query = "UPDATE `vicidial_users` SET `active`=:active WHERE `user`=:username;";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":username" => $username, ":active" => $active));
    }

    public function save_proposta($lead_id, $reserva_id, $proposta) {
        $query = "INSERT INTO `spice_proposta` (`lead_id`, `reserva_id`, `data`, `proposta`) VALUES (:lead_id, :reserva_id, :data, :proposta);";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":lead_id" => $lead_id, ":reserva_id" => $reserva_id, ":data" => date('Y-m-d H:i:s'), ":proposta" => json_encode($proposta)));
    }

    public function get_propostas($lead_id) {
        $query = "SELECT reserva_id,data,proposta FROM `spice_proposta` WHERE lead_id=:lead_id;";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        $js = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row["proposta"] = json_decode($row["proposta"]);
            $js[] = $row;
        }

        return $js;
    }

}
