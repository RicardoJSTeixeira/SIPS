<?php

class user {

    public $id = FALSE;
    public $full_name;
    public $active=FALSE;
    public $user_group = FALSE;
    public $user_level;
    public $allowed_campaigns;
    public $allowed_campaigns_raw;
    public $is_all_campaigns=FALSE;

    public function __construct() {
        $this->id = (isset($_SERVER['PHP_AUTH_USER'])) ? $_SERVER['PHP_AUTH_USER'] : FALSE;

        global $link;
        if ($this->id) {
            $query = mysql_query("Select a.user_group,full_name,user_level,allowed_campaigns,active from vicidial_users a left join vicidial_user_groups b on a.user_group=b.user_group Where user='$this->id'") or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {
                $this->full_name = $row["full_name"];
                $this->active = $row["active"]=="Y";
                $this->user_group = $row["user_group"];
                $this->user_level = $row["user_level"];
                $this->allowed_campaigns_raw = $row["allowed_campaigns"];
                $this->is_all_campaigns=preg_match("/-ALL-CAMPAIGNS-/",$this->allowed_campaigns_raw);
                $this->allowed_campaigns = explode(" ",trim(rtrim($this->allowed_campaigns_raw, " -")));
                
            }
        }
    }

}

?>
