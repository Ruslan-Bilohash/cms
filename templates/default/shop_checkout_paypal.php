<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';

// Получение ID заказа
$order_id = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);
if (!$order_id) {
    echo '<div class="container my-5"><div class="alert alert-danger">Ошибка: ID заказа не указан.</div></div>';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php';
    exit;
}

// Получение суммы заказа
$order_stmt = $conn->prepare("SELECT total_cost FROM shop_orders WHERE id = ? AND status = 'ожидает'");
$order_stmt->bind_param('i', $order_id);
$order_stmt->execute();
$order = $order_stmt->get_result()->fetch_assoc();
$order_stmt->close();

if (!$order) {
    echo '<div class="container my-5"><div class="alert alert-danger">Ошибка: Заказ не найден или уже обработан.</div></div>';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php';
    exit;
}

$order_amount = $order['total_cost'];

// Получение настроек PayPal
$stmt = $conn->prepare("SELECT api_key, payer_type, is_active FROM api WHERE api_type = 'paypal'");
$stmt->execute();
$paypal_settings = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$paypal_settings['is_active']) {
    echo '<div class="container my-5"><div class="alert alert-danger">Оплата через PayPal временно недоступна.</div></div>';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php';
    exit;
}

$client_id = $paypal_settings['api_key'];
$mode = $paypal_settings['payer_type'] === 'live' ? 'www.paypal.com' : 'www.sandbox.paypal.com';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оплата через PayPal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; font-family: 'Arial', sans-serif; }
        .paypal-container { max-width: 600px; margin: 2rem auto; padding: 2rem; background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #343a40; }
    </style>
</head>
<body>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header_shop.php'; ?>

<div class="container my-5">
    <div class="paypal-container">
        <h2 class="mb-4">Оплата заказа #<?php echo htmlspecialchars($order_id); ?></h2>
        <p class="text-center">Сумма к оплате: <?php echo number_format($order_amount, 2); ?> USD</p>
        <div id="paypal-button-container"></div>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>
<script src="https://<?php echo $mode; ?>/sdk/js?client-id=<?php echo htmlspecialchars($client_id); ?>&currency=USD"></script>
<script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            return fetch('/api/paypal.php?action=create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    amount: '<?php echo $order_amount; ?>',
                    order_id: '<?php echo $order_id; ?>'
                })
            }).then(response => response.json()).then(order => order.id);
        },
        onApprove: function(data, actions) {
            return fetch('/api/paypal.php?action=capture', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: data.orderID,
                    shop_order_id: '<?php echo $order_id; ?>'
                })
            }).then(response => response.json()).then(result => {
                if (result.status === 'success') {
                    window.location.href = '/order-success.php?order_id=<?php echo $order_id; ?>';
                } else {
                    alert('Ошибка оплаты. Попробуйте снова.');
                }
            });
        },
        onError: function(err) {
            console.error('PayPal error:', err);
            alert('Произошла ошибка при обработке платежа.');
        }
    }).render('#paypal-button-container');
</script>
</body>
</html>