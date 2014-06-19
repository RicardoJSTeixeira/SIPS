<?php

Class products {

    public function __construct($db) {
        $this->_db = $db;
    }

    public function get_products_to_datatable($product_editable) {
        $output['aaData'] = array();
        $stmt = $this->_db->prepare("SELECT id,name,max_req_m,max_req_s,category,type,color,active,deleted,size from spice_product where deleted=0");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {

            $stmt1 = $this->_db->prepare("SELECT highlight,active,data_inicio,data_fim from spice_promocao where product_id=:id and data_inicio<=:data1 and data_fim>=:data2 order by id desc limit 1");
            $date = date("Y-m-d");
            $stmt1->execute(array(":id" => $row[0], ":data1" => $date, ":data2" => $date));
            $row1 = $stmt1->fetch(PDO::FETCH_NUM);
            $row[4] = ucfirst($row[4]);
            $row[5] = ucwords(implode(", ", json_decode($row[5])));
            if (isset($row1[1])) {
                $active = (bool) $row1[1];
            } else {
                $active = (bool) $row[7];
            }
            if ($product_editable == "true") {
                $row[6] = "<span class='btn-group'>"
                        . "<button class='btn btn_editar_produto btn-primary icon-alone' data-product_id='" . $row[0] . "'><i class='icon-pencil'></i></button>"
                        . "<button data-active='" . $active . "' data-highlight='" . (bool) $row1[0] . "'   data-deleted='" . (bool) $row["deleted"] . "' class='btn btn_ver_produto icon-alone hide' data-product_id='" . $row[0] . "'><i class='icon-eye-open'></i></button>"
                        . "<button class='btn btn_apagar_produto btn-danger icon-alone' data-product_id='" . $row[0] . "'><i class='icon-trash'></i></button>"
                        . "</span>";
            } else {
                $row[6] = "<button data-active='" . $active . "' data-highlight='" . (bool) $row1[0] . "'   data-deleted='" . (bool) $row[8] . "' class='btn btn-info btn_ver_produto  icon-alone' data-product_id='" . $row[0] . "'><i class='icon-eye-open'></i></button>";
            }
            $output['aaData'][] = $row;
        }
        return $output;
    }

    public function get_products($id = null) {
        $relations = array();
        $stmt = $this->_db->prepare("select parent, child from spice_product_assoc");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $relations[$row["parent"]][] = $row["child"];
        }

        $stmt = $this->_db->prepare("SELECT id, name,  max_req_m, max_req_s, category, type, color, active ,size from spice_product where deleted=0");
        $stmt->execute();
        $output = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $row["parents_id"] = array();
            $row["children_id"] = array();
            foreach ($relations as $key => $value) {
                foreach ($value as $value1) {
                    if ($value1 == $row["id"]) {
                        $row["parents_id"][] = $key;
                    }
                    if ($key == $row["id"]) {
                        $row["children_id"][] = $value1;
                    }
                }
            }
            $row["type"] = json_decode($row["type"]);
            $row["color"] = json_decode($row["color"]);

            $output[$row["id"]] = $row;
        }
//ATRIBUIÇÂO DE CHILDS E PARENTS

        foreach ($output as &$value) {
            $value["parent"] = $this->buildTree_parent($output, $value["id"]);
            $value["children"] = $this->buildTree_child($output, $value["id"]);
        }
        foreach ($output as &$value) {
            $value["parent_level"] = $this->get_level_parent($value) - 1;
            $value["children_level"] = $this->get_level_child($value) - 1;
        }


        if ($id) {
            return $output[$id];
        } else {
            $temp = array();
            foreach ($output as $key => $row) {
                $temp[$key] = $row['name'];
            }
            array_multisort($temp, SORT_ASC, $output);
            return $output;
        }
    }

    function buildTree_child(array $elements, $parentId) {
        $branch = array();
        foreach ($elements as $element) {

            if (in_array($parentId, $element["parents_id"])) {

                $branch[] = $element + array("children" => $this->buildTree_child($elements, $element["id"]));
            }
        }
        return $branch;
    }

    function buildTree_parent(array $elements, $childrentId) {
        $branch = array();
        foreach ($elements as $element) {

            if (in_array($childrentId, $element["children_id"])) {

                $branch[] = $element + array("parent" => $this->buildTree_parent($elements, $element["id"]));
            }
        }
        return $branch;
    }

    function get_level_parent($element) {
        $level = 1;
        $temp = array();
        if ($element["parent"]) {
            foreach ($element["parent"] as $value) {
                $temp[] = $level + $this->get_level_parent($value);
            }
            return max($temp);
        }
        return $level;
    }

    function get_level_child($element) {
        $level = 1;
        $temp = array();
        if ($element["children"]) {
            foreach ($element["children"] as $value) {
                $temp[] = $level + $this->get_level_child($value);
            }
            return max($temp);
        }
        return $level;
    }

    public function remove_product($id) {
        $stmt = $this->_db->prepare("delete from spice_product_assoc where child=:id or parent=:id2");
        $stmt->execute(array(":id" => $id, ":id2" => $id));
        $stmt = $this->_db->prepare("update spice_product set deleted=1  where id=:id");
        return $stmt->execute(array(":id" => $id));
    }

    public function add_product($name, $max_req_m, $max_req_s, $parent, $category, $type, $color, $active, $size) {

        $stmt = $this->_db->prepare("insert into spice_product ( `name`,`max_req_m`,`max_req_s`,`category`,`type`,`color`,`active`,`deleted`,`size`) values (:name, :max_req_m,:max_req_s,:category,:type,:color,1,0,:size) ");
        $stmt->execute(array(":name" => $name, ":max_req_m" => $max_req_m, ":max_req_s" => $max_req_s, ":category" => $category, ":type" => json_encode($type), ":color" => $color ? json_encode($color) : json_encode(array()), ":size" => $size));
        $last_id = $this->_db->lastInsertId();

        if (isset($parent)) {
            $stmt1 = $this->_db->prepare("insert into spice_product_assoc (`parent`, `child`) values (:parent,:child) ");
            foreach ($parent as $value) {
                $stmt1->execute(array(":parent" => $value, ":child" => $last_id));
            }
        }

        return array($last_id, $name, $max_req_m, $max_req_s, ucfirst($category), ucwords(implode(", ", json_decode(json_encode($type)))), "<span class='btn-group'>"
            . "<button class='btn btn_editar_produto btn-primary icon-alone' data-product_id='" . $last_id . "'><i class='icon-pencil'></i></button>"
            . "<button data-active='1' data-highlight='0'   data-deleted='0' class='btn btn_ver_produto icon-alone hide' data-product_id='" . $last_id . "'><i class='icon-eye-open'></i></button>"
            . "<button class='btn btn_apagar_produto btn-danger icon-alone' data-product_id='" . $last_id . "'><i class='icon-trash'></i></button>"
            . "</span>");
    }

}

