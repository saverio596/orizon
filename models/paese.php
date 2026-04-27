<?php
class Paese {
    private $conn;
    private $table_name = "paesi";

    public $id;
    public $nome;

    public function __construct($db) {
        $this->conn = $db;
    }

    // creare un nuovo paese
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nome=:nome";
        
        $stmt = $this->conn->prepare($query);

        // Pulizia dati
        $this->nome = htmlspecialchars(strip_tags((string)$this->nome));

        $stmt->bindParam(":nome", $this->nome);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>