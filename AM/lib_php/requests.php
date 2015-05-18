<?php

abstract class requests_class
{

    protected $_db;
    public $user_level = 0;
    public $user_id = "no_user";
    public $user;

    public function __construct(PDO $db, $user_level, $user_id, $user)
    {
        $this->user_level = $user_level;
        $this->user_id = $user_id;
        $this->user = $user;
        $this->_db = $db;
    }

}

class apoio_marketing extends requests_class
{

    public function __construct($db, $user_level, $user_id, $user)
    {
        parent::__construct($db, $user_level, $user_id, $user);
    }

    public function create($data_inicial, $data_final, $horario, $localidade, $local, $morada, $comments, $local_publicidade)
    {
        $query = "INSERT INTO `spice_apoio_marketing`(`user`,`data_criacao`, `data_inicial`,`data_final`,`horario`, `localidade`, `local`, `morada`, `comments`, `local_publicidade`,`status`) "
            . "VALUES (:user,:now,:data_inicial,:data_final,:horario,:localidade,:local,:morada,:comments,:local_pub,:status)";

        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(
            ":user" => $this->user_id,
            ":now" => date("Y-m-d H:i:s"),
            ":data_inicial" => $data_inicial,
            ":data_final" => $data_final,
            ":horario" => json_encode($horario),
            ":localidade" => $localidade,
            ":local" => $local,
            ":morada" => $morada,
            ":comments" => $comments,
            ":local_pub" => json_encode($local_publicidade),
            ":status" => 1));
        return $this->_db->lastInsertId();
    }

    public function get_to_datatable($show_aproved)
    {
        $approved_toggle = "";
        /*if ($show_aproved!="true")
            $approved_toggle = " and status<>1";*/

        $result['aaData'] = array();
        $filter = ($this->user_level == 6) ? ' and user in ("' . implode('","', $this->user->siblings) . '") ' : (($this->user_level < 6) ? ' and user like "' . $this->user_id . '" ' : '');
        $query = "SELECT id, user, data_criacao, data_inicial, data_final, 'horario', localidade, local, morada, comments, local_publicidade, cod, total_rastreios, rastreios_perda, vendas, valor, status, closed
                  from spice_apoio_marketing
                  where 1 $filter $approved_toggle limit 20000";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            //sorting de colunas
            if ($this->user_level > 5) {
                //Admin
                $row[18] = ($row[16] == 0 ? 0 : ($row[16] == 1 ? 2 : 1));
            } else {
                //User
                $row[18] = ($row[16] == 0 ? 1 : ($row[16] == 1 ? 2 : 0));
            }
            $row[9] = preg_replace('/(\v|\s)+/', ' ', $row[9]);
            $row[9] = str_replace(',', ';', $row[9]);
            $row[5] = "<div> <button class='btn icon-alone ver_horario' data-apoio_marketing_id='$row[0]'><i class='icon-time'></i></button></div>";
            $row[10] = "<div> <button class='btn icon-alone ver_local_publicidade' data-apoio_marketing_id='$row[0]' ><i class='icon-home'></i></button></div>";
            if ($row[17] == '1') {
                $row[17] = "<div class='btn-group'> <button class='btn accept_apoio_marketing btn-success icon-alone' disabled value='$row[0]'><i class= 'icon-ok'></i></button><button class='btn decline_apoio_marketing btn-warning icon-alone' disabled value='$row[0]'><i class= 'icon-remove'></i></button> </div>";
                $row[16] = "<span class='label label-success'>Fechado</span>";
            } else {
                switch ($row[16]) {
                    case "0":
                        $row[17] = "<div class='btn-group'> <button class='btn accept_apoio_marketing btn-success icon-alone' value='$row[0]'><i class= 'icon-ok'></i></button><button class='btn decline_apoio_marketing btn-warning icon-alone' value='$row[0]'><i class= 'icon-remove'></i></button> </div>";
                        $row[16] = "Pedido enviado";
                        break;
                    case "1":
                        $row[17] = "<div class='btn-group'> <button class='btn accept_apoio_marketing btn-success icon-alone' disabled value='$row[0]'><i class= 'icon-ok'></i></button><button class='btn decline_apoio_marketing btn-warning icon-alone' value='$row[0]'><i class= 'icon-remove'></i></button> </div>";
                        $row[16] = "<span class='label label-success'>Aprovado</span>";
                        break;
                    case "2":
                        $row[17] = "<div class='btn-group'> <button class='btn accept_apoio_marketing btn-success icon-alone' disabled value='$row[0]'><i class= 'icon-ok'></i></button><button class='btn decline_apoio_marketing btn-warning icon-alone' disabled value='$row[0]'><i class= 'icon-remove'></i></button> </div>";
                        $row[16] = "<span class='label label-important'>Rejeitado</span>";
                        break;
                }
            }

            $result['aaData'][] = $row;
        }
        return $result;
    }

    public function get_horario($id)
    {
        $horarios = array();
        $query = "SELECT horario FROM spice_apoio_marketing WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        if ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $value = json_decode($row->horario);
            $horarios = array("tipo" => $value->tipo, "inicio1" => $value->inicio1, "inicio2" => $value->inicio2, "fim1" => $value->fim1, "fim2" => $value->fim2);
        }
        return $horarios;
    }

    public function get_locais_publicidade($id)
    {
        $locais = array();
        $query = "SELECT local_publicidade FROM spice_apoio_marketing WHERE id=?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $_locais = json_decode($row["local_publicidade"]);
            foreach ($_locais as $value) {
                $locais[] = array("cp" => $value->cp, "freguesia" => $value->freguesia);
            }
        }
        return $locais;
    }

    public function get_reservations($id)
    {
        $query = "SELECT id_reservation FROM spice_apoio_marketing WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $reservation_id = $stmt->fetch(PDO::FETCH_OBJ);
        return json_decode($reservation_id->id_reservation);
    }

    public function accept($id)
    {
        $query = "UPDATE spice_apoio_marketing SET status=1 WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $this->getUser($id);
    }

    public function decline($id)
    {
        $query = "UPDATE spice_apoio_marketing SET status=2 WHERE id=?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        return $this->getUser($id);
    }

    private function getUser($id)
    {
        $query = "SELECT user FROM spice_apoio_marketing WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function setReservation($id, $ref)
    {
        $query = "UPDATE spice_apoio_marketing SET id_reservation=:id_reservation WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id_reservation" => json_encode($ref), ":id" => $id));
    }

    public function get_one($id)
    {
        $query = "SELECT `user`, `data_criacao`, `data_inicial`, `data_final`, `horario`, `localidade`, `local`, `morada`, `comments`, `local_publicidade`, `cod`, `total_rastreios`, `rastreios_perda`, `vendas`, `valor`, `closed` FROM `spice_apoio_marketing` WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $ap = $stmt->fetch(PDO::FETCH_OBJ);
        $ap->horario = json_decode($ap->horario);
        $ap->local_publicidade = json_decode($ap->local_publicidade);
        return $ap;
    }

    public function set_report($id, $cod, $total_rastreios, $rastreios_perda, $vendas, $valor)
    {
        $query = "UPDATE spice_apoio_marketing SET cod=:cod, total_rastreios=:total_rastreios, rastreios_perda=:rastreios_perda, vendas=:vendas, valor=:valor, closed=1 WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":cod" => $cod, ":total_rastreios" => $total_rastreios, ":rastreios_perda" => $rastreios_perda, ":vendas" => $vendas, ":valor" => $valor, ":id" => $id));
    }

