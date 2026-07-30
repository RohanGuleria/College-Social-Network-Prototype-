<?php
require 'auth.php';
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    
    $result = $conn->query("SELECT * FROM posts WHERE id = $post_id AND user_id = $user_id");
    
    if ($result->num_rows > 0) {
        $conn->query("DELETE FROM posts WHERE id = $post_id");
        $conn->query("DELETE FROM comments WHERE post_id = $post_id");
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
}
?>
