<?php
include "db.php";   // db.php already starts session

$error = "";

// Only run login logic if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === "" || $password === "") {
        $error = "All fields are required";
    } else {

        // Hash the password
        $passwordHash = hash('sha256', $password);

        // Prepare statement to fetch full user info (id + username + role)
        $stmt = $conn->prepare("SELECT id, username, role FROM userss WHERE username=? AND password=?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ss", $username, $passwordHash);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            // Fetch user info
            $user = $result->fetch_assoc();

            // Store full user info in session
            $_SESSION['user'] = [
                'id'   => $user['id'],
                'name' => $user['username'],
                'role' => $user['role']
            ];

            // Redirect to main page
            header("Location: index.php");
            exit;

        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="css/main.css">
</head>
<body>

<div class="form-box">
    <h2>Login</h2>

    <?php if($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
