<?php
// Подключение шапки
require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header_shop.php';
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Відстеження посилки - Нова Пошта</title>
	<meta name="description" content="Відстежуйте ваші посилки Нової Пошти швидко та зручно!">
<meta name="keywords" content="Нова Пошта, трекінг, відстеження посилок">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome для иконок -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
            padding: 20px;
        }
        .tracking-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            max-width: 800px;
            margin: 40px auto;
            animation: fadeIn 0.5s ease-in;
        }
        .tracking-header {
            color: #ff6200;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .tracking-image {
            display: block;
            max-width: 200px;
            margin: 15px auto;
            border-radius: 10px;
        }
        .form-control {
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 1.2rem;
            padding: 15px;
            height: 60px;
        }
        .form-control:focus {
            box-shadow: 0 0 10px rgba(255, 98, 0, 0.3);
            border-color: #ff6200;
        }
        .btn-primary {
            background: #ff6200;
            border: none;
            font-size: 1.2rem;
            padding: 15px;
            border-radius: 12px;
        }
        .btn-primary:hover {
            background: #e55a00;
            transform: translateY(-2px);
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 10px;
            background: #f8f9fa;
            display: none;
        }
        .error {
            color: #dc3545;
            font-weight: bold;
        }
        .success {
            color: #28a745;
            font-weight: bold;
        }
        .status-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 1.1rem;
        }
        .status-item:last-child {
            border-bottom: none;
        }
        .loading {
            text-align: center;
            font-style: italic;
            color: #6c757d;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 576px) {
            .tracking-container {
                padding: 15px;
                margin: 20px auto;
            }
            .tracking-header {
                font-size: 1.5rem;
            }
            .form-control {
                font-size: 1rem;
                height: 50px;
                padding: 12px;
            }
            .btn-primary {
                font-size: 1rem;
                padding: 12px;
            }
            .tracking-image {
                max-width: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="tracking-container">
        <h1 class="tracking-header"><i class="fa-solid fa-box me-2"></i> Відстежити посилку</h1>
        <img src="/templates/default/img/newpost.webp" alt="Нова Пошта" class="tracking-image">
        <form id="trackingForm">
            <div class="mb-4">
                <label for="documentNumber" class="form-label"><i class="fa-solid fa-barcode me-2"></i> Номер накладної (ТТН)</label>
                <input type="text" class="form-control" id="documentNumber" placeholder="Введіть номер накладної" required>
            </div>
            <div class="mb-4">
                <label for="phone" class="form-label"><i class="fa-solid fa-phone me-2"></i> Номер телефону (опціонально)</label>
                <input type="text" class="form-control" id="phone" placeholder="Наприклад, 380600000000">
            </div>
            <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-magnifying-glass me-2"></i> Відстежити</button>
        </form>
        <div id="result" class="result"></div>
    </div>

    <!-- Bootstrap JS и Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('trackingForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            
            const documentNumber = document.getElementById('documentNumber').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const resultDiv = document.getElementById('result');
            
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<div class="loading"><i class="fa-solid fa-spinner fa-spin me-2"></i> Завантаження...</div>';

            try {
                const response = await fetch('/api/newpost_traking.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ documentNumber, phone })
                });

                const data = await response.json();

                if (data.success && data.data.length > 0) {
                    const tracking = data.data[0];
                    resultDiv.innerHTML = `
                        <div class="success"><i class="fa-solid fa-check-circle me-2"></i> Посилку знайдено!</div>
                        <div class="status-item"><strong><i class="fa-solid fa-info-circle me-2"></i> Статус:</strong> ${tracking.Status || 'Немає даних'}</div>
                        <div class="status-item"><strong><i class="fa-solid fa-city me-2"></i> Місто відправника:</strong> ${tracking.CitySender || 'Немає даних'}</div>
                        <div class="status-item"><strong><i class="fa-solid fa-city me-2"></i> Місто отримувача:</strong> ${tracking.CityRecipient || 'Немає даних'}</div>
                        <div class="status-item"><strong><i class="fa-solid fa-warehouse me-2"></i> Відділення отримувача:</strong> ${tracking.WarehouseRecipient || 'Немає даних'}</div>
                        <div class="status-item"><strong><i class="fa-solid fa-calendar-alt me-2"></i> Дата створення:</strong> ${tracking.DateCreated || 'Немає даних'}</div>
                        <div class="status-item"><strong><i class="fa-solid fa-truck me-2"></i> Очікувана доставка:</strong> ${tracking.ScheduledDeliveryDate || 'Немає даних'}</div>
                        ${tracking.RecipientFullName ? `<div class="status-item"><strong><i class="fa-solid fa-user me-2"></i> Отримувач:</strong> ${tracking.RecipientFullName}</div>` : ''}
                    `;
                } else {
                    resultDiv.innerHTML = '<div class="error"><i class="fa-solid fa-exclamation-triangle me-2"></i> Посилку не знайдено або виникла помилка.</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = '<div class="error"><i class="fa-solid fa-exclamation-triangle me-2"></i> Помилка зв’язку з сервером.</div>';
            }
        });
    </script>

<?php
// Подключение подвала
require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer_shop_contact.php';
?>
</body>
</html>