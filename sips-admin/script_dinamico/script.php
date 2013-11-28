<?php

class script {

    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

//GET

    public function get_feedbacks() {
        $js = array();
        $query = ("select status,status_name from((SELECT status,status_name FROM vicidial_campaign_statuses ) union all (SELECT status, status_name FROM vicidial_statuses)) a  group by status order by status_name asc");
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("status" => $row["status"], "status_name" => $row["status_name"]);
        }
        return $js;
    }

    public function get_schedule($user_group) {
        $js = array();
        $query = "SELECT id_scheduler,display_text FROM sips_sd_schedulers where active='1' and user_group=:user_group order by display_text";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":user_group" => $user_group));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id" => $row["id_scheduler"], "text" => $row["display_text"]);
        }
        return $js;
    }

    public function get_schedule_by_id($ids) {
        $js = array();
        $query = "SELECT id_scheduler,display_text FROM sips_sd_schedulers where  active='1' and id_scheduler in($ids) order by display_text";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id" => $row["id_scheduler"], "text" => $row["display_text"]);
        }
        return $js;
    }

    public function get_element_tags($id_script) {
        $js = array();
        $query = "SELECT tag,type FROM `script_dinamico` WHERE id_script=:id_script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("tag" => $row["tag"], "type" => $row["type"]);
        }
        return $js;
    }

    public function get_client_info_by_lead_id($lead_id, $user_logged) {
        $js = array();
        $query = "SELECT * from vicidial_list where lead_id=:lead_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row["nome_operador"] = $user_logged["full_name"];
            $js = $row;
        }
        return $js;
    }

    public function get_tag_fields($id_script) {
        $js = array();
        $campaigns = array();
        $query = "SELECT id_camp_linha FROM script_assoc where id_script=:id_script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $campaigns[] = $row["id_camp_linha"];
        }
        foreach ($campaigns as $value) {
            $query = "Select campaign_name from vicidial_campaigns where campaign_id = :value;";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(":value" => $value));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $js[$value]["text"] = $row['campaign_name'];

            $query = "SELECT indice,Name,Display_name FROM `vicidial_list_ref`  where campaign_id =:value and active='1'  GROUP BY name";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(":value" => $value));
            $js[$value]["lista"] = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $js[$row["campaign_id"]]["lista"][] = array("id" => $row["indice"], "value" => $row["Name"], "name" => $row["Display_name"]);
            }
        }
        return $js;
    }

    public function get_camp_linha_by_id_script() {
        $js = array();

        $query = "SELECT * FROM script_assoc ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id_script" => $row["id_script"], "id_camp_linha" => $row["id_camp_linha"], "tipo" => $row["tipo"]);
        }
        return $js;
    }

    public function get_scripts($user_group) {
        $js = array();
        $user_group_temp = "where user_group=:user_group";
        $variables = array(":user_group" => $user_group);
        if ($user_group == "ADMIN") {
            $variables = array();
            $user_group_temp = "";
        }
        $query = "SELECT * FROM script_dinamico_master $user_group_temp";
        $stmt = $this->db->prepare($query);
        $stmt->execute($variables);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[$row["id"]] = array("id" => $row["id"], "name" => $row["name"]);
        }
        return $js;
    }

    public function get_scripts_by_campaign($id_campaign) {
        $js = array();
        $query = "SELECT * FROM script_dinamico_master sdm inner join script_assoc sa on sa.id_script=sdm.id where sa.id_camp_linha=:id_campaign";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_campaign" => $id_campaign));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js = array("id" => $row["id"], "name" => $row["name"]);
        }
        return $js;
    }

    public function get_scripts_by_id_script($id_script) {
        $js = array();
        $query = "SELECT * FROM script_dinamico_master where id=:id_script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js = array("id" => $row["id"], "name" => $row["name"]);
        }
        return $js;
    }

    public function get_results_to_populate($lead_id, $id_script) {
        $js = array();
        $query = "SELECT b.id_script,a.lead_id,a.tag_elemento,a.valor,b.type,a.param_1 FROM script_result a inner join script_dinamico b on a.tag_elemento=b.tag  where a.lead_id=? and b.id_script=? and a.unique_id= (select max(unique_id) from script_result where lead_id=?) ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($lead_id, $id_script, $lead_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id_script" => $row["id_script"], "lead_id" => $row["lead_id"], "tag_elemento" => $row["tag_elemento"], "valor" => $row["valor"], "type" => $row["type"], "param1" => $row["param_1"]);
        }
        return $js;
    }

    public function get_pages($id_script) {
        $js = array();
        $query = "SELECT id,name,pos FROM script_dinamico_pages where id_script=:id_script order by pos";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id" => $row["id"], "name" => $row["name"], "pos" => $row["pos"]);
        }
        return $js;
    }

    public function get_data_render($id_script) {
        $js = array();
        $query = "SELECT sd.id,sd.tag,sd.id_script,id_page,type,ordem,dispo,texto,placeholder,max_length,values_text,default_value,required,hidden,param1 FROM script_dinamico sd inner join script_dinamico_pages sdp on sd.id_page=sdp.id  WHERE sd.id_script=:id_script  order by sdp.pos,sd.ordem asc";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id" => $row["id"], "tag" => $row["tag"], "id_script" => $row["id_script"], "id_page" => $row["id_page"], "type" => $row["type"], "ordem" => $row["ordem"], "dispo" => $row["dispo"], "texto" => $row["texto"], "placeholder" => json_decode($row["placeholder"]), "max_length" => $row["max_length"], "values_text" => json_decode($row["values_text"]), "default_value" => $row["default_value"], "required" => $row["required"] == 1, "hidden" => $row["hidden"] == 1, "param1" => $row["param1"]);
        }
        return $js;
    }

    public function get_data($id_script, $id_page) {
        $js = array();
        $query = "SELECT * FROM `script_dinamico` WHERE id_script=:id_script and id_page=:id_page order by ordem asc";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script, ":id_page" => $id_page));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id" => $row["id"], "tag" => $row["tag"], "id_script" => $row["id_script"], "id_page" => $row["id_page"], "type" => $row["type"], "ordem" => $row["ordem"], "dispo" => $row["dispo"], "texto" => $row["texto"], "placeholder" => json_decode($row["placeholder"]), "max_length" => $row["max_length"], "values_text" => json_decode($row["values_text"]), "default_value" => $row["default_value"], "required" => $row["required"] == 1, "hidden" => $row["hidden"] == 1, "param1" => $row["param1"]);
        }
        return $js;
    }

    public function get_data_individual($id) {
        $js = array();
        $query = "SELECT * FROM `script_dinamico` WHERE id=:id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $js = array("id" => $row["id"], "tag" => $row["tag"], "id_script" => $row["id_script"], "id_page" => $row["id_page"], "type" => $row["type"], "ordem" => $row["ordem"], "dispo" => $row["dispo"], "texto" => $row["texto"], "placeholder" => json_decode($row["placeholder"]), "max_length" => $row["max_length"], "values_text" => json_decode($row["values_text"]), "default_value" => $row["default_value"], "required" => $row["required"] == 1, "hidden" => $row["hidden"] == 1, "param1" => $row["param1"]);
        return $js;
    }

    public function get_rules_by_trigger($tag_trigger, $id_script) {
        $js = array();
        $query = "SELECT * FROM `script_rules` WHERE tag_trigger=:tag_trigger and id_script=:id_script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script, ":tag_trigger" => $tag_trigger));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id" => $row["id"], "id_script" => $row["id_script"], "tipo_elemento" => $row["tipo_elemento"], "tag_trigger" => $row["tag_trigger"], "tag_trigger2" => json_decode($row["tag_trigger2"]), "tag_target" => json_decode($row["tag_target"]), "tipo" => $row["tipo"], "param1" => $row["param1"], "param2" => json_decode($row["param2"]));
        }
        return $js;
    }

    public function get_rules($id_script) {
        $js = array();
        $query = "SELECT * FROM `script_rules` WHERE id_script=:id_script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id" => $row["id"], "id_script" => $row["id_script"], "tipo_elemento" => $row["tipo_elemento"], "tag_trigger" => $row["tag_trigger"], "tag_trigger2" => json_decode($row["tag_trigger2"]), "tag_target" => json_decode($row["tag_target"]), "tipo" => $row["tipo"], "param1" => $row["param1"], "param2" => json_decode($row["param2"]));
        }
        return $js;
    }

    public function get_campaign_linha_inbound($campaigns, $user_is_all_campaigns) {
        $js = array();
        if ($user_is_all_campaigns) {
            $campaigns_query = "";
        } else {
            $campaigns_query = "and campaign_id in('$campaigns')";
        }
        $query = "SELECT  campaign_id,campaign_name  FROM  vicidial_campaigns where active='y' $campaigns_query  order by campaign_name ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js["campaign"][] = array("id" => $row["campaign_id"], "name" => $row["campaign_name"]);
        }
        $query = "SELECT group_id,group_name FROM vicidial_inbound_groups";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js["linha_inbound"][] = array("id" => $row["group_id"], "name" => $row["group_name"]);
        }
        return $js;
    }

    public function iscloud() {
        $js = array();
        $query = "SELECT cloud FROM servers";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($row[0] == "1");
    }

    public function has_rules($tag) {
        $js = array();
        $temp = 0;
        $query = "SELECT count(id) as count FROM script_rules where tag_trigger=:tag";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":tag" => $tag));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $temp = $row["count"];
        }
        return $temp;
    }

    public function get_image_pdf() {
        $js = array();
        $path = getcwd() . "/files/";
        $show = array('.jpg', '.gif', '.png', '.jpeg', '.pdf');

        $select = "<select name=\"select_box\">";
        $select .= "<option value='' selected>Selecione uma opção</option>\n";
        $dh = @opendir($path);
        $temp_ext = "";
        while (false !== ( $file = readdir($dh) )) {
            $ext = substr($file, -4, 4);
            if (in_array($ext, $show)) {
                if ($ext == ".pdf") {
                    $temp_ext = "pdf";
                } else {
                    $temp_ext = "image";
                }
                $select .= "<option data-type='$temp_ext' value='$file'>$file</option>\n";
            }
        }
        $select .= "</select>";
        closedir($dh);
        return $select;
    }

    public function get_php_ajax() {
        $js = array();
        $path = getcwd() . "/files/";
        $show = array('.php');

        $select = "<select name=\"select_box1\">";
        $select .= "<option value=''>Selecione uma opção</option>\n";
        $dh = @opendir($path);
        $temp_ext = "";
        while (false !== ( $file = readdir($dh) )) {
            $ext = substr($file, -4, 4);
            if (in_array($ext, $show)) {
                $select .= "<option  value='$file'>$file</option>\n";
            }
        }
        $select .= "</select>";
        closedir($dh);
        return $select;
    }

