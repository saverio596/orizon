<?php

class Router {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function dispatch($resource, $id, $method) {
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
                echo json_encode(["messaggio" => "Risorsa non trovata"]);
                break;
        }
    }

    private function handleViaggio($controller, $id, $method) {
        switch ($method) {
            case 'GET':
                $id ? $controller->getOne($id) : $controller->getAll($_GET);
                break;
            case 'POST':
                $controller->create(json_decode(file_get_contents("php://input")));
                break;
            case 'PUT':
                $controller->update($id, json_decode(file_get_contents("php://input")));
                break;
            case 'DELETE':
                $controller->delete($id);
                break;
        }
    }

    private function handlePaese($controller, $id, $method) {
        switch ($method) {
            case 'GET':
                $controller->getAll();
                break;
            case 'POST':
                $controller->create(json_decode(file_get_contents("php://input")));
                break;
            case 'PUT':
                $controller->update($id, json_decode(file_get_contents("php://input")));
                break;
            case 'DELETE':
                $controller->delete($id);
                break;
        }
    }
}