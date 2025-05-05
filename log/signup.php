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
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validazione dei dati
    if (empty($username) || empty($password)) {
        echo "Tutti i campi sono obbligatori";
        exit;
    }

    // Controllo se username esiste già
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    if (!$check_stmt) {
        die("Errore nella preparazione della query: " . $conn->error);
    }
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "Username già in uso. Scegline un altro.";
        $check_stmt->close();
        exit;
    }
    $check_stmt->close();

    // Inserimento della password in chiaro (si consiglia l'hashing in ambiente reale)
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    if (!$stmt) {
        die("Errore nella preparazione della query: " . $conn->error);
    }

    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        echo "Registrazione completata!";
    } else {
        echo "Errore durante la registrazione: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
