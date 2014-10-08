<?php
// database & mysqli
require("../../ini/dbconnect.php");

// post & get
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


if ($action == "get_user_groups") {

    $stmt = "SELECT user_group FROM vicidial_users WHERE user='$curUser'";
    $rslt = mysql_query($stmt, $link);

    $rslt = mysql_fetch_assoc($rslt);
    $grupo = $rslt['user_group'];
    $js = array();

    if ($grupo == 'ADMIN') {
        $query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups  ORDER BY group_name";
    } else {
        $query = "SELECT user_group,group_name from vicidial_user_groups WHERE user_group = '$grupo' ORDER BY group_name";
    }

    $query = mysql_query($query, $link);
    while ($row = mysql_fetch_row($query)) {
        $js['user_groups']['value'][] = $row[0];
        $js['user_groups']['description'][] = $row[1];
    }
    echo json_encode($js);
}


if ($action == "get_users") {

    //Users INICIO
    $query = "select a.user_group,allowed_campaigns,user_level from vicidial_users a inner join `vicidial_user_groups` b on a.user_group=b.user_group where user='$curUser'";
    $result = mysql_query($query) or die(mysql_error());
    $row = mysql_fetch_assoc($result);


    $user_level = $row['user_level'];
    $allowed_camps_regex = str_replace(" ", "|", trim(rtrim($row['allowed_campaigns'], " -")));

    if ($row['user_group'] != "ADMIN") {
        $ret = "WHERE allowed_campaigns REGEXP '$allowed_camps_regex'";


        $user_groups = "";
        $result = mysql_query("SELECT `user_group`, `allowed_campaigns` FROM `vicidial_user_groups` $ret ") or die(mysql_error());
        while ($row1 = mysql_fetch_assoc($result)) {
            $user_groups .= "'$row1[user_group]',";
        }
        $user_groups = rtrim($user_groups, ",");

        $users_regex = "";
        $result = mysql_query("SELECT `user` FROM `vicidial_users` WHERE user_group in ($user_groups) AND user_level < $user_level") or die(mysql_error());
        while ($rugroups = mysql_fetch_assoc($result)) {
            $users_regex .= "^" . mysql_real_escape_string($rugroups['user']) . "$|";
        }
        $users_regex = rtrim($users_regex, "|");
        $users_regex = "AND user REGEXP '$users_regex'";
    }
//Users FIM

    $query = "SELECT user, full_name FROM vicidial_users WHERE active = 'Y'  $users_regex ORDER BY full_name";
    $js = array();

    $query = mysql_query($query, $link) or die(mysql_error());
    while ($row = mysql_fetch_row($query)) {
        $js['users']['value'][] = $row[0];
        $js['users']['description'][] = $row[1];
    }
    echo json_encode($js);
}

if ($action == "submit_msg") {

    $sent_from = mysql_real_escape_string($sent_from);
    $sent_msg = mysql_real_escape_string($sent_msg);
    $sent_msg_type = mysql_real_escape_string($sent_msg_type);

    $today = date("Y-m-d H:i:s");

    $query = mysql_query("SELECT full_name FROM vicidial_users WHERE user='$sent_from'") or die(mysql_error());
    $row = mysql_fetch_row($query);
    $sent_from = $row[0];

    /** @var array $sent_users */
    if (count($sent_users) > 0) {
        foreach ($sent_users as $key => $value) {
            $query = "INSERT INTO sips_msg (`from`, `to`, `msg`, `type`, `event_date`) VALUES ('$sent_from', '$value', '$sent_msg', '$sent_msg_type', '$today')";
            $query = mysql_query($query) or die(mysql_error());
        }
    } else {
        $sent_groups = implode("','", mysql_real_escape_string($sent_groups));
        $query = "SELECT user FROM vicidial_users WHERE user_group IN ('$sent_groups')";
        $query = mysql_query($query) or die(mysql_error());

        while ($row = mysql_fetch_row($query)) {
            $query = "INSERT INTO sips_msg (`from`, `to`, `msg`, `type`, `event_date`) VALUES ('$sent_from', '$row[0]', '$sent_msg', '$sent_msg_type', '$today')";
            mysql_query($query) or die(mysql_error());
        }

    }
}