<?php
session_start();

// Подключаем зависимости с абсолютными путями
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Установка заголовков безопасности
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Подключаем header_shop.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header_shop.php';

// Получение ID категории из URL
$category_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;

// Проверка существования категории и получение всех мета-тегов
$category_stmt = $conn->prepare('
    SELECT name, meta_title, meta_desc, og_title, og_desc, twitter_title, twitter_desc, keywords 
    FROM shop_categories 
    WHERE id = ? AND status = 1
');
$category_stmt->bind_param('i', $category_id);
$category_stmt->execute();
$category = $category_stmt->get_result()->fetch_assoc();
$category_stmt->close();

if (!$category) {
    echo '<div class="container my-5"><div class="alert alert-warning text-center"><i class="fas fa-exclamation-triangle me-2"></i>Категория не найдена.</div></div>';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php';
    exit;
}

// Установка значений по умолчанию для мета-тегов
$meta_title = !empty($category['meta_title']) ? htmlspecialchars($category['meta_title'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') . ' - Магазин';
$meta_desc = !empty($category['meta_desc']) ? htmlspecialchars($category['meta_desc'], ENT_QUOTES, 'UTF-8') : 'Товары в категории ' . htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8');
$og_title = !empty($category['og_title']) ? htmlspecialchars($category['og_title'], ENT_QUOTES, 'UTF-8') : $meta_title;
$og_desc = !empty($category['og_desc']) ? htmlspecialchars($category['og_desc'], ENT_QUOTES, 'UTF-8') : $meta_desc;
$twitter_title = !empty($category['twitter_title']) ? htmlspecialchars($category['twitter_title'], ENT_QUOTES, 'UTF-8') : $meta_title;
$twitter_desc = !empty($category['twitter_desc']) ? htmlspecialchars($category['twitter_desc'], ENT_QUOTES, 'UTF-8') : $meta_desc;
$keywords = !empty($category['keywords']) ? htmlspecialchars($category['keywords'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8');

// Получение товаров в категории с дополнительными полями
$products_stmt = $conn->prepare('
    SELECT id, name, short_desc, price, image, custom_url 
    FROM shop_products 
    WHERE category_id = ? AND status = "active"
');
$products_stmt->bind_param('i', $category_id);
$products_stmt->execute();
$products = $products_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$products_stmt->close();

// Настройки магазина
$shop_settings = require_once $_SERVER['DOCUMENT_ROOT'] . '/uploads/shop_settings.php';
$currency_symbol = [
    'EUR' => '€',
    'ГРН' => '₴',
    'РУБ' => '₽',
    'USD' => '$'
][$shop_settings['shop_currency']] ?? '€';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $meta_title; ?></title>
    <meta name="description" content="<?php echo $meta_desc; ?>">
    <meta name="keywords" content="<?php echo $keywords; ?>">
    <meta property="og:title" content="<?php echo $og_title; ?>">
    <meta property="og:description" content="<?php echo $og_desc; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $twitter_title; ?>">
    <meta name="twitter:description" content="<?php echo $twitter_desc; ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #f5f5f5;
            font-family: 'Arial', sans-serif;
            color: #333;
        }
        .search-bar {
            background: #fff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .search-bar input {
            border-radius: 25px;
            padding: 10px 20px;
            border: 1px solid #007bff;
            transition: border-color 0.3s ease;
        }
        .search-bar input:focus {
            border-color: #0056b3;
            box-shadow: 0 0 5px rgba(0, 86, 179, 0.5);
        }
        .search-bar button {
            border-radius: 25px;
            background: linear-gradient(90deg, #007bff, #00c4cc);
            border: none;
            padding: 10px 20px;
            color: white;
            transition: all 0.3s ease;
        }
        .search-bar button:hover {
            background: linear-gradient(90deg, #0056b3, #007bff);
        }
        .category-header {
            background: linear-gradient(135deg, #007bff, #00c4cc);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        .category-header h1 {
            margin: 0;
            font-size: 2.5rem;
            text-transform: uppercase;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }
        .category-header a {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .category-header a:hover {
            color: #ffeb3b;
        }
        .category-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(30deg);
            pointer-events: none;
        }
        .product-card {
            background: white;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .product-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
            transition: transform 0.3s ease;
        }
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        .product-card .card-body {
            padding: 1.5rem;
            text-align: center;
        }
        .product-title a {
            font-size: 1.25rem;
            font-weight: 600;
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .product-title a:hover {
            color: #0056b3;
        }
        .product-desc {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
            height: 3rem;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .btn-quick-order {
            background: #28a745;
            border: none;
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
            transition: background 0.3s ease;
        }
        .btn-quick-order:hover {
            background: #218838;
        }
        .btn-cart {
            background: linear-gradient(90deg, #28a745, #34c759);
            border: none;
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
            transition: background 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        }
        .btn-cart:hover {
            background: linear-gradient(90deg, #218838, #2ecc71);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.6);
        }
        .btn-cart.disabled {
            background: #6c757d;
            box-shadow: none;
        }
        .price {
            font-size: 1.1rem;
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        @media (max-width: 768px) {
            .category-header h1 {
                font-size: 1.8rem;
            }
            .product-card {
                margin-bottom: 1.5rem;
            }
        }
    </style>
</head>
<body>

<div class="container my-5">
    <!-- Поиск по категории -->
    <div class="search-bar">
        <form action="" method="GET" class="d-flex justify-content-center gap-2">
            <input type="text" name="search" class="form-control w-50" placeholder="Поиск товаров в категории..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="btn"><i class="fas fa-search me-2"></i>Найти</button>
        </form>
    </div>

    <!-- Заголовок категории с иконками -->
    <div class="category-header text-center">
        <h1>
            <i class="fas fa-shopping-bag me-2"></i>
            <a href="/shop_category?id=<?php echo $category_id; ?>"><?php echo htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8'); ?></a>
            <i class="fas fa-tags ms-2"></i>
        </h1>
    </div>

    <!-- Секция товаров -->
    <section class="category-products">
        <div class="row">
            <?php 
            // Фильтрация товаров по поисковому запросу, если он есть
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = strtolower(trim($_GET['search']));
                $products = array_filter($products, function($product) use ($search) {
                    return strpos(strtolower($product['name']), $search) !== false || strpos(strtolower($product['short_desc'] ?? ''), $search) !== false;
                });
            }
            ?>
            <?php if (empty($products)): ?>
                <div class="col-12 text-center">
                    <p class="text-muted"><i class="fas fa-exclamation-circle me-2"></i>В этой категории пока нет товаров или ничего не найдено.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="product-card">
                            <!-- Главное изображение -->
                            <?php
                            $image_path = '/uploads/assets/no_photo.webp';
                            if (!empty($product['image'])) {
                                $images = json_decode($product['image'], true);
                                if (is_array($images) && !empty($images)) {
                                    $image_path = '/uploads/shop/' . $images[0];
                                    if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $image_path)) {
                                        $image_path = '/uploads/assets/no_photo.webp';
                                    }
                                }
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($image_path, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" class="product-image">
                            
                            <div class="card-body">
                                <!-- Название товара с ссылкой -->
                                <h5 class="product-title">
                                    <a href="/shop/<?php echo htmlspecialchars($product['custom_url'] ?? generate_url($product['name']), ENT_QUOTES, 'UTF-8'); ?>">
                                        <i class="fas fa-box-open me-2"></i><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </h5>
                                
                                <!-- Короткое описание (до 100 символов) -->
                                <p class="product-desc">
                                    <?php 
                                    $short_desc = $product['short_desc'] ?? 'Описание отсутствует, но товар отличный, покупайте скорее!';
                                    echo htmlspecialchars(mb_substr($short_desc, 0, 100, 'UTF-8') . (mb_strlen($short_desc, 'UTF-8') > 100 ? '...' : ''), ENT_QUOTES, 'UTF-8'); 
                                    ?>
                                </p>
                                
                                <!-- Цена с иконкой -->
                                <div class="price"><i class="fas fa-money-bill-wave me-2"></i><?php echo number_format($product['price'], 2) . ' ' . $currency_symbol; ?></div>
                                
                                <!-- Кнопки -->
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="/checkout/<?php echo (int)$product['id']; ?>" class="btn btn-quick-order text-white">
    <i class="fas fa-bolt me-1"></i> Быстрый заказ
</a>
 <button class="btn btn-cart text-white add-to-cart" data-product-id="<?php echo (int)$product['id']; ?>" data-quantity="1">
                                        <i class="fas fa-cart-plus me-1"></i> В корзину
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const addToCartButtons = document.querySelectorAll('.add-to-cart');

    addToCartButtons.forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.getAttribute('data-product-id');
            const quantity = this.getAttribute('data-quantity');

            fetch('/templates/default/cart_add.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${encodeURIComponent(productId)}&quantity=${encodeURIComponent(quantity)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Товар добавлен в корзину!');
                    button.disabled = true;
                    button.textContent = 'Добавлено';
                    setTimeout(() => {
                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-cart-plus me-1"></i> В корзину';
                    }, 2000);
                } else {
                    alert('Ошибка: ' + (data.error || 'Не удалось добавить товар в корзину'));
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при добавлении в корзину.');
            });
        });
    });
});

function generate_url(name) {
    return name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
}
</script>
</body>
</html>