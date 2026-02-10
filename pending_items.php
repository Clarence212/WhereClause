<?php
include "db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

/* =========================
   Fetch Pending Items
========================= */
$sql = "SELECT id, type, description, image FROM itemss WHERE status='pending' ORDER BY created_at DESC";
$result = $conn->query($sql);
if (!$result) {
    die("SQL ERROR: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pending Items Approval</title>
<link rel="stylesheet" href="css/main.css">
<style>
    /* Existing .btn class already styles buttons */
.btn {
    display: inline-block;
    padding: 8px 15px;
    text-decoration: none;
    color: #fff;
    background-color: #007bff; /* default blue */
    border-radius: 5px;
    margin: 2px;
    font-size: 14px;
    transition: 0.3s;
}

.btn:hover {
    opacity: 0.8;
}

/* Approve button - green */
.btn.approve {
    background-color: #28a745;
}

/* Reject button - red */
.btn.reject {
    background-color: #dc3545;
}

</style>
</head>
<body>

<h1 class="title">Pending Items Approval</h1>
<div class="top-buttons">
    <a href="index.php" class="btn">Back to Main</a>
</div>

<table class="data-table">
<thead>
<tr>
<th>Type</th>
<th>Description</th>
<th>Image</th>
<th>Actions</th>
</tr>
</thead>
<tbody>

<?php if($result->num_rows > 0): ?>
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
        <td>
            <a href="approve_item.php?id=<?= $row['id'] ?>" class="btn approve">Approve</a>
            <a href="reject_item.php?id=<?= $row['id'] ?>" class="btn reject">Reject</a>
        </td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="4" class="empty">No pending items.</td>
</tr>
<?php endif; ?>

</tbody>
</table>

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
