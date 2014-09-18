<?php

Class Calendars
{

    protected $_db;

    public function __construct(PDO $db)
    {
        $this->_db = $db;
    }

    public function getNames($user)
    {
        $refs = $this->_getRefs($user);
        $names = array();
        foreach ($refs as $ref) {
            $names[] = (object)array("name" => $this->_getName($ref->id, $ref->is_scheduler), "id" => $ref->id, "is_scheduler" => $ref->is_scheduler);
        }
        return $names;
    }

    public function _getRefs($user)
    {
        $stmt = $this->_db->prepare("SELECT user, id_calendar, cal_type FROM sips_sd_agent_ref WHERE user=:user");
        $stmt->execute(array(":user" => $user));
        $refs = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            if ($row->cal_type != "SCHEDULER") {
                $refs[] = (object)array("id" => $row->id_calendar);
            } else {
                $rsc = $this->_db->prepare("SELECT id_resource id FROM sips_sd_resources WHERE id_scheduler=:id AND active=1");
                $rsc->execute(array(":id" => $row->id_calendar));
                while ($rscs = $rsc->fetch(PDO::FETCH_OBJ)) {
                    $refs[] = $rscs;
                }
            }
        }
        return $refs;
    }

    protected function _getName($id, $is_scheduler)
    {
        if ($is_scheduler) {
            $query = "SELECT display_text name FROM sips_sd_schedulers WHERE id_scheduler=:id AND active=1";
        } else {
            $query = "SELECT display_text name FROM sips_sd_resources WHERE id_resource=:id AND active=1";
        }
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetch(PDO::FETCH_OBJ)->name;
    }

    public function getByRefs($user, $beg, $end)
    {
        $reservas = array();
        $refs = $this->_getRefs($user);
        foreach ($refs as $row) {
            $reservas = \array_merge($reservas, $this->_getReservas($row->is_scheduler, $row->id, \date('Y-m-d H:i:s', $beg), \date('Y-m-d H:i:s', $end), true));
        }
        return $reservas;
    }

    protected function _getReservas($is_scheduler, $id, $beg, $end, $forceUneditable = false)
    {
        if ($is_scheduler) {
            $query = "SELECT id_reservation, start_date, end_date, a.id_resource,id_user,a.lead_id,id_reservation_type, b.display_text rsc_name, min_time, max_time, del, e.display_text, d.postal_code, CONCAT(d.first_name, ' ', d.middle_initial, ' ', d.last_name) client_name, c.extra1 codCamp, changed, e.closed, obs, extra_id, has_accessories, sale, useful
                FROM sips_sd_reservations a
                LEFT JOIN vicidial_list d ON a.lead_id = d.lead_id
                LEFT JOIN sips_sd_resources b ON a.id_resource=b.id_resource
                LEFT JOIN sips_sd_reservations_types e ON a.id_reservation_type=e.id_reservations_types
                LEFT JOIN spice_consulta f ON a.id_reservation=f.reserva_id
                WHERE b.id_scheduler=:id AND start_date <=:end AND end_date >=:beg AND gone=0";
        } else {
            $query = "SELECT id_reservation, start_date, end_date, a.id_resource,id_user,a.lead_id,id_reservation_type, b.display_text rsc_name, min_time, max_time, del, d.display_text, c.postal_code, CONCAT(c.first_name, ' ', c.middle_initial, ' ', c.last_name) client_name, c.extra1 codCamp, changed, e.closed, obs, extra_id, has_accessories, sale, useful
                FROM sips_sd_reservations a
                LEFT JOIN vicidial_list c ON a.lead_id = c.lead_id
                LEFT JOIN sips_sd_resources b ON a.id_resource=b.id_resource
                LEFT JOIN sips_sd_reservations_types d ON a.id_reservation_type=d.id_reservations_types
                LEFT JOIN spice_consulta e ON a.id_reservation=e.reserva_id
                WHERE a.id_resource=:id AND start_date <=:end AND end_date >=:beg AND gone=0";
        }
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id, ":end" => $end, ":beg" => $beg));
        $reservars = array();
        $system_types = $this->getSystemTypes(false);
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $reservars[] = array(
                'id' => (int)$row->id_reservation,
                'title' => (string)$row->rsc_name . " " . $row->display_text . (((bool)$row->closed) ? " - Fechado" : ""),
                'type_text' => (string)$row->display_text,
                'client_name' => (string)$row->client_name,
                'lead_id' => (int)$row->lead_id,
                'codCamp' => (string)$row->codCamp,
                'postal' => (string)$row->postal_code,
                'start' => (string)$row->start_date,
                'end' => (string)$row->end_date,
                'editable' => (bool)!(((bool)$row->closed || $row->has_accessories || $forceUneditable) || ($system_types[$row->id_reservation_type])),
                'closed' => (bool)$row->closed || $row->has_accessories,
                'changed' => (int)$row->changed,
                'className' => (string)"t" . $row->id_reservation_type . (((bool)$row->del) ? " del" : ""),
                'bloqueio' => (bool)($system_types[$row->id_reservation_type] == "Rastreio c/ MKT"),
                'user' => (string)$row->id_user,
                'system' => (bool)$system_types[$row->id_reservation_type],
                'extra_id' => (int)$row->extra_id,
                'rsc' => (int)$row->id_resource,
                'max' => (int)$row->max_time,
                'min' => (int)$row->min_time,
                'del' => (bool)$row->del,
                'obs' => (string)$row->obs,
                "sale" => (bool)$row->sale,
                "useful" => (bool)$row->useful
            );
        }
        return $reservars;
    }

    public function getSystemTypes($inverted = true)
    {
        $stmt = $this->_db->prepare("SELECT id_reservations_types, display_text, color,active FROM sips_sd_reservations_types WHERE user_group='SYSTEM'");
        $stmt->execute();
        $system = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            if ($inverted) {
                $system[$row->display_text] = $row->id_reservations_types;
            } else {
                $system[$row->id_reservations_types] = $row->display_text;
            }
        }
        return $system;
    }

    public function getTipoReservas($user_groups = array())
    {
        if ($user_groups) {
            $user_groups[] = "SYSTEM";

            $prepare_hack = "";

            for ($index = 0; $index < count($user_groups); $index++) {
                $prepare_hack .= "?,";
            }
            $prepare_hack = rtrim($prepare_hack, ",");
            $stmt = $this->_db->prepare("SELECT id_reservations_types, display_text, color, min_time, max_time, active, user_group, sale, useful FROM sips_sd_reservations_types WHERE user_group IN ($prepare_hack);");
        } else {
            $stmt = $this->_db->prepare("SELECT id_reservations_types, display_text, color, min_time, max_time, active, user_group, sale, useful FROM sips_sd_reservations_types");
        }
        $stmt->execute($user_groups);

        $tipo_reservas = array();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $tipo_reservas[] = (object)array(
                "id" => $row->id_reservations_types,
                "css" => ".t" . $row->id_reservations_types . " {background: " . $row->color . ";}",
                "type" => $row->id_reservations_types,
                "text" => $row->display_text,
                "max" => (int)$row->max_time,
                "min" => (int)$row->min_time,
                "active" => (bool)!(!$row->active || ($row->user_group == "SYSTEM")),
                "color" => $row->color,
                "sale" => (bool)$row->sale,
                "useful" => (bool)$row->useful);
        }
        return $tipo_reservas;
    }

    public function newReserva($user, $lead_id, $start, $end, $rtype, $resource, $obs = "", $extraid = "")
    {
        if ($start == $end) {
            throw new Exception("Start and End date are the same.");
        }
        $query = "INSERT INTO sips_sd_reservations(start_date, end_date, has_accessories, id_reservation_type, id_resource,id_user,lead_id,obs,extra_id) VALUES (:start, :end, '0', :rtype, :resource, :user, :lead_id, :obs, :extra_id)";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":user" => $user, ":lead_id" => $lead_id, ":start" => \date('Y-m-d H:i:s', $start), ":end" => \date('Y-m-d H:i:s', $end), ":rtype" => $rtype, ":resource" => $resource, ":obs" => $obs, ":extra_id" => $extraid));
        return (int)$this->_db->lastInsertId();
    }

    public function removeReserva($id)
    {
        $query = "UPDATE sips_sd_reservations SET gone=1 WHERE id_reservation=:id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id" => $id));
    }

    public function deleteReserva($id)
    {
        $query = "UPDATE sips_sd_reservations SET del=1 WHERE id_reservation=:id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id" => $id));
    }

    public function closeMKT($id)
    {
        $query = "UPDATE sips_sd_reservations SET has_accessories=1 WHERE id_reservation=:id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id" => $id));
    }

    public function changeReserva($id, $start, $end)
    {
        $query = "UPDATE sips_sd_reservations SET start_date=:start,end_date=:end, changed=changed+1 WHERE id_reservation=:id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id" => $id, ":start" => \date('Y-m-d H:i:s', $start), ":end" => \date('Y-m-d H:i:s', $end)));
    }

    public function changeReservaResource($id, $rsc_id)
    {
        $query = "UPDATE sips_sd_reservations SET id_resource=:rsc_id WHERE id_reservation=:id";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":id" => $id, ":rsc_id" => $rsc_id));
    }

    public function set_obs($obs, $id_reservation)
    {
        $query = "Update sips_sd_reservations SET obs = :obs, has_accessories=1 WHERE id_reservation = :id_reservation";
        $stmt = $this->_db->prepare($query);
        return $stmt->execute(array(":obs" => json_encode(array("date" => \date('Y-m-d H:i:s'), "obs" => $obs)), ":id_reservation" => $id_reservation));
    }

    public function get_obs($id_reservation)
    {
        $query = "Select obs from sips_sd_reservations  WHERE id_reservation = :id_reservation";
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id_reservation" => $id_reservation));
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        $result = json_decode($result->obs);
        $obs = array("date" => $result->date, "obs" => $result->obs);
        return $obs;
    }

    public function getResTypeRaw()
    {
        $stmt = $this->_db->prepare("SELECT id_reservations_types FROM sips_sd_reservations_types WHERE sale =1");
        $stmt->execute();
        $rs = array();
        while ($v = $stmt->fetch(PDO::FETCH_OBJ)) {
            $rs[] = $v->id_reservations_types;
        }
        return $rs;
    }

    protected function _getSeries($id, $is_scheduler)
    {
        if ($is_scheduler) {
            $query = "Select a.id_resource,a.start_time,a.end_time,a.day_of_week_start,a.day_of_week_end From sips_sd_series a LEFT JOIN sips_sd_resources b ON a.id_resource = b.id_resource WHERE b.id_scheduler=:id";
        } else {
            $query = "Select id_resource,start_time,end_time,day_of_week_start,day_of_week_end From sips_sd_series Where id_resource=:id";
        }
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id));
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    protected function _getExcecoes($beg, $end, $id, $is_scheduler)
    {
        if ($is_scheduler) {
            $query = "SELECT a.id_execao,a.id_resource, a.start_date, a.end_date FROM sips_sd_execoes a LEFT JOIN sips_sd_resources b ON a.id_resource=b.id_resource WHERE b.id_scheduler=:id AND a.start_date < :end AND a.end_date > :start;";
        } else {
            $query = "SELECT id_execao,id_resource, start_date, end_date FROM sips_sd_execoes WHERE id_resource=:id AND start_date < :end AND end_date > :start;";
        }
        $stmt = $this->_db->prepare($query);
        $stmt->execute(array(":id" => $id, ":start" => \date('Y-m-d H:i:s', $beg), ":end" => \date('Y-m-d H:i:s', $end)));
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    protected function _getConfigs($id, $type)
    {
        switch ($type) {
            case "sch":
                $where = "b.id_scheduler=:id";
                break;
            case "rsc":
                $where = "b.id_resource=:id";
                break;
            case "ref":
                $where = "c.cp=:id";
                break;
            case "cp":
                $where = "a.alias_code=:id";
                break;
            default:
                die("Erro: calendário inválido.");
                break;
        }
        $resources = Array();
        $user_groups = Array();
        $stmt = $this->_db->prepare("SELECT b.display_text, days_visible, blocks, begin_time, end_time, a.id_scheduler, b.id_resource, b.restrict_days, a.user_group FROM sips_sd_schedulers a INNER JOIN sips_sd_resources b ON a.id_scheduler=b.id_scheduler WHERE $where AND b.active=1 AND a.active=1");
        $stmt->execute(array(":id" => $id));
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $resources[] = $row;
            $user_groups[] = $row->user_group;
        }
        return (object)array("resources" => $resources, "user_groups" => $user_groups);
    }

}

