<?php
session_start();

// Redirezione se l'utente non è loggato o manca il token
if (!isset($_SESSION['username']) || !isset($_SESSION['token'])) {
  header("Location: login.html");
  exit;
}

$username = $_SESSION['username'];
$encryptedToken = $_SESSION['token'];

// Costanti di decifratura (devono combaciare con login.php)
define('ENCRYPTION_METHOD', 'aes-256-cbc');
define('ENCRYPTION_KEY', 'BrBrPatapimPatatone');

// Funzione per decifrare il token
function decryptToken($encrypted) {
  $parts = explode('::', base64_decode($encrypted));
  if (count($parts) !== 2) return 'Token non valido';
  $iv = $parts[0];
  $ciphertext = $parts[1];
  return openssl_decrypt($ciphertext, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, $iv);
}

$decryptedToken = decryptToken($encryptedToken);
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile Settings</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f0f2f5;
      display: flex;
      height: 100vh;
    }

    .container {
      display: flex;
      width: 100%;
    }

    .sidebar {
      width: 250px;
      background-color: #fff;
      border-right: 2px solid #ddd;
      padding: 40px 20px 20px 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .sidebar-header h3 {
      text-align: center;
      color: #333;
      font-size: 24px;
    }

    .sidebar-content {
      margin-top: 20px;
      flex-grow: 1;
    }

    .sidebar-button {
      display: block;
      padding: 10px 20px;
      margin-bottom: 10px;
      background-color: #667eea;
      color: #fff;
      text-align: center;
      text-decoration: none;
      border-radius: 6px;
      transition: background 0.3s;
    }

    .sidebar-button:hover {
      background-color: #556cd6;
    }

    .sidebar-footer {
      margin-top: 20px;
    }

    .main-content {
      flex: 1;
      padding: 40px;
      background-color: #fff;
      position: relative;
    }

    .main-content h1 {
      color: #333;
      font-size: 28px;
      margin-bottom: 20px;
    }

    .main-content p {
      font-size: 16px;
      color: #666;
    }

    .debug-token {
      position: absolute;
      top: 10px;
      right: 20px;
      background: #eaeaea;
      padding: 10px;
      font-size: 0.85rem;
      border-radius: 8px;
      box-shadow: 0 0 4px rgba(0,0,0,0.1);
      max-width: 40%;
      word-break: break-word;
    }

    .faq-section {
      margin-top: 40px;
    }

    .faq-section h2 {
      color: #333;
      font-size: 28px;
      margin-bottom: 20px;
      font-weight: bold;
    }

    .faq-question {
      background-color: #fff;
      color: #333;
      padding: 12px;
      font-size: 1rem;
      cursor: pointer;
      width: 100%;
      border: 2px solid #667eea;
      border-radius: 6px;
      margin-bottom: 10px;
      text-align: left;
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .faq-question:hover {
      background-color: #f1f1f1;
      border-color: #5b67cc;
    }

    .faq-question .arrow {
      transition: transform 0.3s ease;
    }

    .faq-question.active .arrow {
      transform: rotate(180deg);
    }

    .faq-answer {
      display: none;
      padding: 10px;
      background-color: #fff;
      color: #333;
      margin-top: 5px;
      border-left: 2px solid #667eea;
      border-radius: 6px;
      margin-bottom: 30px;
    }
  </style>
</head>
<body>
  <div class="container">
    <nav class="sidebar">
      <div class="sidebar-header">
        <h3>Settings</h3>
      </div>
      <div class="sidebar-content">
        <a href="b0r1ello.html" class="sidebar-button">Edit Profile</a>
        <a href="s3rp0.php" class="sidebar-button">Change Password</a>
      </div>
      <div class="sidebar-footer">
        <a href="../contact.html" class="sidebar-button">Having Trouble? Contact Us</a>
      </div>
    </nav>

    <div class="main-content">
      <h1>Profile Settings</h1>
      <p>Welcome, <?= htmlspecialchars($username) ?>. Update your profile or change your password here.</p>

      <div class="debug-token">
        <strong>Debug:</strong><br>
        User: <?= htmlspecialchars($username) ?><br>
        Token: <?= htmlspecialchars($decryptedToken) ?>
      </div>

      <div class="faq-section">
        <h2>Frequently Asked Questions</h2>

        <div class="faq-question">
          <span>How do I update my profile?</span>
          <span class="arrow">&#9660;</span>
        </div>
        <div class="faq-answer">
          <p>To update your profile, go to the 'Edit Profile' section on the left menu and make changes to your details.</p>
        </div>

        <div class="faq-question">
          <span>What do I do if I forget my password?</span>
          <span class="arrow">&#9660;</span>
        </div>
        <div class="faq-answer">
          <p>If you've forgotten your password, you can reset it by clicking on the 'Change Password' button in the settings.</p>
        </div>

        <div class="faq-question">
          <span>Can I delete my account?</span>
          <span class="arrow">&#9660;</span>
        </div>
        <div class="faq-answer">
          <p>Currently, account deletion is not supported. Please contact support for further assistance.</p>
        </div>

        <div class="faq-question">
          <span>How can I contact customer support?</span>
          <span class="arrow">&#9660;</span>
        </div>
        <div class="faq-answer">
          <p>You can reach customer support by clicking on 'Having Trouble? Contact Us' at the bottom of the left menu.</p>
        </div>

        <div class="faq-question">
          <span>Is there a way to change my email address?</span>
          <span class="arrow">&#9660;</span>
        </div>
        <div class="faq-answer">
          <p>Yes, you can change your email address by editing your profile settings.</p>
        </div>

        <div class="faq-question">
          <span>How do I report a bug?</span>
          <span class="arrow">&#9660;</span>
        </div>
        <div class="faq-answer">
          <p>If you encounter any bugs, please contact our support team via the 'Contact Us' option in the settings.</p>
        </div>

        <div class="faq-question">
          <span>How do I update my payment information?</span>
          <span class="arrow">&#9660;</span>
        </div>
        <div class="faq-answer">
          <p>To update your payment information, visit your account settings and follow the instructions under the 'Payment Settings' section.</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    const faqQuestions = document.querySelectorAll('.faq-question');
    faqQuestions.forEach(question => {
      question.addEventListener('click', () => {
        const answer = question.nextElementSibling;
        const isVisible = answer.style.display === 'block';
        document.querySelectorAll('.faq-answer').forEach(ans => ans.style.display = 'none');
        answer.style.display = isVisible ? 'none' : 'block';
        question.classList.toggle('active');
      });
    });
  </script>
</body>
</html>
