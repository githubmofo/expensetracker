<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'expense_tracker_db');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user  = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');
    $cpw   = trim($_POST['confirmPassword'] ?? '');
    if ($user && filter_var($email, FILTER_VALIDATE_EMAIL) && $pass && $pass === $cpw) {
        // Hash password
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO register (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $user, $email, $hashed_pass);

        if ($stmt->execute()) {
            $_SESSION['username'] = $user;
            $_SESSION['login_time'] = date('Y-m-d H:i:s');
            header('Location: dashboard.php');
            exit;
        } else {
            $message = 'Username or Email already exists.';
        }
        $stmt->close();
    } else {
        $message = 'Complete all fields correctly.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register</title>
  <style>
    html, body {
      height: 100%;
      min-height: 100vh;
      margin: 0;
    }
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .container {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .register-box {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 6px 32px rgba(102,126,234,0.13);
      padding: 2.3rem 2rem 1.5rem 2rem;
      max-width: 400px;
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      margin: 0 auto;
    }
    h1 { text-align: center; margin-bottom: 1.4rem; color: #393e54;}
    input, button {
      font-size: 1.07rem;
      padding: 0.88rem 1rem;
      border-radius: 8px;
      border: 1.5px solid #cbd5e0;
      margin-bottom: 1rem;
      box-sizing: border-box;
      width: 100%;
    }
    input:focus { border-color: #667eea; outline: none; }
    button {
      background: linear-gradient(90deg,#667eea 20%,#764ba2 100%);
      color: #fff;
      border: none;
      font-weight: 600;
      margin-bottom: 0.5rem;
      box-shadow: 0 2px 7px rgba(102,126,234,0.13);
      cursor: pointer;
      transition: background .2s;
    }
    button:hover { background: #667eea;}
    .error {
      color: #e53e3e;
      background: #fee;
      padding: 0.7rem 1rem;
      border-radius: 8px;
      margin-bottom: 1rem;
      text-align: center;
    }
    .footer {
      width: 100%;
      background: linear-gradient(90deg, #667eea 30%, #764ba2 80%);
      color: #fff;
      padding: 1.1rem 0 1.2rem 0;
      border-radius: 0 0 16px 16px;
      text-align: center;
      font-size: 1.09em;
      margin-top: auto;
      box-shadow: 0 -2px 12px rgba(102,126,234,0.08);
      letter-spacing:.2px;
    }
    .footer-content {
      display: flex;
      justify-content: center;
      align-items: center;
      font-weight: 500;
    }
    @media(max-width:500px){
      .container { min-height:300px;}
      .register-box { padding: 1.3rem;}
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="register-box">
      <h1>Register</h1>
      <?php if($message): ?><p class="error"><?=htmlspecialchars($message)?></p><?php endif; ?>
      <form method="POST">
        <input name="username" placeholder="Username" required />
        <input name="email" type="email" placeholder="Email" required />
        <input name="password" type="password" placeholder="Password" required />
        <input name="confirmPassword" type="password" placeholder="Confirm Password" required />
        <button type="submit">Register</button>
      </form>
      <p style="text-align:center; margin-top:1.3rem;">
        Already registered? <a href="login.php" style="color:#667eea;">Login here</a>
      </p>
    </div>
  </div>
  <footer class="footer">
    <div class="footer-content">
      <span>
        Logged in: <span id="datetime"></span>
      </span>
    </div>
  </footer>
  <script>
    function updateDateTime() {
      const now = new Date();
      document.getElementById('datetime').textContent = now.toLocaleString();
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);
  </script>
</body>
</html>
