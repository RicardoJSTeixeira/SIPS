<?php

Class requests {

    public function __construct($db, $user_level, $user_id) {
        $this->_user_level = $user_level;
        $this->_user_id = $user_id;
        $this->_db = $db;
    }

    public function create_relatorio_frota($data, $matricula, $km, $viatura, $ocorrencias, $comments) {
           $query = "INSERT INTO `spice_report_frota` ( `data`, `matricula`, `km`, `viatura`, `comments`, `ocorrencia) VALUES ( :data,:matricula,:km,:viatura,:comments,:ocorrencia)";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":data" => $data, ":matricula" => $matricula, ":km" => $km, ":viatura" => $viatura, ":comments" => $comments, ":ocorrencia" => json_encode($ocorrencias)));
    }

}
