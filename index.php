<?php
include "db.php";   // db.php already starts session

/* =========================
   Check login
========================= */
$loggedIn = isset($_SESSION['user']);
$userRole = $loggedIn ? $_SESSION['user']['role'] : null;

/* =========================
   Auto-cleanup: delete items older than 1 month
========================= */
$cleanupSql = "DELETE FROM itemss WHERE status='approved' AND created_at < (NOW() - INTERVAL 1 MONTH)";
$conn->query($cleanupSql);

/* =========================
   Filters
========================= */
$search = $_GET['search'] ?? "";
$type   = $_GET['type'] ?? "";

/* =========================
   Pagination
========================= */
$itemsPerPage = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $itemsPerPage;

/* =========================
   Build Conditions
========================= */
$conditions = ["status='approved'"]; // Only approved items

if (!empty($search)) {
    $searchEscaped = $conn->real_escape_string($search);
    $conditions[] = "description LIKE '%$searchEscaped%'";
}

if (!empty($type)) {
    $typeEscaped = $conn->real_escape_string($type);
    $conditions[] = "LOWER(type) = LOWER('$typeEscaped')";
}

/* =========================
   Main Query
========================= */
$sql = "SELECT * FROM itemss";
if ($conditions) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY created_at DESC LIMIT $itemsPerPage OFFSET $offset";

$result = $conn->query($sql);
if (!$result) {
    die("SQL ERROR: " . $conn->error);
}

/* =========================
   Count Query
========================= */
$countSql = "SELECT COUNT(*) as total FROM itemss";
if ($conditions) {
    $countSql .= " WHERE " . implode(" AND ", $conditions);
}
$totalResult = $conn->query($countSql);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'] ?? 0;
$totalPages = max(1, ceil($totalItems / $itemsPerPage));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>WhereClause</title>
<link rel="stylesheet" href="css/style.css">
<style>
/* Buttons */
.btn {
    display: inline-block;
    padding: 8px 15px;
    text-decoration: none;
    border-radius: 5px;
    margin: 2px;
    font-size: 14px;
    transition: 0.3s;
    font-weight: bold;
    cursor: pointer;
}

/* Edit button - yellow */
.btn.edit { background-color: #ffc107; color: #000; }
/* Claim button - red */
.btn.claim { background-color: #dc3545; color: #fff; }
/* Logout button - dark gray */
.btn.logout { background-color: #333; color: #fff; }
/* Table image zoom */
.table-img { max-width: 80px; cursor: pointer; }
.img-modal {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
.img-zoomed { max-width: 90%; max-height: 90%; }
</style>
</head>
<body>

<h1 class="title">WhereClause</h1>

<div class="top-controls">

    <form method="get" class="filter-form">
        <input type="text" name="search" placeholder="Search item..." value="<?= htmlspecialchars($search) ?>">
        <select name="type">
            <option value="">All Types</option>
            <option value="electronic" <?= strtolower($type)=="electronic"?"selected":"" ?>>Electronic</option>
            <option value="clothing" <?= strtolower($type)=="clothing"?"selected":"" ?>>Clothing</option>
            <option value="money" <?= strtolower($type)=="money"?"selected":"" ?>>Money</option>
            <option value="jewelry" <?= strtolower($type)=="jewelry"?"selected":"" ?>>Jewelry</option>
            <option value="other" <?= strtolower($type)=="other"?"selected":"" ?>>Other</option>
        </select>
        <button type="submit">Search</button>
    </form>

    <div class="top-buttons">
        <?php if($_SESSION['user']['role'] == 'admin'): ?>
            <a href="pending_items.php" class="btn">Pending Items</a>
        <?php endif; ?>

        <a href="add.php" class="btn">Add Item</a>

        <!-- Refresh Button -->
        <a href="index.php" class="btn" style="background-color:#17a2b8; color:#fff;">Refresh</a>

        <a href="logout.php" class="btn logout" style="background-color:#333; color:#fff;">Logout</a>
    </div>


</div>

<table class="data-table">
<thead>
<tr>
<th>Type</th>
<th>Description</th>
<th>Image</th>
<th>Expires On</th>
<?php if($loggedIn && $userRole=='admin'): ?>
    <th>Actions</th>
<?php endif; ?>
</tr>
</thead>
<tbody>

<?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($row['type']) ?></td>
        <td><?= htmlspecialchars($row['description']) ?></td>
        <td>
            <?php if(!empty($row['image'])): ?>
                <img src="<?= htmlspecialchars($row['image']) ?>" class="table-img" onclick="zoomImage(this)">
            <?php else: ?>
                <span class="no-img">No Image</span>
            <?php endif; ?>
        </td>
        <td><?= date("Y-m-d", strtotime($row['created_at'] . " +1 month")) ?></td>
        <?php if($loggedIn && $userRole=='admin'): ?>
        <td>
            <a href="edit_item.php?id=<?= $row['id'] ?>" class="btn edit">Edit</a>
            <a href="claim_item.php?id=<?= $row['id'] ?>" class="btn claim">Claim</a>
        </td>
        <?php endif; ?>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="<?= ($loggedIn && $userRole=='admin') ? 5 : 4 ?>" class="empty">No items found.</td>
</tr>
<?php endif; ?>

</tbody>
</table>

<div class="pagination">
    <?php if($page > 1): ?>
        <a class="page-btn" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($type) ?>">Prev</a>
    <?php endif; ?>

    <span class="page-info">Page <?= $page ?> of <?= $totalPages ?></span>

    <?php if($page < $totalPages): ?>
        <a class="page-btn" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&type=<?= urlencode($type) ?>">Next</a>
    <?php endif; ?>
</div>

<script>
function zoomImage(img) {
    const modal = document.createElement('div');
    modal.className = "img-modal";
    modal.onclick = () => modal.remove();

    const zoomed = document.createElement('img');
    zoomed.src = img.src;
    zoomed.className = "img-zoomed";

    modal.appendChild(zoomed);
    document.body.appendChild(modal);
}
</script>

</body>
</html>
