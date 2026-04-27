<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT"); // Specifichiamo il metodo

include_once '../../config/database.php';
include_once '../../models/paese.php';

$database = new Database();
$db = $database->getConnection();
$paese = new Paese($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->nome)) {
    $paese->id = $data->id;
    $paese->nome = $data->nome;

    if($paese->update()) {
        http_response_code(200);
        echo json_encode(["messaggio" => "Paese aggiornato con successo."]);
    } else {
        http_response_code(503);
        echo json_encode(["messaggio" => "Impossibile aggiornare il paese."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["messaggio" => "Dati incompleti (servono id e nome)."]);
}
?>