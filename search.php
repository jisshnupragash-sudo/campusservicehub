
<?php
require_once __DIR__ . '/config/database.php';
include __DIR__ . '/includes/header.php';

$baseUrl = '/campus-service-hub/';
?>

<style>
    .search-page {
        padding: 50px 0 70px;
        background: #fafafa;
        min-height: 100vh;
    }

    .search-hero {
        background: #ffffff;
        border: 1px solid #ececec;
        border-radius: 28px;
        padding: 32px;
        margin-bottom: 32px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    }

    .search-title {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .search-subtitle {
        color: #6b7280;
        margin-bottom: 22px;
    }

    .search-input {
        border-radius: 999px;
        padding: 15px 22px;
        border: 1px solid #dcdfe4;
        font-size: 1rem;
        box-shadow: none !important;
    }

    .search-input:focus {
        border-color: #111;
    }

    .results-info {
        margin-bottom: 18px;
        color: #6b7280;
        font-weight: 500;
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
        aspect-ratio: 4 / 5;
        overflow: hidden;
        background: #f3f4f6;
    }

    .service-image-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
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

    .empty-state {
        background: #fff;
        border: 1px dashed #d1d5db;
        border-radius: 22px;
        padding: 50px 24px;
        text-align: center;
        color: #6b7280;
    }

    .loading-text {
        color: #6b7280;
        font-weight: 500;
        padding: 10px 0;
    }
</style>

<section class="search-page">
    <div class="container">
        <div class="search-hero">
            <h1 class="search-title">Explore Services</h1>
            <p class="search-subtitle">Search for tutoring, design, repair, writing, coding, and more.</p>

            <input
                type="text"
                id="liveSearch"
                class="form-control search-input"
                placeholder="Search services..."
                autocomplete="off"
            >
        </div>

        <div id="resultsInfo" class="results-info">Showing latest services</div>
        <div id="loadingMessage" class="loading-text" style="display:none;">Searching...</div>
        <div id="searchResults" class="row g-4"></div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('liveSearch');
    const searchResults = document.getElementById('searchResults');
    const resultsInfo = document.getElementById('resultsInfo');
    const loadingMessage = document.getElementById('loadingMessage');

    let debounceTimer;

    function loadServices(query = '') {
        loadingMessage.style.display = 'block';

        fetch('<?= $baseUrl ?>ajax/search_services.php?q=' + encodeURIComponent(query))
            .then(response => response.text())
            .then(data => {
                searchResults.innerHTML = data;
                loadingMessage.style.display = 'none';

                if (query.trim() === '') {
                    resultsInfo.textContent = 'Showing latest services';
                } else {
                    resultsInfo.textContent = 'Search results for: "' + query + '"';
                }
            })
            .catch(error => {
                loadingMessage.style.display = 'none';
                searchResults.innerHTML = `
                    <div class="col-12">
                        <div class="empty-state">
                            <h5 class="mb-2">Search failed</h5>
                            <p class="mb-0">Please check your AJAX path or PHP search file.</p>
                        </div>
                    </div>
                `;
                console.error('Search error:', error);
            });
    }

    loadServices();

    searchInput.addEventListener('keyup', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            loadServices(searchInput.value);
        }, 300);
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>