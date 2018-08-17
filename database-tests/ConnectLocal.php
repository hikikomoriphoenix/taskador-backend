<?php
require_once __DIR__ . '/../autoload.php';

class ConnectLocal {
    static function connectToLocalhostDB() {
        $servername = 'localhost';
        $databasename = 'taskadordb';
        $username = 'taskador';
        $password = 'password';
        
        try {
            return Connect::connectToDB($servername, $databasename, $username, $password);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}

