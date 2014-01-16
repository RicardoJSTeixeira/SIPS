<?php

Class products {

    public function __construct($db) {
        $this->_db = $db;
    }

    public function get_products_to_datatable() {
        $stmt = $this->_db->prepare("SELECT id,name,parent ,max_req_m,max_req_s,category,type from spice_product");
        $stmt->execute();
        $output['aaData'] = $stmt->fetchAll(PDO::FETCH_BOTH);
        return $output;
    }

    public function get_products_to_datatable_by_id($parent) {
        $stmt = $this->_db->prepare("SELECT id,name, max_req_m,max_req_s,category,type from spice_product where parent=:parent");
        $stmt->execute(array(":parent" => $parent));
        $output['aaData'] = $stmt->fetchAll(PDO::FETCH_BOTH);
        return $output;
    }

    public function get_products() {
        $stmt = $this->_db->prepare("SELECT id,name,parent, max_req_m,max_req_s,category,type from spice_product");
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

    public function add_product($name, $parent,  $max_req_m, $max_req_s, $category, $type) {
        $stmt = $this->_db->prepare("insert into spice_product ( `name`, `parent`,   `max_req_m`, `max_req_s`, `category`,`type`) values (:name,:parent, :max_req_m,:max_req_s,:category,:type) ");
        return $stmt->execute(array(":name" => $name, ":parent" => $parent,   ":max_req_m" => $max_req_m, ":max_req_s" => $max_req_s, ":category" => $category, ":type" => $type));
    }

}

class product extends products {

    protected $_db;
    protected $_id;
    protected $_name;
    protected $_parent;
     protected $_max_req_m;
    protected $_max_req_s;
    protected $_category;
    protected $_type;

    public function __construct(PDO $db, $id) {
        $this->_db = $db;


        $stmt = $this->_db->prepare("SELECT id,name,parent ,max_req_m,max_req_s,category,type from spice_product where id=:id");
        $stmt->execute(array(":id" => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->_id = $row["id"];
        $this->_name = $row["name"];
        $this->_parent = $row["parent"];
        
        $this->_max_req_m = $row["max_req_m"];
        $this->_max_req_s = $row["max_req_s"];
        $this->_category = $row["category"];
        $this->_type = $row["type"];
    }

    public function edit_product($name, $parent,  $max_req_m, $max_req_s, $category, $type) {
        $this->_name = $name;
        $this->_parent = $parent;
         
        $this->_max_req_m = $max_req_m;
        $this->_max_req_s = $max_req_s;
        $this->_category = $category;
        $this->_type = $type;
        return $this->edit_product_save();
    }

    public function edit_product_save() {
        $stmt = $this->_db->prepare("update spice_product set name=:name,parent=:parent, max_req_m=:max_req_m,max_req_s=:max_req_s,category=:category,type=:type where id=:id");
        return $stmt->execute(array(":id" => $this->_id, ":name" => $this->_name, ":parent" => $this->_parent,  ":max_req_m" => $this->_max_req_m, ":max_req_s" => $this->_max_req_s, ":category" => $this->_category, ":type" => $this->_type));
    }

    public function get_info() {

        return array("id" => $this->_id, "name" => $this->_name, "parent" => $this->_parent, "max_req_m" => $this->_max_req_m, "max_req_s" => $this->_max_req_s, "category" => $this->_category, "type" => $this->_type);
    }

}
