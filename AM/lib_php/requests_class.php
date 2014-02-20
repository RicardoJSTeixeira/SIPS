<?php

abstract class requests_class {

    public $user_level = 0;
    public $user_id = "no_user";

    public function __construct($db, $user_level, $user_id) {
        $this->user_level = $user_level;
        $this->user_id = $user_id;
        $this->_db = $db;
    }

}

class apoio_marketing extends requests_class {

    public function __construct($db, $user_level, $user_id) {
        parent::__construct($db, $user_level, $user_id);
    }

    public function create($data, $horario, $localidade, $local, $morada, $comments, $local_publicidade) {
        $query = "INSERT INTO `spice_apoio_marketing`(`user`,`data_criaçao`, `data`,`horario`, `localidade`, `local`, `morada`, `comments`, `local_publicidade`,`status`) VALUES (?,?,?,?,?,?,?,?,?,?)";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array($this->user_id, date("Y-m-d H:i:s"), $data, json_encode($horario), $localidade, $local, $morada, $comments, json_encode($local_publicidade), 0));
    }

    public function edit() {
        
    }

    public function delete() {
        
    }

//EXTRA FUNCTIONS______________________________________________________________________________________________________________________________________________

    public function get_to_datatable() {
        $result['aaData'] = [];
        $query = "SELECT id,user,data_criaçao,data,horario,localidade,local,morada,comments,'local_publicididade',status from spice_apoio_marketing";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            $row[4] = "<div> <button class='btn ver_horario'  data-apoio_marketing_id='" . $row[0] . "'><i class='icon-eye-open'></i>Horario</button></div>";
            $row[9] = "<div> <button class='btn ver_local_publicidade'  data-apoio_marketing_id='" . $row[0] . "' ><i class='icon-eye-open'></i>Ver localidades</button></div>";

            switch ($row[10]) {
                case "0":
                    $row[10] = "Pedido enviado";
                    break;
                case "1":
                    $row[10] = "Aprovado";
                    break;
                case "2":
                    $row[10] = "Rejeitado";
                    break;
            }
            if ($this->user_level > 5) {
                $row[10] = $row[10] . " <button class='btn accept_apoio_marketing btn-success' value='" . $row["id"] . "'><i class= 'icon-ok'></i>Aceitar</button><button class='btn decline_apoio_marketing btn-warning' value='" . $row["id"] . "'><i class= 'icon-remove'></i>Rejeitar</button></div>";
            }
            $result['aaData'][] = $row;
        }

        return $result;
    }

    public function get_horario($id) {

        $query = "SELECT horario from spice_apoio_marketing where id=?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row["horario"];
    }

    public function get_locais_publicidade($id) {

        $query = "SELECT local_publicidade from spice_apoio_marketing where id=?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row["local_publicidade"];
    }

    public function accept_apoio_marketing($id) {
        $query = "Update spice_apoio_marketing set status=1 where id=?";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array($id));
    }

    public function decline_apoio_marketing($id) {
        $query = "Update spice_apoio_marketing set status=2 where id=?";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array($id));
    }

}

class correio extends requests_class {

    public function __construct($db, $user_level, $user_id) {
        parent::__construct($db, $user_level, $user_id);
    }

    public function create($carta_porte, $data, $doc, $lead_id, $input_doc_obj_assoc, $comments) {
        $query = "INSERT INTO `spice_report_correio`(`user`, `carta_porte`, `data_envio`, `documento`, `lead_id`,  `anexo`, `comments`) VALUES (?,?,?,?,?,?,?)";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array($this->user_id, $carta_porte, $data, $doc, $lead_id, $input_doc_obj_assoc, $comments));
    }

    public function edit() {
        
    }

    public function delete() {
        
    }

    //EXTRA FUNCTIONS______________________________________________________________________________________________________________________________________________
    public function get_to_datatable() {
        $result['aaData'] = [];
        $query = "SELECT id,user,carta_porte,data_envio,documento,lead_id,anexo,comments,status from spice_report_correio";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {


            switch ($row[8]) {
                case "0":
                    $row[8] = "Pedido enviado";
                    break;
                case "1":
                    $row[8] = "Aprovado";
                    break;
                case "2":
                    $row[8] = "Rejeitado";
                    break;
            }
            if ($this->user_level > 5) {
                $row[8] = $row[8] . " <button class='btn accept_report_correio btn-success' value='" . $row["id"] . "'><i class= 'icon-ok'></i>Aceitar</button><button class='btn decline_report_correio btn-warning' value='" . $row["id"] . "'><i class= 'icon-remove'></i>Rejeitar</button></div>";
            }
            $result['aaData'][] = $row;
        }

        return $result;
    }

    public function accept_report_correio($id) {
        $query = "Update spice_report_correio set status=1 where id=?";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array($id));
    }

    public function decline_report_correio($id) {
        $query = "Update spice_report_correio set status=2 where id=?";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array($id));
    }

}

class frota extends requests_class {

    public function __construct($db, $user_level, $user_id) {
        parent::__construct($db, $user_level, $user_id);
    }

    public function create($data, $matricula, $km, $viatura, $ocorrencias, $comments) {
        $query = "INSERT INTO `spice_report_frota` (`user`, `data`, `matricula`, `km`, `viatura`, `comments`, `ocorrencia`) VALUES (?,?,?,?,?,?,?)";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array($this->user_id, $data, $matricula, $km, $viatura, $comments, json_encode($ocorrencias)));
    }

    public function edit() {
        
    }

    public function delete() {
        
    }

    //EXTRA FUNCTIONS______________________________________________________________________________________________________________________________________________
    public function get_to_datatable() {
        $result['aaData'] = [];
        $query = "SELECT id,user,data,matricula,km,viatura,comments,ocorrencia,status from spice_report_frota";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {


            switch ($row[8]) {
                case "0":
                    $row[8] = "Pedido enviado";
                    break;
                case "1":
                    $row[8] = "Aprovado";
                    break;
                case "2":
                    $row[8] = "Rejeitado";
                    break;
            }

            $row[7] = "<div> <button class='btn ver_ocorrencias'  data-relatorio_frota_id='" . $row[0] . "'><i class='icon-eye-open'></i>Ver Ocorrências</button></div>";

            if ($this->user_level > 5) {
                $row[8] = $row[8] . " <button class='btn accept_report_frota btn-success' value='" . $row["id"] . "'><i class= 'icon-ok'></i>Aceitar</button><button class='btn decline_report_frota btn-warning' value='" . $row["id"] . "'><i class= 'icon-remove'></i>Rejeitar</button></div>";
            }
            $result['aaData'][] = $row;
        }
        return $result;
    }

    public function accept_report_frota($id) {
        $query = "Update spice_report_frota set status=1 where id=?";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array($id));
    }

    public function decline_report_frota($id) {
        $query = "Update spice_report_frota set status=2 where id=?";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array($id));
    }

//EXTRA FUNCTIONS______________________________________________________________________________________________________________________________________________
}
