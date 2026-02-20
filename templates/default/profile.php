<?php
session_start();

// Логируем начало выполнения
file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debug.log', "Profile.php started: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debug.log', "DB included: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debug.log', "Functions included: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Устанавливаем заголовки безопасности
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debug.log', "User not logged in, redirecting: " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
    header('Location: /register');
    exit;
}

$user_id = (int)$_SESSION['user_id'];
file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debug.log', "User ID: $user_id\n", FILE_APPEND);

// Получаем данные пользователя
$stmt = $conn->prepare('SELECT * FROM users WHERE id = ?');
if ($stmt === false) {
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debug.log', "Prepare failed: " . $conn->error . "\n", FILE_APPEND);
    die("Ошибка подготовки запроса: " . $conn->error);
}
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debug.log', "User data fetched: " . json_encode($user) . "\n", FILE_APPEND);

// Обработка обновления профиля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = htmlspecialchars(trim($_POST['first_name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $last_name = htmlspecialchars(trim($_POST['last_name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $nickname = htmlspecialchars(trim($_POST['nickname'] ?? ''), ENT_QUOTES, 'UTF-8');
    $about = htmlspecialchars(trim($_POST['about'] ?? ''), ENT_QUOTES, 'UTF-8');
    $city_id = (int)($_POST['city_id'] ?? 0) ?: null;
    $category_id = (int)($_POST['category_id'] ?? 0) ?: null;

    $photo_sql = '';
    $photo_path = $user['photo']; // Сохраняем старое фото, если новое не загружено
    if (!empty($_FILES['photo']['name'])) {
        $photo = $_FILES['photo'];
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/users/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $photo_name = $user_id . '_' . time() . '.' . pathinfo($photo['name'], PATHINFO_EXTENSION);
        $photo_path = '/public/uploads/users/' . $photo_name;
        if (move_uploaded_file($photo['tmp_name'], $upload_dir . $photo_name)) {
            $photo_sql = ', photo = ?';
        }
    }

    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, nickname = ?, about = ?, city_id = ?, category_id = ?, profile_completed = 1 $photo_sql WHERE id = ?");
    if ($stmt === false) {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/debug.log', "Update prepare failed: " . $conn->error . "\n", FILE_APPEND);
        die("Ошибка подготовки запроса: " . $conn->error);
    }
    if ($photo_sql) {
        $stmt->bind_param('ssssiiisi', $first_name, $last_name, $nickname, $about, $city_id, $category_id, $photo_path, $user_id);
    } else {
        $stmt->bind_param('ssssiii', $first_name, $last_name, $nickname, $about, $city_id, $category_id, $user_id);
    }
    $stmt->execute();
    $stmt->close();
    header('Location: /profile');
    exit;
}

// Обработка добавления в галерею
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_gallery'])) {
    $gallery_title = htmlspecialchars(trim($_POST['gallery_title'] ?? ''), ENT_QUOTES, 'UTF-8');
    if (!empty($_FILES['gallery_image']['name'])) {
        $image = $_FILES['gallery_image'];
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/gallery/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $image_name = $user_id . '_' . time() . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
        $image_path = '/public/uploads/gallery/' . $image_name;
        if (move_uploaded_file($image['tmp_name'], $upload_dir . $image_name)) {
            $stmt = $conn->prepare('INSERT INTO gallery (user_id, title, image) VALUES (?, ?, ?)');
            $stmt->bind_param('iss', $user_id, $gallery_title, $image_path);
            $stmt->execute();
            $stmt->close();
            header('Location: /profile');
            exit;
        }
    }
}

// Обработка добавления документа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_document'])) {
    $document_title = htmlspecialchars(trim($_POST['document_title'] ?? ''), ENT_QUOTES, 'UTF-8');
    if (!empty($_FILES['document_file']['name'])) {
        $file = $_FILES['document_file'];
        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/documents/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $file_name = $user_id . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_path = '/public/uploads/documents/' . $file_name;
        if (move_uploaded_file($file['tmp_name'], $upload_dir . $file_name)) {
            $stmt = $conn->prepare('INSERT INTO documents (user_id, title, file) VALUES (?, ?, ?)');
            $stmt->bind_param('iss', $user_id, $document_title, $file_path);
            $stmt->execute();
            $stmt->close();
            header('Location: /profile');
            exit;
        }
    }
}

