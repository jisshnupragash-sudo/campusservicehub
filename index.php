<?php
require_once __DIR__ . '/config/database.php';
include __DIR__ . '/includes/header.php';

$baseUrl = '/campus-service-hub/';
$featuredServices = [];

$sql = "
    SELECT 
        services.id,
        services.title,
        services.description,
        services.price,
        services.image,
        services.created_at,
        users.name AS user_name
    FROM services
    INNER JOIN users ON services.user_id = users.id
    ORDER BY services.created_at DESC
    LIMIT 6
";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $featuredServices[] = $row;
    }
}

function formatImagePath(?string $image): string
{
    if (!empty($image) && file_exists(__DIR__ . '/uploads/' . $image)) {
        return '/campus-service-hub/uploads/' . rawurlencode($image);
    }

    return 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=900&q=80';
}
?>

<style>
    .hero-section {
        padding: 72px 0 56px;
        background:
            linear-gradient(to right, rgba(255,255,255,0.96), rgba(255,255,255,0.82)),
            url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1400&q=80') center/cover no-repeat;
        border-bottom: 1px solid #e5e7eb;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        color: #111111;
        border-radius: 999px;
        padding: 10px 16px;
        font-size: 0.9rem;
        font-weight: 600;
        box-shadow: 0 8px 24px rgba(0,0,0,0.04);
        margin-bottom: 18px;
    }

    .hero-title {
        font-size: clamp(2.4rem, 5vw, 4.8rem);
        line-height: 1.03;
        font-weight: 800;
        letter-spacing: -1.5px;
        max-width: 700px;
        margin-bottom: 18px;
    }

    .hero-text {
        font-size: 1.08rem;
        color: #6b7280;
        max-width: 620px;
        margin-bottom: 28px;
    }

    .hero-actions {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
        margin-bottom: 34px;
    }

    .featured-section {
        padding: 80px 0 20px;
    }

    .service-card {
        background: #fff;
        border-radius: 22px;
        overflow: hidden;
        border: 1px solid #ececec;
        box-shadow: 0 8px 24px rgba(0,0,0,0.05);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        height: 100%;
    }

    .service-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 18px 40px rgba(0,0,0,0.09);
    }

    .service-image-wrap {
        position: relative;
        aspect-ratio: 4 / 5;
        overflow: hidden;
        background: #f3f4f6;
    }

    .service-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }

    .service-card:hover .service-image-wrap img {
        transform: scale(1.05);
    }

    .service-tag {
        position: absolute;
        top: 14px;
        left: 14px;
        background: rgba(17,17,17,0.88);
        color: #fff;
        border-radius: 999px;
        padding: 7px 12px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .service-body {
        padding: 18px;
    }

    .service-seller {
        font-size: 0.9rem;
        color: #6b7280;
        margin-bottom: 8px;
    }

    .service-title {
        font-size: 1.08rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: #111;
    }

    .service-description {
        color: #6b7280;
        font-size: 0.94rem;
        min-height: 48px;
        margin-bottom: 16px;
    }

    .service-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .service-price {
        font-size: 1.1rem;
        font-weight: 800;
    }

    .categories-section,
    .why-section,
    .cta-section {
        padding: 80px 0;
    }

    .category-card {
        background: #fafafa;
        border: 1px solid #ececec;
        border-radius: 22px;
        padding: 24px;
        height: 100%;
        transition: 0.25s ease;
    }

    .category-card:hover {
        background: #fff;
        transform: translateY(-5px);
        box-shadow: 0 14px 34px rgba(0,0,0,0.06);
    }

    .category-card i {
        font-size: 1.5rem;
        margin-bottom: 14px;
        display: inline-block;
    }

    .category-card h5 {
        font-weight: 700;
        margin-bottom: 10px;
    }

    .category-card p {
        color: #6b7280;
        margin-bottom: 0;
    }

    .why-card {
        background: #fff;
        border: 1px solid #ececec;
        border-radius: 22px;
        padding: 28px;
        height: 100%;
        box-shadow: 0 8px 24px rgba(0,0,0,0.04);
    }

    .why-card .icon-wrap {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        background: #111;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 16px;
    }

    .cta-box {
        background: linear-gradient(135deg, #111111 0%, #1d1d1d 100%);
        border-radius: 32px;
        padding: 48px 32px;
        color: #fff;
        text-align: center;
        box-shadow: 0 24px 60px rgba(0,0,0,0.18);
    }

    .cta-box p {
        color: rgba(255,255,255,0.78);
        max-width: 720px;
        margin: 0 auto 22px;
    }

    .empty-state {
        background: #fafafa;
        border: 1px dashed #d1d5db;
        border-radius: 22px;
        padding: 40px 24px;
        text-align: center;
        color: #6b7280;
    }

    @media (max-width: 991.98px) {
        .hero-section {
            padding: 56px 0 40px;
        }

        .hero-panel img {
            height: 340px;
        }

        .quick-strip-inner {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 575.98px) {
        .quick-strip-inner {
            grid-template-columns: 1fr;
        }

        .hero-title {
            letter-spacing: -0.8px;
        }

        .hero-stat-card {
            width: 100%;
        }
    }
</style>

<section class="hero-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <div class="hero-badge">
                    <i class="bi bi-stars"></i>
                    Student Skills & Services Platform
                </div>

                <h1 class="hero-title">
                    Discover campus services with a cleaner, smarter marketplace feel.
                </h1>

                <p class="hero-text">
                    Find tutoring, graphic design, tech help, repairs, writing support, and more —
                    all in one polished platform built for your campus community.
                </p>

                <div class="hero-actions">
                    <a href="search.php" class="btn btn-dark-custom btn-lg">
                        Explore Services
                    </a>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="register.php" class="btn btn-outline-dark-custom btn-lg">
                            Create Account
                        </a>
                    <?php else: ?>
                        <a href="dashboard.php" class="btn btn-outline-dark-custom btn-lg">
                            Go to Dashboard
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="hero-panel">
                    <img
                        src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80"
                        alt="Campus Service Hub"
                    >
                </div>
            </div>
        </div>
    </div>
</section>
<section class="featured-section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
            <div>
                <h2 class="section-title">Featured Services</h2>
                <p class="section-subtitle mb-0">
                    Fresh listings from students offering useful skills and campus services.
                </p>
            </div>
            <a href="search.php" class="btn btn-outline-dark-custom">View All</a>
        </div>

        <div class="row g-4">
            <?php if (!empty($featuredServices)): ?>
                <?php foreach ($featuredServices as $service): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="service-card">
                            <div class="service-image-wrap">
                                <span class="service-tag">Featured</span>
                                <img
                                    src="<?= htmlspecialchars(formatImagePath($service['image'] ?? '')); ?>"
                                    alt="<?= htmlspecialchars($service['title']); ?>"
                                >
                            </div>

                            <div class="service-body">
                                <div class="service-seller">
                                    <i class="bi bi-person-circle me-1"></i>
                                    <?= htmlspecialchars($service['user_name']); ?>
                                </div>

                                <div class="service-title">
                                    <?= htmlspecialchars($service['title']); ?>
                                </div>

                                <div class="service-description">
                                    <?= htmlspecialchars(mb_strimwidth($service['description'], 0, 95, '...')); ?>
                                </div>

                                <div class="service-footer">
                                    <span class="service-price">
                                        RM <?= number_format((float)$service['price'], 2); ?>
                                    </span>
                                    <a href="search.php" class="btn btn-dark-custom btn-sm">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-state">
                        <h5 class="mb-2">No services yet</h5>
                        <p class="mb-3">Be the first to add a service and start building your campus marketplace.</p>
                        <a href="auth/login.php" class="btn btn-dark-custom">Get Started</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="categories-section">
    <div class="container">
        <div class="mb-4">
            <h2 class="section-title">Popular Categories</h2>
            <p class="section-subtitle">
                Organize your platform like a real marketplace so users can scan services quickly.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="category-card">
                    <i class="bi bi-journal-code"></i>
                    <h5>Academic Help</h5>
                    <p>Tutoring, study support, assignment guidance, and revision sessions.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="category-card">
                    <i class="bi bi-palette"></i>
                    <h5>Creative Design</h5>
                    <p>Posters, logos, social media graphics, and presentation design work.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="category-card">
                    <i class="bi bi-laptop"></i>
                    <h5>Tech Services</h5>
                    <p>Basic coding help, device setup, troubleshooting, and digital support.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="category-card">
                    <i class="bi bi-tools"></i>
                    <h5>Campus Support</h5>
                    <p>Errands, repairs, printing help, and other practical student services.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="cta-section">
    <div class="container">
        <div class="cta-box">
            <h2 class="fw-bold mb-3">Start listing your campus services today</h2>
            <p>
                Build a cleaner portfolio-worthy project with better layout, stronger visual hierarchy,
                and a more premium storefront-style homepage.
            </p>

            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="search.php" class="btn btn-light btn-lg rounded-pill px-4">Browse Services</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="register.php" class="btn btn-outline-light btn-lg rounded-pill px-4">Create Account</a>
                <?php else: ?>
                    <a href="dashboard.php" class="btn btn-outline-light btn-lg rounded-pill px-4">Manage Listings</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>