<?php
require_once __DIR__ . '/../config/session.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header("Location: $path");
    exit;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']);
}

function current_user(): ?array
{
    if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
        return $_SESSION['user'];
    }

    return null;
}

function require_login(): void
{
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Please log in first.';
        redirect('/campus-service-hub/auth/login.php');
    }
}

function require_role(string $role): void
{
    require_login();

    $user = current_user();
    if (($user['role'] ?? '') !== $role) {
        $_SESSION['flash_error'] = 'Unauthorized access.';
        redirect('/campus-service-hub/dashboard.php');
    }
}

function flash_message(): string
{
    $output = '';

    if (!empty($_SESSION['flash_success'])) {
        $output .= '<div class="alert alert-success">' . e($_SESSION['flash_success']) . '</div>';
        unset($_SESSION['flash_success']);
    }

    if (!empty($_SESSION['flash_error'])) {
        $output .= '<div class="alert alert-danger">' . e($_SESSION['flash_error']) . '</div>';
        unset($_SESSION['flash_error']);
    }

    return $output;
}

function old(string $key, string $default = ''): string
{
    return e($_POST[$key] ?? $default);
}

function validate_image_upload(array $file, array &$errors): ?string
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Image is required.';
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Image upload failed.';
        return null;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        $errors[] = 'Image must be less than 2MB.';
        return null;
    }

    $allowedMime = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png'
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowedMime[$mime])) {
        $errors[] = 'Only JPG and PNG files are allowed.';
        return null;
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $allowedMime[$mime];
    $destination = __DIR__ . '/../uploads/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $errors[] = 'Unable to save uploaded image.';
        return null;
    }

    return $filename;
}

function regenerate_session_once(): void
{
    if (!isset($_SESSION['session_regenerated'])) {
        session_regenerate_id(true);
        $_SESSION['session_regenerated'] = true;
    }
}