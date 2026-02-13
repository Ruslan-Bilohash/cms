<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /news.php");
    exit;
}

// Отладка: выводим все POST-данные
var_dump($_POST);
// exit; // Раскомментируйте для проверки данных

$news_id = isset($_POST['news_id']) ? (int)$_POST['news_id'] : 0;
$custom_url = isset($_POST['custom_url']) ? $_POST['custom_url'] : '';
$review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$guest_name = isset($_POST['guest_name']) ? trim($_POST['guest_name']) : '';
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// Валидация
if ($news_id <= 0 || empty($review_text) || $rating < 1 || $rating > 10) {
    $_SESSION['error'] = "Ошибка: Заполните все обязательные поля (новость, текст отзыва, рейтинг).";
    header("Location: /news/" . urlencode($custom_url));
    exit;
}

// Проверка reCAPTCHA (если используется)
if (!empty($settings['recaptcha_secret_key'])) {
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = [
        'secret' => $settings['recaptcha_secret_key'],
        'response' => $recaptcha_response,
        'remoteip' => $_SERVER['REMOTE_ADDR']
    ];
    $recaptcha_options = [
        'http' => [
            'method' => 'POST',
            'content' => http_build_query($recaptcha_data)
        ]
    ];
    $recaptcha_context = stream_context_create($recaptcha_options);
    $recaptcha_result = file_get_contents($recaptcha_url, false, $recaptcha_context);
    $recaptcha_json = json_decode($recaptcha_result);
    if (!$recaptcha_json->success) {
        $_SESSION['error'] = "Ошибка: Пройдите проверку reCAPTCHA.";
        header("Location: /news/" . urlencode($custom_url));
        exit;
    }
}

// Сохранение отзыва
$stmt = $conn->prepare("INSERT INTO news_reviews (news_id, user_id, guest_name, review_text, rating, created_at, is_approved) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
$is_approved = 0; // 0, если требуется модерация
$stmt->bind_param("iissii", $news_id, $user_id, $guest_name, $review_text, $rating, $is_approved);
if ($stmt->execute()) {
    $_SESSION['success'] = "Отзыв успешно отправлен и ожидает модерации.";
} else {
    $_SESSION['error'] = "Ошибка: Не удалось сохранить отзыв. " . $stmt->error;
}
$stmt->close();

header("Location: /news/" . urlencode($custom_url));
exit;
?>