<?php
include "db.php";

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

/* =========================
   Get POST Data
========================= */
$item_id = $_POST['item_id'] ?? null;
$claimer_name = trim($_POST['user_name'] ?? '');
$claimer_number = trim($_POST['number'] ?? '');
$claimer_classroom = trim($_POST['classroom'] ?? '');

if (!$item_id || !$claimer_name || !$claimer_number || !$claimer_classroom) {
    die("All fields are required.");
}

/* =========================
   Handle Image Upload
========================= */
$claimer_image = "";

$uploadDir = "uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (isset($_FILES['id_image']) && $_FILES['id_image']['tmp_name']) {
    $ext = pathinfo($_FILES['id_image']['name'], PATHINFO_EXTENSION);
    $claimer_image = $uploadDir . "claimer_" . time() . "." . $ext;

    if (!move_uploaded_file($_FILES['id_image']['tmp_name'], $claimer_image)) {
        die("Failed to upload image");
    }
} else {
    die("ID image is required.");
}

/* =========================
   Fetch Item Info
========================= */
$stmtItem = $conn->prepare("SELECT description, type, image FROM itemss WHERE id = ?");
$stmtItem->bind_param("i", $item_id);
$stmtItem->execute();
$resultItem = $stmtItem->get_result();

if ($resultItem->num_rows == 0) {
    die("Item not found.");
}

$item = $resultItem->fetch_assoc();

/* =========================
   Insert Into claimss
========================= */
$stmt = $conn->prepare("
    INSERT INTO claimss 
    (item_id, item_description, item_type, item_image, 
     claimer_name, claimer_number, claimer_classroom, claimer_image, claimed_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
");

$stmt->bind_param(
    "isssssss",
    $item_id,
    $item['description'],
    $item['type'],
    $item['image'],
    $claimer_name,
    $claimer_number,
    $claimer_classroom,
    $claimer_image
);

if (!$stmt->execute()) {
    die("Claim save failed: " . $stmt->error);
}

/* =========================
   Delete Item From itemss
========================= */
$stmtDel = $conn->prepare("DELETE FROM itemss WHERE id = ?");
$stmtDel->bind_param("i", $item_id);
$stmtDel->execute();

/* =========================
   Redirect Back to Index
========================= */
header("Location: index.php");
exit;
?>
x`