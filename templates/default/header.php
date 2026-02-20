<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$categories = $conn->query("SELECT * FROM news_categories ORDER BY title")->fetch_all(MYSQLI_ASSOC);
$category_tree = buildCategoryTree($categories);

$settings = get_settings();
if (empty($settings)) {
    die("Ошибка загрузки настроек из файла site_settings.php");
}

$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;

// Дефолтные SEO настройки
$default_title = htmlspecialchars($settings['site_title'] ?? 'Tender CMS');
$default_description = htmlspecialchars($settings['site_description'] ?? '');
$default_keywords = htmlspecialchars($settings['site_keywords'] ?? '');
$default_og_title = htmlspecialchars($settings['og_title'] ?? $default_title);
$default_og_description = htmlspecialchars($settings['og_description'] ?? $default_description);
$default_og_image = !empty($settings['og_image']) ? htmlspecialchars($settings['og_image']) : '';
$default_og_type = htmlspecialchars($settings['og_type'] ?? 'website');
$default_og_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$default_twitter_card_type = htmlspecialchars($settings['twitter_card_type'] ?? 'summary_large_image');
$default_robots = htmlspecialchars($settings['meta_robots'] ?? 'index, follow');

// Инициализация переменных страницы
$page_title = $default_title;
$page_description = $default_description;
$page_keywords = $default_keywords;
$page_og_title = $default_og_title;
$page_og_description = $default_og_description;
$page_og_image = $default_og_image;
$page_og_type = $default_og_type;
$page_og_url = $default_og_url;
$page_twitter_card_type = $default_twitter_card_type;
$page_robots = $default_robots;
$page_twitter_title = $default_og_title;
$page_twitter_description = $default_og_description;

$current_page = basename($_SERVER['PHP_SELF']);

