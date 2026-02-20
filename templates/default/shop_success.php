<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/novaya_pochta.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$shop_settings = require $_SERVER['DOCUMENT_ROOT'] . '/uploads/shop_settings.php';
$currency_symbol = $shop_settings['shop_currency'] ? 
    ['EUR' => '€', 'ГРН' => '₴', 'РУБ' => '₽', 'USD' => '$'][$shop_settings['shop_currency']] : '₴';

$np = new NovaPoshtaAPI();

// Получаем последний заказ пользователя
$last_order = $conn->query("
    SELECT o.*, p.name as product_name, c.name as category_name
    FROM shop_orders o 
    LEFT JOIN shop_products p ON o.product_id = p.id 
    LEFT JOIN shop_categories c ON o.category_id = c.id 
    ORDER BY o.created_at DESC 
    LIMIT 1
")->fetch_assoc();

// Преобразование UUID в читаемые названия для полей Новой Почты
if ($last_order) {
    if ($last_order['nova_poshta_city'] && preg_match('/^[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}$/i', $last_order['nova_poshta_city'])) {
        $city_response = $np->request(
            ['model' => 'Address', 'method' => 'getCities'],
            ['Ref' => $last_order['nova_poshta_city']]
        );
        $last_order['nova_poshta_city_display'] = $city_response['success'] && !empty($city_response['data'])
            ? $city_response['data'][0]['Description']
            : $last_order['nova_poshta_city'];
    } else {
        $last_order['nova_poshta_city_display'] = $last_order['nova_poshta_city'] ?: 'Не указан';
    }

    if ($last_order['nova_poshta_warehouse'] && preg_match('/^[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}$/i', $last_order['nova_poshta_warehouse'])) {
        $warehouse_response = $np->getWarehouses($last_order['nova_poshta_city']);
        $last_order['nova_poshta_warehouse_display'] = $last_order['nova_poshta_warehouse'];
        if ($warehouse_response['success'] && !empty($warehouse_response['data'])) {
            foreach ($warehouse_response['data'] as $warehouse) {
                if ($warehouse['Ref'] === $last_order['nova_poshta_warehouse']) {
                    $last_order['nova_poshta_warehouse_display'] = $warehouse['Description'];
                    break;
                }
            }
        }
    } else {
        $last_order['nova_poshta_warehouse_display'] = $last_order['nova_poshta_warehouse'] ?: '';
    }

    if ($last_order['nova_poshta_street'] && preg_match('/^[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}$/i', $last_order['nova_poshta_street'])) {
        $street_response = $np->getStreets($last_order['nova_poshta_city']);
        $last_order['nova_poshta_street_display'] = $last_order['nova_poshta_street'];
        if ($street_response['success'] && !empty($street_response['data'])) {
            foreach ($street_response['data'] as $street) {
                if ($street['Ref'] === $last_order['nova_poshta_street']) {
                    $last_order['nova_poshta_street_display'] = $street['Description'];
                    break;
                }
            }
        }
    } else {
        $last_order['nova_poshta_street_display'] = $last_order['nova_poshta_street'] ?: '';
    }
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header_shop.php';
?>

<div class="container my-5 order-success-container">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-success text-white text-center py-4">
            <h1 class="mb-0">
                <i class="fas fa-check-circle me-2"></i>
                Заказ успешно принят!
            </h1>
            <p class="mt-2 mb-0">Спасибо за покупку в <?php echo htmlspecialchars($shop_settings['shop_name'] ?? 'Нашем магазине'); ?></p>
        </div>
        <div class="card-body p-5">
            <div class="row">
                <div class="col-12">
                    <h3 class="text-center mb-4">Счёт-фактура</h3>
                    <div class="invoice-header mb-4">
                        <div class="d-flex justify-content-between flex-wrap">
                            <div>
                                <h5><?php echo htmlspecialchars($shop_settings['shop_name'] ?? 'Наш магазин'); ?></h5>
                                <p class="text-muted">Дата: <?php echo date('d.m.Y H:i', strtotime($last_order['created_at'])); ?></p>
                            </div>
                            <div class="text-end">
                                <h5>Номер заказа</h5>
                                <p class="fw-bold"><?php echo htmlspecialchars($last_order['order_number']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="invoice-details mb-4">
                        <h5 class="mb-3">Детали заказа:</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Товар</th>
                                <td><?php echo htmlspecialchars($last_order['product_name'] ?? 'Не указан'); ?></td>
                            </tr>
                            <tr>
                                <th>Категория</th>
                                <td><?php echo htmlspecialchars($last_order['category_name'] ?? 'Без категории'); ?></td>
                            </tr>
                            <tr>
                                <th>Покупатель</th>
                                <td><?php echo htmlspecialchars(implode(' ', array_filter([
                                    $last_order['last_name'] ?? '',
                                    $last_order['first_name'] ?? '',
                                    $last_order['patronymic'] ?? ''
                                ])) ?: 'Не указан'); ?></td>
                            </tr>
                            <tr>
                                <th>Телефон</th>
                                <td><?php echo htmlspecialchars($last_order['customer_phone'] ?? 'Не указан'); ?></td>
                            </tr>
                            <tr>
                                <th>Город</th>
                                <td><?php echo htmlspecialchars($last_order['nova_poshta_city_display']); ?></td>
                            </tr>
                            <tr>
                                <th>Адрес доставки</th>
                                <td>
                                    <?php 
                                    if ($last_order['nova_poshta_warehouse_display']) {
                                        echo htmlspecialchars($last_order['nova_poshta_warehouse_display']);
                                    } elseif ($last_order['nova_poshta_street_display'] && $last_order['building_number']) {
                                        echo htmlspecialchars($last_order['nova_poshta_street_display'] . ', ' . $last_order['building_number']);
                                    } else {
                                        echo 'Не указан';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Регион</th>
                                <td><?php echo htmlspecialchars($last_order['region'] ?? 'Не указан'); ?></td>
                            </tr>
                            <tr>
                                <th>Стоимость товаров</th>
                                <td>
                                    <?php
                                    $items_total = $last_order['total_cost'] - ($last_order['delivery_cost'] ?? 0);
                                    echo number_format($items_total, 2) . ' ' . $currency_symbol;
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Стоимость доставки</th>
                                <td><?php echo number_format($last_order['delivery_cost'] ?? 0, 2) . ' ' . $currency_symbol; ?></td>
                            </tr>
                            <tr>
                                <th>Итого</th>
                                <td><?php echo number_format($last_order['total_cost'], 2) . ' ' . $currency_symbol; ?></td>
                            </tr>
                            <tr>
                                <th>Способ доставки</th>
                                <td><?php echo htmlspecialchars($last_order['delivery_method'] ?? 'Не указан'); ?></td>
                            </tr>
                            <tr>
                                <th>Способ оплаты</th>
                                <td><?php echo htmlspecialchars($last_order['payment_method'] ?? 'Не указан'); ?></td>
                            </tr>
                            <tr>
                                <th>Статус</th>
                                <td><span class="badge bg-warning"><?php echo htmlspecialchars($last_order['status']); ?></span></td>
                            </tr>
                        </table>
                    </div>

                    <div class="text-center">
                        <a href="/" class="btn btn-success btn-lg">
                            <i class="fas fa-home me-2"></i>Вернуться на главную
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-center py-3">
            <p class="mb-0 text-muted">Если у вас есть вопросы, свяжитесь с нами: <?php echo htmlspecialchars($shop_settings['shop_email'] ?? 'support@example.com'); ?></p>
        </div>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>

<style>
.order-success-container {
    max-width: 800px;
}
.card {
    border-radius: 15px;
    overflow: hidden;
}
.card-header {
    background: linear-gradient(135deg, #28a745, #218838);
}
.invoice-header {
    border-bottom: 2px solid #28a745;
    padding-bottom: 15px;
}
.table th {
    background-color: #f8f9fa;
    width: 30%;
}
.btn-success {
    background: #28a745;
    border: none;
    padding: 12px 30px;
    border-radius: 25px;
    transition: all 0.3s ease;
}
.btn-success:hover {
    background: #218838;
    transform: translateY(-2px);
}
.card-footer {
    background-color: #f8f9fa;
}
</style>