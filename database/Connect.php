<?php
require_once __DIR__ . '/../config/config.php';

class Connect {
    /**
     * Creates a PDO instance. This represents connection to the database.
     * 
     * @return PDO an instance to PDO.
     * @throws PDOException 
     */
    static function connectToDB() {
        $servername = DBConfig::HOSTNAME;
        $databasename = DBConfig::DATABASENAME;
        $username = DBConfig::USERNAME;
        $password = DBConfig::PASSWORD;

        try {
            $conn = new PDO("mysql:host=$servername;dbname=$databasename", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch(PDOException $e) {
            throw $e;
        }
    }
}