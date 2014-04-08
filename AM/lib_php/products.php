<?php

Class products {

    public function __construct($db) {
        $this->_db = $db;
    }

    public function get_products_to_datatable($product_editable) {
        $output['aaData'] = [];


        $stmt = $this->_db->prepare("SELECT id,name,price,max_req_m,max_req_s,category,type,color,active,deleted from spice_product where deleted=0");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            $row[2] = $row[2] . "€";
            $row["price"] = $row["price"] . "€";
            $stmt1 = $this->_db->prepare("SELECT highlight,active,data_inicio,data_fim from spice_promocao where product_id=:id and data_inicio<=:data1 and data_fim>=:data2 order by id desc limit 1");
            $date = date("Y-m-d");
            $stmt1->execute(array(":id" => $row[0], ":data1" => $date, ":data2" => $date));
            $row1 = $stmt1->fetch(PDO::FETCH_BOTH);

            $row[5] = ucfirst($row[5]);

            $row[6] = ucwords(implode(", ", json_decode($row[6])));


            $level = $this->get_levels($row[0]);
            $row[10] = $level;
            $row["level"] = $level;


            if (isset($row1["active"]))
                $active = (bool) $row1["active"];
            else
                $active = (bool) $row["active"];


            if ($product_editable == "true")
                $row[7] = "<button data-active='" . $active . "' data-highlight='" . (bool) $row1["highlight"] . "' data-level='" . $row["level"] . "' data-deleted='" . (bool) $row["deleted"] . "' class='btn btn_ver_produto icon-alone hide' data-product_id='" . $row[0] . "'><i class='icon-eye-open'></i></button><button class='btn btn_editar_produto btn-primary  icon-alone' data-product_id='" . $row[0] . "' data-level='" . $row["level"] . "'><i class='icon-pencil'></i></button><button class='btn btn_apagar_produto btn-danger  icon-alone' data-product_id='" . $row[0] . "'><i class='icon-remove'></i></button>";
            else
                $row[7] = "<button data-active='" . $active . "' data-highlight='" . (bool) $row1["highlight"] . "' data-level='" . $row["level"] . "'  data-deleted='" . (bool) $row["deleted"] . "' class='btn btn_ver_produto  icon-alone' data-product_id='" . $row[0] . "'><i class='icon-eye-open'></i></button>";
            $output['aaData'][] = $row;
        }
        return $output;
    }

    public function get_products() {

        $stmt = $this->_db->prepare("SELECT id,name,price, max_req_m,max_req_s,category,type,color,active from spice_product where deleted=0");
        $stmt->execute();
        $children = array();
        $parent = array();

        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {





            //  $level = in_array($row[0], $product_levels) ? array_search($row[0], $product_levels) : 1;
            //  $row[9] = $level;
            //  $row["level"] = $level;
            $row[6] = json_decode($row[6]);
            $row["type"] = json_decode($row["type"]);
            $row[7] = json_decode($row[7]);
            $row["color"] = json_decode($row["color"]);
            $row["parents"] = $parent;
            $row["children"] = $children;
            $output[] = $row;
        }




        $relations = array();
        $stmt = $this->_db->prepare("select parent,child from spice_product_assoc");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $relations[$row["parent"]][] = $row["child"];
        }




        foreach ($relations as $key => $value) {
            
        }



        return $output;
    }

    public function remove_product($id) {
        $stmt = $this->_db->prepare("delete from spice_product_assoc where child=:id or parent=:id2");
        $stmt->execute(array(":id" => $id, ":id2" => $id));

        $stmt = $this->_db->prepare("update spice_product set deleted=1  where id=:id");
        return $stmt->execute(array(":id" => $id));
    }

    public function add_product($name, $price, $max_req_m, $max_req_s, $parent, $category, $type, $color, $active) {

        $stmt = $this->_db->prepare("insert into spice_product ( `name`, `price`,  `max_req_m`, `max_req_s`, `category`,`type`,`color`,`active`,`deleted`) values (:name,:price, :max_req_m,:max_req_s,:category,:type,:color,:active,0) ");
        $stmt->execute(array(":name" => $name, ":price" => $price, ":max_req_m" => $max_req_m, ":max_req_s" => $max_req_s, ":category" => $category, ":type" => json_encode($type), ":color" => json_encode($color), ":active" => 1));
        $last_id = $this->_db->lastInsertId();

        if (isset($parent)) {
            $stmt1 = $this->_db->prepare("insert into spice_product_assoc (`parent`, `child`) values (:parent,:child) ");
            foreach ($parent as $value) {
                $stmt1->execute(array(":parent" => $value, ":child" => $last_id));
            }
        }
        return array($last_id, $name, $price, $max_req_m, $max_req_s, $category, $type, "<button data-active='" . $active . "' data-highlight='0'  class='btn btn_ver_produto  icon-alone hide'   data-level='0' data-product_id='" . $last_id . "'><i class='icon-eye-open'></i></button><button data-level='0' class='btn btn_editar_produto btn-primary  icon-alone' data-product_id='" . $last_id . "'><i class='icon-pencil'></i></button><button class='btn btn_apagar_produto btn-danger  icon-alone' data-product_id='" . $last_id . "'><i class='icon-remove'></i></button>");
    }

}

