<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Установка заголовков безопасности
$security_headers = [
    "X-Content-Type-Options: nosniff",
    "X-Frame-Options: DENY",
    "X-XSS-Protection: 1; mode=block",
    "Strict-Transport-Security: max-age=31536000; includeSubDomains"
];
foreach ($security_headers as $header) {
    header($header);
}

// CSRF-токен
$csrf_token = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

// Ограничение попыток входа
$login_attempts = $_SESSION['login_attempts'] ?? 0;
$last_attempt_time = $_SESSION['last_attempt_time'] ?? time();
$_SESSION['login_attempts'] = $login_attempts;
$_SESSION['last_attempt_time'] = $last_attempt_time;

$max_attempts = 5;
$lockout_time = 300;

// Настройки Telegram
$settings_file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/site_settings.php';
$settings = file_exists($settings_file) ? include $settings_file : [];
$telegram_bot_token = $settings['telegram_login']['bot_token'] ?? '';
$telegram_bot_username = $settings['telegram_login']['bot_username'] ?? '';
$telegram_enabled = $settings['telegram_login']['enabled'] ?? false;

if (empty($telegram_bot_token)) {
    $telegram_error = "Ошибка: Токен Telegram не найден!";
} else {
    $telegram_status = "Токен найден: " . htmlspecialchars($telegram_bot_token);
}

// Обработка Telegram-авторизации (POST-запрос от fetch)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json') {
    $raw_input = file_get_contents('php://input');
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debug.log', "Telegram POST received: " . $raw_input . "\n", FILE_APPEND);
    $input = json_decode($raw_input, true);
    $csrf_header = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    if ($csrf_header !== $csrf_token) {
        echo json_encode(['success' => false, 'error' => 'Неверный CSRF-токен']);
        exit;
    }

    $telegram_id = $input['id'] ?? null;
    $first_name = htmlspecialchars($input['first_name'] ?? '', ENT_QUOTES, 'UTF-8');
    $last_name = htmlspecialchars($input['last_name'] ?? '', ENT_QUOTES, 'UTF-8');
    $username = htmlspecialchars($input['username'] ?? '', ENT_QUOTES, 'UTF-8');
    $photo_url = htmlspecialchars($input['photo_url'] ?? '', ENT_QUOTES, 'UTF-8');

    if (!$telegram_id) {
        echo json_encode(['success' => false, 'error' => 'Telegram ID не передан']);
        exit;
    }

    // Логируем данные для проверки
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debug.log', "Photo URL: " . $photo_url . "\n", FILE_APPEND);

    // Если photo_url есть, скачиваем фото локально
    $photo_path = null;
    if ($photo_url) {
        $photo_content = @file_get_contents($photo_url);
        if ($photo_content !== false) {
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/users/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $photo_name = $telegram_id . '_telegram_avatar.jpg';
            $photo_path = '/public/uploads/users/' . $photo_name;
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . $photo_path, $photo_content);
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debug.log', "Photo saved to: " . $photo_path . "\n", FILE_APPEND);
        } else {
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debug.log', "Failed to download photo from: " . $photo_url . "\n", FILE_APPEND);
        }
    }

    // Проверка, существует ли пользователь с таким telegram_id
    $stmt = $conn->prepare("SELECT id, role FROM users WHERE telegram_id = ?");
    if ($stmt === false) {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debug.log', "Prepare failed: " . $conn->error . "\n", FILE_APPEND);
        echo json_encode(['success' => false, 'error' => 'Ошибка подготовки запроса: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("s", $telegram_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user) {
        // Пользователь существует, обновляем сессию
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt_time'] = time();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        echo json_encode(['success' => true]);
    } else {
        // Новый пользователь, регистрируем с photo_path (если фото скачано) или оставляем NULL
        $stmt = $conn->prepare("INSERT INTO users (telegram_id, first_name, last_name, nickname, photo, role, created_at) VALUES (?, ?, ?, ?, ?, 'customer', NOW())");
        if ($stmt === false) {
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debug.log', "Insert prepare failed: " . $conn->error . "\n", FILE_APPEND);
            echo json_encode(['success' => false, 'error' => 'Ошибка подготовки запроса: ' . $conn->error]);
            exit;
        }
        $stmt->bind_param("sssss", $telegram_id, $first_name, $last_name, $username, $photo_path);
        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            $_SESSION['role'] = 'customer';
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt_time'] = time();
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            echo json_encode(['success' => true]);
        } else {
            file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debug.log', "Insert failed: " . $stmt->error . "\n", FILE_APPEND);
            echo json_encode(['success' => false, 'error' => 'Ошибка базы данных: ' . $stmt->error]);
        }
        $stmt->close();
    }
    exit;
}

// Обработка обычной формы входа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $post_csrf_token = $_POST['csrf_token'] ?? '';
    $contact = htmlspecialchars(trim($_POST['contact'] ?? ''), ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'] ?? '';

    if ($post_csrf_token !== $csrf_token) {
        $error = "Ошибка: Неверный CSRF-токен";
    } elseif ($login_attempts >= $max_attempts && (time() - $last_attempt_time) < $lockout_time) {
        $remaining = ceil($lockout_time - (time() - $last_attempt_time));
        $error = "Слишком много попыток. Подождите $remaining секунд.";
    } elseif (empty($contact) || empty($password)) {
        $error = "Заполните все поля!";
    } else {
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE contact = ?");
        if ($stmt === false) {
            $error = "Ошибка базы данных: " . $conn->error;
        } else {
            $stmt->bind_param("s", $contact);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['login_attempts'] = 0;
                $_SESSION['last_attempt_time'] = time();
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                header("Location: /profile");
                exit;
            } else {
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = time();
                $error = "Неверный контакт или пароль!";
            }
        }
    }
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center mb-4">Вход</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form method="POST" class="card shadow-sm p-4">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <div class="mb-3">
                    <label class="form-label">Контакт</label>
                    <input type="text" name="contact" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Войти</button>
            </form>

            <?php if (!empty($telegram_bot_token) && !empty($telegram_bot_username) && $telegram_enabled): ?>
                <div class="text-center mt-3">
                    <p style="color: red;">Кнопка Telegram ниже (бот: <?php echo htmlspecialchars($telegram_bot_username); ?>):</p>
                    <script async src="https://telegram.org/js/telegram-widget.js?22" 
                            data-telegram-login="<?php echo htmlspecialchars(str_replace('@', '', $telegram_bot_username)); ?>" 
                            data-size="large" 
                            data-onauth="onTelegramAuth(user)" 
                            data-request-access="write"></script>
                </div>
            <?php else: ?>
                <p style="color: red;">Кнопка Telegram отключена или не настроена.</p>
            <?php endif; ?>
            
            <p class="mt-3 text-center">Нет аккаунта? <a href="/register">Зарегистрируйтесь</a></p>
        </div>
    </div>
</main>

<script type="text/javascript">
    function onTelegramAuth(user) {
        fetch('/templates/default/telegram_login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?php echo htmlspecialchars($csrf_token); ?>'
            },
            body: JSON.stringify({
                id: user.id,
                first_name: user.first_name,
                last_name: user.last_name,
                username: user.username,
                photo_url: user.photo_url
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Статус ответа: ' + response.status + ' ' + response.statusText);
            }
            return response.text();
        })
        .then(text => {
            console.log('Ответ сервера:', text);
            return JSON.parse(text);
        })
        .then(data => {
            if (data.success) {
                window.location.href = '/profile';
            } else {
                alert('Ошибка авторизации: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Произошла ошибка при авторизации: ' + error.message);
        });
    }
</script>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>