<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Завантаження налаштувань із site_settings.php
$settings_file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/site_settings.php';
$settings = [];
$tiny_api_key = '';
if (file_exists($settings_file)) {
    $settings = include $settings_file;
    $tiny_api_key = $settings['tiny_api_key'] ?? '';
}

// Установка заголовков безопасности
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

// Загрузка настроек сайта (оставляем для совместимости)
$settings = file_exists($settings_file) ? include $settings_file : [];

// Проверяем наличие custom_url или id в URL
$custom_url = isset($_GET['custom_url']) ? $_GET['custom_url'] : null;
$news_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$custom_url && !$news_id) {
    header("Location: /news.php");
    exit;
}

// Если передан id, делаем редирект на custom_url
if ($news_id) {
    $stmt = $conn->prepare("SELECT custom_url FROM news WHERE id = ? AND published = 1");
    $stmt->bind_param("i", $news_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        header("Location: /news/{$row['custom_url']}", true, 301);
        exit;
    } else {
        header("Location: /news.php");
        exit;
    }
    $stmt->close();
}

// Получаем новость по custom_url
$stmt = $conn->prepare("SELECT n.*, c.title AS category_title FROM news n LEFT JOIN news_categories c ON n.category_id = c.id WHERE n.custom_url = ? AND n.published = 1");
$stmt->bind_param("s", $custom_url);
$stmt->execute();
$news_item = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$news_item) {
    header("HTTP/1.0 404 Not Found");
    echo '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>404 - Новость не найдена</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '</head><body>';
    echo '<div class="container my-4"><h1>404 - Новость не найдена</h1></div>';
    echo '<footer class="footer p-3 text-white text-center bg-dark"><div class="container">';
    echo '<a href="/privacy-policy" class="text-white text-decoration-none">Политика конфиденциальности</a> | ';
    echo '<a href="/terms-of-use" class="text-white text-decoration-none">Условия использования</a> | ';
    echo '<a href="/feedback" class="text-white text-decoration-none">Обратная связь</a>';
    echo '<p class="small mt-1">© ' . date('Y') . ' Tender CMS. Все права защищены. <br><a href="/page/help" class="text-white text-decoration-none">Справка</a></p>';
    echo '</div></footer></body></html>';
    exit;
}

// Получаем похожие новости
$category_id = $news_item['category_id'];
$similar_stmt = $conn->prepare("SELECT id, title, image, custom_url FROM news WHERE category_id = ? AND id != ? AND published = 1 ORDER BY created_at DESC LIMIT 3");
$similar_stmt->bind_param("ii", $category_id, $news_item['id']);
$similar_stmt->execute();
$similar_news = $similar_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$similar_stmt->close();

$categories = $conn->query("SELECT * FROM news_categories")->fetch_all(MYSQLI_ASSOC);

// Обработка изображений
$images = $news_item['image'] ? json_decode($news_item['image'], true) : [];
$main_image = !empty($images) ? 'https://' . $_SERVER['HTTP_HOST'] . '/public/uploads/news/' . $images[0] : 'https://' . $_SERVER['HTTP_HOST'] . '/assets/placeholder.png';

// Мета-теги из таблицы news
$page_title = htmlspecialchars($news_item['meta_title'] ?? $news_item['title']);
$page_description = htmlspecialchars($news_item['meta_desc'] ?? $news_item['short_desc']);
$page_keywords = htmlspecialchars($news_item['keywords'] ?? '');
$page_og_title = htmlspecialchars($news_item['og_title'] ?? $news_item['title']);
$page_og_description = htmlspecialchars($news_item['og_desc'] ?? $news_item['short_desc']);
$page_og_image = !empty($images) ? $main_image : '';
$page_og_url = 'https://' . $_SERVER['HTTP_HOST'] . '/news/' . htmlspecialchars($news_item['custom_url']);
$page_og_type = 'article';
$page_twitter_title = htmlspecialchars($news_item['twitter_title'] ?? $news_item['title']);
$page_twitter_description = htmlspecialchars($news_item['twitter_desc'] ?? $news_item['short_desc']);
$page_twitter_card_type = !empty($page_og_image) ? 'summary_large_image' : 'summary';

// Настройки кнопок
$button_size_class = '';
switch ($settings['button_size'] ?? 'medium') {
    case 'small': $button_size_class = 'btn-sm'; break;
    case 'large': $button_size_class = 'btn-lg'; break;
    default: $button_size_class = ''; break;
}
$button_shape = (int)($settings['button_shape'] ?? 0);

