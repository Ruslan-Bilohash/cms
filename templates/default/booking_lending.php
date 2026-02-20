<?php
// templates/default/booking.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';

// Логирование для отладки
function logDebug($message) {
    error_log("[booking] " . $message . "\n", 3, $_SERVER['DOCUMENT_ROOT'] . '/logs/booking_debug.log');
}

// Загрузка настроек из файла
$settings_file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/booking_settings.php';
$settings = file_exists($settings_file) ? include $settings_file : [
    'currency' => 'UAH',
    'min_price' => 50,
    'max_price' => 5000,
    'items_per_page' => 5,
    'robots' => 'index, follow',
    'description' => 'Бронирование номеров онлайн - найдите идеальное место для отдыха.',
    'keywords' => 'бронирование, номера, отель, отдых, аренда'
];
$currency = $settings['currency'];
$min_price = $settings['min_price'];
$max_price = $settings['max_price'];
$items_per_page = $settings['items_per_page'];
$robots = $settings['robots'];
$description = $settings['description'];
$keywords = $settings['keywords'];

// Параметры поиска (опционально)
$check_in = $_POST['check_in'] ?? '';
$check_out = $_POST['check_out'] ?? '';
$guests = (int)($_POST['guests'] ?? 1);

// Валидация дат
if ($check_in && $check_out) {
    $check_in_date = DateTime::createFromFormat('Y-m-d', $check_in);
    $check_out_date = DateTime::createFromFormat('Y-m-d', $check_out);
    if (!$check_in_date || !$check_out_date || $check_out_date <= $check_in_date) {
        $error = "Неверные даты заезда или выезда.";
        $check_in = $check_out = '';
    }
}

// Поиск доступных номеров
$rooms = [];
$error = '';

// Базовый запрос для отображения номеров по умолчанию
$query = "SELECT r.*, c.name AS category_name 
          FROM rooms r 
          LEFT JOIN booking_categories c ON r.category_id = c.id 
          WHERE r.status = 'available' 
          AND r.price BETWEEN ? AND ? 
          AND r.capacity >= ? 
          LIMIT ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    $error = "Ошибка подготовки запроса: " . $conn->error;
} else {
    $stmt->bind_param("iiii", $min_price, $max_price, $guests, $items_per_page);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $rooms = $result->fetch_all(MYSQLI_ASSOC);
        if (empty($rooms)) {
            $error = "Нет доступных номеров для указанных параметров.";
        }
    } else {
        $error = "Ошибка выполнения запроса: " . $stmt->error;
    }
    $stmt->close();
}

