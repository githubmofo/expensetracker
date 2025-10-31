<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'expense_tracker_db');
if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit;
}
$user = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>About - Expense Tracker</title>
  <link rel="stylesheet" href="style.css">
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
    <h1>About Us</h1>
    <p>Expense Tracker helps you manage your finances with ease. Track, categorize, and analyze your expenses in one place.</p>
    <p>Built for simplicity and security, it requires just one login per session to keep you focused on what matters—your money.</p>
    <h2>Why Choose Expense Tracker?</h2>
    <p>With Expense Tracker, you get:</p>
    <ul>
      <li><strong>Intuitive Dashboard:</strong> A clean, customizable overview of your spending patterns by category, date, and payment method.</li>
      <li><strong>Real-Time Insights:</strong> Interactive charts and graphs update instantly as you log new transactions, so you always know where your money goes.</li>
      <li><strong>Multiple Categories & Tags:</strong> Organize expenses with unlimited categories and tags to spot trends and cut unnecessary costs.</li>
      <li><strong>Secure Data Storage:</strong> All records are encrypted in transit and at rest, ensuring your financial data remains private and protected.</li>
      <li><strong>Mobile-Friendly:</strong> Fully responsive design allows you to log expenses on the go, from any device or screen size.</li>
      <li><strong>Bulk Import & Export:</strong> Import CSV bank statements or export your data for backups, tax time, or further analysis in spreadsheet tools.</li>
    </ul>
    <h2>Our Mission</h2>
    <p>We believe financial clarity is the first step toward better money decisions. Our mission is to empower you with insightful tools that remove complexity and help you build healthier spending habits.</p>
    <h2>Future Roadmap</h2>
    <p>Coming soon:</p>
    <ul>
      <li>Automated bank sync for seamless transaction import</li>
      <li>Budget alerts and customizable spending limits</li>
      <li>Shared family accounts with role-based access</li>
      <li>Intelligent expense categorization powered by machine learning</li>
    </ul>
    <p>Thank you for choosing Expense Tracker. We’re here to make budgeting effortless and insightful, every step of the way.</p>
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
