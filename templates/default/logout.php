<?php
session_start();

// Подключаем зависимости (не обязательно для logout, но добавляем для консистентности)
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Завершаем сессию
session_destroy();

// Перенаправляем на страницу входа
header("Location: /login");
exit;