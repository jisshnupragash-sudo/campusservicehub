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
    $_SESSION['flash_error'] = 'You cannot edit this service.';
    redirect('/campus-service-hub/dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $imageName = $service['image'];

    if ($title === '') {
        $errors[] = 'Title is required.';
    }

    if ($description === '') {
        $errors[] = 'Description is required.';
    }

    if ($price === '' || !is_numeric($price) || (float)$price < 0) {
        $errors[] = 'Valid price is required.';
    }

    if (!empty($_FILES['image']['name'])) {
        $newImage = validate_image_upload($_FILES['image'], $errors);

        if ($newImage) {
            $oldPath = __DIR__ . '/../uploads/' . $service['image'];
            if (!empty($service['image']) && is_file($oldPath)) {
                unlink($oldPath);
            }
            $imageName = $newImage;
        }
    }

    if (empty($errors)) {
        $update = $conn->prepare('UPDATE services SET title = ?, description = ?, price = ?, image = ? WHERE id = ?');

        if ($update) {
            $priceValue = (float) $price;
            $update->bind_param('ssdsi', $title, $description, $priceValue, $imageName, $id);

            if ($update->execute()) {
                $update->close();
                $_SESSION['flash_success'] = 'Service updated successfully.';
                redirect('/campus-service-hub/dashboard.php');
            } else {
                $errors[] = 'Failed to update service.';
                $update->close();
            }
        } else {
            $errors[] = 'Database error while updating service.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="mb-3">Edit Service</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input
                            type="text"
                            name="title"
                            class="form-control"
                            value="<?= e($_POST['title'] ?? $service['title']) ?>"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea
                            name="description"
                            class="form-control"
                            rows="4"
                            required
                        ><?= e($_POST['description'] ?? $service['description']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price (RM)</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="price"
                            class="form-control"
                            value="<?= e($_POST['price'] ?? (string)$service['price']) ?>"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Replace Image (optional)</label>
                        <input
                            type="file"
                            name="image"
                            class="form-control image-input"
                            accept=".jpg,.jpeg,.png"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">Update Service</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>