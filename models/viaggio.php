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

    // creare un nuovo paese
    public function create() {
    try {
        // Iniziamo la transazione (o tutto o niente)
        $this->conn->beginTransaction();

        // 1. Prepariamo la query per la tabella 'viaggi'
        $query = "INSERT INTO " . $this->table_name . " (posti_disponibili) VALUES (:posti)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':posti', $this->posti_disponibili);

        // Eseguiamo l'inserimento del viaggio
        if (!$stmt->execute()) {
            throw new Exception("Errore durante la creazione del viaggio.");
        }

        // --- PASSAGGIO CHIAVE ---
        // Recuperiamo l'ID del viaggio appena inserito nel database
        $this->id = $this->conn->lastInsertId();

        // Se l'ID è vuoto, fermiamo tutto prima di generare l'errore SQL
        if (empty($this->id)) {
            throw new Exception("Impossibile recuperare l'ID del viaggio.");
        }

        // 2. Inseriamo i collegamenti nella tabella ponte 'viaggi_paesi'
        if (!empty($this->paesi_ids) && is_array($this->paesi_ids)) {
            foreach ($this->paesi_ids as $p_id) {
                $query_ponte = "INSERT INTO viaggi_paesi (viaggio_id, paese_id) VALUES (:v_id, :p_id)";
                $stmt_ponte = $this->conn->prepare($query_ponte);

                // Qui passiamo l'ID recuperato sopra
                $stmt_ponte->bindParam(':v_id', $this->id);
                $stmt_ponte->bindParam(':p_id', $p_id);

                if (!$stmt_ponte->execute()) {
                    throw new Exception("Errore durante il collegamento del paese ID: " . $p_id);
                }
            }
        }

        // Se siamo arrivati qui senza errori, confermiamo tutto nel database
        $this->conn->commit();
        return true;

    } catch (Exception $e) {
        // Se qualcosa è andato storto, annulliamo ogni modifica fatta sopra
        $this->conn->rollBack();
        return false;
    }
}

// Metodo per leggere i viaggi con filtri opzionali
public function read($paese_id = null, $posti = null) {
    $query = "SELECT v.id, v.posti_disponibili, p.nome as paese_nome, p.id as paese_id
              FROM " . $this->table_name . " v
              LEFT JOIN viaggi_paesi vp ON v.id = vp.viaggio_id
              LEFT JOIN paesi p ON vp.paese_id = p.id
              WHERE 1=1";

    if ($paese_id != null) {
        $query .= " AND v.id IN (SELECT viaggio_id FROM viaggi_paesi WHERE paese_id = :paese_id)";
    }
    if ($posti != null) {
        $query .= " AND v.posti_disponibili >= :posti";
    }

    $stmt = $this->conn->prepare($query);

    if ($paese_id != null) { $stmt->bindParam(':paese_id', $paese_id); }
    if ($posti != null) { $stmt->bindParam(':posti', $posti); }

    $stmt->execute();
    return $stmt;
}

// Metodo per eliminare un viaggio
public function delete() {
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
    $stmt = $this->conn->prepare($query);

    $this->id = htmlspecialchars(strip_tags($this->id));
    $stmt->bindParam(':id', $this->id);

    if ($stmt->execute()) return true;
    return false;
}

// Aggiornamento dei dati
public function update() {
    try {
        $this->conn->beginTransaction();

        // 1. Aggiorna i posti disponibili
        $query = "UPDATE " . $this->table_name . " SET posti_disponibili = :posti WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':posti', $this->posti_disponibili);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        // 2. Elimina i vecchi legami con i paesi
        $query_delete = "DELETE FROM viaggi_paesi WHERE viaggio_id = :id";
        $stmt_delete = $this->conn->prepare($query_delete);
        $stmt_delete->bindParam(':id', $this->id);
        $stmt_delete->execute();

        // 3. Inserisci i nuovi legami
        if (!empty($this->paesi_ids)) {
            foreach ($this->paesi_ids as $p_id) {
                $query_insert = "INSERT INTO viaggi_paesi (viaggio_id, paese_id) VALUES (:v_id, :p_id)";
                $stmt_insert = $this->conn->prepare($query_insert);
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