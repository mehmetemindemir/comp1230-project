<?php
require 'db.config.php';
require 'classes.php';

$config = require 'db.config.php';
$pdo = new PDO(
    "mysql:host={$config['host']};dbname={$config['database']}",
    $config['username'],
    $config['password']
);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user = new User($pdo);

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($user->registerUser($username, $email, $password)) {
        $success = 'Registration successful. You can now log in.';
    } else {
        $error = 'Registration failed. Username or email may already be in use.';
    }
}
?>

<?php $pageTitle = 'Register Page'; include './header.php' ?>
<body class="login-body"> 
<div class="registration-container">
        <h2>Create an Account</h2>
        <?php if ($error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p style="color: green;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>     
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" >Register</button>
        </form>
        <a href="index.php" class="login-link">Already have an account? Log in</a>
    </div>
<script>
    document.querySelector('form').addEventListener('submit', function(event) {
        const password = document.getElementById('password').value;
        if (password.length < 9) {
            alert('Password must be at least 9 characters long.');
            event.preventDefault(); // Prevent form submission
        }
    });
</script>

<?php include './footer.php' ?>
