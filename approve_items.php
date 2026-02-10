<?php
include "db.php";

if($_SESSION['user']['role']!='admin') die("Access denied");

$result = $conn->query("SELECT * FROM itemss WHERE status='pending'");
?>
<a href="approve.php?id=<?= $row['id'] ?>">Approve</a>
