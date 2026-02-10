<?php
include "db.php";

/* =========================
   Start session safely
========================= */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================
   Check login
========================= */
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

/* =========================
   Validate ID
========================= */
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    die("Invalid item ID");
}

$item_id = intval($_POST['id']);

/* =========================
   Validate fields
========================= */
$description = trim($_POST['description'] ?? '');
$type        = trim($_POST['type'] ?? '');

if ($description === '' || $type === '') {
    die("All fields are required.");
}

/* =========================
   Check if item exists
========================= */
$check = $conn->prepare("SELECT id FROM itemss WHERE id=?");
$check->bind_param("i", $item_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows === 0) {
    die("Item not found");
}

/* =========================
   OPTIONAL SECURITY
========================= */
/*
if(isset($_SESSION['role']) && $_SESSION['role'] !== 'admin'){
    if(isset($_SESSION['user_id'])){
        $ownerCheck = $conn->prepare("SELECT user_id FROM itemss WHERE id=?");
        $ownerCheck->bind_param("i",$item_id);
        $ownerCheck->execute();
        $owner = $ownerCheck->get_result()->fetch_assoc();

        if($owner['user_id'] != $_SESSION['user_id']){
            die("You cannot edit this item.");
        }
    }
}
*/

/* =========================
   Image Upload
========================= */
$image = "";

if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {

    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image = $targetDir . "item_" . time() . "." . $ext;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $image)) {
        die("Image upload failed");
    }
}

/* =========================
   Update Query
========================= */
if ($image !== "") {

    $stmt = $conn->prepare("
        UPDATE itemss
        SET description=?, type=?, image=?, created_at=NOW()
        WHERE id=?
    ");

    if(!$stmt) die("Prepare failed: ".$conn->error);

    $stmt->bind_param("sssi", $description, $type, $image, $item_id);

} else {

    $stmt = $conn->prepare("
        UPDATE itemss
        SET description=?, type=?, created_at=NOW()
        WHERE id=?
    ");

    if(!$stmt) die("Prepare failed: ".$conn->error);

    $stmt->bind_param("ssi", $description, $type, $item_id);
}

/* =========================
   Execute
========================= */
if ($stmt->execute()) {

    header("Location: index.php");
    exit;

} else {

    die("Update failed: " . $stmt->error);
}
?>
