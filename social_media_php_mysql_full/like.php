<?php
require_once 'config.php';
$user = current_user();
if (!$user) { header('Location: login.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
if (!check_csrf($_POST['csrf'] ?? '')) { die('Invalid CSRF'); }
$post_id = (int)($_POST['post_id'] ?? 0);
if (!$post_id) header('Location: index.php');
$stmt = $mysqli->prepare('SELECT id FROM likes WHERE user_id=? AND post_id=? LIMIT 1');
$stmt->bind_param('ii',$user['id'],$post_id); $stmt->execute();
if ($stmt->get_result()->fetch_assoc()){
    $d = $mysqli->prepare('DELETE FROM likes WHERE user_id=? AND post_id=?'); $d->bind_param('ii',$user['id'],$post_id); $d->execute();
} else {
    $i = $mysqli->prepare('INSERT INTO likes (user_id, post_id) VALUES (?,?)'); $i->bind_param('ii',$user['id'],$post_id); $i->execute();
}
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
