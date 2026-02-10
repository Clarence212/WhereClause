<?php
include "db.php";   // db.php already starts session

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid item ID");

// Fetch item for display
$sql = "SELECT * FROM itemss WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows == 0) die("Item not found");
$item = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Claim Item</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>

<div class="form-box">
    <a href="index.php" class="close-btn">✖</a>

    <h2>Claim Item</h2>
    <p class="item-preview"><?= htmlspecialchars($item['description']) ?></p>

    <form action="claim_save.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="item_id" value="<?= $item['id'] ?>">

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
