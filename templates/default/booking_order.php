<?php
// templates/default/booking_order.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';

// Запускаем сессию (только здесь и в booking.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Определяем язык
$default_lang = 'ru';
$lang_code = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : $default_lang);
if (!in_array($lang_code, ['ru', 'en', 'ua', 'no', 'lt'])) {
    $lang_code = $default_lang;
}
$_SESSION['lang'] = $lang_code;

// Загружаем переводы
$lang_file = $_SERVER['DOCUMENT_ROOT'] . "/templates/default/lang/booking_{$lang_code}.php";
$lang = file_exists($lang_file) ? include $lang_file : [];

// Настройки
$settings_file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/booking_settings.php';
$settings = file_exists($settings_file) ? include $settings_file : [
    'currency' => 'UAH',
    'min_price' => 50,
    'max_price' => 5000
];
$currency = $settings['currency'];

// Получаем номер
$room_id = (int)($_GET['room_id'] ?? 0);
$stmt = $conn->prepare("SELECT r.*, c.name AS category_name FROM rooms r LEFT JOIN booking_categories c ON r.category_id = c.id WHERE r.id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$room) {
    die($lang['error_room_not_found'] ?? "Номер не найден.");
}

// Обработка формы
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $guests = (int)($_POST['guests'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    $check_in_date = DateTime::createFromFormat('Y-m-d', $check_in);
    $check_out_date = DateTime::createFromFormat('Y-m-d', $check_out);

    if (!$check_in_date || !$check_out_date || $check_out_date <= $check_in_date) {
        $message = $lang['error_invalid_dates'] ?? "Ошибка: Неверные даты заезда или выезда.";
    } elseif ($guests > $room['capacity']) {
        $message = $lang['error_guests_exceed'] ?? "Ошибка: Количество гостей превышает вместимость номера.";
    } elseif (empty($name)) {
        $message = $lang['error_name_required'] ?? "Ошибка: Укажите ваше имя.";
    } elseif (empty($phone) || !preg_match('/^\+?[0-9]{10,15}$/', $phone)) {
        $message = $lang['error_phone_invalid'] ?? "Ошибка: Укажите корректный номер телефона.";
    } else {
        $stmt = $conn->prepare("INSERT INTO bookings (room_id, check_in, check_out, guests, name, phone, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("ississ", $room_id, $check_in, $check_out, $guests, $name, $phone);
        if ($stmt->execute()) {
            $message = $lang['success_booking'] ?? "Бронирование успешно отправлено! Ожидайте подтверждения.";
        } else {
            $message = $lang['error_booking'] ?? "Ошибка при бронировании: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Функция изображений
function getImages($imageJson) {
    $defaultImage = ['/uploads/booking/default_room.webp'];
    if (empty($imageJson)) return $defaultImage;
    $images = json_decode($imageJson, true);
    if (!is_array($images) || empty($images)) return $defaultImage;
    $validImages = array_filter($images, function($path) {
        return strpos($path, '/uploads/booking/') === 0 && file_exists($_SERVER['DOCUMENT_ROOT'] . $path);
    });
    return !empty($validImages) ? $validImages : $defaultImage;
}

// Другие доступные номера
$free_rooms = $conn->query("SELECT * FROM rooms WHERE status = 'available' AND id != $room_id LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?php echo $lang_code; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['title_order'] ?? 'Оформлення бронювання'; ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #1a2980;
            --primary-dark: #0f1a5a;
            --accent: #00d4ff;
            --accent-dark: #00a0cc;
            --light: #f8f9ff;
            --dark: #0a0f2c;
            --gray: #64748b;
            --success: #10b981;
            --error: #ef4444;
            --glass: rgba(255,255,255,0.08);
            --shadow: 0 25px 50px -12px rgba(0,0,0,0.4);
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #e2e8f0;
            min-height: 100vh;
            background-attachment: fixed;
        }

        .container { max-width: 1280px; margin: 0 auto; padding: 0 1.5rem; }

        .header {
            padding: 5rem 0 3rem;
            text-align: center;
        }

        .header h1 {
            font-size: clamp(2.5rem, 5vw, 3.8rem);
            background: linear-gradient(90deg, #00d4ff, #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .room-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
            background: var(--glass);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .room-gallery {
            height: 320px;
            position: relative;
            overflow: hidden;
            border-radius: 16px;
        }

        .gallery-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .room-gallery:hover img {
            transform: scale(1.08);
        }

        .booking-form {
            background: var(--glass);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow);
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input, .form-group select {
            width: 100%;
            padding: 1rem 1rem 1rem 2.5rem;
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            color: white;
            font-size: 1rem;
        }

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(0,212,255,0.2);
        }

        .form-group i {
            position: absolute;
            left: 1rem;
            top: 2.6rem;
            color: var(--accent);
        }

        .btn {
            width: 100%;
            padding: 1.2rem;
            background: var(--accent);
            color: #0f172a;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: var(--accent-dark);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,212,255,0.4);
        }

        .alert {
            padding: 1.2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .alert-success { background: rgba(16,185,129,0.2); color: #a7f3d0; }
        .alert-error { background: rgba(239,68,68,0.2); color: #fecaca; }

        .rooms-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 1.8rem;
            margin-top: 3rem;
        }

        .room-card {
            background: var(--glass);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .room-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 30px 60px rgba(0,212,255,0.18);
        }

        .room-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .room-info {
            padding: 1.5rem;
        }

        .room-info h3 {
            color: var(--accent);
            margin-bottom: 0.8rem;
        }

        @media (max-width: 1024px) {
            .room-details { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .rooms-list { grid-template-columns: 1fr; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const gallery = document.querySelector('.room-gallery');
            if (!gallery) return;

            const mainImages = gallery.querySelectorAll('.gallery-main img');
            const thumbs = gallery.querySelectorAll('.gallery-thumbs img');
            const prevMain = gallery.querySelector('.prev');
            const nextMain = gallery.querySelector('.next');
            let current = 0;

            function show(idx) {
                mainImages.forEach(img => img.classList.remove('active'));
                thumbs.forEach(t => t.classList.remove('active'));
                mainImages[idx].classList.add('active');
                thumbs[idx].classList.add('active');
                current = idx;
            }

            thumbs.forEach((thumb, i) => thumb.addEventListener('click', () => show(i)));
            prevMain?.addEventListener('click', () => show(current = (current - 1 + mainImages.length) % mainImages.length));
            nextMain?.addEventListener('click', () => show(current = (current + 1) % mainImages.length));

            show(0);
        });
    </script>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-calendar-check"></i> <?php echo $lang['header_order_title'] ?? 'Оформлення бронювання'; ?></h1>
        <p><?php echo $lang['header_order_subtitle'] ?? 'Забронюйте своє проживання'; ?></p>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert <?php echo strpos($message, 'Ошибка') === false && strpos($message, 'Помилка') === false ? 'alert-success' : 'alert-error'; ?>">
                <i class="fas <?php echo strpos($message, 'Ошибка') === false && strpos($message, 'Помилка') === false ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="room-details">
            <div class="room-gallery">
                <div class="gallery-main">
                    <?php foreach (getImages($room['image']) as $idx => $img): ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" class="<?php echo $idx === 0 ? 'active' : ''; ?>">
                    <?php endforeach; ?>
                </div>
                <div class="gallery-nav">
                    <button class="prev"><i class="fas fa-chevron-left"></i></button>
                    <button class="next"><i class="fas fa-chevron-right"></i></button>
                </div>
                <div class="gallery-thumbs">
                    <?php foreach (getImages($room['image']) as $idx => $img): ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" alt="Thumbnail" class="<?php echo $idx === 0 ? 'active' : ''; ?>">
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <h2><?php echo htmlspecialchars($room['name']); ?></h2>
                <p><i class="fas fa-folder"></i> <?php echo $lang['label_category'] ?? 'Категорія'; ?>: <?php echo htmlspecialchars($room['category_name']); ?></p>
                <p><i class="fas fa-users"></i> <?php echo $lang['label_capacity'] ?? 'Місткість'; ?>: <?php echo htmlspecialchars($room['capacity']); ?> <?php echo $lang['guests'] ?? 'гостей'; ?></p>
                <p><i class="fas fa-money-bill-wave"></i> <?php echo $lang['label_price'] ?? 'Ціна'; ?>: <?php echo htmlspecialchars($room['price']); ?> <?php echo htmlspecialchars($currency); ?>/<?php echo $lang['per_night'] ?? 'ніч'; ?></p>
            </div>
        </div>

        <div class="booking-form">
            <h3><i class="fas fa-edit"></i> <?php echo $lang['section_your_details'] ?? 'Ваші дані'; ?></h3>
            <form method="POST">
                <div class="form-group">
                    <label><?php echo $lang['label_name'] ?? 'Ім’я'; ?>:</label>
                    <i class="fas fa-user"></i>
                    <input type="text" name="name" placeholder="<?php echo $lang['placeholder_name'] ?? 'Введіть ваше ім’я'; ?>" required>
                </div>
                <div class="form-group">
                    <label><?php echo $lang['label_phone'] ?? 'Телефон'; ?>:</label>
                    <i class="fas fa-phone"></i>
                    <input type="tel" name="phone" placeholder="<?php echo $lang['placeholder_phone'] ?? '+380123456789'; ?>" pattern="\+?[0-9]{10,15}" required>
                </div>
                <div class="form-group">
                    <label><?php echo $lang['label_check_in'] ?? 'Дата заїзду'; ?>:</label>
                    <i class="fas fa-calendar-day"></i>
                    <input type="date" name="check_in" required>
                </div>
                <div class="form-group">
                    <label><?php echo $lang['label_check_out'] ?? 'Дата виїзду'; ?>:</label>
                    <i class="fas fa-calendar-day"></i>
                    <input type="date" name="check_out" required>
                </div>
                <div class="form-group">
                    <label><?php echo $lang['label_guests'] ?? 'Кількість гостей'; ?>:</label>
                    <i class="fas fa-users"></i>
                    <select name="guests" required>
                        <?php for ($i = 1; $i <= $room['capacity']; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $lang['guests_suffix'][$i == 1 ? 1 : ($i < 5 ? 2 : 3)] ?? ($i == 1 ? 'гість' : 'гостей'); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" class="btn"><i class="fas fa-calendar-check"></i> <?php echo $lang['btn_book'] ?? 'Забронювати'; ?></button>
            </form>
        </div>

        <div>
            <h3><i class="fas fa-hotel"></i> <?php echo $lang['section_other_rooms'] ?? 'Інші доступні номери'; ?></h3>
            <div class="rooms-list">
                <?php foreach ($free_rooms as $free_room): ?>
                    <div class="room-card">
                        <img src="<?php echo htmlspecialchars(getImages($free_room['image'])[0]); ?>" alt="<?php echo htmlspecialchars($free_room['name']); ?>">
                        <div class="room-info">
                            <h3><?php echo htmlspecialchars($free_room['name']); ?></h3>
                            <p><i class="fas fa-users"></i> До <?php echo htmlspecialchars($free_room['capacity']); ?> <?php echo $lang['guests'] ?? 'гостей'; ?></p>
                            <p><i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($free_room['price']); ?> <?php echo htmlspecialchars($currency); ?>/<?php echo $lang['per_night'] ?? 'ніч'; ?></p>
                            <a href="/templates/default/booking_order.php?room_id=<?php echo $free_room['id']; ?>&lang=<?php echo $lang_code; ?>" class="btn">
                                <i class="fas fa-calendar-check"></i> <?php echo $lang['btn_book'] ?? 'Забронювати'; ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php include 'booking_footer.php'; ?>
</body>
</html>