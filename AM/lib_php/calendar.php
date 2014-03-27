<?php

Class Calendars {

    protected $_db;

    public function __construct($db) {
        $this->_db = $db;
    }

    protected function _getReservas($is_scheduler, $id, $beg, $end) {
        if ($is_scheduler) {
            $query = "SELECT id_reservation, start_date, end_date, a.id_resource,id_user,a.lead_id,id_reservation_type, e.display_text, d.postal_code, d.first_name, c.extra1 codCamp, IFNULL(e.id,false) closed "
                    . "FROM sips_sd_reservations a "
                    . "LEFT JOIN vicidial_list d ON a.lead_id = d.lead_id "
                    . "LEFT JOIN sips_sd_resources b ON a.id_resource=b.id_resource "
                    . "LEFT JOIN sips_sd_reservations_types e ON a.id_reservation_type=e.id_reservations_types "
                    . "LEFT JOIN spice_consulta f ON a.id_reservation=f.reserva_id "
                    . "WHERE b.id_scheduler=:id And start_date <=:end And start_date >=:beg";
        } else {
            $query = "SELECT id_reservation, start_date, end_date, id_resource,id_user,a.lead_id,id_reservation_type, d.display_text, c.postal_code, c.first_name, c.extra1 codCamp, IFNULL(e.id,false) closed "
                    . "FROM sips_sd_reservations a "
                    . "LEFT JOIN vicidial_list c ON a.lead_id = c.lead_id "
                    . "LEFT JOIN sips_sd_reservations_types d ON a.id_reservation_type=d.id_reservations_types "
                    . "LEFT JOIN spice_consulta e ON a.id_reservation=e.reserva_id "
                    . "WHERE id_resource=:id And start_date <=:end And start_date >=:beg";
        }
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id, ":end" => $end, ":beg" => $beg));
        $reservars = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $reservars[] = array(
                'id' => $row->id_reservation,
                'title' => ((is_null($row->postal_code)) ? $row->display_text : $row->postal_code.' - '.$row->display_text).(((bool)$row->closed)?" - Fechado":""),
                'client_name' => (is_null($row->first_name) ? "" : $row->first_name),
                'lead_id' => (is_null($row->lead_id) ? "" : $row->lead_id),
                'codCamp' => (is_null($row->codCamp) ? "" : $row->codCamp),
                'start' => $row->start_date,
                'end' => $row->end_date,
                'editable' => !(bool)$row->closed,
                'className' => "t" . $row->id_reservation_type,
                'user' => $row->id_user,
            );
        }
        return $reservars;
    }

    protected function _getRefs($user) {
        $stmt = $this->_db->prepare("SELECT user, id_calendar, cal_type FROM sips_sd_agent_ref WHERE user=:user");
        $stmt->execute(array(":user" => $user));
        $refs = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            if ($row->cal_type != "SCHEDULER") {
                $refs[] = (object) array("is_scheduler" => false, "id" => $row->id_calendar);
            } else {
                $rsc = $this->_db->prepare("SELECT id_resource FROM sips_sd_resources WHERE id_scheduler=:id AND active=1");
                $rsc->execute(array(":id" => $row->id_calendar));
                while ($row_rsc = $rsc->fetch(PDO::FETCH_OBJ)) {
                    $refs[] = (object) array("is_scheduler" => false, "id" => $row_rsc->id_resource);
                }
            }
        }
        return $refs;
    }

    public function getNames($user) {
        $refs = $this->_getRefs($user);
        $names = array();
        foreach ($refs as $ref) {
            $names[] = (object) array("name" => $this->_getName($ref->id, $ref->is_scheduler), "id" => $ref->id, "is_scheduler" => $ref->is_scheduler);
        }
        return $names;
    }

    public function getByRefs($user, $beg, $end) {
        $reservas = array();
        $refs = $this->_getRefs($user);
        foreach ($refs as $row) {
            $reservas = \array_merge($reservas, $this->_getReservas($row->is_scheduler, $row->id, \date('Y-m-d H:i:s', $beg), \date('Y-m-d H:i:s', $end)));
        }
        return $reservas;
    }

    public function getTipoReservas($user_groups = array()) {
        if ($user_groups) {
            for ($index = 0; $index < count($user_groups); $index++) {
                $prepare_hack.="?,";
            }
            $stmt = $this->_db->prepare("SELECT id_reservations_types, display_text, color,active FROM sips_sd_reservations_types where user_group in (" . rtrim($prepare_hack, ",") . ");");
        } else {
            $stmt = $this->_db->prepare("SELECT id_reservations_types, display_text, color,active FROM sips_sd_reservations_types");
        }
        $stmt->execute($user_groups);
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $this->_tipo_reservas[] = (object) array("id" => $row->id_reservations_types, "css" => ".t" . $row->id_reservations_types . " {background: " . $row->color . ";}", "type" => $row->id_reservations_types, "text" => $row->display_text, "color" => $row->color);
        }
        return $this->_tipo_reservas;
    }

    protected function _getSeries($id, $is_scheduler) {
        if ($is_scheduler) {
            $query = "Select a.id_resource,a.start_time,a.end_time,a.day_of_week_start,a.day_of_week_end From sips_sd_series a LEFT JOIN sips_sd_resources b ON a.id_resource = b.id_resource Where b.id_scheduler=:id";
        } else {
            $query = "Select id_resource,start_time,end_time,day_of_week_start,day_of_week_end From sips_sd_series Where id_resource=:id";
        }
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    protected function _getExcecoes($id, $is_scheduler) {
        if ($is_scheduler) {
            $query = "SELECT a.id_execao,a.id_resource, a.start_date, a.end_date FROM sips_sd_execoes a LEFT JOIN sips_sd_resources b ON a.id_resource=b.id_resource WHERE b.id_scheduler=:id";
        } else {
            $query = "SELECT id_execao,id_resource, start_date, end_date FROM sips_sd_execoes WHERE id_resource=:id";
        }
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    protected function _getConfigs($id, $type) {
        switch ($type) {
            case "sch":$where = "b.id_scheduler=:id";
                break;
            case "rsc":$where = "b.id_resource=:id";
                break;
            case "ref":$where = "c.cp=:id";
                break;
            case "cp":$where = "a.alias_code=:id";
                break;
            default:die("Erro: calendário inválido.");
                break;
        }
        $resources = Array();
        $user_groups = Array();
        $stmt = $this->_db->prepare("SELECT b.display_text,days_visible,blocks,begin_time,end_time,a.id_scheduler,b.id_resource,b.restrict_days,a.user_group FROM sips_sd_schedulers a INNER JOIN sips_sd_resources b ON a.id_scheduler=b.id_scheduler WHERE $where AND b.active=1 AND a.active=1");
        $stmt->execute(array(":id" => $id));
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $resources[] = $row;
            $user_groups[] = $row->user_group;
        }
        return (object) array("resources" => $resources, "user_groups" => $user_groups);
    }

    protected function _getName($id, $is_scheduler) {
        if ($is_scheduler) {
            $query = "SELECT display_text name FROM sips_sd_schedulers WHERE id_scheduler=:id AND active=1";
        } else {
            $query = "SELECT display_text name FROM sips_sd_resources WHERE id_resource=:id AND active=1";
        }
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetch(PDO::FETCH_OBJ)->name;
    }

    public function newReserva($user, $lead_id, $start, $end, $rtype, $resource) {
        $query = "INSERT INTO `sips_sd_reservations`(`start_date`, `end_date`, `has_accessories`, `id_reservation_type`, `id_resource`,`id_user`,`lead_id`) VALUES (:start, :end, '0', :rtype, :resource,:user,:lead_id)";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":user" => $user, ":lead_id" => $lead_id, ":start" => date('Y-m-d H:i:s',$start), ":end" => date('Y-m-d H:i:s',$end), ":rtype" => $rtype, ":resource" => $resource));
        return $this->_db->lastInsertId();
    }

    public function removeReserva($id) {
        $query = "DELETE FROM sips_sd_reservations WHERE id_reservation=:id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id" => $id));
    }

    public function changeReserva($id, $start, $end) {
        $query = "UPDATE `sips_sd_reservations` SET `start_date`=:start,`end_date`=:end WHERE `id_reservation`=:id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id" => $id, ":start" => date('Y-m-d H:i:s',$start), ":end" => date('Y-m-d H:i:s',$end)));
    }

}