//EDIT


    public function edit_script($name, $id_script, $campaign) {
        $js = array();
        $query = "update script_dinamico_master set name=:name where id=:id_script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script, ":name" => $name));
        $query = "delete from script_assoc where id_script=:id_script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));
        foreach ($campaign as $value) {
            $query = "INSERT INTO `script_assoc` values(:id_script,:value,'campaign')";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(":id_script" => $id_script, ":value" => $value));
        }
        foreach ($linha_inbound as $value) {
            $query = "INSERT INTO `script_assoc` values(:id_script,:value,'linha_inbound')";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(":id_script" => $id_script, ":value" => $value));
        }
        return 1;
    }

    public function edit_page($old_pos, $new_pos, $id_script, $name, $id_pagina) {
        $js = array();
        $query = "update script_dinamico_pages set pos=:old_pos where pos=:new_pos and  id_script=:id_script";

        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script, ":old_pos" => $old_pos, ":new_pos" => $new_pos));
        $query = "update script_dinamico_pages set name=:name,pos=:new_pos where id=:id_pagina";

        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_pagina" => $id_pagina, ":name" => $name, ":new_pos" => $new_pos));
        return 1;
    }

    public function edit_item($id_script, $id_page, $type, $ordem, $dispo, $texto, $placeholder, $max_length, $values_text, $default_value, $required, $hidden, $param1, $id) {
        $js = array();
        $values_text = (!isset($values_text)) ? array() : $values_text;
        $query = "UPDATE script_dinamico SET id_script=?,id_page=?,type=?,ordem=?,dispo=?,texto=?,placeholder=?,max_length=?,values_text=?,default_value=?,required=?,hidden=?,param1=? WHERE id=?";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($id_script, $id_page, $type, $ordem, $dispo, $texto, json_encode($placeholder), $max_length, json_encode($values_text), $default_value, $required, $hidden, $param1, $id));
        return 1;
    }

    public function edit_item_order($ordem, $id) {
        $js = array();
        $query = "UPDATE script_dinamico SET ordem=:ordem WHERE id=:id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":ordem" => $ordem, ":id" => $id));
        return 1;
    }

