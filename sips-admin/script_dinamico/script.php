<?php

class script {

    protected $db;

    public function __construct($db) {
        $this->db = $db;
    }

//GET


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
        $query = "SELECT tag,type FROM `script_dinamico` WHERE id_script=:id_script order by ordem";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("tag" => $row["tag"], "type" => $row["type"]);
        }
        return $js;
    }

    public function get_client_info_by_lead_id($lead_id, $operador) {
        $js = array();
        $query = "SELECT `phone_number`, `title`, `first_name`, `middle_initial`, `last_name`, `address1`, `address2`, `address3`, `city`, `state`, `province`, `postal_code`, `country_code`, `date_of_birth`, `alt_phone`, `email`, `security_phrase`, `comments`,`rank`, `owner`,`extra1`, `extra2`, `extra3`, `extra4`, `extra5`, `extra6`, `extra7`, `extra8`, `extra9`, `extra10`, `extra11`, `extra12`, `extra13`, `extra14`, `extra15` from vicidial_list where lead_id=:lead_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":lead_id" => $lead_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row["nome_operador"] = $operador;
            $js = $row;
        }
        return $js;
    }

    public function get_tag_fields($id_script) {
        $campaigns = array();
        //CAMPOS DEFAULT
        $dfields[] = array("campaign_name" => "Campos Gerais", "display_name" => "Nome do Operador", "name" => "NOME_OPERADOR");
        $dfields[] = array("campaign_name" => "Campos Gerais", "display_name" => "Nome", "name" => "FIRST_NAME");
        $dfields[] = array("campaign_name" => "Campos Gerais", "display_name" => "Telefone", "name" => "PHONE_NUMBER");
        $dfields[] = array("campaign_name" => "Campos Gerais", "display_name" => "Telemóvel", "name" => "ADDRESS3");
        $dfields[] = array("campaign_name" => "Campos Gerais", "display_name" => "Telefone Alternativo", "name" => "ALT_PHONE");
        $dfields[] = array("campaign_name" => "Campos Gerais", "display_name" => "Morada", "name" => "ADDRESS1");
        $dfields[] = array("campaign_name" => "Campos Gerais", "display_name" => "Código Postal", "name" => "POSTAL_CODE");
        $dfields[] = array("campaign_name" => "Campos Gerais", "display_name" => "E-mail", "name" => "EMAIL");
        $dfields[] = array("campaign_name" => "Campos Gerais", "display_name" => "Comentários", "name" => "COMMENTS");
        $query = "SELECT id_camp_linha FROM script_assoc where id_script=:id_script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $campaigns[] = $row["id_camp_linha"];
        }
        foreach ($campaigns as $value) {
            $query = "Select campaign_name,campaign_id from vicidial_campaigns where campaign_id = :campaign_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(":campaign_id" => $value));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $query = "SELECT Name,Display_name   FROM vicidial_list_ref WHERE campaign_id=:campaign_id AND active=1 Order by field_order ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(":campaign_id" => $value));
            while ($row1 = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $dfields[] = array("campaign_name" => $row["campaign_name"], "campaign_id" => $row["campaign_id"], "display_name" => $row1["Display_name"], "name" => $row1["Name"]);
            }
        }
        return $dfields;
    }

    public function get_camp_linha_by_id_script() {
        $js = array();
        $query = "SELECT id_script,id_camp_linha,tipo  FROM script_assoc ";
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

    public function get_scripts_by_lead_id($lead_id) {
        $js = array();
        $query = "SELECT sdm.id,sdm.name FROM script_dinamico_master sdm inner join script_assoc sa on sa.id_script=sdm.id inner join vicidial_list vl on vl.list_id=sa.id_camp_linha where vl.lead_id=?";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($lead_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js = array("id" => $row["id"], "name" => $row["name"]);
        }
        return $js;
    }

    public function get_render_scripts_by_campaign($id_campaign) {
        $js = array();
        $query = "SELECT sdm.id,sdm.name  FROM script_dinamico_master sdm inner join script_assoc sa on sa.id_script=sdm.id where sa.id_camp_linha=:id_campaign";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_campaign" => $id_campaign));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js = array("id" => $row["id"], "name" => $row["name"]);
        }
        return $js;
    }

    public function get_script_by_campaign($campaign_id) {
        $query = "SELECT a.id id,a.id_script,a.texto name,a.values_text,a.type,a.tag   FROM script_dinamico a  left join script_assoc b on a.id_script=b.id_script where b.id_camp_linha =? ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($campaign_id));
        $js = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $js;
    }

    public function get_scripts_by_id_script($id_script) {
        $js = array();
        $query = "SELECT id,name FROM script_dinamico_master where id=:id_script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js = array("id" => $row["id"], "name" => $row["name"]);
        }
        return $js;
    }

    public function get_results_to_populate($search_spice, $lead_id, $id_script, $unique_id) {
        $js = array();

        if (isset($unique_id)) {
            $query = "SELECT b.id_script,a.lead_id,a.tag_elemento,a.valor,b.type,a.param_1 FROM script_result a inner join script_dinamico b on a.tag_elemento=b.tag  where a.lead_id=? and b.id_script=? and a.unique_id=?";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array($lead_id, $id_script, $unique_id));
        } else {
            if ($search_spice == "true") {
                $query = "SELECT b.id_script,a.lead_id,a.tag_elemento,a.valor,b.type,a.param_1 FROM script_result a inner join script_dinamico b on a.tag_elemento=b.tag  where a.lead_id=? and b.id_script=? and a.unique_id= 0 ";
                $stmt = $this->db->prepare($query);
                $stmt->execute(array($lead_id, $id_script));
            } else {
                $query = "SELECT b.id_script,a.lead_id,a.tag_elemento,a.valor,b.type,a.param_1 FROM script_result a inner join script_dinamico b on a.tag_elemento=b.tag  where a.lead_id=? and b.id_script=? and a.unique_id= (select max(unique_id) from script_result where lead_id=?) ";
                $stmt = $this->db->prepare($query);
                $stmt->execute(array($lead_id, $id_script, $lead_id));
            }
        }

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

    public function get_data_render($id_script, $lead_id, $operador) {
        $js = array();
        $client_info = array();
        if (isset($lead_id)) {
            $query = "SELECT `phone_number`, `title`, `first_name`, `middle_initial`, `last_name`, `address1`, `address2`, `address3`, `city`, `state`, `province`, `postal_code`, `country_code`, `date_of_birth`, `alt_phone`, `email`, `security_phrase`, `comments`,`rank`, `owner`,`extra1`, `extra2`, `extra3`, `extra4`, `extra5`, `extra6`, `extra7`, `extra8`, `extra9`, `extra10`, `extra11`, `extra12`, `extra13`, `extra14`, `extra15` from vicidial_list where lead_id=:lead_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(":lead_id" => $lead_id));
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $row["nome_operador"] = $operador;
                $client_info = $row;
            }
        }

        $query = "SELECT sd.id,sd.tag,sd.id_script,id_page,type,ordem,dispo,texto,placeholder,max_length,values_text,default_value,required,hidden,param1 FROM script_dinamico sd inner join script_dinamico_pages sdp on sd.id_page=sdp.id  WHERE sd.id_script=:id_script  order by sdp.pos,sd.ordem asc";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