class Calendar extends Calendars {

    protected $_resources = array();
    protected $_tipo_reservas = array();
    protected $_type_ref = "";
    protected $_id_ref = "";
    protected $_id_scheduler = "";
    protected $_id_resource = "";
    protected $_is_scheduler = true;
    protected $_user_groups = array();
    protected $_db;

    public function __construct(PDO $db, $id, $type) {
        $this->_db = $db;
        $this->_type_ref = $type;
        $this->_id_ref = $id;

        $calendarios = parent::_getConfigs($id, $type);
        $this->_resources = $calendarios->resources;
        $this->_user_groups = $calendarios->user_groups;
        if ($this->_type_ref == "cp" or $this->_type_ref == "ref" or $this->_type_ref == "rsc") {
            $this->_is_scheduler = false;
            $this->_id_resource = $this->_resources[0]->id_resource;
        } else {
            $this->_id_scheduler = $this->_resources[0]->id_scheduler;
        }
    }

    public function getNames($user) {
        $name_arr = array();
        foreach ($this->_resources as $rsc) {
            $name_arr[] = $rsc->display_text;
        }
        return implode(" ", $name_arr);
    }

    public function getReservas($beg, $end) {
        return parent::_getReservas($this->_is_scheduler, ($this->_is_scheduler) ? $this->_id_scheduler : $this->_id_resource, \date('Y-m-d H:i:s', $beg), \date('Y-m-d H:i:s', $end));
    }

