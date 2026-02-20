<?php
session_start();

// Подключаем зависимости с использованием абсолютных путей
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Установка заголовков безопасности
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Получаем ID тендера и валидируем его
$tender_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;

if ($tender_id <= 0) {
    header('Location: /'); // Редирект на корень, так как главная страница обрабатывается через RewriteRule
    exit;
}

// Подготовленное выражение для защиты от SQL-инъекций
$stmt = $conn->prepare('
    SELECT t.*, c.title AS category_title, ci.name AS city_name 
    FROM tenders t 
    LEFT JOIN categories c ON t.category_id = c.id 
    LEFT JOIN cities ci ON t.city_id = ci.id 
    WHERE t.id = ?
');
$stmt->bind_param('i', $tender_id);
$stmt->execute();
$tender = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$tender) {
    header('Location: /'); // Редирект на корень при отсутствии тендера
    exit;
}

// Декодируем изображения и документы
$images = !empty($tender['images']) ? json_decode($tender['images'], true) : [];
$documents = !empty($tender['documents']) ? json_decode($tender['documents'], true) : [];

// Получаем настройки
$settings = $conn->query('SELECT * FROM settings WHERE id = 1')->fetch_assoc();

// Подключаем header и footer
include $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header.php';
?>

<main class="container py-5">
    <!-- Заголовок и дата -->
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <h1 class="mb-2"><?php echo htmlspecialchars($tender['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
            <span class="badge bg-secondary"><?php echo date('d-m-Y H:i:s', strtotime($tender['created_at'])); ?></span>
        </div>
        <hr class="my-3">
    </div>

    <div class="row">
        <!-- Главная информация и изображение -->
        <div class="col-md-8 col-12 mb-4">
            <?php if (!empty($images)): ?>
                <div class="mb-4">
                    <img src="/public/uploads/tenders/images/<?php echo htmlspecialchars($images[0], ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid rounded shadow main-image" alt="Главное изображение тендера" style="max-height: 400px; width: 100%; object-fit: cover;">
                </div>
            <?php endif; ?>
            <div class="card mb-4">
                <div class="card-header text-white" style="background-color: <?php echo htmlspecialchars($settings['header_background_color'] ?? '#007bff', ENT_QUOTES, 'UTF-8'); ?>;">
                    <h5 class="card-title mb-0">Описание</h5>
                </div>
                <div class="card-body">
                    <div class="card-text"><?php echo nl2br(htmlspecialchars_decode($tender['short_desc'])); ?></div>
                </div>
            </div>
        </div>

        <!-- Боковая панель с деталями -->
        <div class="col-md-4 col-12 mb-4">
            <div class="card">
                <div class="card-header text-white" style="background-color: <?php echo htmlspecialchars($settings['header_background_color'] ?? '#007bff', ENT_QUOTES, 'UTF-8'); ?>;">
                    <h5 class="card-title mb-0">Детали тендера</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <strong>Имя:</strong> 
                            <span class="fw-bold fs-4"><?php echo htmlspecialchars($tender['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <strong>Город:</strong> 
                            <a href="/tenders?city_id=<?php echo (int)$tender['city_id']; ?>" class="text-decoration-none text-end"><?php echo htmlspecialchars($tender['city_name'] ?? 'Не указан', ENT_QUOTES, 'UTF-8'); ?></a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <strong>Категория:</strong> 
                            <a href="/tenders?category_id=<?php echo (int)$tender['category_id']; ?>" class="text-decoration-none text-end"><?php echo htmlspecialchars($tender['category_title'] ?? 'Без категории', ENT_QUOTES, 'UTF-8'); ?></a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <strong>Бюджет:</strong> 
                            <span><?php echo number_format($tender['budget'], 2); ?> грн</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <button class="btn btn-primary btn-lg w-100 mt-2" onclick="this.innerHTML = '<?php echo htmlspecialchars($tender['phone'], ENT_QUOTES, 'UTF-8'); ?>'; this.disabled = true;">Показать телефон</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Мини-галерея -->
    <?php if (count($images) > 1): ?>
        <div class="row mb-4">
            <h3 class="mb-3 text-white p-2 rounded" style="background-color: <?php echo htmlspecialchars($settings['header_background_color'] ?? '#007bff', ENT_QUOTES, 'UTF-8'); ?>;">Дополнительные изображения</h3>
            <?php foreach (array_slice($images, 1) as $index => $img): ?>
                <div class="col-md-3 col-sm-6 col-12 mb-3">
                    <img src="/public/uploads/tenders/images/<?php echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid rounded shadow-sm gallery-img" alt="Изображение тендера" style="max-height: 150px; width: 100%; object-fit: cover;" data-index="<?php echo $index + 1; ?>">
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Документы -->
    <?php if (!empty($documents)): ?>
        <div class="row">
            <h3 class="mb-3 text-white p-2 rounded" style="background-color: <?php echo htmlspecialchars($settings['header_background_color'] ?? '#007bff', ENT_QUOTES, 'UTF-8'); ?>;">Документы</h3>
            <div class="col-12">
                <ul class="list-group">
                    <?php foreach ($documents as $doc): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="/public/uploads/tenders/documents/<?php echo htmlspecialchars($doc, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="text-decoration-none">
                                <i class="fas fa-file-pdf me-2"></i> <?php echo htmlspecialchars(basename($doc), ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                            <a href="/public/uploads/tenders/documents/<?php echo htmlspecialchars($doc, ENT_QUOTES, 'UTF-8'); ?>" download class="btn btn-sm btn-success">
                                <i class="fas fa-download"></i> Скачать
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</main>

<!-- Модальное окно для галереи -->
<div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="galleryModalLabel">Галерея изображений</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalImage" class="img-fluid" style="max-height: 500px; width: auto;">
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-primary" id="prevBtn"><i class="fas fa-arrow-left"></i> Назад</button>
                <button type="button" class="btn btn-primary" id="nextBtn">Вперёд <i class="fas fa-arrow-right"></i></button>
            </div>
        </div>
    </div>
</div>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const galleryImages = document.querySelectorAll('.gallery-img');
    const modal = new bootstrap.Modal(document.getElementById('galleryModal'));
    const modalImage = document.getElementById('modalImage');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    let currentIndex;

    galleryImages.forEach(img => {
        img.addEventListener('click', function () {
            currentIndex = parseInt(this.getAttribute('data-index'));
            updateModalImage();
            modal.show();
        });
    });

    prevBtn.addEventListener('click', function () {
        if (currentIndex > 1) {
            currentIndex--;
            updateModalImage();
        }
    });

    nextBtn.addEventListener('click', function () {
        if (currentIndex < <?php echo count($images) - 1; ?>) {
            currentIndex++;
            updateModalImage();
        }
    });

    function updateModalImage() {
        const images = <?php echo json_encode(array_slice($images, 1)); ?>;
        modalImage.src = '/public/uploads/tenders/images/' + images[currentIndex - 1];
        prevBtn.disabled = currentIndex === 1;
        nextBtn.disabled = currentIndex === <?php echo count($images) - 1; ?>;
    }
});
</script>