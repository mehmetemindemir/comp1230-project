<?php
    include 'auth.php';
    // get the username from the session
    $username = $_SESSION['username'];
?>


<!-- get page header -->
 <?php $pageTitle='Dashboard Page'; include 'header.php';  ?> 
 <body class="login-body">  
    <div class="dashboard-container">
        <h1>Welcome, <?= htmlspecialchars($username) ?>!</h1>
        <a href="topics_craete.php">Create Topic</a>
        <a href="topics.php">View Topics</a>
        <a href="professor.php">For Prof</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
<?php include 'footer.php'; ?>    
