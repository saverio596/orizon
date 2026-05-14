<?php
class ViaggioController {
    private $db;
    private $viaggio;

    public function __construct($db) {
        $this->db = $db;
        $this->viaggio = new Viaggio($db);
    }

    // GET /viaggio/{id}
    public function getOne($id) {
        $this->viaggio->id = $id;
        $stmt = $this->viaggio->readOne();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $viaggio_item = null;

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (!$viaggio_item) {
                    $viaggio_item = [
                        "id"                => $id,
                        "posti_disponibili" => $row['posti_disponibili'],
                        "paesi"             => []
                    ];
                }

                if (!empty($row['paese_nome'])) {
                    $viaggio_item["paesi"][] = [
                        "id"   => $row['paese_id'],
                        "nome" => $row['paese_nome']
                    ];
                }
            }

            http_response_code(200);
            echo json_encode($viaggio_item);
        } else {
            http_response_code(404);
            echo json_encode(["messaggio" => "Viaggio non trovato."]);
        }
    }

    // GET /viaggio (con o senza filtri)
    public function getAll($params) {
        $posti    = $params['posti']    ?? null;
        $paese_id = $params['paese_id'] ?? null;

        $stmt = $this->viaggio->read($paese_id, $posti);
        $num  = $stmt->rowCount();

        if ($num > 0) {
            $viaggi_arr = [];

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $vid = $row['id'];

                if (!isset($viaggi_arr[$vid])) {
                    $viaggi_arr[$vid] = [
                        "id"                => $vid,
                        "posti_disponibili" => $row['posti_disponibili'],
                        "paesi"             => []
                    ];
                }

                if (!empty($row['paese_nome'])) {
                    $viaggi_arr[$vid]["paesi"][] = [
                        "id"   => $row['paese_id'],
                        "nome" => $row['paese_nome']
                    ];
                }
            }

            http_response_code(200);
            echo json_encode(array_values($viaggi_arr));
        } else {
            http_response_code(404);
            echo json_encode(["messaggio" => "Nessun viaggio trovato."]);
        }
    }

    // POST /viaggio
    public function create($data) {
        if (!empty($data->posti_disponibili) && !empty($data->paesi_ids)) {
            $this->viaggio->posti_disponibili = $data->posti_disponibili;
            $this->viaggio->paesi_ids         = $data->paesi_ids;

            if ($this->viaggio->create()) {
                http_response_code(201);
                echo json_encode(["messaggio" => "Viaggio creato con successo."]);
            } else {
                http_response_code(503);
                echo json_encode(["messaggio" => "Impossibile creare il viaggio."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["messaggio" => "Dati incompleti."]);
        }
    }

    // PUT /viaggio/{id}
    public function update($id, $data) {
        if (!empty($id) && !empty($data->posti_disponibili)) {
            $this->viaggio->id                = $id;
            $this->viaggio->posti_disponibili = $data->posti_disponibili;
            $this->viaggio->paesi_ids         = $data->paesi_ids ?? [];

            if ($this->viaggio->update()) {
                http_response_code(200);
                echo json_encode(["messaggio" => "Viaggio aggiornato correttamente."]);
            } else {
                http_response_code(503);
                echo json_encode(["messaggio" => "Impossibile aggiornare il viaggio."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["messaggio" => "ID o dati mancanti."]);
        }
    }

    // DELETE /viaggio/{id}
    public function delete($id) {
        if (!empty($id)) {
            $this->viaggio->id = $id;

            if ($this->viaggio->delete()) {
                http_response_code(200);
                echo json_encode(["messaggio" => "Viaggio eliminato con successo."]);
            } else {
                http_response_code(503);
                echo json_encode(["messaggio" => "Impossibile eliminare il viaggio."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["messaggio" => "ID mancante."]);
        }
    }
}