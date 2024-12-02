<?php
require 'db.config.php';
require 'classes.php';

session_start();

$config = require 'db.config.php';
$pdo = new PDO(
    "mysql:host={$config['host']};dbname={$config['database']}",
    $config['username'],
    $config['password']
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user = new User($pdo);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($user->authenticateUser($username, $password)) {
        $_SESSION['username'] = $username; // Save username in the session
        $_SESSION['user_id'] = $user->getUserId($username); // Save user ID in the session
        header('Location: dashboard.php'); // Redirect to the dashboard
        exit();
    } else {
        $error = 'Invalid username or password.';
    }
}
?>

<?php $pageTitle = 'Login Page';
include './header.php' ?>

<body class=" login-body">
    <div class="form-container">

        <?php if ($error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" onsubmit="return validateLoginForm()">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
    <?php include './footer.php' ?>