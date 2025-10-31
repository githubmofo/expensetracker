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
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['contactName'] ?? '');
    $email  = trim($_POST['contactEmail'] ?? '');
    $msg    = trim($_POST['contactMessage'] ?? '');
    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $msg) {
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $email, $msg);
        if ($stmt->execute()) {
            $message = 'Thank you, your message has been sent.';
        } else {
            $message = 'Database error: Could not send message.';
        }
        $stmt->close();
    } else {
        $message = 'Please fill all fields correctly.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact - Expense Tracker</title>
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
        <h1>Contact Us</h1>
        <?php if ($message): ?>
            <p class="<?= strpos($message, 'Thank') === 0 ? 'success' : 'error' ?>"><?=htmlspecialchars($message)?></p>
        <?php endif; ?>
        <form method="POST">
            <input name="contactName" placeholder="Your Name" required>
            <input name="contactEmail" type="email" placeholder="Your Email" required>
            <textarea name="contactMessage" placeholder="Your Message" rows="5" required></textarea>
            <button type="submit">Send Message</button>
        </form>
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
