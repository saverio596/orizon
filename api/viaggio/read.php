<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/database.php';
include_once '../../models/viaggio.php';

$database = new Database();
$db = $database->getConnection();
$viaggio = new Viaggio($db);

// Leggiamo i filtri dalla URL (es. read.php?paese_id=1)
$paese_id = isset($_GET['paese_id']) ? $_GET['paese_id'] : null;
$posti = isset($_GET['posti']) ? $_GET['posti'] : null;

$stmt = $viaggio->read($paese_id, $posti);
$num = $stmt->rowCount();

if($num > 0) {
    $viaggi_arr = array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        
        // Se è la prima volta che incontriamo questo ID viaggio, creiamo la struttura
        if(!isset($viaggi_arr[$id])) {
            $viaggi_arr[$id] = [
                "id" => $id,
                "posti_disponibili" => $posti_disponibili,
                "paesi" => []
            ];
        }
        
        // Aggiungiamo il paese all'array solo se esiste (non è null)
        if(!empty($paese_nome)) {
            $viaggi_arr[$id]["paesi"][] = [
                "id" => $paese_id,
                "nome" => $paese_nome
            ];
        }
    }

    http_response_code(200);
    // Usiamo array_values per resettare le chiavi numeriche dell'array
    echo json_encode(array_values($viaggi_arr));
} else {
    http_response_code(404);
    echo json_encode(["messaggio" => "Nessun viaggio trovato."]);
}
?>