<?php
session_start();
require 'auth.php';
$conn = new mysqli("localhost", "root", "", "cliqnet");

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $bio = trim($_POST['bio']);

    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
        move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);
        $conn->query("UPDATE users SET profile_pic='$target_file' WHERE id=$user_id");
    }

    $stmt = $conn->prepare("UPDATE users SET username=?, bio=? WHERE id=?");
    $stmt->bind_param("ssi", $username, $bio, $user_id);
    if ($stmt->execute()) {
        header("Location: profile.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar bg-body-light navbar-expand-lg shadow py-2 px-3">
    <a class="navbar-brand fw-bold" href="#"><b>CliqNet</b></a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
  
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav gap-2">
        <li class="nav-item">
          <a class="nav-link btn btn-light rounded-pill px-3" href="#">Connect</a>
        </li>
        <li class="nav-item">
          <a class="nav-link btn btn-light rounded-pill px-3" href="#">Create</a>
        </li>
        <li class="nav-item">
          <a class="nav-link btn btn-light rounded-pill px-3" href="#">Collaborate</a>
        </li>
      </ul>
    </div>
  
    <div class="d-flex gap-2">
      <a class="btn btn-dark rounded-pill px-3" href="feed.php">Feed</a>
      <a class="btn btn-light rounded-pill px-3" href="upload_post.html">Create Post</a>
      <a class="btn btn-dark rounded-pill px-3" href="logout.php">Logout</a>
    </div>
</nav>

<div class="container text-center mt-4">
    <h2>Your Profile</h2>
    <p>Manage your account details</p>
</div><br>

<div class="container d-flex flex-column align-items-center mt-4">
    <div class="text-center mb-3">
        <h3>Profile Information</h3>
    </div>

    <form action="profile.php" method="POST" enctype="multipart/form-data" class="w-50">
        <div class="mb-3 text-center">
            <img src="<?= $user['profile_pic'] ?>" id="profilePic" class="rounded-circle" width="120" height="120" alt="Profile Picture"><br><br>
            <label class="btn btn-dark">
                Update Profile Pic <input type="file" name="profile_pic" accept="image/*" hidden>
            </label>
        </div>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" value="<?= $user['username'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?= $user['email'] ?>" readonly>
        </div>
        <div class="mb-3">
            <label class="form-label">Bio</label>
            <textarea class="form-control" name="bio" rows="3"><?= $user['bio'] ?></textarea>
        </div>
        <div class="d-grid">
            <input class="btn btn-dark" type="submit" value="Update Profile">
        </div>
    </form>
</div>

<hr>

<footer class="bg-light py-5">
  <div class="container">
      <div class="row">
          <div class="col-md-4">
              <h5 class="fw-bold">CliqNet</h5>
              <p class="text-muted">Connect. Create. Collaborate.</p>
          </div>
          <div class="col-md-2">
              <h6 class="fw-bold">Platform</h6>
              <ul class="list-unstyled text-muted">
                  <li><a href="#" class="text-decoration-none text-muted">Features</a></li>
                  <li><a href="#" class="text-decoration-none text-muted">Pricing</a></li>
                  <li><a href="#" class="text-decoration-none text-muted">FAQ</a></li>
              </ul>
          </div>
          <div class="col-md-2">
              <h6 class="fw-bold">Company</h6>
              <ul class="list-unstyled text-muted">
                  <li><a href="#" class="text-decoration-none text-muted">About</a></li>
                  <li><a href="#" class="text-decoration-none text-muted">Blog</a></li>
                  <li><a href="#" class="text-decoration-none text-muted">Careers</a></li>
              </ul>
          </div>
          <div class="col-md-2">
              <h6 class="fw-bold">Legal</h6>
              <ul class="list-unstyled text-muted">
                  <li><a href="#" class="text-decoration-none text-muted">Terms</a></li>
                  <li><a href="#" class="text-decoration-none text-muted">Privacy</a></li>
                  <li><a href="#" class="text-decoration-none text-muted">Cookies</a></li>
              </ul>
          </div>
      </div>
      <div class="text-center mt-4 text-muted">
          &copy; 2025 CliqNet. All rights reserved.
      </div>
  </div>
</footer>

<script src="css/bootstrap.bundle.js"></script>
</body>
</html>
