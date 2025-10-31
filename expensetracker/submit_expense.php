<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'expense_tracker_db');
if (!isset($_SESSION['username'])) {
  header('Location: login.php');
  exit;
}
$user = $_SESSION['username'];
$id   = $_POST['id'] ?? null;
$name = trim($_POST['expenseName'] ?? '');
$amt  = trim($_POST['amount'] ?? '');
$cat  = trim($_POST['category'] ?? '');
$date = trim($_POST['date'] ?? '');
$errors = [];
if (!$name) $errors[]='Name required.';
if (!$amt||!is_numeric($amt)) $errors[]='Valid amount required.';
if (!$cat) $errors[]='Category required.';
if (!$date) $errors[]='Date required.';
if ($errors) {
  $_SESSION['errors']=$errors;
  header('Location: view_expenses.php');
  exit;
}
$conn = new mysqli('localhost','root','','expense_tracker_db');
if ($id) {
  $stmt = $conn->prepare(
    "UPDATE expenses SET expense_name=?, amount=?, category=?, date=? WHERE id=? AND username=?"
  );
  $stmt->bind_param('sdssis',$name,$amt,$cat,$date,$id,$user);
} else {
  $stmt = $conn->prepare(
    "INSERT INTO expenses(username,expense_name,amount,category,date) VALUES(?,?,?,?,?)"
  );
  $stmt->bind_param('ssdss',$user,$name,$amt,$cat,$date);
}
$stmt->execute();
$stmt->close();
$conn->close();
$_SESSION['success'] = $id ? 'Expense updated!' : 'Expense added!';
header('Location: view_expenses.php');
exit;




 
 