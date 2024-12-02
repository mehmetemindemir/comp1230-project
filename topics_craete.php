<?php
include 'auth.php'; // Include authentication script
require 'db.config.php'; // Load database configuration
require 'classes.php'; // Load necessary classes



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

// Handle topic creation form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_topic'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $userId = $_SESSION['user_id'];

    // Validate input
    if (empty($title) || empty($description)) {
        $message = "Title and description cannot be empty.";
    } else {
        // Create the topic using the Topic class
        $success = $topic->createTopic($userId, $title, $description);
        if ($success) {
            $message = "Topic created successfully!";
        } else {
            $message = "Failed to create topic. Please try again.";
        }
    }
}

// Retrieve all topics to display
$topicsList = $topic->getTopics();
?>
<?php  $pageTitle = 'Topics Page';  include 'header.php'; ?>
<body class="topic-body">

    <h1>Topics</h1>

    <?php if ($message): ?>
        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="topics-container">
        <h2>Create a New Topic</h2>
        <form action="topics_craete.php" method="POST">
            <div class="topics-form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="topics-form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            <button type="submit" name="create_topic">Create Topic</button>
        </form>
    </div>

    <div class="topics-container">
        <h2>All Topics</h2>
        <?php if (!empty($topicsList)): ?>
            <ul class="topics-list">
                <?php foreach ($topicsList as $topicItem): ?>
                    <li class="topic-item">
                        <strong><?php echo htmlspecialchars($topicItem->title); ?></strong><br>
                        <p><?php echo nl2br(htmlspecialchars($topicItem->description)); ?></p>
                        <div class="topic-meta">
                            Created by User ID: <?php echo htmlspecialchars($topicItem->user_id); ?><br>
                            Created at: <?php echo htmlspecialchars($topicItem->created_at); ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No topics available.</p>
        <?php endif; ?>
    </div>

    <a href="dashboard.php">Back to Dashboard</a>

<?php include './footer.php' ?>