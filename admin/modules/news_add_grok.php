<?php
if (!isAdmin()) {
    header("Location: ../index.php");
    exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Отключаем отладочный вывод
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Загрузка настроек сайта
$settings_file = $_SERVER['DOCUMENT_ROOT'] . '/Uploads/site_settings.php';
$settings = file_exists($settings_file) ? include $settings_file : [];
$tiny_api_key = $settings['tiny_api_key'] ?? '';

$categories = $conn->query("SELECT * FROM news_categories")->fetch_all(MYSQLI_ASSOC);
$languages = $conn->query("SELECT * FROM languages WHERE is_active = 1 ORDER BY code")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_news'])) {
    $category_id = (int)$_POST['category_id'];
    $published = isset($_POST['published']) ? 1 : 0;
    $reviews_enabled = isset($_POST['reviews_enabled']) ? 1 : 0;

    $title = $conn->real_escape_string($_POST['title'] ?? '');
    $short_desc = $conn->real_escape_string($_POST['short_desc'] ?? '');
    $full_desc = $_POST['full_desc'] ?? '';
    $keywords = $conn->real_escape_string($_POST['keywords'] ?? '');
    $meta_title = $conn->real_escape_string($_POST['meta_title'] ?? $title);
    $meta_desc = $conn->real_escape_string($_POST['meta_desc'] ?? $short_desc);
    $og_title = $conn->real_escape_string($_POST['og_title'] ?? $title);
    $og_desc = $conn->real_escape_string($_POST['og_desc'] ?? $short_desc);
    $twitter_title = $conn->real_escape_string($_POST['twitter_title'] ?? $title);
    $twitter_desc = $conn->real_escape_string($_POST['twitter_desc'] ?? $short_desc);

    $custom_url_input = trim($_POST['custom_url'] ?? '');
    $custom_url_base = !empty($custom_url_input) ? $custom_url_input : transliterate($title);
    $custom_url = $conn->real_escape_string($custom_url_base);

    $counter = 1;
    while (true) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM news WHERE custom_url = ?");
        $stmt->bind_param("s", $custom_url);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_row()[0];
        $stmt->close();
        if ($result == 0) break;
        $custom_url = $custom_url_base . '-' . $counter++;
    }

    $images = [];
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['name'] as $key => $name) {
            if ($_FILES['images']['error'][$key] == 0) {
                $file = [
                    'name' => $name,
                    'type' => $_FILES['images']['type'][$key],
                    'tmp_name' => $_FILES['images']['tmp_name'][$key],
                    'error' => $_FILES['images']['error'][$key],
                    'size' => $_FILES['images']['size'][$key]
                ];
                $uploaded = upload_image($file, $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/news/');
                if ($uploaded) $images[] = $uploaded;
            }
        }
    }
    $image = !empty($images) ? json_encode($images) : '';

    // Сохраняем основную новость (русский язык)
    $stmt = $conn->prepare("
        INSERT INTO news 
        (category_id, title, short_desc, full_desc, keywords, meta_title, meta_desc, 
         og_title, og_desc, twitter_title, twitter_desc, custom_url, image, 
         published, reviews_enabled, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("issssssssssssiis", $category_id, $title, $short_desc, $full_desc, $keywords,
        $meta_title, $meta_desc, $og_title, $og_desc, $twitter_title, $twitter_desc,
        $custom_url, $image, $published, $reviews_enabled);
    $stmt->execute();
    $news_id = $conn->insert_id;
    $stmt->close();

    // Сохраняем переводы для дополнительных языков
    foreach ($languages as $lang) {
        $lang_code = $lang['code'];
        if (!empty($_POST['title'][$lang_code])) {
            $trans_title = $conn->real_escape_string($_POST['title'][$lang_code]);
            $trans_short_desc = $conn->real_escape_string($_POST['short_desc'][$lang_code] ?? '');
            $trans_full_desc = $_POST['full_desc'][$lang_code] ?? '';
            $trans_keywords = $conn->real_escape_string($_POST['keywords'][$lang_code] ?? '');
            $trans_meta_title = $conn->real_escape_string($_POST['meta_title'][$lang_code] ?? $trans_title);
            $trans_meta_desc = $conn->real_escape_string($_POST['meta_desc'][$lang_code] ?? $trans_short_desc);
            $trans_og_title = $conn->real_escape_string($_POST['og_title'][$lang_code] ?? $trans_title);
            $trans_og_desc = $conn->real_escape_string($_POST['og_desc'][$lang_code] ?? $trans_short_desc);
            $trans_twitter_title = $conn->real_escape_string($_POST['twitter_title'][$lang_code] ?? $trans_title);
            $trans_twitter_desc = $conn->real_escape_string($_POST['twitter_desc'][$lang_code] ?? $trans_short_desc);

            $trans_custom_url_input = trim($_POST['custom_url'][$lang_code] ?? '');
            $trans_custom_url_base = !empty($trans_custom_url_input) ? $trans_custom_url_input : generate_seo_url($trans_title);
            $trans_custom_url = $conn->real_escape_string($trans_custom_url_base);

            $counter = 1;
            while (true) {
                $stmt = $conn->prepare("
                    SELECT COUNT(*) FROM news_translations 
                    WHERE custom_url = ? AND language_code = ? AND news_id != ?
                ");
                $stmt->bind_param("ssi", $trans_custom_url, $lang_code, $news_id);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_row()[0];
                $stmt->close();
                if ($result == 0) break;
                $trans_custom_url = $trans_custom_url_base . '-' . $counter++;
            }

            $stmt = $conn->prepare("
                INSERT INTO news_translations 
                (news_id, language_code, title, short_desc, full_desc, keywords, meta_title, meta_desc, 
                 og_title, og_desc, twitter_title, twitter_desc, custom_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("issssssssssss", $news_id, $lang_code, $trans_title, $trans_short_desc,
                $trans_full_desc, $trans_keywords, $trans_meta_title, $trans_meta_desc,
                $trans_og_title, $trans_og_desc, $trans_twitter_title, $trans_twitter_desc,
                $trans_custom_url);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: ?module=news_list");
    exit;
}

function generate_seo_url($title) {
    $title = mb_strtolower($title, 'UTF-8');
    $title = preg_replace('/[^a-zа-я0-9\s]/u', '', $title);
    $words = array_filter(explode(' ', trim($title)));
    $words = array_slice($words, 0, 5);
    return implode('-', $words);
}

function transliterate($text) {
    $translit = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
        'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
        'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
        ' ' => '-', 'ъ' => '', 'ь' => ''
    ];
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^а-яa-z0-9\s]/u', '', $text);
    $result = '';
    for ($i = 0; $i < mb_strlen($text, 'UTF-8'); $i++) {
        $char = mb_substr($text, $i, 1, 'UTF-8');
        $result .= $translit[$char] ?? $char;
    }
    $result = preg_replace('/-+/', '-', trim($result, '-'));
    return $result;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Добавить новость</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/<?= htmlspecialchars($tiny_api_key) ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #343a40;
        }
        .card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            background: #fff;
            transition: transform 0.3s;
        }
        .card:hover { transform: translateY(-5px); }
        .form-control, .form-select {
            border-radius: 12px;
            padding: 12px;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
        }
        /* ... (весь ваш существующий стиль остаётся без изменений) ... */
        .grok-paste-section {
            background: #f0f8ff;
            border: 2px dashed #007bff;
            border-radius: 12px;
            padding: 20px;
            margin-top: 2.5rem;
        }
        .grok-paste-section textarea {
            font-family: 'Courier New', Courier, monospace;
            font-size: 1rem;
            line-height: 1.5;
        }
    </style>
</head>
<body>

<main class="container py-5">
    <h2 class="text-center mb-5 fw-bold text-primary">
        <i class="fas fa-plus-circle me-2"></i> Добавить новость
    </h2>

    <?php if (empty($tiny_api_key)): ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> 
            Ошибка: Ключ API для TinyMCE не найден. Проверьте настройки в 
            <code>/Uploads/site_settings.php</code> или обратитесь к администратору.
            <a href="https://www.tiny.cloud/" target="_blank">Получить ключ</a>.
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="card p-5">
        <input type="hidden" name="add_news" value="1">

        <div class="row g-4">
            <!-- Основные поля (Русский язык) -->
            <div class="col-12 form-section">
                <h5 class="fw-bold text-primary mb-4">
                    <i class="fas fa-globe me-2"></i> Основной язык (Русский)
                </h5>

                <!-- ... Весь ваш существующий HTML-код полей остаётся без изменений ... -->

                <!-- Здесь все ваши поля: title, category_id, images, custom_url, short_desc, full_desc, keywords, meta-поля, OG, Twitter ... -->
            </div>

            <!-- Дополнительные языки -->
            <?php if (!empty($languages)): ?>
                <!-- ... весь блок дополнительных языков остаётся без изменений ... -->
            <?php endif; ?>

            <!-- Опубликовано и отзывы -->
            <div class="col-md-6 mt-4">
                <!-- ... чекбоксы published и reviews_enabled ... -->
            </div>

            <!-- Блок быстрой вставки от Grok -->
            <div class="col-12 grok-paste-section">
                <h5 class="text-primary mb-3">
                    <i class="fas fa-robot me-2"></i> Быстрая вставка новости от Grok
                </h5>
                <p class="text-muted small mb-3">
                    Вставьте сюда весь ответ Grok (заголовок + анонс + текст)
                </p>
                <textarea id="grok-paste-area" class="form-control mb-3" rows="7" placeholder="Ожидаемый формат:

Заголовок новости
Короткое описание (1–3 предложения)

Полный текст статьи...
                "></textarea>

                <div class="d-flex flex-wrap gap-3">
                    <button type="button" class="btn btn-primary btn-lg px-5" onclick="pasteFromGrok()">
                        Вставить в форму ↓
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('grok-paste-area').value=''">
                        Очистить
                    </button>
                </div>
            </div>

            <!-- Кнопка отправки -->
            <div class="col-12 mt-5">
                <button type="submit" name="submit" class="btn btn-custom-primary w-100 py-3">
                    <i class="fas fa-plus me-2"></i> Добавить новость
                </button>
            </div>
        </div>
    </form>
</main>

<script>
// Ваш существующий большой скрипт (updateSEO, updateUrlPreview, fillMeta и т.д.)
// ...оставляем как есть...

// Новая функция для вставки от Grok
function pasteFromGrok() {
    const area = document.getElementById('grok-paste-area');
    const text = area.value.trim();
    
    if (!text) {
        alert('Сначала вставьте текст от Grok');
        return;
    }

    const lines = text.split('\n')
                     .map(line => line.trim())
                     .filter(line => line.length > 0);

    let title = '';
    let shortDesc = '';
    let fullDesc = '';

    if (lines.length >= 3) {
        title = lines[0];
        shortDesc = lines[1];
        fullDesc = lines.slice(2).join('\n');
    } else if (lines.length === 2) {
        title = lines[0];
        fullDesc = lines[1];
        shortDesc = fullDesc.substring(0, 160) + (fullDesc.length > 160 ? '...' : '');
    } else if (lines.length === 1) {
        fullDesc = lines[0];
        const firstSentence = fullDesc.split(/[.!?]\s/)[0] || fullDesc.substring(0, 70);
        title = firstSentence.trim() + (firstSentence.length < fullDesc.length ? '...' : '');
        shortDesc = fullDesc.substring(0, 160) + (fullDesc.length > 160 ? '...' : '');
    }

    // Заполняем основные поля
    document.querySelector('[name="title"]').value = title;
    document.querySelector('[name="short_desc"]').value = shortDesc;

    // TinyMCE
    const editor = tinymce.get('full_desc');
    if (editor) {
        editor.setContent(fullDesc);
    } else {
        document.querySelector('[name="full_desc"]').value = fullDesc;
    }

    // Заполняем мета-поля (можно убрать, если не нужно)
    document.querySelector('[name="meta_title"]').value = title;
    document.querySelector('[name="meta_desc"]').value = shortDesc;
    document.querySelector('[name="og_title"]').value = title;
    document.querySelector('[name="og_desc"]').value = shortDesc;

    // Обновляем все прогресс-бары
    ['title', 'short_desc', 'full_desc', 'meta_title', 'meta_desc', 'og_title', 'og_desc']
        .forEach(id => {
            const el = document.getElementById(id);
            if (el) updateSEO(el);
        });

    // Плавный скролл вверх и уведомление
    window.scrollTo({ top: 0, behavior: 'smooth' });
    alert('Новость успешно вставлена в форму!\n\nПроверьте, выберите рубрику и при необходимости добавьте изображения.');

    // area.value = '';  // раскомментировать, если хотите очищать поле после вставки
}
</script>
</body>
</html>