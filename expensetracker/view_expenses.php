<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'expense_tracker_db');
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['username'];
$errors  = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? '';
unset($_SESSION['errors'], $_SESSION['success']);

// Delete expense
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM expenses WHERE id=? AND username=?");
    $stmt->bind_param('is', $id, $user);
    $stmt->execute();
    $stmt->close();
    $_SESSION['success'] = 'Expense deleted.';
    header('Location: view_expenses.php');
    exit;
}

// Update expense
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $id = (int)$_POST['update_id'];
    $amt = trim($_POST['amount'] ?? '');
    $cat = trim($_POST['category'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $errors = [];
    if (!$amt || !is_numeric($amt)) $errors[] = 'Valid amount required.';
    if (!$cat) $errors[] = 'Category required.';
    if (!$date) $errors[] = 'Date required.';
    if ($errors) {
        $_SESSION['errors'] = $errors;
        $_SESSION['edit_id'] = $id;
        header('Location: view_expenses.php?edit=' . $id);
        exit;
    }
    $stmt = $conn->prepare("UPDATE expenses SET amount=?, category=?, date=? WHERE id=? AND username=?");
    $stmt->bind_param('dssis', $amt, $cat, $date, $id, $user);
    $stmt->execute();
    $stmt->close();
    $_SESSION['success'] = 'Expense updated!';
    header('Location: view_expenses.php');
    exit;
}

$edit_id = $_GET['edit'] ?? ($_SESSION['edit_id'] ?? null);
unset($_SESSION['edit_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>My Expenses</title>
  <style>
    body { font-family: Inter, sans-serif; background: #f0f4f8; color: #2d3748; }
    .container { max-width: 900px; margin: 2rem auto; background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.05); }
    nav {
      background: linear-gradient(90deg, #667eea 30%, #764ba2 80%);
      box-shadow: 0 6px 14px rgba(102,126,234,0.09);
      padding: 1.3rem 0; border-radius: 0 0 16px 16px; text-align: center;
    }
    nav a {
      display: inline-block; margin: 0 1.1rem; color: #fff; text-decoration: none;
      font-weight: 600; letter-spacing: 0.5px; padding: 0.6rem 1.2rem; border-radius: 8px;
      font-size: 1.15rem; transition: background .25s, color .2s, box-shadow .2s;
    }
    nav a:hover, nav a:focus { background: rgba(255,255,255,0.18); top:-2px; box-shadow:0 3px 12px rgba(102,126,234,0.09);}
    h1 { font-size: 2rem; text-align: center; margin-bottom: 1.5rem; }
    table { width: 100%; border-collapse: collapse; margin-top:1rem; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.06); }
    th, td { padding:0.75rem 1rem; border-bottom:1px solid #f7fafc; text-align:left; }
    thead { background: linear-gradient(135deg,#667eea 0%,#764ba2 100%); color:#fff; }
    .error { color:#e53e3e; background:#fed7d7; padding:1rem; border-radius:8px; margin-bottom:1rem; }
    .success { color:#38a169; background:#c6f6d5; padding:1rem; border-radius:8px; margin-bottom:1rem; }
    .btn { display:inline-block; padding:6px 16px; border-radius:6px; font-weight:600; font-size:0.9rem; color:#fff; text-decoration:none; cursor:pointer; transition:background-color .25s, box-shadow .2s; }
    .btn-update { background:#3b82f6; box-shadow:0 2px 10px rgba(59,130,246,0.4); }
    .btn-delete { background:#ef4444; box-shadow:0 2px 10px rgba(239,68,68,0.4); }
    .btn-update:hover { background:#2563eb; box-shadow:0 4px 16px rgba(37,99,235,0.6); }
    .btn-delete:hover { background:#dc2626; box-shadow:0 4px 16px rgba(220,38,38,0.6); }
    .footer {
      width:100%; background:linear-gradient(90deg,#667eea 30%,#764ba2 80%); color:#fff; padding:1.1rem 0;
      border-radius:0 0 16px 16px; text-align:center; font-size:1.1em; margin-top:2rem; box-shadow:0 -2px 12px rgba(102,126,234,0.08);
    }
    .footer-content { max-width:900px; margin:auto; letter-spacing:0.3px; font-weight:500; }
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
    <h1>My Expenses</h1>
    <?php if ($errors): ?><div class="error"><?=implode('<br>', array_map('htmlspecialchars', $errors))?></div><?php endif; ?>
    <?php if ($success): ?><div class="success"><?=htmlspecialchars($success)?></div><?php endif; ?>
    <table>
      <thead>
        <tr>
          <th>Amount</th>
          <th>Category</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $stmt = $conn->prepare("SELECT id, amount, category, date FROM expenses WHERE username=? ORDER BY date DESC");
        $stmt->bind_param('s', $user);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows):
          while ($r = $res->fetch_assoc()):
            if ($edit_id && $edit_id == $r['id']):
        ?>
        <tr>
          <form method="POST" style="display:contents">
            <td><input name="amount" value="<?=htmlspecialchars($r['amount'])?>" /></td>
            <td><input name="category" value="<?=htmlspecialchars($r['category'])?>" /></td>
            <td><input type="date" name="date" value="<?=htmlspecialchars($r['date'])?>" /></td>
            <td>
              <input type="hidden" name="update_id" value="<?= $r['id'] ?>" />
              <button type="submit" class="btn btn-update">Save</button>
              <a href="view_expenses.php" class="btn btn-delete">Cancel</a>
            </td>
          </form>
        </tr>
        <?php else: ?>
        <tr>
          <td><?=htmlspecialchars($r['amount'])?></td>
          <td><?=htmlspecialchars($r['category'])?></td>
          <td><?=htmlspecialchars($r['date'])?></td>
          <td>
            <a href="view_expenses.php?edit=<?= $r['id'] ?>" class="btn btn-update">Update</a>
            <a href="view_expenses.php?delete=<?= $r['id'] ?>" class="btn btn-delete" onclick="return confirm('Delete this expense?')">Delete</a>
          </td>
        </tr>
        <?php endif; endwhile; else: ?>
        <tr><td colspan="4" style="text-align:center;">No expenses yet.</td></tr>
        <?php endif; $stmt->close(); $conn->close(); ?>
      </tbody>
    </table>
  </div>
  <footer class="footer">
    <div class="footer-content">Logged in: <span id="datetime"></span></div>
  </footer>
  <script>
    function updateDateTime() {
      document.getElementById('datetime').textContent = new Date().toLocaleString();
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);
  </script>
</body>
</html>
