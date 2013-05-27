<?php

$PHP_AUTH_USER = $_SERVER[PHP_AUTH_USER];
$IP = $_SERVER[REMOTE_ADDR];

function delete_table_row($table, $where) {
    $q = "DELETE FROM $table WHERE $where";
    if (mysql_query($q) != FALSE)
        return array(true, $q);
    print "Query: {$q} resulted in: " . mysql_error();
    return false;
}

// columns and values are arrays
function insert_table_data($table, $columns, $values) {

    if (!$table || !$columns || !$values) {
        print "insert_table_data($table, $columns, $values); has failed! insufficient parameters";
        return false;
    }

    $cols = array_to_list($columns, "`");
    $vals = array_to_list($values, "'");

    $query = "INSERT INTO $table ($cols) VALUES ($vals)";

    if (mysql_query($query) != FALSE)
        return array(mysql_insert_id(), $query);

    return false;
}

function combine_columns_and_values($columns, $values, $cq = "`", $vq = "'") {
    $pairs = "";
    $cols = count($columns);
    for ($i = 0; $i < $cols; $i++) {
        $pairs .= $cq . $columns[$i] . $cq . "=" . $vq . $values[$i] . $vq;
        if ($i + 1 < $cols)
            $pairs .= ",";
    }
    return $pairs;
}

function set_table_data($table, $columns, $values, $where = "", $limit = "") {

    $pair = combine_columns_and_values($columns, $values);

    $query = "UPDATE $table SET $pair";

    if ($where)
        $query .= " WHERE $where";
    if ($limit)
        $query .= " LIMIT $limit";

    $q = mysql_query($query);

    if ($q != FALSE)
        return array(true, $q);

    print "set_table_data($query): " . mysql_error();

    return false;
}

function get_table_data($table, $columns, $where = "", $order = "", $limit = "") {

    $query = "SELECT $columns FROM $table";

    if ($where)
        $query .= " WHERE $where";
    if ($order)
        $query .= " ORDER BY $order";
    if ($limit)
        $query .= " LIMIT $limit";

    //print_g("query string = ".$query);

    $q = mysql_query($query);
    if ($q != FALSE) {
        $a = array();
        $i = 0;

        while (($r = mysql_fetch_row($q)) != FALSE)
            $a[$i++] = $r;

        return array($a, $q);
    } else {
        print "get_table_data($query): " . mysql_error();
        return false;
    }
}

function log($topic, $event, $id, $query, $comments = "") {
    global $link,$IP,$PHP_AUTH_USER;
    $stmt = "INSERT INTO vicidial_admin_log set event_date=NOW(), user='$PHP_AUTH_USER', ip_address='$IP', event_section='$topic', event_type='ADD', record_id='$id', event_code='$event', event_sql='" . mysql_real_escape_string($query) . "', event_notes='$comments';";
    if (!mysql_query($stmt, $link)) {
        print "Log Error: ".  mysql_error();
    }
}

//DID :-)  
class DID {

    function create($did_pattern, $did_description) {
        $rslt = insert_table_data("vicidial_inbound_dids", array("did_pattern", "did_description"), array($did_pattern, $did_description));
        if ($rslt != FALSE) {
            log("DID", "Create DID", $rslt[0], $rslt[1]);
            return $rslt[0];
        }
        return FALSE;
    }

    function edit($id, $columns, $values) {
        $rslt = set_table_data("vicidial_inbound_dids", $columns, $values, "did_id='$id'");
        if ($rslt != FALSE) {
            log("DID", "Edit DID", $id, $rslt[1]);
            return $rslt[0];
        }
        return FALSE;
    }

    function delete($id) {
        $rslt = delete_table_row("vicidial_inbound_dids", "did_id='$id'");
        if ($rslt != FALSE) {
            log("DID", "Delete DID", $id, $rslt[1]);
            return $rslt[0];
        }
        return FALSE;
    }

    function get($columns, $id) {
        $rslt = get_table_data("vicidial_inbound_dids", $columns, "did_id='$id'");
        if ($rslt != FALSE) {
            log("DID", "GET DID", $id, $rslt[1]);
            return $rslt[0];
        }
        return FALSE;
    }

    function get_all($columns, $where = "", $order = "", $limit = "") {
        $rslt = get_table_data("vicidial_inbound_dids", $columns, $where, $order, $limit);
        if ($rslt != FALSE) {
            log("DID", "GET DIV", "", $rslt[1]);
            return $rslt[0];
        }
        return FALSE;
    }

}

//IVR #CAPTAIN OBVIOUS STRIKES AGAIN#
class IVR {

    function create($menu_id, $menu_name) {
        $rslt = insert_table_data("vicidial_call_menu", array("menu_id", "menu_name"), array($menu_id, $menu_name));
        if ($rslt != FALSE) {
            log("IVR", "Create IVR", $rslt[0], $rslt[1]);
            return $rslt[0];
        }
        return FALSE;
    }

    function edit($id, $columns, $values) {
        $rslt = set_table_data("vicidial_call_menu", $columns, $values, "menu_id='$id'");
        if ($rslt != FALSE) {
            log("IVR", "Edit IVR", $id, $rslt[1]);
            return $rslt[0];
        }
        return FALSE;
    }

    function delete($id) {
        $rslt = delete_table_row("vicidial_call_menu", "menu_id='$id'");
        if ($rslt != FALSE) {
            log("IVR", "Delete IVR", $id, $rslt[1]);
            return $rslt[0];
        }
        return FALSE;
    }

