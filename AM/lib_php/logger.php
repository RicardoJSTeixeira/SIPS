<?php

/**
 * Esta class serve para inserir no log geral do spice, 
 * também tem a possibilidade de retribuir um evento ou o total de uma secção.
 * As secções são constantes desta class
 *
 * @author ricardo
 */
class logger {

    private $_db;
    const APMKT="Apoio Mkt";
    const MAIL="Correio";
    const FROTA="Frota";
    const STOCK="Stock";
    const MOVSTOCK="Mov. Stock";
    const PROD="Produto";
    const ENC="Encomenda";

    public function __construct(PDO $db) {
        $this->_db = $db;
    }

    public function set($username, $id, $type, $note, $section) {
        $query = "INSERT INTO `spice_log` (`username`, `record_id`, `type`, `note`, `section`) VALUES (:username, :id, :type, :note, :section);";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":username" => $username, ":id" => $id, ":type" => $type, ":note" => $note, ":section" => $section));
    }

    public function get($id, $section) {
        $query = "SELECT `event_date`, `username`, `record_id`, `type`, `note`, `section` FROM `spice_log` WHERE record_id=:id and section=:section;";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id, ":section" => $section));
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function getAll($section) {
        $query = "SELECT `event_date`, `username`, `record_id`, `type`, `note`, `section` FROM `spice_log` WHERE section=:section limit 20000;";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":section" => $section));
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

}
