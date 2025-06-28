<?php

function isValidFile($file): bool
{
    $allowedTypes = [
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
        'application/pdf',                                                         // .pdf
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' // .pptx
    ];

    if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
        return false;
    }
    $mimeType = mime_content_type($file['tmp_name']);
    return $file['error'] === UPLOAD_ERR_OK && in_array($mimeType, $allowedTypes);
}


function sanitizeFileName($filename): string
{
    $basename = basename($filename);
    return preg_replace('/[^a-zA-Z0-9-_\.]/', '', $basename);
}

function createUploadDirectory($path): bool
{
    if (!file_exists($path)) {
        return mkdir($path, 0755, true);
    }
    return true;
}

function saveUploadedFile($file, $directory, $prefix = ''): ?string
{
    $safeName = sanitizeFileName($file['name']);
    $uniqueName = $prefix . uniqid() . '_' . $safeName;
    $targetPath = rtrim($directory, '/') . '/' . $uniqueName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $targetPath;
    }

    return null;
}
