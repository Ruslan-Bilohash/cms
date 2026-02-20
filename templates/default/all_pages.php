<?php
session_start();

// Подключаем зависимости с абсолютными путями
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions_cache.php';

// Проверяем, доступен ли $conn
if (!isset($conn) || $conn->connect_error) {
    die("Ошибка подключения к базе данных. Проверьте /includes/db.php");
}

// Включаем отображение ошибок (убрать в продакшене)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Проверяем кеш страницы
$cache_key = 'all_pages_' . md5($_SERVER['QUERY_STRING'] ?? ''); // Кеш зависит от параметров запроса
$cache_path = '/templates/default';
$cached_content = get_from_cache($cache_key, $cache_path);

if ($cached_content !== false && should_cache_path($cache_path)) {
    echo $cached_content;
    exit;
}

ob_start();

// Устанавливаем заголовки безопасности
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Обработка параметров поиска и фильтра по букве
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$letter_filter = isset($_GET['letter']) && preg_match('/^[А-ЯЁа-яё]$/', $_GET['letter']) ? $_GET['letter'] : '';

// Формируем SQL-запрос (добавляем поле created_at для даты публикации)
$sql = 'SELECT title, url, created_at FROM pages WHERE url != "" AND url IS NOT NULL';
$params = [];
$types = '';

if ($search_query) {
    $sql .= ' AND title LIKE ?';
    $params[] = '%' . $search_query . '%';
    $types .= 's';
}

if ($letter_filter) {
    $sql .= ' AND title LIKE ?';
    $params[] = $letter_filter . '%';
    $types .= 's';
}

$sql .= ' ORDER BY title ASC';
$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$pages = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Генерация русского алфавита
$alphabet = array_merge(range('А', 'Я'), ['Ё']);

// Кеширование статических файлов
$css_content = cache_static_file('/templates/default/css/style.css');
$style_php_content = cache_static_file('/templates/default/css/style.php');
$js_content = cache_static_file('/templates/default/js/js.js');

// Кеширование внешних ресурсов
$bootstrap_css = cache_external_resource('https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css', 'css');

// Подключаем header
require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Все страницы</title>
    <style>
        <?php echo $css_content; ?>
        <?php echo $style_php_content; ?>
        <?php echo $bootstrap_css; ?>
        /* Добавляем отступ перед футером */
        footer {
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h1>Все страницы</h1>

    <!-- Форма поиска -->
    <form method="GET" action="/all_pages" class="mb-4">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Поиск по заголовку" value="<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="btn btn-primary">Найти</button>
            <?php if ($search_query || $letter_filter): ?>
                <a href="/all_pages" class="btn btn-secondary ms-2">Сбросить</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Алфавитный указатель (только русский) -->


    <!-- Список страниц с датой публикации -->
    <?php if (!empty($pages)): ?>
    <div class="pages-list">
        <ul class="list-group">
            <?php foreach ($pages as $page): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="/<?php echo htmlspecialchars($page['url'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($page['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <span class="text-muted">
                        <?php 
                        // Форматируем дату публикации (предполагается, что created_at в формате datetime)
                        echo $page['created_at'] ? date('d.m.Y', strtotime($page['created_at'])) : 'Нет даты';
                        ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php else: ?>
    <p>Страниц не найдено.</p>
    <?php endif; ?>
</div>

<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; 

// Сохраняем в кеш
$content = ob_get_clean();
if (should_cache_path($cache_path)) {
    save_to_cache($cache_key, $content, $cache_path);
}
echo $content;
?>
</body>
</html>