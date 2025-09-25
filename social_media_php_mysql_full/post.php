<?php
require_once 'config.php';
require_once 'functions.php';
$user = current_user();
if (!$user) header('Location: login.php');
$id = isset($_GET['id'])?(int)$_GET['id']:0;
$stmt = $mysqli->prepare('SELECT p.*, u.username, u.display_name FROM posts p JOIN users u ON u.id=p.user_id WHERE p.id=? LIMIT 1');
$stmt->bind_param('i',$id); $stmt->execute(); $post = $stmt->get_result()->fetch_assoc();
if (!$post) { echo 'Post not found'; exit; }

// handle comment
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['comment'])){
    if (!check_csrf($_POST['csrf'] ?? '')) $error='Invalid CSRF';
    else {
        $c = trim($_POST['comment']);
        if ($c !== ''){
            $s = $mysqli->prepare('INSERT INTO comments (user_id, post_id, content) VALUES (?,?,?)');
            $s->bind_param('iis', $user['id'], $id, $c);
            $s->execute();
            header('Location: post.php?id='.$id); exit;
        }
    }
}

// fetch comments
$cstmt = $mysqli->prepare('SELECT c.*, u.username, u.display_name FROM comments c JOIN users u ON u.id=c.user_id WHERE c.post_id=? ORDER BY c.created_at ASC');
$cstmt->bind_param('i',$id); $cstmt->execute(); $comments = $cstmt->get_result();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Post</title><link rel="stylesheet" href="assets/style.css"></head>
<body>
<main class="container single">
  <article class="post">
    <div class="post-header"><strong><?php echo e($post['display_name']); ?></strong> <span class="muted">@<?php echo e($post['username']); ?> · <?php echo e($post['created_at']); ?></span></div>
    <div class="post-body"><p><?php echo nl2br(e($post['content'])); ?></p>
      <?php if($post['media']): $ext = pathinfo($post['media'], PATHINFO_EXTENSION); if (in_array(strtolower($ext), ['mp4','webm'])): ?>
        <video controls src="<?php echo e($post['media']); ?>" class="post-media"></video>
      <?php else: ?>
        <img src="<?php echo e($post['media']); ?>" class="post-media">
      <?php endif; endif; ?></div>
    <div class="post-actions">
      <form method="post"><textarea name="comment" placeholder="Write a comment..." required></textarea>
      <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>"><button type="submit">Comment</button></form>
    </div>
  </article>
  <section class="comments"><h3>Comments</h3>
    <?php while($c = $comments->fetch_assoc()): ?>
      <div class="comment"><strong><?php echo e($c['display_name']); ?></strong> <span class="muted"><?php echo e($c['created_at']); ?></span>
        <div><?php echo nl2br(e($c['content'])); ?></div></div>
    <?php endwhile; ?>
  </section>
</main>
</body></html>