class Calendar extends Calendars
{

    protected $_resources = array();
    protected $_tipo_reservas = array();
    protected $_type_ref = "";
    protected $_id_ref = "";
    protected $_id_scheduler = "";
    protected $_id_resource = "";
    protected $_is_scheduler = true;
    protected $_is_restricted_days = true;
    protected $_user_groups = array();
    protected $_db;

    public function __construct(PDO $db, $id, $type)
    {
        $this->_db = $db;
        $this->_type_ref = $type;
        $this->_id_ref = $id;

        $calendarios = parent::_getConfigs($id, $type);
        $this->_resources = $calendarios->resources;
        $this->_user_groups = $calendarios->user_groups;
        $this->_is_restricted_days = $this->_resources[0]->restrict_days;

        if ($this->_type_ref == "cp" or $this->_type_ref == "ref" or $this->_type_ref == "rsc") {
            $this->_is_scheduler = false;
            $this->_id_resource = $this->_resources[0]->id_resource;
        } else {
            $this->_id_scheduler = $this->_resources[0]->id_scheduler;
        }
    }

    public function getNames($user)
    {
        $name_arr = array();
        foreach ($this->_resources as $rsc) {
            $name_arr[] = $rsc->display_text;
        }
        return implode(" ", $name_arr);
    }

