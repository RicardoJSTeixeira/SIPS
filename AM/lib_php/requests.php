<?php

abstract class requests_class {

    protected $_db;
    public $user_level = 0;
    public $user_id = "no_user";
    public $user;

    public function __construct($db, $user_level, $user_id, $user) {
        $this->user_level = $user_level;
        $this->user_id = $user_id;
        $this->user = $user;
        $this->_db = $db;
    }

}

class apoio_marketing extends requests_class {

    public function __construct($db, $user_level, $user_id, $user) {
        parent::__construct($db, $user_level, $user_id, $user);
    }

    public function create($data_inicial, $data_final, $horario, $localidade, $local, $morada, $comments, $local_publicidade) {
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

    public function get_to_datatable() {
        $result['aaData'] = array();
        $filter = ($this->user_level == 6 ) ? ' where user in ("' . implode('","', $this->user->siblings) . '") ' : (($this->user_level < 6 ) ? ' where user like "' . $this->user_id . '" ' : '');
        $query = "SELECT id, user, data_criacao, data_inicial, data_final, 'horario', localidade, local, morada, comments, local_publicidade, cod, total_rastreios, rastreios_perda, vendas, valor, status, closed from spice_apoio_marketing $filter limit 20000";
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
            $row[19] = implode(",", $row);
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

    public function get_horario($id) {
        $horarios = array();
        $query = "SELECT horario from spice_apoio_marketing where id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        if ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $value = json_decode($row->horario);
            $horarios = array("tipo" => $value->tipo, "inicio1" => $value->inicio1, "inicio2" => $value->inicio2, "fim1" => $value->fim1, "fim2" => $value->fim2);
        }
        return $horarios;
    }

    public function get_locais_publicidade($id) {
        $locais = array();
        $query = "SELECT local_publicidade from spice_apoio_marketing where id=?";
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

    public function get_reservations($id) {
        $query = "Select id_reservation From spice_apoio_marketing where id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $reservation_id = $stmt->fetch(PDO::FETCH_OBJ);
        return json_decode($reservation_id->id_reservation);
    }

    public function accept($id) {
        $query = "Update spice_apoio_marketing set status=1 where id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $this->getUser($id);
    }

    public function decline($id) {
        $query = "Update spice_apoio_marketing set status=2 where id=?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        return $this->getUser($id);
    }

    private function getUser($id) {
        $query = "Select user FROM spice_apoio_marketing where id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function setReservation($id, $ref) {
        $query = "UPDATE spice_apoio_marketing set id_reservation=:id_reservation where id=:id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id_reservation" => json_encode($ref), ":id" => $id));
    }

    public function get_one($id) {
        $query = "SELECT `user`, `data_criacao`, `data_inicial`, `data_final`, `horario`, `localidade`, `local`, `morada`, `comments`, `local_publicidade`, `cod`, `total_rastreios`, `rastreios_perda`, `vendas`, `valor`, `closed` FROM `spice_apoio_marketing` WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $ap = $stmt->fetch(PDO::FETCH_OBJ);
        $ap->horario = json_decode($ap->horario);
        $ap->local_publicidade = json_decode($ap->local_publicidade);
        return $ap;
    }

    public function set_report($id, $cod, $total_rastreios, $rastreios_perda, $vendas, $valor) {
        $query = "UPDATE spice_apoio_marketing SET cod=:cod, total_rastreios=:total_rastreios, rastreios_perda=:rastreios_perda, vendas=:vendas, valor=:valor, closed=1 WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":cod" => $cod, ":total_rastreios" => $total_rastreios, ":rastreios_perda" => $rastreios_perda, ":vendas" => $vendas, ":valor" => $valor, ":id" => $id));
    }

}

class correio extends requests_class {

    public function __construct($db, $user_level, $user_id, $user) {
        parent::__construct($db, $user_level, $user_id, $user);
    }

    public function create($carta_porte, $data, $input_doc_obj_assoc, $comments) {
        $query = "INSERT INTO `spice_report_correio`(`user`, `carta_porte`, `data_envio`,  `anexo`, `comments`) VALUES (:user,:carta_porte,:data_envio,:anexo,:comments)";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":user" => $this->user_id, ":carta_porte" => $carta_porte, ":data_envio" => $data, ":anexo" => json_encode($input_doc_obj_assoc), ":comments" => $comments));
    }

    public function get_to_datatable() {
        $result['aaData'] = array();
        $filter = ($this->user_level == 6 ) ? ' where user in ("' . implode('","', $this->user->siblings) . '") ' : (($this->user_level < 6 ) ? ' where user like "' . $this->user_id . '" ' : '');
        $query = "SELECT id,user,carta_porte,data_envio,anexo,comments,status from spice_report_correio $filter limit 20000";
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

    public function accept($id) {
        $query = "Update spice_report_correio set status = 1 where id = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $this->getUser($id);
    }

    public function decline($id) {
        $query = "Update spice_report_correio set status = 2 where id = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $this->getUser($id);
    }

    public function get_anexo_correio($id) {
        $query = "select anexo from spice_report_correio where id = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $_anexos = json_decode($row["anexo"]);
            foreach ($_anexos as $value) {
                $anexos[] = $value;
            }
        }
        return $anexos;
    }

    public function save_anexo_correio($id, $anexos) {
        $query = "update spice_report_correio set anexo = :anexo where id = :id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id" => $id, ":anexo" => json_encode($anexos)));
    }

    private function getUser($id) {
        $query = "Select user FROM spice_report_correio where id = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function get_one($id) {
        $query = "SELECT id,user,carta_porte,data_envio,anexo,comments,status from spice_report_correio WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $ap = $stmt->fetch(PDO::FETCH_OBJ);
        return $ap;
    }

}

