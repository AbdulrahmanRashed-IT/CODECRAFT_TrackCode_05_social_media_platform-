<?php
require_once 'config.php';
require_once 'functions.php';

$user = current_user();
if (!$user) {
    header('Location: login.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!check_csrf($_POST['csrf'] ?? '')) {
        $errors[] = 'Invalid CSRF token';
    }

    $display = trim($_POST['display_name'] ?? '');
    $bio     = trim($_POST['bio'] ?? '');
    $avatar_path = null;

    if (!empty($_FILES['avatar']['name'])) {
        $avatar_path = save_media($_FILES['avatar']);
    }

    if (empty($errors)) {
        $stmt = $mysqli->prepare('UPDATE users SET display_name=?, bio=?, avatar=? WHERE id=?');
        $stmt->bind_param('sssi', $display, $bio, $avatar_path, $user['id']);
        $stmt->execute();

        header('Location: profile.php?u=' . urlencode($user['username']));
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="container single">
    <div class="card">
        <h2>Edit profile</h2>

        <?php if ($errors): ?>
            <?php foreach ($errors as $er): ?>
                <div class="errors"><?php echo e($er); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <input 
                name="display_name" 
                placeholder="Display name" 
                value="<?php echo e($user['display_name'] ?? ''); ?>" 
                required
            >
            <textarea 
                name="bio" 
                placeholder="Bio"
            ><?php echo e($user['bio'] ?? ''); ?></textarea>

            <input type="file" name="avatar" accept="image/*">

            <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">

            <button type="submit">Save</button>
        </form>
    </div>
</main>
</body>
</html>
