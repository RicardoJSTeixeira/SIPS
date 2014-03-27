<?php

Class products {

    public function __construct($db) {
        $this->_db = $db;
    }

    public function get_products_to_datatable($product_editable) {
        $output['aaData'] = [];
        $stmt = $this->_db->prepare("SELECT id,name,max_req_m,max_req_s,category,type,color from spice_product");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            $row[5] = json_decode($row[5]);

            if ($product_editable == "true")
                $row[6] = "<button class='btn btn_ver_produto' data-product_id='" . $row[0] . "'>Ver</button><button class='btn btn_editar_produto' data-product_id='" . $row[0] . "'>Editar</button><button class='btn btn_apagar_produto' data-product_id='" . $row[0] . "'>Apagar</button>";
            else
                $row[6] = "<button class='btn btn_ver_produto' data-product_id='" . $row[0] . "'>Ver</button>";
            $output['aaData'][] = $row;
        };
        return $output;
    }

    public function get_products_to_datatable_by_id($parent) {
        $stmt = $this->_db->prepare("SELECT id,name, max_req_m,max_req_s,category,type,color from spice_product where parent=:parent");
        $stmt->execute(array(":parent" => $parent));

        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            $row[5] = json_decode($row[5]);
            $output['aaData'][] = $row;
        }
        return $output;
    }

    public function get_products() {
        $stmt = $this->_db->prepare("SELECT id,name, max_req_m,max_req_s,category,type,color from spice_product");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            $row[5] = json_decode($row[5]);
            $row["type"] = json_decode($row["type"]);
            $row[6] = json_decode($row[6]);
            $row["color"] = json_decode($row["color"]);
            $output[] = $row;
        }
        return $output;
    }

    public function remove_product($id) {
        $stmt = $this->_db->prepare("delete from spice_product where id=:id");
        return $stmt->execute(array(":id" => $id));

        $stmt = $this->_db->prepare("delete from spice_product_assoc where parent=:parent or child=:child");
        return $stmt->execute(array(":parent" => $id, ":child" => $id));
    }

    public function remove_products() {
        $stmt = $this->_db->prepare("delete from spice_product_assoc");
        $stmt->execute();
        $stmt = $this->_db->prepare("delete from spice_product");
        return $stmt->execute();
    }

    public function add_product($name, $max_req_m, $max_req_s, $parent, $category, $type, $color) {

        $stmt = $this->_db->prepare("insert into spice_product ( `name`,   `max_req_m`, `max_req_s`, `category`,`type`,`color`) values (:name, :max_req_m,:max_req_s,:category,:type,:color) ");
        $stmt->execute(array(":name" => $name, ":max_req_m" => $max_req_m, ":max_req_s" => $max_req_s, ":category" => $category, ":type" => json_encode($type), ":color" => json_encode($color)));
        $last_id = $this->_db->lastInsertId();

        if (isset($parent))
            foreach ($parent as $value) {

                $stmt1 = $this->_db->prepare("insert into spice_product_assoc (   `parent`, `child`) values (:parent,:child) ");
                $stmt1->execute(array(":parent" => $value, ":child" => $last_id));
            };
        return true;
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

    public function __construct(PDO $db, $id) {
        $this->_db = $db;
        $stmt = $this->_db->prepare("SELECT id,name ,max_req_m,max_req_s,category,type,color from spice_product where id=:id");
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->_id = $row["id"];
        $this->_name = $row["name"];
        $this->_max_req_m = $row["max_req_m"];
        $this->_max_req_s = $row["max_req_s"];
        $this->_category = $row["category"];
        $this->_type = $row["type"];
        $this->_color = $row["color"];
    }

    public function edit_product($name, $max_req_m, $max_req_s, $parent, $category, $type, $color) {
        $this->_name = $name;
        $this->_max_req_m = $max_req_m;
        $this->_max_req_s = $max_req_s;
        $this->_category = $category;
        $this->_type = $type;
        $this->_color = $color;
        $stmt = $this->_db->prepare("delete from spice_product_assoc where child=:child");
        $stmt->execute(array(":child" => $this->_id));
        if (isset($parent))
            foreach ($parent as $value) {
                $stmt = $this->_db->prepare("insert into spice_product_assoc (   `parent`, `child`) values (:parent,:child) ");
                $stmt->execute(array(":parent" => $value, ":child" => $this->_id));
            }
        return $this->edit_product_save();
    }

    public function edit_product_save() {
        $stmt = $this->_db->prepare("update spice_product set name=:name, max_req_m=:max_req_m,max_req_s=:max_req_s,category=:category,type=:type,color=:color where id=:id");
        return $stmt->execute(array(":id" => $this->_id, ":name" => $this->_name, ":max_req_m" => $this->_max_req_m, ":max_req_s" => $this->_max_req_s, ":category" => $this->_category, ":type" => json_encode($this->_type), ":color" => json_encode($this->_color)));
    }

    public function get_info() {
        $children = array();
        $parent = array();
        $parent_ids = array();
        //get children  
        $stmt = $this->_db->prepare("select a.id,a.name,a.category from spice_product  a inner join spice_product_assoc b on b.child=a.id where b.parent=:parent");
        $stmt->execute(array(":parent" => $this->_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $children[] = $row;
        };
        //get parent
        $stmt = $this->_db->prepare("select a.id,a.name,a.category from spice_product  a inner join spice_product_assoc b on b.parent=a.id where b.child=:child");
        $stmt->execute(array(":child" => $this->_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $parent[] = $row;
            $parent_ids[] = $row["id"];
        };

        return array("id" => $this->_id, "name" => $this->_name, "max_req_m" => $this->_max_req_m, "children" => $children, "parent" => $parent, "parent_ids" => $parent_ids, "max_req_s" => $this->_max_req_s, "category" => $this->_category, "type" => json_decode($this->_type), "color" => json_decode($this->_color));
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
        };
        return $js;
    }

}