// Обработка удаления из галереи
if (isset($_GET['delete_gallery'])) {
    $gallery_id = (int)$_GET['delete_gallery'];
    $stmt = $conn->prepare('DELETE FROM gallery WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ii', $gallery_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header('Location: /profile');
    exit;
}

// Обработка удаления документа
if (isset($_GET['delete_document'])) {
    $document_id = (int)$_GET['delete_document'];
    $stmt = $conn->prepare('DELETE FROM documents WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ii', $document_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header('Location: /profile');
    exit;
}

// Получаем данные
$gallery = $conn->query("SELECT * FROM gallery WHERE user_id = $user_id")->fetch_all(MYSQLI_ASSOC);
$documents = $conn->query("SELECT * FROM documents WHERE user_id = $user_id")->fetch_all(MYSQLI_ASSOC);
$reviews = $conn->query("SELECT r.*, u.nickname AS author FROM reviews r JOIN users u ON r.author_id = u.id WHERE r.user_id = $user_id")->fetch_all(MYSQLI_ASSOC);
$cities = $conn->query('SELECT * FROM cities')->fetch_all(MYSQLI_ASSOC);
$categories = $conn->query('SELECT * FROM categories')->fetch_all(MYSQLI_ASSOC);
$settings = $conn->query('SELECT * FROM settings WHERE id = 1')->fetch_assoc();

include $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header.php';
?>



<main class="container py-5">
    <!-- Фото профиля -->
    <div class="text-center mb-5">
        <?php if (!empty($user['photo'])): ?>
            <img src="<?php echo htmlspecialchars($user['photo'], ENT_QUOTES, 'UTF-8'); ?>" class="rounded-circle profile-photo" alt="Фото профиля">
        <?php else: ?>
            <img src="/public/uploads/users/default-avatar.webp" class="rounded-circle profile-photo" alt="Фото по умолчанию">
        <?php endif; ?>
    </div>

    <h2 class="text-center mb-5">Ваш профиль</h2>

    <!-- Основная информация -->
    <section class="col-lg-8 mx-auto mb-5 profile-section">
        <h3 class="mb-4">Основная информация</h3>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="card shadow-sm p-4">
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-envelope me-2"></i>Ваш контакт</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['contact'] ?? $user['telegram_id'], ENT_QUOTES, 'UTF-8'); ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-user me-2"></i>Имя</label>
                <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-user me-2"></i>Фамилия</label>
                <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-id-badge me-2"></i>Никнейм</label>
                <input type="text" name="nickname" class="form-control" value="<?php echo htmlspecialchars($user['nickname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-info-circle me-2"></i>О себе</label>
                <textarea name="about" class="form-control" rows="5"><?php echo htmlspecialchars($user['about'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-city me-2"></i>Город</label>
                <select name="city_id" class="form-select">
                    <option value="">Выберите город</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?php echo (int)$city['id']; ?>" <?php echo $user['city_id'] == $city['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($city['name'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-tags me-2"></i>Категория</label>
                <select name="category_id" class="form-select">
                    <option value="">Выберите категорию</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo (int)$cat['id']; ?>" <?php echo $user['category_id'] == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['title'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-camera me-2"></i>Фото профиля</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
                <small class="form-text text-muted">JPEG, PNG, GIF, WEBP. Максимум 5 МБ.</small>
            </div>
            <button type="submit" name="update_profile" class="btn btn-primary w-100"><i class="fas fa-save me-2"></i>Сохранить</button>
        </form>
    </section>

    <!-- Галерея работ -->
    <section class="col-lg-8 mx-auto mb-5 profile-section">
        <h3 class="mb-4">Галерея работ</h3>
        <div class="row g-3">
            <?php foreach ($gallery as $item): ?>
                <div class="col-sm-6 col-md-4">
                    <div class="card shadow-sm h-100">
                        <img src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top gallery-image" alt="<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="card-body">
                            <p class="card-text"><?php echo htmlspecialchars($item['title'] ?: 'Без названия', ENT_QUOTES, 'UTF-8'); ?></p>
                            <a href="/profile?delete_gallery=<?php echo (int)$item['id']; ?>" class="btn btn-danger btn-sm w-100" onclick="return confirm('Удалить это изображение?')"><i class="fas fa-trash me-2"></i>Удалить</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <form method="POST" enctype="multipart/form-data" class="card shadow-sm p-4 mt-4">
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-heading me-2"></i>Название работы</label>
                <input type="text" name="gallery_title" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-image me-2"></i>Изображение</label>
                <input type="file" name="gallery_image" class="form-control" accept="image/*" required>
                <small class="form-text text-muted">JPEG, PNG, GIF, WEBP.</small>
            </div>
            <button type="submit" name="add_gallery" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Добавить</button>
        </form>
    </section>

    <!-- Документы -->
    <section class="col-lg-8 mx-auto mb-5 profile-section">
        <h3 class="mb-4">Документы</h3>
        <ul class="list-group mb-4">
            <?php foreach ($documents as $doc): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="<?php echo htmlspecialchars($doc['file'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="text-primary"><i class="fas fa-file-alt me-2"></i><?php echo htmlspecialchars($doc['title'] ?: 'Документ', ENT_QUOTES, 'UTF-8'); ?></a>
                    <a href="/profile?delete_document=<?php echo (int)$doc['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Удалить этот документ?')"><i class="fas fa-trash me-2"></i>Удалить</a>
                </li>
            <?php endforeach; ?>
        </ul>
        <form method="POST" enctype="multipart/form-data" class="card shadow-sm p-4">
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-heading me-2"></i>Название документа</label>
                <input type="text" name="document_title" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-file-upload me-2"></i>Файл</label>
                <input type="file" name="document_file" class="form-control" required>
                <small class="form-text text-muted">PDF, DOC, DOCX.</small>
            </div>
            <button type="submit" name="add_document" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Добавить</button>
        </form>
    </section>

    <!-- Отзывы -->
    <section class="col-lg-8 mx-auto profile-section">
        <h3 class="mb-4">Отзывы</h3>
        <?php if (empty($reviews)): ?>
            <p class="text-muted">Отзывов пока нет.</p>
        <?php else: ?>
            <ul class="list-group">
                <?php foreach ($reviews as $review): ?>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <strong><i class="fas fa-user-circle me-2"></i><?php echo htmlspecialchars($review['author'], ENT_QUOTES, 'UTF-8'); ?></strong>
                            <span class="badge bg-primary"><i class="fas fa-star me-1"></i><?php echo (int)$review['rating']; ?>/5</span>
                        </div>
                        <p class="mb-2"><?php echo htmlspecialchars($review['text'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i><?php echo date('d.m.Y H:i', strtotime($review['created_at'])); ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</main>

<!-- Подключение Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Подключение Bootstrap JS для навигации -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
    /* Стили для профиля */
    .profile-section .card {
        border: none;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .profile-section .form-label {
        font-weight: 500;
        color: #333;
        margin-bottom: 0.5rem;
    }
    .profile-section .form-control,
    .profile-section .form-select {
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 12px;
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .profile-section .form-control:focus,
    .profile-section .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.2);
        outline: none;
    }
    .profile-section textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }
    .profile-section .btn-primary {
        background-color: #007bff;
        border: none;
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 1rem;
        transition: background-color 0.2s, transform 0.2s;
    }
    .profile-section .btn-primary:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
    }
    .profile-section .btn-danger {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: background-color 0.2s;
    }
    .profile-section .btn-danger:hover {
        background-color: #c82333;
    }
    .profile-photo {
        width: 180px;
        height: 180px;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .gallery-image {
        height: 200px;
        object-fit: cover;
    }
    .list-group-item {
        border-radius: 8px;
        margin-bottom: 8px;
        transition: background-color 0.2s;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    .badge.bg-primary {
        padding: 6px 12px;
        border-radius: 12px;
    }

    /* Стили для навигации */
    .navbar {
        border-radius: 12px;
        background: #fff;
        padding: 1rem;
    }
    .navbar-nav .nav-link {
        color: #333;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        transition: background-color 0.2s, color 0.2s;
    }
    .navbar-nav .nav-link:hover {
        background-color: #f8f9fa;
        color: #007bff;
    }
    .navbar-nav .nav-link.active {
        color: #007bff;
        font-weight: 700;
        background-color: #e9ecef;
    }
    .navbar-toggler {
        border: none;
        padding: 0.5rem;
    }
    .navbar-toggler:focus {
        box-shadow: none;
    }
    .navbar-collapse {
        justify-content: center;
    }

    /* Адаптивность */
    @media (max-width: 576px) {
        .profile-photo {
            width: 120px;
            height: 120px;
        }
        .profile-section .card {
            padding: 1.5rem;
        }
        .profile-section .form-label {
            font-size: 0.9rem;
        }
        .profile-section .form-control,
        .profile-section .form-select,
        .profile-section .btn-primary,
        .profile-section .btn-danger {
            font-size: 0.9rem;
            padding: 10px;
        }
        .profile-section h2,
        .profile-section h3 {
            font-size: 1.5rem;
        }
        .gallery-image {
            height: 150px;
        }
        .profile-section textarea.form-control {
            min-height: 80px;
        }
        .navbar-nav .nav-link {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .navbar-collapse {
            background: #fff;
            border-radius: 8px;
            margin-top: 0.5rem;
            padding: 0.5rem;
        }
    }
    @media (min-width: 577px) and (max-width: 991px) {
        .profile-photo {
            width: 150px;
            height: 150px;
        }
        .profile-section .card {
            padding: 2rem;
        }
        .profile-section .form-label {
            font-size: 1rem;
        }
        .profile-section .form-control,
        .profile-section .form-select,
        .profile-section .btn-primary {
            font-size: 1rem;
            padding: 12px;
        }
        .profile-section h2 {
            font-size: 1.75rem;
        }
        .profile-section h3 {
            font-size: 1.5rem;
        }
        .gallery-image {
            height: 180px;
        }
        .navbar-nav .nav-link {
            padding: 0.75rem 1.25rem;
        }
    }
    @media (min-width: 992px) {
        .profile-section .card {
            padding: 2.5rem;
        }
        .profile-section h2 {
            font-size: 2rem;
        }
        .profile-section h3 {
            font-size: 1.75rem;
        }
        .navbar-nav .nav-link {
            padding: 0.75rem 1.5rem;
        }
    }
</style>

<?php include $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>