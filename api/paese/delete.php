<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");

include_once '../../config/database.php';
include_once '../../models/paese.php';

$database = new Database();
$db = $database->getConnection();
$paese = new Paese($db);

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id)) {
    $paese->id = $data->id;

    if($paese->delete()) {
        http_response_code(200);
        echo json_encode(["messaggio" => "Paese eliminato."]);
    } else {
        http_response_code(503);
        echo json_encode(["messaggio" => "Impossibile eliminare il paese."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["messaggio" => "Dati incompleti (serve l'id)."]);
}
?>