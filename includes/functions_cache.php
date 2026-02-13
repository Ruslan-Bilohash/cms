<?php

// Загружаем настройки кеша
function get_cache_settings() {
    $cache_file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/site_cache.php';
    $defaults = [
        'cache_enabled' => 0,              // Кеширование отключено
        'default_lifetime' => 3600,        // Время жизни кеша по умолчанию (1 час)
        'default_compress' => 0,           // Сжатие отключено
        'cache_rules' => [],               // Правила кеширования для путей
        'db_cache' => ['all' => 0, 'tables' => []], // Настройки кеша базы данных
        'static_cache' => [],              // Настройки кеша статических файлов
        'external_cache' => [],            // Настройки кеша внешних ресурсов
        'redis_enabled' => 0,              // Redis отключен по умолчанию
        'redis_host' => '127.0.0.1',       // Хост Redis
        'redis_port' => 6379,              // Порт Redis
        'redis_password' => null           // Пароль Redis (если требуется)
    ];

    if (file_exists($cache_file)) {
        $settings = include $cache_file;
        // Объединяем настройки из файла с значениями по умолчанию
        return array_merge($defaults, $settings);
    }
    return $defaults;
}

// Сохранение настроек кеша
function save_cache_settings($settings) {
    $cache_file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/site_cache.php';
    $content = '<?php return ' . var_export($settings, true) . ';';
    return file_put_contents($cache_file, $content) !== false;
}

// Проверка, нужно ли кешировать путь
function should_cache_path($path) {
    $settings = get_cache_settings();
    if (!$settings['cache_enabled']) {
        return false; // Кеширование отключено глобально
    }
    if (isset($settings['cache_rules'][$path])) {
        return $settings['cache_rules'][$path]['enabled']; // Проверяем правило для пути
    }
    return true; // По умолчанию кешируем, если нет специального правила
}

// Получение данных из кеша (только файлы)
function get_from_cache($cache_key, $path) {
    $settings = get_cache_settings();
    $cache_dir = $_SERVER['DOCUMENT_ROOT'] . '/cache' . $path;
    $cache_file = $cache_dir . '/' . $cache_key . '.cache';

    if (!file_exists($cache_file)) {
        return false; // Файл кеша не существует
    }

    $content = file_get_contents($cache_file);
    if ($content === false) {
        return false; // Ошибка чтения файла
    }

    $is_compressed = $settings['default_compress'] || ($settings['cache_rules'][$path]['compress'] ?? false);
    if ($is_compressed) {
        $content = @gzuncompress($content);
        if ($content === false) {
            unlink($cache_file); // Удаляем поврежденный файл
            return false;
        }
    }

    $cache_data = @unserialize($content);
    if ($cache_data === false || !is_array($cache_data) || !isset($cache_data['timestamp'])) {
        unlink($cache_file); // Удаляем поврежденный файл
        return false;
    }

    $lifetime = $settings['cache_rules'][$path]['lifetime'] ?? $settings['default_lifetime'];
    if (time() - $cache_data['timestamp'] > $lifetime) {
        unlink($cache_file); // Удаляем устаревший кеш
        return false;
    }

    return $cache_data['data'];
}

// Сохранение данных в кеш (только файлы)
function save_to_cache($cache_key, $data, $path) {
    $settings = get_cache_settings();
    $cache_dir = $_SERVER['DOCUMENT_ROOT'] . '/cache' . $path;
    $cache_file = $cache_dir . '/' . $cache_key . '.cache';

    if (!is_dir($cache_dir)) {
        mkdir($cache_dir, 0777, true);
    }

    $cache_data = [
        'timestamp' => time(),
        'data' => $data
    ];

    $content = serialize($cache_data);
    if ($settings['default_compress'] || ($settings['cache_rules'][$path]['compress'] ?? false)) {
        $content = gzcompress($content);
    }

    file_put_contents($cache_file, $content);
}

// Кеширование запросов к базе данных
function cache_query($query, $conn, $path = '', $table = null) {
    $settings = get_cache_settings();
    $cache_key = 'db_' . md5($query);
    $should_cache = $settings['db_cache']['all'] || ($table && isset($settings['db_cache']['tables'][$table]) && $settings['db_cache']['tables'][$table]);

    if ($should_cache && should_cache_path($path)) {
        // Проверяем Redis, если файл cache_redis.php подключен
        if (function_exists('get_from_redis_cache')) {
            $cached_result = get_from_redis_cache($cache_key);
            if ($cached_result !== false) {
                return $cached_result;
            }
        }
        // Проверяем файловый кеш
        $cached_result = get_from_cache($cache_key, $path);
        if ($cached_result !== false) {
            return $cached_result;
        }
    }

    try {
        $result = $conn->query($query);
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            if ($should_cache && should_cache_path($path)) {
                if (function_exists('save_to_redis_cache')) {
                    save_to_redis_cache($cache_key, $data, $path);
                } else {
                    save_to_cache($cache_key, $data, $path);
                }
            }
            return $data;
        }
        error_log("Ошибка выполнения запроса: $query");
        return false;
    } catch (Exception $e) {
        error_log("Ошибка в cache_query: " . $e->getMessage() . " | Запрос: $query");
        return false;
    }
}

