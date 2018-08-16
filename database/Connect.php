<?php
require_once __DIR__ . '/../config/config.php';

class Connect {
    /**
     * Creates a PDO instance that represents connection to taskador's backend
     * database.
     * 
     * @return PDO an instance of PDO.
     * @throws Exception
     */
    static function connectToTaskadorDB() {
        $servername = DBConfig::HOSTNAME;
        $databasename = DBConfig::DATABASENAME;
        $username = DBConfig::USERNAME;
        $password = DBConfig::PASSWORD; 
        
        try {
            return self::connectToDB($servername, $databasename, $username, $password);
        } catch(Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Creates a PDO instance. This represents connection to the database.
     * 
     * @return PDO an instance to PDO.
     * @throws PDOException 
     */
    static function connectToDB($servername, $databasename, $username, $password) {
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