class product extends products {

    protected $_db;
    protected $_id;
    protected $_name;
    protected $_max_req_m;
    protected $_max_req_s;
    protected $_parent;
    protected $_category;
    protected $_type;
    protected $_color;
    protected $_active;
    protected $_size;

    public function __construct(PDO $db, $id) {
        $this->_db = $db;
        $stmt = $this->_db->prepare("SELECT id,name , max_req_m,max_req_s,category,type,color,active,size from spice_product where id=:id ");
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->_id = $row["id"];
        $this->_name = $row["name"];

        $this->_max_req_m = $row["max_req_m"];
        $this->_max_req_s = $row["max_req_s"];
        $this->_category = $row["category"];
        $this->_type = $row["type"];
        $this->_color = $row["color"];
        $this->_active = $row["active"];
        $this->_size = $row["size"];
    }

    public function edit_product($name, $max_req_m, $max_req_s, $parent, $category, $type, $color, $active, $size) {
        $this->_name = $name;

        $this->_max_req_m = $max_req_m;
        $this->_max_req_s = $max_req_s;
        $this->_category = $category;
        $this->_type = $type;
        $this->_size = $size;
        $this->_color = $color ? $color : [];
        $this->_active = $active == "true" ? 1 : 0;
        $stmt = $this->_db->prepare("delete from spice_product_assoc where child=:child");
        $stmt->execute(array(":child" => $this->_id));
        if (isset($parent)) {
            foreach ($parent as $value) {
                $stmt = $this->_db->prepare("insert into spice_product_assoc (`parent`, `child`) values (:parent,:child) ");
                $stmt->execute(array(":parent" => $value, ":child" => $this->_id));
            }
        }
        return $this->edit_product_save();
    }

    public function edit_product_save() {
        $stmt = $this->_db->prepare("update spice_product set name=:name,  max_req_m=:max_req_m,max_req_s=:max_req_s,category=:category,type=:type,color=:color,active=:active,size=:size where id=:id");
        return $stmt->execute(array(":id" => $this->_id, ":name" => $this->_name, ":max_req_m" => $this->_max_req_m, ":max_req_s" => $this->_max_req_s, ":category" => $this->_category, ":type" => json_encode($this->_type), ":color" => json_encode($this->_color), ":active" => $this->_active, ":size" => $this->_size));
    }

    public function add_promotion($active, $highlight, $data_inicio, $data_fim) {
        $stmt = $this->_db->prepare("insert into spice_promocao ( `product_id`,   `highlight`, `active`, `data_inicio`,`data_fim`) values (:product_id, :highlight,:active,:data_inicio,:data_fim) ");
        return $stmt->execute(array(":product_id" => $this->_id, ":highlight" => $highlight == "true" ? 1 : 0, ":active" => $active == "true" ? 1 : 0, ":data_inicio" => $data_inicio, ":data_fim" => $data_fim));
    }

    public function remove_promotion($id_promotion) {
        $stmt = $this->_db->prepare("Delete from spice_promocao where  id=:id_promotion and product_id=:product_id");
        return $stmt->execute(array(":id_promotion" => $id_promotion, ":product_id" => $this->_id));
    }

    public function get_promotion() {
        $js = array();
        $stmt = $this->_db->prepare("select * from spice_promocao where product_id=:product_id");
        $stmt->execute(array(":product_id" => $this->_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $js[] = $row;
        }
        return $js;
    }

}
