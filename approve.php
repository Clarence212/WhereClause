<?php
include "db.php";

if($_SESSION['user']['role']!='admin') die("Access denied");

$id = intval($_GET['id']);

$stmt = $conn->prepare("
UPDATE itemss 
SET status='approved' 
WHERE id=?
");

$stmt->bind_param("i",$id);
$stmt->execute();

header("Location: approve_items.php");
?>