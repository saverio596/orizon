<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT"); // Specifichiamo il metodo

include_once '../../config/database.php';
include_once '../../models/viaggio.php';

$database = new Database();
$db = $database->getConnection();
$viaggio = new Viaggio($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->posti_disponibili) && !empty($data->paesi_ids)) {
    $viaggio->id = $data->id;
    $viaggio->posti_disponibili = $data->posti_disponibili;
    $viaggio->paesi_ids = $data->paesi_ids;

    if($viaggio->update()) {
        http_response_code(200);
        echo json_encode(["messaggio" => "Viaggio aggiornato correttamente."]);
    } else {
        http_response_code(503);
        echo json_encode(["messaggio" => "Impossibile aggiornare il viaggio."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["messaggio" => "Dati incompleti."]);
}
?>