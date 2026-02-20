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
$current_url = $_SERVER['REQUEST_URI'];
$cache_key = 'page_' . md5($current_url);
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

// Получаем текущий URL без параметров и слеша в конце
$current_url = trim($_SERVER['REQUEST_URI'], '/'); // Убираем слеши с начала и конца
$current_url = strtok($current_url, '?'); // Убираем параметры
$stmt = $conn->prepare('SELECT * FROM pages WHERE url = ?');
$stmt->bind_param('s', $current_url);
$stmt->execute();
$result = $stmt->get_result();
$page = $result->fetch_assoc();
$stmt->close();

if (!$page) {
    header('HTTP/1.0 404 Not Found');
    require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header.php';
    echo "<div class='container mt-4'><h1>404 - Страница не найдена</h1></div>";
    require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php';
    $content = ob_get_clean();
    if (should_cache_path($cache_path)) {
        save_to_cache($cache_key, $content, $cache_path);
    }
    echo $content;
    exit;
}

// Функция для вычисления схожести строк
function similarity($str1, $str2) {
    $str1 = strtolower(strip_tags($str1 ?? ''));
    $str2 = strtolower(strip_tags($str2 ?? ''));
    similar_text($str1, $str2, $percent);
    return $percent;
}

// Поиск похожих страниц с кешированием
$similar_pages_cache_key = 'similar_pages_' . md5($page['id']);
$similar_pages = get_from_cache($similar_pages_cache_key, $cache_path);

if ($similar_pages === false) {
    $similar_pages = [];
    $result = $conn->query("SELECT id, title, url, content FROM pages WHERE url != '' AND url IS NOT NULL AND id != " . (int)$page['id']);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $title_similarity = similarity($page['title'], $row['title']);
            $content_similarity = similarity($page['content'], $row['content']);
            $similarity_score = ($title_similarity * 0.4) + ($content_similarity * 0.6);

            if ($similarity_score >= 20) { // Порог в 20% для отображения
                $similar_pages[] = [
                    'title' => $row['title'],
                    'url' => $row['url'],
                    'score' => $similarity_score
                ];
            }
        }
        $result->free();
    }
    usort($similar_pages, function ($a, $b) {
        return $b['score'] <=> $a['score'];
    });
    if (should_cache_path($cache_path)) {
        save_to_cache($similar_pages_cache_key, $similar_pages, $cache_path);
    }
}

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
    <title><?php echo htmlspecialchars($page['title'], ENT_QUOTES, 'UTF-8'); ?></title>
    <?php if ($page['no_index']): ?>
        <meta name="robots" content="noindex">
    <?php endif; ?>
    <style><?php echo $css_content; ?></style>
    <style><?php echo $style_php_content; ?></style>
    <style><?php echo $bootstrap_css; ?></style>
</head>
<body>
<div class="container mt-4">
    <h1><?php echo htmlspecialchars($page['title'], ENT_QUOTES, 'UTF-8'); ?></h1>

    <!-- Ветка ссылок -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/all_pages">Все страницы</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($page['title'], ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
    </nav>

    <!-- Основное содержимое -->
    <div class="content">
        <?php 
        if (!empty($page['content'])) {
            echo $page['content']; // Предполагается, что контент безопасен, иначе добавить htmlspecialchars
        } else {
            echo '<p>Содержимое отсутствует.</p>';
        }
        ?>
    </div>

    <!-- Прикрепленный файл -->
    <?php if (!empty($page['file_path'])): ?>
    <div class="file mt-4">
        <h3>Прикрепленный файл</h3>
        <p><a href="<?php echo htmlspecialchars($page['file_path'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank">Скачать файл</a></p>
    </div>
    <?php endif; ?>

    <!-- Похожие страницы -->
    <?php if (!empty($similar_pages)): ?>
    <div class="similar-pages mt-4">
        <h3>Похожие страницы</h3>
        <div class="row">
            <?php 
            $similar_pages = array_slice($similar_pages, 0, 5); // Ограничиваем до 5 страниц
            foreach ($similar_pages as $similar): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="/<?php echo htmlspecialchars($similar['url'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($similar['title'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </h5>
                            <p class="card-text">
                                Сходство: <?php echo round($similar['score'], 1); ?>%
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="similar-pages mt-4">
        <h3>Похожие страницы</h3>
        <p>Похожих страниц не найдено.</p>
    </div>
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