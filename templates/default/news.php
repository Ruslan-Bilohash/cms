<?php
// Включаем отображение ошибок для диагностики
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Подключаем зависимости с абсолютными путями
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Подключаем настройки сайта (если не подключено в header.php)
$settings_file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/site_settings.php';
$settings = file_exists($settings_file) ? include $settings_file : [
    'button_size' => 'medium',
    'button_shape' => 0
];

// Установка заголовков безопасности
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

$where = "";
$search_query = '';
$category_id = 0;
$category_desc = ''; // Для SEO
$keywords = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
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

$query = "SELECT n.*, c.title AS category_title FROM news n LEFT JOIN news_categories c ON n.category_id = c.id $where ORDER BY n.created_at DESC";
$stmt = $conn->prepare($query);

// Подготовка параметров для bind_param
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

// Выполняем запрос только если есть условия, иначе получаем все новости
if ($where) {
    $stmt->execute();
    $news = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $news = $conn->query("SELECT n.*, c.title AS category_title FROM news n LEFT JOIN news_categories c ON n.category_id = c.id ORDER BY n.created_at DESC")->fetch_all(MYSQLI_ASSOC);
}

$categories = $conn->query("SELECT * FROM news_categories")->fetch_all(MYSQLI_ASSOC);

// Определяем классы размера кнопок в зависимости от настройки
$button_size_class = '';
switch ($settings['button_size']) {
    case 'small':
        $button_size_class = 'btn-sm';
        break;
    case 'large':
        $button_size_class = 'btn-lg';
        break;
    case 'medium':
    default:
        $button_size_class = '';
        break;
}
$button_shape = (int)($settings['button_shape'] ?? 0);

// Подключаем header
require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header.php';
?>

<main class="container py-5">
    <h1 class="text-center mb-5 display-4 fw-bold">Новости</h1>

    <!-- Поиск -->
    <form method="POST" class="mb-5">
        <div class="input-group shadow-sm rounded">
            <input type="text" name="search_query" class="form-control border-0 flex-grow-1" placeholder="Поиск по новостям..." value="<?php echo htmlspecialchars($search_query); ?>">
            <select name="category_id" class="form-select border-0">
                <option value="">Все категории</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="search" class="btn btn-primary <?php echo $button_size_class; ?>"><i class="fas fa-search fa-lg"></i> Найти</button>
        </div>
    </form>

    <!-- Список новостей -->
    <div class="row g-4">
        <?php if (empty($news)): ?>
            <p class="text-center text-muted fs-5">Новостей пока нет.</p>
        <?php else: ?>
            <?php foreach ($news as $item): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><a href="/news/<?php echo htmlspecialchars($item['custom_url']); ?>" class="text-dark text-decoration-none"><?php echo htmlspecialchars($item['title']); ?></a></h5>
                            <?php 
                            $images = json_decode($item['image'] ?? '[]', true);
                            if (!empty($images) && is_array($images)): 
                                $main_image = $images[0];
                            ?>
                                <div class="main-image mt-3">
                                    <img src="/public/uploads/news/<?php echo htmlspecialchars($main_image); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                </div>
                            <?php else: ?>
                                <div class="main-image bg-light d-flex align-items-center justify-content-center mt-3">
                                    <span class="text-muted">Нет изображения</span>
                                </div>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between align-items-center text-muted mt-3 mb-2">
                                <span><i class="fas fa-folder-open fa-lg"></i> <a href="/news?category_id=<?php echo $item['category_id']; ?>" class="text-primary"><?php echo htmlspecialchars($item['category_title'] ?? 'Без категории'); ?></a></span>
                                <span><i class="fas fa-calendar-alt fa-lg"></i> <?php echo date('d.m.Y', strtotime($item['created_at'])); ?></span>
                            </div>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($item['short_desc']); ?></p>
                            <?php if (!empty($item['keywords'])): ?>
                                <p><small><i class="fas fa-tags fa-lg"></i> <?php echo implode(', ', array_map(fn($kw) => "<a href='/news?keywords=$kw' class='text-primary'>" . htmlspecialchars($kw) . "</a>", explode(',', $item['keywords']))); ?></small></p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="/news/<?php echo htmlspecialchars($item['custom_url']); ?>" class="btn btn-outline-primary btn-modern w-100 <?php echo $button_size_class; ?>"><i class="fas fa-arrow-right fa-lg"></i> Читать далее</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<style>
    /* Применяем настройки кнопок */
    .btn-modern, 
    .btn-primary {
        border-radius: <?php echo $button_shape; ?>px !important;
        transition: all 0.3s ease;
    }
    .main-image {
        width: 100%;
        height: 200px;
        overflow: hidden;
        border-radius: 10px;
    }
    .main-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .card {
        transition: transform 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
    }
</style>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>