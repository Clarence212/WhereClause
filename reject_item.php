<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') die("Access denied");

$item_id = $_GET['id'] ?? null;
if (!$item_id) die("No item ID provided");

// Option 1: Delete rejected item
$stmt = $conn->prepare("DELETE FROM itemss WHERE id=?");
$stmt->bind_param("i",$item_id);
$stmt->execute();

header("Location: pending_items.php");
exit;
?>
