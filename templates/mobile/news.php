<?php
// /templates/mobile/news.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Подключаем зависимости
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Загружаем настройки
$settings = get_settings();
if (empty($settings)) {
    $settings = [
        'button_size' => 'medium',
        'button_shape' => 0,
        'site_title' => 'Pro Website Management',
        'site_description' => 'Добро пожаловать в Pro Website Management',
        'site_keywords' => 'CMS, веб-разработка'
    ];
    error_log("Использованы настройки по умолчанию в mobile/news.php.");
}

// Установка заголовков безопасности
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

// Проверяем подключение к базе данных
if (!$conn) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}

// CSRF-токен
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Обработка поиска и фильтров
$where = "";
$search_query = '';
$category_id = 0;
$category_desc = '';
$keywords = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Ошибка: Неверный CSRF-токен");
    }

    $search_query = htmlspecialchars(trim($_POST['search_query'] ?? ''), ENT_QUOTES, 'UTF-8');
    $category_id = (int)($_POST['category_id'] ?? 0);

    if (!empty($search_query)) {
        $where .= " WHERE (n.title LIKE ? OR n.short_desc LIKE ?)";
    }
    if ($category_id) {
        $where .= $where ? " AND" : " WHERE";
        $where .= " n.category_id = ?";
        $stmt = $conn->prepare("SELECT description FROM news_categories WHERE id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $category_desc = $stmt->get_result()->fetch_assoc()['description'] ?? '';
        $stmt->close();
    }

    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
} elseif (isset($_GET['category_id'])) {
    $category_id = (int)$_GET['category_id'];
    $where = "WHERE n.category_id = ?";
    $stmt = $conn->prepare("SELECT description FROM news_categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $category_desc = $stmt->get_result()->fetch_assoc()['description'] ?? '';
    $stmt->close();
} elseif (isset($_GET['keywords'])) {
    $keywords = htmlspecialchars(trim($_GET['keywords'] ?? ''), ENT_QUOTES, 'UTF-8');
    $where = "WHERE n.keywords LIKE ?";
}

// Запрос новостей
$query = "SELECT n.*, c.title AS category_title FROM news n LEFT JOIN news_categories c ON n.category_id = c.id $where ORDER BY n.created_at DESC";
$stmt = $conn->prepare($query);

if (!empty($search_query) && $category_id) {
    $search_param1 = "%$search_query%";
    $search_param2 = "%$search_query%";
    $stmt->bind_param("ssi", $search_param1, $search_param2, $category_id);
} elseif (!empty($search_query)) {
    $search_param1 = "%$search_query%";
    $search_param2 = "%$search_query%";
    $stmt->bind_param("ss", $search_param1, $search_param2);
} elseif ($category_id) {
    $stmt->bind_param("i", $category_id);
} elseif (!empty($keywords)) {
    $keywords_param = "%$keywords%";
    $stmt->bind_param("s", $keywords_param);
}

if ($where) {
    $stmt->execute();
    $news = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $news = $conn->query("SELECT n.*, c.title AS category_title FROM news n LEFT JOIN news_categories c ON n.category_id = c.id ORDER BY n.created_at DESC")->fetch_all(MYSQLI_ASSOC);
}

// Загрузка категорий
$categories = $conn->query("SELECT * FROM news_categories")->fetch_all(MYSQLI_ASSOC);

// Классы кнопок
$button_size_class = '';
switch ($settings['button_size'] ?? 'medium') {
    case 'small': $button_size_class = 'btn-sm'; break;
    case 'large': $button_size_class = 'btn-lg'; break;
    default: $button_size_class = ''; break;
}
$button_shape = (int)($settings['button_shape'] ?? 0);

// Подключаем шаблоны
$header_path = $_SERVER['DOCUMENT_ROOT'] . '/templates/mobile/header.php';
$meta_path = $_SERVER['DOCUMENT_ROOT'] . '/templates/mobile/meta.php';
$footer_path = $_SERVER['DOCUMENT_ROOT'] . '/templates/mobile/footer_shop_contact.php';

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

<main class="mobile-container py-4">
    <h1 class="section-title mb-4"><i class="fas fa-newspaper me-2"></i> Новости</h1>

    <!-- Поиск -->
    <form method="POST" class="mb-4">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="input-group shadow-sm rounded">
            <input type="text" name="search_query" class="form-control border-0" placeholder="Поиск по новостям..." value="<?php echo htmlspecialchars($search_query); ?>">
            <select name="category_id" class="form-select border-0">
                <option value="">Все категории</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="search" class="btn btn-primary <?php echo $button_size_class; ?>" style="background: var(--primary-gradient); border: none;">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>

    <!-- Список новостей -->
    <?php if (empty($news)): ?>
        <p class="text-center text-muted">Новостей пока нет.</p>
    <?php else: ?>
        <?php foreach ($news as $item): ?>
            <div class="news-card mb-4">
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
    <?php endif; ?>
</main>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6B46C1, #4C51BF);
        --accent-color: #ED8936;
        --text-color: #2D3748;
        --bg-color: #FFFFFF;
    }
    .mobile-container {
        max-width: 100%;
        padding: 15px;
    }
    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-color);
        text-align: center;
    }
    .input-group {
        background: var(--bg-color);
        border-radius: 8px;
    }
    .form-control, .form-select {
        font-size: 0.9rem;
        padding: 10px;
    }
    .btn-primary {
        padding: 10px 15px;
        font-size: 0.9rem;
        border-radius: <?php echo $button_shape; ?>px;
    }
    .news-card {
        background: var(--bg-color);
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
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
        border-radius: <?php echo $button_shape; ?>px;
    }
    .news-card .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    .news-card .btn i {
        margin-left: 5px;
    }
    @media (max-width: 576px) {
        .mobile-container {
            padding: 10px;
        }
        .section-title {
            font-size: 1.3rem;
        }
        .form-control, .form-select {
            font-size: 0.85rem;
            padding: 8px;
        }
        .btn-primary {
            padding: 8px 12px;
            font-size: 0.85rem;
        }
        .news-card .card-title {
            font-size: 1.1rem;
        }
        .news-card img,
        .news-card .no-image {
            height: 120px;
        }
        .news-card .card-text {
            font-size: 0.9rem;
        }
        .news-card .card-meta {
            font-size: 0.8rem;
        }
        .news-card .btn {
            font-size: 0.9rem;
            padding: 7px 10px;
        }
    }
</style>

<?php 
if (file_exists($footer_path)) {
    include $footer_path;
} else {
    die("Ошибка: файл $footer_path не найден.");
}
?>