// Проверка, является ли страница страницей товара
$custom_url = basename($_SERVER['REQUEST_URI']);
$is_product_page = false;
if ($current_page === 'product.php') {
    $product_stmt = $conn->prepare('
        SELECT p.name, p.short_desc, p.meta_title, p.meta_desc, p.og_title, p.og_desc, p.image
        FROM shop_products p 
        WHERE p.custom_url = ? AND p.status = "active"
    ');
    $product_stmt->bind_param('s', $custom_url);
    $product_stmt->execute();
    $product = $product_stmt->get_result()->fetch_assoc();
    $product_stmt->close();

    if ($product) {
        $is_product_page = true;
        $page_title = htmlspecialchars($product['meta_title'] ?? $product['name']) . ' - ' . $default_title;
        $page_description = htmlspecialchars($product['meta_desc'] ?? $product['short_desc'] ?? $default_description);
        $page_keywords = htmlspecialchars($product['keywords'] ?? $default_keywords);
        $page_og_title = htmlspecialchars($product['og_title'] ?? $product['name'] ?? $default_og_title);
        $page_og_description = htmlspecialchars($product['og_desc'] ?? $product['short_desc'] ?? $default_og_description);
        $images = $product['image'] ? json_decode($product['image'], true) : [];
        $page_og_image = !empty($images) ? 'https://' . $_SERVER['HTTP_HOST'] . '/Uploads/shop/' . htmlspecialchars($images[0]) : $default_og_image;
        $page_og_type = 'product';
        $page_og_url = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $custom_url;
        $page_twitter_card_type = $page_og_image ? 'summary_large_image' : 'summary';
        $page_twitter_title = $page_og_title;
        $page_twitter_description = $page_og_description;
    }
}

// Логика для других страниц
if (!$is_product_page) {
    if ($current_page === 'index.php') {
        // Главная — значения по умолчанию
    } elseif ($current_page === 'login.php') {
        $page_title = "Вход | $default_title";
        $page_og_title = "Вход | $default_og_title";
    } elseif ($current_page === 'add_tender.php') {
        $page_title = "Добавить тендер | $default_title";
        $page_og_title = "Добавить тендер | $default_og_title";
    } elseif ($current_page === 'shop.php') {
        $page_title = "Магазин | $default_title";
        $page_og_title = "Магазин | $default_og_title";
    } elseif ($current_page === 'news_full.php' && isset($_GET['id'])) {
        $news_id = (int)$_GET['id'];
        $stmt = $conn->prepare("SELECT title, short_desc, meta_title, meta_desc, keywords, og_title, og_desc, image FROM news WHERE id = ?");
        $stmt->bind_param("i", $news_id);
        $stmt->execute();
        $news = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($news) {
            $page_title = htmlspecialchars($news['meta_title'] ?? $news['title'] ?? $default_title);
            $page_description = htmlspecialchars($news['meta_desc'] ?? $news['short_desc'] ?? $default_description);
            $page_keywords = htmlspecialchars($news['keywords'] ?? $default_keywords);
            $page_og_title = htmlspecialchars($news['og_title'] ?? $news['title'] ?? $default_og_title);
            $page_og_description = htmlspecialchars($news['og_desc'] ?? $news['short_desc'] ?? $default_og_description);
            $page_og_image = !empty($news['image']) ? 'https://' . $_SERVER['HTTP_HOST'] . '/public/uploads/news/' . htmlspecialchars(json_decode($news['image'], true)[0] ?? '') : $default_og_image;
            $page_og_type = 'article';
            $page_og_url = 'https://' . $_SERVER['HTTP_HOST'] . '/news_full/' . $news_id;
            $page_twitter_card_type = $page_og_image ? 'summary_large_image' : 'summary';
            $page_twitter_title = $page_og_title;
            $page_twitter_description = $page_og_description;
        }
    }
}

$login_button_size_class = match ($settings['login_button_size'] ?? 'medium') {
    'small' => 'btn-sm',
    'large' => 'btn-lg',
    default => ''
};

$is_feedback_page = ($current_page === 'feedback.php');
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $page_description; ?>">
    <meta name="keywords" content="<?php echo $page_keywords; ?>">
    <meta name="robots" content="<?php echo $page_robots; ?>">
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
    <!-- Подключение стилей -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/templates/default/css/style.css?v=1">
    <link rel="stylesheet" href="/templates/default/css/style.php?v=1">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <header>
        <div class="header-stripe"></div>
        <nav class="navbar navbar-expand-md navbar-light">
            <div class="container">
                <a class="navbar-brand" href="/">
                    <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $settings['logo_value'] ?? '')): ?>
                        <img src="<?php echo htmlspecialchars($settings['logo_value']); ?>" style="width: 75px;" alt="Logo" class="img-fluid">
                    <?php else: ?>
                        <?php echo htmlspecialchars($settings['logo_value'] ?? 'Pro Website Management'); ?>
                    <?php endif; ?>
                </a>
				<?php if (!$is_feedback_page): ?>
                        <ul class="navbar-nav main-nav">
                                            <li class="nav-item admin-link">
                    <a class="nav-link" href="/admin/login.php"><i class="fas fa-user-lock me-2"></i>Демо: Admin Panel</a>
                </li>
                            
                        </ul>
                    <?php endif; ?>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav auth-nav align-items-center">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item me-3">
                                <a class="nav-link <?php echo $current_page === 'profile.php' ? 'active' : ''; ?>" href="/profile">Профиль</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn header-btn <?php echo $login_button_size_class; ?> <?php echo $current_page === 'logout.php' ? 'active' : ''; ?>" href="/logout">Выйти</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item me-3">
                                <a class="btn header-btn <?php echo $login_button_size_class; ?> <?php echo $current_page === 'login.php' ? 'active' : ''; ?>" href="/login">Войти</a>
                            </li>
                            <li class="nav-item">
                                <a class="btn header-btn <?php echo $login_button_size_class; ?> <?php echo $current_page === 'register.php' ? 'active' : ''; ?>" href="/register">Регистрация</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <style>
        /* Стили для навигации */
        .navbar {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }
        .navbar-brand img {
            transition: transform 0.3s ease;
        }
        .navbar-brand img:hover {
            transform: scale(1.1);
        }
        .navbar-nav.auth-nav .nav-link,
        .navbar-nav.auth-nav .header-btn {
            position: relative;
            color: #333;
            font-weight: 500;
            padding: 0.5rem 1rem;
            text-decoration: none;
            transition: color 0.3s ease, transform 0.3s ease;
        }
        .navbar-nav.auth-nav .nav-link:hover,
        .navbar-nav.auth-nav .header-btn:hover {
            color: #007bff;
            transform: scale(1.05);
        }
        .navbar-nav.auth-nav .nav-link::after,
        .navbar-nav.auth-nav .header-btn::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #007bff;
            transition: width 0.3s ease;
        }
        .navbar-nav.auth-nav .nav-link:hover::after,
        .navbar-nav.auth-nav .header-btn:hover::after {
            width: 100%;
        }
        .navbar-nav.auth-nav .nav-link.active,
        .navbar-nav.auth-nav .header-btn.active {
            color: #007bff;
            font-weight: 700;
        }
        .navbar-nav.auth-nav .nav-link.active::after,
        .navbar-nav.auth-nav .header-btn.active::after {
            width: 100%;
        }
        .navbar-toggler {
            border: none;
            padding: 0.5rem;
        }
        .navbar-toggler:focus {
            box-shadow: none;
        }
        .header-stripe {
            height: 4px;
            background: linear-gradient(to right, #007bff, #00d4ff);
        }

        /* Адаптивность */
        @media (max-width: 767px) {
            .navbar-nav.auth-nav {
                margin-top: 0.5rem;
                background: #fff;
                border-radius: 8px;
                padding: 0.5rem;
            }
            .navbar-nav.auth-nav .nav-item {
                margin: 0.25rem 0;
            }
            .navbar-nav.auth-nav .nav-link,
            .navbar-nav.auth-nav .header-btn {
                font-size: 0.9rem;
                padding: 0.5rem;
            }
            .navbar-nav.auth-nav .nav-link::after,
            .navbar-nav.auth-nav .header-btn::after {
                height: 1px;
            }
        }
        @media (min-width: 768px) {
            .navbar-nav.auth-nav .nav-link,
            .navbar-nav.auth-nav .header-btn {
                margin-left: 0.5rem;
                padding: 0.5rem 1.25rem;
            }
        }
    </style>

    <!-- Скрипты в конце body -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="/templates/default/js/js.js" defer></script>
</body>
</html>