//CODIGOS DE MARKETING - CREATE,DELETE,EDIT e GET------------------------------------------------------
    public function create_marketing_code($codmkt, $description)
    {
        $duplicates = $this->get_marketing_code($codmkt);
        if (gettype($duplicates) == "object") {
            return false;
        } else {
            $query = "INSERT INTO `spice_codigos_mkt`(`codmkt`, `description`) VALUES (:codmkt,:description)";
            $stmt = $this->_db->prepare($query);

            return $stmt->execute(array(":codmkt" => $codmkt, ":description" => $description));
        }
    }

    public function create_multiple_marketing_code($codes)
    {
        $query = "INSERT INTO `spice_codigos_mkt`(`codmkt`, `description`) VALUES (:codmkt,:description) ON DUPLICATE KEY UPDATE codmkt=:codmkt1, description=:description1";
        $stmt = $this->_db->prepare($query);
        foreach ($codes as $lines) {
            $stmt->execute(array(":codmkt" => $lines[0], ":description" => $lines[1], ":codmkt1" => $lines[0], ":description1" => $lines[1]));
        }
        return json_encode(true);
    }


    public function edit_marketing_code($id_codmkt, $new_codmkt, $description)
    {
        $temp = $this->get_marketing_code($new_codmkt);
        if (gettype($temp) == "object") {
            if ($temp->id != $id_codmkt)
                return "duplicate";
        }

        $query = "UPDATE `spice_codigos_mkt` SET codmkt=:new_codmkt,description=:description, modify_date=:modify_date WHERE id=:id_codmkt";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":new_codmkt" => $new_codmkt, ":description" => $description, ":modify_date" => date('Y-m-d H:i:s'), ":id_codmkt" => $id_codmkt));
    }

    public function get_marketing_code($codmkt)
    {
        $query = "SELECT id, codmkt, description, entry_date, modify_date FROM spice_codigos_mkt WHERE codmkt=:codmkt";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":codmkt" => $codmkt));
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function get_marketing_code_to_datatable()
    {
        $result['aaData'] = array();

        $query = "SELECT id, codmkt, description, entry_date, modify_date FROM spice_codigos_mkt";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();

        $result['aaData'] = $stmt->fetchAll(PDO::FETCH_NUM);

        return $result;
    }

    public function delete_marketing_code($id)
    {
        $stmt = $this->_db->prepare("DELETE FROM spice_codigos_mkt WHERE id=:id");
        return $stmt->execute(array(":id" => $id));
    }