class frota extends requests_class {

    public function __construct($db, $user_level, $user_id, $user) {
        parent::__construct($db, $user_level, $user_id, $user);
    }

    public function create($data, $matricula, $km, $viatura, $ocorrencias, $comments) {
        $query = "INSERT INTO `spice_report_frota` (`user`, `data`, `matricula`, `km`, `viatura`, `comments`, `ocorrencia`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array($this->user_id, $data, $matricula, $km, $viatura, $comments, json_encode($ocorrencias)));
    }

    public function get_to_datatable() {
        $result['aaData'] = array();
        $filter = ($this->user_level == 6 ) ? ' where user in ("' . implode('","', $this->user->siblings) . '") ' : (($this->user_level < 6 ) ? ' where user like "' . $this->user_id . '" ' : '');
        $query = "SELECT id, user, data, matricula, km, viatura, comments, ocorrencia, status from spice_report_frota $filter limit 20000";
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

    public function accept($id) {
        $query = "Update spice_report_frota set status = 1 where id = ?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        return $this->getUser($id);
    }

    public function decline($id) {
        $query = "Update spice_report_frota set status = 2 where id = ?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        return $this->getUser($id);
    }

    public function get($id) {
        $ocorrencia = array();
        $query = "SELECT ocorrencia from spice_report_frota where id = ?";
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

    private function getUser($id) {
        $query = "Select user FROM spice_report_frota where id = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function get_one($id) {
        $query = "SELECT id, user, data, matricula, km, viatura, comments, ocorrencia, status from spice_report_frota WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $ap = $stmt->fetch(PDO::FETCH_OBJ);
        return $ap;
    }

}

class mensal_stock extends requests_class {

    public function __construct($db, $user_level, $user_id, $user) {
        parent::__construct($db, $user_level, $user_id, $user);
    }

    public function create($data, $produtos) {
        $query = "INSERT INTO `spice_report_stock` (`user`, `data`, `produtos`) VALUES (?, ?, ?)";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array($this->user_id, $data, json_encode($produtos)));
    }

    public function get_to_datatable() {
        $result['aaData'] = array();
        $filter = ($this->user_level == 6 ) ? ' where user in ("' . implode('","', $this->user->siblings) . '") ' : (($this->user_level < 6 ) ? ' where user like "' . $this->user_id . '" ' : '');
        $query = "SELECT id, user, data, produtos, status from spice_report_stock $filter limit 20000";
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

    public function accept($id) {
        $query = "Update spice_report_stock set status = 1 where id = ?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        return $this->getUser($id);
    }

    public function decline($id) {
        $query = "Update spice_report_stock set status = 2 where id = ?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        return $this->getUser($id);
    }

    public function get($id) {
        $produtos = array();
        $query = "SELECT produtos from spice_report_stock where id = ?";
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

    public function save_stock($id, $produtos) {
        $query = "update spice_report_stock set produtos = :produtos where id = :id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id" => $id, ":produtos" => json_encode($produtos)));
    }

    private function getUser($id) {
        $query = "Select user FROM spice_report_stock where id = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function get_one($id) {
        $query = "SELECT id, user, data, produtos, status from spice_report_stock WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $ap = $stmt->fetch(PDO::FETCH_OBJ);
        return $ap;
    }

}

class movimentacao_stock extends requests_class {

    public function __construct($db, $user_level, $user_id, $user) {
        parent::__construct($db, $user_level, $user_id, $user);
    }

    public function create($data, $produtos) {
        $query = "INSERT INTO `spice_report_movimentacao` (`user`, `data`, `produtos`) VALUES (?, ?, ?)";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array($this->user_id, $data, json_encode($produtos)));
    }

    public function get_to_datatable() {
        $result['aaData'] = array();
        $filter = ($this->user_level == 6 ) ? ' where user in ("' . implode('","', $this->user->siblings) . '") ' : (($this->user_level < 6 ) ? ' where user like "' . $this->user_id . '" ' : '');
        $query = "SELECT id, user, entry_date, produtos, status from spice_report_movimentacao $filter limit 20000";
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

    public function accept($id) {
        $query = "Update spice_report_movimentacao set status = 1 where id = ?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        return $this->getUser($id);
    }

    public function decline($id) {
        $query = "Update spice_report_movimentacao set status = 2 where id = ?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        return $this->getUser($id);
    }

    public function get($id) {
        $produtos = array();
        $query = "SELECT produtos from spice_report_movimentacao where id = ?";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array($id));
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $_produtos = json_decode($row->produtos);
            foreach ($_produtos as $value) {
                $produtos[] = array("destinatario" => $value->destinatario, "quantidade" => $value->quantidade, "destinatario" => $value->destinatario, "descricao" => $value->descricao, "serie" => $value->serie, "obs" => $value->obs, "confirmed" => $value->confirmed, "admin" => $value->admin);
            }
        }

        return $produtos;
    }

    private function getUser($id) {
        $query = "Select user FROM spice_report_movimentacao where id = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function save_mov_stock($id, $produtos) {
        $query = "update spice_report_movimentacao set produtos = :produtos where id = :id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id" => $id, ":produtos" => json_encode($produtos)));
    }

    public function get_one($id) {
        $query = "SELECT id, user, entry_date, produtos, status from spice_report_movimentacao WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $ap = $stmt->fetch(PDO::FETCH_OBJ);
        return $ap;
    }

}