// Получаем отзывы и средний рейтинг
$reviews = [];
$average_rating = 0;
$reviews_per_page = $settings['reviews_per_page'] ?? 10;
$page = isset($_GET['review_page']) ? max(1, (int)$_GET['review_page']) : 1;
$offset = ($page - 1) * $reviews_per_page;

if ($news_item['reviews_enabled']) {
    // Получаем отзывы
    $stmt = $conn->prepare("SELECT r.*, u.first_name, u.last_name, u.photo, u.id AS user_id FROM news_reviews r LEFT JOIN users u ON r.user_id = u.id WHERE r.news_id = ? AND r.is_approved = 1 ORDER BY r.created_at DESC LIMIT ? OFFSET ?");
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }
    $stmt->bind_param("iii", $news_item['id'], $reviews_per_page, $offset);
    $stmt->execute();
    $reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Подсчет общего количества отзывов
    $stmt = $conn->prepare("SELECT COUNT(*) FROM news_reviews WHERE news_id = ? AND is_approved = 1");
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }
    $stmt->bind_param("i", $news_item['id']);
    $stmt->execute();
    $total_reviews = $stmt->get_result()->fetch_row()[0];
    $stmt->close();
    $total_pages = ceil($total_reviews / $reviews_per_page);

    // Рассчитываем средний рейтинг
    $stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating FROM news_reviews WHERE news_id = ? AND is_approved = 1");
    $stmt->bind_param("i", $news_item['id']);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $average_rating = $result['avg_rating'] ? round($result['avg_rating'], 1) : 0;
    $stmt->close();
}

