<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Filters
$search = $_GET['search'] ?? "";
$type   = $_GET['type'] ?? "";

// Pagination
$itemsPerPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $itemsPerPage;

// Build SQL
$conditions = [];

if ($search) {
    $searchEscaped = $conn->real_escape_string($search);
    $conditions[] = "description LIKE '%$searchEscaped%'";
}

if ($type) {
    $typeEscaped = $conn->real_escape_string($type);
    $conditions[] = "LOWER(type) = LOWER('$typeEscaped')";
}

$sql = "SELECT * FROM items";
if ($conditions) $sql .= " WHERE " . implode(" AND ", $conditions);
$sql .= " ORDER BY created_at DESC LIMIT $itemsPerPage OFFSET $offset";

$result = $conn->query($sql);

// Pagination total
$countSql = "SELECT COUNT(*) as total FROM items";
if ($conditions) $countSql .= " WHERE " . implode(" AND ", $conditions);
$totalResult = $conn->query($countSql);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $itemsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Panel - Lost & Found</title>
<link rel="stylesheet" href="css/main.css">
</head>
<body>

<h1>Admin Panel - Lost & Found</h1>

<div class="top-controls">
    <form method="get" class="filter-form">
        <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
        <select name="type">
            <option value="">All Types</option>
            <option value="electronic" <?= strtolower($type)=="electronic"?"selected":"" ?>>Electronic</option>
            <option value="clothing" <?= strtolower($type)=="clothing"?"selected":"" ?>>Clothing</option>
            <option value="other" <?= strtolower($type)=="other"?"selected":"" ?>>Other</option>
        </select>
        <button type="submit">Search</button>
    </form>
    <div class="top-buttons">
        <a href="logout.php" class="btn logout">Logout</a>
        <a href="admin_claims.php" class="btn">Claim History</a>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Founder</th>
            <th>Type</th>
            <th>Description</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['founder']) ?></td>
            <td><?= htmlspecialchars($row['type']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td>
                <?php if($row['image']): ?>
                    <img src="<?= htmlspecialchars($row['image']) ?>" width="80" onclick="zoomImage(this)">
                <?php else: ?>
                    No Image
                <?php endif; ?>
            </td>
            <td>
                <a href="claim.php?id=<?= $row['id'] ?>" class="btn">Claim</a>
                <a href="delete_item.php?id=<?= $row['id'] ?>" class="btn" onclick="return confirm('Delete this item?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="5">No items found.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<div class="pagination">
<?php if($page > 1): ?>
    <a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($type) ?>">Prev</a>
<?php endif; ?>
<?php if($page < $totalPages): ?>
    <a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($type) ?>">Next</a>
<?php endif; ?>
</div>

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
