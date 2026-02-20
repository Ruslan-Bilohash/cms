<?php
// admin/modules/dashboard.php — Modern 2025+ version
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions_cache.php';

if (!isAdmin()) {
    header("Location: /admin/login.php");
    exit;
}

$tr = load_admin_translations();

// Статистика (без изменений)
$total_users      = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0] ?? 0;
$total_tenders    = $conn->query("SELECT COUNT(*) FROM tenders WHERE status = 'published'")->fetch_row()[0] ?? 0;
$total_categories = $conn->query("SELECT COUNT(*) FROM categories")->fetch_row()[0] ?? 0;
$total_feedback   = $conn->query("SELECT COUNT(*) FROM feedback WHERE type = 'message'")->fetch_row()[0] ?? 0;
$unread_feedback  = $conn->query("SELECT COUNT(*) FROM feedback WHERE type = 'message' AND is_read = 0")->fetch_row()[0] ?? 0;

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

<div class="container-fluid py-4">

    <!-- Навигация -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-transparent p-0">
            <li class="breadcrumb-item"><a href="/admin/index.php" class="text-decoration-none">Главная</a></li>
            <li class="breadcrumb-item active">Дашборд</li>
        </ol>
    </nav>

    <!-- Приветствие -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5 gap-3">
        <h1 class="fw-bold mb-0">
            <?php echo date('m-d') === '12-25' ? 'С Рождеством! ✨' : 'Добро пожаловать!'; ?>
        </h1>
        
        <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Быстрые действия - горизонтальный скролл на мобильных -->
    <div class="d-flex gap-3 mb-5 overflow-auto pb-3" style="scrollbar-width: thin;">
        <a href="?module=tenders_add" class="btn btn-modern bg-success text-white flex-shrink-0">
            <i class="fas fa-gavel me-2"></i>Добавить тендер
        </a>
        <a href="?module=news_add" class="btn btn-modern bg-primary text-white flex-shrink-0">
            <i class="fas fa-newspaper me-2"></i>Новая новость
        </a>
        <a href="?module=shop_add_product" class="btn btn-modern bg-info text-white flex-shrink-0">
            <i class="fas fa-box-open me-2"></i>Добавить товар
        </a>
        <a href="?module=feedback" class="btn btn-modern bg-warning text-dark position-relative flex-shrink-0">
            <i class="fas fa-envelope me-2"></i>Сообщения
            <?php if ($unread_feedback > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?php echo $unread_feedback; ?>
                </span>
            <?php endif; ?>
        </a>
        <form method="POST" class="d-inline">
            <button type="submit" name="clear_cache" class="btn btn-modern bg-danger text-white flex-shrink-0">
                <i class="fas fa-trash-alt me-2"></i>Очистить кеш
                <span class="badge bg-dark ms-2"><?php echo format_size($cache_stats['size'] ?? 0); ?></span>
            </button>
        </form>
    </div>

    <!-- Статистика карточками -->
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3 col-xl-3">
            <div class="card-modern stat-card bg-gradient-primary">
                <i class="fas fa-users icon"></i>
                <h5>Пользователи</h5>
                <div class="number"><?php echo number_format($total_users); ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-3">
            <div class="card-modern stat-card bg-gradient-success">
                <i class="fas fa-gavel icon"></i>
                <h5>Тендеры</h5>
                <div class="number"><?php echo number_format($total_tenders); ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-3">
            <div class="card-modern stat-card bg-gradient-info">
                <i class="fas fa-box-open icon"></i>
                <h5>Товары</h5>
                <div class="number"><?php echo number_format($total_products ?? 0); ?></div>
            </div>
        </div>
        <div class="col-6 col-md-3 col-xl-3">
            <div class="card-modern stat-card bg-gradient-warning text-dark">
                <i class="fas fa-envelope icon"></i>
                <h5>Непрочитано</h5>
                <div class="number"><?php echo $unread_feedback; ?></div>
            </div>
        </div>
    </div>

    <!-- График + последние записи (2 колонки на больших экранах) -->
    <div class="row g-4">
        <!-- График -->
        <div class="col-lg-6">
            <div class="card-modern h-100">
                <div class="card-header bg-transparent border-0 pt-4">
                    <h5 class="mb-0">Тендеры за 7 дней</h5>
                </div>
                <div class="card-body">
                    <canvas id="tenderChart" height="140"></canvas>
                </div>
            </div>
        </div>

        <!-- Последние сообщения -->
        <div class="col-lg-6">
            <div class="card-modern h-100">
                <div class="card-header bg-transparent border-0 pt-4">
                    <h5 class="mb-0">Последние сообщения</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($feedback_messages)): ?>
                        <p class="text-center py-5 text-muted">Пока нет сообщений</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Отправитель</th>
                                        <th>Сообщение</th>
                                        <th>Дата</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($feedback_messages as $msg): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($msg['contact'] ?? 'Аноним'); ?></td>
                                        <td><?php echo htmlspecialchars(substr($msg['message'] ?? '', 0, 60)) . '...'; ?></td>
                                        <td><?php echo date('d.m H:i', strtotime($msg['created_at'])); ?></td>
                                        <td>
                                            <a href="?module=feedback&action=view&id=<?php echo $msg['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
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

<!-- Новогодний/приветственный попап -->
<div class="overlay" id="overlay"></div>
<div class="popup position-fixed top-50 start-50 translate-middle p-4 text-center" id="welcomePopup" style="max-width:420px;z-index:1050;display:none;">
    <h4 class="mb-3">С возвращением! 🎉</h4>
    <p class="mb-4"><?php echo date('d.m.Y'); ?><br>Удачного управления!</p>
    <button class="btn btn-modern bg-primary text-white px-5" onclick="closePopup()">Продолжить</button>
</div>

<script>
// График
const ctx = document.getElementById('tenderChart')?.getContext('2d');
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_keys($tender_stats)); ?>,
            datasets: [{
                label: 'Тендеры',
                data: <?php echo json_encode(array_values($tender_stats)); ?>,
                borderColor: 'rgba(99,102,241,1)',
                backgroundColor: 'rgba(99,102,241,0.2)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: 'rgba(99,102,241,1)',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
}

// Попап
function showPopup() {
    const p = document.getElementById('welcomePopup');
    const o = document.getElementById('overlay');
    p.style.display = 'block';
    o.style.display = 'block';
    setTimeout(() => p.classList.add('show'), 100);
}

function closePopup() {
    const p = document.getElementById('welcomePopup');
    p.classList.remove('show');
    setTimeout(() => {
        p.style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    }, 500);
}

// Показывать попап только раз в сутки (по желанию)
if (!localStorage.getItem('dashboardPopupShown') || localStorage.getItem('dashboardPopupShown') !== new Date().toDateString()) {
    window.addEventListener('load', showPopup);
    localStorage.setItem('dashboardPopupShown', new Date().toDateString());
}
</script>

<style>
    /* Быстрые градиенты для карточек */
    .bg-gradient-primary { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-gradient-info    { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
</style>