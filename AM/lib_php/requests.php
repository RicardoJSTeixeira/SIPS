<?php

Class requests {

    public function __construct($db, $user_level, $user_id) {
        $this->_user_level = $user_level;
        $this->_user_id = $user_id;
        $this->_db = $db;
    }


}