// CAMPOS DINAMICOS -> §
            if (isset($lead_id)) {
                $temp = "";
                if (preg_match_all("/\§[A-Za-z0-9\_]+\§/", $row["texto"], $temp)) {
                    $temp = $temp[0];
                    foreach ($temp as $value) {
                        $value1 = str_replace("§", "", $value);
                        $row["texto"] = preg_replace("/$value/", $client_info[strtolower($value1)], $row["texto"]);
                    }
                }
                if ($row["type"] == "texto") {
                    $placeholder = json_decode($row["placeholder"]);
                    if (preg_match_all("/\§[A-Za-z0-9\_]+\§/", $placeholder, $temp)) {
                        $temp = $temp[0];
                        foreach ($temp as $value) {
                            $value1 = str_replace("§", "", $value);
                            $placeholder = preg_replace("/$value/", $client_info[strtolower($value1)], $placeholder);
                        }
                        $row["placeholder"] = json_encode($placeholder);
                    }
                }
                if ($row["type"] == "legend" || $row["type"] == "textfield") {
                    $values_text = json_decode($row["values_text"]);

                    if (preg_match_all("/\§[A-Za-z0-9\_]+\§/", $values_text, $temp)) {
                        $temp = $temp[0];
                        foreach ($temp as $value) {
                            $value1 = str_replace("§", "", $value);
                            $values_text = preg_replace("/$value/", $client_info[strtolower($value1)], $values_text);
                        }
                        $row["values_text"] = json_encode($values_text);
                    }
                }
            }
            // TAGS -> @          
            $temp = "";
            if (preg_match_all("/\@(\d{1,5})\@/", $row["texto"], $temp)) {
                $temp = $temp[0];
                foreach ($temp as $value) {
                    $value1 = str_replace("@", "", $value);

                    $row["texto"] = preg_replace("/$value/", "<span data-id=" . $value1 . " class='" . $value1 . "tag tagReplace'></span>", $row["texto"]);
                }
            }
            if ($row["type"] == "legend" || $row["type"] == "textfield") {
                $values_text = json_decode($row["values_text"]);
                if (preg_match_all("/\@(\d{1,5})\@/", $values_text, $temp)) {
                    $temp = $temp[0];
                    foreach ($temp as $value) {
                        $value1 = str_replace("@", "", $value);
                        $values_text = preg_replace("/\@(\d{1,5})\@/", "<span data-id=" . $value . " class='" . $value . "tag tagReplace'></span>", $values_text);
                    }
                    $row["values_text"] = json_encode($values_text);
                }
            }
            $js[] = array("id" => $row["id"], "tag" => $row["tag"], "id_script" => $row["id_script"], "id_page" => $row["id_page"], "type" => $row["type"], "ordem" => $row["ordem"], "dispo" => $row["dispo"], "texto" => $row["texto"], "placeholder" => json_decode($row["placeholder"]), "max_length" => $row["max_length"], "values_text" => json_decode($row["values_text"]), "default_value" => json_decode($row["default_value"]), "required" => $row["required"] == 1, "hidden" => $row["hidden"] == 1, "param1" => $row["param1"]);
        }
        return $js;
    }

    public function get_data($id_script, $id_page) {
        $js = array();
        $query = "SELECT * FROM `script_dinamico` WHERE id_script=:id_script and id_page=:id_page order by ordem asc";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script, ":id_page" => $id_page));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = array("id" => $row["id"], "tag" => $row["tag"], "id_script" => $row["id_script"], "id_page" => $row["id_page"], "type" => $row["type"], "ordem" => $row["ordem"], "dispo" => $row["dispo"], "texto" => $row["texto"], "placeholder" => json_decode($row["placeholder"]), "max_length" => $row["max_length"], "values_text" => json_decode($row["values_text"]), "default_value" => json_decode($row["default_value"]), "required" => $row["required"] == 1, "hidden" => $row["hidden"] == 1, "param1" => $row["param1"]);
        }
        return $js;
    }

    public function get_data_individual($id) {
        $js = array();
        $query = "SELECT * FROM `script_dinamico` WHERE id=:id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $js = array("id" => $row["id"], "tag" => $row["tag"], "id_script" => $row["id_script"], "id_page" => $row["id_page"], "type" => $row["type"], "ordem" => $row["ordem"], "dispo" => $row["dispo"], "texto" => $row["texto"], "placeholder" => json_decode($row["placeholder"]), "max_length" => $row["max_length"], "values_text" => json_decode($row["values_text"]), "default_value" => json_decode($row["default_value"]), "required" => $row["required"] == 1, "hidden" => $row["hidden"] == 1, "param1" => $row["param1"]);
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

    public function iscloud() {
        $query = "SELECT cloud FROM servers";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_BOTH);
        return ($row[0] == "1");
    }

    public function has_rules($tag, $id_script) {
        $temp = 0;
        $query = "SELECT count(id) as count FROM script_rules where tag_trigger=:tag and id_script=:script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":tag" => $tag, ":script" => $id_script));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $temp = $row["count"];
        }
        return $temp;
    }

    public function get_image_pdf() {
        $js = array();
        $path = getcwd() . "/files/";
        $show = array('.jpg', '.gif', '.png', '.jpeg', '.pdf');
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
                if ($file != "dummy.gitignore")
                    $js[] = array("type" => $temp_ext, "value" => $file);
            }
        }

        closedir($dh);
        return $js;
    }

    public function get_php_ajax() {
        $path = getcwd() . "/files/";
        $show = array('.php');
        $js = array();
        $dh = @opendir($path);
        $temp_ext = "";
        while (false !== ( $file = readdir($dh) )) {
            $ext = substr($file, -4, 4);
            if (in_array($ext, $show)) {
                $js[] = $file;
            }
        }

        closedir($dh);
        return $js;
    }

