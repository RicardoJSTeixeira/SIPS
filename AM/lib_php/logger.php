<?php

/**
 * Esta class serve para inserir no log geral do spice, 
 * também tem a possibilidade de retribuir um evento ou o total de uma secção.
 * As secções são constantes desta class
 *
 * @author ricardo
 */
class Logger {

    private $_db;
    private $_username;

    const S_APMKT = "Apoio Mkt";
    const S_CAL = "Calendário";
    const S_ENC = "Encomenda";
    const S_FROTA = "Frota";
    const S_MAIL = "Correio";
    const S_MOVSTOCK = "Mov. Stock";
    const S_PROD = "Produto";
    const S_PROM = "Promoção";
    const S_STOCK = "Stock";
    const S_USER = "User";
    const T_INS = "Insert";
    const T_UPD = "Update";
    const T_DEL = "Delete";
    const T_RM = "Remove";

    public function __construct(PDO $db, $user) {
        $this->_db = $db;
        $this->_username = $user->username;
    }

    public function set($id, $type, $section, $note = "") {
        $query = "INSERT INTO `spice_log` (`username`, `record_id`, `type`, `note`, `section`) VALUES (:username, :id, :type, :note, :section);";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":username" => $this->_username, ":id" => $id, ":type" => $type, ":note" => $note, ":section" => $section));
    }

    public function get($id, $section) {
        $query = "SELECT `event_date`, `username`, `record_id`, `type`, `note`, `section` FROM `spice_log` WHERE record_id=:id and section=:section;";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id, ":section" => $section));
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function get_all_logs() {
       
        $query = "SELECT `id`, `event_date`, `username`, `record_id`, `type`, `note`, `section` FROM `spice_log";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();
         return  $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getAll($section) {
        $query = "SELECT `event_date`, `username`, `record_id`, `type`, `note`, `section` FROM `spice_log` WHERE section=:section limit 20000;";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":section" => $section));
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

}
