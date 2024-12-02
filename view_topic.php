<?php
session_start();
require 'db.config.php'; // Load database configuration
require 'classes.php'; // Load necessary classes

// Ensure user is authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php'); // Redirect to index page if not logged in
    exit();
}

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

// Instantiate Topic and Comment classes
$topic = new Topic($pdo);
$comment = new Comment($pdo);
$vote = new Vote($pdo);

// Get topic details
$topicId = $_GET['id'] ?? null;
if (!$topicId) {
    die("Topic not found.");
}
$topicDetails = $topic->getTopicById($topicId);

// Handle comment submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $userId = $_SESSION['user_id'];
    $commentText = trim($_POST['comment']);

    if (empty($commentText)) {
        $message = "Comment cannot be empty.";
    } else {
        $success = $comment->addComment($userId, $topicId, $commentText);
        if ($success) {
            $message = "Comment added successfully!";
        } else {
            $message = "Failed to add comment. Please try again.";
        }
    }
}

// Handle voting
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote_type'])) {
    $userId = $_SESSION['user_id'];
    $voteType = $_POST['vote_type'];
    $success = $vote->vote($userId, $topicId, $voteType);
    if ($success) {
        $message = "Your vote has been recorded.";
    }
}

// Get comments
$commentsList = $comment->getComments($topicId);
// Get vote counts
$upvoteCount = $vote->countUpvotes($topicId);
$downvoteCount = $vote->countDownvotes($topicId);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Topic Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }

        .container {
            margin-bottom: 20px;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .comment-box,
        .vote-box {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .comments-list {

            list-style-type: none;
            padding: 0;
        }

        .comment-item {
            margin-bottom: 15px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>


    <div class="vote-box">
        <h1><?php echo htmlspecialchars($topicDetails['title']); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($topicDetails['description'])); ?></p>
        <small>Created by User ID: <?php echo htmlspecialchars($topicDetails['user_id']); ?></small><br>
        <small>Created at: <?php echo htmlspecialchars(TimeFormatter::formatTimestamp( $topicDetails['created_at'])); ?></small>

        <?php if ($message): ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form id="voteForm" action="view_topic.php?id=<?php echo htmlspecialchars($topicId); ?>" method="POST">
            <button type="button" style="background-color: transparent; color: #0056b3;" onclick="submitVote('up')">
                👍 <?php echo htmlspecialchars($upvoteCount); ?>
            </button>
            <button type="button" style="background-color: transparent; color: #0056b3;" onclick="submitVote('down')">
                👎 <?php echo htmlspecialchars($downvoteCount); ?>
            </button>
            <input type="hidden" id="vote_type" name="vote_type" >
        </form>
    </div>

    <div class="comment-box">
        <h2>Add a Comment</h2>
        <form action="view_topic.php?id=<?php echo htmlspecialchars($topicId); ?>" method="POST">
            <textarea name="comment" rows="4" cols="50" required></textarea><br><br>
            <button type="submit" name="add_comment">Add Comment</button>
        </form>
    </div>

    <div class="container">
        <h2>Comments</h2>
        <?php if (!empty($commentsList)): ?>
            <ul class="comments-list">
                <?php foreach ($commentsList as $commentItem): ?>
                    <li class="comment-item">
                        <strong> <?php echo htmlspecialchars($commentItem['user_name']); ?></strong><br>
                        <?php echo nl2br(htmlspecialchars($commentItem['comment'])); ?><br>
                        <small>Commented at: <?php echo htmlspecialchars(TimeFormatter::formatTimestamp($commentItem['commented_at'])); ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No comments yet. Be the first to comment!</p>
        <?php endif; ?>
    </div>

    <a href="topics.php">Back to Topics</a>

    <script>
        function submitVote(voteType) {
            document.getElementById('vote_type').value = voteType;

            document.getElementById('voteForm').submit();
        }
    </script>
</body>

</html>