//------------------------------------------------------------------------------------------------------------
}

class correio extends requests_class
{

    public function __construct($db, $user_level, $user_id, $user)
    {
        parent::__construct($db, $user_level, $user_id, $user);
    }

    public function create($carta_porte, $data, $input_doc_obj_assoc, $comments)
    {
        $query = "INSERT INTO `spice_report_correio`(`user`, `carta_porte`, `data_envio`,  `anexo`, `comments`) VALUES (:user,:carta_porte,:data_envio,:anexo,:comments)";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":user" => $this->user_id, ":carta_porte" => $carta_porte, ":data_envio" => $data, ":anexo" => json_encode($input_doc_obj_assoc), ":comments" => $comments));
        return array($this->_db->lastInsertId(), $this->user_id, $carta_porte, $data, json_encode($input_doc_obj_assoc), $comments);
    }

    public function get_to_datatable($show_aproved)
    {
        $approved_toggle = "";
        if ($show_aproved != "true")
            $approved_toggle = " and status<>1";


        $result['aaData'] = array();
        $filter = ($this->user_level == 6) ? ' and user in ("' . implode('","', $this->user->siblings) . '") ' : (($this->user_level < 6) ? ' and user like "' . $this->user_id . '" ' : '');
        $query = "SELECT id,user,carta_porte,data_envio,anexo,comments,status from spice_report_correio where 1  $filter $approved_toggle limit 20000";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            //sorting de colunas
            if ($this->user_level > 5) {
                //Admin 
                $row[8] = ($row[6] == 0 ? 0 : ($row[6] == 1 ? 2 : 1));
            } else {
                //User
                $row[8] = ($row[6] == 0 ? 1 : ($row[6] == 1 ? 2 : 0));
            }

            $row[5] = preg_replace('/(\v|\s)+/', ' ', $row[5]);
            $row[5] = str_replace(',', ';', $row[5]);
            $approved = $row[6] == 1 ? 1 : 0;

            switch ($row[6]) {
                case "0":
                    $row[7] = "<div class = 'btn-group'><button class = 'btn accept_report_correio btn-success icon-alone' value = '$row[0]'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_correio btn-warning icon-alone' value = '$row[0]'><i class = 'icon-remove'></i></button></div>";
                    $row[6] = "Pedido enviado";
                    break;
                case "1":
                    $row[7] = "<div class = 'btn-group'><button class = 'btn accept_report_correio btn-success icon-alone' disabled value = '$row[0]'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_correio btn-warning icon-alone' value = '$row[0]'><i class = 'icon-remove'></i></button></div>";
                    $row[6] = "<span class = 'label label-success'>Aprovado</span>";
                    break;
                case "2":
                    $row[7] = "<div class = 'btn-group'><button class = 'btn accept_report_correio btn-success icon-alone' value = '$row[0]'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_correio btn-warning icon-alone' disabled value = '$row[0]'><i class = 'icon-remove'></i></button></div>";
                    $row[6] = "<span class = 'label label-important'>Pendente</span>";
                    break;
            }
            $row[9] = implode(",", $row);
            if ($row[4]) {
                $row[4] = "<button data-anexo_id = '$row[0]' data-approved = '$approved' class = 'btn ver_anexo_correio icon-alone'><i class = 'icon-folder-close'></i></button>";
            } else {
                $row[4] = "Sem anexo";
            }

            $result['aaData'][] = $row;
        }

        return $result;
    }

    public function accept($id)
    {
        $query = "UPDATE spice_report_correio SET status = 1 WHERE id = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $this->getUser($id);
    }

    public function decline($id)
    {
        $query = "UPDATE spice_report_correio SET status = 2 WHERE id = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $this->getUser($id);
    }

    public function get_anexo_correio($id)
    {
        $query = "SELECT anexo FROM spice_report_correio WHERE id = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $anexos = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $_anexos = json_decode($row["anexo"]);
            foreach ($_anexos as $value) {
                $anexos[] = $value;
            }
        }
        return $anexos;
    }

    public function save_anexo_correio($id, $anexos)
    {
        $query = "UPDATE spice_report_correio SET anexo = :anexo WHERE id = :id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id" => $id, ":anexo" => json_encode($anexos)));
    }

    private function getUser($id)
    {
        $query = "SELECT user FROM spice_report_correio WHERE id = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function get_one($id)
    {
        $query = "SELECT id,user,carta_porte,data_envio,anexo,comments,status FROM spice_report_correio WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $ap = $stmt->fetch(PDO::FETCH_OBJ);
        return $ap;
    }

}

