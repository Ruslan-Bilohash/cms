<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Завантаження налаштувань із site_settings.php
$settings_file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/site_settings.php';
$settings = [];
$tiny_api_key = '';
if (file_exists($settings_file)) {
    $settings = include $settings_file;
    $tiny_api_key = $settings['tiny_api_key'] ?? '';
}

// Запускаємо сесію
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Установка заголовків безпеки
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");

// Генерація CSRF-токена
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Перевірка авторизації
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: /register");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
// Отримання даних користувача
$stmt = $conn->prepare("SELECT first_name, profile_completed FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$categories = $conn->query("SELECT * FROM categories")->fetch_all(MYSQLI_ASSOC);
$cities = $conn->query("SELECT * FROM cities")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Ошибка: Неверный CSRF-токен");
    }

    $title = $conn->real_escape_string($_POST['title'] ?? '');
    $short_desc = $conn->real_escape_string($_POST['short_desc'] ?? '');
    $budget = (float)($_POST['budget'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $city_id = (int)($_POST['city_id'] ?? 0);
    $phone = $conn->real_escape_string($_POST['phone'] ?? '');
    $name = $conn->real_escape_string($_POST['name'] ?? '');

    $images = [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $filename) {
            if ($_FILES['images']['error'][$key] == 0) {
                $file = [
                    'name' => $_FILES['images']['name'][$key],
                    'tmp_name' => $_FILES['images']['tmp_name'][$key],
                    'size' => $_FILES['images']['size'][$key],
                    'type' => $_FILES['images']['type'][$key]
                ];
                $uploaded = upload_image($file, $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/tenders/images/');
                if ($uploaded) $images[] = $uploaded;
            }
        }
    }

    $documents = [];
    if (!empty($_FILES['documents']['name'][0])) {
        foreach ($_FILES['documents']['name'] as $key => $filename) {
            if ($_FILES['documents']['error'][$key] == 0) {
                $file = [
                    'name' => $_FILES['documents']['name'][$key],
                    'tmp_name' => $_FILES['documents']['tmp_name'][$key],
                    'size' => $_FILES['documents']['size'][$key],
                    'type' => $_FILES['documents']['type'][$key]
                ];
                $uploaded = upload_image($file, $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/tenders/documents/');
                if ($uploaded) $documents[] = $uploaded;
            }
        }
    }

    $images_json = json_encode($images);
    $documents_json = json_encode($documents);

    $stmt = $conn->prepare("INSERT INTO tenders (user_id, title, short_desc, budget, category_id, city_id, phone, name, images, documents, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("issdiissss", $user_id, $title, $short_desc, $budget, $category_id, $city_id, $phone, $name, $images_json, $documents_json);
    if ($stmt->execute()) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Оновлення CSRF-токена
        $redirect = $user['profile_completed'] ? '/' : '/profile';
        header("Location: $redirect");
        exit;
    } else {
        $error = "Ошибка: Не удалось сохранить тендер. " . $stmt->error;
    }
    $stmt->close();
}

// Підключення шапки
require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/header.php';
?>

<main class="container-fluid py-5">
    <h2 class="text-center mb-4 fw-bold text-primary"><i class="fas fa-plus-circle me-2"></i>Добавить тендер</h2>
    <?php if (isset($error)): ?>
        <div class="container"><div class="alert alert-danger"><?php echo $error; ?></div></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data" class="card shadow-lg p-4 rounded-3 bg-light">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="row g-3">
            <div class="col-md-6 col-12">
                <label class="form-label"><i class="fas fa-heading me-2"></i>Название</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="col-md-6 col-12">
                <label class="form-label"><i class="fas fa-user me-2"></i>Ваше имя</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6 col-12">
                <label class="form-label"><i class="fas fa-phone me-2"></i>Номер телефона</label>
                <input type="text" name="phone" class="form-control" placeholder="+380..." required>
            </div>
            <div class="col-md-6 col-12">
                <label class="form-label"><i class="fas fa-city me-2"></i>Город</label>
                <select name="city_id" class="form-select" required>
                    <option value="">Выберите город</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?php echo $city['id']; ?>"><?php echo htmlspecialchars($city['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-12">
                <label class="form-label"><i class="fas fa-folder me-2"></i>Категория</label>
                <select name="category_id" class="form-select" required>
                    <option value="">Выберите категорию</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 col-12">
                <label class="form-label"><i class="fas fa-money-bill-wave me-2"></i>Бюджет (грн)</label>
                <input type="number" name="budget" class="form-control" step="0.01" required>
            </div>
            <div class="col-12">
                <label class="form-label"><i class="fas fa-align-left me-2"></i>Краткое описание</label>
                <textarea name="short_desc" class="form-control" id="short_desc_add" required></textarea>
            </div>
            <div class="col-md-6 col-12">
                <label class="form-label"><i class="fas fa-image me-2"></i>Изображения</label>
                <input type="file" name="images[]" id="imageInputAdd" class="form-control" multiple accept="image/*">
            </div>
            <div class="col-12">
                <div class="row" id="imagePreviewAdd"></div>
            </div>
            <div class="col-12">
                <label class="form-label"><i class="fas fa-file-alt me-2"></i>Документы</label>
                <input type="file" name="documents[]" class="form-control" multiple accept=".pdf,.doc,.docx">
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-custom-primary w-100 py-3 fw-bold"><i class="fas fa-plus me-2"></i>Добавить</button>
            </div>
        </div>
    </form>
</main>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/templates/default/footer.php'; ?>

<script src="https://cdn.tiny.cloud/1/<?php echo htmlspecialchars($tiny_api_key); ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
tinymce.init({
    selector: '#short_desc_add',
    height: 300,
    plugins: 'advlist autolink lists link image charmap preview anchor',
    toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist outdent indent | link image',
    menubar: false,
    content_style: 'body { font-family: Arial, sans-serif; }',
    setup: function (editor) {
        editor.on('change', function () {
            editor.save();
        });
    }
});

document.querySelector('form').addEventListener('submit', function () {
    tinymce.triggerSave();
});

const imageInputAdd = document.getElementById('imageInputAdd');
const imagePreviewAdd = document.getElementById('imagePreviewAdd');
let selectedFilesAdd = [];

imageInputAdd.addEventListener('change', function () {
    imagePreviewAdd.innerHTML = '';
    selectedFilesAdd = Array.from(this.files);

    selectedFilesAdd.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const div = document.createElement('div');
            div.className = 'col-md-3 col-6 mb-3 position-relative';
            div.innerHTML = `
                <img src="${e.target.result}" class="img-thumbnail" alt="Превью">
                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" onclick="this.parentElement.remove(); selectedFilesAdd.splice(${index}, 1); updateFileInputAdd();"><i class="fas fa-trash"></i></button>
            `;
            imagePreviewAdd.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});

function updateFileInputAdd() {
    const dataTransfer = new DataTransfer();
    selectedFilesAdd.forEach(file => dataTransfer.items.add(file));
    imageInputAdd.files = dataTransfer.files;
}
</script>