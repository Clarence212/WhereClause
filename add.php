<?php
include "db.php";
if (session_status() === PHP_SESSION_NONE) session_start();

// Assign guest session if no user logged in
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'id' => 0,
        'role' => 'guest',
        'name' => 'Guest'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Lost Item</title>
<link rel="stylesheet" href="css/form.css">
</head>
<body>

<form class="form-box" action="save.php" method="POST" enctype="multipart/form-data">
  <a href="index.php" class="close-btn">✖</a>
  <h2>Add Lost Item</h2>

  <select name="type" required>
    <option value="electronic">Electronic</option>
    <option value="clothing">Clothing</option>
    <option value="money">Money</option>
    <option value="jewelry">Jewelry</option>
    <option value="other">Other</option>
  </select>

  <textarea name="description" placeholder="Description" required></textarea>

  <input type="file" name="image">

  <button type="submit">Save</button>
</form>

</body>
</html>
