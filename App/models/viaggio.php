<?php
class Viaggio {
    private $conn;
    private $table_name = "viaggi";

    public $id;
    public $posti_disponibili;
    public $paesi_ids = [];

    public function __construct($db) {
        $this->conn = $db;
    }

    // Creare un nuovo viaggio
    public function create() {
        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO " . $this->table_name . " (posti_disponibili) VALUES (:posti)";
            $stmt  = $this->conn->prepare($query);
            $stmt->bindParam(':posti', $this->posti_disponibili);

            if (!$stmt->execute()) {
                throw new Exception("Errore durante la creazione del viaggio.");
            }

            $this->id = $this->conn->lastInsertId();

            if (empty($this->id)) {
                throw new Exception("Impossibile recuperare l'ID del viaggio.");
            }

            if (!empty($this->paesi_ids) && is_array($this->paesi_ids)) {
                foreach ($this->paesi_ids as $p_id) {
                    $query_ponte = "INSERT INTO viaggi_paesi (viaggio_id, paese_id) VALUES (:v_id, :p_id)";
                    $stmt_ponte  = $this->conn->prepare($query_ponte);
                    $stmt_ponte->bindParam(':v_id', $this->id);
                    $stmt_ponte->bindParam(':p_id', $p_id);

                    if (!$stmt_ponte->execute()) {
                        throw new Exception("Errore durante il collegamento del paese ID: " . $p_id);
                    }
                }
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Leggere un singolo viaggio con i suoi paesi
    public function readOne() {
        $query = "SELECT v.id, v.posti_disponibili, p.nome as paese_nome, p.id as paese_id
                  FROM " . $this->table_name . " v
                  LEFT JOIN viaggi_paesi vp ON v.id = vp.viaggio_id
                  LEFT JOIN paesi p ON vp.paese_id = p.id
                  WHERE v.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt;
    }

    // Leggere tutti i viaggi con filtri opzionali
    public function read($paese_id = null, $posti = null) {
        $query = "SELECT v.id, v.posti_disponibili, p.nome as paese_nome, p.id as paese_id
                  FROM " . $this->table_name . " v
                  LEFT JOIN viaggi_paesi vp ON v.id = vp.viaggio_id
                  LEFT JOIN paesi p ON vp.paese_id = p.id
                  WHERE 1=1";

        if ($paese_id !== null) {
            $query .= " AND v.id IN (SELECT viaggio_id FROM viaggi_paesi WHERE paese_id = :paese_id)";
        }
        if ($posti !== null) {
            $query .= " AND v.posti_disponibili >= :posti";
        }

        $stmt = $this->conn->prepare($query);

        if ($paese_id !== null) { $stmt->bindParam(':paese_id', $paese_id); }
        if ($posti !== null)     { $stmt->bindParam(':posti', $posti); }

        $stmt->execute();
        return $stmt;
    }

    // Eliminare un viaggio
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) return true;
        return false;
    }

    // Aggiornare un viaggio
    public function update() {
        try {
            $this->conn->beginTransaction();

            $query = "UPDATE " . $this->table_name . " SET posti_disponibili = :posti WHERE id = :id";
            $stmt  = $this->conn->prepare($query);
            $stmt->bindParam(':posti', $this->posti_disponibili);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();

            $query_delete = "DELETE FROM viaggi_paesi WHERE viaggio_id = :id";
            $stmt_delete  = $this->conn->prepare($query_delete);
            $stmt_delete->bindParam(':id', $this->id);
            $stmt_delete->execute();

            if (!empty($this->paesi_ids)) {
                foreach ($this->paesi_ids as $p_id) {
                    $query_insert = "INSERT INTO viaggi_paesi (viaggio_id, paese_id) VALUES (:v_id, :p_id)";
                    $stmt_insert  = $this->conn->prepare($query_insert);
                    $stmt_insert->bindParam(':v_id', $this->id);
                    $stmt_insert->bindParam(':p_id', $p_id);
                    $stmt_insert->execute();
                }
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>