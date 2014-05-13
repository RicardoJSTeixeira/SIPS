<?php

abstract class requests_class {

    protected $_db;
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
                    ":user" => $this->user_id,
                    ":now" => date("Y-m-d H:i:s"), //GRAVAR NESTE FORMATO, so A ler é q se muda para d-m-Y
                    ":data_inicial" => $data_inicial,
                    ":data_final" => $data_final,
                    ":horario" => json_encode($horario),
                    ":localidade" => $localidade,
                    ":local" => $local,
                    ":morada" => $morada,
                    ":comments" => $comments,
                    ":local_pub" => json_encode($local_publicidade),
                    ":status" => 1,
                    ":id_reservation" => json_encode($id_reservation)));
    }

//EXTRA FUNCTIONS______________________________________________________________________________________________________________________________________________

    public function get_to_datatable() {
        $result['aaData'] = array();
        $filter = ($this->user_level < 5 ) ? ' where user like "' . $this->user_id . '" ' : '';
        $query = "SELECT id,user,data_criacao,data_inicial,data_final,horario,localidade,local,morada,comments,'local_publicididade',status from spice_apoio_marketing $filter";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {

            $row[2] = date("d-m-Y H:i:s", strtotime($row[2]));
            $row[3] = date("d-m-Y H:i:s", strtotime($row[3]));
            $row[4] = date("d-m-Y H:i:s", strtotime($row[4]));

            $row[5] = "<div> <button class='btn ver_horario' data-apoio_marketing_id='" . $row[0] . "'><i class='icon-eye-open'></i>Horario</button></div>";
            $row[10] = "<div> <button class='btn ver_local_publicidade' data-apoio_marketing_id='" . $row[0] . "' ><i class='icon-eye-open'></i>localidades</button></div>";
            switch ($row[11]) {
                case "0":
                    $row[12] = "<div class='btn-group'> <button class='btn accept_apoio_marketing btn-success icon-alone' value='" . $row[0] . "'><i class= 'icon-ok'></i></button><button class='btn decline_apoio_marketing btn-warning icon-alone' value='" . $row[0] . "'><i class= 'icon-remove'></i></button> </div>";
                    $row[11] = "Pedido enviado";
                    break;
                case "1":
                    $row[12] = "<div class='btn-group'> <button class='btn accept_apoio_marketing btn-success icon-alone' disabled value='" . $row[0] . "'><i class= 'icon-ok'></i></button><button class='btn decline_apoio_marketing btn-warning icon-alone' value='" . $row[0] . "'><i class= 'icon-remove'></i></button> </div>";
                    $row[11] = "<span class='label label-success'>Aprovado</span>";
                    break;
                case "2":
                    $row[12] = "<div class='btn-group'> <button class='btn accept_apoio_marketing btn-success icon-alone' disabled value='" . $row[0] . "'><i class= 'icon-ok'></i></button><button class='btn decline_apoio_marketing btn-warning icon-alone' disabled value='" . $row[0] . "'><i class= 'icon-remove'></i></button> </div>";
                    $row[11] = "<span class='label label-important'>Rejeitado</span>";
                    break;
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
            $horarios[] = array("tipo" => $value->tipo, "inicio1" => $value->inicio1, "inicio2" => $value->inicio2, "fim1" => $value->fim1, "fim2" => $value->fim2);
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

    public function get_reservation($id) {
        $query = "Select id_reservation From spice_apoio_marketing where id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $reservation_id = $stmt->fetch(PDO::FETCH_OBJ);
        return $reservation_id->id_reservation;
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

}

class correio extends requests_class {

    public function __construct($db, $user_level, $user_id) {
        parent::__construct($db, $user_level, $user_id);
    }

    public function create($carta_porte, $data, $doc, $lead_id, $input_doc_obj_assoc, $comments) {
        $query = "INSERT INTO `spice_report_correio`(`user`, `carta_porte`, `data_envio`, `documento`, `lead_id`, `anexo`, `comments`) VALUES (:user,:carta_porte,:data_envio,:documento,:lead_id,:anexo,:comments)";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":user" => $this->user_id, ":carta_porte" => $carta_porte, ":data_envio" => $data, ":documento" => $doc, ":lead_id" => $lead_id, ":anexo" => json_encode($input_doc_obj_assoc), ":comments" => $comments));
    }

//EXTRA FUNCTIONS______________________________________________________________________________________________________________________________________________
    public function get_to_datatable() {
        $result['aaData'] = array();
        $filter = ($this->user_level < 5 ) ? ' where user like "' . $this->user_id . '" ' : '';
        $query = "SELECT id,user,carta_porte,data_envio,documento,lead_id,anexo,comments,status from spice_report_correio $filter";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $approved = $row[8] == "1" ? 1 : 0;

            $row[3] = date("d-m-Y", strtotime($row[3]));
            if ($row[6])
                $row[6] = "<button data-anexo_id = '$row[0]' data-approved = '$approved' class = 'btn ver_anexo_correio'><i class = 'icon-eye-open'></i>Anexos</button>";
            else
                $row[6] = "Sem anexo";
            switch ($row[8]) {
                case "0":
                    $row[9] = "<div class = 'btn-group'><button class = 'btn accept_report_correio btn-success icon-alone' value = '" . $row[0] . "'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_correio btn-warning icon-alone' value = '" . $row[0] . "'><i class = 'icon-remove'></i></button></div>";
                    $row[8] = "Pedido enviado";
                    break;
                case "1":
                    $row[9] = "<div class = 'btn-group'><button class = 'btn accept_report_correio btn-success icon-alone' disabled value = '" . $row[0] . "'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_correio btn-warning icon-alone' value = '" . $row[0] . "'><i class = 'icon-remove'></i></button></div>";
                    $row[8] = "<span class = 'label label-success'>Aprovado</span>";
                    break;
                case "2":
                    $row[9] = "<div class = 'btn-group'><button class = 'btn accept_report_correio btn-success icon-alone' value = '" . $row[0] . "'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_correio btn-warning icon-alone' disabled value = '" . $row[0] . "'><i class = 'icon-remove'></i></button></div>";
                    $row[8] = "<span class = 'label label-important'>Pendente</span>";
                    break;
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

}

class frota extends requests_class {

    public function __construct($db, $user_level, $user_id) {
        parent::__construct($db, $user_level, $user_id);
    }

    public function create($data, $matricula, $km, $viatura, $ocorrencias, $comments) {
        $query = "INSERT INTO `spice_report_frota` (`user`, `data`, `matricula`, `km`, `viatura`, `comments`, `ocorrencia`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array($this->user_id, $data, $matricula, $km, $viatura, $comments, json_encode($ocorrencias)));
    }

    //EXTRA FUNCTIONS______________________________________________________________________________________________________________________________________________
    public function get_to_datatable() {
        $result['aaData'] = array();
        $filter = ($this->user_level < 5 ) ? ' where user like "' . $this->user_id . '" ' : '';
        $query = "SELECT id, user, data, matricula, km, viatura, comments, ocorrencia, status from spice_report_frota $filter";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {

            $row[2] = date("d-m-Y H:i:s", strtotime($row[2]));
            switch ($row[8]) {
                case "0":
                    $row[9] = "<div class = 'btn-group'><button class = 'btn accept_report_frota btn-success icon-alone' value = '" . $row[0] . "'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_frota btn-warning icon-alone' value = '" . $row[0] . "'><i class = 'icon-remove'></i></button></div>";
                    $row[8] = "Pedido enviado";
                    break;
                case "1":
                    $row[9] = "<div class = 'btn-group'><button class = 'btn accept_report_frota btn-success icon-alone' disabled value = '" . $row[0] . "'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_frota btn-warning icon-alone' value = '" . $row[0] . "'><i class = 'icon-remove'></i></button></div>";
                    $row[8] = "<span class = 'label label-success'>Aprovado</span>";
                    break;
                case "2":
                    $row[9] = "<div class = 'btn-group'><button class = 'btn accept_report_frota btn-success icon-alone' value = '" . $row[0] . "'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_frota btn-warning icon-alone' disabled value = '" . $row[0] . "'><i class = 'icon-remove'></i></button></div>";
                    $row[8] = "<span class = 'label label-important'>Pendente</span>";
                    break;
            }

            $row[7] = "<div> <button class = 'btn ver_ocorrencias' data-relatorio_frota_id = '" . $row[0] . "'><i class = 'icon-eye-open'></i>Ver Ocorrências</button></div>";


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
                $ocorrencia[] = array("data" => $row[3] = date("d-m-Y H:i:s", strtotime($value->data)), "ocorrencia" => $value->ocorrencia, "km" => $value->km);
            }
        }

        return $ocorrencia;
    }

    private function getUser($id) {
        $query = "Select user FROM spice_report_correio where id = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

//EXTRA FUNCTIONS______________________________________________________________________________________________________________________________________________
}

class mensal_stock extends requests_class {

    public function __construct($db, $user_level, $user_id) {
        parent::__construct($db, $user_level, $user_id);
    }

    public function create($data, $produtos) {
        $query = "INSERT INTO `spice_report_stock` (`user`, `data`, `produtos`) VALUES (?, ?, ?)";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array($this->user_id, $data, json_encode($produtos)));
    }

    //EXTRA FUNCTIONS______________________________________________________________________________________________________________________________________________
    public function get_to_datatable() {
        $result['aaData'] = array();
        $filter = ($this->user_level < 5 ) ? ' where user like "' . $this->user_id . '" ' : '';
        $query = "SELECT id, user, data, produtos, status from spice_report_stock $filter";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $approved = $row[4] == "1" ? 1 : 0;
            $row[2] = date("d-m-Y H:i:s", strtotime($row[2]));
            switch ($row[4]) {
                case "0":
                    $row[5] = "<div class = 'btn-group'><button class = 'btn accept_report_stock btn-success icon-alone' value = '" . $row[0] . "'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_stock btn-warning icon-alone' value = '" . $row[0] . "'><i class = 'icon-remove'></i></button></div>";
                    $row[4] = "Pedido enviado";
                    break;
                case "1":
                    $row[5] = "<div class = 'btn-group'><button class = 'btn accept_report_stock btn-success icon-alone' disabled value = '" . $row[0] . "'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_stock btn-warning icon-alone' value = '" . $row[0] . "'><i class = 'icon-remove'></i></button></div>";
                    $row[4] = "<span class = 'label label-success'>Aprovado</span>";
                    break;
                case "2":
                    $row[5] = "<div class = 'btn-group'><button class = 'btn accept_report_stock btn-success icon-alone' value = '" . $row[0] . "'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_stock btn-warning icon-alone' disabled value = '" . $row[0] . "'><i class = 'icon-remove'></i></button></div>";
                    $row[4] = "<span class = 'label label-important'>Pendente</span>";
                    break;
            }

            $row[3] = "<div> <button class = 'btn  ver_produto_stock' data-approved = '$approved' data-stock_id = '" . $row[0] . "'><i class = 'icon-eye-open'></i>Itens</button></div>";


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
                $produtos[] = array("quantidade" => $value->quantidade, "descricao" => $value->descricao, "serie" => $value->serie, "obs" => $value->obs, "confirmed" => $value->confirmed);
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

//EXTRA FUNCTIONS______________________________________________________________________________________________________________________________________________
}

class movimentacao_stock extends requests_class {

    public function __construct($db, $user_level, $user_id) {
        parent::__construct($db, $user_level, $user_id);
    }

    public function create($data, $produtos) {
        $query = "INSERT INTO `spice_report_movimentacao` (`user`, `data`, `produtos`) VALUES (?, ?, ?)";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array($this->user_id, $data, json_encode($produtos)));
    }

    //EXTRA FUNCTIONS______________________________________________________________________________________________________________________________________________
    public function get_to_datatable() {
        $result['aaData'] = array();
        $filter = ($this->user_level < 5 ) ? ' where user like "' . $this->user_id . '" ' : '';
        $query = "SELECT id, user, data, produtos, status from spice_report_movimentacao $filter";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $approved = $row[4] == "1" ? 1 : 0;
            $row[2] = date("d-m-Y H:i:s", strtotime($row[2]));
            switch ($row[4]) {
                case "0":
                    $row[5] = "<div class = 'btn-group'><button class = 'btn accept_report_movimentacao btn-success icon-alone' value = '" . $row[0] . "'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_movimentacao btn-warning icon-alone' value = '" . $row[0] . "'><i class = 'icon-remove'></i></button></div>";
                    $row[4] = "Pedido enviado";
                    break;
                case "1":
                    $row[5] = "<div class = 'btn-group'><button class = 'btn accept_report_movimentacao btn-success icon-alone' disabled value = '" . $row[0] . "'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_movimentacao btn-warning icon-alone' value = '" . $row[0] . "'><i class = 'icon-remove'></i></button></div>";
                    $row[4] = "<span class = 'label label-success'>Aprovado</span>";
                    break;
                case "2":
                    $row[5] = "<div class = 'btn-group'><button class = 'btn accept_report_movimentacao btn-success icon-alone' value = '" . $row[0] . "'><i class = 'icon-ok'></i></button><button class = 'btn decline_report_movimentacao btn-warning icon-alone' disabled value = '" . $row[0] . "'><i class = 'icon-remove'></i></button></div>";
                    $row[4] = "<span class = 'label label-important'>Pendente</span>";
                    break;
            }

            $row[3] = "<div> <button class = 'btn  ver_produto_mov_stock' data-approved = '$approved' data-movimentacao_id = '" . $row[0] . "'><i class = 'icon-eye-open'></i>Itens</button></div>";


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
                $produtos[] = array("quantidade" => $value->quantidade, "destinario" => $value->destinario, "descricao" => $value->descricao, "serie" => $value->serie, "obs" => $value->obs, "confirmed" => $value->confirmed);
            }
        }

        return $produtos;
    }

    private function getUser($id) {
        $query = "Select user FROM spice_report_stock where id = :id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function save_mov_stock($id, $produtos) {
        $query = "update spice_report_movimentacao set produtos = :produtos where id = :id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id" => $id, ":produtos" => json_encode($produtos)));
    }

//EXTRA FUNCTIONS______________________________________________________________________________________________________________________________________________
}
