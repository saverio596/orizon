<?php
class Trip {
    private $conn;
    private $table = "trips";

    public $id;
    public $available_seats;
    public $country_ids = [];

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new trip with associated countries
    public function create() {
        try {
            $this->conn->beginTransaction();

            $query = "INSERT INTO " . $this->table . " (available_seats) VALUES (:seats)";
            $stmt  = $this->conn->prepare($query);
            $stmt->bindParam(':seats', $this->available_seats);

            if (!$stmt->execute()) {
                throw new Exception("Error while creating the trip.");
            }

            $this->id = $this->conn->lastInsertId();

            if (empty($this->id)) {
                throw new Exception("Unable to retrieve the trip ID.");
            }

            if (!empty($this->country_ids) && is_array($this->country_ids)) {
                foreach ($this->country_ids as $country_id) {
                    $query_pivot = "INSERT INTO trips_countries (trip_id, country_id) VALUES (:trip_id, :country_id)";
                    $stmt_pivot  = $this->conn->prepare($query_pivot);
                    $stmt_pivot->bindParam(':trip_id', $this->id);
                    $stmt_pivot->bindParam(':country_id', $country_id);

                    if (!$stmt_pivot->execute()) {
                        throw new Exception("Error while linking country ID: " . $country_id);
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

    // Read a single trip with its countries
    public function readOne() {
        $query = "SELECT t.id, t.available_seats, c.name AS country_name, c.id AS country_id
                  FROM " . $this->table . " t
                  LEFT JOIN trips_countries tc ON t.id = tc.trip_id
                  LEFT JOIN countries c ON tc.country_id = c.id
                  WHERE t.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt;
    }

    // Read all trips with optional filters
    public function read($country_id = null, $seats = null) {
        $query = "SELECT t.id, t.available_seats, c.name AS country_name, c.id AS country_id
                  FROM " . $this->table . " t
                  LEFT JOIN trips_countries tc ON t.id = tc.trip_id
                  LEFT JOIN countries c ON tc.country_id = c.id
                  WHERE 1=1";

        if ($country_id !== null) {
            $query .= " AND t.id IN (SELECT trip_id FROM trips_countries WHERE country_id = :country_id)";
        }
        if ($seats !== null) {
            $query .= " AND t.available_seats >= :seats";
        }

        $stmt = $this->conn->prepare($query);

        if ($country_id !== null) { $stmt->bindParam(':country_id', $country_id); }
        if ($seats !== null)      { $stmt->bindParam(':seats', $seats); }

        $stmt->execute();
        return $stmt;
    }

    // Delete a trip
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt  = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) return true;
        return false;
    }

    // Update a trip and its countries
    public function update() {
        try {
            $this->conn->beginTransaction();

            $query = "UPDATE " . $this->table . " SET available_seats = :seats WHERE id = :id";
            $stmt  = $this->conn->prepare($query);
            $stmt->bindParam(':seats', $this->available_seats);
            $stmt->bindParam(':id', $this->id);
            $stmt->execute();

            $query_delete = "DELETE FROM trips_countries WHERE trip_id = :id";
            $stmt_delete  = $this->conn->prepare($query_delete);
            $stmt_delete->bindParam(':id', $this->id);
            $stmt_delete->execute();

            if (!empty($this->country_ids)) {
                foreach ($this->country_ids as $country_id) {
                    $query_insert = "INSERT INTO trips_countries (trip_id, country_id) VALUES (:trip_id, :country_id)";
                    $stmt_insert  = $this->conn->prepare($query_insert);
                    $stmt_insert->bindParam(':trip_id', $this->id);
                    $stmt_insert->bindParam(':country_id', $country_id);
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