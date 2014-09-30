<?php

Class requisitions
{

    public function __construct($db, $user_level, $user_id, $siblings)
    {
        $this->_user_level = $user_level;
        $this->_user_id = $user_id;
        $this->_user_siblings = $siblings;
        $this->_db = $db;
    }

    public function get_requisitions_to_datatable()
    {
        $result['aaData'] = array();
        $filter = ($this->_user_level == 6) ? ' where sr.user in ("' . implode('","', $this->_user_siblings) . '")' : (($this->_user_level < 6) ? ' where sr.user like "' . $this->_user_id . '" ' : '');
        $query = "SELECT sr.id,sr.user,sr.type,vl.first_name,sr.date,sr.contract_number,vl.extra2,sr.attachment,'products',sr.status,'botoes','sorting','object',sr.lead_id,vl.middle_initial,vl.last_name,vl.phone_number,vl.address1,vl.city  from spice_requisition sr left join vicidial_list vl on vl.lead_id=sr.lead_id $filter  ";

        $stmt = $this->_db->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            //sorting de colunas
            if ($this->_user_level > 5) {
                //Admin
                $row[11] = ($row[9] == 0 ? 0 : ($row[9] == 1 ? 2 : 1));
            } else {
                //User
                $row[11] = ($row[9] == 0 ? 1 : ($row[9] == 1 ? 2 : 0));
            }
            #$row[4] = date("d-m-Y H:i:s", strtotime($row[4]));
            if ($row[2] == "mensal") {
                $row[2] = "Mensal";
                $row[6] = "<i class='icon-ban-circle'></i>";
                $row[5] = "<i class='icon-ban-circle'></i>";
            } else {
                if ($this->_user_level > 5) {
                    if ($row[6])
                        $row[6] = "$row[6]";
                    else
                        $row[6] = "<input placeholder='Insira código de cliente' class='cod_cliente_input span validate[custom[onlyNumberSp]]' data-clientID='$row[13]' type='text'/>";
                } else {
                    if (!$row[6])
                        $row[6] = "<i class='icon-ban-circle'></i>";
                }
            }
            $row[3] = $row[13] == "0" ? "<i class='icon-ban-circle'></i>" : "<button class='btn btn-mini icon-alone ver_cliente' data-lead_id='$row[13]' title='Ver Cliente'><i class='icon-edit'></i></button> $row[3]";
            switch ($row[9]) {
                case "0":
                    $row[10] = "<div class='btn-group'> <button class='btn accept_requisition btn-success icon-alone' value='" . $row[0] . "'><i class= 'icon-ok'></i></button><button class='btn decline_requisition btn-warning icon-alone' value='" . $row[0] . "'><i class= 'icon-remove'></i></button> </div>";
                    $row[9] = "Pedido enviado";
                    break;
                case "1":
                    $row[10] = "<div class='btn-group'> <button class='btn accept_requisition btn-success icon-alone' disabled value='" . $row[0] . "'><i class= 'icon-ok'></i></button><button class='btn decline_requisition btn-warning icon-alone' value='" . $row[0] . "'><i class= 'icon-remove'></i></button> </div>";
                    $row[9] = "<span class='label label-success'>Aprovado</span>";
                    break;
                case "2":
                    $row[10] = "<div class='btn-group'> <button class='btn accept_requisition btn-success icon-alone' value='" . $row[0] . "'><i class= 'icon-ok'></i></button><button class='btn decline_requisition btn-warning icon-alone' disabled value='" . $row[0] . "'><i class= 'icon-remove'></i></button> </div>";
                    $row[9] = "<span class='label label-important'>Pendente</span>";
                    break;
            }
            if ($row[7])
                $row[7] = "<button class='btn ver_requisition_anexo' value='$row[0]'><i class='icon-folder-open'></i>$row[7]</button>";
            else
                $row[7] = "Sem Anexo";
            $row[8] = "<div><button class='btn ver_requisition_products icon-alone' value='" . $row[0] . "'><i class='icon-eye-open'></i></button></div>";
            $row[12] = implode(",", $row);

            $result['aaData'][] = $row;
        }
        return $result;
    }

    public function get_requisitions_by_id($id_req)
    {
        $query = "SELECT sr.id,sr.user,sr.type,vl.first_name,sr.date,sr.contract_number,vl.extra2,sr.attachment,sr.products,sr.status, sr.lead_id,vl.middle_initial,vl.last_name,vl.phone_number,vl.address1,vl.city  FROM spice_requisition sr LEFT JOIN vicidial_list vl ON vl.lead_id=sr.lead_id WHERE sr.id=:id_req  ";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id_req" => $id_req));
        $result = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            switch ($row["status"]) {
                case "0":
                    $row["status"] = "Pedido enviado";
                    break;
                case "1":
                    $row["status"] = " Aprovado ";
                    break;
                case "2":
                    $row["status"] = " Pendente";
                    break;
            }
            $result[] = $row;
        }
        return $result;
    }

    public function edit_requisition($id, $cod_cliente)
    {
        $query = "UPDATE vicidial_list SET extra2=:cod_cliente WHERE lead_id=:id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":cod_cliente" => $cod_cliente, ":id" => $id));
    }

    public function create_requisition($type, $lead_id, $contract_number, $attachment, $products_list, $comments)
    {
        $query = "INSERT INTO `spice_requisition`( `user`, `type`, `lead_id`, `date`, `contract_number`, `attachment`, `products`,`comments`,`status`) VALUES ( :user, :type, :lead_id, :date, :contract_number, :attachment, :products,:comments, :status)";
        $stmt = $this->_db->prepare($query);
        $data = date('Y-m-d H:i:s');
        $stmt->execute(array(":user" => $this->_user_id, ":type" => $type, ":lead_id" => $lead_id, ":date" => $data, ":contract_number" => $contract_number, ":attachment" => $attachment, ":products" => json_encode($products_list), ":comments" => $comments, ":status" => 0));
        $last_insert_id = $this->_db->lastInsertId();
        return array($last_insert_id, $this->_user_id, $type, $lead_id, $data, $contract_number, $attachment, "<div><button class='btn ver_requisition_products' value='" . $last_insert_id . "'><i class='icon-eye-open'></i>Ver</button></div>", "Pedido enviado");
    }

    public function get_products_by_requisiton($id)
    {
        $query = "SELECT products,comments FROM spice_requisition WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array("id" => $id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $productalia["comments"] = $row["comments"];
        $productalia = array();
        foreach (json_decode($row["products"]) as $value) {
            $productalia[$value->id]["qcs"][] = array("quantity" => $value->quantity, "color" => $value->color, "color_name" => $value->color_name, "size" => $value->size);
        }
        $query = "SELECT id,name,category FROM spice_product WHERE id IN ('" . join("','", array_keys($productalia)) . "') ORDER BY category ASC";
        $stmt = $this->_db->prepare($query);
        $stmt->execute();
        while ($row1 = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productalia[$row1["id"]]["product_info"] = array("name" => $row1["name"], "category" => $row1["category"]);
        }

        return $productalia;
    }

    public function get_comments_by_requisiton($id)
    {
        $query = "SELECT comments FROM spice_requisition WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        $comments = $stmt->fetch(PDO::FETCH_ASSOC);
        return $comments["comments"];
    }

    public function accept_requisition($id)
    {
        $query = "UPDATE  spice_requisition SET status=1 WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array("id" => $id));
        return $this->getUser($id);
    }

    public function decline_requisition($id)
    {
        $query = "UPDATE  spice_requisition SET status=2 WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array("id" => $id));
        return $this->getUser($id);
    }

    public function check_month_requisitions()
    {
        $query = "SELECT count(id) count FROM  spice_requisition  WHERE user=:user AND date BETWEEN :date_first AND :date_last AND type='month' AND status != '2' ";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":user" => $this->_user_id, ":date_first" => date("Y-m-01") . " 00:00:00", ":date_last" => date("Y-m-t") . " 23:59:59"));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row["count"];
    }

    private function getUser($id)
    {
        $query = "SELECT user FROM spice_requisition WHERE id=:id";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

}
