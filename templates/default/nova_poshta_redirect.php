<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/novaya_pochta.php';

$np = new NovaPoshtaAPI();
$cities_response = $np->getCities();
$cities = $cities_response['data'] ?? [];
$api_error = $cities_response['error'] ?? null;

// Получение настроек из админ-панели
$api_settings = $conn->query("SELECT payment_method, service_type_default FROM api WHERE api_type = 'nova_poshta' AND is_active = 1 LIMIT 1")->fetch_assoc();
$np_payment_method = $api_settings['payment_method'] ?? 'Cash';
$np_service_type = $api_settings['service_type_default'] ?? 'WarehouseWarehouse';

$redirect_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_redirect'])) {
    $int_doc_number = $conn->real_escape_string(trim($_POST['int_doc_number']));
    $last_name = $conn->real_escape_string(trim($_POST['last_name']));
    $first_name = $conn->real_escape_string(trim($_POST['first_name']));
    $patronymic = $conn->real_escape_string(trim($_POST['patronymic']));
    $recipient_phone = $conn->real_escape_string(trim($_POST['recipient_phone']));
    $recipient_city_ref = $conn->real_escape_string(trim($_POST['recipient_city']));
    $recipient_warehouse = $conn->real_escape_string(trim($_POST['recipient_warehouse'] ?? ''));
    $recipient_street = $conn->real_escape_string(trim($_POST['recipient_street'] ?? ''));
    $building_number = $conn->real_escape_string(trim($_POST['building_number'] ?? ''));
    $note_address = $conn->real_escape_string(trim($_POST['note_address'] ?? ''));
    $service_type = $conn->real_escape_string(trim($_POST['service_type']));

    $params = [
        'IntDocNumber' => $int_doc_number,
        'RecipientContactName' => "$last_name $first_name $patronymic",
        'RecipientPhone' => $recipient_phone,
        'RecipientSettlement' => $recipient_city_ref,
        'RecipientWarehouse' => $service_type === 'WarehouseWarehouse' ? $recipient_warehouse : '',
        'RecipientSettlementStreet' => $service_type === 'DoorsWarehouse' ? $recipient_street : '',
        'BuildingNumber' => $service_type === 'DoorsWarehouse' ? $building_number : '',
        'NoteAddressRecipient' => $note_address,
        'ServiceType' => $service_type,
        'OrderType' => 'orderRedirecting',
        'PaymentMethod' => $np_payment_method,
    ];

    $redirect_result = $np->createRedirectRequest($params);
    if ($redirect_result['success'] && !empty($redirect_result['data'])) {
        $redirect_message = '<div class="alert alert-success">Заявка на переадресацию создана! Номер: ' . htmlspecialchars($redirect_result['data'][0]['Number']) . '</div>';
    } else {
        $redirect_message = '<div class="alert alert-danger">Ошибка создания заявки: ' . htmlspecialchars($redirect_result['errors'][0] ?? 'Неизвестная ошибка') . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Переадресация отправления - Новая Почта</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f5f5f5; font-family: 'Arial', sans-serif; color: #333; }
        .redirect-container { background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); max-width: 600px; margin: 2rem auto; }
        .form-label { font-weight: 600; color: #343a40; }
        .form-control, .form-select { border-radius: 10px; padding: 10px; }
        .btn-submit { background: linear-gradient(90deg, #28a745, #34c759); border: none; border-radius: 25px; padding: 0.75rem 2rem; color: white; }
        .btn-submit:hover { background: linear-gradient(90deg, #218838, #2ecc71); }
        .d-none { display: none; }
        @media (max-width: 576px) {
            .redirect-container { padding: 1rem; margin: 1rem; }
            .btn-submit { padding: 0.5rem 1rem; }
        }
    </style>
</head>
<body>
<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header_shop.php'; ?>

<div class="container my-5">
    <div class="redirect-container">
        <h2 class="text-center mb-4"><i class="fas fa-route me-2"></i>Переадресация отправления</h2>

        <?php if ($api_error): ?>
            <div class="alert alert-danger">Ошибка API: <?php echo htmlspecialchars($api_error); ?></div>
        <?php elseif (empty($cities)): ?>
            <div class="alert alert-danger">Не удалось загрузить города. Проверьте API-ключ.</div>
        <?php else: ?>
            <?php if ($redirect_message): ?>
                <?php echo $redirect_message; ?>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-barcode me-2"></i>Номер ТТН</label>
                    <input type="text" name="int_doc_number" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-user me-2"></i>Фамилия</label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-user me-2"></i>Имя</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-user me-2"></i>Отчество</label>
                    <input type="text" name="patronymic" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-phone me-2"></i>Телефон</label>
                    <input type="tel" name="recipient_phone" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-city me-2"></i>Город</label>
                    <select name="recipient_city" id="recipient_city" class="form-select" required>
                        <option value="">Выберите город</option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?php echo htmlspecialchars($city['Ref']); ?>">
                                <?php echo htmlspecialchars($city['Description']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-box me-2"></i>Тип доставки</label>
                    <select name="service_type" id="service_type" class="form-select" required>
                        <option value="WarehouseWarehouse" <?php echo $np_service_type === 'WarehouseWarehouse' ? 'selected' : ''; ?>>Отделение</option>
                        <option value="DoorsWarehouse" <?php echo $np_service_type === 'DoorsWarehouse' ? 'selected' : ''; ?>>Адрес</option>
                    </select>
                </div>
                <div class="mb-3" id="warehouse_field" <?php echo $np_service_type === 'DoorsWarehouse' ? 'class="d-none"' : ''; ?>>
                    <label class="form-label"><i class="fas fa-warehouse me-2"></i>Отделение</label>
                    <select name="recipient_warehouse" id="recipient_warehouse" class="form-select">
                        <option value="">Выберите отделение</option>
                    </select>
                </div>
                <div class="mb-3" id="address_field" <?php echo $np_service_type === 'WarehouseWarehouse' ? 'class="d-none"' : ''; ?>>
                    <label class="form-label"><i class="fas fa-road me-2"></i>Улица</label>
                    <select name="recipient_street" id="recipient_street" class="form-select">
                        <option value="">Выберите улицу</option>
                    </select>
                </div>
                <div class="mb-3" id="building_field" <?php echo $np_service_type === 'WarehouseWarehouse' ? 'class="d-none"' : ''; ?>>
                    <label class="form-label"><i class="fas fa-home me-2"></i>Номер дома</label>
                    <input type="text" name="building_number" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fas fa-comment me-2"></i>Комментарий к адресу</label>
                    <input type="text" name="note_address" class="form-control">
                </div>
                <button type="submit" name="request_redirect" class="btn btn-submit w-100"><i class="fas fa-route me-2"></i>Надіслати заявку на переадресацию</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('recipient_city').addEventListener('change', function() {
    const cityRef = this.value;
    const warehouseSelect = document.getElementById('recipient_warehouse');
    const streetSelect = document.getElementById('recipient_street');
    warehouseSelect.innerHTML = '<option value="">Выберите отделение</option>';
    streetSelect.innerHTML = '<option value="">Выберите улицу</option>';

    if (cityRef) {
        // Получение отделений
        fetch('/api/novaya_pochta_warehouses.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ city_ref: cityRef })
        })
        .then(response => response.json())
        .then(data => {
            if (data.data) {
                data.data.forEach(warehouse => {
                    const option = document.createElement('option');
                    option.value = warehouse.Ref;
                    option.textContent = warehouse.Description;
                    warehouseSelect.appendChild(option);
                });
            }
        });

        // Получение улиц
        fetch('/api/novaya_pochta_streets.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ city_ref: cityRef })
        })
        .then(response => response.json())
        .then(data => {
            if (data.data) {
                data.data.forEach(street => {
                    const option = document.createElement('option');
                    option.value = street.Ref;
                    option.textContent = street.Description;
                    streetSelect.appendChild(option);
                });
            }
        });
    }
});

document.getElementById('service_type').addEventListener('change', function() {
    const warehouseField = document.getElementById('warehouse_field');
    const addressField = document.getElementById('address_field');
    const buildingField = document.getElementById('building_field');

    if (this.value === 'WarehouseWarehouse') {
        warehouseField.classList.remove('d-none');
        addressField.classList.add('d-none');
        buildingField.classList.add('d-none');
    } else {
        warehouseField.classList.add('d-none');
        addressField.classList.remove('d-none');
        buildingField.classList.remove('d-none');
    }
});
</script>
</body>
</html>