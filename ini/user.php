<?php

class user {

    public $id;
    public $user_group;

    public function __construct() {
        $this->id = (isset($_SERVER['PHP_AUTH_USER'])) ? $_SERVER['PHP_AUTH_USER'] : FALSE;

        global $link;
        $this->user_group = FALSE;
        if ($this->id) {
            $query = mysql_query("Select user_group from vicidial_users Where user='$this->id'");
            while ($row = mysql_fetch_assoc($query)) {
                $this->user_group = $row["user_group"];
            }
        }
    }

}

?>
