<!-- github_block.php — красивый блок репозитория GitHub -->
<div class="github-card container my-5">
    <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, #0d1117 0%, #161b22 100%);">
        <div class="card-body text-center text-white p-5">
            <!-- Иконка GitHub + название -->
            <div class="d-flex justify-content-center align-items-center mb-4">
                <i class="fab fa-github fa-5x me-3" style="color: #ffffff; filter: drop-shadow(0 0 10px rgba(255,255,255,0.4));"></i>
                <h2 class="fw-bold mb-0" style="font-size: 2.8rem; letter-spacing: 1px;">
                    Tender-CMS
                </h2>
            </div>

            <!-- Описание -->
            <p class="lead mb-4" style="font-size: 1.25rem; opacity: 0.9;">
                Открытый исходный код нашей мощной CMS для управления сайтом и магазином.
            </p>

            <!-- Ключевые особенности (иконки + текст) -->
            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="feature-item p-4 rounded-3" style="background: rgba(255,255,255,0.05);">
                        <i class="fas fa-rocket fa-3x mb-3" style="color: #58a6ff;"></i>
                        <h5 class="fw-bold">Быстрый запуск</h5>
                        <p class="small">Установка за 5 минут</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-item p-4 rounded-3" style="background: rgba(255,255,255,0.05);">
                        <i class="fas fa-code-branch fa-3x mb-3" style="color: #f78166;"></i>
                        <h5 class="fw-bold">Открытый код</h5>
                        <p class="small">Полная свобода кастомизации</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-item p-4 rounded-3" style="background: rgba(255,255,255,0.05);">
                        <i class="fas fa-shield-alt fa-3x mb-3" style="color: #56d364;"></i>
                        <h5 class="fw-bold">Безопасность</h5>
                        <p class="small">Защита от атак + 2FA</p>
                    </div>
                </div>
            </div>

            <!-- Кнопки -->
            <div class="d-flex flex-column flex-md-row gap-3 justify-content-center">
                <a href="https://github.com/Ruslan-Bilohash/Tender-CMS" target="_blank" class="btn btn-lg btn-github d-flex align-items-center justify-content-center">
                    <i class="fab fa-github me-2 fs-3"></i> Перейти в репозиторий
                </a>
                <a href="https://github.com/Ruslan-Bilohash/Tender-CMS/stargazers" target="_blank" class="btn btn-lg btn-outline-light d-flex align-items-center justify-content-center">
                    <i class="fas fa-star me-2"></i> Поставить звезду
                </a>
            </div>

            <!-- GitHub-статистика (опционально, можно убрать) -->
            <div class="mt-5">
                <img src="https://img.shields.io/github/stars/Ruslan-Bilohash/Tender-CMS?style=social" alt="GitHub stars">
                <img src="https://img.shields.io/github/forks/Ruslan-Bilohash/Tender-CMS?style=social" alt="GitHub forks">
                <img src="https://img.shields.io/github/license/Ruslan-Bilohash/Tender-CMS?style=flat-square" alt="License">
            </div>
        </div>
    </div>
</div>

<!-- Стили для блока -->
<style>
    .github-card {
        max-width: 1100px;
        margin: 0 auto;
    }
    .btn-github {
        background: #21262d;
        color: white;
        border: 1px solid #30363d;
        transition: all 0.3s ease;
    }
    .btn-github:hover {
        background: #30363d;
        color: #58a6ff;
        border-color: #58a6ff;
        transform: translateY(-3px);
    }
    .feature-item {
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .feature-item:hover {
        background: rgba(255,255,255,0.1);
        transform: translateY(-5px);
    }
    @media (max-width: 768px) {
        .header h1 { font-size: 2.5rem; }
        .btn-lg { font-size: 1rem; padding: 0.75rem 1.5rem; }
    }
</style>