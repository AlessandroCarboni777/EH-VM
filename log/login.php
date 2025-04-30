<?php
// Visualizza errori per debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Dati connessione
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'cappu22');
define('DB_PASSWORD', 'ciao123');
define('DB_NAME', 'siteht');

// Connessione al database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verifica la connessione
if ($conn->connect_error) {
    respond('error', 'Connessione fallita: ' . $conn->connect_error);
}

// Funzione per rispondere in JSON
function respond($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

// Solo richieste POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validazione dei dati
    if (empty($username) || empty($password)) {
        respond('error', 'Tutti i campi sono obbligatori.');
    }

    // Preparazione query
    $stmt = $conn->prepare('SELECT password FROM users WHERE username = ?');
    if (!$stmt) {
        respond('error', 'Errore nella preparazione della query: ' . $conn->error);
    }

    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($storedPassword);
        $stmt->fetch();

        if ($password === $storedPassword) {
            respond('success', 'Login effettuato con successo.');
        } else {
            respond('error', 'Password errata.');
        }
    } else {
        respond('error', 'Utente non trovato.');
    }

    $stmt->close();
} else {
    respond('error', 'Metodo non consentito.');
}

$conn->close();
?>
