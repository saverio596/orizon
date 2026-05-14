<?php
class Country {
    private $conn;
    private $table = "countries";

    public $id;
    public $name;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new country
    public function create() {
        $query = "INSERT INTO " . $this->table . " (name) VALUES (:name)";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':name', $this->name);

        if ($stmt->execute()) return true;
        return false;
    }

    // Read all countries ordered by most recent
    public function read() {
        $query = "SELECT id, name FROM " . $this->table . " ORDER BY id DESC";
        $stmt  = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Update a country
    public function update() {
        $query = "UPDATE " . $this->table . " SET name = :name WHERE id = :id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) return true;
        return false;
    }

    // Delete a country
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) return true;
        return false;
    }
}
?>