<?php
require_once 'config.php';
require_once 'functions.php';
$user = current_user();
if (!$user) header('Location: login.php');

// handle post create
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['content'])){
    if (!check_csrf($_POST['csrf'] ?? '')) { $error = 'Invalid CSRF token.'; }
    else {
        $content = trim($_POST['content']);
        $media = null;
        if (!empty($_FILES['media']['name'])) $media = save_media($_FILES['media']);
        $stmt = $mysqli->prepare('INSERT INTO posts (user_id, content, media) VALUES (?, ?, ?)');
        $stmt->bind_param('iss', $user['id'], $content, $media);
        $stmt->execute();
        header('Location: index.php'); exit;
    }
}

// pagination
$page = isset($_GET['page'])?(int)$_GET['page']:1;
list($offset,$limit) = paginate($page, 10);

// fetch posts with like/comment counts
$sql = "SELECT p.*, u.username, u.display_name,
  (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) AS like_count,
  (SELECT COUNT(*) FROM comments c WHERE c.post_id = p.id) AS comment_count
  FROM posts p JOIN users u ON u.id = p.user_id
  ORDER BY p.created_at DESC LIMIT ?, ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('ii', $offset, $limit);
$stmt->execute();
$posts = $stmt->get_result();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>MySocial - Feed</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar"><div class="brand">MySocial</div>
<nav><?php echo e($user['display_name']); ?> | <a href="profile.php?u=<?php echo e($user['username']); ?>">Profile</a> | <a href="logout.php">Logout</a></nav></header>
<main class="container">
  <aside class="side">
    <div class="card">
      <h3>Create post</h3>
      <?php if(!empty($error)): ?><div class="errors"><?php echo e($error); ?></div><?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <textarea name="content" placeholder="What's happening?" required></textarea>
        <input type="file" name="media" accept="image/*,video/*">
        <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
        <button type="submit">Post</button>
      </form>
    </div>
  </aside>
  <section class="feed">
    <?php while($p = $posts->fetch_assoc()): ?>
      <article class="post">
        <div class="post-header"><strong><?php echo e($p['display_name']); ?></strong> <span class="muted">@<?php echo e($p['username']); ?> · <?php echo e($p['created_at']); ?></span></div>
        <div class="post-body">
          <p><?php echo nl2br(e($p['content'])); ?></p>
          <?php if($p['media']): $ext = pathinfo($p['media'], PATHINFO_EXTENSION); if (in_array(strtolower($ext), ['mp4','webm'])): ?>
            <video controls src="<?php echo e($p['media']); ?>" class="post-media"></video>
          <?php else: ?>
            <img src="<?php echo e($p['media']); ?>" class="post-media">
          <?php endif; endif; ?>
        </div>
        <div class="post-actions">
          <form method="post" action="like.php" style="display:inline;">
            <input type="hidden" name="post_id" value="<?php echo e($p['id']); ?>">
            <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
            <button type="submit">Like (<?php echo (int)$p['like_count']; ?>)</button>
          </form>
          <a href="post.php?id=<?php echo e($p['id']); ?>">Comments (<?php echo (int)$p['comment_count']; ?>)</a>
        </div>
      </article>
    <?php endwhile; ?>
    <div class="pagination">
      <?php if($page>1): ?><a href="?page=<?php echo $page-1; ?>">Prev</a><?php endif; ?>
      <a href="?page=<?php echo $page+1; ?>">Next</a>
    </div>
  </section>
</main>
</body>
</html>
