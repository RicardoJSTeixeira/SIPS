<?php

class user {

    public $id;
    public $user_group;
    public $user_level;
    public $allowed_campaigns;
    public $allowed_campaigns_raw;

    public function __construct() {
        $this->id = (isset($_SERVER['PHP_AUTH_USER'])) ? $_SERVER['PHP_AUTH_USER'] : FALSE;

        global $link;
        $this->user_group = FALSE;
        if ($this->id) {
            $query = mysql_query("Select a.user_group,user_level,allowed_campaigns from vicidial_users a left join vicidial_user_groups b on a.user_group=b.user_group Where user='$this->id'") or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {
                $this->user_group = $row["user_group"];
                $this->user_level = $row["user_level"];
                $this->allowed_campaigns_raw = $row["allowed_campaigns"];
                $this->allowed_campaigns = explode(" ",trim(rtrim($this->allowed_campaigns_raw, " -")));
                
            }
        }
    }

}

?>