// Кеширование статических файлов
function cache_static_file($file_path) {
    $settings = get_cache_settings();
    $full_path = $_SERVER['DOCUMENT_ROOT'] . $file_path;
    $cache_key = 'static_' . md5($file_path);
    $cache_path = '/cache/static';

    if (isset($settings['static_cache'][$file_path]) && $settings['static_cache'][$file_path] && should_cache_path($cache_path)) {
        if (function_exists('get_from_redis_cache')) {
            $cached_content = get_from_redis_cache($cache_key);
            if ($cached_content !== false) {
                return $cached_content;
            }
        }
        $cached_content = get_from_cache($cache_key, $cache_path);
        if ($cached_content !== false) {
            return $cached_content;
        }
    }

    if (file_exists($full_path)) {
        $content = file_get_contents($full_path);
        if (isset($settings['static_cache'][$file_path]) && $settings['static_cache'][$file_path] && should_cache_path($cache_path)) {
            if (function_exists('save_to_redis_cache')) {
                save_to_redis_cache($cache_key, $content, $cache_path);
            } else {
                save_to_cache($cache_key, $content, $cache_path);
            }
        }
        return $content;
    }

    error_log("Статический файл не найден: $full_path");
    return '';
}

// Кеширование внешних ресурсов
function cache_external_resource($url, $type) {
    $settings = get_cache_settings();
    $cache_key = 'external_' . md5($url);
    $cache_path = '/cache/external';

    if (isset($settings['external_cache'][$type]) && $settings['external_cache'][$type] && should_cache_path($cache_path)) {
        if (function_exists('get_from_redis_cache')) {
            $cached_content = get_from_redis_cache($cache_key);
            if ($cached_content !== false) {
                return $cached_content;
            }
        }
        $cached_content = get_from_cache($cache_key, $cache_path);
        if ($cached_content !== false) {
            return $cached_content;
        }
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $content = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($content !== false && $http_code == 200) {
        if (isset($settings['external_cache'][$type]) && $settings['external_cache'][$type] && should_cache_path($cache_path)) {
            if (function_exists('save_to_redis_cache')) {
                save_to_redis_cache($cache_key, $content, $cache_path);
            } else {
                save_to_cache($cache_key, $content, $cache_path);
            }
        }
        return $content;
    }

    error_log("Не удалось загрузить внешний ресурс: $url (HTTP код: $http_code)");
    return '';
}

// Очистка всего кеша (только файлы)
function clear_cache($cache_dir) {
    if (function_exists('clear_redis_cache')) {
        clear_redis_cache();
    }

    if (!is_dir($cache_dir)) {
        return true;
    }
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cache_dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }

    return true;
}

// Очистка кеша для конкретного пути (только файлы)
function clear_path_cache($path, $cache_dir) {
    if (function_exists('clear_redis_path_cache')) {
        clear_redis_path_cache($path);
    }

    $specific_cache_dir = $cache_dir . $path;
    if (!is_dir($specific_cache_dir)) {
        return true;
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($specific_cache_dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }

    rmdir($specific_cache_dir);
    return true;
}

// Получение общей статистики кеша (только файлы)
function get_cache_stats($cache_dir) {
    $stats = [
        'size' => 0,
        'files' => 0,
        'last_cleared' => null
    ];

    if (!is_dir($cache_dir)) {
        return $stats;
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cache_dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $fileinfo) {
        $stats['size'] += $fileinfo->getSize();
        $stats['files']++;
        $mtime = $fileinfo->getMTime();
        if ($stats['last_cleared'] === null || $mtime > $stats['last_cleared']) {
            $stats['last_cleared'] = $mtime;
        }
    }

    return $stats;
}

// Получение размера кеша для конкретного пути (только файлы)
function get_path_cache_size($path, $cache_dir) {
    $specific_cache_dir = $cache_dir . $path;
    if (!is_dir($specific_cache_dir)) {
        return 0;
    }

    $size = 0;
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($specific_cache_dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $fileinfo) {
        $size += $fileinfo->getSize();
    }

    return $size;
}

// Получение количества файлов кеша для конкретного пути (только файлы)
function get_cache_file_count($path, $cache_dir) {
    $specific_cache_dir = $cache_dir . $path;
    if (!is_dir($specific_cache_dir)) {
        return 0;
    }

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($specific_cache_dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    return iterator_count($files);
}

// Форматирование размера в читаемый вид
function format_size($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}

?>