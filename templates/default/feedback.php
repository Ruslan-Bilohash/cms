<?php
session_start();

// Подключаем зависимости с абсолютными путями
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
$settings = require_once $_SERVER['DOCUMENT_ROOT'] . '/uploads/site_settings.php'; // Подключаем файл с ключами reCAPTCHA

// Устанавливаем заголовки безопасности
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

// Генерируем CSRF-токен, если его нет
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Получаем список тем
$topics = $conn->query("SELECT * FROM feedback WHERE type = 'topic' ORDER BY title ASC")->fetch_all(MYSQLI_ASSOC);

// Проверка ключей reCAPTCHA
$recaptcha_site_key = $settings['recaptcha']['site_key'] ?? '';
$recaptcha_secret_key = $settings['recaptcha']['secret_key'] ?? '';
if (empty($recaptcha_site_key) || empty($recaptcha_secret_key)) {
    $error = "Ошибка: Ключи reCAPTCHA не определены в настройках.";
}

// Устанавливаем страну по умолчанию
$country_code = 'UA'; // По умолчанию Украина

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем CSRF-токен
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Ошибка: Неверный CSRF-токен.";
    } else {
        // Проверка reCAPTCHA
        if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
            $error = "Пожалуйста, подтвердите, что вы не робот.";
        } else {
            $secret = $recaptcha_secret_key;
            $response = $_POST['g-recaptcha-response'];
            $remoteip = $_SERVER['REMOTE_ADDR'];
            $url = "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$response&remoteip=$remoteip";
            $curlData = json_decode(file_get_contents($url), true);

            if (!$curlData['success']) {
                $error = "Ошибка проверки reCAPTCHA: Неверный ключ или настройки домена.";
            }
        }

        if (!isset($error)) {
            $name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
            $contact_type = $_POST['contact_type'] ?? '';
            $contact = htmlspecialchars(trim($_POST['contact'] ?? ''), ENT_QUOTES, 'UTF-8');
            $phone = htmlspecialchars(trim($_POST['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
            $topic_id = (int)($_POST['topic_id'] ?? 0);
            $message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');
            $file_path = null;

            // Проверка данных
            if (empty($name) || empty($topic_id) || empty($message)) {
                $error = "Все поля обязательны для заполнения.";
            } elseif ($contact_type === 'email' && !filter_var($contact, FILTER_VALIDATE_EMAIL)) {
                $error = "Введите корректный email.";
            } elseif ($contact_type === 'phone' && !preg_match('/^\+?[0-9]{9,15}$/', $phone)) {
                $error = "Введите корректный номер телефона (например, +380123456789).";
            } else {
                // Определяем значение contact для базы данных
                $contact_value = $contact_type === 'phone' ? $phone : $contact;

                // Загрузка файла, если есть
                if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/feedback/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    $file_path = upload_image($_FILES['file'], $upload_dir);
                    if ($file_path === false) {
                        $error = "Ошибка загрузки файла. Поддерживаются только изображения, PDF и документы Word.";
                    } else {
                        $file_path = '/uploads/feedback/' . $file_path;
                    }
                } elseif (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $error = "Ошибка загрузки файла: код " . $_FILES['file']['error'];
                }

                if (!isset($error)) {
                    $stmt = $conn->prepare("INSERT INTO feedback (type, name, contact, topic_id, title, message, file_path, created_at) VALUES ('message', ?, ?, ?, 'Сообщение от пользователя', ?, ?, NOW())");
                    if ($stmt === false) {
                        $error = "Ошибка подготовки запроса: " . $conn->error;
                    } else {
                        $stmt->bind_param("ssiss", $name, $contact_value, $topic_id, $message, $file_path);
                        if ($stmt->execute()) {
                            $success = "Сообщение отправлено в службу поддержки Tender CMS!";
                            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        } else {
                            $error = "Ошибка отправки: " . $stmt->error;
                        }
                        $stmt->close();
                    }
                }
            }
        }
    }
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header.php';
?>

<main class="container py-5 feedback-container">
    <div class="row justify-content-center">
        <div class="col-12">
            <h1 class="text-center mb-4">Обратная связь</h1>
            <?php if (isset($success)): ?>
                <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
            <?php elseif (isset($error)): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <div class="card shadow-sm p-4">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Ваше имя</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Способ связи</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="contact_type" id="contact_phone" value="phone" checked>
                            <label class="form-check-label" for="contact_phone">Телефон</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="contact_type" id="contact_email" value="email">
                            <label class="form-check-label" for="contact_email">Email</label>
                        </div>
                    </div>
                    <div class="mb-3" id="phone_field">
                        <label for="phone" class="form-label">Телефон</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3" id="email_field" style="display: none;">
                        <label for="contact" class="form-label">Email</label>
                        <input type="email" class="form-control" id="contact" name="contact" placeholder="email@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="topic_id" class="form-label">Тема</label>
                        <select class="form-select" id="topic_id" name="topic_id" required>
                            <option value="">Выберите тему</option>
                            <?php foreach ($topics as $topic): ?>
                                <option value="<?php echo $topic['id']; ?>"><?php echo htmlspecialchars($topic['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Сообщение</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">Прикрепить файл</label>
                        <input type="file" class="form-control" id="file" name="file">
                    </div>
                    <div class="mb-3 text-center">
                        <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key); ?>"></div>
                    </div>
                    <div class="text-center d-flex justify-content-center gap-3 flex-wrap">
                        <button type="submit" class="btn btn-primary">Отправить</button>
                        <a href="https://t.me/meistru_lt" class="btn btn-telegram" target="_blank">
                            <svg class="telegram-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.895 17.097c-.338.758-1.193 1.04-1.94.625l-3.297-2.416-1.99 1.51c-.216.164-.476.285-.764.285-.533 0-.896-.447-.896-.972l.03-3.404 6.27-5.717c.275-.25.133-.687-.242-.562l-7.777 4.756-3.34-1.043c-.73-.228-.754-.81-.05-1.134l13.003-5.013c.597-.23 1.132.285.928.906l-2.235 11.183z" fill="currentColor"/>
                            </svg>
                            Написать в Telegram
                        </a>
                        <a href="viber://chat?number=%2B380977001414" class="btn btn-viber" target="_blank">
                            <svg class="viber-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm4.61 17.848c-.732.408-1.792.684-3.11.684-3.31 0-5.99-2.686-5.99-5.998 0-1.318.426-2.54 1.15-3.53.172-.235.344-.46.53-.675-.058-.057-.115-.115-.172-.173-.747-.747-1.15-1.74-1.15-2.796 0-1.058.403-2.05 1.15-2.797.057-.057.114-.114.172-.172-.186-.215-.358-.44-.53-.675-.724-.99-1.15-2.212-1.15-3.53 0-3.312 2.68-5.998 5.99-5.998 1.318 0 2.378.276 3.11.684.402.224.75.51 1.02.848.27.338.45.72.54 1.134.058.288.058.576 0 .864-.09.414-.27.796-.54 1.134-.27.338-.618.624-1.02.848-.732.408-1.792.684-3.11.684-1.318 0-2.378-.276-3.11-.684-.402-.224-.75-.51-1.02-.848-.27-.338-.45-.72-.54-1.134-.058-.288-.058-.576 0-.864.09-.414-.27-.796.54-1.134.27-.338.618-.624 1.02-.848.732-.408 1.792-.684 3.11-.684 3.31 0 5.99 2.686 5.99 5.998 0 1.318-.426 2.54-1.15 3.53-.172.235-.344-.46-.53.675.058.057.115.115.172.173.747.747 1.15 1.74 1.15 2.796 0 1.058-.403 2.05-1.15 2.797-.057.057-.114.114-.172.172.186.215.358.44.53.675.724.99 1.15 2.212 1.15 3.53 0 3.312-2.68 5.998-5.99 5.998z" fill="currentColor"/>
                            </svg>
                            Написать в Viber
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- Подключение reCAPTCHA -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<!-- Подключение стилей и скриптов intl-tel-input -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

<script>
    // Инициализация intl-tel-input с фиксированной страной
    const phoneInput = document.querySelector("#phone");
    const countryCode = "<?php echo htmlspecialchars($country_code); ?>";
    const iti = window.intlTelInput(phoneInput, {
        initialCountry: countryCode.toLowerCase(), // Устанавливаем Украину по умолчанию
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
    });

    // Переключение между полями телефона и email
    const phoneField = document.querySelector("#phone_field");
    const emailField = document.querySelector("#email_field");
    const phoneRadio = document.querySelector("#contact_phone");
    const emailRadio = document.querySelector("#contact_email");

    function toggleContactFields() {
        if (phoneRadio.checked) {
            phoneField.style.display = "block";
            emailField.style.display = "none";
            phoneInput.required = true;
            document.querySelector("#contact").required = false;
        } else {
            phoneField.style.display = "none";
            emailField.style.display = "block";
            phoneInput.required = false;
            document.querySelector("#contact").required = true;
        }
    }

    phoneRadio.addEventListener("change", toggleContactFields);
    emailRadio.addEventListener("change", toggleContactFields);

    // Вызываем при загрузке, чтобы установить начальное состояние
    toggleContactFields();
</script>

<style>
    .card { background: #fff; border-radius: 10px; }
    @media (max-width: 576px) {
        .card { padding: 1rem; }
        .form-label { font-size: 0.9rem; }
        .form-control, .btn { font-size: 0.9rem; }
        h1, h2 { font-size: 1.5rem; }
        textarea { rows: 4; }
        .btn-telegram, .btn-viber { font-size: 0.85rem; padding: 0.5rem 1rem; }
    }
    @media (min-width: 577px) and (max-width: 991px) {
        .card { padding: 1.5rem; }
        .form-label { font-size: 1rem; }
        .form-control, .btn { font-size: 1rem; }
        h1, h2 { font-size: 1.75rem; }
    }
    /* Стили для intl-tel-input */
    .iti { width: 100%; }
    .iti__flag-container { padding-left: 0; }
    /* Стили для кнопки Telegram */
    .btn-telegram {
        background-color: #0088cc;
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 0.25rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        font-size: 1rem;
        transition: background-color 0.2s;
    }
    .btn-telegram:hover {
        background-color: #0077b3;
        color: white;
        text-decoration: none;
    }
    /* Стили для кнопки Viber */
    .btn-viber {
        background-color: #7360f2;
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 0.25rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        font-size: 1rem;
        transition: background-color 0.2s;
    }
    .btn-viber:hover {
        background-color: #5e4ddb;
        color: white;
        text-decoration: none;
    }
    .telegram-icon, .viber-icon {
        vertical-align: middle;
    }
    /* Стили для reCAPTCHA */
    .g-recaptcha {
        display: inline-block;
    }
</style>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>