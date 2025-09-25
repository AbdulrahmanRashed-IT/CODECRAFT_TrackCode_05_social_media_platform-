<?php
require_once 'config.php';
$user = current_user();
if (!$user) header('Location: login.php');
$username = $_GET['u'] ?? $user['username'];
$stmt = $mysqli->prepare('SELECT id, username, display_name, bio, avatar FROM users WHERE username = ? LIMIT 1');
$stmt->bind_param('s',$username); $stmt->execute(); $profile = $stmt->get_result()->fetch_assoc();
if (!$profile) { echo 'User not found'; exit; }
$postsStmt = $mysqli->prepare('SELECT p.*,(SELECT COUNT(*) FROM likes l WHERE l.post_id=p.id) as like_count FROM posts p WHERE p.user_id=? ORDER BY p.created_at DESC');
$postsStmt->bind_param('i',$profile['id']); $postsStmt->execute(); $posts = $postsStmt->get_result();
?>
<!doctype html><html><head><meta charset="utf-8"><title><?php echo e($profile['display_name']); ?></title><link rel="stylesheet" href="assets/style.css"></head>
<body>
<main class="container single">
  <div class="card profile-header">
    <h2><?php echo e($profile['display_name']); ?> <small>@<?php echo e($profile['username']); ?></small></h2>
    <p><?php echo e($profile['bio']); ?></p>
    <?php if ($profile['id'] === $user['id']): ?><a href="edit_profile.php">Edit profile</a><?php endif; ?>
  </div>
  <section class="feed">
    <?php while($p = $posts->fetch_assoc()): ?>
      <article class="post"><div class="post-header"><strong><?php echo e($profile['display_name']); ?></strong> <span class="muted"><?php echo e($p['created_at']); ?></span></div>
        <div class="post-body"><p><?php echo nl2br(e($p['content'])); ?></p><?php if($p['media']): ?><img src="<?php echo e($p['media']); ?>" class="post-media"><?php endif; ?></div>
        <div class="post-actions">Likes: <?php echo (int)$p['like_count']; ?></div>
      </article>
    <?php endwhile; ?>
  </section>
</main>
</body></html>
