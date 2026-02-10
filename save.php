<?php
include "db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit;
}

/* =========================
   Get POST data safely
========================= */
$type        = trim($_POST["type"] ?? "");
$description = trim($_POST["description"] ?? "");

/* =========================
   Validation
========================= */
if ($type === "" || $description === "") {
    die("All fields are required.");
}

/* =========================
   Image upload
========================= */
$imgPath = "";

if (!empty($_FILES["image"]["name"])) {

    if ($_FILES["image"]["error"] !== 0) {
        die("Image upload error.");
    }

    $targetDir = "uploads/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

    // basic allowed formats
    $allowed = ["jpg","jpeg","png","gif","webp"];
    if (!in_array($ext, $allowed)) {
        die("Invalid image type.");
    }

    $imgName = "item_" . time() . "." . $ext;
    $imgPath = $targetDir . $imgName;

    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $imgPath)) {
        die("Image upload failed.");
    }
}

/* =========================
   Insert into DB with status 'pending'
========================= */
$stmt = $conn->prepare("
    INSERT INTO itemss (user_id, type, description, image, status, created_at)
    VALUES (?, ?, ?, ?, 'pending', NOW())
");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// save uploader's user ID
$user_id = $_SESSION['user']['id'];

$stmt->bind_param("isss", $user_id, $type, $description, $imgPath);

if (!$stmt->execute()) {
    die("DB Error: " . $stmt->error);
}

/* =========================
   Redirect after save
========================= */
header("Location: index.php");
exit;
?>
