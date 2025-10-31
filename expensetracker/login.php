<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'expense_tracker_db');
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');
    if ($user && $pass) {
        $_SESSION['username'] = $user;
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
        header('Location: dashboard.php');
        exit;
    }
    $message = 'Enter valid credentials.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login</title>
  <style>
    html, body {
      height: 100%;
      min-height: 100vh;
      margin: 0;
    }
    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      flex-direction: column;
    }
    .container {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-box {
      background: #fff;
      box-shadow: 0 8px 32px rgba(102,126,234,0.15);
      border-radius: 14px;
      padding: 2.2rem 2rem 2rem 2rem;
      max-width: 400px;
      width: 100%;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      align-items: stretch;
    }
    h1 {
      text-align:center; margin-bottom:1.6rem; color:#393e54; font-size:2rem;
    }
    input, button {
      font-size: 1rem;
      padding: 0.85rem 1rem;
      border-radius: 8px;
      border: 1.5px solid #cbd5e0;
      margin-bottom: 1rem;
      width: 100%;
      box-sizing: border-box;
    }
    input:focus { border-color: #667eea; }
    button {
      background: linear-gradient(90deg,#667eea 20%,#764ba2 100%);
      color: #fff;
      border: none;
      font-weight: 600;
      box-shadow: 0 2px 7px rgba(102,126,234,0.13);
      cursor: pointer;
      transition: background .2s;
    }
    button:hover { background: #667eea;}
    .error {
      color:#e53e3e;
      background: #fee;
      padding: 0.7rem 1rem;
      border-radius: 8px;
      margin-bottom: 1rem;
      text-align:center;
    }
    .register-link {
      text-align:center;
      margin-top:1.1rem;
    }
    .register-link a { color: #667eea; text-decoration:none;}
    .register-link a:hover { text-decoration: underline;}
    .footer {
      width: 100%;
      background: linear-gradient(90deg, #667eea 30%, #764ba2 80%);
      color: #fff;
      padding: 1.15rem 0;
      border-radius: 0 0 16px 16px;
      text-align: center;
      font-size: 1.1em;
      margin-top:auto;
      letter-spacing:.2px;
      box-shadow: 0 -2px 12px rgba(102,126,234,0.09);
    }
    .footer-content {
      display: flex;
      justify-content: center;
      align-items: center;
      font-weight: 500;
    }
    .footer-content span {
      display: block;
    }
    @media(max-width:500px){
      .login-box {padding:1.1rem;}
    }
  </style>
</head>
<body>
    
  <div class="container">
    <div class="login-box">
      <h1>Login</h1>
      <?php if($message): ?><p class="error"><?=htmlspecialchars($message)?></p><?php endif; ?>
      <form method="POST">
        <input name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Login</button>
      </form>
      <div class="register-link">
        New user? <a href="register.php">Register here</a>
      </div>
    </div>
  </div>
 <footer>
  Logged in: <span id="datetime"></span>
</footer>

<script>
  function updateDateTime() {
    const now = new Date();
    document.getElementById('datetime').textContent = now.toLocaleString();
  }
  updateDateTime();
  setInterval(updateDateTime, 1000); // update every second
</script>

</body>
</html>
