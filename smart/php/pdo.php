<?php

try {
        $db = new PDO("pgsql:dbname=fusionpbx;host=localhost", 'postgres', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    } catch (PDOException $e) {
        die('Connection failed: ' . $e->getMessage());
    }
    