<?php
// admin/modules/dashboard.php — Modern Adaptive 2026 Light Version
// ВСЕ СТРОКИ И ВСЕ СЛОВА переведены через $tr (каждая кнопка, заголовок, текст, сообщение, попап)
// Ключи полностью совместимы с твоими файлами ru.php, en.php, ua.php, lt.php, no.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions_cache.php';
if (!isAdmin()) {
    header("Location: /admin/login.php");
    exit;
}
$tr = load_admin_translations();

// Статистика
$total_users = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0] ?? 0;
$total_tenders = $conn->query("SELECT COUNT(*) FROM tenders WHERE status = 'published'")->fetch_row()[0] ?? 0;
$total_products = $conn->query("SELECT COUNT(*) FROM shop_products")->fetch_row()[0] ?? 0;
$unread_feedback = $conn->query("SELECT COUNT(*) FROM feedback WHERE type = 'message' AND is_read = 0")->fetch_row()[0] ?? 0;

// Последние записи
$feedback_messages = $conn->query("SELECT * FROM feedback WHERE type = 'message' ORDER BY created_at DESC LIMIT 5")
    ->fetch_all(MYSQLI_ASSOC);
$recent_tenders = $conn->query("SELECT id, title, created_at FROM tenders WHERE status = 'published' ORDER BY created_at DESC LIMIT 5")
    ->fetch_all(MYSQLI_ASSOC);

