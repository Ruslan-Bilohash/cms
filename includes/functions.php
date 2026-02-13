<?php
// includes/functions.php
// Основные вспомогательные функции проекта
// Последнее обновление: декабрь 2025

/**
 * Определяет текущий язык сайта/админки
 *
 * @return string Код языка (ru, en, ua, lt, no и т.д.)
 */
function getLanguage(): string
{
    $default = 'ru';
    $allowed = ['ru', 'en', 'ua', 'lt', 'no'];

    // Приоритет: GET → SESSION → default
    $lang = $_GET['lang'] ?? ($_SESSION['lang'] ?? $default);

    return in_array($lang, $allowed, true) ? $lang : $default;
}

/**
 * Проверяет, является ли текущий пользователь администратором
 *
 * @return bool
 */
function isAdmin(): bool
{
    return isset($_SESSION['admin']) && $_SESSION['admin'] === true;
}

/**
 * Затемняет HEX-цвет на указанный процент
 *
 * @param string $hex HEX-цвет (#rrggbb)
 * @param float $percent Процент затемнения (0.0–1.0)
 * @return string Новый HEX-цвет
 */
function darken_color(string $hex, float $percent = 0.2): string
{
    $hex = ltrim($hex, '#');
    if (strlen($hex) !== 6) {
        return $hex; // возвращаем исходный, если формат неверный
    }

    $rgb = array_map('hexdec', str_split($hex, 2));
    foreach ($rgb as &$channel) {
        $channel = max(0, min(255, (int)round($channel * (1 - $percent))));
    }

    return sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
}

/**
 * Генерирует безопасный URL-ключ из заголовка
 *
 * @param string $title Исходный заголовок
 * @return string Человекопонятный URL-ключ
 */
function generate_url(string $title): string
{
    $title = mb_strtolower(trim($title), 'UTF-8');
    $title = preg_replace('/[^a-z0-9а-яё]+/iu', '-', $title);
    $title = trim($title, '-');

    return $title ?: 'item-' . time();
}

/**
 * Загружает и обрабатывает изображение (поддержка WebP, множественной загрузки)
 *
 * @param array $file $_FILES элемент
 * @param string $path Путь для сохранения (без завершающего слеша)
 * @param int|null $index Индекс в массиве (для множественной загрузки)
 * @param bool $convert_to_webp Конвертировать в WebP (если возможно)
 * @return string|false Имя сохранённого файла или false
 */
function upload_image(array $file, string $path, ?int $index = null, bool $convert_to_webp = true): string|false
{
    // Создаём директорию, если отсутствует
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }

    $allowed = [
        'image/jpeg' => '.jpg',
        'image/png'  => '.png',
        'image/gif'  => '.gif',
        'application/pdf' => '.pdf',
        'application/msword' => '.doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx'
    ];

    // Определяем данные файла
    $mime = $index === null ? mime_content_type($file['tmp_name']) : mime_content_type($file['tmp_name'][$index]);
    $size = $index === null ? $file['size'] : $file['size'][$index];
    $tmp   = $index === null ? $file['tmp_name'] : $file['tmp_name'][$index];
    $name  = $index === null ? $file['name'] : $file['name'][$index];

    if (!array_key_exists($mime, $allowed) || $size > 5 * 1024 * 1024) {
        return false;
    }

    $ext = $allowed[$mime];
    $filename = uniqid() . '-' . preg_replace('/[^a-z0-9-]/i', '', pathinfo($name, PATHINFO_FILENAME)) . $ext;
    $target = rtrim($path, '/') . '/' . $filename;

    if (!move_uploaded_file($tmp, $target)) {
        return false;
    }

    // Конвертация в WebP (если включено и файл — изображение)
    if ($convert_to_webp && in_array($mime, ['image/jpeg', 'image/png', 'image/gif'], true)) {
        $webp_path = str_replace($ext, '.webp', $target);
        $image = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($target),
            'image/png'  => imagecreatefrompng($target),
            'image/gif'  => imagecreatefromgif($target),
            default => null
        };

        if ($image) {
            imagewebp($image, $webp_path, 80);
            imagedestroy($image);
            unlink($target); // удаляем оригинал
            return basename($webp_path);
        }
    }

    return $filename;
}

/**
 * Проверяет существование изображения
 *
 * @param string $filename Имя файла
 * @param string $path Путь
 * @return bool
 */
