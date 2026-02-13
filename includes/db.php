<?php
// Включаем отображение ошибок (убрать на продакшене)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Подключаем конфигурационный файл с абсолютным путем
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/config.php';

// Подключаемся к базе данных
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

// Устанавливаем кодировку
$conn->set_charset("utf8mb4"); // Используем utf8mb4 для полной поддержки Unicode

// Закрытие соединения при завершении скрипта (опционально)
// register_shutdown_function(function() use ($conn) {
//     $conn->close();
// });
?>