class frota extends requests_class
{

    public function __construct($db, $user_level, $user_id, $user)
    {
        parent::__construct($db, $user_level, $user_id, $user);
    }

    public function create($data, $matricula, $km, $viatura, $ocorrencias, $comments)
    {
        $query = "INSERT INTO `spice_report_frota` (`user`, `data`, `matricula`, `km`, `viatura`, `comments`, `ocorrencia`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($this->user_id, $data, $matricula, $km, $viatura, $comments, json_encode($ocorrencias)));
        return array($this->_db->lastInsertId(), $this->user_id, $data, $matricula, $km, $viatura, $comments, json_encode($ocorrencias));
    }

    public function get_to_datatable($show_aproved)
    {

        $approved_toggle = "";
        if ($show_aproved != "true")
            $approved_toggle = " and status<>1";


        $result['aaData'] = array();
        $filter = ($this->user_level == 6) ? ' and user in ("' . implode('","', $this->user->siblings) . '") ' : (($this->user_level < 6) ? ' and user like "' . $this->user_id . '" ' : '');
        $query = "SELECT id, user, data, matricula, km, viatura, comments, ocorrencia, status from spice_report_frota where 1 $filter $approved_toggle limit 20000";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            if ($this->user_level > 5) {
                //Admin
                $row[10] = ($row[8] == 0 ? 0 : ($row[8] == 1 ? 2 : 1));
            } else {
                //User
                $row[10] = ($row[8] == 0 ? 1 : ($row[8] == 1 ? 2 : 0));
            }
            $row[6] = preg_replace('/(\v|\s)+/', ' ', $row[6]);
            $row[6] = str_replace(',', ';', $row[6]);
            switch ($row[8]) {
                case "0":
                    $row[9] = "<div class = 'btn-group'><button class = 'btn accept_report_frota btn-success icon-alone' value = '$row[0]'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_frota btn-warning icon-alone' value = '$row[0]'><i class = 'icon-remove'></i></button></div>";
                    $row[8] = "Pedido enviado";
                    break;
                case "1":
                    $row[9] = "<div class = 'btn-group'><button class = 'btn accept_report_frota btn-success icon-alone' disabled value = '$row[0]'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_frota btn-warning icon-alone' value = '$row[0]'><i class = 'icon-remove'></i></button></div>";
                    $row[8] = "<span class = 'label label-success'>Aprovado</span>";
                    break;
                case "2":
                    $row[9] = "<div class = 'btn-group'><button class = 'btn accept_report_frota btn-success icon-alone' value = '$row[0]'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_frota btn-warning icon-alone' disabled value = '$row[0]'><i class = 'icon-remove'></i></button></div>";
                    $row[8] = "<span class = 'label label-important'>Pendente</span>";
                    break;
            }
            $row[11] = implode(",", $row);
            $row[7] = "<div> <button class = 'btn ver_ocorrencias icon-alone' data-relatorio_frota_id = '$row[0]'><i class = 'icon-list'></i></button></div>";
            $result['aaData'][] = $row;
        }
        return $result;
    }

    public function accept($id)
    {
        $query = "UPDATE spice_report_frota SET status = 1 WHERE id = ?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        return $this->getUser($id);
    }

    public function decline($id)
    {
        $query = "UPDATE spice_report_frota SET status = 2 WHERE id = ?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        return $this->getUser($id);
    }

    public function get($id)
    {
        $ocorrencia = array();
        $query = "SELECT ocorrencia FROM spice_report_frota WHERE id = ?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $ocorrencias = json_decode($row["ocorrencia"]);
            foreach ($ocorrencias as $value) {
                $ocorrencia[] = array("data" => $row[3] = $value->data, "ocorrencia" => $value->ocorrencia, "km" => $value->km);
            }
        }
        return $ocorrencia;
    }

    private function getUser($id)
    {
        $query = "SELECT user FROM spice_report_frota WHERE id = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function get_one($id)
    {
        $query = "SELECT id, user, data, matricula, km, viatura, comments, ocorrencia, status FROM spice_report_frota WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $ap = $stmt->fetch(PDO::FETCH_OBJ);
        return $ap;
    }

}

