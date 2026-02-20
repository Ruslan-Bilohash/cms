<?php
// /templates/mobile/header.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
$settings = get_settings();

// Диагностика
if (!is_array($settings)) {
    error_log("Ошибка: настройки в mobile/header.php не являются массивом.");
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pro Website Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/templates/mobile/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>
    <header class="bg-light shadow-sm">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/">Pro Website Management</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/admin/login.php"><i class="fas fa-user-lock me-2"></i> Демо (admin/12345)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/news"><i class="fas fa-newspaper me-2"></i> Новости</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/add_tender"><i class="fas fa-plus me-2"></i> Добавить тендер</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/shop"><i class="fas fa-shopping-cart me-2"></i> Магазин</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/feedback"><i class="fas fa-envelope me-2"></i> Обратная связь</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/seo"><i class="fas fa-chart-line me-2"></i> SEO</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/templates"><i class="fas fa-paint-brush me-2"></i> Шаблоны</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/modules"><i class="fas fa-puzzle-piece me-2"></i> Модули</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/payment_gateways"><i class="fas fa-credit-card me-2"></i> Платежные шлюзы</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/api"><i class="fas fa-code me-2"></i> API</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?force_mobile=0"><i class="fas fa-desktop me-2"></i> Десктопная версия</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6B46C1, #4C51BF);
            --accent-color: #ED8936;
            --text-color: #2D3748;
            --bg-color: #FFFFFF;
        }
        .navbar {
            font-family: 'Inter', sans-serif;
            background: var(--bg-color);
        }
        .navbar-brand {
            font-size: 1.4rem;
            color: var(--text-color);
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .navbar-nav .nav-link {
            font-size: 0.95rem;
            font-weight: 500;
            color: var(--text-color);
            padding: 8px 12px;
            border-radius: 8px;
            transition: background 0.2s, transform 0.2s;
        }
        .navbar-nav .nav-link:hover {
            background: var(--accent-color);
            color: white;
            transform: translateY(-2px);
        }
        .navbar-nav .nav-link i {
            margin-right: 8px;
            font-size: 1.1rem;
        }
        .navbar-toggler {
            border: none;
            padding: 5px;
        }
        .navbar-toggler:focus {
            box-shadow: none;
        }
        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.2rem;
            }
            .navbar-nav .nav-link {
                font-size: 0.9rem;
                padding: 10px 15px;
            }
            .navbar-collapse {
                background: var(--bg-color);
                border-radius: 8px;
                margin-top: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
        }
    </style>