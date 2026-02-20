<?php
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

$seo_settings_file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/shop_seo_settings.php';
$seo_settings = file_exists($seo_settings_file) ? include $seo_settings_file : [];

$price_range = $conn->query('SELECT MIN(price) as min_price, MAX(price) as max_price FROM shop_products WHERE status = "active"')->fetch_assoc();
$min_price_range = floor($price_range['min_price'] ?? 0);
$max_price_range = ceil($price_range['max_price'] ?? 1000);

// Обработка фильтров
$where_clauses = ['p.status = "active"'];
$params = [];
$types = '';

$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : $min_price_range;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : $max_price_range;

$where_clauses[] = 'p.price >= ?';
$params[] = $min_price;
$types .= 'd';
$where_clauses[] = 'p.price <= ?';
$params[] = $max_price;
$types .= 'd';

$query = "
    SELECT p.id, p.name, p.price, p.image, p.short_desc, p.custom_url, c.name AS category_name
    FROM shop_products p
    LEFT JOIN shop_categories c ON p.category_id = c.id
    WHERE " . implode(' AND ', $where_clauses) . "
    LIMIT 6";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$popular_products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$shop_settings = require_once $_SERVER['DOCUMENT_ROOT'] . '/uploads/shop_settings.php';
$currency_symbol = ['EUR' => '€', 'ГРН' => '₴', 'РУБ' => '₽', 'USD' => '$'][$shop_settings['shop_currency']] ?? '€';

$default_title = 'Магазин - Tender CMS';
$default_desc = 'Добро пожаловать в наш магазин! Ознакомьтесь с популярными товарами и категориями.';
$default_keywords = 'магазин, товары, категории, покупки';

