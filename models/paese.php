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

    public function read() {
    // La query che seleziona i paesi dal più recente al più vecchio
    $query = "SELECT id, nome FROM " . $this->table_name . " ORDER BY id DESC";
    
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    
    return $stmt;
}

// Metodo per aggiornare un paese
public function update() {
    $query = "UPDATE " . $this->table_name . " SET nome = :nome WHERE id = :id";
    $stmt = $this->conn->prepare($query);

    // Sanificazione
    $this->nome = htmlspecialchars(strip_tags($this->nome));
    $this->id = htmlspecialchars(strip_tags($this->id));

    $stmt->bindParam(':nome', $this->nome);
    $stmt->bindParam(':id', $this->id);

    if ($stmt->execute()) return true;
    return false;
}

// Metodo per eliminare un paese
public function delete() {
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
    $stmt = $this->conn->prepare($query);

    $this->id = htmlspecialchars(strip_tags($this->id));
    $stmt->bindParam(':id', $this->id);

    if ($stmt->execute()) return true;
    return false;
}
}
?>