<?php
declare(strict_types=1);

$pageTitle = 'Help & FAQ Center - Practical 6 Fetch API';
$activePage = 'support';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase mb-2">
                <i class="bi bi-cpu-fill me-1"></i> Practical 6 Integration
            </span>
            <h1 class="fw-bold">Help & Knowledge Base</h1>
            <p class="text-muted">
                Demonstrating asynchronous JSON retrieval via Fetch API with live search, category filtering, sorting, and pagination.
            </p>
        </div>

        <!-- Practical 6 Control Toolbar -->
        <div class="card card-sh p-4 mb-4 border-0">
            <div class="row g-3 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="faqSearch" class="form-control border-start-0" placeholder="Search questions or keywords...">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <select id="faqCategoryFilter" class="form-select">
                        <option value="ALL">All Categories</option>
                        <option value="Academic">Academic</option>
                        <option value="Admissions">Admissions</option>
                        <option value="Events">Events</option>
                        <option value="Account">Account</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="faqSort" class="form-select">
                        <option value="views_desc">Most Popular (Views)</option>
                        <option value="views_asc">Least Popular</option>
                        <option value="id_asc">Default Order</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Loading & Status Indicators -->
        <div id="faqLoading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading FAQs...</span>
            </div>
            <p class="text-muted mt-2 small">Loading FAQs via Fetch API from <code>data/faqs.json</code>...</p>
        </div>

        <div id="faqError" class="alert alert-danger d-none" role="alert"></div>

        <!-- Render Target Accordion -->
        <div class="accordion mb-4" id="faqAccordion"></div>

        <!-- Pagination Controls -->
        <div id="faqPaginationContainer" class="d-flex justify-content-between align-items-center d-none">
            <small class="text-muted" id="faqPageCountText">Showing 1 to 5</small>
            <nav aria-label="FAQ Pagination">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item" id="faqPrevLi">
                        <button class="page-link" id="faqPrevBtn"><i class="bi bi-chevron-left"></i> Previous</button>
                    </li>
                    <li class="page-item" id="faqNextLi">
                        <button class="page-link" id="faqNextBtn">Next <i class="bi bi-chevron-right"></i></button>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</main>

<!-- Practical 6 Fetch API Script -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const jsonUrl = "<?= APP_URL ?>/data/faqs.json";
    let allFaqs = [];
    let filteredFaqs = [];
    let currentPage = 1;
    const itemsPerPage = 4;

    const faqAccordion = document.getElementById("faqAccordion");
    const faqLoading = document.getElementById("faqLoading");
    const faqError = document.getElementById("faqError");
    const searchInput = document.getElementById("faqSearch");
    const categoryFilter = document.getElementById("faqCategoryFilter");
    const sortSelect = document.getElementById("faqSort");
    const paginationContainer = document.getElementById("faqPaginationContainer");
    const pageCountText = document.getElementById("faqPageCountText");
    const prevLi = document.getElementById("faqPrevLi");
    const nextLi = document.getElementById("faqNextLi");
    const prevBtn = document.getElementById("faqPrevBtn");
    const nextBtn = document.getElementById("faqNextBtn");

    // Fetch JSON from server
    async function loadFaqs() {
        faqLoading.classList.remove("d-none");
        faqError.classList.add("d-none");
        faqAccordion.innerHTML = "";

        try {
            const response = await fetch(jsonUrl, { cache: "no-store" });
            if (!response.ok) {
                throw new Error("HTTP error " + response.status + ": Unable to fetch JSON file.");
            }
            allFaqs = await response.json();
            faqLoading.classList.add("d-none");
            applyFilterAndRender();
        } catch (err) {
            faqLoading.classList.add("d-none");
            faqError.classList.remove("d-none");
            faqError.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Fetch API Error:</strong> ' + err.message;
        }
    }

    // Filter, sort, and slice for pagination
    function applyFilterAndRender() {
        const query = searchInput.value.toLowerCase().trim();
        const selectedCat = categoryFilter.value;
        const sortMode = sortSelect.value;

        filteredFaqs = allFaqs.filter(item => {
            const matchesQuery = item.question.toLowerCase().includes(query) ||
                                 item.answer.toLowerCase().includes(query);
            const matchesCat = (selectedCat === "ALL") || (item.category === selectedCat);
            return matchesQuery && matchesCat;
        });

        // Sorting
        if (sortMode === "views_desc") {
            filteredFaqs.sort((a, b) => b.views - a.views);
        } else if (sortMode === "views_asc") {
            filteredFaqs.sort((a, b) => a.views - b.views);
        } else {
            filteredFaqs.sort((a, b) => a.id - b.id);
        }

        renderPage();
    }

    // Render current pagination slice
    function renderPage() {
        const totalItems = filteredFaqs.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIndex = (currentPage - 1) * itemsPerPage;
        const currentItems = filteredFaqs.slice(startIndex, startIndex + itemsPerPage);

        faqAccordion.innerHTML = "";

        if (totalItems === 0) {
            faqAccordion.innerHTML = '<div class="alert alert-secondary text-center py-4">No matching questions found for your search.</div>';
            paginationContainer.classList.add("d-none");
            return;
        }

        currentItems.forEach((faq, index) => {
            const itemHtml = `
                <div class="accordion-item card-sh mb-3 border-0 overflow-hidden">
                    <h2 class="accordion-header" id="heading${faq.id}">
                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${faq.id}" aria-expanded="false" aria-controls="collapse${faq.id}">
                            <span class="badge bg-primary-subtle text-primary me-2">${faq.category}</span>
                            ${faq.question}
                        </button>
                    </h2>
                    <div id="collapse${faq.id}" class="accordion-collapse collapse" aria-labelledby="heading${faq.id}" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            <p class="mb-2">${faq.answer}</p>
                            <small class="text-secondary"><i class="bi bi-eye me-1"></i>${faq.views} views &bull; Updated: ${faq.updated}</small>
                        </div>
                    </div>
                </div>
            `;
            faqAccordion.insertAdjacentHTML("beforeend", itemHtml);
        });

        // Update pagination UI
        paginationContainer.classList.remove("d-none");
        pageCountText.textContent = `Showing ${startIndex + 1} to ${Math.min(startIndex + itemsPerPage, totalItems)} of ${totalItems} questions`;

        prevLi.classList.toggle("disabled", currentPage === 1);
        nextLi.classList.toggle("disabled", currentPage === totalPages);
    }

    // Event listeners
    searchInput.addEventListener("input", function () {
        currentPage = 1;
        applyFilterAndRender();
    });

    categoryFilter.addEventListener("change", function () {
        currentPage = 1;
        applyFilterAndRender();
    });

    sortSelect.addEventListener("change", function () {
        applyFilterAndRender();
    });

    prevBtn.addEventListener("click", function () {
        if (currentPage > 1) {
            currentPage--;
            renderPage();
        }
    });

    nextBtn.addEventListener("click", function () {
        const totalPages = Math.ceil(filteredFaqs.length / itemsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            renderPage();
        }
    });

    // Initial load
    loadFaqs();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

