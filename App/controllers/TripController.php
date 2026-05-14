<?php
class TripController {
    private $db;
    private $trip;

    public function __construct($db) {
        $this->db   = $db;
        $this->trip = new Trip($db);
    }

    // GET /trip/{id}
    public function getOne($id) {
        $this->trip->id = $id;
        $stmt = $this->trip->readOne();
        $num  = $stmt->rowCount();

        if ($num > 0) {
            $trip_item = null;

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (!$trip_item) {
                    $trip_item = [
                        "id"              => $id,
                        "available_seats" => $row['available_seats'],
                        "countries"       => []
                    ];
                }

                if (!empty($row['country_name'])) {
                    $trip_item["countries"][] = [
                        "id"   => $row['country_id'],
                        "name" => $row['country_name']
                    ];
                }
            }

            http_response_code(200);
            echo json_encode($trip_item);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Trip not found."]);
        }
    }

    // GET /trip
    public function getAll($params) {
        $seats      = $params['seats']      ?? null;
        $country_id = $params['country_id'] ?? null;

        $stmt = $this->trip->read($country_id, $seats);
        $num  = $stmt->rowCount();

        if ($num > 0) {
            $trips = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tid = $row['id'];

                if (!isset($trips[$tid])) {
                    $trips[$tid] = [
                        "id"              => $tid,
                        "available_seats" => $row['available_seats'],
                        "countries"       => []
                    ];
                }

                if (!empty($row['country_name'])) {
                    $trips[$tid]["countries"][] = [
                        "id"   => $row['country_id'],
                        "name" => $row['country_name']
                    ];
                }
            }

            http_response_code(200);
            echo json_encode(array_values($trips));
        } else {
            http_response_code(404);
            echo json_encode(["message" => "No trips found."]);
        }
    }

    // POST /trip
    public function create($data) {
        if (!empty($data->available_seats) && !empty($data->country_ids)) {
            $this->trip->available_seats = $data->available_seats;
            $this->trip->country_ids     = $data->country_ids;

            if ($this->trip->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Trip created successfully."]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Unable to create the trip."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Incomplete data."]);
        }
    }

    // PUT /trip/{id}
    public function update($id, $data) {
        if (!empty($id) && !empty($data->available_seats)) {
            $this->trip->id              = $id;
            $this->trip->available_seats = $data->available_seats;
            $this->trip->country_ids     = $data->country_ids ?? [];

            if ($this->trip->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Trip updated successfully."]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Unable to update the trip."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID or data is missing."]);
        }
    }

    // DELETE /trip/{id}
    public function delete($id) {
        if (!empty($id)) {
            $this->trip->id = $id;

            if ($this->trip->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Trip deleted successfully."]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Unable to delete the trip."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID is required."]);
        }
    }
}