<?php
class Database {
    private $host = "localhost";
    private $db_name = "Orizon";
    private $username = "root";
    private $password = "root";

    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            
            // QUESTA RIGA È LA PIÙ IMPORTANTE
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            // Ora se fallisce, vedrai l'errore chiaramente
            echo "Errore di connessione: " . $exception->getMessage();
            die(); // Fermiamo tutto se non ci connettiamo
        }
        return $this->conn;
    }
}
?>