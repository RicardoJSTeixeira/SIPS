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
        $stmt = $this->_db->prepare('SELECT user,pass,user_level,full_name,allowed_campaigns from vicidial_users a left join vicidial_user_groups b on a.user_group=b.user_group WHERE user=:user');
        $stmt->execute(array(":user" => $this->_username));
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
        $camp= preg_replace("/-ALL-CAMPAIGNS-/",'', $this->_user->allowed_campaigns);
        $camp=explode(" ", trim(rtrim($camp, " -")));
        $camp=$camp[0]; 
        $stmt=$this->_db->prepare("Select list_id From vicidial_lists WHERE campaign_id=:id");
        $stmt->execute(array(":id"=>$camp));
        $lists=$stmt->fetchAll(PDO::FETCH_OBJ);
        $list=$lists[0]->list_id;
        return (object) array("name" => $this->_user->full_name, "username" => $this->_user->user, "campaign"=>$camp, "list_id"=>$list);
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
