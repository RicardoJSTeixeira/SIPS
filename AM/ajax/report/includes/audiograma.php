<?php

$curTime = date("Y-m-d_H:i:s");
$filename = "audiograma_" . $curTime;
header("Content-Disposition: attachment; filename=" . $filename . ".csv");
$output = fopen('php://output', 'w');

$u = $user->getUser();

$query = "SELECT extra2 'codCliente', a.extra_id as 'itLogID', a.lead_id , id_reservation , a.entry_date, f.user, consulta_razao, alias_code as 'salespersonCode', f.produtos, f.venda, MAX(IF(g.name='AR',g.value,''))'AR',MAX(IF(g.name='AL',g.value,'')) 'AL',MAX(IF(g.name='BCR',g.value,'')) 'BCR',MAX(IF(g.name='BCL',g.value,'')) 'BCL',MAX(IF(g.name='ULLR',g.value,'')) 'ULLR',MAX(IF(g.name='ULLL',g.value,'')) 'ULLL'
                FROM sips_sd_reservations a
                INNER JOIN sips_sd_resources b ON a.id_resource = b.id_resource
                INNER JOIN vicidial_list d ON a.lead_id = d.lead_id
                INNER JOIN spice_consulta f ON a.id_reservation=f.reserva_id
                INNER JOIN spice_audiograma g ON a.id_reservation=g.uniqueid
                WHERE f.closed=1 and a.start_date BETWEEN :data_inicial AND :data_final group by g.uniqueid limit 20000";

$stmt = $db->prepare($query);
$stmt->execute(array(":data_inicial" => "$data_inicial 00:00:00", ":data_final" => "$data_final 23:59:59"));
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

fputcsv($output, array(
    "Contact No.",
    "Interaction Log Entry No.",
    "Spice Contact No.",
    "ID Consulta SPICE",
    "Date Entered",
    "Assigned UserId",
    "Air Right 250",
    "Air Right 500",
    "Air Right 1000",
    "Air Right 1500",
    "Air Right 2000",
    "Air Right 3000",
    "Air Right 4000",
    "Air Right 6000",
    "Air Right 8000",
    "Air Right Mask",
    "Air Left 250",
    "Air Left 500",
    "Air Left 1000",
    "Air Left 1500",
    "Air Left 2000",
    "Air Left 3000",
    "Air Left 4000",
    "Air Left 6000",
    "Air Left 8000",
    "Air Left Mask",
    "Bone Cond Right 500",
    "Bone Cond Right 1000",
    "Bone Cond Right 1500",
    "Bone Cond Right 2000",
    "Bone Cond Right 3000",
    "Bone Cond Right 4000",
    "Bone Cond Right Mask",
    "Bone Cond Left 500",
    "Bone Cond Left 1000",
    "Bone Cond Left 1500",
    "Bone Cond Left 2000",
    "Bone Cond Left 3000",
    "Bone Cond Left 4000",
    "Bone Cond Left Mask",
    "ULL Right 500",
    "ULL Right 1000",
    "ULL Right 1500",
    "ULL Right 2000",
    "ULL Right 3000",
    "ULL Right 4000",
    "ULL Left 500",
    "ULL Left 1000",
    "ULL Left 1500",
    "ULL Left 2000",
    "ULL Left 3000",
    "ULL Left 4000",
    "Tipo Perda Dto.",
    "Tipo Perda Esq.",
    "Pct. Perda Dto.",
    "Pct. Perda Esq.",
    "Marca Dto.",
    "Marca Esq.",
    "Gama Dto.",
    "Gama Esq.",
    "Modelo Dto.",
    "Modelo Esq.",
    "Resultado",
    "Tipo de Venda"), ";");

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
    fputcsv($output, array_merge(
        array(
        $row->codCliente,
        $row->itLogID,
        $row->id_reservation,
        $row->lead_id,
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
        )), ";");
}

fclose($output);
