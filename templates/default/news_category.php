<?php
// Включаем отображение ошибок для диагностики
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Получение всех категорий для списка
$categories = $conn->query("SELECT * FROM news_categories ORDER BY title")->fetch_all(MYSQLI_ASSOC);
$category_tree = buildCategoryTree($categories);

// Получение URL параметра
$custom_url = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

// Поиск по категории (если есть запрос поиска)
$search_query = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
if ($search_query) {
    $stmt = $conn->prepare("SELECT * FROM news_categories WHERE title LIKE ? OR custom_url LIKE ?");
    $like_query = "%$search_query%";
    $stmt->bind_param("ss", $like_query, $like_query);
    $stmt->execute();
    $search_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Если custom_url пустой, показываем список всех категорий
if (empty($custom_url)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header.php';
    ?>
    <main class="container py-4">
        <h1>Все категории новостей</h1>
        <?php if (empty($category_tree) || !is_array($category_tree)): ?>
            <p>Категорий пока нет или данные некорректны.</p>
        <?php else: ?>
            <ul class="list-group">
                <?php foreach ($category_tree as $cat): ?>
                    <?php if (!is_array($cat)): ?>
                        <li class="list-group-item">Ошибка: неверные данные категории - <?php echo htmlspecialchars($cat); ?></li>
                    <?php else: ?>
                        <li class="list-group-item">
                            <a href="/news_category?category=<?php echo htmlspecialchars($cat['custom_url'] ?? ''); ?>">
                                <?php echo str_repeat('— ', $cat['level'] ?? 0) . htmlspecialchars($cat['title'] ?? 'Без названия'); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </main>
    <?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php';
    exit;
}

// Поиск категории по custom_url
$stmt = $conn->prepare("SELECT * FROM news_categories WHERE custom_url = ?");
$stmt->bind_param("s", $custom_url);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$category) {
    header("HTTP/1.0 404 Not Found");
    $path_404 = $_SERVER['DOCUMENT_ROOT'] . '/templates/default/404.php';
    if (file_exists($path_404)) {
        include $path_404;
    } else {
        echo "<h1>404 - Страница не найдена</h1><p>Файл 404.php отсутствует.</p>";
    }
    exit;
}