// Если указаны даты, фильтруем дополнительно
if ($check_in && $check_out && empty($error)) {
    $query = "SELECT r.*, c.name AS category_name 
              FROM rooms r 
              LEFT JOIN booking_categories c ON r.category_id = c.id 
              WHERE r.status = 'available' 
              AND r.price BETWEEN ? AND ? 
              AND r.capacity >= ? 
              AND r.id NOT IN (
                  SELECT room_id FROM bookings 
                  WHERE (check_in <= ? AND check_out >= ?) 
                  AND status != 'cancelled'
              ) 
              LIMIT ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        $error = "Ошибка подготовки запроса: " . $conn->error;
    } else {
        $stmt->bind_param("iiissi", $min_price, $max_price, $guests, $check_out, $check_in, $items_per_page);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rooms = $result->fetch_all(MYSQLI_ASSOC);
            if (empty($rooms)) {
                $error = "Нет доступных номеров на выбранные даты.";
            }
        } else {
            $error = "Ошибка выполнения запроса: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Функция для получения массива изображений
function getImages($imageJson) {
    $defaultImage = ['/uploads/booking/default_room.webp'];
    if (empty($imageJson)) {
        logDebug("Image JSON is empty");
        return $defaultImage;
    }
    $images = json_decode($imageJson, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($images) || empty($images)) {
        logDebug("Invalid JSON or empty array: " . $imageJson);
        return $defaultImage;
    }
    $validImages = [];
    foreach ($images as $imagePath) {
        if (strpos($imagePath, '/uploads/booking/') === 0) {
            $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
            if (file_exists($fullPath)) {
                $validImages[] = $imagePath;
                logDebug("Valid image found: " . $imagePath);
            } else {
                logDebug("Image file not found: " . $fullPath);
            }
        } else {
            logDebug("Invalid image path: " . $imagePath);
        }
    }
    return !empty($validImages) ? $validImages : $defaultImage;
}

// Отладочный вывод всех комнат
if (isset($_GET['debug'])) {
    echo '<pre>';
    foreach ($rooms as $room) {
        echo "Room ID: {$room['id']}, Image JSON: {$room['image']}\n";
        $images = getImages($room['image']);
        print_r($images);
    }
    echo '</pre>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="<?php echo htmlspecialchars($robots); ?>">
    <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($keywords); ?>">
    <title>Онлайн бронирование - Tender CMS</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #003580;
            --secondary-color: #febb02;
            --success-color: #0071c2;
            --text-color: #333;
            --error-color: #721c24;
            --error-bg: #f8d7da;
        }
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: var(--text-color);
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: var(--primary-color);
            color: white;
            padding: 2rem;
            text-align: center;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .search-form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin: -50px 0 30px;
            position: relative;
            z-index: 1;
        }
        .search-form form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .search-form input, .search-form select {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            flex: 1;
            min-width: 150px;
        }
        .search-form button {
            padding: 12px 20px;
            background: var(--secondary-color);
            color: var(--primary-color);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
        .search-form button:hover {
            background: #e6a900;
        }
        .rooms-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .room-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .room-card:hover {
            transform: translateY(-5px);
        }
        .room-info {
            padding: 15px;
        }
        .room-info h3 {
            margin: 0 0 10px;
        }
        .room-info h3 a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s;
        }
        .room-info h3 a:hover {
            color: var(--success-color);
        }
        .room-info p {
            margin: 5px 0;
        }
        .room-info .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--success-color);
        }
        .book-btn {
            display: block;
            text-align: center;
            padding: 10px;
            background: var(--success-color);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            transition: background 0.3s;
        }
        .book-btn:hover {
            background: #005ea6;
        }
        .error {
            color: var(--error-color);
            background: var(--error-bg);
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
        /* Стили для мини-галереи */
        .room-gallery {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }
        .gallery-main {
            width: 100%;
            height: 100%;
        }
        .gallery-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
            border-radius: 10px 10px 0 0;
        }
        .gallery-main img.active {
            display: block;
        }
        .gallery-thumbs {
            display: flex;
            gap: 5px;
            padding: 10px;
            overflow-x: auto;
            background: #f9f9f9;
        }
        .gallery-thumbs img {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s, transform 0.3s;
        }
        .gallery-thumbs img:hover {
            opacity: 1;
            transform: scale(1.1);
        }
        .gallery-thumbs img.active {
            opacity: 1;
            border: 2px solid var(--primary-color);
        }
        .gallery-nav {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            transform: translateY(-50%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .room-gallery:hover .gallery-nav {
            opacity: 1;
        }
        .gallery-nav button {
            background: rgba(0, 0, 0, 0.5);
            border: none;
            color: white;
            font-size: 1.2rem;
            padding: 10px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .gallery-nav button:hover {
            background: rgba(0, 0, 0, 0.8);
        }
        @media (max-width: 768px) {
            .search-form form {
                flex-direction: column;
            }
            .search-form input, .search-form select, .search-form button {
                width: 100%;
            }
            .rooms-list {
                grid-template-columns: 1fr;
            }
            .room-gallery {
                height: 150px;
            }
            .gallery-thumbs img {
                width: 50px;
                height: 35px;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.room-gallery').forEach(gallery => {
                const mainImages = gallery.querySelectorAll('.gallery-main img');
                const thumbs = gallery.querySelectorAll('.gallery-thumbs img');
                const prevBtn = gallery.querySelector('.gallery-nav .prev');
                const nextBtn = gallery.querySelector('.gallery-nav .next');
                let currentIndex = 0;

                if (mainImages.length === 0) return; // Пропускаем, если нет изображений

                function updateGallery(index) {
                    mainImages.forEach(img => img.classList.remove('active'));
                    thumbs.forEach(thumb => thumb.classList.remove('active'));
                    mainImages[index].classList.add('active');
                    thumbs[index].classList.add('active');
                    currentIndex = index;
                }

                thumbs.forEach((thumb, index) => {
                    thumb.addEventListener('click', () => updateGallery(index));
                });

                if (prevBtn) {
                    prevBtn.addEventListener('click', () => {
                        let newIndex = currentIndex - 1;
                        if (newIndex < 0) newIndex = mainImages.length - 1;
                        updateGallery(newIndex);
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', () => {
                        let newIndex = currentIndex + 1;
                        if (newIndex >= mainImages.length) newIndex = 0;
                        updateGallery(newIndex);
                    });
                }

                updateGallery(0); // Инициализация с первым изображением
            });
        });
    </script>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-hotel"></i> Скрипт онлайн Бронирование</h1>
        <p>Найдите идеальное место для отдыха</p>
    </div>
    <div class="container">
        <div class="search-form">
            <form method="POST">
                <input type="date" name="check_in" value="<?php echo htmlspecialchars($check_in); ?>" required>
                <input type="date" name="check_out" value="<?php echo htmlspecialchars($check_out); ?>" required>
                <select name="guests" required>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo $guests == $i ? 'selected' : ''; ?>><?php echo $i; ?> <?php echo $i == 1 ? 'гость' : 'гостей'; ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit"><i class="fas fa-search"></i> Найти</button>
            </form>
        </div>

        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="rooms-list">
            <?php foreach ($rooms as $room): ?>
                <div class="room-card">
                    <div class="room-gallery">
                        <div class="gallery-main">
                            <?php foreach (getImages($room['image']) as $index => $image): ?>
                                <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>">
                            <?php endforeach; ?>
                        </div>
                        <div class="gallery-nav">
                            <button class="prev"><i class="fas fa-chevron-left"></i></button>
                            <button class="next"><i class="fas fa-chevron-right"></i></button>
                        </div>
                        <div class="gallery-thumbs">
                            <?php foreach (getImages($room['image']) as $index => $image): ?>
                                <img src="<?php echo htmlspecialchars($image); ?>" alt="Thumbnail" class="<?php echo $index === 0 ? 'active' : ''; ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="room-info">
                        <h3><a href="/templates/default/booking_order.php?room_id=<?php echo $room['id']; ?>"><?php echo htmlspecialchars($room['name']); ?></a></h3>
                        <p><i class="fas fa-folder"></i> Категория: <?php echo htmlspecialchars($room['category_name']); ?></p>
                        <p><i class="fas fa-users"></i> Вместимость: <?php echo htmlspecialchars($room['capacity']); ?> гостей</p>
                        <p class="price"><i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($room['price']); ?> <?php echo htmlspecialchars($currency); ?> / ночь</p>
                        <a href="/templates/default/booking_order.php?room_id=<?php echo $room['id']; ?>" class="book-btn"><i class="fas fa-book"></i> Забронировать</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</body>
</html>
<?php include 'booking_footer.php'; ?>