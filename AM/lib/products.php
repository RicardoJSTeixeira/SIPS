<?php

Class products {

    public function __construct($db) {
        $this->_db = $db;
    }

    public function get_products() {
        $stmt = $this->_db->prepare("SELECT * from spice_product");
        $stmt->execute();
        $output['aaData'] = $stmt->fetchAll(PDO::FETCH_BOTH);
        return $output;
    }

    public function remove_product() {
        $stmt = $this->_db->prepare("SELECT * from spice_product");
        $stmt->execute();
    }

    public function edit_product() {
        
    }

    public function add_product() {
        
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

    public function __construct(PDO $db, $id, $type) {
        $this->_db = $db;

        $stmt = $this->_db->prepare("SELECT * from aaa where id=:id");
        $stmt->execute(array(":id" => $id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            
        }
    }

    public function get_info($id) {
        return "";
    }

}
