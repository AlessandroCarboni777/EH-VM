<?php
const DB_SRV = 'localhost';
const DB_USER = 'modernadmin';
const DB_PASS = 'NuN728Xx2nCH';
const DB_NAME = 'ModernShop2';

const TOK_SRV  = 'localhost';
const TOK_USER = 'tokuser';
const TOK_PASS = 'Patapim77!';
const TOK_NAME = 'tokens';

const SECRET_KEY = 'CCMN-PatapimèPienoDiSlim';      // per decriptare dal DB
const SESSION_KEY = 'BrBrPatapimPatatone';          // per criptare nella sessione

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

/* genera token identico a signup.php */
function generateToken($id, $username, $is_admin, $role, $reg_date) {
  return hash('sha256', "$id:$username:$is_admin:$role:$reg_date");
}

/* decifra token salvato nel DB */
function decryptToken($ciphered, $key) {
  $raw = base64_decode($ciphered);
  if ($raw === false || strlen($raw) < 17) return false;

  $iv = substr($raw, 0, 16);
  $cipher = substr($raw, 16);

  return openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
}

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  exit(json_encode(['status' => 'error', 'message' => 'Metodo non consentito']));
}

/* connessione DB utenti */
$u = new mysqli(DB_SRV, DB_USER, DB_PASS, DB_NAME);
if ($u->connect_error)
  exit(json_encode(['status' => 'error', 'message' => 'Connessione utenti: '.$u->connect_error]));

/* connessione DB token */
$t = new mysqli(TOK_SRV, TOK_USER, TOK_PASS, TOK_NAME);
if ($t->connect_error)
  exit(json_encode(['status' => 'error', 'message' => 'Connessione token: '.$t->connect_error]));

/* input */
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
if ($username === '' || $password === '') {
  exit(json_encode(['status' => 'error', 'message' => 'Tutti i campi sono obbligatori']));
}

/* cerca utente */
$s = $u->prepare('SELECT id, password, is_admin, role, reg_date FROM users WHERE username=?');
$s->bind_param('s', $username);
$s->execute();
$s->store_result();
if ($s->num_rows !== 1)
  exit(json_encode(['status' => 'error', 'message' => 'Utente non trovato']));
$s->bind_result($id, $pwdHash, $is_admin, $role, $reg_date);
$s->fetch();

/* verifica password */
$pwdOk = password_verify($password, $pwdHash) || ($password === $pwdHash);
if (!$pwdOk)
  exit(json_encode(['status' => 'error', 'message' => 'Password errata']));

/* genera token */
$newTok = generateToken($id, $username, $is_admin, $role, $reg_date);

/* recupera token cifrato dal DB */
$tq = $t->prepare('SELECT token FROM user_tokens WHERE username=?');
$tq->bind_param('s', $username);
$tq->execute();
$tq->store_result();
if ($tq->num_rows !== 1)
  exit(json_encode(['status' => 'error', 'message' => 'Token non trovato per l’utente']));
$tq->bind_result($savedCiphered); 
$tq->fetch();

/* confronta token decifrato con quello generato */
$decrypted = decryptToken($savedCiphered, SECRET_KEY);
if ($decrypted === false || $decrypted !== $newTok)
  exit(json_encode(['status' => 'error', 'message' => 'Token non corrispondente']));

/* cifra il token per sessione */
$iv = openssl_random_pseudo_bytes(16);
$cipher = openssl_encrypt($newTok, 'aes-256-cbc', SESSION_KEY, 0, $iv);
$encrypted_token = base64_encode($iv . '::' . $cipher);

/* salva dati in sessione */
$_SESSION['username']    = $username;
$_SESSION['is_admin']    = $is_admin;
$_SESSION['role']        = $role;
$_SESSION['token']       = $encrypted_token;
$_SESSION['csrf_token']  = $newTok;

echo json_encode([
  'status'  => 'success',
  'message' => 'Login effettuato con successo.',
  'token'   => $newTok
]);
?>
