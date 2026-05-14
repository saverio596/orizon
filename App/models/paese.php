<?php
class Paese {
    private $conn;
    private $table_name = "paesi";

    public $id;
    public $nome;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Creare un nuovo paese
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (nome) VALUES (:nome)";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':nome', $this->nome);

        if ($stmt->execute()) return true;
        return false;
    }

    // Leggere tutti i paesi dal più recente al più vecchio
    public function read() {
        $query = "SELECT id, nome FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Aggiornare un paese
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nome = :nome WHERE id = :id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) return true;
        return false;
    }

    // Eliminare un paese
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) return true;
        return false;
    }
}
?>