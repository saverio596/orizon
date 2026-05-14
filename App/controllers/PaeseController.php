<?php
class PaeseController {
    private $db;
    private $paese;

    public function __construct($db) {
        $this->db = $db;
        require_once __DIR__ . '/../models/paese.php';
        $this->paese = new Paese($db);
    }

    // GET /paese
    public function getAll() {
        $stmt = $this->paese->read();
        $num = $stmt->rowCount();

        if($num > 0) {
            $paesi_arr = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $paese_item = [
                    "id" => $row['id'],
                    "nome" => $row['nome']
                ];
                array_push($paesi_arr, $paese_item);
            }
            http_response_code(200);
            echo json_encode($paesi_arr);
        } else {
            http_response_code(404);
            echo json_encode(["messaggio" => "Nessun paese trovato."]);
        }
    }

    // POST /paese
    public function create($data) {
        if(!empty($data->nome)) {
            $this->paese->nome = $data->nome;
            if($this->paese->create()) {
                http_response_code(201);
                echo json_encode(["messaggio" => "Paese creato."]);
            } else {
                http_response_code(503);
                echo json_encode(["messaggio" => "Errore nella creazione."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["messaggio" => "Nome mancante."]);
        }
    }

    // PUT /paese/{id}
    public function update($id, $data) {
        if(!empty($id) && !empty($data->nome)) {
            $this->paese->id = $id;
            $this->paese->nome = $data->nome;
            if($this->paese->update()) {
                http_response_code(200);
                echo json_encode(["messaggio" => "Paese aggiornato."]);
            } else {
                http_response_code(503);
                echo json_encode(["messaggio" => "Errore nell'aggiornamento."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["messaggio" => "Dati insufficienti."]);
        }
    }

    // DELETE /paese/{id}
    public function delete($id) {
        if(!empty($id)) {
            $this->paese->id = $id;
            if($this->paese->delete()) {
                http_response_code(200);
                echo json_encode(["messaggio" => "Paese eliminato."]);
            } else {
                http_response_code(503);
                echo json_encode(["messaggio" => "Errore nell'eliminazione."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["messaggio" => "ID mancante."]);
        }
    }
}