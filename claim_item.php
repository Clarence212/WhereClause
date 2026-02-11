<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "db.php";

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$item_id = $_GET['id'] ?? null;
if (!$item_id) die("No item specified");

// Fetch item safely
$stmt = $conn->prepare("SELECT * FROM itemss WHERE id=? AND status='approved'");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
if (!$item) die("Item not found or already claimed.");

// Handle POST (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_name = trim($_POST['user_name'] ?? '');
    $number    = trim($_POST['number'] ?? '');
    $classroom = trim($_POST['classroom'] ?? '');

    if (empty($user_name) || empty($number) || empty($classroom)) {
        $error = "All fields are required.";
    } else {

        // Handle ID image upload
        $id_image_path = "";
        if (!empty($_FILES["id_image"]["name"])) {
            $targetDir = "uploads/claims/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

            $ext = strtolower(pathinfo($_FILES["id_image"]["name"], PATHINFO_EXTENSION));
            $allowed = ["jpg","jpeg","png","gif","webp"];
            if (!in_array($ext, $allowed)) die("Invalid image type.");

            $id_image_name = "claim_" . time() . "." . $ext;
            $id_image_path = $targetDir . $id_image_name;

            if (!move_uploaded_file($_FILES["id_image"]["tmp_name"], $id_image_path)) {
                die("Failed to upload ID image.");
            }
        }

        // Insert into claims table
        $stmt_claim = $conn->prepare("
            INSERT INTO claims (item_id, claimed_by, user_name, number, classroom, id_image, claimed_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $user_id = $_SESSION['user']['id'];
        $stmt_claim->bind_param("iissss", $item_id, $user_id, $user_name, $number, $classroom, $id_image_path);

        if (!$stmt_claim->execute()) {
            die("Failed to save claim: " . $stmt_claim->error);
        }

        // Update item as claimed
        $stmt_update = $conn->prepare("UPDATE itemss SET status='claimed' WHERE id=?");
        $stmt_update->bind_param("i", $item_id);
        $stmt_update->execute();

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Claim Item</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="form-box">
    <a href="index.php" class="close-btn">✖</a>

    <h2>Claim Item</h2>

    <?php if(!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <p class="item-preview"><?= htmlspecialchars($item['description']) ?></p>

    <form method="post" enctype="multipart/form-data">
        <label>Name:</label>
        <input type="text" name="user_name" required>

        <label>Number:</label>
        <input type="text" name="number" required>

        <label>Classroom:</label>
        <input type="text" name="classroom" required>

        <label>ID Picture:</label>
        <input type="file" name="id_image" required>

        <button type="submit">Submit Claim</button>
    </form>
</div>

</body>
</html>
