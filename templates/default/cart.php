<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/novaya_pochta.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_log("cart.php: Session started, session_id=" . session_id());

clearstatcache();

$payment_methods = [];
$stmt = $conn->prepare("SELECT api_type, is_active, api_key, api_secret, payer_type, default_note, shop_name FROM api WHERE api_type IN ('cash_on_delivery', 'bank_transfer', 'stripe', 'apple_pay', 'google_pay', 'paypal') AND is_active = 1");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $payment_methods[] = [
        'name' => ucfirst(str_replace('_', ' ', $row['api_type'])),
        'enabled' => (bool)$row['is_active'],
        'environment' => $row['payer_type'] ?? 'TEST',
        'merchant_id' => $row['default_note'] ?? '',
        'api_key' => $row['api_key'] ?? '',
        'api_secret' => $row['api_secret'] ?? '',
        'domains' => $row['shop_name'] ?? ''
    ];
}
$stmt->close();

error_log("cart.php: Payment methods loaded: " . json_encode($payment_methods));

$currency_code = 'UAH';
$currency_symbol = ['EUR' => '€', 'UAH' => '₴', 'RUB' => '₽', 'USD' => '$'][$currency_code] ?? '₴';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $id => $quantity) {
            $quantity = max(0, (int)$quantity);
            if ($quantity > 0) {
                $_SESSION['cart'][$id] = $quantity;
            } else {
                unset($_SESSION['cart'][$id]);
            }
        }
        header('Location: /cart');
        exit;
    } elseif (isset($_POST['remove_item'])) {
        $id = (int)$_POST['remove_item'];
        unset($_SESSION['cart'][$id]);
        header('Location: /cart');
        exit;
    } elseif (isset($_POST['checkout'])) {
        $cart_items = [];
        $total = 0;
        $delivery_cost = 0;

        if (!empty($_SESSION['cart'])) {
            $product_ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
            $cart_products = $conn->query("
                SELECT id, name, price, category_id 
                FROM shop_products 
                WHERE id IN ($product_ids) AND status = 'active'
            ")->fetch_all(MYSQLI_ASSOC);

            foreach ($cart_products as $product) {
                $quantity = $_SESSION['cart'][$product['id']];
                $subtotal = $product['price'] * $quantity;
                $total += $subtotal;
                $cart_items[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                    'category_id' => $product['category_id']
                ];
            }
        }

        if (!empty($cart_items)) {
            $delivery_method = $conn->real_escape_string(trim($_POST['delivery_method'] ?? ''));
            $payment_method = $conn->real_escape_string(trim($_POST['payment_method'] ?? ''));

            error_log("cart.php: Delivery method selected: '$delivery_method', Payment method: '$payment_method'");

            if ($delivery_method) {
                $stmt = $conn->prepare("SELECT cost FROM shop_delivery_methods WHERE name = ? AND is_enabled = 1");
                $stmt->bind_param('s', $delivery_method);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $delivery_cost = $result->fetch_assoc()['cost'];
                } else {
                    error_log("cart.php: Delivery method '$delivery_method' not found or disabled");
                    $error_message = '<div class="alert alert-danger">Выбранный метод доставки недоступен.</div>';
                }
                $stmt->close();
            }

            if (strtolower(trim($delivery_method)) === 'новая почта') {
                $first_product_id = $cart_items[0]['id'];
                $_SESSION['cart_items'] = $cart_items;
                $_SESSION['delivery_cost'] = $delivery_cost;
                $_SESSION['payment_method'] = $payment_method;
                error_log("cart.php: Redirecting to nova_poshta_checkout.php with product_id=$first_product_id, payment_method=$payment_method, cart_items=" . json_encode($cart_items));
                header("Location: /templates/default/nova_poshta_checkout.php?product_id=$first_product_id&delivery_method=nova_poshta");
                exit;
            }

            $order_number = 'ORD-' . time();
            $first_name = $conn->real_escape_string(trim($_POST['first_name'] ?? ''));
            $last_name = $conn->real_escape_string(trim($_POST['last_name'] ?? ''));
            $patronymic = $conn->real_escape_string(trim($_POST['patronymic'] ?? ''));
            $customer_phone = $conn->real_escape_string(trim($_POST['customer_phone'] ?? ''));
            $city = $conn->real_escape_string(trim($_POST['city'] ?? ''));
            $address = $conn->real_escape_string(trim($_POST['address'] ?? ''));
            $status = 'ожидает';
            $login = isset($_SESSION['user_login']) ? $conn->real_escape_string($_SESSION['user_login']) : null;
            $first_item = $cart_items[0];
            $category_id = $first_item['category_id'] ?? null;

            $total_with_delivery = $total + $delivery_cost;

            if (isset($_SESSION['nova_poshta_data'])) {
                $np_data = $_SESSION['nova_poshta_data'];
                $nova_poshta_city = $conn->real_escape_string($np_data['nova_poshta_city_name'] ?? '');
                $nova_poshta_warehouse = $conn->real_escape_string($np_data['nova_poshta_warehouse_ref'] ?? '');
                $nova_poshta_street = $conn->real_escape_string($np_data['nova_poshta_street_ref'] ?? '');
                $building_number = $conn->real_escape_string($np_data['building_number'] ?? '');
                $city = $nova_poshta_city;
                $address = $np_data['service_type'] === 'WarehouseWarehouse' ? 'Отделение Новой Почты' : $building_number;
                $first_name = $conn->real_escape_string($np_data['first_name'] ?? '');
                $last_name = $conn->real_escape_string($np_data['last_name'] ?? '');
                $patronymic = $conn->real_escape_string($np_data['patronymic'] ?? '');
                $customer_phone = $conn->real_escape_string($np_data['customer_phone'] ?? '');
                $payment_method = $conn->real_escape_string($np_data['payment_method'] ?? '');
                $delivery_cost = $_SESSION['delivery_cost'] ?? $delivery_cost;
                $total_with_delivery = $total + $delivery_cost;
            }

            if (empty($customer_phone) || empty($delivery_method) || empty($payment_method) || empty($city) || empty($address)) {
                $error_message = '<div class="alert alert-danger">Заполните все обязательные поля (телефон, способ доставки, способ оплаты, город, адрес)!</div>';
            } else {
                $stmt = $conn->prepare("
                    INSERT INTO shop_orders (
                        product_id, category_id, order_number, first_name, last_name, patronymic, 
                        customer_phone, city, address, delivery_method, payment_method, total_cost, 
                        delivery_cost, status, login, nova_poshta_city, nova_poshta_warehouse, 
                        nova_poshta_street, building_number, nova_poshta_ttn, currency_code, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->bind_param(
                    "iisssssssssdsssssssss",
                    $first_item['id'],
                    $category_id,
                    $order_number,
                    $first_name,
                    $last_name,
                    $patronymic,
                    $customer_phone,
                    $city,
                    $address,
                    $delivery_method,
                    $payment_method,
                    $total_with_delivery,
                    $delivery_cost,
                    $status,
                    $login,
                    $nova_poshta_city,
                    $nova_poshta_warehouse,
                    $nova_poshta_street,
                    $building_number,
                    $nova_poshta_ttn,
                    $currency_code
                );
                if ($stmt->execute()) {
                    $order_id = $conn->insert_id;

                    $item_stmt = $conn->prepare("
                        INSERT INTO order_items (order_id, product_id, quantity, price, subtotal)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    if (!$item_stmt) {
                        error_log("cart.php: Ошибка подготовки запроса order_items: " . $conn->error);
                        $error_message = '<div class="alert alert-danger">Ошибка сервера. Попробуйте позже.</div>';
                    } else {
                        foreach ($cart_items as $item) {
                            $item_stmt->bind_param(
                                "iiidd",
                                $order_id,
                                $item['id'],
                                $item['quantity'],
                                $item['price'],
                                $item['subtotal']
                            );
                            if (!$item_stmt->execute()) {
                                error_log("cart.php: Ошибка вставки в order_items: " . $item_stmt->error);
                            }
                        }
                        $item_stmt->close();
                    }

                    unset($_SESSION['cart']);
                    unset($_SESSION['cart_items']);
                    unset($_SESSION['delivery_cost']);
                    unset($_SESSION['nova_poshta_data']);
                    unset($_SESSION['payment_method']);

                    if ($payment_method === 'Paypal') {
                        $paypal_index = array_search('Paypal', array_column($payment_methods, 'name'));
                        if ($paypal_index !== false && !empty($payment_methods[$paypal_index]['api_key'])) {
                            header("Location: /templates/default/shop_checkout_paypal.php?order_id=$order_id");
                            exit;
                        } else {
                            $error_message = '<div class="alert alert-danger">PayPal не настроен. Выберите другой метод оплаты.</div>';
                        }
                    } elseif ($payment_method === 'Apple Pay' || $payment_method === 'Google Pay') {
                        header("Location: /payment_process.php?order_id=$order_id");
                        exit;
                    } else {
                        header('Location: /shop_success');
                        exit;
                    }
                } else {
                    error_log("cart.php: Ошибка создания заказа: " . $stmt->error);
                    $error_message = '<div class="alert alert-danger">Ошибка при создании заказа: ' . htmlspecialchars($stmt->error) . '</div>';
                }
                $stmt->close();
            }
        } else {
            $error_message = '<div class="alert alert-danger">Ваша корзина пуста.</div>';
        }
    }
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header_shop.php';

$cart_items = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $product_ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $cart_products = $conn->query("
        SELECT id, name, price 
        FROM shop_products 
        WHERE id IN ($product_ids) AND status = 'active'
    ")->fetch_all(MYSQLI_ASSOC);

    foreach ($cart_products as $product) {
        $quantity = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $quantity;
        $total += $subtotal;
        $cart_items[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}

$delivery_methods = $conn->query("SELECT name, cost FROM shop_delivery_methods WHERE is_enabled = 1 ORDER BY sort_order ASC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корзина</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="container my-5 cart-container">
    <h1 class="mb-4 text-center"><i class="fas fa-shopping-cart me-2"></i> Корзина</h1>

    <?php if (empty($payment_methods)): ?>
        <div class="alert alert-warning">Нет активных методов оплаты.</div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <?php echo $error_message; ?>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
        <p class="text-muted text-center"><i class="fas fa-exclamation-circle me-2"></i> Ваша корзина пуста.</p>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-7 col-md-12 mb-4">
                <form method="POST" id="cartForm">
                    <div class="cart-items">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item card mb-3 shadow-sm">
                                <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
                                    <div class="cart-item-name"><i class="fas fa-box me-2"></i> <?php echo htmlspecialchars($item['name']); ?></div>
                                    <div class="d-flex align-items-center">
                                        <span class="me-3"><i class="fas fa-ruble-sign me-1"></i> <?php echo number_format($item['price'], 2) . ' ' . $currency_symbol; ?></span>
                                        <input type="number" name="quantity[<?php echo $item['id']; ?>]" value="<?php echo $item['quantity']; ?>" min="0" class="form-control cart-quantity-input" onchange="this.form.submit()">
                                        <span class="ms-3"><i class="fas fa-money-bill-wave me-1"></i> <?php echo number_format($item['subtotal'], 2) . ' ' . $currency_symbol; ?></span>
                                        <button type="submit" name="remove_item" value="<?php echo $item['id']; ?>" class="btn btn-outline-danger btn-sm ms-3" title="Удалить">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" name="update_cart" class="btn btn-outline-success w-100 mt-3">
                        <i class="fas fa-sync-alt me-2"></i> Обновить корзину
                    </button>
                </form>
            </div>

            <div class="col-lg-5 col-md-12">
                <div class="checkout-section card shadow-sm p-4">
                    <h3 class="mb-4 text-center"><i class="fas fa-check-circle me-2"></i> Оформление заказа</h3>
                    <form method="POST" id="checkoutForm">
                        <div class="mb-3">
                            <label for="delivery_method" class="form-label"><i class="fas fa-truck me-2"></i> Способ доставки</label>
                            <select name="delivery_method" id="delivery_method" class="form-select stylish-select" required>
                                <option value="">Выберите способ доставки</option>
                                <?php foreach ($delivery_methods as $method): ?>
                                    <option value="<?php echo htmlspecialchars($method['name']); ?>" data-cost="<?php echo $method['cost']; ?>">
                                        <?php echo htmlspecialchars($method['name']); ?>
                                        <?php if ($method['cost'] > 0): ?>
                                            (+<?php echo number_format($method['cost'], 2) . ' ' . $currency_symbol; ?>)
                                        <?php elseif ($method['name'] === 'Новая Почта'): ?>
                                            (рассчитывается при оформлении)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="customer-info" class="hidden">
                            <div class="mb-3">
                                <label for="first_name" class="form-label"><i class="fas fa-user me-2"></i> Имя</label>
                                <input type="text" name="first_name" id="first_name" class="form-control stylish-input">
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label"><i class="fas fa-user me-2"></i> Фамилия</label>
                                <input type="text" name="last_name" id="last_name" class="form-control stylish-input">
                            </div>
                            <div class="mb-3">
                                <label for="patronymic" class="form-label"><i class="fas fa-user me-2"></i> Отчество</label>
                                <input type="text" name="patronymic" id="patronymic" class="form-control stylish-input">
                            </div>
                            <div class="mb-3">
                                <label for="customer_phone" class="form-label"><i class="fas fa-phone me-2"></i> Телефон</label>
                                <input type="text" name="customer_phone" id="customer_phone" class="form-control stylish-input" required>
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label"><i class="fas fa-city me-2"></i> Город</label>
                                <input type="text" name="city" id="city" class="form-control stylish-input" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label"><i class="fas fa-map-marker-alt me-2"></i> Адрес</label>
                                <input type="text" name="address" id="address" class="form-control stylish-input" required>
                            </div>
                            <div class="mb-3">
                                <label for="payment_method" class="form-label"><i class="fas fa-credit-card me-2"></i> Способ оплаты</label>
                                <select name="payment_method" id="payment_method" class="form-select stylish-select" required>
                                    <option value="">Выберите способ оплаты</option>
                                    <?php foreach ($payment_methods as $method): ?>
                                        <option value="<?php echo htmlspecialchars($method['name']); ?>">
                                            <?php echo htmlspecialchars($method['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="total-text mb-3 text-center">
                                <div><i class="fas fa-boxes me-2"></i> Товары: <?php echo number_format($total, 2) . ' ' . $currency_symbol; ?></div>
                                <div id="delivery-cost-text"><i class="fas fa-truck me-2"></i> Доставка: 0.00 <?php echo $currency_symbol; ?></div>
                                <div><i class="fas fa-wallet me-2"></i> Итого: <span id="total-with-delivery"><?php echo number_format($total, 2); ?></span> <?php echo $currency_symbol; ?></div>
                            </div>
                            <button type="submit" name="checkout" class="btn btn-success w-100 mb-2 default-checkout-button">
                                <i class="fas fa-check-circle me-2"></i> Подтвердить заказ
                            </button>
                            <div id="payment-buttons">
                                <button type="submit" name="checkout" id="apple-pay-button" class="btn apple-pay-btn w-100 mb-2" style="display: none;">
                                    <span class="apple-pay-logo"></span> Оплатить
                                </button>
                                <button type="submit" name="checkout" id="google-pay-button" class="btn google-pay-btn w-100" style="display: none;">
                                    <span class="google-pay-logo"></span> Оплатить
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer_shop_contact.php'; ?>

<style>
body {
    background: #f5f5f5;
    font-family: 'Arial', sans-serif;
    color: #333;
}
.cart-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}
.cart-items .cart-item {
    background: #fff;
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.cart-items .cart-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
.cart-item-name {
    font-weight: 600;
    color: #2ecc71;
}
.cart-quantity-input {
    width: 80px;
    text-align: center;
    border: 2px solid #2ecc71;
    border-radius: 25px;
    padding: 5px;
    transition: all 0.3s ease;
}
.cart-quantity-input:focus {
    border-color: #27ae60;
    box-shadow: 0 0 8px rgba(46, 204, 113, 0.5);
    outline: none;
}
.btn-outline-danger {
    border-color: #ff4d4d;
    color: #ff4d4d;
    border-radius: 25px;
    transition: all 0.3s ease;
}
.btn-outline-danger:hover {
    background: #ff4d4d;
    color: #fff;
}
.btn-outline-success {
    border-color: #2ecc71;
    color: #2ecc71;
    border-radius: 25px;
    transition: all 0.3s ease;
}
.btn-outline-success:hover {
    background: #2ecc71;
    color: #fff;
}
.checkout-section {
    background: #fff;
    border-radius: 15px;
}
.stylish-input, .stylish-select {
    border: 2px solid #2ecc71;
    border-radius: 10px;
    padding: 10px;
    font-size: 16px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.stylish-input:focus, .stylish-select:focus {
    border-color: #27ae60;
    box-shadow: 0 0 10px rgba(46, 204, 113, 0.5);
    outline: none;
}
.form-label {
    font-weight: 600;
    color: #343a40;
}
.btn-success {
    background: #2ecc71;
    border: none;
    border-radius: 25px;
    padding: 12px;
    font-size: 18px;
    transition: all 0.3s ease;
}
.btn-success:hover {
    background: #27ae60;
    transform: translateY(-2px);
}
.total-text {
    font-size: 18px;
    font-weight: 600;
    color: #333;
}
.total-text div {
    margin-bottom: 5px;
}
.hidden {
    display: none !important;
}
.apple-pay-btn {
    background: #000;
    color: #fff;
    border-radius: 25px;
    padding: 12px;
    font-size: 18px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}
.apple-pay-btn:hover {
    background: #333;
    transform: translateY(-2px);
}
.apple-pay-logo {
    display: inline-block;
    width: 40px;
    height: 24px;
    background: url('https://applepay.cdn-apple.com/jsapi/v1/apple-pay-logo-white.svg') no-repeat center;
    background-size: contain;
    margin-right: 8px;
}
.google-pay-btn {
    background: #fff;
    color: #000;
    border: 2px solid #4285f4;
    border-radius: 25px;
    padding: 10px;
    font-size: 18px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}
.google-pay-btn:hover {
    background: #f1f3f4;
    transform: translateY(-2px);
}
.google-pay-logo {
    display: inline-block;
    width: 50px;
    height: 20px;
    background: url('https://developers.google.com/pay/api/images/brandguidelines/googlepay-button-dark.svg') no-repeat center;
    background-size: contain;
    margin-right: 8px;
}
@media (max-width: 992px) {
    .cart-quantity-input { width: 70px; }
    .total-text { font-size: 16px; }
}
@media (max-width: 768px) {
    .cart-item { flex-direction: column; text-align: center; }
    .cart-item-name { margin-bottom: 10px; }
    .btn-outline-danger { width: 100%; margin-top: 10px; }
    .total-text { font-size: 14px; }
}
@media (max-width: 576px) {
    .cart-quantity-input { width: 60px; }
    .total-text { font-size: 14px; }
    .btn-success, .apple-pay-btn, .google-pay-btn { font-size: 16px; padding: 10px; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://pay.google.com/gp/p/js/pay.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deliveryMethodSelect = document.getElementById('delivery_method');
    const customerInfo = document.getElementById('customer-info');
    const paymentMethodSelect = document.getElementById('payment_method');
    const paymentButtons = document.getElementById('payment-buttons');
    const applePayButton = document.getElementById('apple-pay-button');
    const googlePayButton = document.getElementById('google-pay-button');
    const defaultCheckoutButton = document.querySelector('.default-checkout-button');
    const deliveryCostText = document.getElementById('delivery-cost-text');
    const totalWithDeliveryText = document.getElementById('total-with-delivery');
    const total = <?php echo $total; ?>;
    const firstProductId = <?php echo !empty($cart_items) ? $cart_items[0]['id'] : 0; ?>;

    customerInfo.classList.add('hidden');
    paymentButtons.classList.remove('hidden');

    function updatePaymentButtons() {
        applePayButton.style.display = 'none';
        googlePayButton.style.display = 'none';
        defaultCheckoutButton.style.display = 'block';

        const paymentMethod = paymentMethodSelect.value;
        if (paymentMethod === 'Apple Pay' && <?php echo json_encode(in_array('Apple Pay', array_column($payment_methods, 'name'))); ?>) {
            applePayButton.style.display = 'block';
            defaultCheckoutButton.style.display = 'none';
        } else if (paymentMethod === 'Google Pay' && <?php echo json_encode(in_array('Google Pay', array_column($payment_methods, 'name'))); ?>) {
            googlePayButton.style.display = 'block';
            defaultCheckoutButton.style.display = 'none';
        }
    }

    deliveryMethodSelect.addEventListener('change', function() {
        console.log('Выбран метод доставки:', this.value, 'firstProductId:', firstProductId);
        if (this.value.trim().toLowerCase() === 'новая почта') {
            if (firstProductId) {
                console.log('Перенаправление на nova_poshta_checkout.php с product_id=' + firstProductId);
                window.location.href = '/templates/default/nova_poshta_checkout.php?product_id=' + firstProductId + '&delivery_method=nova_poshta';
            } else {
                console.error('Ошибка: firstProductId отсутствует');
                alert('Ошибка: нет товаров для оформления доставки.');
            }
        } else if (this.value) {
            customerInfo.classList.remove('hidden');
            const selectedOption = this.options[this.selectedIndex];
            const deliveryCost = parseFloat(selectedOption.getAttribute('data-cost') || 0);
            deliveryCostText.innerHTML = `<i class="fas fa-truck me-2"></i> Доставка: ${deliveryCost.toFixed(2)} <?php echo $currency_symbol; ?>`;
            totalWithDeliveryText.textContent = (total + deliveryCost).toFixed(2);
            updatePaymentButtons();
        } else {
            customerInfo.classList.add('hidden');
            deliveryCostText.innerHTML = `<i class="fas fa-truck me-2"></i> Доставка: 0.00 <?php echo $currency_symbol; ?>`;
            totalWithDeliveryText.textContent = total.toFixed(2);
            updatePaymentButtons();
        }
    });

    paymentMethodSelect.addEventListener('change', function() {
        console.log('Выбран метод оплаты:', this.value);
        updatePaymentButtons();
    });
});
</script>
</body>
</html>