<?php
  include 'auth.php'; 
require 'db.config.php'; // Load database configuration
require 'classes.php'; // Load necessary classes// Load Topic class



// Create a PDO instance using the database configuration
try {
    $config = require 'db.config.php';
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']}",
        $config['username'],
        $config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Instantiate Topic class
$topic = new Topic($pdo);

// Retrieve all topics to display
$topicsList = $topic->getTopics();
?>
<?php $pageTitle = 'Topics Page'; include 'header.php'; ?>

<body class=" topic-body">
    <h1>Topics</h1>
    
        <?php if (!empty($topicsList)): ?>
        <div class=" topics-container">
            <ul class="topics-list">
                <?php foreach ($topicsList as $topicItem): ?>
                    <li class="topic-item">
                        <a href="view_topic.php?id=<?php echo htmlspecialchars($topicItem->id); ?>">
                            <strong > ➡️  <?php echo htmlspecialchars($topicItem->title); ?></strong>
                            <br/><br/>
                            <small>Created by: <?php echo htmlspecialchars($topicItem->user_name); ?></small>
                            <small >Created at:  <?php echo htmlspecialchars($topicItem->created_at); ?></small>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php else: ?>
            <div class=" topics-container">
            <p>No topics available.</p>
           </div> 
        <?php endif; ?>
   
    <a href="dashboard.php">Back to Dashboard</a>
</body>

</html>