<?php
// Mostra tutti gli errori
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Parametri di connessione
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'cappu22');
define('DB_PASSWORD', 'ciao123');
define('DB_NAME', 'siteht');

// Connessione al database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Verifica la connessione
if ($conn->connect_error) {
    die(json_encode([
        "status" => "error",
        "message" => "Connessione fallita: " . $conn->connect_error
    ]));
}

// Solo se POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validazione dei dati
    if (empty($email) || empty($password)) {
        echo "Tutti i campi sono obbligatori";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Formato email non valido";
        exit;
    }

    // Inserimento della password in chiaro
    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    if (!$stmt) {
        die("Errore nella preparazione della query: " . $conn->error);
    }

    $stmt->bind_param("ss", $email, $password);

    if ($stmt->execute()) {
        echo "Registrazione completata!";
    } else {
        echo "Errore durante la registrazione: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
