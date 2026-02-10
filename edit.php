<?php
include "db.php";

// Start session BEFORE any HTML or output
if (session_status() === PHP_SESSION_NONE) session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Get the item ID from URL
$id = $_GET['id'] ?? null;
if (!$id) {
    die("Invalid item ID");
}

// Fetch item from database
$sql = "SELECT * FROM items WHERE id=$id";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    die("Item not found");
}

$item = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Item</title>
<link rel="stylesheet" href="css/form.css">
</head>
<body>

<div class="form-box">
    <a href="index.php" class="close-btn">X</a>
    <h2>Edit Item</h2>
    <form action="edit_save.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $item['id'] ?>">

        <label>Founder:</label>
        <input type="text" name="founder" value="<?= htmlspecialchars($item['founder']) ?>" required>

        <label>Type:</label>
        <select name="type" required>
            <option <?= strtolower($item['type'])=="electronic"?"selected":"" ?>>Electronic</option>
            <option <?= strtolower($item['type'])=="clothing"?"selected":"" ?>>Clothing</option>
            <option <?= strtolower($item['type'])=="money"?"selected":"" ?>>Money</option>
            <option <?= strtolower($item['type'])=="jewelry"?"selected":"" ?>>Jewelry</option>
            <option <?= strtolower($item['type'])=="other"?"selected":"" ?>>Other</option>
        </select>

        <label>Description:</label>
        <textarea name="description" required><?= htmlspecialchars($item['description']) ?></textarea>

        <label>Image:</label>
        <input type="file" name="image">
        <?php if($item['image']): ?>
            <img src="<?= htmlspecialchars($item['image']) ?>" class="preview">
        <?php endif; ?>

        <button type="submit">Save Changes</button>
    </form>
</div>

</body>
</html>
