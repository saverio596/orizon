<?php
class Router {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function dispatch($resource, $id, $method) {

        // Handle CORS preflight requests
        if ($method === 'OPTIONS') {
            header("Access-Control-Allow-Headers: Content-Type, Authorization");
            http_response_code(200);
            exit();
        }

        switch ($resource) {
            case 'trip':
                $controller = new TripController($this->db);
                $this->handleTrip($controller, $id, $method);
                break;

            case 'country':
                $controller = new CountryController($this->db);
                $this->handleCountry($controller, $id, $method);
                break;

            default:
                http_response_code(404);
                echo json_encode(["message" => "Resource not found."]);
                break;
        }
    }

    private function handleTrip($controller, $id, $method) {
        switch ($method) {
            case 'GET':
                $id ? $controller->getOne($id) : $controller->getAll($_GET);
                break;
            case 'POST':
                $data = json_decode(file_get_contents("php://input"));
                if ($data === null) {
                    http_response_code(400);
                    echo json_encode(["message" => "Invalid JSON body."]);
                    return;
                }
                $controller->create($data);
                break;
            case 'PUT':
                $data = json_decode(file_get_contents("php://input"));
                if ($data === null) {
                    http_response_code(400);
                    echo json_encode(["message" => "Invalid JSON body."]);
                    return;
                }
                $controller->update($id, $data);
                break;
            case 'DELETE':
                $controller->delete($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(["message" => "Method not allowed."]);
                break;
        }
    }

    private function handleCountry($controller, $id, $method) {
        switch ($method) {
            case 'GET':
                $controller->getAll();
                break;
            case 'POST':
                $data = json_decode(file_get_contents("php://input"));
                if ($data === null) {
                    http_response_code(400);
                    echo json_encode(["message" => "Invalid JSON body."]);
                    return;
                }
                $controller->create($data);
                break;
            case 'PUT':
                $data = json_decode(file_get_contents("php://input"));
                if ($data === null) {
                    http_response_code(400);
                    echo json_encode(["message" => "Invalid JSON body."]);
                    return;
                }
                $controller->update($id, $data);
                break;
            case 'DELETE':
                $controller->delete($id);
                break;
            default:
                http_response_code(405);
                echo json_encode(["message" => "Method not allowed."]);
                break;
        }
    }
}