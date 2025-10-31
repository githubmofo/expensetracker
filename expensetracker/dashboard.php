<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'expense_tracker_db');
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit;
}
$user = $_SESSION['username'];
$loginTime = $_SESSION['login_time'] ?? date('Y-m-d H:i:s');

// Handle add expense from dashboard
$success = '';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expenseName'])) {
  $name = trim($_POST['expenseName']);
  $amt = trim($_POST['amount']);
  $cat = trim($_POST['category']);
  $date = trim($_POST['date']);
  
  if (!$name) $errors[] = 'Expense Name is required.';
  if (!$amt || !is_numeric($amt)) $errors[] = 'Valid Amount is required.';
  if (!$cat) $errors[] = 'Category is required.';
  if (!$date) $errors[] = 'Date is required.';

  if (!$errors) {
    $stmt = $conn->prepare("INSERT INTO expenses(username, amount, category, date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('sdss', $user, $amt, $cat, $date);
    if ($stmt->execute()) {
      $success = "Expense added!";
    } else {
      $errors[] = "Database error: Failed to add expense.";
    }
    $stmt->close();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .main-message { font-size: 1.3em; margin: 1.5rem 0 1rem; text-align: center;}
    .dash-card {
      max-width: 460px;
      margin: 2rem auto;
      background: #f7fafc;
      border-radius: 14px;
      padding: 2rem 1.5rem;
      box-shadow: 0 2px 16px rgba(102,126,234,0.07);
      text-align: center;
      font-size: 1rem;
      color: #4a5568;
    }
    .dash-user { color: #667eea; font-weight: bold; letter-spacing: 1px;}
    .add-expense-form {
      display: grid;
      gap: 1.05rem;
      margin: 1.3rem auto 0 auto;
      grid-template-columns: 1fr 1fr;
    }
    .add-expense-form input, .add-expense-form button {
      font-size: 1.05rem;
      padding: 0.73rem 1rem;
      border-radius: 8px;
      border: 1.5px solid #cbd5e0;
    }
    .add-expense-form input:focus { border-color: #667eea; outline: none; }
    .add-expense-form button {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: #fff;
      grid-column: 1 / -1;
      border: none;
      font-weight: 600;
      transition: background .2s, box-shadow .2s;
      margin-top: 0.6rem;
      box-shadow: 0 2px 12px rgba(102,126,234,0.13);
      cursor: pointer;
    }
    .add-expense-form button:hover { background:#667eea; }
    .msg { margin: 0.9rem auto 0.5rem auto; }
    .error { color: #e53e3e; background: #fed7d7; padding: 1rem 0.6rem; border-radius: 8px; margin-bottom: 1rem; }
    .success { color: #38a169; background: #c6f6d5; padding: 1rem 0.6rem; border-radius: 8px; margin-bottom: 1rem;}
    @media(max-width:600px){
      .add-expense-form {grid-template-columns: 1fr;}
      .dash-card { padding: 1rem 0.4rem;}
    }
  </style>
</head>
<body>
  <nav>
    <a href="dashboard.php">Dashboard</a> |
    <a href="view_expenses.php">My Expenses</a> |
    <a href="about.php">About</a> |
    <a href="contact.php">Contact</a> |
    <a href="logout.php">Logout (<?=htmlspecialchars($user)?>)</a>
  </nav>
  <div class="container">
    <h1>Dashboard</h1>
    <p class="main-message">
      Hello, <span class="dash-user"><?=htmlspecialchars($user)?></span>!
    </p>
    <div class="dash-card">
      <h2 style="margin-bottom:1rem;">Add New Expense</h2>
      <?php if ($errors): ?>
        <ul class="error msg"><?php foreach ($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach; ?></ul>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="success msg"><?=htmlspecialchars($success)?></div>
      <?php endif; ?>
      <form method="POST" class="add-expense-form" autocomplete="off">
        <input name="expenseName" placeholder="Expense Name" required />
        <input name="amount" type="number" step="0.01" min="0" placeholder="Amount (₹)" required />
        <input name="category" placeholder="Category" required />
        <input type="date" name="date" required />
        <button type="submit">Add Expense</button>
      </form>
      <p style="margin:1.5rem 0 0 0; color:#718096; font-size:1em;">Your session is secured. You only need to login once.</p>
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
  setInterval(updateDateTime, 1000); // update every second
</script>
</body>
</html>
