<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

clearstatcache();

// Проверка существования магазина
$shop_enabled = true;
if (!$shop_enabled) {
    die('<div class="alert alert-warning">Магазин отключён!</div>');
}

$product_id = filter_input(INPUT_GET, 'product_id', FILTER_VALIDATE_INT);
if (!$product_id) {
    header("Location: /shop_category.php");
    exit;
}

$product_stmt = $conn->prepare("SELECT id, name, price, category_id FROM shop_products WHERE id = ? AND status = 'active'");
$product_stmt->bind_param('i', $product_id);
$product_stmt->execute();
$product = $product_stmt->get_result()->fetch_assoc();
$product_stmt->close();

if (!$product) {
    echo '<div class="container my-5"><div class="alert alert-danger">Товар не найден.</div></div>';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php';
    exit;
}

// Получаем методы доставки
$delivery_methods = $conn->query("SELECT * FROM shop_delivery_methods WHERE is_enabled = 1 ORDER BY sort_order ASC")->fetch_all(MYSQLI_ASSOC);

$nova_poshta_enabled = true;
if ($nova_poshta_enabled) {
    $delivery_methods[] = [
        'id' => 'nova_poshta',
        'name' => 'Новая Почта',
        'cost' => 0.00,
        'regions' => '',
        'is_nova_poshta' => 1
    ];
}

if (empty($delivery_methods)) {
    echo '<div class="container my-5"><div class="alert alert-warning">Нет доступных методов доставки.</div></div>';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php';
    exit;
}

// Получаем методы оплаты
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

error_log("shop_checkout.php: Payment methods loaded: " . json_encode($payment_methods));

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $customer_name = $conn->real_escape_string(trim($_POST['customer_name']));
    $customer_phone = $conn->real_escape_string(trim($_POST['customer_phone']));
    $delivery_method_id = $_POST['delivery_method'];
    $payment_method = $conn->real_escape_string(trim($_POST['payment_method']));
    $region = $conn->real_escape_string(trim($_POST['region']));

    if ($delivery_method_id === 'nova_poshta') {
        header("Location: /templates/default/nova_poshta_checkout.php?product_id=$product_id&delivery_method=nova_poshta");
        exit;
    }

    if (empty($customer_name) || empty($customer_phone) || empty($region)) {
        $message = '<div class="alert alert-danger">Заполните все обязательные поля (имя, телефон, регион).</div>';
    } else {
        $delivery_stmt = $conn->prepare("SELECT name, cost FROM shop_delivery_methods WHERE id = ? AND is_enabled = 1");
        $delivery_stmt->bind_param('i', $delivery_method_id);
        $delivery_stmt->execute();
        $delivery = $delivery_stmt->get_result()->fetch_assoc();
        $delivery_stmt->close();

        if (!$delivery) {
            $message = '<div class="alert alert-danger">Выбранный метод доставки недоступен.</div>';
        } else {
            $total_cost = $product['price'] + $delivery['cost'];
            $order_number = 'ORD-' . time();
            $status = 'ожидает';
            $currency_code = 'UAH'; // Предполагаем, можно взять из настроек

            $stmt = $conn->prepare("
                INSERT INTO shop_orders (product_id, category_id, order_number, customer_name, customer_phone, 
                    delivery_method, payment_method, total_cost, status, created_at, region, currency_code) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)
            ");
            $stmt->bind_param('iisssssdsss', $product['id'], $product['category_id'], $order_number, $customer_name, 
                $customer_phone, $delivery['name'], $payment_method, $total_cost, $status, $region, $currency_code);
            
            if ($stmt->execute()) {
                $order_id = $conn->insert_id;
                if ($payment_method === 'Paypal') {
                    // Проверка настроек PayPal
                    $paypal_index = array_search('Paypal', array_column($payment_methods, 'name'));
                    if ($paypal_index !== false && !empty($payment_methods[$paypal_index]['api_key'])) {
                        header("Location: /templates/default/shop_checkout_paypal.php?order_id=$order_id");
                        exit;
                    } else {
                        $message = '<div class="alert alert-danger">PayPal не настроен. Выберите другой метод оплаты.</div>';
                    }
                } elseif ($payment_method === 'Apple Pay' || $payment_method === 'Google Pay') {
                    header("Location: /payment_process.php?order_id=$order_id");
                    exit;
                } else {
                    header('Location: /shop_success');
                    exit;
                }
            } else {
                $message = '<div class="alert alert-danger">Ошибка: ' . $stmt->error . '</div>';
            }
            $stmt->close();
        }
    }
}

