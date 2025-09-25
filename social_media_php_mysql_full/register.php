<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD']==='POST'){
    if (!check_csrf($_POST['csrf'] ?? '')) { $error = 'Invalid CSRF token.'; }
    else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        if (!$username || !$email || !$password) $error = 'All fields required.';
        else {
            // basic validation
            if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/',$username)) $error = 'Invalid username.';
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error = 'Invalid email.';
            else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $mysqli->prepare('INSERT INTO users (username, email, password, display_name) VALUES (?,?,?,?)');
                $stmt->bind_param('ssss', $username, $email, $hash, $username);
                if ($stmt->execute()){
                    $_SESSION['user_id'] = $mysqli->insert_id;
                    header('Location: index.php'); exit;
                } else {
                    $error = 'Registration failed: ' . $mysqli->error;
                }
            }
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Register</title><link rel="stylesheet" href="assets/style.css"></head>
<body class="auth"><div class="card">
  <h2>Create account</h2>
  <?php if(!empty($error)): ?><div class="errors"><?php echo e($error); ?></div><?php endif; ?>
  <form method="post">
    <input name="username" placeholder="username" required>
    <input name="email" type="email" placeholder="email" required>
    <input name="password" type="password" placeholder="password" required>
    <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
    <button type="submit">Register</button>
  </form>
  <p>Already? <a href="login.php">login</a></p>
</div></body></html>
