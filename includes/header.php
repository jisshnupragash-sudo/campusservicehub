<?php
require_once __DIR__ . '/../config/session.php';

$currentPage = basename($_SERVER['PHP_SELF']);
$baseUrl = '/campus-service-hub/';

function isActive(string $page, string $currentPage): string
{
    return $page === $currentPage ? 'active-nav' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Service Hub</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">

    <style>
        :root {
            --bg: #ffffff;
            --text: #111111;
            --muted: #6b7280;
            --border: #e5e7eb;
            --accent: #111111;
            --accent-hover: #2c2c2c;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .custom-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
            padding: 14px 0;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--text) !important;
            letter-spacing: 0.3px;
        }

        .navbar-brand span {
            color: #7c7c7c;
            font-weight: 600;
        }

        .nav-link {
            color: var(--text) !important;
            font-weight: 500;
            padding: 8px 14px !important;
            border-radius: 999px;
            transition: 0.25s ease;
        }

        .nav-link:hover {
            background: #f3f4f6;
        }

        .active-nav {
            background: #111111;
            color: #ffffff !important;
        }

        .btn-dark-custom {
            background: var(--accent);
            color: #fff;
            border: 1px solid var(--accent);
            border-radius: 999px;
            padding: 11px 22px;
            font-weight: 600;
            transition: 0.25s ease;
        }

        .btn-dark-custom:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
            color: #fff;
        }

        .user-badge {
            background: #f3f4f6;
            color: #111;
            border: 1px solid #e5e7eb;
            border-radius: 999px;
            padding: 10px 14px;
            font-size: 0.92rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg custom-navbar sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= $baseUrl ?>index.php">
            Campus <span>Service Hub</span>
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link <?= isActive('index.php', $currentPage); ?>" href="<?= $baseUrl ?>index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= isActive('search.php', $currentPage); ?>" href="<?= $baseUrl ?>search.php">Explore</a>
                </li>

                <?php if (isset($_SESSION['user']) && is_array($_SESSION['user'])): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('dashboard.php', $currentPage); ?>" href="<?= $baseUrl ?>dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <span class="user-badge">
                            <?= htmlspecialchars($_SESSION['user']['role'] ?? 'User'); ?>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-dark-custom ms-lg-2" href="<?= $baseUrl ?>auth/logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('login.php', $currentPage); ?>" href="<?= $baseUrl ?>auth/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-dark-custom ms-lg-2" href="<?= $baseUrl ?>auth/register.php">Join Now</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>