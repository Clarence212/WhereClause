<?php
include "db.php";

// Start session if not started
if (session_status() === PHP_SESSION_NONE) session_start();

// Clear all session data
session_unset();
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;