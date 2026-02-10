<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') die("Access denied");

$item_id = $_GET['id'] ?? null;
if (!$item_id) die("No item ID provided");

$stmt = $conn->prepare("UPDATE itemss SET status='approved' WHERE id=?");
$stmt->bind_param("i",$item_id);
$stmt->execute();

header("Location: pending_items.php");
exit;
?>
