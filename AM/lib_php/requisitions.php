<?php

Class requisitions {

    public function __construct($db, $user_level, $user_id) {
        $this->_user_level = $user_level;
        $this->_user_id = $user_id;
        $this->_db = $db;
    }

    public function get_requisitions_to_datatable($show_admin) {

        $result['aaData'] = [];
        $query = "SELECT id,user,type,lead_id,date,contract_number,attachment,'products',status  from spice_requisition where user=:user";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":user" => $this->_user_id));

        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
            $row[2] = $row[2] == "month" ? "Mensal" : "Especial";
            $row[3] = $row[3] == "0" ? "Não utilizado" : $row[3];

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

            $row[7] = "<div> <button class='btn ver_requisition_products' value='" . $row["id"] . "'><i class='icon-eye-open'></i>Ver</button></div>";
            if ($this->_user_level > 5 || $show_admin == 1)
                $row[9] = $row[9] . " <button class='btn accept_requisition btn-success' value='" . $row["id"] . "'><i class= 'icon-ok'></i></button><button class='btn decline_requisition btn-warning' value='" . $row["id"] . "'><i class= 'icon-remove'></i></button></div>";

            $result['aaData'][] = $row;
        }

        return $result;
    }

    public function create_requisition($type, $lead_id, $contract_number, $attachment, $products_list) {
        $query = "INSERT INTO `spice_requisition`( `user`, `type`, `lead_id`, `date`, `contract_number`, `attachment`, `products`,`status`) VALUES ( :user,:type,:lead_id,:date,:contract_number,:attachment,:products,:status)";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":user" => $this->_user_id, ":type" => $type, ":lead_id" => $lead_id, ":date" => date('Y-m-d H:i:s'), ":contract_number" => $contract_number, ":attachment" => $attachment, ":products" => json_encode($products_list), ":status" => 0));
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