// Получение новостей данной категории
$stmt = $conn->prepare("SELECT * FROM news WHERE category_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->bind_param("i", $category['id']);
$stmt->execute();
$news = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Хлебные крошки
function getBreadcrumbs($conn, $category_id) {
    $breadcrumbs = [];
    while ($category_id) {
        $stmt = $conn->prepare("SELECT id, title, custom_url, parent_id FROM news_categories WHERE id = ?");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $cat = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        
        if ($cat) {
            $breadcrumbs[] = $cat;
            $category_id = $cat['parent_id'];
        } else {
            break;
        }
    }
    return array_reverse($breadcrumbs);
}

$breadcrumbs = getBreadcrumbs($conn, $category['id']);
?>

<!DOCTYPE html>
<html lang="ru">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($category['meta_title'] ?? $category['title'] ?? 'Категория'); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($category['meta_desc'] ?? $category['description'] ?? ''); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($category['keywords'] ?? ''); ?>">
<!-- Open Graph -->
<meta property="og:title" content="<?php echo htmlspecialchars($category['og_title'] ?? $category['title'] ?? ''); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($category['og_desc'] ?? $category['description'] ?? ''); ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . '/news_category?category=' . htmlspecialchars($category['custom_url'] ?? ''); ?>">
<!-- Twitter Cards -->
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="<?php echo htmlspecialchars($category['twitter_title'] ?? $category['title'] ?? ''); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($category['twitter_desc'] ?? $category['description'] ?? ''); ?>">
<link rel="canonical" href="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . '/news_category?category=' . htmlspecialchars($category['custom_url'] ?? ''); ?>">
<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<!-- Основные стили -->
<link rel="stylesheet" href="/templates/default/css/style.php?v=<?php echo time(); ?>">

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header.php'; ?>

<!-- Хлебные крошки с микроразметкой -->
<nav aria-label="breadcrumb" class="container py-3">
    <ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
        <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <a itemprop="item" href="/">
                <span itemprop="name">Главная</span>
            </a>
            <meta itemprop="position" content="1">
        </li>
        <?php foreach ($breadcrumbs as $index => $crumb): ?>
            <li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <?php if ($index === count($breadcrumbs) - 1): ?>
                    <span itemprop="name"><?php echo htmlspecialchars($crumb['title'] ?? ''); ?></span>
                <?php else: ?>
                    <a itemprop="item" href="/news_category?category=<?php echo htmlspecialchars($crumb['custom_url'] ?? ''); ?>">
                        <span itemprop="name"><?php echo htmlspecialchars($crumb['title'] ?? ''); ?></span>
                    </a>
                <?php endif; ?>
                <meta itemprop="position" content="<?php echo $index + 2; ?>">
            </li>
        <?php endforeach; ?>
    </ol>
</nav>

<!-- Основной контент -->
<main class="container py-4">
    <div class="row">
        <!-- Основной контент -->
        <div class="col-md-8">
            <article itemscope itemtype="https://schema.org/NewsArticle">
                <header>
                    <h1 itemprop="headline"><?php echo htmlspecialchars($category['title'] ?? 'Без названия'); ?></h1>
                    <?php if (!empty($category['description'])): ?>
                        <div itemprop="description"><?php echo nl2br(htmlspecialchars($category['description'])); ?></div>
                    <?php endif; ?>
                    <meta itemprop="datePublished" content="<?php echo $category['created_at'] ?? ''; ?>">
                </header>

                <!-- Список новостей -->
                <section class="news-list">
                    <?php if (empty($news) || !is_array($news)): ?>
                        <p>Новостей в этой категории пока нет или данные некорректны.</p>
                    <?php else: ?>
                        <?php foreach ($news as $item): ?>
                            <?php if (!is_array($item)): ?>
                                <p>Ошибка: неверные данные новости - <?php echo htmlspecialchars($item); ?></p>
                            <?php else: ?>
                                <div class="news-item" itemscope itemtype="https://schema.org/NewsArticle">
                                    <h2 itemprop="headline">
                                        <a href="/news/<?php echo htmlspecialchars($item['id'] ?? ''); ?>" itemprop="url">
                                            <?php echo htmlspecialchars($item['title'] ?? 'Без названия'); ?>
                                        </a>
                                    </h2>
                                    <meta itemprop="datePublished" content="<?php echo htmlspecialchars($item['created_at'] ?? ''); ?>">
                                    <?php if (!empty($item['short_desc'])): ?>
                                        <p itemprop="description"><?php echo htmlspecialchars($item['short_desc']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </article>
        </div>

        <!-- Боковая панель: категории и поиск -->
        <div class="col-md-4">
            <!-- Поиск по категориям -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Поиск категорий</h5>
                    <form method="GET" action="/news_category">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Введите название..." value="<?php echo htmlspecialchars($search_query); ?>">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                    <?php if ($search_query && !empty($search_results) && is_array($search_results)): ?>
                        <ul class="list-group mt-3">
                            <?php foreach ($search_results as $result): ?>
                                <li class="list-group-item">
                                    <a href="/news_category?category=<?php echo htmlspecialchars($result['custom_url'] ?? ''); ?>">
                                        <?php echo htmlspecialchars($result['title'] ?? 'Без названия'); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php elseif ($search_query): ?>
                        <p class="mt-3 text-muted">Ничего не найдено</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Список категорий -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Категории</h5>
                    <?php if (empty($category_tree) || !is_array($category_tree)): ?>
                        <p>Ошибка: список категорий недоступен.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($category_tree as $cat): ?>
                                <?php if (!is_array($cat)): ?>
                                    <li class="list-group-item">Ошибка: неверные данные категории - <?php echo htmlspecialchars($cat); ?></li>
                                <?php else: ?>
                                    <li class="list-group-item <?php echo ($cat['id'] ?? '') == ($category['id'] ?? '') ? 'active' : ''; ?>">
                                        <a href="/news_category?category=<?php echo htmlspecialchars($cat['custom_url'] ?? ''); ?>">
                                            <?php echo str_repeat('— ', $cat['level'] ?? 0) . htmlspecialchars($cat['title'] ?? 'Без названия'); ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- JSON-LD микроразметка -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "CollectionPage",
    "name": "<?php echo htmlspecialchars($category['title'] ?? ''); ?>",
    "description": "<?php echo htmlspecialchars($category['description'] ?? $category['meta_desc'] ?? ''); ?>",
    "url": "<?php echo 'https://' . $_SERVER['HTTP_HOST'] . '/news_category?category=' . htmlspecialchars($category['custom_url'] ?? ''); ?>",
    "breadcrumb": {
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Главная",
                "item": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/"
            }
            <?php foreach ($breadcrumbs as $index => $crumb): ?>
            ,{
                "@type": "ListItem",
                "position": <?php echo $index + 2; ?>,
                "name": "<?php echo htmlspecialchars($crumb['title'] ?? ''); ?>",
                "item": "https://<?php echo $_SERVER['HTTP_HOST']; ?>/news_category?category=<?php echo htmlspecialchars($crumb['custom_url'] ?? ''); ?>"
            }
            <?php endforeach; ?>
        ]
    }
}
</script>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</html>