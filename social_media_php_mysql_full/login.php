<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD']==='POST'){
    if (!check_csrf($_POST['csrf'] ?? '')) { $error = 'Invalid CSRF token.'; }
    else {
        $user = trim($_POST['user'] ?? '');
        $pass = $_POST['password'] ?? '';
        if (!$user || !$pass) $error = 'All fields required.';
        else {
            $stmt = $mysqli->prepare('SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1');
            $stmt->bind_param('ss',$user,$user);
            $stmt->execute();
            $u = $stmt->get_result()->fetch_assoc();
            if ($u && password_verify($pass, $u['password'])) {
                $_SESSION['user_id'] = $u['id'];
                header('Location: index.php'); exit;
            } else $error = 'Invalid credentials.';
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Login</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="auth"><div class="card">
  <h2>Sign in</h2>
  <?php if(!empty($error)): ?><div class="errors"><?php echo e($error); ?></div><?php endif; ?>
  <form method="post">
    <input name="user" placeholder="username or email" required>
    <input name="password" type="password" placeholder="password" required>
    <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
    <button type="submit">Login</button>
  </form>
  <p>New? <a href="register.php">create account</a></p>
</div></body></html>