//EDIT


    public function edit_script($name, $id_script, $campaign, $linha_inbound, $bd) {
        $query = "update script_dinamico_master set name=:name where id=:id_script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script, ":name" => $name));
        $query = "delete from script_assoc where id_script=:id_script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script));

        if (isset($campaign)) {
            foreach ($campaign as $value) {
                $query = "INSERT INTO `script_assoc` values(:id_script,:value,'campaign')";
                $stmt = $this->db->prepare($query);
                $stmt->execute(array(":id_script" => $id_script, ":value" => $value));
            }
        }
        if (isset($linha_inbound)) {
            foreach ($linha_inbound as $value) {
                $query = "INSERT INTO `script_assoc` values(:id_script,:value,'linha_inbound')";
                $stmt = $this->db->prepare($query);
                $stmt->execute(array(":id_script" => $id_script, ":value" => $value));
            }
        }
        if (isset($bd)) {
            foreach ($bd as $value) {
                $query = "INSERT INTO `script_assoc` values(:id_script,:value,'bd')";
                $stmt = $this->db->prepare($query);
                $stmt->execute(array(":id_script" => $id_script, ":value" => $value));
            }
        }
        return 1;
    }

    public function edit_page($old_pos, $new_pos, $id_script, $name, $id_pagina) {
        $query = "update script_dinamico_pages set pos=:old_pos where pos=:new_pos and  id_script=:id_script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script, ":old_pos" => $old_pos, ":new_pos" => $new_pos));
        $query = "update script_dinamico_pages set name=:name,pos=:new_pos where id=:id_pagina";
        $stmt = $this->db->prepare($query);

        return $stmt->execute(array(":id_pagina" => $id_pagina, ":name" => $name, ":new_pos" => $new_pos));
    }

    public function edit_item($id_script, $id_page, $type, $ordem, $dispo, $texto, $placeholder, $max_length, $values_text, $default_value, $required, $hidden, $param1, $id) {
        $values_text = (!isset($values_text)) ? array() : $values_text;
        $query = "UPDATE script_dinamico SET id_script=?,id_page=?,type=?,ordem=?,dispo=?,texto=?,placeholder=?,max_length=?,values_text=?,default_value=?,required=?,hidden=?,param1=? WHERE id=?";
        $stmt = $this->db->prepare($query);

        return $stmt->execute(array($id_script, $id_page, $type, $ordem, $dispo, $texto, json_encode($placeholder), $max_length, json_encode($values_text), json_encode($default_value), $required, $hidden, $param1, $id));
    }

    public function edit_item_order($ordem, $id) {
        $query = "UPDATE script_dinamico SET ordem=:ordem WHERE id=:id";
        $stmt = $this->db->prepare($query);

        return $stmt->execute(array(":ordem" => $ordem, ":id" => $id));
    }

