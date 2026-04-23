<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
require_once __DIR__ . '/includes/header.php';

$user = current_user();
$services = [];

if ($user['role'] === 'admin') {
    $sql = "SELECT services.*, users.name AS owner_name
            FROM services
            JOIN users ON services.user_id = users.id
            ORDER BY services.created_at DESC";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $services[] = $row;
        }
    }
} else {
    $sql = "SELECT services.*, users.name AS owner_name
            FROM services
            JOIN users ON services.user_id = users.id
            WHERE services.user_id = ?
            ORDER BY services.created_at DESC";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('i', $user['id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $services[] = $row;
            }
        }

        $stmt->close();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Dashboard</h2>
        <p class="text-muted mb-0">Manage your services here.</p>
    </div>
    <a href="services/create.php" class="btn btn-primary">Add Service</a>
</div>

<div class="card shadow-sm">
    <div class="card-body table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Price</th>
                    <th>Owner</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($services)): ?>
                    <tr>
                        <td colspan="6" class="text-center">No services found.</td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($services as $service): ?>
                    <tr>
                        <td>
                            <img
                                src="uploads/<?= e($service['image']) ?>"
                                class="thumb"
                                alt="<?= e($service['title']) ?>"
                            >
                        </td>
                        <td><?= e($service['title']) ?></td>
                        <td>RM <?= e(number_format((float)$service['price'], 2)) ?></td>
                        <td><?= e($service['owner_name']) ?></td>
                        <td><?= e($service['created_at']) ?></td>
                        <td>
                            <a href="services/edit.php?id=<?= e((string)$service['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="services/delete.php?id=<?= e((string)$service['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this service?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>