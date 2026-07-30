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

    if (!$post_id) {
        echo json_encode(["status" => "error", "message" => "Invalid post ID"]);
        exit();
    }

    // Check if user already liked the post
    $check_like = $conn->query("SELECT * FROM likes WHERE user_id = $user_id AND post_id = $post_id");

    if ($check_like->num_rows > 0) {
        $conn->query("DELETE FROM likes WHERE user_id = $user_id AND post_id = $post_id");
        echo json_encode(["status" => "unliked"]);
    } else {
        $conn->query("INSERT INTO likes (user_id, post_id) VALUES ($user_id, $post_id)");
        echo json_encode(["status" => "liked"]);
    }
    exit();
}
?>