// Проверка интервала публикации для авторизованных пользователей
$can_post_review = true;
if (isset($_SESSION['user_id']) && ($settings['review_interval'] ?? 0) > 0) {
    $interval_minutes = $settings['review_interval'];
    $stmt = $conn->prepare("SELECT created_at FROM news_reviews WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $last_review_time = strtotime($row['created_at']);
        $current_time = time();
        $minutes_since_last = ($current_time - $last_review_time) / 60;
        if ($minutes_since_last < $interval_minutes) {
            $can_post_review = false;
            $_SESSION['error'] = "Вы можете оставить следующий отзыв через " . ceil($interval_minutes - $minutes_since_last) . " минут.";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $page_description; ?>">
    <meta name="keywords" content="<?php echo $page_keywords; ?>">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="<?php echo $page_og_title; ?>">
    <meta property="og:description" content="<?php echo $page_og_description; ?>">
    <meta property="og:type" content="<?php echo $page_og_type; ?>">
    <meta property="og:url" content="<?php echo $page_og_url; ?>">
    <?php if ($page_og_image): ?>
        <meta property="og:image" content="<?php echo $page_og_image; ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="<?php echo $page_twitter_card_type; ?>">
    <meta name="twitter:title" content="<?php echo $page_twitter_title; ?>">
    <meta name="twitter:description" content="<?php echo $page_twitter_description; ?>">
    <?php if ($page_og_image): ?>
        <meta name="twitter:image" content="<?php echo $page_og_image; ?>">
    <?php endif; ?>
    <link rel="canonical" href="<?php echo $page_og_url; ?>">
    <title><?php echo $page_title; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Отладка: вывод значения tiny_api_key -->
    <?php var_dump($tiny_api_key); ?>
    <script src="https://cdn.tiny.cloud/1/<?php echo htmlspecialchars($tiny_api_key); ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        .fullnews-btn-modern, 
        .fullnews-similar-btn, 
        .fullnews-nav-pills .btn {
            border-radius: <?php echo $button_shape; ?>px !important;
            padding: 8px 20px;
            transition: all 0.3s ease;
        }
        .fullnews-nav-pills { gap: 10px; }
        .fullnews-content { position: relative; z-index: 1; }
        .fullnews-content h2, .fullnews-content h3 { margin-top: 1.5rem; }
        .fullnews-content ul, .fullnews-content ol { padding-left: 20px; }
        .fullnews-content p { line-height: 1.6; }
        .fullnews-main-image-container {
            position: relative;
            width: 100%;
            max-height: 600px;
            overflow: hidden;
            border-radius: 15px;
        }
        .fullnews-main-image {
            width: 100%;
            height: auto;
            max-height: 600px;
            object-fit: cover;
            display: block;
        }
        .fullnews-similar-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .fullnews-similar-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }
        .fullnews-similar-img-wrapper {
            width: 100%;
            height: 180px;
            overflow: hidden;
            position: relative;
        }
        .fullnews-similar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .fullnews-similar-img:hover { transform: scale(1.05); }
        .fullnews-similar-title {
            margin-bottom: 0.75rem;
            line-height: 1.4;
            font-size: 1.1rem;
        }
        .fullnews-similar-btn { border-color: #007bff; color: #007bff; }
        .fullnews-similar-btn:hover { background-color: #007bff; color: white; border-color: #007bff; }
        .card-body { flex-grow: 1; position: relative; z-index: 1; }
        .navbar { background-color: #343a40; }
        .footer { background-color: #343a40; }
        .modal-dialog {
            max-width: 100%;
            width: 80%;
            margin: 0 auto;
        }
        .modal-content {
            border-radius: 0;
            background: transparent;
            border: none;
        }
        .modal-body {
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .modal-body img#modalImage {
            width: 100%;
            height: auto;
            max-height: 90vh;
            object-fit: contain;
        }
        .modal-header, .modal-footer {
            background: rgba(0, 0, 0, 0.7);
            border: none;
            color: white;
        }
        .modal-footer {
            justify-content: space-between;
        }
        .btn-close {
            filter: invert(1);
        }
        @media (max-width: 576px) {
            .modal-body img#modalImage {
                max-height: 80vh;
            }
            .modal-header, .modal-footer {
                padding: 10px;
            }
        }
        .review-section { margin-top: 2rem; }
        .review-card { border: 1px solid #e9ecef; border-radius: 10px; padding: 15px; margin-bottom: 15px; }
        .review-rating i { color: #dc3545; }
        .review-form { background: #f8f9fa; padding: 20px; border-radius: 10px; }
        .rating-stars { font-size: 1.5rem; cursor: pointer; }
        .rating-stars i:hover, .rating-stars i.active { color: #dc3545; }
        .review-avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px; }
        .review-author { color: <?php echo $settings['author_name_color'] ?? '#007bff'; ?>; }
        .average-rating { font-size: 1.2rem; margin-bottom: 1rem; }
    </style>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <!-- Шапка -->
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header.php'; ?>

    <!-- Основной контент -->
    <main class="container py-5">
        <article class="col-md-10 mx-auto">
            <h1 class="display-4 fw-bold mb-4"><?php echo htmlspecialchars($news_item['title']); ?></h1>

            <div class="d-flex justify-content-between text-muted mb-4">
                <span><i class="bi bi-folder-fill text-primary"></i> <a href="/news?category_id=<?php echo $news_item['category_id']; ?>" class="text-primary"><?php echo htmlspecialchars($news_item['category_title'] ?? 'Без категории'); ?></a></span>
                <span><i class="bi bi-calendar3 text-success"></i> <?php echo date('d.m.Y H:i', strtotime($news_item['created_at'])); ?></span>
            </div>

            <?php 
            $images = json_decode($news_item['image'] ?? '[]', true);
            if (!empty($images) && is_array($images)): 
                $main_image = $images[0];
                $gallery_images = array_slice($images, 1);
            ?>
                <div class="fullnews-main-image-container mb-4">
                    <img src="/public/uploads/news/<?php echo htmlspecialchars($main_image); ?>" class="fullnews-main-image img-fluid rounded" alt="<?php echo htmlspecialchars($news_item['title']); ?>">
                </div>

                <?php if (!empty($gallery_images)): ?>
                    <div class="gallery mb-4 d-flex flex-wrap gap-2">
                        <?php foreach ($gallery_images as $img): ?>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-image="/public/uploads/news/<?php echo htmlspecialchars($img); ?>">
                                <img src="/public/uploads/news/<?php echo htmlspecialchars($img); ?>" class="img-thumbnail" alt="Галерея" style="width: 100px; height: 100px; object-fit: cover;">
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="fullnews-content mb-5">
                <?php 
                if (strip_tags($news_item['full_desc']) !== $news_item['full_desc']) {
                    echo $news_item['full_desc'];
                } else {
                    echo nl2br(htmlspecialchars($news_item['full_desc']));
                }
                ?>
            </div>

            <nav class="mb-4">
                <h5>Рубрики:</h5>
                <ul class="nav fullnews-nav-pills">
                    <?php foreach ($categories as $cat): ?>
                        <li class="nav-item">
                            <a class="btn <?php echo $button_size_class; ?> <?php echo $cat['id'] == $news_item['category_id'] ? 'btn-primary' : 'btn-outline-primary'; ?>" href="/news?category_id=<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['title']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <?php if (!empty($news_item['keywords'])): ?>
                <p class="text-muted"><i class="bi bi-tags-fill text-info"></i> 
                    <?php
                    $keywords = explode(',', $news_item['keywords']);
                    $keyword_links = array_map(function($kw) {
                        $kw = trim($kw);
                        return "<a href='/news?keywords=" . urlencode($kw) . "' class='text-primary'>" . htmlspecialchars($kw) . "</a>";
                    }, $keywords);
                    echo implode(', ', $keyword_links);
                    ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($similar_news)): ?>
                <section class="mt-5">
                    <h3 class="fw-bold mb-4">Похожие новости</h3>
                    <div class="row">
                        <?php foreach ($similar_news as $similar): ?>
                            <div class="col-md-4 mb-4">
                                <div class="fullnews-similar-card h-100 shadow-sm border-0">
                                    <?php if (!empty($similar['image'])): 
                                        $similar_images = json_decode($similar['image'], true);
                                        $thumbnail = $similar_images[0] ?? '';
                                    ?>
                                        <div class="fullnews-similar-img-wrapper">
                                            <img src="/public/uploads/news/<?php echo htmlspecialchars($thumbnail); ?>" class="fullnews-similar-img" alt="<?php echo htmlspecialchars($similar['title']); ?>">
                                        </div>
                                    <?php else: ?>
                                        <div class="fullnews-similar-img-wrapper bg-light d-flex align-items-center justify-content-center">
                                            <span class="text-muted">Нет изображения</span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body p-3">
                                        <h5 class="fullnews-similar-title text-truncate"><?php echo htmlspecialchars($similar['title']); ?></h5>
                                        <a href="/news/<?php echo htmlspecialchars($similar['custom_url']); ?>" class="btn <?php echo $button_size_class; ?> mt-2">Читать</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Секция отзывов -->
            <?php if ($news_item['reviews_enabled']): ?>
                <section class="review-section mt-5">
                    <h3 class="fw-bold mb-4">Отзывы</h3>
                    <?php if ($total_reviews > 0): ?>
                        <div class="average-rating">
                            Средний рейтинг: <strong><?php echo $average_rating; ?> / 10</strong> (<?php echo $total_reviews; ?> отзывов)
                        </div>
                    <?php endif; ?>

                    <?php if (!($settings['allow_guest_view'] ?? 1) && !isset($_SESSION['user_id'])): ?>
                        <p class="text-muted">Просматривать и писать отзывы могут только зарегистрированные пользователи. <a href="/login.php">Войдите</a> или <a href="/register.php">зарегистрируйтесь</a>.</p>
                    <?php else: ?>
                        <!-- Форма для отправки отзыва -->
                        <?php if (($settings['allow_guest_reviews'] ?? 0) || isset($_SESSION['user_id'])): ?>
                            <?php if ($can_post_review || !isset($_SESSION['user_id'])): ?>
                                <div class="review-form mb-4">
                                    <form action="/includes/add_review.php" method="POST">
                                        <input type="hidden" name="news_id" value="<?php echo $news_item['id']; ?>">
                                        <input type="hidden" name="custom_url" value="<?php echo htmlspecialchars($custom_url); ?>">
                                        <?php if (!isset($_SESSION['user_id']) && ($settings['guest_name_policy'] ?? 'optional_name') !== 'always_anonymous'): ?>
                                            <div class="mb-3">
                                                <label for="guest_name" class="form-label">Ваше имя</label>
                                                <input type="text" name="guest_name" id="guest_name" class="form-control" maxlength="50" <?php echo ($settings['guest_name_policy'] ?? 'optional_name') === 'require_name' ? 'required' : ''; ?>>
                                            </div>
                                        <?php endif; ?>
                                        <div class="mb-3">
                                            <label for="review_text" class="form-label">Ваш отзыв</label>
                                            <textarea name="review_text" id="review_text" class="form-control" rows="4" required></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Оценка</label>
                                            <div class="rating-stars">
                                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                                    <i class="fas fa-heart" data-rating="<?php echo $i; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <input type="hidden" name="rating" id="rating" required>
                                        </div>
                                        <?php if (!empty($settings['recaptcha_site_key'])): ?>
                                            <div class="mb-3">
                                                <div class="g-recaptcha" data-sitekey="<?php echo $settings['recaptcha_site_key']; ?>"></div>
                                            </div>
                                        <?php endif; ?>
                                        <button type="submit" class="btn btn-primary <?php echo $button_size_class; ?>">Отправить отзыв</button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <p class="text-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted">Просматривать и писать отзывы могут только зарегистрированные пользователи. <a href="/login.php">Войдите</a> или <a href="/register.php">зарегистрируйтесь</a>.</p>
                        <?php endif; ?>

                        <!-- Список отзывов -->
                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-card">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $review['photo'] ? '/public/uploads/users/' . htmlspecialchars($review['photo']) : '/public/uploads/users/default-avatar.webp'; ?>" class="review-avatar" alt="Аватар">
                                            <strong class="review-author">
                                                <?php 
                                                if ($review['user_id'] && !empty($review['first_name'])) {
                                                    echo '<a href="/profile.php?id=' . $review['user_id'] . '" class="text-decoration-none">' . htmlspecialchars($review['first_name'] . ' ' . ($review['last_name'] ?? '')) . '</a>';
                                                } elseif ($review['guest_name'] && ($settings['guest_name_policy'] ?? 'optional_name') !== 'always_anonymous') {
                                                    echo htmlspecialchars($review['guest_name']);
                                                } else {
                                                    echo 'Аноним';
                                                }
                                                ?>
                                            </strong>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="text-muted"><?php echo date('d.m.Y H:i', strtotime($review['created_at'])); ?></span>
                                            <?php if (isset($_SESSION['user_id']) && $review['user_id'] == $_SESSION['user_id']): ?>
                                                <?php if ($settings['allow_author_edit'] ?? 1): ?>
                                                    <a href="#" class="ms-2 text-primary" data-bs-toggle="modal" data-bs-target="#editReviewModal" data-review-id="<?php echo $review['id']; ?>" data-review-text="<?php echo htmlspecialchars($review['review_text']); ?>" data-rating="<?php echo $review['rating']; ?>"><i class="bi bi-pencil"></i></a>
                                                <?php endif; ?>
                                                <?php if ($settings['allow_author_delete'] ?? 1): ?>
                                                    <a href="/includes/delete_review.php?review_id=<?php echo $review['id']; ?>&custom_url=<?php echo urlencode($custom_url); ?>" class="ms-2 text-danger" onclick="return confirm('Вы уверены, что хотите удалить отзыв?');"><i class="bi bi-trash"></i></a>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="review-rating my-2">
                                        <?php for ($i = 1; $i <= 10; $i++): ?>
                                            <i class="fas fa-heart <?php echo $i <= $review['rating'] ? 'text-danger' : 'text-muted'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <div><?php echo $review['review_text']; ?></div>
                                </div>
                            <?php endforeach; ?>

                            <!-- Пагинация -->
                            <?php if ($total_pages > 1): ?>
                                <nav aria-label="Пагинация отзывов">
                                    <ul class="pagination justify-content-center">
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                                <a class="page-link" href="?custom_url=<?php echo urlencode($custom_url); ?>&review_page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted"><?php echo ($settings['allow_guest_reviews'] ?? 0) || isset($_SESSION['user_id']) ? 'Оставьте отзыв, будьте первым!' : 'Отзывов пока нет.'; ?></p>
                        <?php endif; ?>
                    <?php endif; ?>
                </section>
            <?php endif; ?>

            <button class="btn btn-outline-secondary fullnews-btn-modern <?php echo $button_size_class; ?> mt-4" onclick="history.back()"><i class="bi bi-arrow-left-circle"></i> Назад</button>
        </article>
    </main>

    <!-- Модальное окно для редактирования отзыва -->
    <div class="modal fade" id="editReviewModal" tabindex="-1" aria-labelledby="editReviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editReviewModalLabel">Редактировать отзыв</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/includes/edit_review.php" method="POST">
                        <input type="hidden" name="review_id" id="edit_review_id">
                        <input type="hidden" name="news_id" value="<?php echo $news_item['id']; ?>">
                        <input type="hidden" name="custom_url" value="<?php echo htmlspecialchars($custom_url); ?>">
                        <div class="mb-3">
                            <label for="edit_review_text" class="form-label">Ваш отзыв</label>
                            <textarea name="review_text" id="edit_review_text" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Оценка</label>
                            <div class="rating-stars">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <i class="fas fa-heart" data-rating="<?php echo $i; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="rating" id="edit_rating" required>
                        </div>
                        <button type="submit" class="btn btn-primary <?php echo $button_size_class; ?>">Сохранить изменения</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальное окно для галереи -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Просмотр изображения</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" alt="Просмотр">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light <?php echo $button_size_class; ?>" id="prevImage"><i class="bi bi-arrow-left"></i></button>
                    <button type="button" class="btn btn-outline-light <?php echo $button_size_class; ?>" id="nextImage"><i class="bi bi-arrow-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Футер -->
    <footer class="footer p-3 text-white text-center">
        <div class="container">
            <a href="/privacy-policy" class="text-white text-decoration-none">Политика конфиденциальности</a> | 
            <a href="/terms-of-use" class="text-white text-decoration-none">Условия использования</a> | 
            <a href="/feedback" class="text-white text-decoration-none">Обратная связь</a>
            <p class="small mt-1">
                © <?php echo date('Y'); ?> Tender CMS. Все права защищены. <br><a href="/page/help" class="text-white text-decoration-none">Справка</a>
            </p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const images = <?php echo json_encode($images ?? []); ?>;
        let currentIndex = 0;

        const galleryLinks = document.querySelectorAll('.gallery a');
        const modalImage = document.getElementById('modalImage');
        const prevButton = document.getElementById('prevImage');
        const nextButton = document.getElementById('nextImage');

        // Проверка на наличие элементов
        if (!modalImage || !prevButton || !nextButton) {
            console.error('Не найдены необходимые элементы для модального окна');
            return;
        }

        // Обработчик клика по изображениям галереи
        galleryLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const imageSrc = this.getAttribute('data-image');
                const imageName = imageSrc.split('/').pop();
                currentIndex = images.indexOf(imageName);
                if (currentIndex === -1) currentIndex = 0;
                showImage(imageSrc);
            });
        });

        // Обработчик кнопки "Предыдущее"
        prevButton.addEventListener('click', () => {
            if (images.length > 0) {
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                showImage('/public/uploads/news/' + images[currentIndex]);
            }
        });

        // Обработчик кнопки "Следующее"
        nextButton.addEventListener('click', () => {
            if (images.length > 0) {
                currentIndex = (currentIndex + 1) % images.length;
                showImage('/public/uploads/news/' + images[currentIndex]);
            }
        });

        // Функция отображения изображения
        function showImage(src) {
            modalImage.src = src;
            modalImage.alt = 'Изображение ' + (currentIndex + 1);
        }

        // Управление видимостью кнопок навигации
        function updateButtonVisibility() {
            prevButton.style.display = images.length > 1 ? 'block' : 'none';
            nextButton.style.display = images.length > 1 ? 'block' : 'none';
        }

        updateButtonVisibility();

        // Обработчик рейтинга
        function setupRating(starsSelector, ratingInputId) {
            const stars = document.querySelectorAll(starsSelector);
            const ratingInput = document.getElementById(ratingInputId);
            stars.forEach(star => {
                star.addEventListener('click', () => {
                    const rating = star.getAttribute('data-rating');
                    ratingInput.value = rating;
                    console.log('Selected Rating:', rating); // Отладка
                    stars.forEach(s => {
                        s.classList.remove('active');
                        if (s.getAttribute('data-rating') <= rating) {
                            s.classList.add('active');
                        }
                    });
                });
            });
        }

        setupRating('.review-form .rating-stars i', 'rating');
        setupRating('#editReviewModal .rating-stars i', 'edit_rating');

        // Отладка: Проверка наличия элементов для TinyMCE
        console.log('review_text:', document.querySelector('#review_text'));
        console.log('edit_review_text:', document.querySelector('#edit_review_text'));

        // TinyMCE
        tinymce.init({
            selector: '#review_text, #edit_review_text',
            plugins: 'lists link image', // Временно убрали 'paste'
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link image',
            menubar: false,
            height: 300,
            content_style: 'body { font-family: Arial, sans-serif; }',
            setup: function (editor) {
                editor.on('change', function () {
                    editor.save();
                });
            }
        });

        // Сохранение данных TinyMCE перед отправкой формы
        document.querySelector('.review-form form').addEventListener('submit', function () {
            console.log('Rating:', document.getElementById('rating').value);
            console.log('Review Text:', tinymce.get('review_text').getContent());
            tinymce.triggerSave();
            console.log('Form Data:', new FormData(this));
        });
    });
    </script>
</body>
</html>