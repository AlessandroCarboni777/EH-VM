<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'modernadmin');
define('DB_PASSWORD', 'NuN728Xx2nCH');
define('DB_NAME', 'ModernShop2');

define('ENCRYPTION_METHOD', 'aes-256-cbc');
define('ENCRYPTION_KEY', 'BrBrPatapimPatatone');

$username = $_SESSION['username'] ?? null;
$encryptedToken = $_SESSION['token'] ?? null;

function decryptToken($encrypted) {
  $parts = explode('::', base64_decode($encrypted));
  if (count($parts) !== 2) return 'Invalid token';
  return openssl_decrypt($parts[1], ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $parts[0]);
}

$decryptedToken = $encryptedToken ? decryptToken($encryptedToken) : 'Token non disponibile';

function respond($status, $message, $debugUser = '', $debugToken = '') {
  echo json_encode([
    'status' => $status,
    'message' => $message,
    'debug_user' => $debugUser,
    'debug_token' => $debugToken
  ]);
  exit;
}

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
  respond('error', 'Connessione fallita: ' . $conn->connect_error, $username, $decryptedToken);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newPassword = trim($_POST['newPassword'] ?? '');
  $confirmPassword = trim($_POST['confirmPassword'] ?? '');

  if (!$username || $newPassword === '' || $confirmPassword === '') {
    respond('error', 'Tutti i campi sono obbligatori.', $username, $decryptedToken);
  }

  if ($newPassword !== $confirmPassword) {
    respond('error', 'Le nuove password non coincidono.', $username, $decryptedToken);
  }

  $stmt = $conn->prepare('SELECT id FROM users WHERE username = ?');
  $stmt->bind_param('s', $username);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows !== 1) {
    respond('error', 'Utente non trovato.', $username, $decryptedToken);
  }

  $stmt->close();

  $hashedPwd = password_hash($newPassword, PASSWORD_DEFAULT);
  $stmt = $conn->prepare('UPDATE users SET password = ? WHERE username = ?');
  $stmt->bind_param('ss', $hashedPwd, $username);

  if ($stmt->execute()) {
    respond('success', 'Password aggiornata con successo.', $username, $decryptedToken);
  } else {
    respond('error', 'Errore durante l\'aggiornamento della password.', $username, $decryptedToken);
  }

  $stmt->close();
} else {
  respond('error', 'Metodo non consentito.', $username, $decryptedToken);
}

$conn->close();
?>
