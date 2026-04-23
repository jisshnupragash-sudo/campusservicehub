<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '') {
        $errors[] = 'Name is required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        $checkStmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');

        if ($checkStmt) {
            $checkStmt->bind_param('s', $email);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->fetch_assoc()) {
                $errors[] = 'Email is already registered.';
            }

            $checkStmt->close();
        } else {
            $errors[] = 'Database error while checking email.';
        }
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';

        $insertStmt = $conn->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');

        if ($insertStmt) {
            $insertStmt->bind_param('ssss', $name, $email, $hashedPassword, $role);

            if ($insertStmt->execute()) {
                $userId = $insertStmt->insert_id;
                $insertStmt->close();

                $_SESSION['user'] = [
                    'id' => $userId,
                    'name' => $name,
                    'email' => $email,
                    'role' => $role
                ];

                regenerate_session_once();
                $_SESSION['flash_success'] = 'Registration successful. Welcome, ' . $name . '!';
                redirect('/campus-service-hub/dashboard.php');
                exit;
            } else {
                $errors[] = 'Failed to register account.';
                $insertStmt->close();
            }
        } else {
            $errors[] = 'Database error while creating account.';
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="mb-3">Create Account</h2>

                <?php if ($errors): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="<?= old('name') ?>"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?= old('email') ?>"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input
                            type="password"
                            name="confirm_password"
                            class="form-control"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>