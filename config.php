<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'modernadmin');
define('DB_PASSWORD', 'NuN728Xx2nCH'); 
define('DB_NAME', 'ModernShop2');

// Crea una connessione
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verifica la connessione
if ($conn->connect_error) {
    // In caso di errore, restituisce un messaggio di errore
    echo json_encode([
        'status' => 'error',
        'message' => 'Connessione fallita: ' . $conn->connect_error
    ]);
    exit;  // Ferma l'esecuzione dello script dopo l'errore
} else {
    // In caso di successo, restituisce un messaggio di successo
    echo json_encode([
        'status' => 'success',
        'message' => 'Connessione al database riuscita con successo!'
    ]);
}

// Chiusura della connessione
$conn->close();
?>
