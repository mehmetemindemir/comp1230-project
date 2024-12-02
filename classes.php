<?php
require 'time_formatter.php';
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // to check if the user is already registered or not then add the user to the database
    public function registerUser($username, $email, $password): bool {
        if (empty($username) || strlen($password) < 9 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("INSERT INTO Users (username, email, password) VALUES (?, ?, ?)");

        try {
            return $stmt->execute([$username, $email, $hashedPassword]);
        } catch (PDOException $e) {
            return false; 
        }
    }
    // to get the user id by username
    public function getUserId($username) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM Users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                return $user['id'];
            } else {
                return null; 
            }
        } catch (PDOException $e) {
            return null; 
        }
    }
    // to check user is exist or not by username and password
    public function authenticateUser($username, $password): bool {
        $stmt = $this->pdo->prepare("SELECT password FROM Users WHERE username = ? ");
        $stmt->execute([$username]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user && password_verify($password, $user['password']);
    }
}
class Topic {
    private $pdo;
    public $id;
    public $user_id;
    public $user_name;
    public $title;
    public $description;
    public $created_at;
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // to create a new topic
    public function createTopic($userId, $title, $description) {
        // Validate input
        if (empty($title) || empty($description)) {
            return false;
        }

        $stmt = $this->pdo->prepare("INSERT INTO Topics (user_id, title, description, created_at) VALUES (:user_id, :title, :description, NOW())");
        return $stmt->execute(['user_id' => $userId, 'title' => $title, 'description' => $description]);
    }

    // to get all topics
    public function getTopics() {
        try {
            $stmt = $this->pdo->query("SELECT t.*, (SELECT ss.username FROM Users ss WHERE ss.id = t.user_id) AS user_name FROM Topics t ORDER BY t.created_at DESC");
            $topicsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            $topics = [];
            foreach ($topicsData as $data) {
                $topic = new Topic($this->pdo);
                $topic->id = $data['id'];
                $topic->user_id = $data['user_id'];
                $topic->user_name = $data['user_name'];
                $topic->title = $data['title'];
                $topic->description = $data['description'];
                $topic->created_at =TimeFormatter::formatTimestamp( $data['created_at']);
                $topics[] = $topic;
            }
    
            return $topics; 
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // to get the topics created by the user
    public function getCreatedTopics($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM Topics WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
    // to get the topic by id
    public function getTopicById($topicId) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM Topics WHERE id = ?");
            $stmt->execute([$topicId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null; 
        }
    }
}
class Vote {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    // to vote on a topic by user
    public function vote($userId, $topicId, $voteType) {   

        try {
            if ($this->hasVoted($userId, $topicId)) {
                $stmt = $this->pdo->prepare("UPDATE Votes SET vote_type = ?, voted_at = NOW() WHERE user_id = ? AND topic_id = ?");
                return $stmt->execute([$voteType, $userId, $topicId]);
            }else{
                $stmt = $this->pdo->prepare("INSERT INTO Votes (user_id, topic_id, vote_type, voted_at) VALUES (?, ?, ?, NOW())");
                return $stmt->execute([$userId, $topicId, $voteType]);
            }
            
        } catch (PDOException $e) {
            return false; 
        }
    }
    // to check if the user has already voted on the topic
    public function hasVoted($userId, $topicId) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Votes WHERE user_id = ? AND topic_id = ?");
            $stmt->execute([$userId, $topicId]);
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch (PDOException $e) {
            return false;
        }
    }
    // to get the vote type by user
    public function countUpvotes($topicId) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Votes WHERE topic_id = ? AND vote_type = 'up'");
            $stmt->execute([$topicId]);
             return $stmt->fetchColumn();
           

        } catch (PDOException $e) {
            return 0; 
        }
    }
    // to get the vote type by user
    public function countDownvotes($topicId) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM Votes WHERE topic_id = ? AND vote_type = 'down'");
            $stmt->execute([$topicId]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0; 
        }
    }
}
class Comment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    // to add a comment on a topic
    public function addComment($userId, $topicId, $comment) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO Comments (user_id, topic_id, comment, commented_at) VALUES (?, ?, ?, NOW())");
            return $stmt->execute([$userId, $topicId, $comment]);
        } catch (PDOException $e) {
            return false; 
        }
    }
    // to get the comments on a topic
    public function getComments($topicId) {
        try {
            $stmt = $this->pdo->prepare("SELECT tt.*,(select username From Users where id=tt.user_id) as user_name FROM Comments tt  WHERE tt.topic_id = ? ORDER BY tt.commented_at DESC");
            $stmt->execute([$topicId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return []; 
        }
    }
}
