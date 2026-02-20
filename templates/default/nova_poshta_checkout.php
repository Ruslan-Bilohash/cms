<?php
// /public_html/templates/default/nova_poshta_checkout.php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Проверка наличия API-файлов
$api_files = [
    $_SERVER['DOCUMENT_ROOT'] . '/api/novaya_pochta_cost.php',
    $_SERVER['DOCUMENT_ROOT'] . '/api/novaya_pochta_cities.php',
    $_SERVER['DOCUMENT_ROOT'] . '/api/novaya_pochta_warehouses.php',
    $_SERVER['DOCUMENT_ROOT'] . '/api/novaya_pochta_streets.php'
];
foreach ($api_files as $file) {
    if (!file_exists($file)) {
        error_log("nova_poshta_checkout.php: Отсутствует файл: $file");
        die('<div class="alert alert-danger">Ошибка: отсутствует необходимый API-файл.</div>');
    }
    require_once $file;
}

// Проверка наличия shop_settings.php
$shop_settings_file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/shop_settings.php';
if (!file_exists($shop_settings_file)) {
    error_log("nova_poshta_checkout.php: Отсутствует файл: $shop_settings_file");
    die('<div class="alert alert-danger">Ошибка: отсутствует файл настроек магазина.</div>');
}
$shop_settings = require $shop_settings_file;

$np_cost = new NovaPoshtaCost();
$product_id = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT);
$delivery_method = isset($_GET['delivery_method']) ? trim($_GET['delivery_method']) : '';
$error_message = '';
$delivery_cost = 0;
$currency_symbol = '₴'; // Используем гривны, как в исходном коде

// Логирование
error_log("nova_poshta_checkout.php: Accessed with product_id=$product_id, delivery_method=$delivery_method, session_id=" . session_id());

// Проверка настроек магазина
if (!$shop_settings['shop_enabled'] || !$shop_settings['nova_poshta_enabled']) {
    header("Location: /shop_checkout.php?product_id=$product_id");
    exit;
}

// Проверка параметров
if (!$product_id || strtolower($delivery_method) !== 'nova_poshta') {
    $error_message = '<div class="alert alert-danger">Ошибка: неверные параметры заказа.</div>';
    error_log("nova_poshta_checkout.php: Invalid parameters: product_id=$product_id, delivery_method=$delivery_method");
}

