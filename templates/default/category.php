<?php
session_start();

// Подключаем зависимости с абсолютными путями
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Установка заголовков безопасности
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($category_id <= 0) {
    header("Location: /tenders");
    exit;
}

// Получаем информацию о категории
$stmt = $conn->prepare("SELECT title FROM categories WHERE id = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$category) {
    header("Location: /tenders");
    exit;
}

// Получаем тендеры для этой категории
$stmt = $conn->prepare("
    SELECT t.*, u.nickname, ci.name AS city_name 
    FROM tenders t 
    LEFT JOIN users u ON t.user_id = u.id 
    LEFT JOIN cities ci ON t.city_id = ci.id 
    WHERE t.category_id = ? AND t.status = 'published'
");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$tenders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Декодируем изображения и обрезаем описание
foreach ($tenders as &$tender) {
    $tender['images'] = !empty($tender['images']) ? json_decode($tender['images'], true) : [];
    $tender['short_desc'] = strlen($tender['short_desc']) > 100 ? substr($tender['short_desc'], 0, 100) . '...' : $tender['short_desc'];
}

// Подключаем header и footer с абсолютными путями
require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header.php';
?>

<main class="container py-5">
    <h1 class="text-center mb-4">Тендеры в категории: <?php echo htmlspecialchars($category['title']); ?></h1>

    <div class="row">
        <?php if (empty($tenders)): ?>
            <p class="text-center">Тендеры в этой категории не найдены.</p>
        <?php else: ?>
            <?php foreach ($tenders as $tender): ?>
                <div class="col-md-12 mb-4">
                    <div class="card d-flex flex-row shadow-sm">
                        <?php if (!empty($tender['images']) && image_exists($tender['images'][0], $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/tenders/images/')): ?>
                            <img src="/public/uploads/tenders/images/<?php echo htmlspecialchars($tender['images'][0]); ?>" class="img-fluid" style="width: 230px; height: auto; object-fit: cover;" alt="<?php echo htmlspecialchars($tender['title']); ?>">
                        <?php else: ?>
                            <div style="width: 230px; height: 100px; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                Нет изображения
                            </div>
                        <?php endif; ?>
                        <div class="card-body flex-grow-1">
                            <h5 class="card-title">
                                <a href="/tenders_full/<?php echo $tender['id']; ?>"><?php echo htmlspecialchars($tender['title']); ?></a>
                                <?php echo $tender['city_name'] ? ' (' . htmlspecialchars($tender['city_name']) . ')' : ''; ?>
                            </h5>
                            <p class="card-text"><?php echo htmlspecialchars($tender['short_desc']); ?></p>
                            <p class="card-text">Бюджет: <?php echo number_format($tender['budget'], 2, '.', ''); ?> грн</p>
                            <a href="/tenders_full/<?php echo $tender['id']; ?>" class="btn btn-primary btn-sm">Смотреть</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>