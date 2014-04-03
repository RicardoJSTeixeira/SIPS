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

                
    public function create($data_inicial, $data_final, $horario, $localidade, $local, $morada, $comments, $local_publicidade, $id_reservation) {
        $query = "INSERT INTO `spice_apoio_marketing`(`user`,`data_criacao`, `data_inicial`,`data_final`,`horario`, `localidade`, `local`, `morada`, `comments`, `local_publicidade`,`status`,`id_reservation`) "
                . "VALUES (:user,:now,:data_inicial,:data_final,:horario,:localidade,:local,:morada,:comments,:local_pub,:status,:id_reservation)";
                
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(
            ":user"=>$this->user_id,
            ":now"=>date("Y-m-d H:i:s"),
            ":data_inicial"=>$data_inicial,
            ":data_final"=> $data_final,
            ":horario"=> json_encode($horario),
            ":localidade"=>$localidade, 
            ":local"=>$local, 
            ":morada"=>$morada, 
            ":comments"=>$comments, 
            ":local_pub"=>json_encode($local_publicidade), 
            ":status"=>0,
            ":id_reservation"=>json_encode($id_reservation)));
    }

    public function edit() {
        
    }

    public function delete($id) {
        $query = "delete from spice_apoio_marketing where id=:id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id" => $id));
    }

//EXTRA FUNCTIONS______________________________________________________________________________________________________________________________________________

    public function get_to_datatable($show_admin) {
        $result['aaData'] = [];
        $query = "SELECT id,user,data_criacao,data_inicial,data_final,horario,localidade,local,morada,comments,'local_publicididade',status from spice_apoio_marketing";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            $row[5] = "<div> <button class='btn ver_horario'  data-apoio_marketing_id='" . $row[0] . "'><i class='icon-eye-open'></i>Horario</button></div>";
            $row[10] = "<div> <button class='btn ver_local_publicidade'  data-apoio_marketing_id='" . $row[0] . "' ><i class='icon-eye-open'></i>localidades</button></div>";

            switch ($row[11]) {
                case "0":
                    $row[11] = "Pedido enviado";
                    break;
                case "1":
                    $row[11] = "Aprovado";
                    break;
                case "2":
                    $row[11] = "Rejeitado";
                    break;
            }
            if ($this->user_level > 5 || $show_admin == 1) {
                $row[12] = $row[12] . " <button class='btn accept_apoio_marketing btn-success icon-alone' value='" . $row["id"] . "'><i class= 'icon-ok'></i></button><button class='btn decline_apoio_marketing btn-warning icon-alone' value='" . $row["id"] . "'><i class= 'icon-remove'></i></button> </div>";
            }
            $result['aaData'][] = $row;
        }

        return $result;
    }

    public function get_horario($id) {
        $horarios = array();
        $query = "SELECT horario from spice_apoio_marketing where id=?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $value = json_decode($row["horario"]);
            $horarios[] = array("inicio1" => $value->inicio1, "inicio2" => $value->inicio2, "fim1" => $value->fim1, "fim2" => $value->fim2);
        }

        return $horarios;
    }

    public function get_locais_publicidade($id) {
        $locais = array();
        $query = "SELECT local_publicidade from spice_apoio_marketing where id=?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            foreach (json_decode($row["local_publicidade"]) as $value) {
                $locais[] = array("cp" => $value->cp, "freguesia" => $value->freguesia);
            }
        }

        return $locais;
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
        $query = "INSERT INTO `spice_report_correio`(`user`, `carta_porte`, `data_envio`, `documento`, `lead_id`,  `anexo`, `comments`) VALUES (:user,:carta_porte,:data_envio,:documento,:lead_id,:anexo,:comments)";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":user" => $this->user_id, ":carta_porte" => $carta_porte, ":data_envio" => $data, ":documento" => $doc, ":lead_id" => $lead_id, ":anexo" => json_encode($input_doc_obj_assoc), ":comments" => $comments));
    }

    public function edit() {
        
    }

    public function delete() {
        
    }

    //EXTRA FUNCTIONS______________________________________________________________________________________________________________________________________________
    public function get_to_datatable($show_admin) {
        $result['aaData'] = [];
        $query = "SELECT id,user,carta_porte,data_envio,documento,lead_id,anexo,comments,status from spice_report_correio";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            if ($row[6])
                $row[6] = "<button data-anexo_id='$row[0]' class='btn ver_anexo_correio'>Anexos</button>";
            else
                $row[6] = "Sem anexo";
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
            if ($this->user_level > 5 || $show_admin == 1) {
                $row[9] = $row[9] . " <button class='btn accept_report_correio btn-success icon-alone' value='" . $row["id"] . "'><i class= 'icon-ok'></i></button><button class='btn decline_report_correio btn-warning icon-alone' value='" . $row["id"] . "'><i class= 'icon-remove'></i></button></div>";
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

    public function get_anexo_correio($id) {
        $query = "select anexo from spice_report_correio where id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


            foreach (json_decode($row["anexo"]) as $value) {
                $anexos[] = $value;
            }
        }

        return $anexos;
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
    public function get_to_datatable($show_admin) {
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

            if ($this->user_level > 5 || $show_admin == 1) {
                $row[9] = $row[9] . " <button class='btn accept_report_frota btn-success icon-alone' value='" . $row["id"] . "'><i class= 'icon-ok'></i></button><button class='btn decline_report_frota btn-warning icon-alone' value='" . $row["id"] . "'><i class= 'icon-remove'></i></button></div>";
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

    public function get_ocorrencias($id) {
        $ocorrencia = array();
        $query = "SELECT ocorrencia from spice_report_frota where id=?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {


            foreach (json_decode($row["ocorrencia"]) as $value) {
                $ocorrencia[] = array("data" => $value->data, "ocorrencia" => $value->ocorrencia, "km" => $value->km);
            }
        }

        return $ocorrencia;
    }

//EXTRA FUNCTIONS______________________________________________________________________________________________________________________________________________
}