class mensal_stock extends requests_class
{

    public function __construct($db, $user_level, $user_id, $user)
    {
        parent::__construct($db, $user_level, $user_id, $user);
    }

    public function create($data, $produtos)
    {
        $query = "INSERT INTO `spice_report_stock` (`user`, `data`, `produtos`) VALUES (?, ?, ?)";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($this->user_id, $data, json_encode($produtos)));
        return array($this->_db->lastInsertId(), $this->user_id, $data, json_encode($produtos));
    }

    public function get_to_datatable($show_aproved)
    {
        $approved_toggle = "";
        if ($show_aproved != "true")
            $approved_toggle = " and status<>1";

        $result['aaData'] = array();
        $filter = ($this->user_level == 6) ? ' and user in ("' . implode('","', $this->user->siblings) . '") ' : (($this->user_level < 6) ? ' and user like "' . $this->user_id . '" ' : '');
        $query = "SELECT id, user, data, produtos, status from spice_report_stock where 1 $filter $approved_toggle limit 20000";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            //sorting de colunas
            if ($this->user_level > 5) {
                //Admin
                $row[6] = ($row[4] == 0 ? 0 : ($row[4] == 1 ? 2 : 1));
            } else {
                //User
                $row[6] = ($row[4] == 0 ? 1 : ($row[4] == 1 ? 2 : 0));
            }
            $approved = $row[4] == "1" ? 1 : 0;
            switch ($row[4]) {
                case "0":
                    $row[5] = "<div class = 'btn-group'><button class = 'btn accept_report_stock btn-success icon-alone' value = '$row[0]'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_stock btn-warning icon-alone' value = '$row[0]'><i class = 'icon-remove'></i></button></div>";
                    $row[4] = "Pedido enviado";
                    break;
                case "1":
                    $row[5] = "<div class = 'btn-group'><button class = 'btn accept_report_stock btn-success icon-alone' disabled value = '$row[0]'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_stock btn-warning icon-alone' value = '$row[0]'><i class = 'icon-remove'></i></button></div>";
                    $row[4] = "<span class = 'label label-success'>Aprovado</span>";
                    break;
                case "2":
                    $row[5] = "<div class = 'btn-group'><button class = 'btn accept_report_stock btn-success icon-alone' value = '$row[0]'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_stock btn-warning icon-alone' disabled value = '$row[0]'><i class = 'icon-remove'></i></button></div>";
                    $row[4] = "<span class = 'label label-important'>Pendente</span>";
                    break;
            }
            $row[7] = implode(",", $row);
            $row[3] = "<div> <button class = 'btn  ver_produto_stock icon-alone' data-approved = '$approved' data-stock_id = '$row[0]'><i class = 'icon-eye-open'></i></button></div>";
            $result['aaData'][] = $row;
        }
        return $result;
    }

    public function accept($id)
    {
        $query = "UPDATE spice_report_stock SET status = 1 WHERE id = ?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        return $this->getUser($id);
    }

    public function decline($id)
    {
        $query = "UPDATE spice_report_stock SET status = 2 WHERE id = ?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        return $this->getUser($id);
    }

    public function get($id)
    {
        $produtos = array();
        $query = "SELECT produtos FROM spice_report_stock WHERE id = ?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $_produtos = json_decode($row->produtos);
            foreach ($_produtos as $value) {
                $produtos[] = array("quantidade" => $value->quantidade, "descricao" => $value->descricao, "serie" => $value->serie, "obs" => $value->obs, "confirmed" => $value->confirmed, "admin" => $value->admin);
            }
        }

        return $produtos;
    }

    public function save_stock($id, $produtos)
    {
        $query = "UPDATE spice_report_stock SET produtos = :produtos WHERE id = :id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id" => $id, ":produtos" => json_encode($produtos)));
    }

    private function getUser($id)
    {
        $query = "SELECT user FROM spice_report_stock WHERE id = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function get_one($id)
    {
        $query = "SELECT id, user, data, produtos, status FROM spice_report_stock WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $ap = $stmt->fetch(PDO::FETCH_OBJ);
        return $ap;
    }

}