//  ADD

    public function add_page($id_script, $pos) {
        $query = "INSERT INTO `script_dinamico_pages` (id,id_script,name,pos) VALUES (NULL,:id_script,'Página nova',:pos)";
        $stmt = $this->db->prepare($query);

        return $stmt->execute(array(":id_script" => $id_script, ":pos" => $pos));
    }

    public function add_script($user_group) {
        $query = "INSERT INTO `script_dinamico_master` (id,name,user_group) VALUES (NULL,'Script novo',?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($user_group));

        $query = "INSERT INTO`script_dinamico_pages` (id,id_script,name,pos) VALUES (NULL,?,'Página nova','1')";
        $stmt = $this->db->prepare($query);

        return $stmt->execute(array($this->db->lastInsertId()));
    }

    public function add_item($id_page, $id_script, $type, $ordem, $dispo, $texto, $placeholder, $max_length, $values_text, $default_value, $required, $hidden, $param1) {
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


        return $stmt->execute(array("NULL", $tag, $id_script, $id_page, $type, $ordem, $dispo, $texto, json_encode($placeholder), $max_length, json_encode($values_text), json_encode($default_value), $required, $hidden, $param1));
    }

    public function add_rules($tag_trigger2, $tag_trigger, $tag_target, $id_script, $tipo_elemento, $tipo, $param1, $param2) {
        if (!empty($tag_trigger2) && !empty($tag_target)) {
            $query = "INSERT INTO `script_rules` (id,id_script,tipo_elemento,tag_trigger,tag_trigger2,tag_target,tipo,param1,param2) VALUES (?,?,?,?,?,?,?,?,?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array("NULL", $id_script, $tipo_elemento, $tag_trigger, json_encode($tag_trigger2), json_encode($tag_target), $tipo, $param1, json_encode($param2)));
        }
        return 1;
    }

    public function duplicate_script($user_group, $nome_script, $id_script) {
        $query = "INSERT INTO script_dinamico_master (id,name,user_group) VALUES (NULL,?,?)";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($nome_script . " duplicado", $user_group));
        $temp_script_id = $this->db->lastInsertId();

        $query = "INSERT INTO script_rules (id,id_script,tipo_elemento,tag_trigger,tag_trigger2,tag_target,tipo,param1,param2) select NULL,?,tipo_elemento,tag_trigger,tag_trigger2,tag_target,tipo,param1,param2 from script_rules where id_script=? and tipo!='goto'";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($temp_script_id, $id_script));
        $query = "SELECT id,id_script,name,pos FROM script_dinamico_pages where id_script=?";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array($id_script));

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
//pages
            $query1 = "INSERT INTO script_dinamico_pages (`id_script`,`name`,`pos`) values(?,?,?)";
            $stmt1 = $this->db->prepare($query1);
            $stmt1->execute(array($temp_script_id, $row['name'], $row['pos']));
            $temp_page = $this->db->lastInsertId();
            //rules de go-to

            $query2 = "INSERT INTO script_rules (id,id_script,tipo_elemento,tag_trigger,tag_trigger2,tag_target,tipo,param1,param2) select NULL,:temp_id_script,tipo_elemento,tag_trigger,tag_trigger2,:tag_target,tipo,param1,param2 from script_rules where tag_target=:tag_target_old and tipo='goto'";
            $stmt2 = $this->db->prepare($query2);
            $stmt2->execute(array(":temp_id_script" => $temp_script_id, ":tag_target" => json_encode($temp_page), ":tag_target_old" => json_encode((string)$row["id"])));