$page_title = htmlspecialchars($seo_settings['shop_meta_title'] ?? $default_title);
$page_description = htmlspecialchars($seo_settings['shop_meta_desc'] ?? $default_desc);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $page_description; ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($seo_settings['shop_keywords'] ?? $default_keywords); ?>">
    <meta name="robots" content="index, follow">
    <meta property="og:title" content="<?php echo htmlspecialchars($seo_settings['shop_og_title'] ?? $default_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($seo_settings['shop_og_desc'] ?? $default_desc); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://<?php echo $_SERVER['HTTP_HOST']; ?>/shop">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($seo_settings['shop_og_title'] ?? $default_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($seo_settings['shop_og_desc'] ?? $default_desc); ?>">
    <link rel="canonical" href="https://<?php echo $_SERVER['HTTP_HOST']; ?>/shop">
    <title><?php echo $page_title; ?></title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .container { max-width: 1200px; margin: 0 auto; padding: 0 15px; }
        .filter-section { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); margin-bottom: 20px; }
        .filter-section h3 { font-size: 1.25rem; font-weight: 600; color: #2c3e50; margin-bottom: 15px; }
        .price-slider { position: relative; height: 8px; background: #ddd; border-radius: 5px; margin: 20px 0; }
        .price-slider .range-bar { position: absolute; height: 100%; background: #28a745; }
        .price-slider .thumb { position: absolute; width: 20px; height: 20px; background: #28a745; border-radius: 50%; top: -6px; cursor: pointer; }
        .price-values { display: flex; justify-content: space-between; font-size: 0.9rem; color: #495057; margin-top: 10px; }
        .btn-filter { background: #28a745; border: none; color: #fff; padding: 10px; border-radius: 25px; width: 100%; transition: all 0.3s ease; }
        .btn-filter:hover { background: #218838; transform: scale(1.02); }
        .product-card { 
            background: linear-gradient(135deg, #ffffff, #f9f9f9); 
            border: none; 
            border-radius: 20px; 
            overflow: hidden; 
            transition: transform 0.3s ease, box-shadow 0.3s ease; 
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08); 
            display: flex; 
            flex-direction: column; 
            height: 100%; 
        }
        .product-card:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15); 
        }
        .product-body { 
            padding: 20px; 
            flex-grow: 1; 
            display: flex; 
            flex-direction: column; 
            gap: 12px; 
        }
        .product-title a { 
            font-size: 1.4rem; 
            font-weight: 700; 
            color: #2c3e50; 
            margin: 0; 
            text-decoration: none; 
            transition: color 0.3s ease; 
            text-align: center; 
        }
        .product-title a:hover { 
            color: #007bff; 
        }
        .product-image { 
            width: 100%; 
            height: 220px; 
            overflow: hidden; 
            background: #f0f2f5; 
            border-radius: 15px; 
        }
        .product-image img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            transition: transform 0.4s ease; 
        }
        .product-card:hover .product-image img { 
            transform: scale(1.08); 
        }
        .product-category { 
            font-size: 0.95rem; 
            color: #6c757d; 
            margin: 0; 
            text-align: center; 
        }
        .product-short-desc { 
            font-size: 0.95rem; 
            color: #495057; 
            margin: 0; 
            text-align: center; 
        }
        .product-price { 
            font-size: 1.3rem; 
            font-weight: 700; 
            color: #28a745; 
            margin: 0; 
            text-align: center; 
        }
        .product-actions { 
            margin-top: auto; 
            gap: 12px; 
            justify-content: center; 
            display: flex; 
        }
        .product-actions .btn-outline-info { 
            border-color: #17a2b8; 
            color: #17a2b8; 
            padding: 0; 
            font-size: 1.1rem; 
            border-radius: 50%; 
            width: 45px; 
            height: 45px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            transition: all 0.3s ease; 
        }
        .product-actions .btn-outline-info:hover { 
            background: #17a2b8; 
            color: #fff; 
        }
        .product-actions .add-to-cart { 
            background: #28a745; 
            border: none; 
            color: #fff; 
            padding: 10px 20px; 
            border-radius: 25px; 
            transition: all 0.3s ease; 
        }
    </style>
</head>
<body>
    <?php 
    $carousel_path = $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header_shop.php';
    if (file_exists($carousel_path)) {
        require_once $carousel_path;
    } else {
        echo "<!-- Не удалось найти header_shop.php -->";
    }
    ?>
    <div class="container my-5">
        <h1 class="text-center mb-3 fw-bold text-primary"><?php echo $page_title; ?></h1>
        <p class="text-center mb-5 text-muted"><?php echo $page_description; ?></p>
        <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/shop_cat.php'; ?>
        <!-- Фильтр по цене -->
        <div class="filter-section">
            <h3>Фильтр по цене</h3>
            <form method="GET" action="/shop" id="filter-form">
                <div class="price-slider" id="priceSlider">
                    <div class="range-bar" id="rangeBar"></div>
                    <div class="thumb" id="minThumb"></div>
                    <div class="thumb" id="maxThumb"></div>
                </div>
                <div class="price-values">
                    <span id="minPriceValue"><?php echo number_format($min_price, 2); ?> <?php echo $currency_symbol; ?></span>
                    <span id="maxPriceValue"><?php echo number_format($max_price, 2); ?> <?php echo $currency_symbol; ?></span>
                </div>
                <input type="hidden" name="min_price" id="minPrice" value="<?php echo $min_price; ?>">
                <input type="hidden" name="max_price" id="maxPrice" value="<?php echo $max_price; ?>">
                <button type="submit" class="btn btn-filter mt-3">Применить фильтр</button>
            </form>
        </div>

        <!-- Товары -->
        <section class="popular-products">
            <h2 class="mb-4 fw-semibold">Популярные товары</h2>
            <div class="row g-4">
                <?php foreach ($popular_products as $product): ?>
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
                    $short_desc = mb_strlen($product['short_desc']) > 50 ? mb_substr($product['short_desc'], 0, 47) . '...' : $product['short_desc'];
                    $product_url = $product['custom_url'] ? "/shop/{$product['custom_url']}" : "/shop/" . generate_url($product['name']);
                    ?>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="product-card">
                            <div class="product-body">
                                <h5 class="product-title">
                                    <a href="<?php echo htmlspecialchars($product_url); ?>">
                                        <?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </h5>
                                <div class="product-image">
                                    <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid">
                                </div>
                                <p class="product-category text-muted"><?php echo htmlspecialchars($product['category_name'] ?? 'Без категории', ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="product-short-desc"><?php echo htmlspecialchars($short_desc, ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="product-price"><?php echo number_format($product['price'], 2); ?> <?php echo $currency_symbol; ?></p>
                                <div class="product-actions">
                                    <a href="<?php echo htmlspecialchars($product_url); ?>" class="btn btn-outline-info btn-sm" title="Информация о товаре"><i class="fas fa-info-circle"></i></a>
                                    <button class="btn add-to-cart" data-product-id="<?php echo (int)$product['id']; ?>" data-quantity="1">В корзину</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer_shop_contact.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const slider = document.getElementById('priceSlider');
            const rangeBar = document.getElementById('rangeBar');
            const minThumb = document.getElementById('minThumb');
            const maxThumb = document.getElementById('maxThumb');
            const minPriceInput = document.getElementById('minPrice');
            const maxPriceInput = document.getElementById('maxPrice');
            const minPriceValue = document.getElementById('minPriceValue');
            const maxPriceValue = document.getElementById('maxPriceValue');
            const filterForm = document.getElementById('filter-form');
            const currencySymbol = '<?php echo $currency_symbol; ?>';

            const minRange = <?php echo $min_price_range; ?>;
            const maxRange = <?php echo $max_price_range; ?>;
            let minVal = parseFloat(minPriceInput.value);
            let maxVal = parseFloat(maxPriceInput.value);

            function updateSlider() {
                const sliderWidth = slider.offsetWidth;
                const rangeWidth = maxRange - minRange;

                const minPos = ((minVal - minRange) / rangeWidth) * (sliderWidth - 20);
                const maxPos = ((maxVal - minRange) / rangeWidth) * (sliderWidth - 20);

                minThumb.style.left = minPos + 'px';
                maxThumb.style.left = maxPos + 'px';
                rangeBar.style.left = minPos + 'px';
                rangeBar.style.width = (maxPos - minPos) + 'px';

                minPriceValue.textContent = minVal.toFixed(2) + ' ' + currencySymbol;
                maxPriceValue.textContent = maxVal.toFixed(2) + ' ' + currencySymbol;
                minPriceInput.value = minVal;
                maxPriceInput.value = maxVal;
            }

            function setValueFromPosition(x, thumb) {
                const sliderWidth = slider.offsetWidth - 20;
                const rangeWidth = maxRange - minRange;
                let newVal = minRange + (x / sliderWidth) * rangeWidth;

                if (thumb === minThumb) {
                    minVal = Math.min(newVal, maxVal - 1);
                } else {
                    maxVal = Math.max(newVal, minVal + 1);
                }
                updateSlider();
            }

            let activeThumb = null;

            minThumb.addEventListener('mousedown', () => activeThumb = minThumb);
            maxThumb.addEventListener('mousedown', () => activeThumb = maxThumb);

            document.addEventListener('mousemove', (e) => {
                if (activeThumb) {
                    const rect = slider.getBoundingClientRect();
                    let x = e.clientX - rect.left - 10;
                    x = Math.max(0, Math.min(x, slider.offsetWidth - 20));
                    setValueFromPosition(x, activeThumb);
                }
            });

            document.addEventListener('mouseup', () => activeThumb = null);

            updateSlider();

            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const productId = this.getAttribute('data-product-id');
                    const quantity = this.getAttribute('data-quantity');

                    fetch('/templates/default/cart_add.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
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
                                button.textContent = 'В корзину';
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
    </script>
</body>
</html>