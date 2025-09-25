<?php
require_once 'config.php';

// Save uploaded media and return relative path or null
function save_media($file){
    if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) return null;
    $max = 12 * 1024 * 1024;
    if ($file['size'] > $max) return null;
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/gif'=>'gif','video/mp4'=>'mp4','video/webm'=>'webm'];
    if (!isset($allowed[$mime])) return null;
    $ext = $allowed[$mime];
    $name = bin2hex(random_bytes(12)) . '.' . $ext;
    $dest = __DIR__ . '/uploads/' . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) return null;
    return 'uploads/' . $name;
}

// Simple pagination helper
function paginate($page, $per){ $p = max(1,(int)$page); $offset = ($p-1)*$per; return [$offset,$per]; }
?>