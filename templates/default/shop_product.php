<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Безопасность
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Подключаем header_shop.php вместо стандартного header
require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header_shop.php';

// Получение custom_url из пути /shop/custom_url
$request_uri = trim($_SERVER['REQUEST_URI'], '/');
$parts = explode('/', $request_uri);
$custom_url = (count($parts) >= 2 && $parts[0] === 'shop') ? $parts[1] : '';

if (!$custom_url) {
    header('HTTP/1.0 404 Not Found');
    echo '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Товар не найден</title></head><body>';
    echo '<div class="container my-4"><p>Товар не найден.</p></div>';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php';
    echo '</body></html>';
    exit;
}

// Получение данных о товаре
$product_stmt = $conn->prepare('
    SELECT p.id, p.name, p.short_desc, p.full_desc, p.price, p.in_stock, p.delivery_status, p.image, p.custom_url, 
           p.meta_title, p.meta_desc, p.og_title, p.og_desc, c.name AS category_name, c.id AS category_id
    FROM shop_products p 
    LEFT JOIN shop_categories c ON p.category_id = c.id 
    WHERE p.custom_url = ? AND p.status = "active"
');
$product_stmt->bind_param('s', $custom_url);
$product_stmt->execute();
$product = $product_stmt->get_result()->fetch_assoc();
$product_stmt->close();

if (!$product) {
    header('HTTP/1.0 404 Not Found');
    echo '<!DOCTYPE html><html lang="ru"><head><meta charset="UTF-8"><title>Товар не найден</title></head><body>';
    echo '<div class="container my-4"><p>Товар не найден.</p></div>';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php';
    echo '</body></html>';
    exit;
}


// Обработка добавления в корзину
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart']) && $product['in_stock']) {
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]) ?: 1;
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][$product['id']] = ($_SESSION['cart'][$product['id']] ?? 0) + $quantity;
    $message = '<div class="alert alert-success">Товар добавлен в корзину!</div>';
}

// Обработка изображений
$images = $product['image'] ? json_decode($product['image'], true) : [];
$main_image = !empty($images) ? 'https://' . $_SERVER['HTTP_HOST'] . '/uploads/shop/' . $images[0] : 'https://' . $_SERVER['HTTP_HOST'] . '/assets/placeholder.png';

// Мета-теги из таблицы shop_products
$page_title = htmlspecialchars($product['meta_title'] ?? $product['name']);
$page_description = htmlspecialchars($product['meta_desc'] ?? $product['short_desc'] ?? '');
$page_og_title = htmlspecialchars($product['og_title'] ?? $product['name']);
$page_og_description = htmlspecialchars($product['og_desc'] ?? $product['short_desc'] ?? '');
$page_og_image = $main_image;
$page_og_url = 'https://' . $_SERVER['HTTP_HOST'] . '/shop/' . htmlspecialchars($product['custom_url']);
$page_og_type = 'product';
$page_twitter_card_type = !empty($page_og_image) ? 'summary_large_image' : 'summary';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $page_description; ?>">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="<?php echo $page_og_title; ?>">
    <meta property="og:description" content="<?php echo $page_og_description; ?>">
    <meta property="og:type" content="<?php echo $page_og_type; ?>">
    <meta property="og:url" content="<?php echo $page_og_url; ?>">
    <?php if ($page_og_image): ?>
        <meta property="og:image" content="<?php echo $page_og_image; ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="<?php echo $page_twitter_card_type; ?>">
    <meta name="twitter:title" content="<?php echo $page_og_title; ?>">
    <meta name="twitter:description" content="<?php echo $page_og_description; ?>">
    <?php if ($page_og_image): ?>
        <meta name="twitter:image" content="<?php echo $page_og_image; ?>">
    <?php endif; ?>
    <link rel="canonical" href="<?php echo $page_og_url; ?>">
    <title><?php echo $page_title; ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/templates/default/css/style.css?v=1">
    <style>
        .product-container { padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        .product-image { max-width: 100%; height: auto; border-radius: 10px; transition: transform 0.3s; }
        .product-image:hover { transform: scale(1.05); }
        .thumbnail { max-width: 60px; cursor: pointer; margin: 5px; border: 2px solid transparent; border-radius: 5px; }
        .thumbnail.active { border-color: #007bff; }
        .btn-custom { background: #007bff; color: #fff; border-radius: 25px; padding: 10px 20px; }
        .btn-custom:hover { background: #0056b3; }
        .description { line-height: 1.6; }
        @media (max-width: 768px) {
            .product-container { padding: 10px; }
            .btn-custom { width: 100%; }
            .thumbnail { max-width: 50px; }
        }
    </style>
</head>
<body>
    <div class="container my-4 product-container">
        <h1 class="mb-3"><i class="fas fa-box me-2"></i><?php echo htmlspecialchars($product['name']); ?></h1>
        <?php if (isset($message)) echo $message; ?>

        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo $main_image; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image" id="main-image">
                <div class="d-flex flex-wrap mt-2">
                    <?php foreach ($images as $img): ?>
                        <img src="/uploads/shop/<?php echo $img; ?>" alt="Thumbnail" class="thumbnail" onclick="changeImage(this.src)">
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-md-6">
                <p><strong><i class="fas fa-folder me-2"></i>Категория:</strong> 
                    <a href="/shop_category/<?php echo (int)$product['category_id']; ?>">
                        <?php echo htmlspecialchars($product['category_name'] ?? 'Без категории'); ?>
                    </a>
                </p>
                <p><strong><i class="fas fa-ruble-sign me-2"></i>Цена:</strong> <?php echo number_format($product['price'], 2) . ' ' . $currency_symbol; ?></p>
                <p><strong><i class="fas fa-truck me-2"></i>Доставка:</strong> <?php echo $product['delivery_status'] ? 'Доступна' : 'Недоступна'; ?></p>
                <p><strong><i class="fas fa-check-circle me-2"></i>Наличие:</strong> <?php echo $product['in_stock'] ? 'В наличии' : 'Нет в наличии'; ?></p>
                <p><strong><i class="fas fa-info-circle me-2"></i>Короткое описание:</strong> <?php echo nl2br(htmlspecialchars($product['short_desc'] ?? '')); ?></p>
                <?php if ($product['in_stock']): ?>
                    <form method="POST" class="mt-3">
                        <div class="input-group mb-3" style="max-width: 200px;">
                            <span class="input-group-text"><i class="fas fa-shopping-cart"></i></span>
                            <input type="number" id="quantity" name="quantity" min="1" value="1" class="form-control">
                        </div>
                        <button type="submit" name="add_to_cart" class="btn btn-custom">
                            <i class="fas fa-cart-plus me-2"></i>Добавить в корзину
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-danger"><i class="fas fa-exclamation-circle me-2"></i>Товара нет в наличии</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="mt-4">
            <h3><i class="fas fa-file-alt me-2"></i>Описание</h3>
            <div class="description"><?php echo $product['full_desc'] ?? nl2br(htmlspecialchars($product['description'] ?? 'Описание отсутствует')); ?></div>
        </div>
    </div>

    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function changeImage(src) {
            document.getElementById('main-image').src = src;
            document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
            event.target.classList.add('active');
        }
    </script>
</body>
</html>