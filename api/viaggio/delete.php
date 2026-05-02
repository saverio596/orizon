<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");

include_once '../../config/database.php';
include_once '../../models/viaggio.php';

$database = new Database();
$db = $database->getConnection();
$viaggio = new Viaggio($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id)) {
    $viaggio->id = $data->id;

    if($viaggio->delete()) {
        http_response_code(200);
        echo json_encode(["messaggio" => "Viaggio eliminato con successo."]);
    } else {
        http_response_code(503);
        echo json_encode(["messaggio" => "Impossibile eliminare il viaggio."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["messaggio" => "Dati incompleti. Fornire l'id del viaggio."]);
}
?>