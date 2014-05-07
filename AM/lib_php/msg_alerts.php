<?php


class messages {

    private $_db;
    public $username;

    function __construct($db, $username) {
        $this->username = $username;
        $this->_db = $db;
    }

    function getAll() {
        $stmt = $this->_db->prepare("SELECT `id_msg`,`from`,`msg`,`event_date` from sips_msg where `to`=:user and delivered=0");
        $stmt->execute(array(":user" => $this->username));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function setReaded($id) {
        $stmt = $this->_db->prepare("UPDATE sips_msg set delivered=1 where `id_msg`=:id");
        return $stmt->execute(array(":id" => $id));
    }

    function setAllReaded() {
        $stmt = $this->_db->prepare("UPDATE sips_msg set delivered=1 where `to`=:user");
        return $stmt->execute(array(":user" => $this->username));
    }

}

class alerts {

    private $_db;
    public $username;

    function __construct($db, $username) {
        $this->username = $username;
        $this->_db = $db;
    }

    function getAll() {
        $stmt = $this->_db->prepare("SELECT `id`,`is_from`,`alert`,`entry_date` from spice_alerts where `is_for`=:user and status=0");
        $stmt->execute(array(":user" => $this->username));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function setReaded($id) {
        $stmt = $this->_db->prepare("UPDATE spice_alerts set status=1, read_date=NOW() where `id`=:id");
        return $stmt->execute(array(":id" => $id));
    }

    function setAllReaded() {
        $stmt = $this->_db->prepare("UPDATE spice_alerts set status=1, read_date=NOW() where `is_for`=:user");
        return $stmt->execute(array(":user" => $this->username));
    }

    function make($is_for, $alert) {
        $stmt = $this->_db->prepare("INSERT INTO spice_alerts (`is_from`, `is_for`, `entry_date`, `status`, `alert`) VALUES (:user, :is_for, NOW(), '0', :alert)");
        return $stmt->execute(array(":user" => $this->username, ":is_for" => $is_for, ":alert" => $alert));
    }

}