// Получение данных о продукте
$product_stmt = $conn->prepare("
    SELECT id, name, price, category_id, weight, width, length, height 
    FROM shop_products 
    WHERE id = ? AND status = 'active'
");
$product_stmt->bind_param('i', $product_id);
$product_stmt->execute();
$product = $product_stmt->get_result()->fetch_assoc();
$product_stmt->close();

if (!$product) {
    $error_message = '<div class="alert alert-danger">Товар не найден.</div>';
    error_log("nova_poshta_checkout.php: Product not found for product_id=$product_id");
}

// Получение данных корзины
$cart_items = $_SESSION['cart_items'] ?? [];
if (empty($cart_items)) {
    $cart_items = [[
        'id' => $product['id'],
        'name' => $product['name'],
        'price' => $product['price'],
        'quantity' => 1,
        'subtotal' => $product['price'],
        'category_id' => $product['category_id'],
        'weight' => $product['weight'] ?? 1.00, // Из вывода: weight=1.00
        'width' => $product['width'] ?? 10.00,
        'length' => $product['length'] ?? 10.00,
        'height' => $product['height'] ?? 10.00
    ]];
}
$total = 0;
$total_weight = 0;
$items = [];
foreach ($cart_items as $item) {
    $total += $item['subtotal'];
    $total_weight += ($item['weight'] ?? 1.00) * $item['quantity'];
    for ($i = 0; $i < $item['quantity']; $i++) {
        $items[] = [
            'weight' => $item['weight'] ?? 1.00,
            'width' => $item['width'] ?? 10.00,
            'length' => $item['length'] ?? 10.00,
            'height' => $item['height'] ?? 10.00
        ];
    }
}

// Настройки API
$np_settings = $conn->query("SELECT service_type_default FROM nova_poshta_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$np_service_type = $np_settings['service_type_default'] ?? 'WarehouseWarehouse';

// Способ оплаты
$np_payment_method = $_SESSION['payment_method'] ?? 'Cash';
$allowed_payment_methods = ['Cash' => 'Наличные', 'Card' => 'Картой'];
if (!array_key_exists($np_payment_method, $allowed_payment_methods)) {
    $np_payment_method = 'Cash';
}

// Расчёт стоимости доставки
$city_ref = $_POST['city_ref'] ?? '';
$service_type = $_POST['service_type'] ?? $np_service_type;
if ($city_ref) {
    $cost_response = $np_cost->calculateDeliveryCost([
        'city_recipient' => $city_ref,
        'service_type' => $service_type,
        'items' => $items,
        'cost' => $total,
        'seats_amount' => count($items)
    ]);
    if ($cost_response['success'] && !empty($cost_response['data'])) {
        $delivery_cost = $cost_response['data'][0]['Cost'] ?? 0;
    } else {
        $error_message = '<div class="alert alert-danger">Ошибка расчёта стоимости доставки: ' . htmlspecialchars($cost_response['errors'][0] ?? 'Неизвестная ошибка') . '</div>';
        error_log("nova_poshta_checkout.php: Delivery cost calculation failed: " . json_encode($cost_response));
    }
}

// Обработка заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_checkout'])) {
    $first_name = $conn->real_escape_string(trim($_POST['first_name'] ?? ''));
    $last_name = $conn->real_escape_string(trim($_POST['last_name'] ?? ''));
    $patronymic = $conn->real_escape_string(trim($_POST['patronymic'] ?? ''));
    $customer_phone = $conn->real_escape_string(trim($_POST['customer_phone'] ?? ''));
    $city_ref = $conn->real_escape_string(trim($_POST['city_ref'] ?? ''));
    $city_name = $conn->real_escape_string(trim($_POST['city_name'] ?? ''));
    $service_type = $conn->real_escape_string(trim($_POST['service_type'] ?? ''));
    $warehouse_ref = $conn->real_escape_string(trim($_POST['warehouse_ref'] ?? ''));
    $street_ref = $conn->real_escape_string(trim($_POST['street_ref'] ?? ''));
    $building_number = $conn->real_escape_string(trim($_POST['building_number'] ?? ''));
    $payment_method = $conn->real_escape_string(trim($_POST['payment_method'] ?? ''));

    // Повторный расчёт
    if ($city_ref) {
        $cost_response = $np_cost->calculateDeliveryCost([
            'city_recipient' => $city_ref,
            'service_type' => $service_type,
            'items' => $items,
            'cost' => $total,
            'seats_amount' => count($items)
        ]);
        if ($cost_response['success'] && !empty($cost_response['data'])) {
            $delivery_cost = $cost_response['data'][0]['Cost'] ?? 0;
        }
    }

    // Валидация
    if (empty($customer_phone) || empty($city_ref) || empty($payment_method) || 
        ($service_type === 'WarehouseWarehouse' && empty($warehouse_ref)) || 
        ($service_type === 'DoorsWarehouse' && (empty($street_ref) || empty($building_number)))) {
        $error_message = '<div class="alert alert-danger">Заполните все обязательные поля!</div>';
        error_log("nova_poshta_checkout.php: Validation failed: missing required fields");
    } else {
        $order_number = 'ORD-' . time();
        $status = 'ожидает';
        $total_with_delivery = $total + $delivery_cost;
        $login = isset($_SESSION['user_login']) ? $conn->real_escape_string($_SESSION['user_login']) : null;

        $stmt = $conn->prepare("
            INSERT INTO shop_orders (
                product_id, category_id, order_number, first_name, last_name, patronymic, 
                customer_phone, city, address, delivery_method, payment_method, total_cost, 
                delivery_cost, status, login, nova_poshta_city, nova_poshta_warehouse, 
                nova_poshta_street, building_number, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $address = $service_type === 'WarehouseWarehouse' ? 'Отделение Новой Почты' : $building_number;
        $stmt->bind_param(
            "iisssssssssdsssssss",
            $product['id'],
            $product['category_id'],
            $order_number,
            $first_name,
            $last_name,
            $patronymic,
            $customer_phone,
            $city_name,
            $address,
            $delivery_method,
            $payment_method,
            $total_with_delivery,
            $delivery_cost,
            $status,
            $login,
            $city_name,
            $warehouse_ref,
            $street_ref,
            $building_number
        );

        if ($stmt->execute()) {
            $order_id = $conn->insert_id;

            $item_stmt = $conn->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price, subtotal)
                VALUES (?, ?, ?, ?, ?)
            ");
            if ($item_stmt) {
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
                        error_log("nova_poshta_checkout.php: Ошибка вставки в order_items: " . $item_stmt->error);
                    }
                }
                $item_stmt->close();
            }

            unset($_SESSION['cart']);
            unset($_SESSION['cart_items']);
            unset($_SESSION['delivery_cost']);
            unset($_SESSION['nova_poshta_data']);
            unset($_SESSION['payment_method']);

            header('Location: /shop_success');
            exit;
        } else {
            $error_message = '<div class="alert alert-danger">Ошибка при создании заказа: ' . htmlspecialchars($stmt->error) . '</div>';
            error_log("nova_poshta_checkout.php: Ошибка создания заказа: " . $stmt->error);
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление доставки - Новая Почта</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f5f5f5; font-family: 'Arial', sans-serif; color: #333; }
        .checkout-container { background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); max-width: 600px; margin: 2rem auto; }
        .form-label { font-weight: 600; color: #343a40; }
        .form-control, .form-select { border-radius: 10px; padding: 10px; }
        .btn-submit { background: linear-gradient(90deg, #28a745, #34c759); border: none; border-radius: 25px; padding: 0.75rem 2rem; color: white; }
        .btn-submit:hover { background: linear-gradient(90deg, #218838, #2ecc71); }
        .product-info { background: #f9f9f9; padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; }
        #total-cost { font-weight: bold; }
        .d-none { display: none; }
        .autocomplete-suggestions { position: absolute; background: white; border: 1px solid #ddd; border-radius: 10px; max-height: 200px; overflow-y: auto; z-index: 1000; width: calc(100% - 24px); box-shadow: 0 5px 10px rgba(0,0,0,0.1); }
        .autocomplete-suggestion { padding: 10px; cursor: pointer; }
        .autocomplete-suggestion:hover { background: #f0f0f0; }
        @media (max-width: 576px) { .checkout-container { padding: 1rem; margin: 1rem; } .btn-submit { padding: 0.5rem 1rem; } }
    </style>
</head>
<body>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header_shop.php'; ?>

<div class="container my-5">
    <div class="checkout-container">
        <h2 class="text-center mb-4"><i class="fas fa-truck me-2"></i>Оформление доставки</h2>

        <?php if ($error_message): ?>
            <?php echo $error_message; ?>
        <?php elseif (!$product): ?>
            <div class="alert alert-danger">Товар не найден.</div>
        <?php else: ?>
            <div class="product-info">
                <h5><i class="fas fa-box-open me-2"></i>Товары в заказе</h5>
                <?php foreach ($cart_items as $item): ?>
                    <p><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>): 
                       <?php echo number_format($item['subtotal'], 2) . ' ' . $currency_symbol; ?></p>
                <?php endforeach; ?>
                <p>Доставка: <span id="delivery-cost"><?php echo number_format($delivery_cost, 2) . ' ' . $currency_symbol; ?></span></p>
                <p>Итого: <span id="total-cost"><?php echo number_format($total + $delivery_cost, 2) . ' ' . $currency_symbol; ?></span></p>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-user me-2"></i>Фамилия</label>
                    <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-user me-2"></i>Имя</label>
                    <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-user me-2"></i>Отчество</label>
                    <input type="text" name="patronymic" class="form-control" value="<?php echo htmlspecialchars($_POST['patronymic'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-phone me-2"></i>Телефон</label>
                    <input type="tel" name="customer_phone" class="form-control" value="<?php echo htmlspecialchars($_POST['customer_phone'] ?? ''); ?>" required>
                </div>
                <div class="mb-3 position-relative">
                    <label class="form-label"><i class="fas fa-city me-2"></i>Город</label>
                    <input type="text" id="city_input" class="form-control" autocomplete="off" placeholder="Введите город" value="<?php echo htmlspecialchars($_POST['city_name'] ?? ''); ?>" required>
                    <input type="hidden" name="city_ref" id="city_ref" value="<?php echo htmlspecialchars($_POST['city_ref'] ?? ''); ?>">
                    <input type="hidden" name="city_name" id="city_name" value="<?php echo htmlspecialchars($_POST['city_name'] ?? ''); ?>">
                    <div id="city_suggestions" class="autocomplete-suggestions d-none"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-box me-2"></i>Тип доставки</label>
                    <select name="service_type" id="service_type" class="form-select" required>
                        <option value="WarehouseWarehouse" <?php echo ($np_service_type === 'WarehouseWarehouse' || ($_POST['service_type'] ?? '') === 'WarehouseWarehouse') ? 'selected' : ''; ?>>Отделение</option>
                        <option value="DoorsWarehouse" <?php echo ($np_service_type === 'DoorsWarehouse' || ($_POST['service_type'] ?? '') === 'DoorsWarehouse') ? 'selected' : ''; ?>>Адрес</option>
                    </select>
                </div>
                <div class="mb-3" id="warehouse_field" <?php echo ($np_service_type === 'DoorsWarehouse' || ($_POST['service_type'] ?? '') === 'DoorsWarehouse') ? 'class="d-none"' : ''; ?>>
                    <label class="form-label"><i class="fas fa-warehouse me-2"></i>Отделение</label>
                    <select name="warehouse_ref" id="warehouse_ref" class="form-select">
                        <option value="">Выберите отделение</option>
                    </select>
                </div>
                <div class="mb-3" id="address_field" <?php echo ($np_service_type === 'WarehouseWarehouse' || ($_POST['service_type'] ?? '') === 'WarehouseWarehouse') ? 'class="d-none"' : ''; ?>>
                    <label class="form-label"><i class="fas fa-road me-2"></i>Улица</label>
                    <select name="street_ref" id="street_ref" class="form-select">
                        <option value="">Выберите улицу</option>
                    </select>
                </div>
                <div class="mb-3" id="building_field" <?php echo ($np_service_type === 'WarehouseWarehouse' || ($_POST['service_type'] ?? '') === 'WarehouseWarehouse') ? 'class="d-none"' : ''; ?>>
                    <label class="form-label"><i class="fas fa-home me-2"></i>Номер дома</label>
                    <input type="text" name="building_number" class="form-control" value="<?php echo htmlspecialchars($_POST['building_number'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-credit-card me-2"></i>Способ оплаты</label>
                    <select name="payment_method" id="payment_method" class="form-select" required>
                        <?php foreach ($allowed_payment_methods as $value => $label): ?>
                            <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $np_payment_method === $value ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="submit_checkout" class="btn btn-submit w-100"><i class="fas fa-check-circle me-2"></i>Подтвердить доставку</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cityInput = document.getElementById('city_input');
    const cityRefInput = document.getElementById('city_ref');
    const cityNameInput = document.getElementById('city_name');
    const suggestionsDiv = document.getElementById('city_suggestions');
    const serviceTypeSelect = document.getElementById('service_type');
    const warehouseField = document.getElementById('warehouse_field');
    const addressField = document.getElementById('address_field');
    const buildingField = document.getElementById('building_field');
    const warehouseSelect = document.getElementById('warehouse_ref');
    const streetSelect = document.getElementById('street_ref');
    const deliveryCostSpan = document.getElementById('delivery-cost');
    const totalCostSpan = document.getElementById('total-cost');
    const total = <?php echo json_encode($total); ?>;
    const items = <?php echo json_encode($items); ?>;
    const currencySymbol = '<?php echo $currency_symbol; ?>';

    function updateDeliveryCost() {
        const cityRef = cityRefInput.value.trim();
        const serviceType = serviceTypeSelect.value;
        if (cityRef && serviceType) {
            fetch('/api/novaya_pochta_cost.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    city_recipient: cityRef,
                    service_type: serviceType,
                    items: items,
                    cost: total,
                    seats_amount: items.length
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data && data.data[0].Cost) {
                    const deliveryCost = parseFloat(data.data[0].Cost);
                    deliveryCostSpan.textContent = `${deliveryCost.toFixed(2)} ${currencySymbol}`;
                    totalCostSpan.textContent = `${(total + deliveryCost).toFixed(2)} ${currencySymbol}`;
                } else {
                    deliveryCostSpan.textContent = `0.00 ${currencySymbol}`;
                    totalCostSpan.textContent = `${total.toFixed(2)} ${currencySymbol}`;
                    console.error('Ошибка расчёта стоимости:', data.errors || 'Неизвестная ошибка');
                }
            })
            .catch(error => {
                console.error('Ошибка запроса стоимости доставки:', error);
                deliveryCostSpan.textContent = `0.00 ${currencySymbol}`;
                totalCostSpan.textContent = `${total.toFixed(2)} ${currencySymbol}`;
            });
        } else {
            deliveryCostSpan.textContent = `0.00 ${currencySymbol}`;
            totalCostSpan.textContent = `${total.toFixed(2)} ${currencySymbol}`;
        }
    }

    cityInput.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length < 3) { // Увеличим до 3 символов для меньшего числа запросов
            suggestionsDiv.classList.add('d-none');
            suggestionsDiv.innerHTML = '';
            cityRefInput.value = '';
            cityNameInput.value = '';
            updateDeliveryCost();
            return;
        }

        fetch('/api/novaya_pochta_cities.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ query: query })
        })
        .then(response => response.json())
        .then(data => {
            suggestionsDiv.innerHTML = '';
            if (data.success && data.data) {
                data.data.forEach(city => {
                    const div = document.createElement('div');
                    div.className = 'autocomplete-suggestion';
                    div.textContent = city.Description;
                    div.dataset.ref = city.Ref;
                    div.addEventListener('click', function() {
                        cityInput.value = city.Description;
                        cityRefInput.value = city.Ref;
                        cityNameInput.value = city.Description;
                        suggestionsDiv.classList.add('d-none');
                        updateWarehousesAndStreets(city.Ref);
                        updateDeliveryCost();
                    });
                    suggestionsDiv.appendChild(div);
                });
                suggestionsDiv.classList.remove('d-none');
            } else {
                suggestionsDiv.classList.add('d-none');
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки городов:', error);
            suggestionsDiv.classList.add('d-none');
        });
    });

    document.addEventListener('click', function(e) {
        if (!cityInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.classList.add('d-none');
        }
    });

    function updateWarehousesAndStreets(cityRef) {
        if (!cityRef || cityRef.length < 10) { // Проверка на валидный UUID
            warehouseSelect.innerHTML = '<option value="">Выберите отделение</option>';
            streetSelect.innerHTML = '<option value="">Выберите улицу</option>';
            return;
        }

        fetch('/api/novaya_pochta_warehouses.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ city_ref: cityRef })
        })
        .then(response => response.json())
        .then(data => {
            warehouseSelect.innerHTML = '<option value="">Выберите отделение</option>';
            if (data.success && data.data) {
                data.data.forEach(warehouse => {
                    const option = document.createElement('option');
                    option.value = warehouse.Ref;
                    option.textContent = warehouse.Description;
                    warehouseSelect.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Ошибка загрузки отделений:', error));

        fetch('/api/novaya_pochta_streets.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ city_ref: cityRef })
        })
        .then(response => response.json())
        .then(data => {
            streetSelect.innerHTML = '<option value="">Выберите улицу</option>';
            if (data.success && data.data) {
                data.data.forEach(street => {
                    const option = document.createElement('option');
                    option.value = street.Ref;
                    option.textContent = street.Description;
                    streetSelect.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Ошибка загрузки улиц:', error));
    }

    serviceTypeSelect.addEventListener('change', function() {
        if (this.value === 'WarehouseWarehouse') {
            warehouseField.classList.remove('d-none');
            addressField.classList.add('d-none');
            buildingField.classList.add('d-none');
            warehouseSelect.required = true;
            streetSelect.required = false;
        } else {
            warehouseField.classList.add('d-none');
            addressField.classList.remove('d-none');
            buildingField.classList.remove('d-none');
            warehouseSelect.required = false;
            streetSelect.required = true;
        }
        updateDeliveryCost();
    });

    // Начальная инициализация только для валидного city_ref
    const initialCityRef = cityRefInput.value.trim();
    if (initialCityRef && initialCityRef.length >= 10) {
        updateWarehousesAndStreets(initialCityRef);
        updateDeliveryCost();
    }
});
</script>
</body>
</html>