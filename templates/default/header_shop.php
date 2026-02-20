<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

$settings_file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/shop_settings.php';
$shop_settings = file_exists($settings_file) && is_readable($settings_file) ? require_once $settings_file : [];
$currency_symbol = ['EUR' => '€', 'ГРН' => '₴', 'РУБ' => '₽', 'USD' => '$'][$shop_settings['shop_currency'] ?? 'EUR'] ?? '€';
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Ваши стили с главной -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Shop Header</title>
</head>
<body>
<header class="header-shop-wave-header">
    <div class="header-shop-top-bar">
        <a href="/shop" class="logo-link"><div class="header-shop-logo"><span>P</span><span>r</span><span>o</span> <span> </span><span>S</span><span>h</span><span>o</span><span>p</span></div></a>
        <div class="header-shop-search-bar">
            <input type="text" class="smart-search" placeholder="Умный поиск товаров..." autocomplete="off">
            <div class="search-results"></div>
        </div>
        <div class="header-shop-settings">
            <div class="header-shop-cart">
                <a href="/cart" class="text-decoration-none">
                   
                    <span><?php echo $cart_count; ?></span>
                </a>
            </div>
            <div class="header-shop-order">
                <a href="https://t.me/meistru_lt" class="btn btn-glow"> Заказать сайт</a>
            </div>
        </div>
    </div>
</header>

<style>
    .header-shop-wave-header * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    .header-shop-wave-header { height: 120px; padding: 15px 40px; background: linear-gradient(135deg, #ffffff, #f0f9f4); box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1); display: flex; align-items: center; position: relative; width: 100%; overflow: hidden; }
    .header-shop-wave-header::before { content: ''; position: absolute; top: 0; left: 0; width: 200%; height: 100%; background: linear-gradient(90deg, transparent, #2ecc7150, transparent); animation: header-shop-wave 5s infinite linear; z-index: 0; }
    .header-shop-wave-header::after { content: ''; position: absolute; top: 0; left: 0; width: 200%; height: 100%; background: linear-gradient(90deg, transparent, #27ae6050, transparent); animation: header-shop-wave 7s infinite linear reverse; z-index: 0; }
    @keyframes header-shop-wave { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
    .header-shop-top-bar { display: flex; justify-content: space-between; align-items: center; width: 100%; position: relative; z-index: 1; }
    .logo-link { text-decoration: none; }
    .header-shop-logo { font-size: 32px; font-weight: 900; color: #ff0000; text-transform: uppercase; display: inline-flex; animation: header-shop-bounce 2s infinite; }
    .header-shop-logo span { display: inline-block; transition: text-shadow 0.3s ease; }
    .header-shop-logo span.glow { text-shadow: 0 0 10px rgba(255, 0, 0, 1), 0 0 20px rgba(255, 0, 0, 0.8); }
    @keyframes header-shop-bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
    .header-shop-settings { display: flex; gap: 15px; align-items: center; }
    .header-shop-currency select, .header-shop-language select { border: 1px solid #2ecc71; padding: 6px 12px; border-radius: 20px; background: #fff; color: #333; cursor: pointer; transition: all 0.3s ease; }
    .header-shop-currency select:hover, .header-shop-language select:hover { border-color: #27ae60; background: #f0f9f4; transform: translateY(-2px); }
    .header-shop-search-bar { width: 50%; display: flex; align-items: center; border: 2px solid #2ecc71; border-radius: 25px; background: rgba(255, 255, 255, 0.95); padding: 8px 15px; box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1); transition: all 0.3s; }
    .header-shop-search-bar:hover { border-color: #27ae60; }
    .header-shop-search-bar i { color: #2ecc71; margin-right: 10px; font-size: 18px; animation: header-shop-pulse-icon 2s infinite; }
    @keyframes header-shop-pulse-icon { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }
    .header-shop-search-bar .smart-search { flex: 1; border: none; background: transparent; font-size: 16px; color: #333; outline: none; }
    .header-shop-search-bar .search-results { position: absolute; top: 100%; left: 0; right: 0; background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); z-index: 1000; max-height: 300px; overflow-y: auto; display: none; }
    .header-shop-cart { position: relative; }
    .header-shop-cart i { font-size: 24px; color: #ff0000; transition: color 0.3s ease; }
    .header-shop-cart:hover i { color: #cc0000; }
    .header-shop-cart span { position: absolute; top: -8px; right: -8px; background: #ff0000; color: #fff; padding: 2px 6px; border-radius: 50%; font-size: 10px; animation: header-shop-cart-pulse 2s infinite; }
    @keyframes header-shop-cart-pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.1); } }
    .header-shop-order .btn-glow { background: #0088cc; /* Цвет Telegram */ color: white; border: none; padding: 8px 15px; border-radius: 25px; font-weight: 600; text-transform: uppercase; box-shadow: 0 0 10px rgba(0, 136, 204, 0.5); animation: glow 1.5s infinite alternate; transition: all 0.3s ease; display: flex; align-items: center; }
    .header-shop-order .btn-glow i { margin-right: 8px; } /* Отступ для иконки */
    .header-shop-order .btn-glow:hover { box-shadow: 0 0 20px rgba(0, 136, 204, 0.8); transform: scale(1.05); text-decoration: none; color: white; }
    @keyframes glow { from { box-shadow: 0 0 10px rgba(0, 136, 204, 0.5); } to { box-shadow: 0 0 20px rgba(0, 136, 204, 1); } }
    @media (max-width: 768px) { 
        .header-shop-wave-header { height: auto; padding: 10px; } 
        .header-shop-top-bar { flex-direction: column; align-items: flex-start; } 
        .header-shop-search-bar { width: 100%; margin: 10px 0; } 
        .header-shop-settings { margin-top: 10px; flex-wrap: wrap; gap: 10px; } 
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const logo = document.querySelector('.header-shop-logo');
        const letters = logo.querySelectorAll('span');

        function randomGlow() {
            letters.forEach(letter => letter.classList.remove('glow'));
            const randomIndex = Math.floor(Math.random() * letters.length);
            letters[randomIndex].classList.add('glow');
        }

        setInterval(randomGlow, 1000); // Меняется каждую секунду
        randomGlow(); // Запуск сразу

        const searchInput = document.querySelector('.smart-search');
        const searchResults = document.querySelector('.search-results');
        let debounceTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();
            if (query.length < 2) { searchResults.style.display = 'none'; return; }
            debounceTimer = setTimeout(() => {
                fetch('https://masterok.lt/templates/default/search_products.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: `query=${encodeURIComponent(query)}` })
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.error) { searchResults.innerHTML = '<div class="result-item">Ошибка: ' + data.error + '</div>'; }
                    else if (data.length > 0) { data.forEach(product => { const div = document.createElement('div'); div.className = 'result-item'; const productUrl = product.custom_url ? `/shop/${product.custom_url}` : `/shop/${generate_url(product.name)}`; div.innerHTML = `<a href="${productUrl}" class="text-decoration-none text-dark">${product.name} - ${product.price} <?php echo $currency_symbol; ?></a>`; searchResults.appendChild(div); }); }
                    else { searchResults.innerHTML = '<div class="result-item">Ничего не найдено</div>'; }
                    searchResults.style.display = 'block';
                })
                .catch(error => { console.error('Ошибка поиска:', error); searchResults.innerHTML = '<div class="result-item">Ошибка поиска</div>'; searchResults.style.display = 'block'; });
            }, 300);
        });

        document.addEventListener('click', function(e) { if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) { searchResults.style.display = 'none'; } });

        function generate_url(name) { return name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, ''); }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>