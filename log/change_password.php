<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'cappu22');
define('DB_PASSWORD', 'ciao123');
define('DB_NAME', 'siteht');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    respond('error', 'Connessione fallita: ' . $conn->connect_error);
}

function respond($status, $message) {
    echo json_encode(['status' => $status, 'message' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $newPassword = trim($_POST['newPassword'] ?? '');
    $confirmPassword = trim($_POST['confirmPassword'] ?? '');

    if (empty($username) || empty($newPassword) || empty($confirmPassword)) {
        respond('error', 'Tutti i campi sono obbligatori.');
    }

    if ($newPassword !== $confirmPassword) {
        respond('error', 'Le nuove password non coincidono.');
    }

    $stmt = $conn->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows !== 1) {
        respond('error', 'Utente non trovato.');
    }

    $stmt->close();

    $stmt = $conn->prepare('UPDATE users SET password = ? WHERE username = ?');
    $stmt->bind_param('ss', $newPassword, $username);
    if ($stmt->execute()) {
        respond('success', 'Password aggiornata con successo.');
    } else {
        respond('error', 'Errore durante l\'aggiornamento della password.');
    }

    $stmt->close();
} else {
    respond('error', 'Metodo non consentito.');
}

$conn->close();
?>
