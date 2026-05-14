<?php
class CountryController {
    private $db;
    private $country;

    public function __construct($db) {
        $this->db      = $db;
        $this->country = new Country($db);
    }

    // GET /country
    public function getAll() {
        $stmt = $this->country->read();
        $num  = $stmt->rowCount();

        if ($num > 0) {
            $countries = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $countries[] = [
                    "id"   => $row['id'],
                    "name" => $row['name']
                ];
            }
            http_response_code(200);
            echo json_encode($countries);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "No countries found."]);
        }
    }

    // POST /country
    public function create($data) {
        if (!empty($data->name)) {
            $this->country->name = $data->name;
            if ($this->country->create()) {
                http_response_code(201);
                echo json_encode(["message" => "Country created successfully."]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Unable to create the country."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Name is required."]);
        }
    }

    // PUT /country/{id}
    public function update($id, $data) {
        if (!empty($id) && !empty($data->name)) {
            $this->country->id   = $id;
            $this->country->name = $data->name;
            if ($this->country->update()) {
                http_response_code(200);
                echo json_encode(["message" => "Country updated successfully."]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Unable to update the country."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Insufficient data."]);
        }
    }

    // DELETE /country/{id}
    public function delete($id) {
        if (!empty($id)) {
            $this->country->id = $id;
            if ($this->country->delete()) {
                http_response_code(200);
                echo json_encode(["message" => "Country deleted successfully."]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Unable to delete the country."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "ID is required."]);
        }
    }
}