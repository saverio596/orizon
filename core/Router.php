<?php

class Router {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function dispatch($resource, $id, $method) {

        // Gestione preflight CORS (richieste OPTIONS dei browser)
        if ($method === 'OPTIONS') {
            header("Access-Control-Allow-Headers: Content-Type, Authorization");
            http_response_code(200);
            exit();
        }

        switch ($resource) {
            case 'viaggio':
                $controller = new ViaggioController($this->db);
                $this->handleViaggio($controller, $id, $method);
                break;

            case 'paese':
                $controller = new PaeseController($this->db);
                $this->handlePaese($controller, $id, $method);
                break;

            default:
                http_response_code(404);
                echo json_encode(["messaggio" => "Risorsa non trovata."]);
                break;
        }
    }

    private function handleViaggio($controller, $id, $method) {
        switch ($method) {
            case 'GET':
                $id ? $controller->getOne($id) : $controller->getAll($_GET);
                break;
            case 'POST':
                $data = json_decode(file_get_contents("php://input"));
                if ($data === null) {
                    http_response_code(400);
                    echo json_encode(["messaggio" => "Body JSON non valido."]);
                    return;
                }
                $controller->create($data);
                break;
            case 'PUT':
                $data = json_decode(file_get_contents("php://input"));
                if ($data === null) {
                    http_response_code(400);
                    echo json_encode(["messaggio" => "Body JSON non valido."]);
                    return;
                }
                $controller->update($id, $data);
                break;
            case 'DELETE':
                $controller->delete($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(["messaggio" => "Metodo non consentito."]);
                break;
        }
    }

    private function handlePaese($controller, $id, $method) {
        switch ($method) {
            case 'GET':
                $controller->getAll();
                break;
            case 'POST':
                $data = json_decode(file_get_contents("php://input"));
                if ($data === null) {
                    http_response_code(400);
                    echo json_encode(["messaggio" => "Body JSON non valido."]);
                    return;
                }
                $controller->create($data);
                break;
            case 'PUT':
                $data = json_decode(file_get_contents("php://input"));
                if ($data === null) {
                    http_response_code(400);
                    echo json_encode(["messaggio" => "Body JSON non valido."]);
                    return;
                }
                $controller->update($id, $data);
                break;
            case 'DELETE':
                $controller->delete($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(["messaggio" => "Metodo non consentito."]);
                break;
        }
    }
}