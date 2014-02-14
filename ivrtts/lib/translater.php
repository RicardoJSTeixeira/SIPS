<?php

class dictionary {

    private $db;

    function __construct($db) {
        $this->db = $db;
    }

    function get() {
        $stmt = $this->db->prepare("SELECT id,original, translation FROM zero.dictionary");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    function getFormated() {
        $formated = array();
        $all = $this->get();
        foreach ($all as $value) {
            $formated[$value->original] = $value->translation;
        }
        return $formated;
    }

    function set($origin, $trans) {
        try {
            $stmt = $this->db->prepare("INSERT INTO zero.dictionary ( original, translation) values (:original,:translate)");
            $stmt->execute(array(":original" => $origin, ":translate" => $trans));
            return $this->db->lastInsertId;
        } catch (PDOException $Exception) {
            throw new Exception($Exception->getMessage(), (int) $Exception->getCode());
        }
    }

    function del($id) {
        $stmt = $this->db->prepare("DELETE FROM zero.dictionary WHERE id=:id");
        return $stmt->execute(array(":id" => $id));
    }

}