//  ADD

    public function add_page($id_script, $pos) {
        $js = array();
        $query = "INSERT INTO `asterisk`.`script_dinamico_pages` (id,id_script,name,pos) VALUES (NULL,:id_script,'Página nova',:pos)";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script, ":pos" => $pos));
        return 1;
    }

    public function add_script($user_group) {
        $js = array();
        $query = "INSERT INTO `asterisk`.`script_dinamico_master` (id,name,user_group) VALUES (NULL,'Script novo',?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($user_group));

        $query = "INSERT INTO `asterisk`.`script_dinamico_pages` (id,id_script,name,pos) VALUES (NULL,?,'Página nova','1')";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($this->db->lastInsertId()));
        return 1;
    }

    public function add_item($id_page, $id_script, $type, $ordem, $dispo, $texto, $placeholder, $max_length, $values_text, $default_value, $required, $hidden, $param1) {
        $js = array();
        $query = "UPDATE script_dinamico SET ordem=ordem+1 where ordem>=? and id_page=? ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($ordem, $id_page));
        $values_text = (!isset($values_text)) ? array() : $values_text;
//get max tag--------------------------------------------------------
        $tag = 0;
        $query = "SELECT max(tag) as max_tag FROM script_dinamico where id_script=?";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($id_script));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $tag = (!isset($row["max_tag"])) ? 0 : $row["max_tag"] + 1;

        $query = "INSERT INTO script_dinamico (`id`, `tag`, `id_script`, `id_page`, `type`, `ordem`, `dispo`, `texto`, `placeholder`, `max_length`, `values_text`, `default_value`, `required`, `hidden`, `param1`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $this->db->prepare($query);

        $stmt->execute(array("NULL", $tag, $id_script, $id_page, $type, $ordem, $dispo, $texto, json_encode($placeholder), $max_length, json_encode($values_text), $default_value, $required, $hidden, $param1));
        return 1;
    }

    public function add_rules($tag_trigger2, $tag_trigger, $tag_target, $id_script, $tipo_elemento, $tipo, $param1, $param2) {
        $js = array();
        if (!empty($tag_trigger2) && !empty($tag_target)) {
            $query = "INSERT INTO `asterisk`.`script_rules` (id,id_script,tipo_elemento,tag_trigger,tag_trigger2,tag_target,tipo,param1,param2) VALUES (?,?,?,?,?,?,?,?,?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array("NULL", $id_script, $tipo_elemento, $tag_trigger, json_encode($tag_trigger2), json_encode($tag_target), $tipo, $param1, json_encode($param2)));
        }
        return 1;
    }

    public function duplicate_script($user_group, $nome_script, $id_script) {
        $js = array();
        $query = "INSERT INTO script_dinamico_master (id,name,user_group) VALUES (NULL,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($nome_script . " duplicado", $user_group));
        $temp_script_page = $this->db->lastInsertId();

        $query = "INSERT INTO script_rules (id,id_script,tipo_elemento,tag_trigger,tag_trigger2,tag_target,tipo,param1,param2) select NULL,?,tipo_elemento,tag_trigger,tag_trigger2,tag_target,tipo,param1,param2 from script_rules where id_script=? and tipo!='goto'";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($temp_script_page, $id_script));
        $query = "SELECT id,id_script,name,pos FROM script_dinamico_pages where id_script=?";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($id_script));

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//pages
            $query = "INSERT INTO script_dinamico_pages (id,id_script,name,pos) values(NULL, ?,?,?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array($temp_script_page, $row['name'], $row['pos']));
            $temp_page = $this->db->lastInsertId();
