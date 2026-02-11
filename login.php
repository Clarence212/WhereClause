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
<link rel="stylesheet" href="css/style.css">
<style>
    /* === Reset & General === */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #6C63FF, #48C6EF);
}

/* === Login wrapper === */
.login-wrapper {
    width: 100%;
    max-width: 400px;
    padding: 20px;
}

/* === Card style === */
.login-card {
    background-color: #fff;
    border-radius: 15px;
    padding: 30px 25px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    text-align: center;
    transition: transform 0.3s ease;
}

.login-card:hover {
    transform: translateY(-5px);
}

/* === Header === */
.login-header .login-logo {
    width: 80px;
    margin-bottom: 15px;
}

.login-header h2 {
    margin-bottom: 5px;
    color: #333;
}

.login-header p {
    color: #777;
    font-size: 14px;
    margin-bottom: 20px;
}

/* === Error message === */
.error {
    background-color: #ffe5e5;
    color: #b30000;
    border: 1px solid #ffb3b3;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 15px;
    text-align: center;
    font-weight: bold;
}

/* === Form inputs === */
.login-form input[type="text"],
.login-form input[type="password"] {
    width: 100%;
    padding: 12px 15px;
    margin-bottom: 15px;
    border-radius: 10px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: all 0.3s ease;
}

.login-form input:focus {
    outline: none;
    border-color: #6C63FF;
    box-shadow: 0 0 5px rgba(108,99,255,0.5);
}

/* === Submit button === */
.login-form button {
    width: 100%;
    padding: 12px 15px;
    border-radius: 10px;
    border: none;
    background: #6C63FF;
    color: #fff;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

.login-form button:hover {
    background: #5848c2;
}

/* === Footer === */
.login-footer p {
    margin-top: 15px;
    font-size: 13px;
    color: #777;
}

.login-footer a {
    color: #6C63FF;
    text-decoration: none;
}

.login-footer a:hover {
    text-decoration: underline;
}
    /* === Footer Watermark === */
.footer-watermark {
    position: fixed;      /* stays at the bottom */
    bottom: 10px;
    width: 100%;
    text-align: center;
    font-size: 12px;
    color: rgba(0,0,0,0.3);  /* light gray / semi-transparent */
    pointer-events: none;     /* won't block clicks */
    user-select: none;        /* can't highlight text */
}
.form-box .guest-btn {
    width: 100%;
    padding: 10px;
    background-color: #28a745;  /* green */
    color: #fff;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
}

.form-box .guest-btn:hover {
    background-color: #218838;
}
</style>
</head>
<body>

<div class="form-box">
    <h2>WhereClause</h2>

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

    <!-- Guest button -->
    <form method="get" action="index.php">
        <button type="submit" class="btn guest-btn">Continue as Guest</button>
    </form>
</div>
    <div class="footer-watermark">
    © 2026 WhereClause inc. All rights reserved
</div>

</body>
</html>