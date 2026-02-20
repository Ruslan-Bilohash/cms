<?php
session_start();

// Подключаем зависимости с абсолютными путями
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Загружаем настройки для reCAPTCHA
$settings_file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/site_settings.php';
if (file_exists($settings_file)) {
    $settings = include $settings_file;
} else {
    $settings = [
        'recaptcha' => [
            'site_key' => '',
            'secret_key' => ''
        ]
    ];
}

// Устанавливаем заголовки безопасности
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Проверка ключей reCAPTCHA
$recaptcha_site_key = $settings['recaptcha']['site_key'] ?? '';
$recaptcha_secret_key = $settings['recaptcha']['secret_key'] ?? '';
if (empty($recaptcha_site_key) || empty($recaptcha_secret_key)) {
    $error = "Ошибка: Ключи reCAPTCHA не определены в настройках.";
}

// Генерация CSRF-токена
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Обработка формы регистрации
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    // Проверяем CSRF-токен
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Ошибка: Неверный CSRF-токен';
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
                $error = "Ошибка проверки reCAPTCHA.";
            }
        }

        // Если reCAPTCHA пройдена, продолжаем валидацию
        if (!isset($error)) {
            // Санитизация и валидация ввода
            $contact = htmlspecialchars(trim($_POST['contact'] ?? ''), ENT_QUOTES, 'UTF-8');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';

            // Проверка корректности данных
            if (empty($contact) || empty($password) || empty($role)) {
                $error = 'Все поля обязательны для заполнения!';
            } elseif (!filter_var($contact, FILTER_VALIDATE_EMAIL) && !preg_match('/^\+?[0-9]{9,15}$/', $contact)) {
                $error = 'Введите корректный email или номер телефона (например, +37012345678).';
            } elseif (strlen($password) < 8) {
                $error = 'Пароль должен содержать минимум 8 символов!';
            } elseif (!in_array($role, ['customer', 'executor', 'company'])) {
                $error = 'Недопустимая роль!';
            } else {
                // Проверка на уникальность контакта
                $stmt = $conn->prepare('SELECT id FROM users WHERE contact = ?');
                if ($stmt === false) {
                    $error = 'Ошибка базы данных: ' . $conn->error;
                } else {
                    $stmt->bind_param('s', $contact);
                    $stmt->execute();
                    if ($stmt->get_result()->fetch_assoc()) {
                        $error = 'Этот контакт уже зарегистрирован!';
                    } else {
                        // Хеширование пароля и регистрация
                        $password_hash = password_hash($password, PASSWORD_DEFAULT);
                        $stmt->close();

                        $stmt = $conn->prepare('INSERT INTO users (contact, password, role) VALUES (?, ?, ?)');
                        if ($stmt === false) {
                            $error = 'Ошибка подготовки запроса: ' . $conn->error;
                        } else {
                            $stmt->bind_param('sss', $contact, $password_hash, $role);
                            if ($stmt->execute()) {
                                $_SESSION['user_id'] = $conn->insert_id;
                                $_SESSION['role'] = $role;
                                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                header('Location: /profile');
                                exit;
                            } else {
                                $error = 'Ошибка регистрации: ' . $stmt->error;
                            }
                            $stmt->close();
                        }
                    }
                }
            }
        }
    }
}

// Подключаем header
include $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-4">
            <h2 class="text-center mb-4">Registration</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <div class="card shadow-sm p-4">
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="register" value="1">
                    <div class="mb-3">
                        <label class="form-label">Contact</label>
                        <input type="text" name="contact" class="form-control" value="<?php echo isset($contact) ? htmlspecialchars($contact, ENT_QUOTES, 'UTF-8') : ''; ?>" placeholder="Email or phone" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                        <small class="form-text text-muted">Minimum 8 characters</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="customer" <?php echo (isset($role) && $role === 'customer') ? 'selected' : ''; ?>>Customer</option>
                            <option value="executor" <?php echo (isset($role) && $role === 'executor') ? 'selected' : ''; ?>>Executor</option>
                            <option value="company" <?php echo (isset($role) && $role === 'company') ? 'selected' : ''; ?>>Company</option>
                        </select>
                    </div>
                    <div class="mb-3 text-center">
                        <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key, ENT_QUOTES, 'UTF-8'); ?>"></div>
                    </div>
                    <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
                </form>
            </div>
            <p class="mt-3 text-center">Already have an account? <a href="/login">Login</a></p>
        </div>
    </div>
</main>

<!-- Подключение reCAPTCHA -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<style>
    .card { background: #fff; border-radius: 10px; }
    .g-recaptcha { display: inline-block; }
    @media (max-width: 576px) {
        .card { padding: 1rem; }
        .form-label { font-size: 0.9rem; }
        .form-control, .form-select, .btn { font-size: 0.9rem; }
        h2 { font-size: 1.5rem; }
        .g-recaptcha { transform: scale(0.85); transform-origin: center; }
    }
    @media (min-width: 577px) and (max-width: 991px) {
        .card { padding: 1.5rem; }
        .form-label { font-size: 1rem; }
        .form-control, .form-select, .btn { font-size: 1rem; }
        h2 { font-size: 1.75rem; }
    }
</style>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>