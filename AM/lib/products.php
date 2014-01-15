<?php

Class products {

    public function __construct($db) {
        $this->_db = $db;
    }

    public function get_products_to_datatable() {
        $stmt = $this->_db->prepare("SELECT id,name,parent,alone,max_req_m,max_req_w,category,type from spice_product");
        $stmt->execute();
        $output['aaData'] = $stmt->fetchAll(PDO::FETCH_BOTH);
        return $output;
    }

    public function get_products_to_datatable_by_id($parent) {
        $stmt = $this->_db->prepare("SELECT id,name,alone,max_req_m,max_req_w,category,type from spice_product where parent=:parent");
        $stmt->execute(array(":parent" => $parent));
        $output['aaData'] = $stmt->fetchAll(PDO::FETCH_BOTH);
        return $output;
    }

    public function get_products() {
        $stmt = $this->_db->prepare("SELECT id,name,parent,alone,max_req_m,max_req_w,category,type from spice_product");
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

    public function add_product($name, $parent, $alone, $max_req_m, $max_req_w, $category, $type) {
        $stmt = $this->_db->prepare("insert into spice_product ( `name`, `parent`, `alone`, `max_req_m`, `max_req_w`, `category`,`type`) values (:name,:parent,:alone,:max_req_m,:max_req_w,:category,:type) ");
        return $stmt->execute(array(":name" => $name, ":parent" => $parent, ":alone" => $alone, ":max_req_m" => $max_req_m, ":max_req_w" => $max_req_w, ":category" => $category, ":type" => $type));
    }

}

class product extends products {

    protected $_db;
    protected $_id;
    protected $_name;
    protected $_parent;
    protected $_alone;
    protected $_max_req_m;
    protected $_max_req_w;
    protected $_category;
    protected $_type;

    public function __construct(PDO $db, $id) {
        $this->_db = $db;


        $stmt = $this->_db->prepare("SELECT id,name,parent,alone,max_req_m,max_req_w,category,type from spice_product where id=:id");
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->_id = $row["id"];
        $this->_name = $row["name"];
        $this->_parent = $row["parent"];
        $this->_alone = $row["alone"];
        $this->_max_req_m = $row["max_req_m"];
        $this->_max_req_w = $row["max_req_w"];
        $this->_category = $row["category"];
        $this->_type = $row["type"];
    }

    public function edit_product($name, $parent, $alone, $max_req_m, $max_req_w, $category, $type) {
        $this->_name = $name;
        $this->_parent = $parent;
        $this->_alone = $alone;
        $this->_max_req_m = $max_req_m;
        $this->_max_req_w = $max_req_w;
        $this->_category = $category;
        $this->_type = $type;
        return $this->edit_product_save();
    }

    public function edit_product_save() {
        $stmt = $this->_db->prepare("update spice_product set name=:name,parent=:parent,alone=:alone,max_req_m=:max_req_m,max_req_w=:max_req_w,category=:category,type=:type where id=:id");
        return $stmt->execute(array(":id" => $this->_id, ":name" => $this->_name, ":parent" => $this->_parent, ":alone" => $this->_alone, ":max_req_m" => $this->_max_req_m, ":max_req_w" => $this->_max_req_w, ":category" => $this->_category, ":type" => $this->_type));
    }

    public function get_info() {

        return array("id" => $this->_id, "name" => $this->_name, "parent" => $this->_parent, "alone" => $this->_alone, "max_req_m" => $this->_max_req_m, "max_req_w" => $this->_max_req_w, "category" => $this->_category, "type" => $this->_type);
    }

}
