<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
if (!isAdmin()) {
    header("Location: /admin/login.php");
    exit;
}
// Подсчёт новых заказов
$new_orders_stmt = $conn->query("SELECT COUNT(*) FROM shop_orders WHERE status = 'ожидает'");
$new_orders_count = $new_orders_stmt->fetch_row()[0];

// Подсчёт непрочитанных сообщений
$unread_count = $conn->query("SELECT COUNT(*) FROM feedback WHERE type = 'message' AND is_read = 0")->fetch_row()[0];

// ==================== ВОЗВРАЩЁННАЯ СИСТЕМА ПЕРЕВОДОВ ====================
$tr = load_admin_translations();   // Загружаем переводы (как было раньше)
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="robots" content="noindex, nofollow">
    <title>Администратор Pro Website</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <link rel="stylesheet" href="/admin/css/style.css">
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --sidebar-width: 280px;
        }
        body {
            background: #f8f9fa;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .top-navbar {
            height: 70px;
            z-index: 1035;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            left: 0;
            top: 70px;
            bottom: 0;
            z-index: 1020;
            transition: all 0.35s cubic-bezier(0.4, 0.0, 0.2, 1);
            box-shadow: 3px 0 25px rgba(0,0,0,0.12);
            overflow-y: auto;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: 80px;
            min-height: 100vh;
        }
        @media (max-width: 991.98px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
                padding-top: 80px;
            }
        }
        .nav-link {
            padding: 14px 22px !important;
            border-radius: 14px;
            margin: 4px 14px;
            font-weight: 500;
            color: #e0e0e0;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            transform: translateX(8px);
            color: #fff;
        }
        .nav-link.active {
            background: linear-gradient(90deg, #0d6efd, #3b82f6) !important;
            color: #fff !important;
            box-shadow: 0 6px 20px rgba(13, 110, 253, 0.4);
        }
        .dropdown-menu {
            background: #1f2937;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border-radius: 14px;
            padding: 8px;
        }
        .dropdown-item {
            padding: 12px 24px;
            border-radius: 10px;
            color: #e0e0e0;
        }
        .dropdown-item:hover {
            background: rgba(59, 130, 246, 0.2);
        }
        .badge {
            font-size: 0.73rem;
            padding: 5px 10px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.12); }
        }
        .offcanvas {
            width: 300px !important;
        }
        .offcanvas-header {
            background: linear-gradient(135deg, #1f2937, #111827);
        }
        .lang-flag {
            font-size: 1.3rem;
            line-height: 1;
        }
    </style>
</head>
<body data-bs-theme="dark">

    <!-- TOP NAV BAR (работает на всех устройствах) -->
    <nav class="navbar navbar-dark bg-dark top-navbar fixed-top">
        <div class="container-fluid px-3">
            <div class="d-flex align-items-center gap-3">
                <!-- Кнопка меню для мобильных и планшетов -->
                <button class="btn btn-dark p-2 d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
                    <i class="bi bi-list fs-3"></i>
                </button>
                
                <div class="d-flex align-items-center gap-2">
                    <span class="fs-3 text-warning">🚀</span>
                    <h4 class="mb-0 fw-bold text-white d-none d-md-block"><?= $tr['admin_title'] ?? 'Pro Website' ?></h4>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="/" class="btn btn-outline-light btn-sm d-flex align-items-center gap-2" target="_blank">
                    <i class="bi bi-globe2"></i>
                    <span class="d-none d-md-inline"><?= $tr['to_site'] ?? 'На сайт' ?></span>
                </a>

                <?php if ($new_orders_count > 0): ?>
                <a href="/admin/index.php?module=shop_order" class="btn btn-warning btn-sm position-relative">
                    <i class="bi bi-bag-fill"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $new_orders_count ?></span>
                </a>
                <?php endif; ?>

                <a href="/admin/index.php?module=feedback" class="btn btn-outline-light btn-sm position-relative">
                    <i class="bi bi-envelope"></i>
                    <?php if ($unread_count > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $unread_count ?></span>
                    <?php endif; ?>
                </a>

                <!-- Кнопка смены языка (кнопки + спойлер) -->
                <div class="dropdown">
                    <button class="btn btn-outline-light btn-sm dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-globe"></i>
                        <span class="d-none d-md-inline"><?= $tr['language'] ?? 'Язык' ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/admin/index.php?lang=ru"><span class="lang-flag me-2">🇷🇺</span> <?= $tr['lang_ru'] ?? 'Русский' ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?lang=en"><span class="lang-flag me-2">🇬🇧</span> <?= $tr['lang_en'] ?? 'English' ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?lang=uk"><span class="lang-flag me-2">🇺🇦</span> <?= $tr['lang_uk'] ?? 'Українська' ?></a></li>
                    </ul>
                </div>

                <a href="/admin/logout.php" class="btn btn-danger btn-sm d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="d-none d-md-inline"><?= $tr['logout'] ?? 'Выйти' ?></span>
                </a>
            </div>
        </div>
    </nav>

    <!-- OFFCANVAS для iPhone, Android, планшетов -->
    <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="sidebarOffcanvas" style="width: 300px;">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold">
                <i class="bi bi-gear-wide-connected text-warning me-2"></i> <?= $tr['management'] ?? 'Управление' ?>
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0">
            <ul class="nav flex-column pt-2">
                <!-- Главная -->
                <li class="nav-item">
                    <a href="/admin/index.php?module=dashboard" class="nav-link <?php echo $module === 'dashboard' ? 'active' : ''; ?>">
                        <i class="bi bi-speedometer2 me-3"></i> <?= $tr['menu_dashboard'] ?? 'Главная' ?>
                    </a>
                </li>

                <!-- Магазин -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module, ['shop_dashboard','shop_add_product','shop_product','shop_category','shop_order','shop_delivery','shop_pay','shop_settings','shop_setting_footer']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-shop me-3"></i> <?= $tr['menu_shop'] ?? 'Магазин' ?> 
                        <?php if ($new_orders_count > 0): ?><span class="badge bg-danger"><?= $new_orders_count ?></span><?php endif; ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white w-100">
                        <li><a class="dropdown-item <?php echo $module === 'shop_dashboard' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_dashboard"><i class="bi bi-speedometer2 me-2"></i> <?= $tr['shop_dashboard'] ?? 'Дашборд магазина' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_add_product' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_add_product"><i class="bi bi-plus-circle me-2"></i> <?= $tr['add_product'] ?? 'Добавить товар' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_product' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_product"><i class="bi bi-boxes me-2"></i> <?= $tr['all_products'] ?? 'Все товары' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_category' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_category"><i class="bi bi-tags me-2"></i> <?= $tr['categories'] ?? 'Категории' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_order' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_order"><i class="bi bi-bag-check me-2"></i> <?= $tr['orders'] ?? 'Заказы' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_delivery' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_delivery"><i class="bi bi-truck me-2"></i> <?= $tr['delivery'] ?? 'Доставка' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_pay' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_pay"><i class="bi bi-credit-card me-2"></i> <?= $tr['payments'] ?? 'Платежи' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_settings' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_settings"><i class="bi bi-gear me-2"></i> <?= $tr['shop_settings'] ?? 'Настройки магазина' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_setting_footer' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_setting_footer"><i class="bi bi-arrow-down-circle me-2"></i> <?= $tr['shop_footer'] ?? 'Подвал магазина' ?></a></li>
                    </ul>
                </li>

                <!-- Бронирования -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module, ['booking_manager','booking','booking_settings']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-calendar-check me-3"></i> <?= $tr['menu_bookings'] ?? 'Бронирования' ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white w-100">
                        <li><a class="dropdown-item <?php echo $module === 'booking_manager' ? 'active' : ''; ?>" href="/admin/index.php?module=booking_manager"><i class="bi bi-building me-2"></i> <?= $tr['manage_objects'] ?? 'Управление объектами' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'booking' ? 'active' : ''; ?>" href="/admin/index.php?module=booking"><i class="bi bi-calendar3 me-2"></i> <?= $tr['all_bookings'] ?? 'Все бронирования' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'booking_settings' ? 'active' : ''; ?>" href="/admin/index.php?module=booking_settings"><i class="bi bi-sliders me-2"></i> <?= $tr['settings'] ?? 'Настройки' ?></a></li>
                    </ul>
                </li>

                <!-- Тендеры -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module, ['tenders','tenders_add','tenders_edit','categories','cities','prices']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-gavel me-3"></i> <?= $tr['menu_tenders'] ?? 'Тендеры' ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white w-100">
                        <li><a class="dropdown-item <?php echo $module === 'tenders_add' ? 'active' : ''; ?>" href="/admin/index.php?module=tenders_add"><i class="bi bi-plus-circle me-2"></i> <?= $tr['add_tender'] ?? 'Добавить тендер' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'tenders' ? 'active' : ''; ?>" href="/admin/index.php?module=tenders"><i class="bi bi-list-ul me-2"></i> <?= $tr['tenders_list'] ?? 'Список тендеров' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'categories' ? 'active' : ''; ?>" href="/admin/index.php?module=categories"><i class="bi bi-folder me-2"></i> <?= $tr['categories'] ?? 'Категории' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'cities' ? 'active' : ''; ?>" href="/admin/index.php?module=cities"><i class="bi bi-geo-alt me-2"></i> <?= $tr['cities'] ?? 'Города' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'prices' ? 'active' : ''; ?>" href="/admin/index.php?module=prices"><i class="bi bi-currency-dollar me-2"></i> <?= $tr['prices'] ?? 'Прайсы' ?></a></li>
                    </ul>
                </li>

                <!-- Новости -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module, ['news_list','news','news_settings','news_categories','news_add','news_settings_lang','news_edit']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-newspaper me-3"></i> <?= $tr['menu_news'] ?? 'Новости' ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white w-100">
                        <li><a class="dropdown-item <?php echo $module === 'news_list' ? 'active' : ''; ?>" href="/admin/index.php?module=news_list"><i class="bi bi-list-ul me-2"></i> <?= $tr['news_list'] ?? 'Список новостей' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'news_add' ? 'active' : ''; ?>" href="/admin/index.php?module=news_add"><i class="bi bi-plus-circle me-2"></i> <?= $tr['add_news'] ?? 'Добавить новость' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'news_edit' ? 'active' : ''; ?>" href="/admin/index.php?module=news_edit"><i class="bi bi-pencil me-2"></i> <?= $tr['edit'] ?? 'Редактировать' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'news_categories' ? 'active' : ''; ?>" href="/admin/index.php?module=news_categories"><i class="bi bi-folder2-open me-2"></i> <?= $tr['categories'] ?? 'Категории' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'news_settings_lang' ? 'active' : ''; ?>" href="/admin/index.php?module=news_settings_lang"><i class="bi bi-translate me-2"></i> <?= $tr['multilang'] ?? 'Мультиязычность' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'news_settings' ? 'active' : ''; ?>" href="/admin/index.php?module=news_settings"><i class="bi bi-gear me-2"></i> <?= $tr['news_settings'] ?? 'Настройки новостей' ?></a></li>
                    </ul>
                </li>

                <!-- Настройки -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module, ['settings','settings_color','settings_form','carusel','seo','users','admins','files','backup','send_email','carusel-brand','shop_pay']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-sliders me-3"></i> <?= $tr['menu_settings'] ?? 'Настройки' ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white w-100">
                        <li><a class="dropdown-item <?php echo $module === 'settings' ? 'active' : ''; ?>" href="/admin/index.php?module=settings"><i class="bi bi-sliders2 me-2"></i> <?= $tr['general_settings'] ?? 'Общие' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_pay' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_pay"><i class="bi bi-credit-card-2-front me-2"></i> <?= $tr['payments'] ?? 'Платежи' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'files' ? 'active' : ''; ?>" href="/admin/index.php?module=files"><i class="bi bi-file-earmark-code me-2"></i> <?= $tr['file_editor'] ?? 'Редактор файлов' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'backup' ? 'active' : ''; ?>" href="/admin/index.php?module=backup"><i class="bi bi-database me-2"></i> <?= $tr['backup'] ?? 'Backup MySQL' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'users' ? 'active' : ''; ?>" href="/admin/index.php?module=users"><i class="bi bi-people me-2"></i> <?= $tr['users'] ?? 'Пользователи' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'send_email' ? 'active' : ''; ?>" href="/admin/index.php?module=send_email"><i class="bi bi-envelope-at me-2"></i> <?= $tr['email_newsletter'] ?? 'Рассылка Email' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'admins' ? 'active' : ''; ?>" href="/admin/index.php?module=admins"><i class="bi bi-shield-lock me-2"></i> <?= $tr['admins'] ?? 'Администраторы' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'settings_color' ? 'active' : ''; ?>" href="/admin/index.php?module=settings_color"><i class="bi bi-palette me-2"></i> <?= $tr['colors'] ?? 'Цвета' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'settings_form' ? 'active' : ''; ?>" href="/admin/index.php?module=settings_form"><i class="bi bi-input-cursor-text me-2"></i> <?= $tr['forms'] ?? 'Формы' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'carusel' ? 'active' : ''; ?>" href="/admin/index.php?module=carusel"><i class="bi bi-images me-2"></i> <?= $tr['carousel'] ?? 'Карусель' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'carusel-brand' ? 'active' : ''; ?>" href="/admin/index.php?module=carusel-brand"><i class="bi bi-building me-2"></i> <?= $tr['brands_carousel'] ?? 'Карусель брендов' ?></a></li>
                    </ul>
                </li>

                <!-- Язык / Мультиязычность (отдельный пункт) -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module, ['news_settings_lang']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-globe me-3"></i> 🌐 <?= $tr['language_multilang'] ?? 'Язык / Мультиязычность' ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white w-100">
                        <li><a class="dropdown-item <?php echo $module === 'news_settings_lang' ? 'active' : ''; ?>" href="/admin/index.php?module=news_settings_lang"><i class="bi bi-translate me-2"></i> <?= $tr['multilang_settings'] ?? 'Настройки мультиязычности' ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?lang=ru"><span class="lang-flag me-2">🇷🇺</span> <?= $tr['lang_ru'] ?? 'Русский' ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?lang=en"><span class="lang-flag me-2">🇬🇧</span> <?= $tr['lang_en'] ?? 'English' ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?lang=uk"><span class="lang-flag me-2">🇺🇦</span> <?= $tr['lang_uk'] ?? 'Українська' ?></a></li>
                    </ul>
                </li>

                <!-- SEO -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module, ['sitemap','seo','shop_seo','perehody']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-search me-3"></i> <?= $tr['menu_seo'] ?? 'SEO Оптимизация' ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white w-100">
                        <li><a class="dropdown-item <?php echo $module === 'sitemap' ? 'active' : ''; ?>" href="/admin/index.php?module=sitemap"><i class="bi bi-sitemap me-2"></i> <?= $tr['sitemap'] ?? 'Карта сайта' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'seo' ? 'active' : ''; ?>" href="/admin/index.php?module=seo"><i class="bi bi-search-heart me-2"></i> <?= $tr['seo_home'] ?? 'SEO Главная' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_seo' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_seo"><i class="bi bi-shop-window me-2"></i> <?= $tr['seo_shop'] ?? 'SEO Магазина' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'perehody' ? 'active' : ''; ?>" href="/admin/index.php?module=perehody"><i class="bi bi-link-45deg me-2"></i> <?= $tr['transitions'] ?? 'Переходы' ?></a></li>
                    </ul>
                </li>

                <!-- Прямые ссылки -->
                <li class="nav-item">
                    <a href="/admin/index.php?module=feedback" class="nav-link <?php echo $module === 'feedback' ? 'active' : ''; ?>">
                        <i class="bi bi-chat-left-text me-3"></i> <?= $tr['feedback'] ?? 'Обратная связь' ?>
                        <?php if ($unread_count > 0): ?><span class="badge bg-danger"><?= $unread_count ?></span><?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/index.php?module=page" class="nav-link <?php echo $module === 'page' ? 'active' : ''; ?>">
                        <i class="bi bi-file-text me-3"></i> <?= $tr['service_pages'] ?? 'Сервисные страницы' ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/index.php?module=security_check" class="nav-link <?php echo $module === 'security_check' ? 'active' : ''; ?>">
                        <i class="bi bi-shield-check me-3"></i> <?= $tr['security'] ?? 'Безопасность' ?>
                    </a>
                </li>

                <!-- API -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module, ['api','nova_poshta_settings']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-plug me-3"></i> <?= $tr['menu_api'] ?? 'API' ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white w-100">
                        <li><a class="dropdown-item <?php echo $module === 'api' ? 'active' : ''; ?>" href="/admin/index.php?module=api"><i class="bi bi-code-square me-2"></i> <?= $tr['external_api'] ?? 'Сторонние API' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'nova_poshta_settings' ? 'active' : ''; ?>" href="/admin/index.php?module=nova_poshta_settings"><i class="bi bi-truck me-2"></i> <?= $tr['nova_poshta'] ?? 'Новая Почта' ?></a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="/admin/index.php?module=cache" class="nav-link <?php echo $module === 'cache' ? 'active' : ''; ?>">
                        <i class="bi bi-memory me-3"></i> <?= $tr['cache'] ?? 'Кеш (cache)' ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Боковая панель для ПК и больших планшетов -->
    <nav class="sidebar bg-dark text-white d-none d-lg-block">
        <div class="p-4">
            <ul class="nav flex-column">
                <!-- Главная -->
                <li class="nav-item">
                    <a href="/admin/index.php?module=dashboard" class="nav-link <?php echo $module === 'dashboard' ? 'active' : ''; ?>">
                        <i class="bi bi-speedometer2 me-3"></i> <?= $tr['menu_dashboard'] ?? 'Главная' ?>
                    </a>
                </li>

                <!-- Магазин -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module, ['shop_dashboard','shop_add_product','shop_product','shop_category','shop_order','shop_delivery','shop_pay','shop_settings','shop_setting_footer']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-shop me-3"></i> <?= $tr['menu_shop'] ?? 'Магазин' ?> 
                        <?php if ($new_orders_count > 0): ?><span class="badge bg-danger"><?= $new_orders_count ?></span><?php endif; ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white w-100">
                        <li><a class="dropdown-item <?php echo $module === 'shop_dashboard' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_dashboard"><i class="bi bi-speedometer2 me-2"></i> <?= $tr['shop_dashboard'] ?? 'Дашборд магазина' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_add_product' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_add_product"><i class="bi bi-plus-circle me-2"></i> <?= $tr['add_product'] ?? 'Добавить товар' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_product' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_product"><i class="bi bi-boxes me-2"></i> <?= $tr['all_products'] ?? 'Все товары' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_category' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_category"><i class="bi bi-tags me-2"></i> <?= $tr['categories'] ?? 'Категории' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_order' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_order"><i class="bi bi-bag-check me-2"></i> <?= $tr['orders'] ?? 'Заказы' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_delivery' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_delivery"><i class="bi bi-truck me-2"></i> <?= $tr['delivery'] ?? 'Доставка' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_pay' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_pay"><i class="bi bi-credit-card me-2"></i> <?= $tr['payments'] ?? 'Платежи' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_settings' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_settings"><i class="bi bi-gear me-2"></i> <?= $tr['shop_settings'] ?? 'Настройки магазина' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_setting_footer' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_setting_footer"><i class="bi bi-arrow-down-circle me-2"></i> <?= $tr['shop_footer'] ?? 'Подвал магазина' ?></a></li>
                    </ul>
                </li>

                <!-- Бронирования -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module, ['booking_manager','booking','booking_settings']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-calendar-check me-3"></i> <?= $tr['menu_bookings'] ?? 'Бронирования' ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white w-100">
                        <li><a class="dropdown-item <?php echo $module === 'booking_manager' ? 'active' : ''; ?>" href="/admin/index.php?module=booking_manager"><i class="bi bi-building me-2"></i> <?= $tr['manage_objects'] ?? 'Управление объектами' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'booking' ? 'active' : ''; ?>" href="/admin/index.php?module=booking"><i class="bi bi-calendar3 me-2"></i> <?= $tr['all_bookings'] ?? 'Все бронирования' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'booking_settings' ? 'active' : ''; ?>" href="/admin/index.php?module=booking_settings"><i class="bi bi-sliders me-2"></i> <?= $tr['settings'] ?? 'Настройки' ?></a></li>
                    </ul>
                </li>

                <!-- Тендеры -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module, ['tenders','tenders_add','tenders_edit','categories','cities','prices']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-gavel me-3"></i> <?= $tr['menu_tenders'] ?? 'Тендеры' ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white w-100">
                        <li><a class="dropdown-item <?php echo $module === 'tenders_add' ? 'active' : ''; ?>" href="/admin/index.php?module=tenders_add"><i class="bi bi-plus-circle me-2"></i> <?= $tr['add_tender'] ?? 'Добавить тендер' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'tenders' ? 'active' : ''; ?>" href="/admin/index.php?module=tenders"><i class="bi bi-list-ul me-2"></i> <?= $tr['tenders_list'] ?? 'Список тендеров' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'categories' ? 'active' : ''; ?>" href="/admin/index.php?module=categories"><i class="bi bi-folder me-2"></i> <?= $tr['categories'] ?? 'Категории' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'cities' ? 'active' : ''; ?>" href="/admin/index.php?module=cities"><i class="bi bi-geo-alt me-2"></i> <?= $tr['cities'] ?? 'Города' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'prices' ? 'active' : ''; ?>" href="/admin/index.php?module=prices"><i class="bi bi-currency-dollar me-2"></i> <?= $tr['prices'] ?? 'Прайсы' ?></a></li>
                    </ul>
                </li>

                <!-- Новости -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module, ['news_list','news','news_settings','news_categories','news_add','news_settings_lang','news_edit']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-newspaper me-3"></i> <?= $tr['menu_news'] ?? 'Новости' ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white w-100">
                        <li><a class="dropdown-item <?php echo $module === 'news_list' ? 'active' : ''; ?>" href="/admin/index.php?module=news_list"><i class="bi bi-list-ul me-2"></i> <?= $tr['news_list'] ?? 'Список новостей' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'news_add' ? 'active' : ''; ?>" href="/admin/index.php?module=news_add"><i class="bi bi-plus-circle me-2"></i> <?= $tr['add_news'] ?? 'Добавить новость' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'news_edit' ? 'active' : ''; ?>" href="/admin/index.php?module=news_edit"><i class="bi bi-pencil me-2"></i> <?= $tr['edit'] ?? 'Редактировать' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'news_categories' ? 'active' : ''; ?>" href="/admin/index.php?module=news_categories"><i class="bi bi-folder2-open me-2"></i> <?= $tr['categories'] ?? 'Категории' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'news_settings_lang' ? 'active' : ''; ?>" href="/admin/index.php?module=news_settings_lang"><i class="bi bi-translate me-2"></i> <?= $tr['multilang'] ?? 'Мультиязычность' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'news_settings' ? 'active' : ''; ?>" href="/admin/index.php?module=news_settings"><i class="bi bi-gear me-2"></i> <?= $tr['news_settings'] ?? 'Настройки новостей' ?></a></li>
                    </ul>
                </li>

                <!-- Настройки -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module, ['settings','settings_color','settings_form','carusel','seo','users','admins','files','backup','send_email','carusel-brand','shop_pay']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-sliders me-3"></i> <?= $tr['menu_settings'] ?? 'Настройки' ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white w-100">
                        <li><a class="dropdown-item <?php echo $module === 'settings' ? 'active' : ''; ?>" href="/admin/index.php?module=settings"><i class="bi bi-sliders2 me-2"></i> <?= $tr['general_settings'] ?? 'Общие' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_pay' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_pay"><i class="bi bi-credit-card-2-front me-2"></i> <?= $tr['payments'] ?? 'Платежи' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'files' ? 'active' : ''; ?>" href="/admin/index.php?module=files"><i class="bi bi-file-earmark-code me-2"></i> <?= $tr['file_editor'] ?? 'Редактор файлов' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'backup' ? 'active' : ''; ?>" href="/admin/index.php?module=backup"><i class="bi bi-database me-2"></i> <?= $tr['backup'] ?? 'Backup MySQL' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'users' ? 'active' : ''; ?>" href="/admin/index.php?module=users"><i class="bi bi-people me-2"></i> <?= $tr['users'] ?? 'Пользователи' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'send_email' ? 'active' : ''; ?>" href="/admin/index.php?module=send_email"><i class="bi bi-envelope-at me-2"></i> <?= $tr['email_newsletter'] ?? 'Рассылка Email' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'admins' ? 'active' : ''; ?>" href="/admin/index.php?module=admins"><i class="bi bi-shield-lock me-2"></i> <?= $tr['admins'] ?? 'Администраторы' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'settings_color' ? 'active' : ''; ?>" href="/admin/index.php?module=settings_color"><i class="bi bi-palette me-2"></i> <?= $tr['colors'] ?? 'Цвета' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'settings_form' ? 'active' : ''; ?>" href="/admin/index.php?module=settings_form"><i class="bi bi-input-cursor-text me-2"></i> <?= $tr['forms'] ?? 'Формы' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'carusel' ? 'active' : ''; ?>" href="/admin/index.php?module=carusel"><i class="bi bi-images me-2"></i> <?= $tr['carousel'] ?? 'Карусель' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'carusel-brand' ? 'active' : ''; ?>" href="/admin/index.php?module=carusel-brand"><i class="bi bi-building me-2"></i> <?= $tr['brands_carousel'] ?? 'Карусель брендов' ?></a></li>
                    </ul>
                </li>

                <!-- Язык / Мультиязычность (ПК версия) -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module, ['news_settings_lang']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-globe me-3"></i> 🌐 <?= $tr['language_multilang'] ?? 'Язык / Мультиязычность' ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white w-100">
                        <li><a class="dropdown-item <?php echo $module === 'news_settings_lang' ? 'active' : ''; ?>" href="/admin/index.php?module=news_settings_lang"><i class="bi bi-translate me-2"></i> <?= $tr['multilang_settings'] ?? 'Настройки мультиязычности' ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?lang=ru"><span class="lang-flag me-2">🇷🇺</span> <?= $tr['lang_ru'] ?? 'Русский' ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?lang=en"><span class="lang-flag me-2">🇬🇧</span> <?= $tr['lang_en'] ?? 'English' ?></a></li>
                        <li><a class="dropdown-item" href="/admin/index.php?lang=uk"><span class="lang-flag me-2">🇺🇦</span> <?= $tr['lang_uk'] ?? 'Українська' ?></a></li>
                    </ul>
                </li>

                <!-- SEO -->
                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module, ['sitemap','seo','shop_seo','perehody']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-search me-3"></i> <?= $tr['menu_seo'] ?? 'SEO Оптимизация' ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white w-100">
                        <li><a class="dropdown-item <?php echo $module === 'sitemap' ? 'active' : ''; ?>" href="/admin/index.php?module=sitemap"><i class="bi bi-sitemap me-2"></i> <?= $tr['sitemap'] ?? 'Карта сайта' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'seo' ? 'active' : ''; ?>" href="/admin/index.php?module=seo"><i class="bi bi-search-heart me-2"></i> <?= $tr['seo_home'] ?? 'SEO Главная' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'shop_seo' ? 'active' : ''; ?>" href="/admin/index.php?module=shop_seo"><i class="bi bi-shop-window me-2"></i> <?= $tr['seo_shop'] ?? 'SEO Магазина' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'perehody' ? 'active' : ''; ?>" href="/admin/index.php?module=perehody"><i class="bi bi-link-45deg me-2"></i> <?= $tr['transitions'] ?? 'Переходы' ?></a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="/admin/index.php?module=feedback" class="nav-link <?php echo $module === 'feedback' ? 'active' : ''; ?>">
                        <i class="bi bi-chat-left-text me-3"></i> <?= $tr['feedback'] ?? 'Обратная связь' ?>
                        <?php if ($unread_count > 0): ?><span class="badge bg-danger"><?= $unread_count ?></span><?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/index.php?module=page" class="nav-link <?php echo $module === 'page' ? 'active' : ''; ?>">
                        <i class="bi bi-file-text me-3"></i> <?= $tr['service_pages'] ?? 'Сервисные страницы' ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/index.php?module=security_check" class="nav-link <?php echo $module === 'security_check' ? 'active' : ''; ?>">
                        <i class="bi bi-shield-check me-3"></i> <?= $tr['security'] ?? 'Безопасность' ?>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link dropdown-toggle <?php echo in_array($module, ['api','nova_poshta_settings']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-plug me-3"></i> <?= $tr['menu_api'] ?? 'API' ?>
                    </a>
                    <ul class="dropdown-menu bg-dark text-white w-100">
                        <li><a class="dropdown-item <?php echo $module === 'api' ? 'active' : ''; ?>" href="/admin/index.php?module=api"><i class="bi bi-code-square me-2"></i> <?= $tr['external_api'] ?? 'Сторонние API' ?></a></li>
                        <li><a class="dropdown-item <?php echo $module === 'nova_poshta_settings' ? 'active' : ''; ?>" href="/admin/index.php?module=nova_poshta_settings"><i class="bi bi-truck me-2"></i> <?= $tr['nova_poshta'] ?? 'Новая Почта' ?></a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="/admin/index.php?module=cache" class="nav-link <?php echo $module === 'cache' ? 'active' : ''; ?>">
                        <i class="bi bi-memory me-3"></i> <?= $tr['cache'] ?? 'Кеш (cache)' ?>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Основной контент (сюда подключаются все модули) -->
    <div class="main-content flex-grow-1 p-4">
        <!-- ТВОЙ ДАШБОРД И ВСЁ ОСТАЛЬНОЕ ИДЁТ СЮДА -->