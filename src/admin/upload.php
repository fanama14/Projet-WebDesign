<?php

// Centralized upload constraints.

function upload_allowed_extensions()
{
    return array('jpg', 'jpeg', 'png', 'webp', 'gif');
}

function upload_allowed_mimes()
{
    return array(
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    );
}

function upload_open_source_image($tmpPath, $mime)
{
    if ($mime === 'image/jpeg') {
        return @imagecreatefromjpeg($tmpPath);
    }
    if ($mime === 'image/png') {
        return @imagecreatefrompng($tmpPath);
    }
    if ($mime === 'image/webp' && function_exists('imagecreatefromwebp')) {
        return @imagecreatefromwebp($tmpPath);
    }
    if ($mime === 'image/gif') {
        return @imagecreatefromgif($tmpPath);
    }

    return false;
}

function upload_resize_to_webp($src, $srcWidth, $srcHeight, $targetWidth, $targetPath)
{
    if ($srcWidth <= 0 || $srcHeight <= 0 || $targetWidth <= 0) {
        throw new RuntimeException('Invalid image dimensions.');
    }

    // Preserve the source ratio using a fixed output width.
    $ratio = $srcHeight / $srcWidth;
    $targetHeight = (int)round($targetWidth * $ratio);
    if ($targetHeight < 1) {
        $targetHeight = 1;
    }

    $dst = imagecreatetruecolor($targetWidth, $targetHeight);
    if ($dst === false) {
        throw new RuntimeException('Cannot allocate resized image.');
    }

    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefilledrectangle($dst, 0, 0, $targetWidth, $targetHeight, $transparent);

    $resampled = imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetWidth, $targetHeight, $srcWidth, $srcHeight);
    if ($resampled === false) {
        imagedestroy($dst);
        throw new RuntimeException('Resampling failed.');
    }

    $saved = imagewebp($dst, $targetPath, 85);
    imagedestroy($dst);

    if ($saved === false) {
        throw new RuntimeException('WebP conversion failed.');
    }
}

function upload_remove_previous_variants($pdo, $articleId)
{
    $stmt = $pdo->prepare('SELECT image_path FROM article_images WHERE article_id = :article_id AND image_kind IN (\'main\', \'thumb\')');
    $stmt->execute(array('article_id' => $articleId));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $path = isset($row['image_path']) ? (string)$row['image_path'] : '';
        if ($path === '') {
            continue;
        }

        $full = __DIR__ . '/../' . ltrim($path, '/');
        if (is_file($full)) {
            @unlink($full);
        }
    }
}

function upload_process_article_image($pdo, $articleId, $fileInfo, $imageAlt)
{
    if (!isset($fileInfo['error']) || $fileInfo['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($fileInfo['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload error code: ' . (string)$fileInfo['error']);
    }

    $tmpPath = isset($fileInfo['tmp_name']) ? (string)$fileInfo['tmp_name'] : '';
    $originalName = isset($fileInfo['name']) ? (string)$fileInfo['name'] : '';

    if ($tmpPath === '' || (!is_uploaded_file($tmpPath) && !is_file($tmpPath))) {
        throw new RuntimeException('Invalid uploaded file.');
    }

    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    if (!in_array($extension, upload_allowed_extensions(), true)) {
        throw new RuntimeException('Unsupported extension.');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if ($finfo === false) {
        throw new RuntimeException('Cannot initialize fileinfo.');
    }
    $mime = (string)finfo_file($finfo, $tmpPath);
    finfo_close($finfo);

    if (!in_array($mime, upload_allowed_mimes(), true)) {
        throw new RuntimeException('Unsupported MIME type: ' . $mime);
    }

    $src = upload_open_source_image($tmpPath, $mime);
    if ($src === false) {
        throw new RuntimeException('Cannot decode source image.');
    }

    $srcWidth = imagesx($src);
    $srcHeight = imagesy($src);

    if ($srcWidth < 1 || $srcHeight < 1) {
        imagedestroy($src);
        throw new RuntimeException('Invalid source image size.');
    }

    $imagesDirFs = __DIR__ . '/../assets/images';
    if (!is_dir($imagesDirFs) && !mkdir($imagesDirFs, 0755, true)) {
        imagedestroy($src);
        throw new RuntimeException('Cannot create image directory.');
    }

    upload_remove_previous_variants($pdo, $articleId);

    try {
        $randomPart = substr(bin2hex(random_bytes(4)), 0, 8);
    } catch (Exception $e) {
        $randomPart = substr(uniqid('', true), -8);
    }

    $token = 'article-' . (int)$articleId . '-' . date('YmdHis') . '-' . $randomPart;
    $thumbName = $token . '-x-thumb_400.webp';
    $mainName = $token . '-main_1200.webp';

    $thumbFs = $imagesDirFs . '/' . $thumbName;
    $mainFs = $imagesDirFs . '/' . $mainName;

    upload_resize_to_webp($src, $srcWidth, $srcHeight, 400, $thumbFs);
    upload_resize_to_webp($src, $srcWidth, $srcHeight, 1200, $mainFs);

    imagedestroy($src);

    $thumbWebPath = '/assets/images/' . $thumbName;
    $mainWebPath = '/assets/images/' . $mainName;

    $sql = "INSERT INTO article_images (article_id, image_kind, image_path, image_alt, sort_order)
            VALUES (:article_id, :image_kind, :image_path, :image_alt, 1)
            ON DUPLICATE KEY UPDATE image_path = VALUES(image_path), image_alt = VALUES(image_alt), sort_order = VALUES(sort_order)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        'article_id' => $articleId,
        'image_kind' => 'main',
        'image_path' => $mainWebPath,
        'image_alt' => $imageAlt !== '' ? $imageAlt : null,
    ));

    $stmt->execute(array(
        'article_id' => $articleId,
        'image_kind' => 'thumb',
        'image_path' => $thumbWebPath,
        'image_alt' => $imageAlt !== '' ? $imageAlt : null,
    ));

    return array('main' => $mainWebPath, 'thumb' => $thumbWebPath);
}
