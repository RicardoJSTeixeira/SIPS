<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/AM/lib_php/db.php";

require "$root/AM/lib_php/user.php";
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

/** @var PDO $db */
$user = new UserLogin($db);
$user->confirm_login();


switch ($action) {

    case "populate_consults"://ALL MARCAÇOES
        $u = $user->getUser();
        $output['aaData'] = array();
        $query = "SELECT extra2 'codCliente', a.extra_id as 'itLogID', a.lead_id , id_reservation , a.entry_date, f.user, consulta_razao, alias_code as 'salespersonCode', f.produtos, f.venda, MAX(IF(g.name='AR',g.value,''))'AR',MAX(IF(g.name='AL',g.value,'')) 'AL',MAX(IF(g.name='BCR',g.value,'')) 'BCR',MAX(IF(g.name='BCL',g.value,'')) 'BCL',MAX(IF(g.name='ULLR',g.value,'')) 'ULLR',MAX(IF(g.name='ULLL',g.value,'')) 'ULLL'
                FROM sips_sd_reservations a
                INNER JOIN sips_sd_resources b ON a.id_resource = b.id_resource
                INNER JOIN vicidial_list d ON a.lead_id = d.lead_id
                INNER JOIN spice_consulta f ON a.id_reservation=f.reserva_id
                INNER JOIN spice_audiograma g ON a.id_reservation=g.uniqueid
                WHERE f.closed=1 AND f.exame=1 group by g.uniqueid limit 20000";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $extractor = function ($a) {
            return $a->value;
        };
        $defaultProdutos = array(
            "direito" => array(
                "gama" => "",
                "marca" => "",
                "modelo" => ""
            ),
            "esquerdo" => array(
                "gama" => "",
                "marca" => "",
                "modelo" => ""
            ),
            "tipo" => ""
        );
        function audioCalc($ar500, $al500, $ar1000, $al1000, $ar2000, $al2000, $ar4000, $al4000)
        {
            $right_ear = (object)array("value" => 0, "text" => "Sem Perda");
            $left_ear = (object)array("value" => 0, "text" => "Sem Perda");

            $right_ear->value = (($ar500 * 4) + ($ar1000 * 3) + ($ar2000 * 2) + ($ar4000 * 1)) / 10;
            $left_ear->value = (($al500 * 4) + ($al1000 * 3) + ($al2000 * 2) + ($al4000 * 1)) / 10;

            if ($right_ear->value < 35 && $left_ear->value < 35) {
                $result = "Sem Perda";
            } else {
                $result = "Perda";
                if ($right_ear->value >= 35 && $right_ear->value < 65) {
                    $right_ear->text = "Perda";
                } else if ($right_ear->value >= 65) {
                    $right_ear->text = "Perda Power";
                }
                if ($left_ear->value >= 35 && $left_ear->value < 65) {
                    $left_ear->text = "Perda";
                } else if ($left_ear->value >= 65) {
                    $left_ear->text = "Perda Power";
                }
            }
            return (object)array("right" => $right_ear, "left" => $left_ear, "result" => $result);
        }

        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $ar = array_map($extractor, json_decode($row->AR));
            $al = array_map($extractor, json_decode($row->AL));
            $bcr = array_map($extractor, json_decode($row->BCR));
            $bcl = array_map($extractor, json_decode($row->BCL));
            $ullr = array_map($extractor, json_decode($row->ULLR));
            $ulll = array_map($extractor, json_decode($row->ULLL));
            if ((bool)$row->venda) {
                $produtos = json_decode($row->produtos, true);
                $produtos = (is_array($produtos)) ? array_replace_recursive($defaultProdutos, $produtos) : $defaultProdutos;
            } else {
                $produtos = $defaultProdutos;
            }

            $audioResult = audioCalc($ar[1], $al[1], $ar[2], $al[2], $ar[3], $al[3], $ar[5], $al[5]);
            $output['aaData'][] = array_merge(array(
                    $row->codCliente,
                    $row->itLogID,
                    $row->lead_id,
                    $row->id_reservation,
                    $row->entry_date,
                    $row->user),
                $ar,
                $al,
                $bcr,
                $bcl,
                $ullr,
                $ulll,
                array(
                    $audioResult->right->text,
                    $audioResult->left->text,
                    $audioResult->right->value,
                    $audioResult->left->value,
                    $produtos['direito']['marca'],
                    $produtos['esquerdo']['marca'],
                    $produtos['direito']['gama'],
                    $produtos['esquerdo']['gama'],
                    $produtos['direito']['modelo'],
                    $produtos['esquerdo']['modelo'],
                    $audioResult->result,
                    $produtos['tipo']
                ));
        }
        echo json_encode($output);
        break;

    default :
        break;
}