    public function getTipoReservas($user_groups = array()) {
        return parent::getTipoReservas($this->_user_groups);
    }

    public function getConfigs() {
        return (object) array("defaultEventMinutes" => (int) $this->_resources[0]->blocks, "events" => (object) array("data" => (object) array("resource" => $this->_id_ref, "is_scheduler" => $this->_is_scheduler)), "slotMinutes" => (int) $this->_resources[0]->blocks, "minTime" => $this->_resources[0]->begin_time / 60, "maxTime" => $this->_resources[0]->end_time / 60, "sch" => $this->_id_scheduler);
    }

    protected function _getSeries($id="", $is_scheduler="") {
        return parent::_getSeries((($this->_is_scheduler) ? $this->_id_scheduler : $this->_id_resource), $this->_is_scheduler);
    }

    protected function _getExcecoes($id="", $is_scheduler="") {
        return parent::_getExcecoes(($this->_is_scheduler) ? $this->_id_scheduler : $this->_id_resource, $this->_is_scheduler);
    }

    public function getBloqueios($beg, $end) {
        $blocks = array();
        $series = $this->_getSeries();
        $aux_date = $beg;
        do {
            foreach ($series as $serie) {
                if (\date("N", $aux_date) >= $serie->day_of_week_start && \date("N", $aux_date) <= $serie->day_of_week_end) {
                    $blocks[] = array(
                        'start' => \date('Y-m-d H:i:s', strtotime($this->_stp($serie->start_time), $aux_date)),
                        'end' => \date('Y-m-d H:i:s', strtotime($this->_stp($serie->end_time), $aux_date)),
                        'editable' => false,
                        'className' => "bloqueado"
                    );
                }
            }
            $aux_date = strtotime("+1 day", $aux_date);
        } while (date('Y-m-d',$aux_date) != date('Y-m-d',$end));
        $excepcoes = $this->_getExcecoes();
        foreach ($excepcoes as $excepcao) {
            $blocks[] = array(
                'start' => $excepcao->start_date,
                'end' => $excepcao->end_date,
                'editable' => false,
                'className' => "bloqueado"
            );
        }
        return $blocks;
    }

    private function _stp($time) {
        $time1 = explode(":", $time);
        return "+$time1[0] hours $time1[1] minutes";
    }

}
