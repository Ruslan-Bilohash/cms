<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

$shop_settings = require_once $_SERVER['DOCUMENT_ROOT'] . '/uploads/shop_settings.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Method not allowed']);
    http_response_code(405);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$validationURL = filter_var($input['validationURL'] ?? '', FILTER_VALIDATE_URL);

if (!$validationURL || !$shop_settings['payment_methods']['apple_pay']['enabled']) {
    echo json_encode(['error' => 'Invalid request or Apple Pay disabled']);
    http_response_code(400);
    exit;
}

$merchantId = $shop_settings['payment_methods']['apple_pay']['merchant_id'] ?? '';
if (empty($merchantId)) {
    echo json_encode(['error' => 'Merchant ID not configured']);
    http_response_code(500);
    exit;
}

// Реальная валидация через Apple Pay API
$ch = curl_init($validationURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'merchantIdentifier' => $merchantId,
    'domainName' => $_SERVER['HTTP_HOST'],
    'displayName' => $shop_settings['shop_name'] ?? 'Your Shop Name'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Включаем проверку SSL

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    echo json_encode(['error' => 'Failed to validate merchant']);
    http_response_code(500);
    exit;
}

echo $response;