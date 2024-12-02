<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', true);
    ini_set('session.cookie_secure', true); 
    ini_set('session.use_strict_mode', true);
    session_start();
    session_regenerate_id(true);
}

// Check if the user is authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirect to login page if not logged in
    exit();
}