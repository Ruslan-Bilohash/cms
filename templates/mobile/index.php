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

// Выбор пути к шаблонам
$is_mobile_device = isMobileDevice();
$preferred_version = $_SESSION['preferred_version'] ?? ($is_mobile_device ? 'mobile' : 'desktop');
$templatePath = ($settings['mobile_version'] ?? true) && $preferred_version === 'mobile' ? '/templates/mobile/' : '/templates/default/';
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

// Кеширование внешних ресурсов
$font_content = cache_external_resource('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', 'fonts');
$icons_content = cache_external_resource('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css', 'icons');

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

// Установка заголовков безопасности
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; object-src 'none';");

// Определение устройства и браузера
function detectDeviceAndBrowser($userAgent) {
    $device = 'unknown';
    $browser = 'unknown';

    if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $userAgent)) {
        $device = 'tablet';
    } elseif (preg_match('/Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/i', $userAgent)) {
        $device = 'mobile';
    } elseif (preg_match('/(windows|macintosh|linux)/i', $userAgent)) {
        $device = 'desktop';
    }

    if (preg_match('/chrome|crios/i', $userAgent)) {
        $browser = 'Chrome';
    } elseif (preg_match('/firefox|fxios/i', $userAgent)) {
        $browser = 'Firefox';
    } elseif (preg_match('/safari/i', $userAgent) && !preg_match('/chrome|crios/i', $userAgent)) {
        $browser = 'Safari';
    } elseif (preg_match('/edg/i', $userAgent)) {
        $browser = 'Edge';
    } elseif (preg_match('/msie|trident/i', $userAgent)) {
        $browser = 'Internet Explorer';
    } elseif (preg_match('/opera|opr/i', $userAgent)) {
        $browser = 'Opera';
    }

    return [$device, $browser];
}

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

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6B46C1, #4C51BF);
        --accent-color: #ED8936;
        --text-color: #2D3748;
        --bg-color: #FFFFFF;
    }
    body {
        font-family: 'Inter', sans-serif;
        background: #F7FAFC;
        margin: 0;
        overscroll-behavior: none;
    }
    .mobile-container {
        max-width: 100%;
        padding: 15px;
    }
    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 15px;
        text-align: center;
    }
    .news-card {
        background: var(--bg-color);
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .news-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }
    .news-card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        display: block;
    }
    .news-card .no-image {
        width: 100%;
        height: 150px;
        background: #EDF2F7;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #A0AEC0;
        font-size: 0.9rem;
    }
    .news-card .card-body {
        padding: 15px;
    }
    .news-card .card-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 10px;
    }
    .news-card .card-title a {
        color: inherit;
        text-decoration: none;
    }
    .news-card .card-text {
        font-size: 0.95rem;
        color: #718096;
        margin-bottom: 10px;
    }
    .news-card .card-meta {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        color: #A0AEC0;
    }
    .news-card .card-meta a {
        color: var(--accent-color);
        text-decoration: none;
    }
    .news-card .card-meta i {
        margin-right: 5px;
    }
    .news-card .card-footer {
        padding: 10px 15px;
        background: transparent;
        border-top: none;
    }
    .news-card .btn {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        font-size: 0.95rem;
        font-weight: 500;
        color: white;
        background: var(--accent-color);
        border-radius: 8px;
        text-decoration: none;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .news-card .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    .news-card .btn i {
        margin-left: 5px;
    }
    .all-news-btn {
        display: block;
        text-align: center;
        padding: 12px;
        font-size: 1rem;
        font-weight: 600;
        color: white;
        background: var(--primary-gradient);
        border-radius: 8px;
        text-decoration: none;
        margin: 20px 0;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .all-news-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    @media (max-width: 576px) {
        .mobile-container {
            padding: 10px;
        }
        .section-title {
            font-size: 1.3rem;
        }
        .news-card .card-title {
            font-size: 1.1rem;
        }
        .news-card img,
        .news-card .no-image {
            height: 120px;
        }
    }
</style>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . $templatePath . 'brand-carusel.php'; ?>

<?php if ($show_news && !empty($news)): ?>
    <section class="mobile-container">
        <h2 class="section-title"><i class="fas fa-newspaper me-2"></i> Последние новости</h2>
        <?php foreach ($news as $item): ?>
            <div class="news-card">
                <?php 
                $images = json_decode($item['image'] ?? '[]', true);
                if (!empty($images) && is_array($images) && ($settings['news_show_image'] ?? false)): 
                    $main_image = $images[0];
                ?>
                    <img src="/public/uploads/news/<?php echo htmlspecialchars($main_image); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" loading="lazy">
                <?php else: ?>
                    <div class="no-image">Нет изображения</div>
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="/news/<?php echo htmlspecialchars($item['custom_url']); ?>">
                            <?php echo htmlspecialchars($item['title']); ?>
                        </a>
                    </h5>
                    <p class="card-text"><?php echo htmlspecialchars($item['short_desc']); ?></p>
                    <div class="card-meta">
                        <?php if ($settings['news_show_category'] ?? false): ?>
                            <span>
                                <i class="fas fa-folder-open"></i>
                                <a href="/news?category_id=<?php echo $item['category_id']; ?>">
                                    <?php echo htmlspecialchars($item['category_title'] ?? 'Без категории'); ?>
                                </a>
                            </span>
                        <?php endif; ?>
                        <?php if ($settings['news_show_date'] ?? false): ?>
                            <span>
                                <i class="fas fa-calendar-alt"></i>
                                <?php echo date('d.m.Y', strtotime($item['created_at'])); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($item['keywords'])): ?>
                        <p class="card-text">
                            <small>
                                <i class="fas fa-tags"></i>
                                <?php echo implode(', ', array_map(fn($kw) => "<a href='/news?keywords=$kw' class='text-accent'>" . htmlspecialchars($kw) . "</a>", explode(',', $item['keywords']))); ?>
                            </small>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="card-footer">
                    <a href="/news/<?php echo htmlspecialchars($item['custom_url']); ?>" class="btn">
                        Читать далее <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
        <a href="/news" class="all-news-btn">Все новости <i class="fas fa-arrow-right ms-2"></i></a>
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