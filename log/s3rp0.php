<?php
session_start();

// Verifica che l'utente sia loggato e che abbia il token
if (!isset($_SESSION['username']) || !isset($_SESSION['token'])) {
  header("Location: login.html");
  exit;
}

$username = $_SESSION['username'];
$encryptedToken = $_SESSION['token'];

// Decifrazione token
define('ENCRYPTION_METHOD', 'aes-256-cbc');
define('ENCRYPTION_KEY', 'BrBrPatapimPatatone');

function decryptToken($encrypted) {
  $parts = explode('::', base64_decode($encrypted));
  if (count($parts) !== 2) return 'Token non valido';
  return openssl_decrypt($parts[1], ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $parts[0]);
}

$decryptedToken = decryptToken($encryptedToken);
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cambia Password</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .login-container {
      background: white;
      padding: 3rem;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
      position: relative;
    }

    .login-container h2 {
      margin-bottom: 1rem;
      text-align: center;
      color: #333;
    }

    .debug-token {
      position: absolute;
      top: 10px;
      right: 10px;
      font-size: 0.8rem;
      background: #eaeaea;
      padding: 8px;
      border-radius: 6px;
      max-width: 80%;
      word-break: break-all;
      color: #222;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      color: #555;
      font-weight: bold;
    }

    input[type="password"] {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 1rem;
      box-sizing: border-box;
    }

    input:focus {
      border-color: #667eea;
      outline: none;
    }

    .btn-login {
      width: 100%;
      padding: 12px;
      margin-top: 0.5rem;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 6px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
      text-align: center;
      text-decoration: none;
      display: block;
    }

    .btn-login:hover {
      opacity: 0.9;
    }

    .message {
      margin-top: 1rem;
      text-align: center;
      font-size: 0.95rem;
      color: #d9534f;
    }

    .message.success {
      color: #5cb85c;
    }
  </style>
</head>
<body>

<div class="login-container">
  <h2>Cambia Password</h2>

  <div class="debug-token">
    <strong>Debug</strong><br>
    Utente: <?= htmlspecialchars($username) ?><br>
    Token: <?= htmlspecialchars($decryptedToken) ?>
  </div>

  <form id="changePasswordForm">
    <div class="form-group">
      <label for="newPassword">Nuova Password</label>
      <input type="password" id="newPassword" name="newPassword" required placeholder="Inserisci la nuova password">
    </div>
    <div class="form-group">
      <label for="confirmPassword">Ripeti Nuova Password</label>
      <input type="password" id="confirmPassword" name="confirmPassword" required placeholder="Ripeti la nuova password">
    </div>
    <button type="submit" class="btn-login">Cambia Password</button>
    <div id="message" class="message"></div>
  </form>  
</div>

<script>
  const form = document.getElementById('changePasswordForm');
  const messageDiv = document.getElementById('message');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(form);

    try {
      const response = await fetch('change_password.php', {
        method: 'POST',
        body: new URLSearchParams(formData)
      });

      const result = await response.json();

      if (result.status === 'success') {
        messageDiv.className = 'message success';
        messageDiv.textContent = result.message;
        setTimeout(() => {
          window.location.href = 'g4t3.html'; // Redirect alla pagina di login
        }, 1500);
      } else {
        messageDiv.className = 'message';
        messageDiv.textContent = result.message;
      }
    } catch (error) {
      messageDiv.className = 'message';
      messageDiv.textContent = 'Errore durante il cambio password.';
    }
  });
</script>

</body>
</html>