// Статистика за 7 дней
$tender_stats = [];
$stats_query = $conn->query("SELECT DATE(created_at) as date, COUNT(*) as count
                             FROM tenders
                             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                             GROUP BY DATE(created_at)");
while ($row = $stats_query->fetch_assoc()) {
    $tender_stats[$row['date']] = $row['count'];
}

// Очистка кэша
$cache_dir = $_SERVER['DOCUMENT_ROOT'] . '/cache';
$cache_stats = get_cache_stats($cache_dir);
$success_message = '';
if (isset($_POST['clear_cache'])) {
    clear_cache($cache_dir);
    $success_message = $tr['cache_cleared'] ?? 'Кеш очищено!';
}
?>
<div class="container-fluid py-4 px-3 px-md-4">

    <!-- Навигация -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0 mb-0">
            <li class="breadcrumb-item"><a href="/admin/index.php" class="text-decoration-none text-primary"><?php echo $tr['breadcrumb_home'] ?? 'Главная'; ?></a></li>
            <li class="breadcrumb-item active fw-semibold"><?php echo $tr['dashboard'] ?? 'Дашборд'; ?></li>
        </ol>
    </nav>

    <!-- Приветствие -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5 gap-3">
        <div>
            <h1 class="fw-bold mb-1 text-dark">
                <?php echo date('m-d') === '12-25' ? ($tr['merry_christmas'] ?? 'С Рождеством! ✨') : ($tr['welcome_back'] ?? 'Добро пожаловать!'); ?>
            </h1>
            <p class="text-muted mb-0">
                <?php echo $tr['today'] ?? 'Сегодня'; ?> <?= date('d.m.Y') ?> — 
                <?php echo $tr['great_day_to_manage'] ?? 'отличный день для управления сайтом'; ?>
            </p>
        </div>
       
        <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show mb-0 shadow-sm" role="alert">
            <?php echo htmlspecialchars($success_message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Быстрые действия -->
    <div class="row g-3 mb-5">
        <div class="col-6 col-sm-6 col-md-3">
            <a href="/admin/index.php?module=tenders_add" class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3 shadow-sm rounded-4 text-white">
                <i class="bi bi-hammer fs-3 mb-2"></i>
                <span class="fw-semibold small"><?php echo $tr['add_tender_btn'] ?? 'Добавить тендер'; ?></span>
            </a>
        </div>
        <div class="col-6 col-sm-6 col-md-3">
            <a href="/admin/index.php?module=news_add" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3 shadow-sm rounded-4 text-white">
                <i class="bi bi-newspaper fs-3 mb-2"></i>
                <span class="fw-semibold small"><?php echo $tr['add_news_btn'] ?? 'Новая новость'; ?></span>
            </a>
        </div>
        <div class="col-6 col-sm-6 col-md-3">
            <a href="/admin/index.php?module=shop_add_product" class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3 shadow-sm rounded-4 text-white">
                <i class="bi bi-box-seam fs-3 mb-2"></i>
                <span class="fw-semibold small"><?php echo $tr['add_product_btn'] ?? 'Добавить товар'; ?></span>
            </a>
        </div>
        <div class="col-6 col-sm-6 col-md-3">
            <a href="/admin/index.php?module=feedback" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3 shadow-sm rounded-4 text-dark position-relative">
                <i class="bi bi-envelope fs-3 mb-2"></i>
                <span class="fw-semibold small"><?php echo $tr['messages_btn'] ?? 'Сообщения'; ?></span>
                <?php if ($unread_feedback > 0): ?>
                    <span class="position-absolute top-0 end-0 badge bg-danger rounded-pill mt-2 me-2"><?php echo $unread_feedback; ?></span>
                <?php endif; ?>
            </a>
        </div>
        <div class="col-12">
            <form method="POST" class="h-100">
                <button type="submit" name="clear_cache" class="btn btn-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3 shadow-sm rounded-4 text-white">
                    <i class="bi bi-trash3 fs-3 mb-2"></i>
                    <span class="fw-semibold"><?php echo $tr['clear_cache_btn'] ?? 'Очистить кеш'; ?></span>
                    <span class="badge bg-dark mt-1"><?php echo format_size($cache_stats['size'] ?? 0); ?></span>
                </button>
            </form>
        </div>
    </div>

    <!-- Статистика карточками -->
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-4 bg-gradient-primary text-white">
                    <i class="bi bi-people-fill fs-1 mb-3 opacity-75"></i>
                    <h5 class="fw-bold mb-1"><?php echo $tr['users_stat'] ?? 'Пользователи'; ?></h5>
                    <div class="fs-3 fw-bold"><?php echo number_format($total_users); ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-4 bg-gradient-success text-white">
                    <i class="bi bi-hammer fs-1 mb-3 opacity-75"></i>
                    <h5 class="fw-bold mb-1"><?php echo $tr['tenders_stat'] ?? 'Тендеры'; ?></h5>
                    <div class="fs-3 fw-bold"><?php echo number_format($total_tenders); ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-4 bg-gradient-info text-white">
                    <i class="bi bi-box-seam fs-1 mb-3 opacity-75"></i>
                    <h5 class="fw-bold mb-1"><?php echo $tr['products_stat'] ?? 'Товары'; ?></h5>
                    <div class="fs-3 fw-bold"><?php echo number_format($total_products); ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-4 bg-gradient-warning text-dark">
                    <i class="bi bi-envelope-open fs-1 mb-3 opacity-75"></i>
                    <h5 class="fw-bold mb-1"><?php echo $tr['unread_stat'] ?? 'Непрочитано'; ?></h5>
                    <div class="fs-3 fw-bold"><?php echo $unread_feedback; ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- График + последние записи -->
    <div class="row g-4">
        <!-- График тендеров -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 pb-0">
                    <h5 class="fw-bold mb-0"><?php echo $tr['tenders_last_7_days'] ?? 'Тендеры за 7 дней'; ?></h5>
                </div>
                <div class="card-body">
                    <canvas id="tenderChart" height="210"></canvas>
                </div>
            </div>
        </div>

        <!-- Последние сообщения -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><?php echo $tr['last_messages_title'] ?? 'Последние сообщения'; ?></h5>
                    <a href="/admin/index.php?module=feedback" class="btn btn-sm btn-outline-primary"><?php echo $tr['all_messages_link'] ?? 'Все →'; ?></a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($feedback_messages)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-chat-left-text fs-1 mb-3"></i>
                            <p><?php echo $tr['no_messages_yet'] ?? 'Пока нет сообщений'; ?></p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th><?php echo $tr['sender_column'] ?? 'Отправитель'; ?></th>
                                        <th><?php echo $tr['message_column'] ?? 'Сообщение'; ?></th>
                                        <th class="text-end"><?php echo $tr['date_column'] ?? 'Дата'; ?></th>
                                        <th width="70"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($feedback_messages as $msg): ?>
                                        <tr>
                                            <td class="fw-medium"><?php echo htmlspecialchars($msg['contact'] ?? $tr['anonymous'] ?? 'Аноним'); ?></td>
                                            <td><?php echo htmlspecialchars(substr($msg['message'] ?? '', 0, 70)) ?>…</td>
                                            <td class="text-end text-muted small"><?php echo date('d.m H:i', strtotime($msg['created_at'])); ?></td>
                                            <td>
                                                <a href="/admin/index.php?module=feedback&action=view&id=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Приветственный попап -->
<div class="modal fade" id="welcomeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <div class="modal-content rounded-4 border-0 shadow-xl">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <span class="display-1">🎉</span>
                </div>
                <h4 class="fw-bold mb-2"><?php echo $tr['popup_title'] ?? 'С возвращением!'; ?></h4>
                <p class="text-muted mb-4">
                    <?php echo date('d.m.Y'); ?><br>
                    <?php echo $tr['popup_text'] ?? 'Удачного управления сайтом сегодня!'; ?>
                </p>
                <button class="btn btn-primary px-5 py-3 rounded-3 fw-semibold" data-bs-dismiss="modal">
                    <?php echo $tr['continue_btn'] ?? 'Продолжить работу'; ?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// График тендеров
const ctx = document.getElementById('tenderChart');
if (ctx) {
    new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_keys($tender_stats)); ?>,
            datasets: [{
                label: '<?php echo $tr['tenders_chart_label'] ?? 'Тендеры'; ?>',
                data: <?php echo json_encode(array_values($tender_stats)); ?>,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.15)',
                borderWidth: 3,
                tension: 0.4,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: '#1e2937', titleColor: '#fff' }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.06)' } },
                x: { grid: { display: false } }
            }
        }
    });
}

// Попап один раз в сутки
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toDateString();
    if (localStorage.getItem('dashboardPopupShown') !== today) {
        const modal = new bootstrap.Modal(document.getElementById('welcomeModal'));
        modal.show();
        localStorage.setItem('dashboardPopupShown', today);
    }
});
</script>

<style>
    .bg-gradient-primary { background: linear-gradient(135deg, #6366f1, #4f46e5) !important; }
    .bg-gradient-success { background: linear-gradient(135deg, #10b981, #059669) !important; }
    .bg-gradient-info    { background: linear-gradient(135deg, #0ea5e9, #0284c7) !important; }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b, #d97706) !important; }
    
    @media (max-width: 576px) {
        .card-body { padding: 1.25rem !important; }
        h1 { font-size: 1.75rem !important; }
    }
</style>