//elements
            $query3 = "INSERT INTO script_dinamico (`id`,tag, `id_script`,id_page, type, `ordem`,dispo, `texto`, `placeholder`, `max_length`, `values_text`,default_value,required,hidden,param1) select NULL,tag,?,?,type, `ordem`,dispo, `texto`, `placeholder`, `max_length`, `values_text`,default_value,required,hidden,param1 from script_dinamico where id_script=? and id_page= ?  ";
            $stmt3 = $this->db->prepare($query3);
            $stmt3->execute(array($temp_script_id, $temp_page, $id_script, $row['id']));
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



        $query = "select sd.tag as tag from script_dinamico sd inner join script_rules sr on sd.tag=sr.tag_trigger where sd.id_page=:id_pagina and sd.id_script=:id_script group by tag";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_pagina" => $id_pagina, ":id_script" => $id_script));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = $row["tag"];
        }

        $rules = implode("','", $js);

        if (count($js)) {
            $query = "delete from  script_rules where tag_trigger in('$rules') and id_script=:id_script";
            $stmt = $this->db->prepare($query);
            $stmt->execute(array(":id_script" => $id_script));
        }

        $query = "delete from  script_dinamico where id_page=:id_pagina";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_pagina" => $id_pagina));

        $query = "delete from  script_rules where tag_target=:id_pagina";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_pagina" => json_encode($id_pagina)));

        return 1;
    }

    public function delete_item($ordem, $id_page, $param1, $id, $id_script) {
        $query = "UPDATE script_dinamico SET ordem=ordem-1 where ordem>:ordem and id_page=:id_page ";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":ordem" => $ordem, ":id_page" => $id_page));
        $query = "delete from script_rules where tag_trigger=:param1 and id_script=:id_script";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":param1" => $param1, ":id_script" => $id_script));
        $query = "delete from script_dinamico where id=:id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $query = "delete from script_result where id_script=:id_script and tag_elemento=:tag";
        $stmt = $this->db->prepare($query);
        $stmt->execute(array(":id_script" => $id_script, ":tag" => $param1));
        return 1;
    }

    public function delete_script($id_script) {
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
        return 1;
    }

    public function delete_rule($id) {
        $query = "delete from script_rules  where id=:id";
        $stmt = $this->db->prepare($query);

        return $stmt->execute(array(":id" => $id));
    }

    // FORM
    public function save_form_result($save_overwrite, $id_script, $results, $user_id, $unique_id, $campaign_id, $lead_id, $admin_review) {
        $sql = array();
        foreach ($results as $row) {
            if ($row['value'] != "") {
                $temp = explode("###", $row['name']);

                if (isset($temp[2])) {
                    //table inputs
                    $sql[] = "('" . date('Y-m-d H:i:s') . "','$id_script','$user_id','$unique_id','$campaign_id','$lead_id','$temp[0]','" . mysql_real_escape_string($row['value']) . "','$temp[2];$temp[1]')";
                } else {
                    //table radios e todos os outros
                    $sql[] = "('" . date('Y-m-d H:i:s') . "','$id_script','$user_id','$unique_id','$campaign_id','$lead_id','$temp[0]', '" . mysql_real_escape_string($row['value']) . "', '$temp[1]')";
                }
            }
        }
        if (count($sql)) {
            if ($admin_review == "1") {
                $query = "Delete from script_result where unique_id=:unique_id and lead_id=:lead_id";
                $stmt = $this->db->prepare($query);
                $stmt->execute(array(":unique_id" => $unique_id, ":lead_id" => $lead_id));
            }
            if ($save_overwrite == true) {
                $query = "Delete from script_result where unique_id=:unique_id and lead_id=:lead_id";
                $stmt = $this->db->prepare($query);
                $stmt->execute(array(":unique_id" => 0, ":lead_id" => $lead_id));
            }

            $query = "INSERT INTO `script_result`(`date`,`id_script`,`user_id`,`unique_id`,`campaign_id`,`lead_id`, `tag_elemento`, `valor`,`param_1`) VALUES  " . implode(',', $sql);
            $stmt = $this->db->prepare($query);
            return $stmt->execute();
        } else
            return "no Data to be saved";
    }

}