class product extends products {

    protected $_db;
    protected $_id;
    protected $_name;
    protected $_price;
    protected $_max_req_m;
    protected $_max_req_s;
    protected $_parent;
    protected $_category;
    protected $_type;
    protected $_color;
    protected $_active;

    public function __construct(PDO $db, $id) {
        $this->_db = $db;
        $stmt = $this->_db->prepare("SELECT id,name , price,max_req_m,max_req_s,category,type,color,active from spice_product where id=:id ");
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->_id = $row["id"];
        $this->_name = $row["name"];
        $this->_price = $row["price"];
        $this->_max_req_m = $row["max_req_m"];
        $this->_max_req_s = $row["max_req_s"];
        $this->_category = $row["category"];
        $this->_type = $row["type"];
        $this->_color = $row["color"];
        $this->_active = $row["active"];
    }

    public function edit_product($name, $price, $max_req_m, $max_req_s, $parent, $category, $type, $color, $active) {
        $this->_name = $name;
        $this->_price = $price;
        $this->_max_req_m = $max_req_m;
        $this->_max_req_s = $max_req_s;
        $this->_category = $category;
        $this->_type = $type;
        $this->_color = $color;
        $this->_active = $active == "true" ? 1 : 0;
        $stmt = $this->_db->prepare("delete from spice_product_assoc where child=:child");
        $stmt->execute(array(":child" => $this->_id));
        if (isset($parent))
            foreach ($parent as $value) {
                $stmt = $this->_db->prepare("insert into spice_product_assoc (`parent`, `child`) values (:parent,:child) ");
                $stmt->execute(array(":parent" => $value, ":child" => $this->_id));
            };
        return $this->edit_product_save();
    }

    public function edit_product_save() {
        $stmt = $this->_db->prepare("update spice_product set name=:name,price=:price, max_req_m=:max_req_m,max_req_s=:max_req_s,category=:category,type=:type,color=:color,active=:active where id=:id");
        return $stmt->execute(array(":id" => $this->_id, ":name" => $this->_name, ":price" => $this->_price, ":max_req_m" => $this->_max_req_m, ":max_req_s" => $this->_max_req_s, ":category" => $this->_category, ":type" => json_encode($this->_type), ":color" => json_encode($this->_color), ":active" => $this->_active));
    }

    public function get_info() {
        $children = array();
        $parent = array();
        $parent_ids = array();
        //get children  
        $stmt = $this->_db->prepare("select a.id,a.name,a.category from spice_product  a inner join spice_product_assoc b on b.child=a.id where b.parent=:parent and a.deleted=0");
        $stmt->execute(array(":parent" => $this->_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $children[] = $row;
        }
        //get parent
        $stmt = $this->_db->prepare("select a.id,a.name,a.category from spice_product  a inner join spice_product_assoc b on b.parent=a.id where b.child=:child and a.deleted=0");
        $stmt->execute(array(":child" => $this->_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $parent[] = $row;
            $parent_ids[] = $row["id"];
        }
        $level = $this->get_parent_level($this->_id);
        return array("id" => $this->_id, "name" => $this->_name, "price" => $this->_price, "max_req_m" => $this->_max_req_m, "children" => $children, "parent" => $parent, "parent_ids" => $parent_ids, "max_req_s" => $this->_max_req_s, "category" => $this->_category, "type" => json_decode($this->_type), "color" => json_decode($this->_color), "active" => $this->_active, "level" => $level);
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
