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
        $stmt = $this->_db->prepare("SELECT `id`,`is_from`,`alert`,`entry_date`,`section`,`record_id`,`cancel` from spice_alerts where `is_for`=:user and status=0");
        $stmt->execute(array(":user" => $this->username));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function setReaded($id) {
        $stmt = $this->_db->prepare("UPDATE spice_alerts set status=1, read_date=NOW() where `id`=:id");
        return $stmt->execute(array(":id" => $id));
    }

    function setAllReaded() {
        $stmt = $this->_db->prepare("UPDATE spice_alerts set status=1, read_date=NOW() where `is_for`=:user and cancel=1");
        return $stmt->execute(array(":user" => $this->username));
    }

    function make($is_for, $alert, $section, $record_id, $cancel) {
        $this->update($section, $record_id);
        $stmt = $this->_db->prepare("INSERT INTO spice_alerts (`is_from`, `is_for`, `entry_date`, `status`, `alert`,`section`,`record_id`,`cancel`) VALUES (:user, :is_for, NOW(), '0', :alert, :section, :record_id, :cancel)");
        return $stmt->execute(array(":user" => $this->username, ":is_for" => $is_for, ":alert" => $alert, ":section" => $section, ":record_id" => $record_id, ":cancel" => $cancel));
    }

    function update($section, $record_id) {
        $stmt = $this->_db->prepare("update spice_alerts set status=1 where record_id=:record_id and section=:section");
        return $stmt->execute(array(":record_id" => $record_id, ":section" => $section));
    }

}
