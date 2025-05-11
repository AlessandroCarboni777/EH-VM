<?php
const DB_SRV = 'localhost';
const DB_USER = 'cappu22';
const DB_PASS = 'ciao123';
const DB_NAME = 'siteht';

const TOK_SRV = 'localhost';
const TOK_USER = 'tokuser';
const TOK_PASS = 'Patapim77!';
const TOK_NAME = 'tokens';



error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

/* Token forgiabile */
function generateToken($id, $username, $is_admin, $role, $reg_date) {
  return hash('sha256', "$id:$username:$is_admin:$role:$reg_date");
}

/* Cifra il token con AES-256-CBC */
function encryptToken($token, $key) {
  $iv = random_bytes(16);
  $cipher = openssl_encrypt($token, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
  return base64_encode($iv . $cipher);  // IV + cifrato
}

/* connessioni */
$u = new mysqli(DB_SRV, DB_USER, DB_PASS, DB_NAME);
if ($u->connect_error){
  header('Content-Type: application/json');
  die(json_encode(['status'=>'error','message'=>'Conn. utenti: '.$u->connect_error]));
}

$t = new mysqli(TOK_SRV, TOK_USER, TOK_PASS, TOK_NAME);
if ($t->connect_error){
  header('Content-Type: application/json');
  die(json_encode(['status'=>'error','message'=>'Conn. token: '.$t->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
  header('Content-Type: application/json');
  exit(json_encode(['status'=>'error','message'=>'Metodo non consentito']));
}

/* input */
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
if ($username === '' || $password === '') {
  header('Content-Type: application/json');
  exit(json_encode(['status'=>'error','message'=>'Tutti i campi sono obbligatori']));
}

/* duplicati */
$dup = $u->prepare('SELECT id FROM users WHERE username=?');
$dup->bind_param('s', $username);
$dup->execute();
$dup->store_result();
if ($dup->num_rows) {
  header('Content-Type: application/json');
  exit(json_encode(['status'=>'error','message'=>'Username già in uso']));
}
$dup->close();

/* inserisci utente */
$is_admin = 0;
$role     = 'user';
$pwdHash  = password_hash($password, PASSWORD_DEFAULT);

$ins = $u->prepare('INSERT INTO users(username,password,is_admin,role,reg_date)
                    VALUES(?, ?, ?, ?, CURDATE())');
$ins->bind_param('ssis', $username, $pwdHash, $is_admin, $role);
if (!$ins->execute()) {
  header('Content-Type: application/json');
  exit(json_encode(['status'=>'error','message'=>'Insert user: '.$ins->error]));
}
const SECRET_KEY = 'CCMN-PatapimèPienoDiSlim'; 
$id       = $ins->insert_id;
$reg_date = date('Y-m-d');
$ins->close();

/* genera token, poi cifra */
$token      = generateToken($id, $username, $is_admin, $role, $reg_date);
$encrypted  = encryptToken($token, SECRET_KEY);

/* salva token cifrato */
$tok = $t->prepare('INSERT INTO user_tokens(username,token)
                    VALUES(?,?) ON DUPLICATE KEY UPDATE token=VALUES(token)');
$tok->bind_param('ss', $username, $encrypted);
if (!$tok->execute()) {
  header('Content-Type: application/json');
  exit(json_encode(['status'=>'error','message'=>'Insert token: '.$tok->error]));
}
$tok->close();

/* risposta */
$_SESSION['username']    = $username;
$_SESSION['csrf_token']  = $token; // token in chiaro nella sessione

header('Content-Type: application/json');
echo json_encode([
  'status'  => 'success',
  'message' => 'Registrazione completata con successo!',
  'token'   => $token  // mostra il token in chiaro, come richiesto
]);
?>
