<?php
// Header obbligatori per le API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../models/paese.php';

$database = new Database();
$db = $database->getConnection();

$paese = new Paese($db);

// Chiamiamo il metodo read che abbiamo appena creato
$stmt = $paese->read();
$num = $stmt->rowCount();

if($num > 0) {
    $paesi_arr = [];
    
    // Cicliamo i risultati
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $paese_item = [
            "id" => $id,
            "nome" => $nome
        ];
        array_push($paesi_arr, $paese_item);
    }

    // Risposta 200 OK
    http_response_code(200);
    echo json_encode($paesi_arr);
} else {
    // Risposta 404 se non c'è nulla
    http_response_code(404);
    echo json_encode(["messaggio" => "Nessun paese trovato."]);
}
?>