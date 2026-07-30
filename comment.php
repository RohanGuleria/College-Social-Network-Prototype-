<?php 
require 'auth.php';
require 'db_connect.php';

header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "User not logged in"]);
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $post_id = $_POST['post_id'] ?? null;
    $comment = trim($_POST['comment'] ?? '');

    if (!$post_id || empty($comment)) {
        echo json_encode(["status" => "error", "message" => "Invalid post ID or empty comment"]);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO comments (user_id, post_id, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $post_id, $comment);
    
    if ($stmt->execute()) {
        $comment_id = $stmt->insert_id;
        $comment_result = $conn->query("SELECT comments.comment, users.username FROM comments 
                                        JOIN users ON comments.user_id = users.id 
                                        WHERE comments.id = $comment_id");

        $new_comment = $comment_result->fetch_assoc();
        echo json_encode(["status" => "success", "username" => $new_comment['username'], "comment" => htmlspecialchars($new_comment['comment'])]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error"]);
    }
    exit();
}
?>
