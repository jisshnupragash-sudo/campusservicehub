<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['flash_error'] = 'Invalid service ID.';
    redirect('/campus-service-hub/dashboard.php');
}

$stmt = $conn->prepare('SELECT * FROM services WHERE id = ? LIMIT 1');

if (!$stmt) {
    $_SESSION['flash_error'] = 'Database error.';
    redirect('/campus-service-hub/dashboard.php');
}

$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$service = $result->fetch_assoc();
$stmt->close();

if (!$service) {
    $_SESSION['flash_error'] = 'Service not found.';
    redirect('/campus-service-hub/dashboard.php');
}

$user = current_user();

if (($user['role'] ?? '') !== 'admin' && (int)$service['user_id'] !== (int)$user['id']) {
    $_SESSION['flash_error'] = 'You cannot delete this service.';
    redirect('/campus-service-hub/dashboard.php');
}

$delete = $conn->prepare('DELETE FROM services WHERE id = ?');

if (!$delete) {
    $_SESSION['flash_error'] = 'Database error while deleting service.';
    redirect('/campus-service-hub/dashboard.php');
}

$delete->bind_param('i', $id);

if ($delete->execute()) {
    $delete->close();

    if (!empty($service['image'])) {
        $path = __DIR__ . '/../uploads/' . $service['image'];
        if (is_file($path)) {
            unlink($path);
        }
    }

    $_SESSION['flash_success'] = 'Service deleted successfully.';
} else {
    $delete->close();
    $_SESSION['flash_error'] = 'Failed to delete service.';
}

redirect('/campus-service-hub/dashboard.php');