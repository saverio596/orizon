<?php
// Header obbligatori per le API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once '../../config/database.php';
include_once '../../models/viaggio.php';

$database = new Database();
$db = $database->getConnection();

$viaggio = new Viaggio($db);

// Leggiamo i dati inviati in formato JSON
$data = json_decode(file_get_contents("php://input"));

// Verifichiamo che ci siano i posti e almeno un paese
if(!empty($data->posti_disponibili) && !empty($data->paesi_ids) && is_array($data->paesi_ids)) {
    
    $viaggio->posti_disponibili = $data->posti_disponibili;
    $viaggio->paesi_ids = $data->paesi_ids;

    if($viaggio->create()) {
        http_response_code(201); // Creato
        echo json_encode(["messaggio" => "Viaggio creato con successo."]);
    } else {
        http_response_code(503); // Servizio non disponibile
        echo json_encode(["messaggio" => "Impossibile creare il viaggio."]);
    }
} else {
    http_response_code(400); // Bad Request
    echo json_encode(["messaggio" => "Dati incompleti. Fornire posti e un array di ID paesi."]);
}
?>