class movimentacao_stock extends requests_class
{

    public function __construct($db, $user_level, $user_id, $user)
    {
        parent::__construct($db, $user_level, $user_id, $user);
    }

    public function create($data, $produtos)
    {
        $query = "INSERT INTO `spice_report_movimentacao` (`user`, `data`, `produtos`) VALUES (?, ?, ?)";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($this->user_id, $data, json_encode($produtos)));
        return array($this->_db->lastInsertId(), $this->user_id, $data, json_encode($produtos));
    }

    public function get_to_datatable($show_aproved)
    {

        $approved_toggle = "";
        if ($show_aproved!="true")
            $approved_toggle = " and status=1";

        $result['aaData'] = array();
        $filter = ($this->user_level == 6) ? ' and user in ("' . implode('","', $this->user->siblings) . '") ' : (($this->user_level < 6) ? ' and user like "' . $this->user_id . '" ' : '');
        $query = "SELECT id, user, entry_date, produtos, status from spice_report_movimentacao where 1 $filter $approved_toggle limit 20000";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            //sorting de colunas
            if ($this->user_level > 5) {
                //Admin
                $row[6] = ($row[4] == 0 ? 0 : ($row[4] == 1 ? 2 : 1));
            } else {
                //User
                $row[6] = ($row[4] == 0 ? 1 : ($row[4] == 1 ? 2 : 0));
            }
            $approved = $row[4] == "1" ? 1 : 0;
            switch ($row[4]) {
                case "0":
                    $row[5] = "<div class = 'btn-group'><button class = 'btn accept_report_movimentacao btn-success icon-alone' value = '$row[0]'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_movimentacao btn-warning icon-alone' value = '$row[0]'><i class = 'icon-remove'></i></button></div>";
                    $row[4] = "Pedido enviado";
                    break;
                case "1":
                    $row[5] = "<div class = 'btn-group'><button class = 'btn accept_report_movimentacao btn-success icon-alone' disabled value = '$row[0]'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_movimentacao btn-warning icon-alone' value = '$row[0]'><i class = 'icon-remove'></i></button></div>";
                    $row[4] = "<span class = 'label label-success'>Aprovado</span>";
                    break;
                case "2":
                    $row[5] = "<div class = 'btn-group'><button class = 'btn accept_report_movimentacao btn-success icon-alone' value = '$row[0]'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_movimentacao btn-warning icon-alone' disabled value = '$row[0]'><i class = 'icon-remove'></i></button></div>";
                    $row[4] = "<span class = 'label label-important'>Pendente</span>";
                    break;
            }
            $row[7] = implode(",", $row);
            $row[3] = "<div> <button class = 'btn  ver_produto_mov_stock icon-alone' data-approved = '$approved' data-movimentacao_id = '$row[0]'><i class = 'icon-eye-open'></i></button></div>";
            $result['aaData'][] = $row;
        }
        return $result;
    }

    public function accept($id)
    {
        $query = "UPDATE spice_report_movimentacao SET status = 1 WHERE id = ?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        return $this->getUser($id);
    }

    public function decline($id)
    {
        $query = "UPDATE spice_report_movimentacao SET status = 2 WHERE id = ?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        return $this->getUser($id);
    }

    public function get($id)
    {
        $produtos = array();
        $query = "SELECT produtos FROM spice_report_movimentacao WHERE id = ?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $_produtos = json_decode($row->produtos);
            foreach ($_produtos as $value) {
                $produtos[] = array("destinatario" => $value->destinatario, "quantidade" => $value->quantidade, "descricao" => $value->descricao, "serie" => $value->serie, "obs" => $value->obs, "confirmed" => $value->confirmed, "admin" => $value->admin);
            }
        }

        return $produtos;
    }

    private function getUser($id)
    {
        $query = "SELECT user FROM spice_report_movimentacao WHERE id = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function save_mov_stock($id, $produtos)
    {
        $query = "UPDATE spice_report_movimentacao SET produtos = :produtos WHERE id = :id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id" => $id, ":produtos" => json_encode($produtos)));
    }

    public function get_one($id)
    {
        $query = "SELECT id, user, entry_date, produtos, status FROM spice_report_movimentacao WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $ap = $stmt->fetch(PDO::FETCH_OBJ);
        return $ap;
    }

}