    public function getReservas($beg, $end)
    {
        return parent::_getReservas($this->_is_scheduler, ($this->_is_scheduler) ? $this->_id_scheduler : $this->_id_resource, \date('Y-m-d H:i:s', $beg), \date('Y-m-d H:i:s', $end), false);
    }

    public function getTipoReservas($user_groups = array())
    {
        return parent::getTipoReservas($this->_user_groups);
    }

    public function getConfigs()
    {
        return (object)array(
            "defaultEventMinutes" => (int)$this->_resources[0]->blocks,
            "events" => (object)array(
                    "data" => (object)array(
                            "resource" => $this->_id_ref
                        )
                ),
            #"slotMinutes" => (int) $this->_resources[0]->blocks,
            "slotMinutes" => 15,
            "minTime" => $this->_resources[0]->begin_time / 60,
            "maxTime" => ($this->_resources[0]->end_time / 60) + ($this->_resources[0]->blocks / 60),
            "sch" => $this->_id_scheduler,
            "lazyFetching" => (bool)!$this->_is_restricted_days,
        );
    }

    public function getBloqueios($beg, $end)
    {
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
                        'className' => "bloqueado",
                        'bloqueio' => true
                    );
                }
            }
            $aux_date = strtotime("+1 day", $aux_date);
        } while (date('Y-m-d', $aux_date) != date('Y-m-d', $end));
        $excepcoes = $this->_getExcecoes($beg, $end);
        foreach ($excepcoes as $excepcao) {
            $blocks[] = array(
                'start' => $excepcao->start_date,
                'end' => $excepcao->end_date,
                'editable' => false,
                'className' => "bloqueado",
                'bloqueio' => true
            );
        }
        if ($this->_is_restricted_days) {
            return $this->inverter($beg, $this->_resources[0]->begin_time / 60, $this->_resources[0]->end_time / 60, $blocks);
        }

        return $blocks;
    }

    protected function _getSeries($id = "", $is_scheduler = "")
    {
        return parent::_getSeries((($this->_is_scheduler) ? $this->_id_scheduler : $this->_id_resource), $this->_is_scheduler);
    }

    private function _stp($time)
    {
        $time1 = explode(":", $time);
        return "+$time1[0] hours $time1[1] minutes";
    }

    protected function _getExcecoes($beg, $end, $id = "", $is_scheduler = "")
    {
        return parent::_getExcecoes($beg, $end, ($this->_is_scheduler) ? $this->_id_scheduler : $this->_id_resource, $this->_is_scheduler);
    }

    public function inverter($start, $shour, $ehour, array $events)
    {
        $aux = $start;
        $blocks = array();
        for ($index = 0; $index < 7; $index++) {
            $blocks = array_merge($blocks, $this->_inverteBloqueio(strtotime("+ " . ($shour * 60) . " minutes", $aux), strtotime("+ " . ($ehour * 60) . " minutes", $aux), $events));
            $aux = strtotime("+1 day", $aux);
        }
        return $blocks;
    }

    private function _inverteBloqueio($start, $end, array $events)
    {
        $block = array();
        $block[] = array(
            'start' => \date('Y-m-d H:i:s', $start),
            'end' => \date('Y-m-d H:i:s', $end),
            'editable' => false,
            'className' => "bloqueado",
            'bloqueio' => true
        );

        while ($bl = array_pop($events)) {
            foreach ($block as $key => &$nbl) {
                //var_dump($bl); //desbloqueio programado
                //var_dump($nbl);
                if ((strtotime($nbl['start']) >= strtotime($bl['start'])) && (strtotime($nbl['end']) <= strtotime($bl['end']))) {
                    unset($block[$key]);
                } elseif ((strtotime($nbl['start']) < strtotime($bl['end'])) && (strtotime($nbl['start']) >= strtotime($bl['start']))) {
                    $nbl['start'] = $bl['end'];
                } elseif ((strtotime($nbl['end']) >= strtotime($bl['start'])) && (strtotime($nbl['end']) <= strtotime($bl['end']))) {
                    $nbl['end'] = $bl['start'];
                } elseif ((strtotime($nbl['start']) < strtotime($bl['start'])) && (strtotime($nbl['end']) > strtotime($bl['end']))) {
                    $block[] = array(
                        'start' => $bl['end'],
                        'end' => $nbl['end'],
                        'editable' => false,
                        'className' => "bloqueado",
                        'bloqueio' => true
                    );
                    $nbl['end'] = $bl['start'];
                }
            }
        }
        return $block;
    }

}