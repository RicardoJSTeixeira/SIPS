<?php

class user {

    public $id = FALSE;
    public $password;
    public $full_name;
    public $active = FALSE;
    public $user_group = FALSE;
    public $user_level;
    public $allowed_campaigns;
    public $allowed_campaigns_raw;
    public $is_all_campaigns = FALSE;
    public $is_script_dinamico = FALSE;

    public function __construct($username = "", $password = "") {
        if (!strlen($username)) {
            $this->id = (isset($_SERVER['PHP_AUTH_USER'])) ? $_SERVER['PHP_AUTH_USER'] : FALSE;
            $this->password = (isset($_SERVER['PHP_AUTH_PW'])) ? $_SERVER['PHP_AUTH_PW'] : FALSE;
        } else {
            $this->id = $username;
            $this->password = $password;
        }

        global $link;
        if ($this->id) {
            $query = mysql_query("Select a.user_group,full_name,user_level,allowed_campaigns,active,agent_fullscreen from vicidial_users a left join vicidial_user_groups b on a.user_group=b.user_group Where user='$this->id' AND pass='$this->password'") or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {
                $this->full_name = $row["full_name"];
                $this->active = $row["active"] == "Y";
                $this->user_group = $row["user_group"];
                $this->user_level = $row["user_level"];
                $this->allowed_campaigns_raw = $row["allowed_campaigns"];
                $this->is_all_campaigns = preg_match("/-ALL-CAMPAIGNS-/", $this->allowed_campaigns_raw);
                $this->allowed_campaigns = explode(" ", trim(rtrim($this->allowed_campaigns_raw, " -")));
                $this->is_script_dinamico = $row["agent_fullscreen"] == "Y";
            }
        }
    }

}

class users extends user {

    function __construct() {
        parent::__construct();
    }

    public function newAdmin($username, $password, $user_group, $name = "") {

        global $link;

        $query = "INSERT INTO vicidial_users (
                user,
                pass,
                full_name,
                user_level,
                user_group,
                phone_login,
                phone_pass,
                delete_users,
                delete_user_groups,
                delete_lists,
                delete_campaigns,
                delete_ingroups,
                delete_remote_agents,
                load_leads,
                campaign_detail,
                ast_admin_access,
                ast_delete_phones,
                delete_scripts,
                modify_leads,
                hotkeys_active,
                change_agent_campaign,
                agent_choose_ingroups,
                closer_campaigns,
                scheduled_callbacks,
                agentonly_callbacks,
                agentcall_manual,
                vicidial_recording,
                vicidial_transfers,
                delete_filters,
                alter_agent_interface_options,
                closer_default_blended,
                delete_call_times,
                modify_call_times,
                modify_users,
                modify_campaigns,
                modify_lists,
                modify_scripts,
                modify_filters,
                modify_ingroups,
                modify_usergroups,
                modify_remoteagents,
                modify_servers,
                view_reports,
                vicidial_recording_override,
                alter_custdata_override,
                qc_enabled,
                qc_user_level,
                qc_pass,
                qc_finish,
                qc_commit,
                add_timeclock_log,
                modify_timeclock_log,
                delete_timeclock_log,
                alter_custphone_override,
                vdc_agent_api_access,
                modify_inbound_dids,
                delete_inbound_dids,
                active, alert_enabled,
                download_lists,
                agent_shift_enforcement_override,
                manager_shift_enforcement_override,
                shift_override_flag, export_reports,
                delete_from_dnc, email, user_code,
                territory, allow_alerts,
                agent_choose_territories,
                custom_one,
                custom_two,
                custom_three,
                custom_four,
                custom_five,
                voicemail_id,
                agent_call_log_view_override,
                callcard_admin,
                agent_choose_blended,
                realtime_block_user_info,
                custom_fields_modify,
                force_change_password,
                agent_lead_search_override)
                VALUE (
                '$username',
                '$password',
                '$name',
                '8',
                '$user_group',
                '',
                '',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                '1',
                'DISABLED',
                'NOT_ACTIVE',
                '',
                '0',
                '',
                '',
                '',
                '1',
                '1',
                '1',
                'NOT_ACTIVE',
                '1',
                '1',
                '1',
                'Y',
                '0',
                '1',
                'DISABLED',
                '1',
                '0',
                '1',
                '1',
                '',
                '',
                '',
                '0',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'DISABLED',
                '1',
                '1',
                '0',
                '1',
                'N',
                'NOT_ACTIVE')";
        mysql_query($query) or die(mysql_error());

        $SQL_log = mysql_real_escape_string($query);
        $stmt = "INSERT INTO vicidial_admin_log set event_date='" . date("Y-m-d H:i:s") . "', user='" . $this->user_group . "', ip_address='" . $_SERVER['REMOTE_ADDR'] . "', event_section='USERGROUPS', event_type='ADD', record_id='$user_group', event_code='ADMIN ADD USER GROUP', event_sql='" . $SQL_log . "', event_notes='';";
        mysql_query($stmt) or die(mysql_error());

        return true;
    }

    public function newUser($username, $password, $user_group, $name = "") {

        global $link;

        $query = "INSERT INTO vicidial_users (
	user,
	pass,
	full_name,
	user_level,
	user_group,
	agentonly_callbacks,
	agentcall_manual)
	VALUE
	('$username',
         '$password',
         '$name',
         '1',
         '$user_group',
         '1',
         '1')";
        mysql_query($query) or die(mysql_error());

        $SQL_log = mysql_real_escape_string($query);
        $stmt = "INSERT INTO vicidial_admin_log set event_date='" . date("Y-m-d H:i:s") . "', user='" . $this->user_group . "', ip_address='" . $_SERVER['REMOTE_ADDR'] . "', event_section='USERS', event_type='ADD', record_id='$username', event_code='ADMIN ADD USER', event_sql='" . $SQL_log . "', event_notes='user: $username';";
        mysql_query($stmt) or die(mysql_error());
        return true;
    }

    public function newUserGroup($user_group, $group_name) {

        global $link;

        $query = "INSERT INTO vicidial_user_groups(user_group,group_name,allowed_campaigns) values('$user_group','$group_name',' -');";
        mysql_query($query) or die(mysql_error());
        return true;
    }

    public function getUser($username) {

        global $link;

        $query = "Select a.user_group,full_name,user_level,allowed_campaigns,active from vicidial_users a left join vicidial_user_groups b on a.user_group=b.user_group Where user='$username'";
        $result = mysql_query($query) or die(mysql_error());
        while ($row = mysql_fetch_assoc($result)) {
            return $row;
        }
        return false;
    }

}

?>