//rules de go-to
            $query = "INSERT INTO script_rules (id,id_script,tipo_elemento,tag_trigger,tag_trigger2,tag_target,tipo,param1,param2) select NULL,?,tipo_elemento,tag_trigger,tag_trigger2,tag_target,tipo,param1,? from script_rules where param2=? and id_script=? and tipo='goto'";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array($temp_script_page, $this->db->lastInsertId(), $row['id'], $id_script));
//elements
            $query = "INSERT INTO script_dinamico (`id`,tag, `id_script`,id_page, type, `ordem`,dispo, `texto`, `placeholder`, `max_length`, `values_text`,default_value,required,hidden,param1) select NULL,tag,?,?,type, `ordem`,dispo, `texto`, `placeholder`, `max_length`, `values_text`,default_value,required,hidden,param1 from script_dinamico where id_script=? and id_page= ?  ";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array($temp_script_page, $temp_page, $id_script, $row['id']));
        }
        return 1;
    }

// DELETE
    public function delete_page($pos, $id_script, $id_pagina) {
          $js = array();
          $query = "update script_dinamico_pages set pos=pos-1 where pos>:pos and id_script=:id_script ";
          $stmt = $this->db->prepare($query);
          $stmt->execute(array(":pos" => $pos, ":id_script" => $id_script));

          $query = "delete from script_dinamico_pages  where id=:id_pagina";
          $stmt = $this->db->prepare($query);
          $stmt->execute(array(":id_pagina" => $id_pagina));
         
          
        $query = "select sd.tag as tag from script_dinsd inner join script_rules sr on sd.tag=sr.tag_trigger where sd.id_page=:id_pagina";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_pagina" => $id_pagina));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = "'" . $row["tag"] . "'";
        }
        $js = implode(",", $js);
        if (sizeof($js) > 0) {
            $query = "delete from  script_rules where tag_trigger in($js)";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
        }
        $query = "delete from  script_dinamico where id_page=:id_pagina";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_pagina" => $id_pagina));

        $query = "delete from  script_rules where tag_target=:id_pagina";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_pagina" => json_encode($id_pagina)));

        return 1;
    }

    public function delete_item($ordem, $id_page, $param1, $id) {
        $js = array();
        $query = "UPDATE script_dinamico SET ordem=ordem-1 where ordem>:ordem and id_page=:id_page ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":ordem" => $ordem, ":id_page" => $id_page));

        $query = "delete from script_rules where tag_trigger=:param1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":param1" => $param1));

        $query = "delete from script_dinamico where id=:id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return 1;
    }

    public function delete_script($id_script) {
        $js = array();
        $query = "delete from script_assoc where id_script=:id_script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));
        $query = "delete from script_dinamico_master where id=:id_script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));
        $query = "delete from script_dinamico_pages where id_script=:id_script ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));
        $query = "delete from script_dinamico where id_script=:id_script ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));
        $query = "delete from script_rules where id_script=:id_script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));
    }

    public function delete_rule($id) {
        $js = array();
        $query = "delete from script_rules  where id=:id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return 1;
    }

    // FORM

    public function save_form_result($id_script, $results, $user_id, $unique_id, $campaign_id, $lead_id, $admin_review) {
        $js = array();
        $sql = array();

        if ($admin_review == "1") {
            $unique_id = time() . "." . rand(1, 1000);


            $query = "INSERT INTO vicidial_log (`uniqueid`, `lead_id`, `list_id`, `campaign_id`, `call_date`, `start_epoch`, `end_epoch`, `length_in_sec`, `status`, `phone_code`, `phone_number`, `user`, `comments`, `processed`, `user_group`, `term_reason`, `alt_dial`)
                select :unique_id, `lead_id`, `list_id`, `campaign_id`, :date, NULL, NULL, `length_in_sec`, 'ESA', `phone_code`, `phone_number`, `user`, 'edit', `processed`, `user_group`, `term_reason`, `alt_dial` from vicidial_log where lead_id=:lead_id order by uniqueid desc limit 1";

            $stmt = $this->db->prepare($query);
            $stmt->execute(array(":unique_id" => $unique_id, ":date" => date("Y-m-d H:i:s"), ":lead_id" => $lead_id));
        }

        foreach ($results as $row) {
            if ($row['value'] != "") {
                $temp = explode(",", $row['name']);
                if (isset($temp[2]))
                    $sql[] = "(null,'" . date('Y-m-d H:i:s') . "',$id_script,'$user_id','$unique_id','$campaign_id','$lead_id','$temp[0]','" . mysql_real_escape_string($row['value']) . "','$temp[2];$temp[1]')";
                else
                    $sql[] = "(null,'" . date('Y-m-d H:i:s') . "',$id_script,'$user_id','$unique_id','$campaign_id','$lead_id','$temp[0]', '" . mysql_real_escape_string($row['value']) . "', '$temp[1]')";
            }
        }
        $query = "INSERT INTO `script_result`(`id`,date,id_script,user_id,unique_id,campaign_id,lead_id, `tag_elemento`, `valor`,param_1) VALUES  " . implode(',', $sql);
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return 1;
    }

}
