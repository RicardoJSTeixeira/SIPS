<?php

Class products {

    public function __construct($db) {
        $this->_db = $db;
    }

    public function get_products_to_datatable($product_editable) {
        $output['aaData'] = [];
        $stmt = $this->_db->prepare("SELECT id,name,max_req_m,max_req_s,category,type,color from spice_product");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {

            if ($product_editable == true)
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
        $output['aaData'] = $stmt->fetchAll(PDO::FETCH_BOTH);
        return $output;
    }

    public function get_products() {
        $stmt = $this->_db->prepare("SELECT id,name, max_req_m,max_req_s,category,type,color from spice_product");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function remove_product($id) {
        $stmt = $this->_db->prepare("delete from spice_product where id=:id");
        return $stmt->execute(array(":id" => $id));
    }

    public function remove_products() {
        $stmt = $this->_db->prepare("delete from spice_product");
        return $stmt->execute();
    }

    public function add_product($name, $max_req_m, $max_req_s, $parent, $category, $type, $color) {
        $stmt = $this->_db->prepare("insert into spice_product ( `name`,   `max_req_m`, `max_req_s`, `category`,`type`,`color`) values (:name, :max_req_m,:max_req_s,:category,:type,:color) ");
        $stmt->execute(array(":name" => $name, ":max_req_m" => $max_req_m, ":max_req_s" => $max_req_s, ":category" => $category, ":type" => json_encode($type), ":color" => $color));
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
    protected $_category;
    protected $_type;

    public function __construct(PDO $db, $id) {
        $this->_db = $db;
        $stmt = $this->_db->prepare("SELECT id,name ,max_req_m,max_req_s,category,type from spice_product where id=:id");
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->_id = $row["id"];
        $this->_name = $row["name"];


        $this->_max_req_m = $row["max_req_m"];
        $this->_max_req_s = $row["max_req_s"];
        $this->_category = $row["category"];
        $this->_type = $row["type"];
    }

    public function edit_product($name, $max_req_m, $max_req_s, $category, $type) {
        $this->_name = $name;
        $this->_max_req_m = $max_req_m;
        $this->_max_req_s = $max_req_s;
        $this->_category = $category;
        $this->_type = $type;
        return $this->edit_product_save();
    }

    public function edit_product_save() {
        $stmt = $this->_db->prepare("update spice_product set name=:name, max_req_m=:max_req_m,max_req_s=:max_req_s,category=:category,type=:type where id=:id");
        return $stmt->execute(array(":id" => $this->_id, ":name" => $this->_name, ":max_req_m" => $this->_max_req_m, ":max_req_s" => $this->_max_req_s, ":category" => $this->_category, ":type" => $this->_type));
    }

    public function get_info() {
        $children = array();
        $parent = array();
        //get children  
        $stmt = $this->_db->prepare("select * from spice_product_assoc where parent=:parent");
        $stmt->execute(array(":parent" => $this->_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $children[] = $row["child"];
        };
        //get parent
        $stmt = $this->_db->prepare("select * from spice_product_assoc where child=:child");
        $stmt->execute(array(":child" => $this->_id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $parent[] = $row["parent"];
        };
        return array("id" => $this->_id, "name" => $this->_name, "max_req_m" => $this->_max_req_m, "children" => $children, "parent" => $parent, "max_req_s" => $this->_max_req_s, "category" => $this->_category, "type" => $this->_type);
    }

}
