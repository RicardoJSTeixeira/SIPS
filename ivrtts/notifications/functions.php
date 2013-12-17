<?php

function Notification($LastInsertId, $EventTime, $NotificationType) {
    global $db;
    switch ($NotificationType) {
        case "admin_global": $NType = "admin_global";
            break;
    }
    $params = array(1);
    $stmt = $db->prepare("SELECT id_user FROM zero.user_notifications WHERE $NType = ?");
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_BOTH);
    for ($i = 0; $i < count($results); $i++) {

        $params = array($results[$i]['id_user'], $EventTime, $LastInsertId);
        $stmt = $db->prepare("INSERT INTO zero.notifications (id_user, event_time, user_auth_log) VALUES (?, ?, ?)");
        $stmt->execute($params);
    }
}
