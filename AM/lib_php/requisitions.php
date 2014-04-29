<?php

Class requisitions {

    public function __construct($db, $user_level, $user_id) {
        $this->_user_level = $user_level;
        $this->_user_id = $user_id;
        $this->_db = $db;
    }

    public function get_requisitions_to_datatable($show_admin) {

        $result['aaData'] = [];
        $query = "SELECT sr.id,sr.user,sr.type,sr.lead_id,sr.date,sr.contract_number,vl.extra2,sr.attachment,'products',sr.status  from spice_requisition sr left join vicidial_list vl on vl.lead_id=sr.lead_id where sr.user=:user";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":user" => $this->_user_id));

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            if ($row[2] == "mensal") {
                $row[2] = "Mensal";
                $row[6] = "<i class='icon-ban-circle'></i>";
            } else {
                $row[2] = "Especial";
                if ($show_admin == 1) {
                    if ($row[6])
                        $row[6] = "$row[6]";
                    else
                        $row[6] = "<input placeholder='Insira código de cliente' class='cod_cliente_input span validate[custom[onlyNumberSp]]' data-clientID='$row[3]' type='text'/>";
                }
                else {
                    if (!$row[6])
                        $row[6] = "<i class='icon-ban-circle'></i>";
                }
            }
            $row[3] = $row[3] == "0" ? "<i class='icon-ban-circle'></i>" : $row[3];
            switch ($row[9]) {
                case "0":
                    $row[9] = "Pedido enviado";
                    break;
                case "1":
                    $row[9] = "Aprovado";
                    break;
                case "2":
                    $row[9] = "Rejeitado";
                    break;
            }

            $row[8] = "<div><button class='btn ver_requisition_products' value='" . $row[0] . "'><i class='icon-eye-open'></i>Ver</button></div>";
            if ($this->_user_level > 5 || $show_admin == 1)
                $row[10] = $row[10] . " <span class='btn-group'><button class='btn accept_requisition icon-alone btn-success' value='" . $row[0] . "'><i class= 'icon-ok'></i></button><button class='btn decline_requisition icon-alone btn-warning' value='" . $row[0] . "'><i class= 'icon-remove'></i></button></div></span>";

            $result['aaData'][] = $row;
        }

        return $result;
    }

    public function edit_requisition($id, $cod_cliente) {
        $query = "Update vicidial_list set extra2=:cod_cliente where lead_id=:id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":cod_cliente" => $cod_cliente, ":id" => $id));
    }

    public function create_requisition($type, $lead_id, $contract_number, $attachment, $products_list) {
        $query = "INSERT INTO `spice_requisition`( `user`, `type`, `lead_id`, `date`, `contract_number`, `attachment`, `products`,`status`) VALUES ( :user,:type,:lead_id,:date,:contract_number,:attachment,:products,:status)";
        $stmt = $this->_db->prepare($query);
        $data = date('Y-m-d H:i:s');
        $stmt->execute(array(":user" => $this->_user_id, ":type" => $type, ":lead_id" => $lead_id, ":date" => $data, ":contract_number" =>  $contract_number, ":attachment" => $attachment, ":products" => json_encode($products_list), ":status" => 0));
        $last_insert_id = $this->_db->lastInsertId();
        return array($last_insert_id, $this->_user_id, $type, $lead_id, $data, $contract_number, $attachment, "<div> <button class='btn ver_requisition_products' value='" . $last_insert_id . "'><i class='icon-eye-open'></i>Ver</button></div>", "Pedido enviado");
    }

    public function get_products_by_requisiton($id) {
        $query = "SELECT products from spice_requisition where id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array("id" => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $productalia = [];
        foreach (json_decode($row["products"]) as $value) {
            $productalia[$value->id] = array("quantity" => $value->quantity, "color" => $value->color, "color_name" => $value->color_name);
        }
        $query = "SELECT id,name,category from spice_product where id in ('" . join("','", array_keys($productalia)) . "') order by category asc";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();
        while ($row1 = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $productalia[$row1["id"]] = ["name" => $row1["name"], "quantity" => $productalia[$row1["id"]]["quantity"], "color" => $productalia[$row1["id"]]["color"], "color_name" => $productalia[$row1["id"]]["color_name"], "category" => $row1["category"]];
        }
        return $productalia;
    }

    public function accept_requisition($id) {
        $query = "Update  spice_requisition set status=1 where id=:id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array("id" => $id));
    }

    public function decline_requisition($id) {
        $query = "Update  spice_requisition set status=2 where id=:id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array("id" => $id));
    }

    public function check_month_requisitions() {
        $query = "select count(id) count from  spice_requisition  where user=:user and date between :date_first and :date_last and type='month' and status != '2' ";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":user" => $this->_user_id, ":date_first" => date("Y-m-01") . " 00:00:00", ":date_last" => date("Y-m-t") . " 23:59:59"));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row["count"];
    }

}
