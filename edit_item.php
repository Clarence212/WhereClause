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
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid item ID");
}

$id = intval($_GET['id']);

/* =========================
   Fetch item safely
========================= */
$stmt = $conn->prepare("
    SELECT id, description, type, image
    FROM itemss
    WHERE id = ?
");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Item not found");
}

$item = $result->fetch_assoc();

/* =========================
   OPTIONAL SECURITY
   (Will only work if you add these later)
========================= */
/*
if(isset($_SESSION['role']) && $_SESSION['role'] !== 'admin'){
    if(isset($item['user_id']) && $item['user_id'] != $_SESSION['user_id']){
        die("You are not allowed to edit this item.");
    }
}
*/
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
    <a href="index.php" class="close-btn">✕</a>
    <h2>Edit Item</h2>

    <form action="edit_save.php" method="post" enctype="multipart/form-data">

        <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">

        <label>Description:</label>
        <textarea name="description" required><?= htmlspecialchars($item['description']) ?></textarea>

        <label>Type:</label>
        <select name="type" required>
            <option value="electronic" <?= strtolower($item['type'])=="electronic"?"selected":"" ?>>Electronic</option>
            <option value="clothing" <?= strtolower($item['type'])=="clothing"?"selected":"" ?>>Clothing</option>
            <option value="money" <?= strtolower($item['type'])=="money"?"selected":"" ?>>Money</option>
            <option value="jewelry" <?= strtolower($item['type'])=="jewelry"?"selected":"" ?>>Jewelry</option>
            <option value="other" <?= strtolower($item['type'])=="other"?"selected":"" ?>>Other</option>
        </select>

        <label>Image:</label>
        <input type="file" name="image" accept="image/*">

        <img 
            id="previewImg"
            src="<?= htmlspecialchars($item['image']) ?>"
            style="display:<?= !empty($item['image']) ? 'block' : 'none' ?>;width:100px;margin-top:5px;"
        >

        <button type="submit">Save Changes</button>
    </form>
</div>

<script>
const fileInput = document.querySelector('input[name="image"]');
const preview = document.getElementById('previewImg');

fileInput.addEventListener('change', () => {
    const file = fileInput.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = () => {
        preview.src = reader.result;
        preview.style.display = "block";
    };
    reader.readAsDataURL(file);
});
</script>

</body>
</html>
