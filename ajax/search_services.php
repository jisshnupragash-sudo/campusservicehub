<?php
require_once __DIR__ . '/../config/database.php';

$q = trim($_GET['q'] ?? '');

$sql = "
    SELECT 
        services.id,
        services.title,
        services.description,
        services.price,
        services.image,
        users.name AS user_name
    FROM services
    INNER JOIN users ON services.user_id = users.id
";

$params = [];
$types = '';

if ($q !== '') {
    $sql .= " WHERE services.title LIKE ? OR services.description LIKE ? OR users.name LIKE ? ";
    $searchTerm = '%' . $q . '%';
    $params = [$searchTerm, $searchTerm, $searchTerm];
    $types = 'sss';
}

$sql .= " ORDER BY services.id DESC ";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo '<div class="col-12"><p>SQL error: ' . htmlspecialchars($conn->error) . '</p></div>';
    exit;
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

function serviceImagePath($image)
{
    if (!empty($image) && file_exists(__DIR__ . '/../uploads/' . $image)) {
        return '/campus-service-hub/uploads/' . rawurlencode($image);
    }

    return 'https://via.placeholder.com/600x750?text=No+Image';
}

if ($result && $result->num_rows > 0) {
    while ($service = $result->fetch_assoc()) {
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="service-card">
                <div class="service-image-wrap">
                    <img
                        src="<?= htmlspecialchars(serviceImagePath($service['image'] ?? '')); ?>"
                        alt="<?= htmlspecialchars($service['title']); ?>"
                    >
                </div>
                <div class="service-body">
                    <div class="service-seller"><?= htmlspecialchars($service['user_name']); ?></div>
                    <div class="service-title"><?= htmlspecialchars($service['title']); ?></div>
                    <div class="service-description">
                        <?= htmlspecialchars(mb_strimwidth($service['description'], 0, 95, '...')); ?>
                    </div>
                    <div class="service-footer">
                        <span class="service-price">RM <?= number_format((float)$service['price'], 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    echo '<div class="col-12"><p>No services found.</p></div>';
}

$stmt->close();
$conn->close();