function image_exists(string $filename, string $path): bool
{
    return $filename !== '' && file_exists(rtrim($path, '/') . '/' . $filename);
}

/**
 * Получает URL новости
 *
 * @param array $news Массив новости
 * @return string
 */
function get_news_url(array $news): string
{
    $base = '/templates/default/news.php';
    $id = $news['custom_url'] ?: $news['id'] ?? '0';
    return $base . '?id=' . urlencode($id);
}

/**
 * Нормализует URL (убирает протокол, домен, параметры, слеши)
 *
 * @param string $url Исходный URL
 * @return string
 */
function normalize_url(string $url): string
{
    $url = preg_replace('#^https?://[^/]+#i', '', $url);
    $url = strtok($url, '?');
    return rtrim($url, '/');
}

/**
 * Загружает настройки сайта из файла
 *
 * @return array
 */
function get_settings(): array
{
    $file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/site_settings.php';

    if (file_exists($file)) {
        $settings = include $file;
        return is_array($settings) ? $settings : [];
    }

    return [];
}

/**
 * Сохраняет настройки в файл
 *
 * @param array $settings
 * @return bool
 */
function save_settings(array $settings): bool
{
    $file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/site_settings.php';
    $content = '<?php return ' . var_export($settings, true) . ';';
    return file_put_contents($file, $content, LOCK_EX) !== false;
}

/**
 * Строит дерево категорий
 *
 * @param array $categories Полный список категорий
 * @param int|null $parent_id ID родителя
 * @param int $level Уровень вложенности
 * @return array
 */
function buildCategoryTree(array $categories, ?int $parent_id = null, int $level = 0): array
{
    $tree = [];

    foreach ($categories as $cat) {
        if ((int)($cat['parent_id'] ?? -1) === (int)$parent_id) {
            $cat['level'] = $level;
            $tree[] = $cat;

            $children = buildCategoryTree($categories, (int)$cat['id'], $level + 1);
            $tree = array_merge($tree, $children);
        }
    }

    return $tree;
}

/**
 * Проверяет, является ли устройство мобильным
 *
 * @return bool
 */
function isMobileDevice(): bool
{
    $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    return (bool) preg_match(
        '/(android|bb\d+|meego).+mobile|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos/i',
        $agent
    );
}

/**
 * Определяет тип устройства и браузер
 *
 * @param string $userAgent
 * @return array [device, browser]
 */
function detectDeviceAndBrowser(string $userAgent): array
{
    $device = 'desktop';
    $browser = 'unknown';

    // Устройство
    if (preg_match('/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i', $userAgent)) {
        $device = 'tablet';
    } elseif (preg_match('/Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/i', $userAgent)) {
        $device = 'mobile';
    }

    // Браузер
    $browser = match (true) {
        str_contains($userAgent, 'Chrome') || str_contains($userAgent, 'CriOS') => 'Chrome',
        str_contains($userAgent, 'Firefox') || str_contains($userAgent, 'FxiOS') => 'Firefox',
        str_contains($userAgent, 'Safari') && !str_contains($userAgent, 'Chrome') => 'Safari',
        str_contains($userAgent, 'Edg') => 'Edge',
        str_contains($userAgent, 'OPR') || str_contains($userAgent, 'Opera') => 'Opera',
        str_contains($userAgent, 'Trident') || str_contains($userAgent, 'MSIE') => 'Internet Explorer',
        default => 'Unknown'
    };

    return [$device, $browser];
}

/**
 * Загружает переводы для админ-панели
 * @return array
 */
function load_admin_translations(): array
{
    $lang = getLanguage(); // твоя функция getLanguage() должна быть выше
    $base = $_SERVER['DOCUMENT_ROOT'] . '/admin/lang/';
    $file = $base . $lang . '.php';

    if (file_exists($file) && is_readable($file)) {
        $translations = include $file;
        if (is_array($translations)) {
            return $translations;
        }
    }

    // Если не нашли — берём русский
    $fallback = $base . 'ru.php';
    if (file_exists($fallback) && is_readable($fallback)) {
        $translations = include $fallback;
        if (is_array($translations)) {
            return $translations;
        }
    }

    // Если совсем ничего — пустой массив
    error_log("Не удалось загрузить переводы для языка: $lang");
    return [];
}