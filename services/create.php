<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');

    if ($title === '') {
        $errors[] = 'Title is required.';
    }

    if ($description === '') {
        $errors[] = 'Description is required.';
    }

    if ($price === '' || !is_numeric($price) || (float)$price < 0) {
        $errors[] = 'Valid price is required.';
    }

    $imageName = validate_image_upload($_FILES['image'] ?? [], $errors);

    if (empty($errors)) {
        $sql = "INSERT INTO services (user_id, title, description, price, image, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())";

        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $userId = (int) current_user()['id'];
            $priceValue = (float) $price;

            $stmt->bind_param('issds', $userId, $title, $description, $priceValue, $imageName);

            if ($stmt->execute()) {
                $stmt->close();
                $_SESSION['flash_success'] = 'Service created successfully.';
                redirect('/campus-service-hub/dashboard.php');
            } else {
                $errors[] = 'Failed to create service.';
                $stmt->close();
            }
        } else {
            $errors[] = 'Database error while creating service.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 p-lg-5">
                <div class="mb-4">
                    <h2 class="mb-1">Add Service</h2>
                    <p class="text-muted mb-0">Create a new service listing for your campus marketplace.</p>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" value="<?= old('title') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="5" required><?= old('description') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price (RM)</label>
                        <input type="number" name="price" class="form-control" min="0" step="0.01" value="<?= old('price') ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required>
                        <div class="form-text">Only JPG and PNG files, maximum 2MB.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark">Create Service</button>
                        <a href="/campus-service-hub/dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>