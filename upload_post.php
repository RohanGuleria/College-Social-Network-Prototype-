<?php
require 'auth.php';
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $caption = trim($_POST['caption']);

    if (!empty($_FILES['post_image']['name'])) {
        $target_dir = "images/posts/";


        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = time() . "_" . basename($_FILES["post_image"]["name"]);
        $target_file = $target_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($file_type, $allowed_types)) {
            echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
            exit();
        }

        
        $check = getimagesize($_FILES["post_image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["post_image"]["tmp_name"], $target_file)) {
            
                $stmt = $conn->prepare("INSERT INTO posts (user_id, image, caption) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $user_id, $target_file, $caption);
                
                if ($stmt->execute()) {
                    header("Location: feed.php");
                    exit();
                } else {
                    echo "Error saving post to the database.";
                }
            } else {
                echo "Error moving uploaded file.";
            }
        } else {
            echo "File is not an image.";
        }
    } else {
        echo "Please upload an image.";
    }
}
?>
