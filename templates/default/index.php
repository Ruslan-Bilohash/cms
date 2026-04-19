<?php
session_start();
// Проверяем параметр принудительного переключения версии
if (isset($_GET['force_mobile'])) {
    $_SESSION['preferred_version'] = $_GET['force_mobile'] == 1 ? 'mobile' : 'desktop';
}
// Проверяем наличие необходимых файлов
$required_files = [
    $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php',
    $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php',
    $_SERVER['DOCUMENT_ROOT'] . '/includes/functions_cache.php'
];
foreach ($required_files as $file) {
    if (!file_exists($file)) {
        die("Ошибка: файл $file не найден.");
    }
    require_once $file;
}
// Загружаем настройки
$settings = get_settings();
if (empty($settings)) {
    $settings = [
        'news_on_index' => true,
        'news_limit' => 3,
        'news_include_on_index' => [],
        'news_exclude_on_index' => [],
        'mobile_version' => true
    ];
    error_log("Использованы настройки по умолчанию в index.php.");
}
// Выбор пути к шаблонам
$is_mobile_device = isMobileDevice();
$preferred_version = $_SESSION['preferred_version'] ?? ($is_mobile_device ? 'mobile' : 'desktop');
$templatePath = ($settings['mobile_version'] ?? 0) && $preferred_version === 'mobile' ? '/templates/mobile/' : '/templates/default/';
$cache_path = $templatePath;
// Проверяем кеш страницы
$current_url = $_SERVER['REQUEST_URI'];
$cache_key = 'page_' . md5($current_url);
$cached_content = get_from_cache($cache_key, $cache_path);
if ($cached_content !== false && should_cache_path($cache_path)) {
    echo $cached_content;
    exit;
}
ob_start();
// Проверяем подключение к базе данных
if (!$conn) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}
// Примеры запросов с кешированием таблиц
$carousel = cache_query("SELECT * FROM carousel LIMIT 5", $conn, $cache_path, 'carousel');
$products = cache_query("SELECT * FROM shop_products LIMIT 10", $conn, $cache_path, 'shop_products');
// Кеширование статических файлов
$js_content = cache_static_file($templatePath . 'js/js.js');
$css_content = cache_static_file($templatePath . 'css/style.css');
$style_php_content = cache_static_file($templatePath . 'css/style.php');
// Кеширование внешних ресурсов
$font_content = cache_external_resource('https://fonts.googleapis.com/css?family=Roboto', 'fonts');
$icons_content = cache_external_resource('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css', 'icons');
// Установка заголовков безопасности
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; object-src 'none';");
$ip_address = $_SERVER['REMOTE_ADDR'];
$page_url = $_SERVER['REQUEST_URI'];
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
list($device_type, $browser) = detectDeviceAndBrowser($user_agent);
// Логирование посетителей
$stmt = $conn->prepare("INSERT INTO visitor_logs (ip_address, page_url, device_type, browser) VALUES (?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("ssss", $ip_address, $page_url, $device_type, $browser);
    $stmt->execute();
    $stmt->close();
} else {
    error_log("Ошибка подготовки запроса для visitor_logs: " . $conn->error);
}
// CSRF-токен
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// Загрузка данных с кешированием запросов
$categories = cache_query("SELECT * FROM categories", $conn, $cache_path);
$cities = cache_query("SELECT * FROM cities", $conn, $cache_path);
$news_categories = cache_query("SELECT * FROM news_categories", $conn, $cache_path);
// Загрузка новостей с учетом настроек
$show_news = $settings['news_on_index'] ?? true;
$news_limit = $settings['news_limit'] ?? 3;
$news_include_on_index = $settings['news_include_on_index'] ?? [];
$news_exclude_on_index = $settings['news_exclude_on_index'] ?? [];
if ($show_news) {
    $news_query = "SELECT n.*, c.title AS category_title
                   FROM news n
                   LEFT JOIN news_categories c ON n.category_id = c.id";
  
    $where_clauses = [];
    if (!empty($news_include_on_index)) {
        $where_clauses[] = "n.category_id IN (" . implode(',', array_map('intval', $news_include_on_index)) . ")";
    }
    if (!empty($news_exclude_on_index)) {
        $where_clauses[] = "n.category_id NOT IN (" . implode(',', array_map('intval', $news_exclude_on_index)) . ")";
    }
  
    if (!empty($where_clauses)) {
        $news_query .= " WHERE " . implode(' AND ', $where_clauses);
    }
  
    $news_query .= " ORDER BY n.created_at DESC LIMIT ?";
    $stmt = $conn->prepare($news_query);
    if ($stmt) {
        $stmt->bind_param("i", $news_limit);
        $stmt->execute();
        $news = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        error_log("Ошибка подготовки запроса для новостей: " . $conn->error);
        $news = [];
    }
}
// Метаданные
$_SESSION['meta'] = [
    'title' => $settings['site_title'] ?? 'Tender CMS',
    'description' => $settings['site_description'] ?? 'Добро пожаловать в Tender CMS',
    'keywords' => $settings['site_keywords'] ?? 'тендеры, CMS',
    'og_title' => $settings['site_title'] ?? 'Tender CMS',
    'og_description' => $settings['site_description'] ?? 'Добро пожаловать в Tender CMS',
    'og_image' => $settings['og_image'] ?? '',
    'og_type' => $settings['og_type'] ?? 'website',
    'og_url' => $settings['og_url'] ?? 'https://' . $_SERVER['HTTP_HOST']
];
// Классы кнопок
$add_tender_button_size_class = '';
switch ($settings['add_tender_button_size'] ?? 'large') {
    case 'extra-small': $add_tender_button_size_class = 'btn-xs'; break;
    case 'small': $add_tender_button_size_class = 'btn-sm'; break;
    case 'medium': $add_tender_button_size_class = ''; break;
    case 'large': $add_tender_button_size_class = 'btn-lg'; break;
    case 'extra-large': $add_tender_button_size_class = 'btn-xl'; break;
    case 'jumbo': $add_tender_button_size_class = 'btn-jumbo'; break;
    default: $add_tender_button_size_class = 'btn-lg'; break;
}
$button_size_class = '';
switch ($settings['button_size'] ?? 'medium') {
    case 'small': $button_size_class = 'btn-sm'; break;
    case 'large': $button_size_class = 'btn-lg'; break;
    default: $button_size_class = ''; break;
}
// Обработка поиска
$search_query = '';
$category_id = 0;
$city_id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Ошибка: Неверный CSRF-токен");
    }
    $search_query = htmlspecialchars(trim($_POST['search_query'] ?? ''), ENT_QUOTES, 'UTF-8');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $city_id = (int)($_POST['city_id'] ?? 0);
    $query = "
        SELECT t.*, u.nickname, c.title AS category, ci.name AS city_name
        FROM tenders t
        LEFT JOIN users u ON t.user_id = u.id
        LEFT JOIN categories c ON t.category_id = c.id
        LEFT JOIN cities ci ON t.city_id = ci.id
        WHERE t.status = 'published'";
  
    $params = [];
    $types = '';
    if (!empty($search_query)) {
        $query .= " AND (t.title LIKE ? OR t.short_desc LIKE ?)";
        $params[] = "%$search_query%";
        $params[] = "%$search_query%";
        $types .= "ss";
    }
    if ($category_id) {
        $query .= " AND t.category_id = ?";
        $params[] = $category_id;
        $types .= "i";
    }
    if ($city_id) {
        $query .= " AND t.city_id = ?";
        $params[] = $city_id;
        $types .= "i";
    }
    $query .= " LIMIT 10";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $tenders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        error_log("Ошибка подготовки запроса для тендеров: " . $conn->error);
        $tenders = [];
    }
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
} else {
    $tenders = cache_query("
        SELECT t.*, u.nickname, c.title AS category, ci.name AS city_name
        FROM tenders t
        LEFT JOIN users u ON t.user_id = u.id
        LEFT JOIN categories c ON t.category_id = c.id
        LEFT JOIN cities ci ON t.city_id = ci.id
        WHERE t.status = 'published'
        LIMIT 10", $conn, $cache_path);
}
foreach ($tenders as &$tender) {
    $tender['images'] = !empty($tender['images']) ? json_decode($tender['images'], true) : [];
    $tender['short_desc'] = $tender['short_desc'];
}
// Проверяем шаблоны
$header_path = $_SERVER['DOCUMENT_ROOT'] . $templatePath . 'header.php';
$meta_path = $_SERVER['DOCUMENT_ROOT'] . $templatePath . 'meta.php';
$footer_path = $_SERVER['DOCUMENT_ROOT'] . $templatePath . 'footer_shop_contact.php';
$carousel_path = $_SERVER['DOCUMENT_ROOT'] . $templatePath . 'carusel.php';
if (file_exists($header_path)) {
    include $header_path;
} else {
    die("Ошибка: файл $header_path не найден.");
}
if (file_exists($meta_path)) {
    include $meta_path;
} else {
    die("Ошибка: файл $meta_path не найден.");
}
?>
<div id="cookie-consent" class="cookie-consent bg-light shadow-lg p-4 rounded position-fixed bottom-0 start-0 end-0 m-3 z-3" style="max-width: 400px; margin: auto;">
    <div class="cookie-content text-center">
        <h4 class="fw-bold mb-3">Мы используем cookies</h4>
        <p class="text-muted mb-4">На сайте https://edukvam.com мы используем cookies для улучшения вашего опыта. Вы можете принять все, настроить или отклонить необязательные. Подробности в <a href="/privacy-policy" class="text-primary">Политике конфиденциальности</a>.</p>
        <div class="cookie-buttons d-flex flex-column flex-md-row justify-content-center gap-2">
            <button onclick="acceptCookies()" class="btn btn-primary btn-md">Принять все</button>
            <button onclick="openSettings()" class="btn btn-outline-secondary btn-md">Настроить</button>
            <button onclick="declineCookies()" class="btn btn-outline-danger btn-md">Отклонить необязательные</button>
        </div>
    </div>
</div>
<!-- Навигация -->
<nav class="navbar navbar-expand-md navbar-dark bg-primary shadow mb-5">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#profileNavbar" aria-controls="profileNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="profileNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="/news"><i class="fas fa-newspaper me-2"></i>Новости</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/tenders"><i class="fas fa-gavel me-2"></i>Auction CMS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/shop"><i class="fas fa-shopping-cart me-2"></i>Shop CMS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/booking"><i class="fas fa-calendar-check me-2"></i>Booking CMS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://ilove.lt"><i class="fas fa-heart me-2"></i>Dating CMS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://mapsme.no"><i class="fas fa-map-marker-alt me-2"></i>MapsME CMS</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/api"><i class="fas fa-code me-2"></i>API</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . $templatePath . 'brand-carusel.php'; ?>

<!-- 🔥 ПРОМО-БЛОК MODULES ADMIN PANEL — подключён здесь -->
<?php require_once $_SERVER['DOCUMENT_ROOT'] . $templatePath . 'modules-promo.php'; ?>

<?php if ($show_news && !empty($news)): ?>
    <section class="container py-5 bg-white shadow rounded">
        <h2 class="text-center mb-5 fw-bold text-primary">Последние новости</h2>
        <div class="row g-4">
            <?php foreach ($news as $item): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow hover-shadow-lg transition-all">
                        <?php if ($settings['news_show_image'] ?? false): ?>
                            <?php
                            $images = json_decode($item['image'] ?? '[]', true);
                            if (!empty($images) && is_array($images)):
                                $main_image = $images[0];
                            ?>
                                <img src="/public/uploads/news/<?php echo htmlspecialchars($main_image); ?>"
                                     class="card-img-top rounded-top"
                                     alt="<?php echo htmlspecialchars($item['title']); ?>">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <span class="text-muted">Нет изображения</span>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold mb-3">
                                <a href="/news/<?php echo htmlspecialchars($item['custom_url']); ?>" class="text-decoration-none text-dark hover-text-primary transition-color">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </a>
                            </h5>
                            <div class="d-flex justify-content-between align-items-center text-muted mb-3 small">
                                <?php if ($settings['news_show_category'] ?? false): ?>
                                    <span>
                                        <i class="fas fa-folder-open me-1"></i>
                                        <a href="/news?category_id=<?php echo $item['category_id']; ?>" class="text-primary text-decoration-none">
                                            <?php echo htmlspecialchars($item['category_title'] ?? 'Без категории'); ?>
                                        </a>
                                    </span>
                                <?php endif; ?>
                                <?php if ($settings['news_show_date'] ?? false): ?>
                                    <span>
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?php echo date('d.m.Y', strtotime($item['created_at'])); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <p class="card-text text-muted flex-grow-1"><?php echo htmlspecialchars($item['short_desc']); ?></p>
                            <?php if (!empty($item['keywords'])): ?>
                                <p class="small text-muted mt-2">
                                    <i class="fas fa-tags me-1"></i>
                                    <?php echo implode(', ', array_map(fn($kw) => "<a href='/news?keywords=$kw' class='text-primary text-decoration-none'>$kw</a>", explode(',', $item['keywords']))); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent border-0 pb-3">
                            <a href="/news/<?php echo htmlspecialchars($item['custom_url']); ?>" class="btn btn-outline-primary w-100">
                                <i class="fas fa-arrow-right me-2"></i> Читать далее
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-5">
            <a href="/news" class="btn btn-primary btn-lg <?php echo $button_size_class; ?>">Все новости</a>
        </div>
    </section>
<?php endif; ?>

<?php
if (file_exists($carousel_path)) {
    $display_on_home_only = true;
    require_once $carousel_path;
} else {
    echo "<!-- Не удалось найти carusel.php -->";
}
?>
<?php
if (file_exists($footer_path)) {
    include $footer_path;
} else {
    die("Ошибка: файл $footer_path не найден.");
}

$content = ob_get_clean();
if (should_cache_path($cache_path)) {
    save_to_cache($cache_key, $content, $cache_path);
}
echo $content;
?>
