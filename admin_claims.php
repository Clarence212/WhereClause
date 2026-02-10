<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$search = $_GET['search'] ?? "";

$conditions = [];
if ($search) {
    $searchEscaped = $conn->real_escape_string($search);
    $conditions[] = "user_name LIKE '%$searchEscaped%'";
}

$sql = "SELECT * FROM claims";
if ($conditions) $sql .= " WHERE " . implode(" AND ", $conditions);
$sql .= " ORDER BY claimed_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Claim History</title>
<link rel="stylesheet" href="css/main.css">
</head>
<body>

<h1>Claim History</h1>

<form method="get" class="filter-form">
    <input type="text" name="search" placeholder="Search User Name..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>

<table>
    <thead>
        <tr>
            <th>User Name</th>
            <th>Section</th>
            <th>Number</th>
            <th>ID Picture</th>
            <th>Item</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['user_name']) ?></td>
            <td><?= htmlspecialchars($row['section']) ?></td>
            <td><?= htmlspecialchars($row['number']) ?></td>
            <td>
                <?php if($row['id_image']): ?>
                    <img src="<?= htmlspecialchars($row['id_image']) ?>" width="80" onclick="zoomImage(this)">
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['item_name']) ?></td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">No claims found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<script>
function zoomImage(img) {
    const modal = document.createElement('div');
    modal.style.position = 'fixed';
    modal.style.top = 0; modal.style.left = 0;
    modal.style.width = '100%'; modal.style.height = '100%';
    modal.style.background = 'rgba(0,0,0,0.8)';
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    modal.style.cursor = 'zoom-out';
    modal.onclick = () => modal.remove();

    const zoomed = document.createElement('img');
    zoomed.src = img.src;
    zoomed.style.maxWidth = '90%';
    zoomed.style.maxHeight = '90%';
    modal.appendChild(zoomed);
    document.body.appendChild(modal);
}
</script>

</body>
</html>