    function get($columns, $id) {
        $rslt = get_table_data("vicidial_call_menu", $columns, "menu_id='$id'");
        if ($rslt != FALSE) {
            log("IVR", "GET IVR", $id, $rslt[1]);
            return $rslt[0];
        }
        return FALSE;
    }

    function get_all($columns, $where = "", $order = "", $limit = "") {
        $rslt = get_table_data("vicidial_call_menu", $columns, $where, $order, $limit);
        if ($rslt != FALSE) {
            log("IVR", "GET IVR", "", $rslt[1]);
            return $rslt[0];
        }
        return FALSE;
    }

    function create_option($menu_id, $option_value, $option_description, $option_route, $option_route_value, $option_route_value_context) {
        $ext = get_table_data("vicidial_call_menu", array("menu_id"), "menu_id='$menu_id' and option_value='$option_value'");
        if (count($ext[0]) == 0) {
            $rslt = insert_table_data("vicidial_call_menu_options", array("menu_id", "option_value", "option_description", "option_route", "option_route_value", "option_route_value_context"), array($menu_id, $option_value, $option_description, $option_route, $option_route_value, $option_route_value_context));
            if ($rslt != FALSE) {
                log("IVR OPTION", "Create IVR OPTION", "$menu_id|$option_value", $rslt[1]);
                return TRUE;
            }
            return FALSE;
        }
    }

    function edit_option($menu_id, $option_value, $option_description, $option_route, $option_route_value, $option_route_value_context) {
        $rslt = set_table_data("vicidial_call_menu", array("option_description", "option_route", "option_route_value", "option_route_value_context"), array($option_description, $option_route, $option_route_value, $option_route_value_context), "menu_id='$menu_id' and option_value='$option_value'");
        if ($rslt != FALSE) {
            log("IVR", "Edit IVR OPTION", "$menu_id|$option_value", $rslt[1]);
            return $rslt[0];
        }
        return FALSE;
    }

    function delete_option($menu_id, $option_value) {
        $rslt = delete_table_row("vicidial_call_menu_options", "menu_id='$menu_id' and option_value='$option_value'");
        if ($rslt != FALSE) {
            log("IVR", "Delete IVR OPTION", "$menu_id|$option_value", $rslt[1]);
            return $rslt[0];
        }
        return FALSE;
    }

}

//Inbound Group
class IBG {

    function create($group_id, $group_name, $group_color, $active, $web_form_address, $voicemail_ext, $next_agent_call, $fronter_display, $script_id, $get_call_launch) {
        $stmtA = "INSERT INTO vicidial_campaign_stats (campaign_id) values('$group_id');";
        $rslt = mysql_query($stmtA, $link);

        $rslt = insert_table_data("vicidial_inbound_groups", array("group_id", "group_name", "group_color", "active", "web_form_address", "voicemail_ext", "next_agent_call", "fronter_display", "ingroup_script", "get_call_launch"), array($group_id, $group_name, $group_color, $active, mysql_real_escape_string($web_form_address), $voicemail_ext, $next_agent_call, $fronter_display, $script_id, $get_call_launch));
        if ($rslt != FALSE) {
            log("IBG", "Create IBG", $rslt[0], $rslt[1]);
            $tmp = insert_table_data("vicidial_campaign_stats", array("campaign_id"), array($group_id));
            if ($tmp != FALSE) {
                log("Campaigns", "Create IBG Campaign", $tmp[0], $tmp[1], "Criação de campanha, para visualzação estatística de grupo Inbound");
            } else {
                $this->delete($rslt[0]);
                return FALSE;
            }
            return $rslt[0];
        }
        return FALSE;
    }

    function edit($id, $columns, $values) {
        $rslt = set_table_data("vicidial_inbound_groups", $columns, $values, "group_id='$id'");
        if ($rslt != FALSE) {
            log("IBG", "Edit IBG", $id, $rslt[1]);
            return $rslt[0];
        }
        return FALSE;
    }

    function delete($id) {
        $rslt = delete_table_row("vicidial_call_menu", "group_id='$id'");
        if ($rslt != FALSE) {
            log("IBG", "Edit IBG", $id, $rslt[1]);
            $tmp = delete_table_row("vicidial_campaign_stats", "campaign_id='$id'");
            if ($tmp != FALSE) {
                log("Campaigns", "Delete IBG Campaign", $id, $tmp[1], "Eliminou campanha, de visualzação estatística de grupo Inbound");
            } else {
                $this->delete($rslt[0]);
                return FALSE;
            }
            return $rslt[0];
        }
        return FALSE;
    }

    function get($columns, $id) {
        $rslt = get_table_data("vicidial_call_menu", $columns, "group_id='$id'");
        if ($rslt != FALSE) {
            log("IBG", "GET IBG", $id, $rslt[1]);
            return $rslt[0];
        }
        return FALSE;
    }

    function get_all($columns, $where = "", $order = "", $limit = "") {
        $rslt = get_table_data("vicidial_call_menu", $columns, $where, $order, $limit);
        if ($rslt != FALSE) {
            log("IBG", "GET IBG", "", $rslt[1]);
            return $rslt[0];
        }
        return FALSE;
    }

}

function reload_asterisk() {
    $rslt = set_table_data("servers", array("rebuild_conf_files"), array("Y"), "generate_vicidial_conf='Y' and active_asterisk_server='Y'");
    if ($rslt != FALSE) {
        log("Server", "Rebuild Conf files", "", $rslt[1]);
        return $rslt[0];
    }
    return FALSE;
}

?>
