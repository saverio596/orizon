<?php
// Header obbligatori per le API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';
include_once '../../models/paese.php';

$database = new Database();
$db = $database->getConnection();

$paese = new Paese($db);

// Leggiamo i dati inviati in formato JSON
$data = json_decode(file_get_contents("php://input"));

// Controlliamo che il nome sia presente
if(!empty($data->nome)) {
    
    $paese->nome = $data->nome;

    if($paese->create()) {
        http_response_code(201); // Creato
        echo json_encode(["messaggio" => "Paese creato con successo."]);
    } else {
        http_response_code(503); // Servizio non disponibile
        echo json_encode(["messaggio" => "Impossibile creare il paese."]);
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(["messaggio" => "Dati incompleti. Inviare il campo 'nome'."]);
}
?>