$currency_code = 'UAH';
$currency_symbol = ['EUR' => '€', 'UAH' => '₴', 'RUB' => '₽', 'USD' => '$'][$currency_code] ?? '₴';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа - <?php echo htmlspecialchars($product['name']); ?></title>
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
        .hidden { display: none !important; }
        @media (max-width: 576px) {
            .checkout-container { padding: 1rem; margin: 1rem; }
            .btn-submit { padding: 0.5rem 1rem; }
        }
    </style>
</head>
<body>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header_shop.php'; ?>

<div class="container my-5">
    <div class="checkout-container">
        <h2 class="text-center mb-4"><i class="fas fa-shopping-cart me-2"></i>Оформление заказа</h2>

        <?php if (empty($payment_methods)): ?>
            <div class="alert alert-warning">Нет активных методов оплаты.</div>
        <?php endif; ?>

        <?php if (isset($message)): ?>
            <?php echo $message; ?>
        <?php else: ?>
            <div class="product-info">
                <h5><i class="fas fa-box-open me-2"></i><?php echo htmlspecialchars($product['name']); ?></h5>
                <p><i class="fas fa-money-bill-wave me-2"></i>Цена: <?php echo number_format($product['price'], 2) . ' ' . $currency_symbol; ?></p>
                <p>Доставка: <span id="delivery-cost">0.00 <?php echo $currency_symbol; ?></span></p>
                <p>Итого: <span id="total-cost"><?php echo number_format($product['price'], 2) . ' ' . $currency_symbol; ?></span></p>
            </div>

            <form method="POST" id="checkout-form">
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-truck me-2"></i>Способ доставки</label>
                    <select name="delivery_method" id="delivery_method" class="form-select" required>
                        <option value="">Выберите способ доставки</option>
                        <?php foreach ($delivery_methods as $method): ?>
                            <option value="<?php echo htmlspecialchars($method['id']); ?>" 
                                    data-cost="<?php echo $method['cost']; ?>" 
                                    data-regions="<?php echo htmlspecialchars($method['regions']); ?>"
                                    data-is-nova-poshta="<?php echo isset($method['is_nova_poshta']) ? $method['is_nova_poshta'] : 0; ?>">
                                <?php 
                                echo htmlspecialchars($method['name']); 
                                echo $method['id'] === 'nova_poshta' ? ' (Рассчитывается при оформлении)' : ' (' . number_format($method['cost'], 2) . ' ' . $currency_symbol . ')'; 
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="customer-info" class="hidden">
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-user me-2"></i>Ваше имя</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-phone me-2"></i>Телефон</label>
                        <input type="tel" name="customer_phone" id="customer_phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-map-marker-alt me-2"></i>Регион</label>
                        <input type="text" name="region" id="region" class="form-control" placeholder="Введите ваш регион" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fas fa-credit-card me-2"></i>Способ оплаты</label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="">Выберите способ оплаты</option>
                            <?php foreach ($payment_methods as $method): ?>
                                <option value="<?php echo htmlspecialchars($method['name']); ?>">
                                    <?php echo htmlspecialchars($method['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="place_order" class="btn btn-submit w-100" id="submitButton"><i class="fas fa-check me-2"></i>Оформить заказ</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://pay.google.com/gp/p/js/pay.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deliverySelect = document.getElementById('delivery_method');
    const customerInfo = document.getElementById('customer-info');
    const paymentMethodSelect = document.getElementById('payment_method');
    const submitButton = document.getElementById('submitButton');

    customerInfo.classList.add('hidden');

    deliverySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const deliveryCost = parseFloat(selectedOption.dataset.cost) || 0;
        const isNovaPoshta = selectedOption.value === 'nova_poshta';
        const productPrice = <?php echo $product['price']; ?>;
        const totalCost = productPrice + deliveryCost;

        document.getElementById('delivery-cost').textContent = isNovaPoshta ? 'Рассчитывается при оформлении' : deliveryCost.toFixed(2) + ' <?php echo $currency_symbol; ?>';
        document.getElementById('total-cost').textContent = isNovaPoshta ? productPrice.toFixed(2) + ' <?php echo $currency_symbol; ?> + доставка' : totalCost.toFixed(2) + ' <?php echo $currency_symbol; ?>';

        if (isNovaPoshta) {
            window.location.href = '/templates/default/nova_poshta_checkout.php?product_id=<?php echo $product_id; ?>&delivery_method=nova_poshta';
        } else if (selectedOption.value) {
            customerInfo.classList.remove('hidden');
        } else {
            customerInfo.classList.add('hidden');
        }
    });

    document.getElementById('region').addEventListener('input', function() {
        const region = this.value.toLowerCase().trim();
        Array.from(deliverySelect.options).forEach(option => {
            if (option.value === '') return;
            const regions = option.dataset.regions.toLowerCase();
            option.style.display = (regions === '' || regions.split(',').some(r => r.trim() === region)) ? '' : 'none';
        });
        if (deliverySelect.selectedIndex > 0 && deliverySelect.options[deliverySelect.selectedIndex].style.display === 'none') {
            deliverySelect.selectedIndex = 0;
            document.getElementById('delivery-cost').textContent = '0.00 <?php echo $currency_symbol; ?>';
            document.getElementById('total-cost').textContent = '<?php echo number_format($product['price'], 2) . ' ' . $currency_symbol; ?>';
            customerInfo.classList.add('hidden');
        }
    });

    function updateTotalCost() {
        const selectedOption = deliverySelect.options[deliverySelect.selectedIndex];
        const deliveryCost = selectedOption ? parseFloat(selectedOption.dataset.cost) || 0 : 0;
        return <?php echo $product['price']; ?> + deliveryCost;
    }

    const googlePayConfig = {
        apiVersion: 2,
        apiVersionMinor: 0,
        allowedPaymentMethods: [{
            type: 'CARD',
            parameters: { 
                allowedAuthMethods: ['PAN_ONLY', 'CRYPTOGRAM_3DS'], 
                allowedCardNetworks: ['MASTERCARD', 'VISA'] 
            },
            tokenizationSpecification: { 
                type: 'PAYMENT_GATEWAY', 
                parameters: { 
                    gateway: 'example', 
                    gatewayMerchantId: '<?php echo isset($payment_methods[array_search('Google Pay', array_column($payment_methods, 'name'))]) ? $payment_methods[array_search('Google Pay', array_column($payment_methods, 'name'))]['merchant_id'] : ''; ?>' 
                } 
            }
        }],
        merchantInfo: { 
            merchantId: '<?php echo isset($payment_methods[array_search('Google Pay', array_column($payment_methods, 'name'))]) ? $payment_methods[array_search('Google Pay', array_column($payment_methods, 'name'))]['merchant_id'] : ''; ?>', 
            merchantName: 'Your Shop Name' 
        },
        transactionInfo: { 
            totalPriceStatus: 'FINAL', 
            totalPrice: updateTotalCost().toString(), 
            currencyCode: '<?php echo $currency_code; ?>', 
            countryCode: 'UA' 
        }
    };

    paymentMethodSelect.addEventListener('change', function() {
        googlePayConfig.transactionInfo.totalPrice = updateTotalCost().toString();

        if (this.value === 'Google Pay' && <?php echo json_encode(in_array('Google Pay', array_column($payment_methods, 'name'))); ?>) {
            const googlePayClient = new google.payments.api.PaymentsClient({ 
                environment: '<?php echo isset($payment_methods[array_search('Google Pay', array_column($payment_methods, 'name'))]) ? $payment_methods[array_search('Google Pay', array_column($payment_methods, 'name'))]['environment'] : 'TEST'; ?>' 
            });
            googlePayClient.isReadyToPay({ 
                apiVersion: 2, 
                apiVersionMinor: 0, 
                allowedPaymentMethods: googlePayConfig.allowedPaymentMethods 
            })
            .then(response => {
                if (response.result) {
                    submitButton.style.display = 'none';
                    googlePayClient.loadPaymentData(googlePayConfig)
                        .then(paymentData => {
                            document.getElementById('checkout-form').submit();
                        })
                        .catch(err => console.error('Google Pay Error:', err));
                }
            })
            .catch(err => console.error('Google Pay Init Error:', err));
        } else if (this.value === 'Apple Pay' && window.ApplePaySession && <?php echo json_encode(in_array('Apple Pay', array_column($payment_methods, 'name'))); ?>) {
            if (ApplePaySession.canMakePayments()) {
                const paymentRequest = {
                    countryCode: 'UA',
                    currencyCode: '<?php echo $currency_code; ?>',
                    supportedNetworks: ['visa', 'masterCard'],
                    merchantCapabilities: ['supports3DS'],
                    total: { 
                        label: 'Your Shop Name', 
                        amount: updateTotalCost().toString() 
                    }
                };
                const session = new ApplePaySession(3, paymentRequest);
                session.onvalidatemerchant = event => {
                    fetch('/includes/validate_apple_pay.php', { 
                        method: 'POST', 
                        body: JSON.stringify({ validationURL: event.validationURL }) 
                    })
                    .then(response => response.json())
                    .then(merchantSession => session.completeMerchantValidation(merchantSession))
                    .catch(err => console.error('Apple Pay Validation Error:', err));
                };
                session.onpaymentauthorized = event => {
                    session.completePayment(ApplePaySession.STATUS_SUCCESS);
                    document.getElementById('checkout-form').submit();
                };
                session.begin();
            } else {
                alert('Apple Pay недоступен на этом устройстве.');
            }
        } else {
            submitButton.style.display = 'block';
        }
    });
});
</script>
</body>
</html>