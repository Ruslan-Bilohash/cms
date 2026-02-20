<?php
session_start();

// Подключаем зависимости
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Установка заголовков безопасности
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Генерация CSRF-токена
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Получаем категории и города
$categories = $conn->query('SELECT * FROM categories')->fetch_all(MYSQLI_ASSOC);
$cities = $conn->query('SELECT * FROM cities')->fetch_all(MYSQLI_ASSOC);

// Пагинация
$per_page = 10;
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?: 1;
$start = ($page - 1) * $per_page;

// Фильтры
$search_query = '';
$category_id = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT) ?: 0;
$city_id = filter_input(INPUT_GET, 'city_id', FILTER_VALIDATE_INT) ?: 0;

// Базовый запрос
$query = '
    SELECT t.*, u.nickname, c.title AS category, ci.name AS city_name 
    FROM tenders t 
    LEFT JOIN users u ON t.user_id = u.id 
    LEFT JOIN categories c ON t.category_id = c.id 
    LEFT JOIN cities ci ON t.city_id = ci.id 
    WHERE t.status = "published"
';
$total_query = 'SELECT COUNT(DISTINCT t.id) as total FROM tenders t WHERE t.status = "published"';

// Обработка формы поиска
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Ошибка: Неверный CSRF-токен');
    }
    $search_query = htmlspecialchars(trim($_POST['search_query'] ?? ''), ENT_QUOTES, 'UTF-8');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $city_id = (int)($_POST['city_id'] ?? 0);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Обновляем токен
} elseif ($category_id || $city_id) {
    $search_query = ''; // Поддержка GET-фильтров
}

// Инициализируем массивы для параметров и типов
$params = [];
$types = '';
$total_params = [];
$total_types = '';

// Добавляем условия фильтрации
if (!empty($search_query)) {
    $query .= ' AND (t.title LIKE ? OR t.short_desc LIKE ?)';
    $total_query .= ' AND (t.title LIKE ? OR t.short_desc LIKE ?)';
    $params[] = "%$search_query%";
    $params[] = "%$search_query%";
    $total_params[] = "%$search_query%";
    $total_params[] = "%$search_query%";
    $types .= 'ss';
    $total_types .= 'ss';
}
if ($category_id) {
    $query .= ' AND t.category_id = ?';
    $total_query .= ' AND t.category_id = ?';
    $params[] = $category_id;
    $total_params[] = $category_id;
    $types .= 'i';
    $total_types .= 'i';
}
if ($city_id) {
    $query .= ' AND t.city_id = ?';
    $total_query .= ' AND t.city_id = ?';
    $params[] = $city_id;
    $total_params[] = $city_id;
    $types .= 'i';
    $total_types .= 'i';
}

// Добавляем пагинацию только к основному запросу
$query .= ' GROUP BY t.id LIMIT ?, ?';
$params[] = $start;
$params[] = $per_page;
$types .= 'ii';

// Подсчет общего количества
$stmt = $conn->prepare($total_query);
if ($stmt === false) {
    die('Ошибка подготовки total_query: ' . $conn->error);
}
if (!empty($total_params)) {
    $stmt->bind_param($total_types, ...$total_params);
}
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();
$pages = ceil($total / $per_page);

// Запрос тендеров
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('Ошибка подготовки query: ' . $conn->error);
}
// Всегда привязываем параметры, так как LIMIT присутствует
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$tenders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Обрабатываем данные тендеров
foreach ($tenders as &$tender) {
    $tender['images'] = !empty($tender['images']) ? json_decode($tender['images'], true) : [];
    $tender['short_desc'] = strlen($tender['short_desc']) > 100 ? substr($tender['short_desc'], 0, 100) . '...' : $tender['short_desc'];
}
unset($tender);

// Подключаем header
include $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все тендеры - Tender CMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<main class="container py-5">
    <h1 class="text-center mb-4">Все тендеры</h1>

    <!-- Форма поиска -->
    <form method="POST" class="col-md-8 mx-auto mb-5">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="input-group">
            <input type="text" name="search_query" class="form-control" placeholder="Поиск по тендерам" value="<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>">
            <select name="category_id" class="form-select" style="max-width: 200px;">
                <option value="">Все категории</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo (int)$cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['title'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="city_id" class="form-select" style="max-width: 200px;">
                <option value="">Все города</option>
                <?php foreach ($cities as $city): ?>
                    <option value="<?php echo (int)$city['id']; ?>" <?php echo $city_id == $city['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($city['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="search" class="btn btn-primary">Найти</button>
        </div>
    </form>

    <!-- Кнопка "Добавить тендер" -->
    <div class="text-center mb-5">
        <a href="/add_tender" class="btn btn-primary btn-lg py-4 px-5" style="font-size: 2rem;">Добавить тендер</a>
    </div>

    <!-- Список тендеров -->
    <div class="row">
        <?php if (empty($tenders)): ?>
            <p class="text-center">Тендеры не найдены.</p>
        <?php else: ?>
            <?php foreach ($tenders as $tender): ?>
                <div class="col-md-12 mb-4">
                    <div class="card d-flex flex-row shadow-sm modern-tender-card">
                        <?php if (!empty($tender['images']) && image_exists($tender['images'][0], $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/tenders/images/')): ?>
                            <img src="/public/uploads/tenders/images/<?php echo htmlspecialchars($tender['images'][0], ENT_QUOTES, 'UTF-8'); ?>" 
                                 class="img-fluid" 
                                 width="230" height="100" 
                                 style="object-fit: cover;" 
                                 alt="<?php echo htmlspecialchars($tender['title'], ENT_QUOTES, 'UTF-8'); ?>" 
                                 loading="lazy">
                        <?php else: ?>
                            <div style="width: 230px; height: 100px; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                Нет изображения<?php echo !empty($tender['images']) ? ' (файл не найден: ' . htmlspecialchars($tender['images'][0], ENT_QUOTES, 'UTF-8') . ')' : ''; ?>
                            </div>
                        <?php endif; ?>
                        <div class="card-body flex-grow-1">
                            <h5 class="card-title">
                                <a href="/tenders_full/<?php echo (int)$tender['id']; ?>"><?php echo htmlspecialchars($tender['title'], ENT_QUOTES, 'UTF-8'); ?></a>
                                <?php echo $tender['city_name'] ? ' (' . htmlspecialchars($tender['city_name'], ENT_QUOTES, 'UTF-8') . ')' : ''; ?>
                            </h5>
                            <p class="card-text">
                                <a href="/tenders?category_id=<?php echo (int)$tender['category_id']; ?>" class="text-muted">
                                    <?php echo htmlspecialchars($tender['category'] ?? 'Нет категории', ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </p>
                            <p class="card-text"><?php echo htmlspecialchars($tender['short_desc'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="card-text">Бюджет: <?php echo number_format($tender['budget'], 2, '.', ''); ?> грн</p>
                            <a href="/tenders_full/<?php echo (int)$tender['id']; ?>" class="btn btn-primary btn-sm">Смотреть</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Пагинация -->
    <?php if ($pages > 1): ?>
        <nav aria-label="Пагинация тендеров">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="/tenders?page=<?php echo $i; ?><?php echo $category_id ? '&category_id=' . $category_id : ''; ?><?php echo $city_id ? '&city_id=' . $city_id : ''; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</main>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>
</body>
</html>