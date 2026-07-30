<?php
require 'auth.php';
require 'db_connect.php';

$result = $conn->query("SELECT posts.*, users.username, users.profile_pic, 
                        (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count 
                        FROM posts 
                        JOIN users ON posts.user_id = users.id 
                        ORDER BY posts.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feed</title>
  <link rel="stylesheet" href="css/bootstrap.css">
  <script src="css/bootstrap.bundle.js"></script>
</head>
<body>

<nav class="navbar bg-light navbar-expand-lg px-3">
  <a class="navbar-brand fw-bold" href="#"><b>CliqNet</b></a>
  <div class="ms-auto d-flex gap-2">
    <a class="btn btn-light rounded-pill px-3" href="upload_post.html">Create Post</a>
    <a class="btn btn-dark rounded-pill px-3" href="logout.php">Logout</a>
  </div>
</nav>

<div class="container mt-4">
  <h2 class="text-center">CliqNet Feed</h2>

  <?php while ($post = $result->fetch_assoc()) { ?>
    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
          <img src="<?= htmlspecialchars($post['profile_pic']) ?>" class="rounded-circle" width="40" height="40">
          <strong class="ms-2"><?= htmlspecialchars($post['username']) ?></strong>
        </div>

        <?php if ($post['user_id'] == $_SESSION['user_id']) { ?>
          <button class="btn btn-dark btn-sm delete-btn" data-post-id="<?= $post['id'] ?>">🗑️ Delete</button>
        <?php } ?>
      </div>

      <img src="<?= htmlspecialchars($post['image']) ?>" class="card-img-top" alt="User post image">

      <div class="card-body">
        <p><?= htmlspecialchars($post['caption']) ?></p>
        <button class="btn btn-outline-primary like-btn" data-post-id="<?= $post['id'] ?>">
          ❤️ Like (<span id="like-count-<?= $post['id'] ?>"><?= $post['like_count'] ?></span>)
        </button>
      </div>

      <div class="card-footer">
        <form class="comment-form" data-post-id="<?= $post['id'] ?>">
          <input type="text" class="form-control comment-input" placeholder="Write a comment...">
          <button type="submit" class="btn btn-sm btn-dark mt-2">Comment</button>
        </form>

        <div id="comments-<?= $post['id'] ?>">
          <?php
          $comment_query = $conn->query("SELECT comments.comment, users.username FROM comments 
                                         JOIN users ON comments.user_id = users.id 
                                         WHERE comments.post_id = {$post['id']} 
                                         ORDER BY comments.id DESC");
          while ($comment = $comment_query->fetch_assoc()) {
              echo "<p><strong>" . htmlspecialchars($comment['username']) . ":</strong> " . htmlspecialchars($comment['comment']) . "</p>";
          }
          ?>
        </div>
      </div>
    </div>
  <?php } ?>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {

    document.body.addEventListener("click", function (e) {
      if (e.target.classList.contains("like-btn")) {
        let postId = e.target.getAttribute("data-post-id");
        let likeCount = document.getElementById("like-count-" + postId);

        fetch("like.php", {
          method: "POST",
          body: new URLSearchParams({ post_id: postId }),
          headers: { "Content-Type": "application/x-www-form-urlencoded" }
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === "liked") {
            likeCount.innerText = parseInt(likeCount.innerText) + 1;
          } else if (data.status === "unliked") {
            likeCount.innerText = parseInt(likeCount.innerText) - 1;
          }
        });
      }

      if (e.target.classList.contains("delete-btn")) {
        let postId = e.target.getAttribute("data-post-id");
        if (!confirm("Are you sure you want to delete this post?")) return;

        fetch("delete_post.php", {
          method: "POST",
          body: new URLSearchParams({ post_id: postId }),
          headers: { "Content-Type": "application/x-www-form-urlencoded" }
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === "success") {
            e.target.closest(".card").remove();
          } else {
            alert("Error deleting post.");
          }
        });
      }
    });

    document.body.addEventListener("submit", function (e) {
      if (e.target.classList.contains("comment-form")) {
        e.preventDefault();

        let form = e.target;
        let postId = form.getAttribute("data-post-id");
        let commentInput = form.querySelector(".comment-input");
        let commentText = commentInput.value.trim();
        let commentSection = document.getElementById("comments-" + postId);

        if (commentText === "") return;

        fetch("comment.php", {
          method: "POST",
          body: new URLSearchParams({ post_id: postId, comment: commentText }),
          headers: { "Content-Type": "application/x-www-form-urlencoded" }
        })
        .then(response => response.json())
        .then(data => {
          if (data.status === "success") {
            let newComment = document.createElement("p");
            newComment.innerHTML = `<strong>${data.username}:</strong> ${data.comment}`;
            commentSection.prepend(newComment);
            commentInput.value = "";
          } else {
            alert(data.message);
          }
        });
      }
    });

  });
</script>

</body>
</html>
