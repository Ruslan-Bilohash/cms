-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Час створення: Жов 26 2025 р., 10:53
-- Версія сервера: 11.8.3-MariaDB-log
-- Версія PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `u762384583_tender`
--

-- --------------------------------------------------------

--
-- Структура таблиці `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `role` enum('admin','demo') NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `created_at`, `role`) VALUES
(1, 'admin', '$2y$10$hEkIRlIb9XP3I44yXrzuoO1.RSRABKcZqSPBrH5/CRpQY6SVma2tG', '2025-03-26 07:31:42', 'admin'),
(3, 'demo', '$2y$10$8z4jY8y8Qz8z8y8Qz8z8y8Qz8z8y8Qz8z8y8Qz8z8y8Qz8z8y8Qz', '2025-04-16 00:37:07', 'demo');

-- --------------------------------------------------------

--
-- Структура таблиці `api`
--

CREATE TABLE `api` (
  `id` int(11) NOT NULL,
  `api_type` varchar(50) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `api_secret` varchar(255) DEFAULT NULL,
  `shop_name` varchar(100) DEFAULT '',
  `is_active` tinyint(1) DEFAULT 1,
  `payer_type` varchar(36) DEFAULT 'Recipient',
  `payment_method` varchar(36) DEFAULT 'Cash',
  `service_type_default` varchar(36) DEFAULT 'WarehouseWarehouse',
  `recipient_counterparty_ref` varchar(36) DEFAULT '',
  `default_note` varchar(100) DEFAULT '',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `api`
--

INSERT INTO `api` (`id`, `api_type`, `api_key`, `api_secret`, `shop_name`, `is_active`, `payer_type`, `payment_method`, `service_type_default`, `recipient_counterparty_ref`, `default_note`, `created_at`) VALUES
(143, 'site_settings', '#007bff', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, '2025-04-15 18:11:05'),
(180, 'cash_on_delivery', '', NULL, '', 1, NULL, NULL, NULL, NULL, '', '2025-04-15 21:44:01'),
(181, 'bank_transfer', '', NULL, '', 1, NULL, NULL, NULL, NULL, '', '2025-04-15 21:44:01'),
(182, 'stripe', '', '', '', 1, NULL, NULL, NULL, NULL, '', '2025-04-15 21:44:01'),
(183, 'apple_pay', '', NULL, '', 0, NULL, NULL, NULL, NULL, '', '2025-04-15 21:44:01'),
(184, 'google_pay', '', NULL, '', 0, 'TEST', NULL, NULL, NULL, '', '2025-04-15 21:44:01'),
(185, 'paypal', 'AS-nwNoRn8Nb8ykAtImARv5Oh0cyWLTdhTUtfIAu-BTRcBgK7lhJ_ySdi0IZZ0BhWXwaFd8sUNssWlBz', 'EPVOlk7VrNhFaiuhqhAIAoWEqIsDpL9jJlo1ruSkkjBu318XpGEsuui1rjjIeaFLXZWEfbH7dXiXuoGE', '', 1, '0', NULL, NULL, NULL, '', '2025-04-15 21:44:01'),
(258, 'recaptcha', '6LcCGxorAAAAAGXTokJe-4x5ArdDT0l0DzE0wR0v', '6LcCGxorAAAAAPSoViM2qqz2-GBg9bGum9QMOGNd', '', 1, 'Recipient', 'Cash', 'WarehouseWarehouse', '', '', '2025-04-15 23:07:05');

-- --------------------------------------------------------

--
-- Структура таблиці `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `position` varchar(50) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `pages` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `banner_slider`
--

CREATE TABLE `banner_slider` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `guests` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `bookings`
--

INSERT INTO `bookings` (`id`, `room_id`, `check_in`, `check_out`, `guests`, `name`, `phone`, `status`) VALUES
(3, 13, '2025-04-13', '2025-04-25', 1, '', '', 'pending'),
(4, 13, '2025-04-12', '2025-04-13', 1, 'SEO Analyzer', '+380980252557', 'pending');

-- --------------------------------------------------------

--
-- Структура таблиці `booking_categories`
--

CREATE TABLE `booking_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `booking_categories`
--

INSERT INTO `booking_categories` (`id`, `name`) VALUES
(1, 'Стандартные номера'),
(2, 'Люксы'),
(3, 'Апартаменты');

-- --------------------------------------------------------

--
-- Структура таблиці `brand_carousel`
--

CREATE TABLE `brand_carousel` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `brand_carousel`
--

INSERT INTO `brand_carousel` (`id`, `image_path`, `thumbnail_path`, `link`, `is_active`, `created_at`) VALUES
(5, '/uploads/brand_carousel/67f801eadd308-nike..webp', '/uploads/brand_carousel/67f801eadd308-nike_thumb._thumb.webp', '', 1, '2025-04-10 17:37:48'),
(6, '/uploads/brand_carousel/67f80227dc1b9-adidas..webp', '/uploads/brand_carousel/67f80227dc1b9-adidas_thumb._thumb.webp', '', 1, '2025-04-10 17:38:47'),
(7, '/uploads/brand_carousel/67f80285d0ddc-kstar..webp', '/uploads/brand_carousel/67f80285d0ddc-kstar_thumb._thumb.webp', '', 1, '2025-04-10 17:40:21'),
(8, '/uploads/brand_carousel/67f8040402991-np..webp', '/uploads/brand_carousel/67f8040402991-np_thumb._thumb.webp', '', 1, '2025-04-10 17:46:44'),
(9, '/uploads/brand_carousel/67f8047a2ef0e-dpd..webp', '/uploads/brand_carousel/67f8047a2ef0e-dpd_thumb._thumb.webp', '', 1, '2025-04-10 17:48:42'),
(10, '/uploads/brand_carousel/67f804ca7d328-mysql..webp', '/uploads/brand_carousel/67f804ca7d328-mysql_thumb._thumb.webp', '', 1, '2025-04-10 17:50:02'),
(11, '/uploads/brand_carousel/67f8052b2a429-ajax..webp', '/uploads/brand_carousel/67f8052b2a429-ajax_thumb._thumb.webp', '', 1, '2025-04-10 17:51:39'),
(12, '/uploads/brand_carousel/67fd98d5d89de-stripe..webp', '/uploads/brand_carousel/67fd98d5d89de-stripe_thumb._thumb.webp', '', 1, '2025-04-14 23:23:01'),
(13, '/uploads/brand_carousel/67fd98db38279-paypal..webp', '/uploads/brand_carousel/67fd98db38279-paypal_thumb._thumb.webp', '', 1, '2025-04-14 23:23:07'),
(14, '/uploads/brand_carousel/67fd99230fab7-ukrpochta..webp', '/uploads/brand_carousel/67fd99230fab7-ukrpochta_thumb._thumb.webp', '', 1, '2025-04-14 23:24:19');

-- --------------------------------------------------------

--
-- Структура таблиці `carousel`
--

CREATE TABLE `carousel` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `caption_color` varchar(7) DEFAULT '#ffffff',
  `caption_opacity` decimal(2,1) DEFAULT 0.6,
  `is_active` tinyint(1) DEFAULT 1,
  `display_on_home` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `carousel`
--

INSERT INTO `carousel` (`id`, `image_path`, `thumbnail_path`, `link`, `caption`, `caption_color`, `caption_opacity`, `is_active`, `display_on_home`, `created_at`) VALUES
(25, '/uploads/carousel/67ec13f65d2cc-dashboard..webp', '/uploads/carousel/67ec13f65d2cc-dashboard_thumb._thumb.webp', '', 'Административная часть', '#ffffff', 0.6, 1, 1, '2025-04-01 16:27:34'),
(27, '/uploads/carousel/67ed8aa85f250-page_insight..webp', '/uploads/carousel/67ed8aa85f250-page_insight_thumb._thumb.webp', '', 'SEO Оптимизирован!', '#ffffff', 0.6, 1, 1, '2025-04-02 19:06:16'),
(28, '/uploads/carousel/67edb55a973ef-news_list..webp', '/uploads/carousel/67edb55a973ef-news_list_thumb._thumb.webp', '', 'Отображение новостей в Админ панели', '#ffffff', 0.6, 1, 1, '2025-04-02 22:08:26'),
(30, '/uploads/carousel/67edb624dc58a-hostinger_test2..webp', '/uploads/carousel/67edb624dc58a-hostinger_test2_thumb._thumb.webp', '', 'Тест скорости на стороних сервисах', '#ffffff', 0.6, 1, 1, '2025-04-02 22:11:49'),
(31, '/uploads/carousel/67edb694361bb-add_news_seo_help..webp', '/uploads/carousel/67edb694361bb-add_news_seo_help_thumb._thumb.webp', '', 'Добавлении новостей +SEO помощник', '#ffffff', 0.6, 1, 1, '2025-04-02 22:13:40'),
(32, '/uploads/carousel/67edb94e0c5d8-users_log..webp', '/uploads/carousel/67edb94e0c5d8-users_log_thumb._thumb.webp', '', 'Лог поситителей', '#ffffff', 0.6, 1, 1, '2025-04-02 22:25:18'),
(33, '/uploads/carousel/67edb95ea8e79-feedback_main..webp', '/uploads/carousel/67edb95ea8e79-feedback_main_thumb._thumb.webp', '', 'Обратная связь', '#ffffff', 0.6, 1, 1, '2025-04-02 22:25:34'),
(34, '/uploads/carousel/67ef786a462c8-google_page_ins..webp', '/uploads/carousel/67ef786a462c8-google_page_ins_thumb._thumb.webp', '', 'Скорость страниц', '#ffffff', 0.6, 1, 1, '2025-04-04 06:12:58');

-- --------------------------------------------------------

--
-- Структура таблиці `carousel_settings`
--

CREATE TABLE `carousel_settings` (
  `id` int(11) NOT NULL,
  `speed` int(11) DEFAULT 5000,
  `autoplay` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `carousel_settings`
--

INSERT INTO `carousel_settings` (`id`, `speed`, `autoplay`) VALUES
(1, 5000, 1);

-- --------------------------------------------------------

--
-- Структура таблиці `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `categories`
--

INSERT INTO `categories` (`id`, `title`, `description`, `parent_id`) VALUES
(1, 'Ремонт квартиры или комнаты', NULL, NULL),
(2, 'Строительство дома', NULL, NULL),
(3, 'Плиточные работы', NULL, NULL),
(4, 'Малярные работы', NULL, NULL),
(5, 'Штукатурные работы', NULL, NULL),
(6, 'Монтаж гипсокартона', NULL, NULL),
(7, 'Поклейка обоев', NULL, NULL),
(8, 'Напольные покрытия', NULL, NULL),
(9, 'Потолки', NULL, NULL),
(10, 'Кровельные работы', NULL, NULL),
(11, 'Электромонтажные работы', NULL, NULL),
(12, 'Сантехнические работы', NULL, NULL),
(23, 'IT-услуги', NULL, NULL),
(24, 'Транспорт', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблиці `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `cities`
--

INSERT INTO `cities` (`id`, `name`) VALUES
(1, 'Вильнюс'),
(2, 'Каунас'),
(3, 'Клайпеда'),
(4, 'Шяуляй'),
(5, 'Паневежис'),
(6, 'Алитус'),
(7, 'Мариямполе'),
(8, 'Мажейкяй'),
(9, 'Йонава'),
(10, 'Утена');

-- --------------------------------------------------------

--
-- Структура таблиці `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `documents`
--

INSERT INTO `documents` (`id`, `user_id`, `file`, `title`, `created_at`) VALUES
(1, 1, '/public/uploads/documents/1_1742921928.png', '', '2025-03-25 16:58:48');

-- --------------------------------------------------------

--
-- Структура таблиці `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `type` enum('topic','message') NOT NULL DEFAULT 'message',
  `name` varchar(255) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `topic_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `feedback`
--

INSERT INTO `feedback` (`id`, `type`, `name`, `contact`, `topic_id`, `title`, `message`, `file_path`, `created_at`, `is_read`) VALUES
(1, 'topic', NULL, NULL, NULL, 'Хочу такой сайт', NULL, NULL, '2025-03-29 22:33:24', 0),
(2, 'topic', NULL, NULL, NULL, 'Найдена ошибка', NULL, NULL, '2025-03-29 22:33:33', 0),
(7, 'topic', NULL, NULL, NULL, 'Заказать сайт для компании', NULL, NULL, '2025-03-29 23:11:02', 0),
(8, 'topic', NULL, NULL, NULL, 'SEO Анализ моего сайта', NULL, NULL, '2025-03-29 23:11:50', 0),
(9, 'topic', NULL, NULL, NULL, 'Анализ моего сайта на возможные уязвимости', NULL, NULL, '2025-03-29 23:12:17', 0),
(13, 'topic', NULL, NULL, NULL, 'Заказать доработку модулей', NULL, NULL, '2025-04-02 14:03:19', 0),
(14, 'topic', NULL, NULL, NULL, 'Заказать шаблон для сайта', NULL, NULL, '2025-04-02 14:03:40', 0);

-- --------------------------------------------------------

--
-- Структура таблиці `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `gallery`
--

INSERT INTO `gallery` (`id`, `user_id`, `image`, `title`, `created_at`) VALUES
(1, 1, '/public/uploads/gallery/1_1742921944.png', '', '2025-03-25 16:59:04');

-- --------------------------------------------------------

--
-- Структура таблиці `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `flag` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `languages`
--

INSERT INTO `languages` (`id`, `code`, `name`, `flag`, `is_active`, `is_default`, `created_at`) VALUES
(1, '12', 'en', '', 1, 0, '2025-04-17 20:52:51'),
(2, 'uk', 'Українська', 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/4.1.5/flags/4x3/uk.svg', 1, 0, '2025-04-17 21:20:45');

-- --------------------------------------------------------

--
-- Структура таблиці `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `short_desc` text NOT NULL,
  `full_desc` text DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `custom_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_desc` text DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_desc` text DEFAULT NULL,
  `twitter_title` varchar(255) DEFAULT NULL,
  `twitter_desc` text DEFAULT NULL,
  `published` tinyint(1) DEFAULT 1,
  `reviews_enabled` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `news`
--

INSERT INTO `news` (`id`, `title`, `short_desc`, `full_desc`, `keywords`, `image`, `custom_url`, `created_at`, `category_id`, `meta_title`, `meta_desc`, `og_title`, `og_desc`, `twitter_title`, `twitter_desc`, `published`, `reviews_enabled`) VALUES
(4, 'О программе', 'SEO Анализатор - это инструмент для анализа SEO-параметров веб-страниц. Он помогает оценить, насколько ваш сайт соответствует требованиям поисковых систем', '<h2>О программе</h2>\r\n<p><strong>SEO Анализатор</strong>&nbsp;- это инструмент для анализа SEO-параметров веб-страниц. Он помогает оценить, насколько ваш сайт соответствует требованиям поисковых систем, и выявить области для улучшения.</p>\r\n<h3>Возможности:</h3>\r\n<ul>\r\n<li>Проверка длины заголовка (Title)</li>\r\n<li>Анализ мета-описания, ключевых слов и robots</li>\r\n<li>Подсчет H1 и H2 заголовков</li>\r\n<li>Проверка alt-тегов изображений</li>\r\n<li>Анализ мобильной адаптивности (viewport)</li>\r\n<li>Измерение времени загрузки и размера контента</li>\r\n<li>Анализ ссылок и структуры URL</li>\r\n<li>Проверка социальных мета-тегов</li>\r\n</ul>\r\n<h3>Как использовать:</h3>\r\n<ol>\r\n<li>Введите URL вашего сайта в поле ввода</li>\r\n<li>Нажмите кнопку \"Анализировать\"</li>\r\n<li>Ознакомьтесь с результатами</li>\r\n<li>Зеленый цвет - всё в порядке, красный - требуется оптимизация</li>\r\n</ol>\r\n<div class=\"tips\">\r\n<h3>Советы по оптимизации:</h3>\r\n<ul>\r\n<li><strong>Title:</strong>&nbsp;Держите длину до 60 символов для лучшей видимости в поиске.</li>\r\n<li><strong>Мета-описание:</strong>&nbsp;Оптимально 120-160 символов с ключевыми словами.</li>\r\n<li><strong>H1:</strong>&nbsp;Используйте только один H1 на страницу.</li>\r\n<li><strong>Изображения:</strong>&nbsp;Добавляйте alt-теги для улучшения доступности и SEO.</li>\r\n<li><strong>Скорость:</strong>&nbsp;Оптимизируйте изображения и код, чтобы время загрузки было менее 3 секунд.</li>\r\n<li><strong>Контент:</strong>&nbsp;Стремитесь к объему более 50 KB для информативности.</li>\r\n<li><strong>URL:</strong> Используйте короткие и читаемые ссылки с ключевыми словами.</li>\r\n</ul>\r\n</div>', '', '[\"67e9acb5af03f-screen..jpg\"]', '-', '2025-03-30 20:42:29', 2, 'SEO Анализатор', 'SEO Анализатор - это инструмент для анализа SEO-параметров веб-страниц. Он помогает оценить, насколько ваш сайт соответствует требованиям поисковых систем', 'О программе', 'SEO Анализатор - это инструмент для анализа SEO-параметров веб-страниц. Он помогает оценить, насколько ваш сайт соответствует требованиям поисковых систем', NULL, NULL, 1, 0),
(7, 'URL Security Checker - SQL Injection', 'URL Security Checker - это инструмент для базового анализа безопасности веб-страниц. Скрипт проверить свои ресурсы на наличие потенциальных уязвимостей', '<h2 id=\"help-title\">О программе</h2>\r\n<p id=\"help-desc\"><strong>URL Security Checker</strong>&nbsp;- это инструмент для базового анализа безопасности веб-страниц. Скрипт помогает владельцам сайтов проверить свои ресурсы на наличие потенциальных уязвимостей и повысить уровень защиты.</p>\r\n<h3 id=\"features-title\">Возможности:</h3>\r\n<ul id=\"features-list\">\r\n<li>Проверка на SQL-инъекции</li>\r\n<li>Обнаружение XSS-уязвимостей</li>\r\n<li>Анализ HTTP-заголовков безопасности</li>\r\n<li>Поиск подозрительного контента</li>\r\n<li>Проверка SSL-сертификата</li>\r\n</ul>\r\n<h3 id=\"how-to-title\">Как использовать:</h3>\r\n<ol id=\"how-to-list\">\r\n<li>Введите URL вашего сайта в поле ввода</li>\r\n<li>Нажмите кнопку \"Проверить\"</li>\r\n<li>Ознакомьтесь с результатами анализа</li>\r\n<li>Зеленый цвет - всё в порядке, красный - требуется внимание</li>\r\n</ol>\r\n<h3 id=\"tips-title\">Советы:</h3>\r\n<ul id=\"tips-list\">\r\n<li>Используйте HTTPS для повышения безопасности</li>\r\n<li>Регулярно обновляйте программное обеспечение сайта</li>\r\n<li>Проверяйте заголовки безопасности через инструменты разработчика</li>\r\n</ul>\r\n<p id=\"warning\"><strong>Важно:</strong> Этот инструмент предназначен исключительно для проверки собственных сайтов с целью повышения их безопасности. Использование для анализа чужих ресурсов без разрешения или в злонамеренных целях запрещено!</p>', 'SQL Injection, ', '[\"67ead2752aa65-screenshot..jpg\",\"67ead2752aea9-screenshot2..jpg\",\"67ead2752b1bb-screenshot3..jpg\"]', 'url-security-checker-sql-injection', '2025-03-31 17:35:49', 2, 'URL Security Checker - SQL Injection', 'URL Security Checker - это инструмент для базового анализа безопасности веб-страниц. Скрипт проверить свои ресурсы на наличие потенциальных уязвимостей', 'URL Security Checker - SQL Injection', 'URL Security Checker - это инструмент для базового анализа безопасности веб-страниц. Скрипт проверить свои ресурсы на наличие потенциальных уязвимостей', NULL, NULL, 1, 0),
(13, 'CMS получила высокую оценку от Google PageSpeed Insights', 'CMS Tender получила отличную оценку в Google PageSpeed Insights, подтверждая свою высокую производительность и SEO-оптимизацию в 2025 году. Google рекомендует.', '<p data-pm-slice=\"0 0 []\"><strong>CMS получила высокую оценку от Google PageSpeed Insights<br>Мы полностью провели оптимизацию сайта.</strong></p>\r\n<p>Наша CMS Tender вновь подтверждает свою эффективность и производительность! Недавно сервис Google PageSpeed Insights провел анализ, и результаты показали, что сайты, созданные на базе CMS , соответствуют всем рекомендациям Google по скорости загрузки и оптимизации.</p>\r\n<h2>Высокая производительность и SEO-оптимизация</h2>\r\n<p>Высокие показатели PageSpeed важны как для удобства пользователей, так и для SEO-продвижения. Быстрая загрузка страниц снижает уровень отказов, повышает конверсию и улучшает ранжирование в поисковой выдаче.</p>\r\n<p>Команда разработчиков постоянно работает над улучшением системы, чтобы обеспечить максимальную производительность и удобство использования. Мы гордимся тем, что Google рекомендует нашу платформу!</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>', 'CMS Tender, PageSpeed Insights, Google, оптимизация сайтов, скорость загрузки', '[\"67ef77dbede0d-google_page_ins..jpg\"]', 'cms-tender-google-pagespeed-insights', '2025-04-02 11:09:54', 4, 'CMS Tender получила высокую оценку от Google PageSpeed Insights', 'CMS Tender получила отличную оценку в Google PageSpeed Insights, подтверждая свою высокую производительность и SEO-оптимизацию в 2025 году. Google рекомендует.', 'CMS Tender получила высокую оценку от Google PageSpeed Insights', 'CMS Tender получила отличную оценку в Google PageSpeed Insights, подтверждая свою высокую производительность и SEO-оптимизацию в 2025 году. Google рекомендует.', 'CMS получила высокую оценку от Google PageSpeed Insights', 'CMS Tender получила отличную оценку в Google PageSpeed Insights, подтверждая свою высокую производительность и SEO-оптимизацию в 2025 году. Google рекомендует.', 1, 0),
(14, 'Управление бэкапами MySQL прямо из админ-панели', 'Включите автосохранение, выберите частоту и укажите максимальное количество бэкапов. Для работы автосохранения добавьте в cron:', '<h5>&nbsp;Создание бэкапа</h5>\r\n<p>Нажмите \"Сохранить базу целиком\" для полного бэкапа или выберите таблицы и нажмите \"Создать бэкап\". Файлы сохраняются в&nbsp;<code>/backups/</code>.</p>\r\n<h5>&nbsp;Восстановление бэкапа</h5>\r\n<p>Выберите файл из списка и нажмите \"Восстановить\". Текущая база будет заменена.</p>\r\n<h5>&nbsp;Автосохранение</h5>\r\n<p>Включите автосохранение, выберите частоту и укажите максимальное количество бэкапов. Для работы автосохранения добавьте в cron:</p>\r\n<pre># Каждый час\r\n0 * * * * php /home/u762384583/domains/masterok.lt/public_html/cron/backup.php hourly\r\n\r\n# Каждые 2 часа\r\n0 */2 * * * php /home/u762384583/domains/masterok.lt/public_html/cron/backup.php 2hours\r\n\r\n# Каждые 3 часа\r\n0 */3 * * * php /home/u762384583/domains/masterok.lt/public_html/cron/backup.php 3hours\r\n\r\n# Ежедневно\r\n0 0 * * * php /home/u762384583/domains/masterok.lt/public_html/cron/backup.php daily\r\n\r\n# Еженедельно\r\n0 0 * * 0 php /home/u762384583/domains/masterok.lt/public_html/cron/backup.php weekly\r\n                            </pre>\r\n<p>Создайте файл&nbsp;<code>/home/u762384583/domains/masterok.lt/public_html/cron/backup.php</code>&nbsp;со следующим содержимым:</p>\r\n<pre>&lt;?php\r\nrequire_once \'/home/u762384583/domains/masterok.lt/public_html/includes/db.php\';\r\n$settings = include \'/home/u762384583/domains/masterok.lt/public_html/uploads/site_settings.php\';\r\nif ($settings[\'backup\'][\'auto_backup\'] &amp;&amp; $settings[\'backup\'][\'frequency\'] === $argv[1]) {\r\n    $backup_dir = \'/home/u762384583/domains/masterok.lt/public_html/backups/\';\r\n    $filename = create_backup($conn, $backup_dir); // Полный бэкап\r\n    $backups = glob($backup_dir . \'*.sql\');\r\n    if (count($backups) &gt; $settings[\'backup\'][\'max_backups\']) {\r\n        array_map(\'unlink\', array_slice($backups, 0, count($backups) - $settings[\'backup\'][\'max_backups\']));\r\n    }\r\n}\r\n\r\nfunction create_backup($conn, $backup_dir, $tables = []) {\r\n    $filename = $backup_dir . \'backup_\' . date(\'Ymd_His\') . \'.sql\';\r\n    $sql = \"-- Бэкап базы данных u762384583_tender \" . date(\'Y-m-d H:i:s\') . \"\\n\\n\";\r\n    $result = $conn-&gt;query(\"SHOW TABLES\");\r\n    $all_tables = [];\r\n    while ($row = $result-&gt;fetch_array(MYSQLI_NUM)) {\r\n        $all_tables[] = $row[0];\r\n    }\r\n    $tables_to_backup = empty($tables) ? $all_tables : array_intersect($tables, $all_tables);\r\n    foreach ($tables_to_backup as $table) {\r\n        $sql .= \"-- Таблица: $table\\n\";\r\n        $sql .= \"DROP TABLE IF EXISTS `$table`;\\n\";\r\n        $create_table = $conn-&gt;query(\"SHOW CREATE TABLE `$table`\");\r\n        $row = $create_table-&gt;fetch_assoc();\r\n        $sql .= $row[\'Create Table\'] . \";\\n\\n\";\r\n        $rows = $conn-&gt;query(\"SELECT * FROM `$table`\");\r\n        while ($row = $rows-&gt;fetch_assoc()) {\r\n            $values = array_map(fn($v) =&gt; $conn-&gt;real_escape_string($v === null ? \'NULL\' : $v), $row);\r\n            $sql .= \"INSERT INTO `$table` VALUES (\'\" . implode(\"\',\'\", $values) . \"\');\\n\";\r\n        }\r\n        $sql .= \"\\n\";\r\n    }\r\n    file_put_contents($filename, $sql);\r\n    return $filename;\r\n}\r\n?&gt;</pre>', '', '[\"67efedf18e246-backup_mysql..jpg\"]', 'mysql', '2025-04-04 14:34:25', 4, 'Управление бэкапами MySQL прямо из админ-панели', 'Включите автосохранение, выберите частоту и укажите максимальное количество бэкапов. Для работы автосохранения добавьте в cron:', 'Управление бэкапами MySQL прямо из админ-панели', 'Включите автосохранение, выберите частоту и укажите максимальное количество бэкапов. Для работы автосохранения добавьте в cron:', 'Управление бэкапами MySQL прямо из админ-панели', 'Включите автосохранение, выберите частоту и укажите максимальное количество бэкапов. Для работы автосохранения добавьте в cron:', 1, 0),
(15, 'SEO-Оптимизация для Товаров в CMS: Подробное Руководство', 'Модуль SEO-оптимизации для товаров в CMS позволяет легко настроить все необходимые параметры для продвижения ваших товаров в поисковых системах.', '<p class=\"\" data-start=\"0\" data-end=\"67\">&nbsp;</p>\r\n<p class=\"\" data-start=\"69\" data-end=\"439\"><strong data-start=\"69\" data-end=\"82\">Описание:</strong><br data-start=\"82\" data-end=\"85\">Модуль SEO-оптимизации для товаров в CMS позволяет легко настроить все необходимые параметры для продвижения ваших товаров в поисковых системах. Оптимизация помогает улучшить видимость вашего онлайн-магазина и привлечь больше покупателей. Инструмент позволяет автоматически генерировать ключевые слова, мета-теги и другие важные параметры для SEO.</p>\r\n<h3 class=\"\" data-start=\"441\" data-end=\"470\"><strong data-start=\"445\" data-end=\"470\">Ключевые возможности:</strong></h3>\r\n<ol data-start=\"472\" data-end=\"2306\">\r\n<li class=\"\" data-start=\"472\" data-end=\"913\">\r\n<p class=\"\" data-start=\"475\" data-end=\"509\"><strong data-start=\"475\" data-end=\"509\">Мета-теги Title и Description:</strong></p>\r\n<ul data-start=\"513\" data-end=\"913\">\r\n<li class=\"\" data-start=\"513\" data-end=\"706\">\r\n<p class=\"\" data-start=\"515\" data-end=\"706\"><strong data-start=\"515\" data-end=\"534\">Мета-тег Title:</strong> Этот тег отвечает за название страницы, которое отображается в результатах поисковых систем. Вы можете настроить уникальные и привлекательные заголовки для каждого товара.</p>\r\n</li>\r\n<li class=\"\" data-start=\"710\" data-end=\"913\">\r\n<p class=\"\" data-start=\"712\" data-end=\"913\"><strong data-start=\"712\" data-end=\"737\">Мета-тег Description:</strong> Краткое описание товара, которое появляется под заголовком в поисковой выдаче. Это описание должно быть информативным и побуждающим к действию, чтобы привлекать пользователей.</p>\r\n</li>\r\n</ul>\r\n</li>\r\n<li class=\"\" data-start=\"915\" data-end=\"1140\">\r\n<p class=\"\" data-start=\"918\" data-end=\"948\"><strong data-start=\"918\" data-end=\"948\">Ключевые слова (Keywords):</strong></p>\r\n<ul data-start=\"952\" data-end=\"1140\">\r\n<li class=\"\" data-start=\"952\" data-end=\"1140\">\r\n<p class=\"\" data-start=\"954\" data-end=\"1140\">Введите 3-5 ключевых слов, которые максимально точно описывают товар. Эти слова используются поисковыми системами для определения релевантности страницы в ответ на запросы пользователей.</p>\r\n</li>\r\n</ul>\r\n</li>\r\n<li class=\"\" data-start=\"1142\" data-end=\"1375\">\r\n<p class=\"\" data-start=\"1145\" data-end=\"1375\"><strong data-start=\"1145\" data-end=\"1163\">Кастомные URL:</strong><br data-start=\"1163\" data-end=\"1166\">Модуль позволяет создавать короткие и понятные ссылки для ваших товаров. Например, вместо длинных и сложных адресов, можно использовать простые и SEO-дружелюбные URL, такие как: <code data-start=\"1347\" data-end=\"1374\">example.com/mercedes-e220</code>.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1377\" data-end=\"1612\">\r\n<p class=\"\" data-start=\"1380\" data-end=\"1400\"><strong data-start=\"1380\" data-end=\"1400\">Open Graph (OG):</strong></p>\r\n<ul data-start=\"1404\" data-end=\"1612\">\r\n<li class=\"\" data-start=\"1404\" data-end=\"1612\">\r\n<p class=\"\" data-start=\"1406\" data-end=\"1612\">Настройка Open Graph тегов для улучшенной интеграции с социальными сетями. С помощью этих тегов, при размещении ссылок на товар в социальных сетях, будет отображаться привлекательное изображение и описание.</p>\r\n</li>\r\n</ul>\r\n</li>\r\n<li class=\"\" data-start=\"1614\" data-end=\"1835\">\r\n<p class=\"\" data-start=\"1617\" data-end=\"1835\"><strong data-start=\"1617\" data-end=\"1644\">Анализ SEO для товаров:</strong><br data-start=\"1644\" data-end=\"1647\">Модуль предоставляет отчеты и рекомендации по улучшению SEO для каждого товара. Это включает в себя советы по улучшению мета-тегов, использованию ключевых слов и корректировке описаний.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1837\" data-end=\"2062\">\r\n<p class=\"\" data-start=\"1840\" data-end=\"2062\"><strong data-start=\"1840\" data-end=\"1878\">Автоматическая генерация описаний:</strong> Если у вас нет времени на написание описания для каждого товара, система может предложить шаблоны и автоматическую генерацию контента с учетом ключевых слов и характеристик товара.</p>\r\n</li>\r\n<li class=\"\" data-start=\"2064\" data-end=\"2306\">\r\n<p class=\"\" data-start=\"2067\" data-end=\"2306\"><strong data-start=\"2067\" data-end=\"2105\">Поддержка мультиязычных магазинов:</strong> Модуль SEO в Tender CMS позволяет настроить оптимизацию для разных языков, что особенно важно для международных магазинов. Каждый товар может иметь уникальные мета-теги и описания на разных языках.</p>\r\n</li>\r\n</ol>\r\n<h3 class=\"\" data-start=\"2308\" data-end=\"2333\"><strong data-start=\"2312\" data-end=\"2333\">Как это работает:</strong></h3>\r\n<ol data-start=\"2335\" data-end=\"2955\">\r\n<li class=\"\" data-start=\"2335\" data-end=\"2569\">\r\n<p class=\"\" data-start=\"2338\" data-end=\"2450\">При добавлении нового товара в систему, вам будет предложено ввести информацию, необходимую для SEO-оптимизации:</p>\r\n<ul data-start=\"2454\" data-end=\"2569\">\r\n<li class=\"\" data-start=\"2454\" data-end=\"2471\">\r\n<p class=\"\" data-start=\"2456\" data-end=\"2471\">Название товара</p>\r\n</li>\r\n<li class=\"\" data-start=\"2475\" data-end=\"2502\">\r\n<p class=\"\" data-start=\"2477\" data-end=\"2502\">Краткое и полное описание</p>\r\n</li>\r\n<li class=\"\" data-start=\"2506\" data-end=\"2522\">\r\n<p class=\"\" data-start=\"2508\" data-end=\"2522\">Ключевые слова</p>\r\n</li>\r\n<li class=\"\" data-start=\"2526\" data-end=\"2538\">\r\n<p class=\"\" data-start=\"2528\" data-end=\"2538\">URL товара</p>\r\n</li>\r\n<li class=\"\" data-start=\"2542\" data-end=\"2569\">\r\n<p class=\"\" data-start=\"2544\" data-end=\"2569\">Мета-теги и мета-описание</p>\r\n</li>\r\n</ul>\r\n</li>\r\n<li class=\"\" data-start=\"2571\" data-end=\"2740\">\r\n<p class=\"\" data-start=\"2574\" data-end=\"2740\">После заполнения данных, система автоматически генерирует SEO-оптимизированный контент, включая мета-теги и описание. Вы также получите рекомендации по улучшению SEO.</p>\r\n</li>\r\n<li class=\"\" data-start=\"2742\" data-end=\"2955\">\r\n<p class=\"\" data-start=\"2745\" data-end=\"2955\">Когда товар добавлен в каталог, все SEO-данные (мета-теги, ключевые слова и описание) становятся частью HTML-кода страницы товара, что способствует лучшему индексированию и повышению позиций в поисковой выдаче.</p>\r\n</li>\r\n</ol>\r\n<h3 class=\"\" data-start=\"2957\" data-end=\"3007\"><strong data-start=\"2961\" data-end=\"3007\">Преимущества SEO-оптимизации в Tender CMS:</strong></h3>\r\n<ul data-start=\"3008\" data-end=\"3283\">\r\n<li class=\"\" data-start=\"3008\" data-end=\"3060\">\r\n<p class=\"\" data-start=\"3010\" data-end=\"3060\">Увеличение видимости товаров в поисковых системах.</p>\r\n</li>\r\n<li class=\"\" data-start=\"3061\" data-end=\"3128\">\r\n<p class=\"\" data-start=\"3063\" data-end=\"3128\">Легкость в настройке и управлении SEO-данными для каждого товара.</p>\r\n</li>\r\n<li class=\"\" data-start=\"3129\" data-end=\"3195\">\r\n<p class=\"\" data-start=\"3131\" data-end=\"3195\">Улучшение позиций в поисковой выдаче и повышение кликабельности.</p>\r\n</li>\r\n<li class=\"\" data-start=\"3196\" data-end=\"3283\">\r\n<p class=\"\" data-start=\"3198\" data-end=\"3283\">Возможность интеграции с социальными сетями для улучшения взаимодействия с клиентами.</p>\r\n</li>\r\n</ul>\r\n<p class=\"\" data-start=\"3285\" data-end=\"3606\"><strong data-start=\"3285\" data-end=\"3294\">Итог:</strong><br data-start=\"3294\" data-end=\"3297\">SEO-оптимизация для товаров в CMS &mdash; это мощный инструмент для повышения видимости вашего онлайн-магазина. Благодаря простоте использования и эффективным инструментам, вы сможете легко настроить все необходимые параметры для привлечения большего числа клиентов через поисковые системы и социальные сети.</p>', 'CMS, CMS Shop Free, CMS Интернет магазина, Онлайн шоп CMS', '[\"67f0557328cac-add_product2..jpg\"]', 'seo-cms', '2025-04-04 21:56:03', 4, 'SEO-Оптимизация для Товаров в CMS: Подробное Руководство', 'Модуль SEO-оптимизации для товаров в CMS позволяет легко настроить все необходимые параметры для продвижения ваших товаров в поисковых системах.', 'SEO-Оптимизация для Товаров в Tender CMS: Подробное Руководство', 'Модуль SEO-оптимизации для товаров в Tender CMS позволяет легко настроить все необходимые параметры для продвижения ваших товаров в поисковых системах.', 'SEO-Оптимизация для Товаров в CMS: Подробное Руководство', 'Модуль SEO-оптимизации для товаров в CMS позволяет легко настроить все необходимые параметры для продвижения ваших товаров в поисковых системах.', 1, 0),
(16, 'Модуль магазина CMS - Удобное управление', 'Модуль магазина для Tender CMS предлагает интеграцию с Stripe, настройку различных методов оплаты, управление заказами, добавление товаров и оптимизацию SEO. ', '<p class=\"\" data-start=\"0\" data-end=\"34\"><strong data-start=\"0\" data-end=\"34\">Модуль магазина для Tender CMS</strong></p>\r\n<p class=\"\" data-start=\"36\" data-end=\"270\">Модуль магазина для <strong data-start=\"56\" data-end=\"70\">Tender CMS</strong> предоставляет удобный и функциональный интерфейс для создания и управления онлайн-магазином. Включает в себя различные способы оплаты, возможность добавления товаров, настройки и управления заказами.</p>\r\n<h3 class=\"\" data-start=\"272\" data-end=\"308\"><strong data-start=\"276\" data-end=\"308\">Основные возможности модуля:</strong></h3>\r\n<ol data-start=\"310\" data-end=\"1884\">\r\n<li class=\"\" data-start=\"310\" data-end=\"559\">\r\n<p class=\"\" data-start=\"313\" data-end=\"337\"><strong data-start=\"313\" data-end=\"337\">Оплата через Stripe:</strong></p>\r\n<ul data-start=\"341\" data-end=\"559\">\r\n<li class=\"\" data-start=\"341\" data-end=\"400\">\r\n<p class=\"\" data-start=\"343\" data-end=\"400\">Интеграция с системой Stripe для удобных онлайн-платежей.</p>\r\n</li>\r\n<li class=\"\" data-start=\"404\" data-end=\"470\">\r\n<p class=\"\" data-start=\"406\" data-end=\"470\">Поддержка различных методов оплаты, включая оплату при доставке.</p>\r\n</li>\r\n<li class=\"\" data-start=\"474\" data-end=\"559\">\r\n<p class=\"\" data-start=\"476\" data-end=\"559\">Возможность настроить публичный и секретный ключи Stripe для безопасности платежей.</p>\r\n</li>\r\n</ul>\r\n</li>\r\n<li class=\"\" data-start=\"561\" data-end=\"647\">\r\n<p class=\"\" data-start=\"564\" data-end=\"587\"><strong data-start=\"564\" data-end=\"587\">Банковский перевод:</strong></p>\r\n<ul data-start=\"591\" data-end=\"647\">\r\n<li class=\"\" data-start=\"591\" data-end=\"647\">\r\n<p class=\"\" data-start=\"593\" data-end=\"647\">Включение возможности оплаты через банковский перевод.</p>\r\n</li>\r\n</ul>\r\n</li>\r\n<li class=\"\" data-start=\"649\" data-end=\"926\">\r\n<p class=\"\" data-start=\"652\" data-end=\"676\"><strong data-start=\"652\" data-end=\"676\">Управление заказами:</strong></p>\r\n<ul data-start=\"680\" data-end=\"926\">\r\n<li class=\"\" data-start=\"680\" data-end=\"783\">\r\n<p class=\"\" data-start=\"682\" data-end=\"783\">Просмотр всех заказов с фильтрами по статусу, имени покупателя, категории товара и другим параметрам.</p>\r\n</li>\r\n<li class=\"\" data-start=\"787\" data-end=\"926\">\r\n<p class=\"\" data-start=\"789\" data-end=\"926\">Для каждого заказа отображаются подробности: товар, категория, заказ, покупатель, телефон, дата и время, цена, доставка, оплата и статус.</p>\r\n</li>\r\n</ul>\r\n</li>\r\n<li class=\"\" data-start=\"928\" data-end=\"1237\">\r\n<p class=\"\" data-start=\"931\" data-end=\"954\"><strong data-start=\"931\" data-end=\"954\">Добавление товаров:</strong></p>\r\n<ul data-start=\"958\" data-end=\"1237\">\r\n<li class=\"\" data-start=\"958\" data-end=\"1070\">\r\n<p class=\"\" data-start=\"960\" data-end=\"1070\">Легкость добавления новых товаров с указанием категории, названия, цены, статуса, краткого и полного описания.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1074\" data-end=\"1116\">\r\n<p class=\"\" data-start=\"1076\" data-end=\"1116\">Возможность загрузки изображений товара.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1120\" data-end=\"1160\">\r\n<p class=\"\" data-start=\"1122\" data-end=\"1160\">Настройка кастомной ссылки для товара.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1164\" data-end=\"1237\">\r\n<p class=\"\" data-start=\"1166\" data-end=\"1237\">Возможность задать мета-теги (Title и Description) для оптимизации SEO.</p>\r\n</li>\r\n</ul>\r\n</li>\r\n<li class=\"\" data-start=\"1239\" data-end=\"1429\">\r\n<p class=\"\" data-start=\"1242\" data-end=\"1265\"><strong data-start=\"1242\" data-end=\"1265\">Настройки магазина:</strong></p>\r\n<ul data-start=\"1269\" data-end=\"1429\">\r\n<li class=\"\" data-start=\"1269\" data-end=\"1312\">\r\n<p class=\"\" data-start=\"1271\" data-end=\"1312\">Включение/выключение магазина для работы.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1316\" data-end=\"1401\">\r\n<p class=\"\" data-start=\"1318\" data-end=\"1401\">Настройка лимита записей на странице (от 1 до 100 записей) для удобства управления.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1405\" data-end=\"1429\">\r\n<p class=\"\" data-start=\"1407\" data-end=\"1429\">Выбор валюты магазина.</p>\r\n</li>\r\n</ul>\r\n</li>\r\n<li class=\"\" data-start=\"1431\" data-end=\"1643\">\r\n<p class=\"\" data-start=\"1434\" data-end=\"1460\"><strong data-start=\"1434\" data-end=\"1460\">Информационная панель:</strong></p>\r\n<ul data-start=\"1464\" data-end=\"1643\">\r\n<li class=\"\" data-start=\"1464\" data-end=\"1535\">\r\n<p class=\"\" data-start=\"1466\" data-end=\"1535\">Панель управления позволяет легко отслеживать текущие и новые заказы.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1539\" data-end=\"1643\">\r\n<p class=\"\" data-start=\"1541\" data-end=\"1643\">Доступ к полному списку товаров с возможностью фильтрации по названию, цене, категории и популярности.</p>\r\n</li>\r\n</ul>\r\n</li>\r\n<li class=\"\" data-start=\"1645\" data-end=\"1772\">\r\n<p class=\"\" data-start=\"1648\" data-end=\"1671\"><strong data-start=\"1648\" data-end=\"1671\">Категории и города:</strong></p>\r\n<ul data-start=\"1675\" data-end=\"1772\">\r\n<li class=\"\" data-start=\"1675\" data-end=\"1772\">\r\n<p class=\"\" data-start=\"1677\" data-end=\"1772\">Возможность создания и управления категориями товаров, а также добавление городов для доставки.</p>\r\n</li>\r\n</ul>\r\n</li>\r\n<li class=\"\" data-start=\"1774\" data-end=\"1884\">\r\n<p class=\"\" data-start=\"1777\" data-end=\"1798\"><strong data-start=\"1777\" data-end=\"1798\">Настройки оплаты:</strong></p>\r\n<ul data-start=\"1802\" data-end=\"1884\">\r\n<li class=\"\" data-start=\"1802\" data-end=\"1884\">\r\n<p class=\"\" data-start=\"1804\" data-end=\"1884\">Простая настройка различных методов оплаты, включая Stripe и банковский перевод.</p>\r\n</li>\r\n</ul>\r\n</li>\r\n</ol>\r\n<h3 class=\"\" data-start=\"1886\" data-end=\"1917\"><strong data-start=\"1890\" data-end=\"1917\">Интерфейс пользователя:</strong></h3>\r\n<p class=\"\" data-start=\"1918\" data-end=\"2161\">Модуль предоставляет удобный и интуитивно понятный интерфейс для владельцев магазинов, позволяя быстро управлять товарами, заказами и настройками магазина. Весь процесс &mdash; от добавления товара до обработки заказа &mdash; стал намного проще и быстрее.</p>\r\n<h3 class=\"\" data-start=\"2163\" data-end=\"2189\"><strong data-start=\"2167\" data-end=\"2189\">Доступные функции:</strong></h3>\r\n<ul data-start=\"2190\" data-end=\"2350\">\r\n<li class=\"\" data-start=\"2190\" data-end=\"2235\">\r\n<p class=\"\" data-start=\"2192\" data-end=\"2235\">Управление товаром, категориями и заказами.</p>\r\n</li>\r\n<li class=\"\" data-start=\"2236\" data-end=\"2301\">\r\n<p class=\"\" data-start=\"2238\" data-end=\"2301\">Интеграция с платёжными системами (Stripe, банковский перевод).</p>\r\n</li>\r\n<li class=\"\" data-start=\"2302\" data-end=\"2350\">\r\n<p class=\"\" data-start=\"2304\" data-end=\"2350\">Полная настройка параметров магазина и валюты.</p>\r\n</li>\r\n</ul>\r\n<hr class=\"\" data-start=\"2352\" data-end=\"2355\">\r\n<p class=\"\" data-start=\"2460\" data-end=\"2705\">&nbsp;</p>', 'CMS онлайн-магазин, Stripe интеграция, управление товарами, SEO оптимизация, методы оплаты Stripe', '[\"67f0568b88bbf-add_product..jpg\",\"67f0568b88faf-add_product2..jpg\",\"67f0568b89380-category..jpg\",\"67f0568b896a6-new_order..jpg\",\"67f0568b898c7-product..jpg\",\"67f0568b89b2b-setting..jpg\",\"67f0568b89d31-stripe..jpg\",\"67f1c4cfbe33d-shop_google_page..jpg\"]', 'shop-module', '2025-04-04 22:00:43', 4, 'Модуль магазина для Tender CMS - Удобное управление', 'Модуль магазина для Tender CMS предлагает интеграцию с Stripe, настройку различных методов оплаты, управление заказами, добавление товаров и оптимизацию SEO. ', 'Модуль магазина для Tender CMS - Удобное управление', 'Модуль магазина для Tender CMS предлагает интеграцию с Stripe, настройку различных методов оплаты, управление заказами, добавление товаров и оптимизацию SEO. ', 'Модуль магазина для Tender CMS - Удобное управление', 'Модуль магазина для Tender CMS предлагает интеграцию с Stripe, настройку различных методов оплаты, управление заказами, добавление товаров и оптимизацию SEO. ', 1, 0),
(17, 'Управление кешем', 'Этот инструмент разработан для того, чтобы ваш сайт работал быстрее, эффективнее и надёжнее, предоставляя вам полный контроль над процессом кеширования данных', '<div>\r\n<p class=\"break-words\" dir=\"auto\">Новый модуль \"Управление кешем\" в CMS Pro Website Management: ускорение сайта на новом уровне<br><br>demo: /admin/index.php?module=cache</p>\r\n<p class=\"break-words\" dir=\"auto\">Дорогие пользователи CMS Pro Website Management! Мы с гордостью представляем вам долгожданное обновление нашей системы &mdash; новый модуль \"Управление кешем\". Этот инструмент разработан для того, чтобы ваш сайт работал быстрее, эффективнее и надёжнее, предоставляя вам полный контроль над процессом кеширования данных. Теперь оптимизация производительности вашего веб-ресурса становится проще и доступнее, чем когда-либо. Давайте разберёмся, что предлагает этот модуль и как он может улучшить ваш опыт управления сайтом.</p>\r\n<p class=\"break-words\" dir=\"auto\">Что такое \"Управление кешем\" и зачем оно нужно?<br>Кеширование &mdash; это технология, которая позволяет временно сохранять часто используемые данные, чтобы ускорить их загрузку при последующих запросах. Это особенно важно для сайтов с большим количеством контента, динамическими страницами или высокой посещаемостью. Новый модуль \"Управление кешем\" в CMS Pro Website Management поднимает эту технологию на новый уровень, предоставляя гибкие настройки и подробную статистику. Он помогает снизить нагрузку на сервер, сократить время отклика и улучшить пользовательский опыт ваших посетителей.</p>\r\n<p class=\"break-words\" dir=\"auto\">Глобальные настройки для полного контроля<br>Модуль включает в себя раздел глобальных настроек, где вы можете управлять основными параметрами кеширования. Активируйте функцию кеширования одним нажатием кнопки, настройте время жизни кеша по умолчанию (мы рекомендуем 3600 секунд, но вы можете выбрать любое значение в зависимости от ваших задач), а также включите сжатие кеша. Сжатие позволяет значительно уменьшить объём хранимых данных, что особенно полезно для сайтов с ограниченным дисковым пространством или высокой интенсивностью обновлений.</p>\r\n<p class=\"break-words\" dir=\"auto\">Оптимизация базы данных: от общего к частному<br>Одной из ключевых особенностей модуля является возможность кеширования базы данных. Вы можете выбрать, кешировать ли всю базу целиком для максимальной скорости загрузки сайта, или настроить выборочное кеширование отдельных таблиц. Мы предусмотрели поддержку всех основных разделов вашей CMS:</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Карусель: для быстрого отображения слайдеров и баннеров.</li>\r\n<li class=\"break-words\">Страницы: ускорение загрузки статического контента.</li>\r\n<li class=\"break-words\">Товары: оптимизация каталогов интернет-магазинов.</li>\r\n<li class=\"break-words\">Тендеры: мгновенный доступ к данным тендерных разделов.</li>\r\n<li class=\"break-words\">Логи посетителей: ускорение аналитики и статистики.</li>\r\n<li class=\"break-words\">Галерея: быстрая загрузка изображений и альбомов.</li>\r\n<li class=\"break-words\">Новости: оперативное отображение актуальных публикаций.<br>Такая гибкость позволяет вам сосредоточиться на тех данных, которые наиболее важны для вашего сайта, не перегружая систему лишними операциями.</li>\r\n</ul>\r\n<p class=\"break-words\" dir=\"auto\">Кеширование статических файлов: ускорение ресурсов<br>Модуль \"Управление кешем\" также оптимизирует работу со статическими файлами, которые являются основой любого сайта. Теперь основные ресурсы, такие как:</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Основной JavaScript (/templates/default/js/js.js),</li>\r\n<li class=\"break-words\">Основной CSS (/templates/default/css/style.css),</li>\r\n<li class=\"break-words\">Динамический CSS (/templates/default/css/style.php),<br>будут загружаться быстрее благодаря кешированию. Это сокращает время рендеринга страниц и улучшает восприятие сайта вашими посетителями.</li>\r\n</ul>\r\n<p class=\"break-words\" dir=\"auto\">Поддержка внешних ресурсов: шрифты и иконки<br>Для сайтов, использующих внешние ресурсы, мы добавили возможность их кеширования. Шрифты, такие как Google Fonts или кастомные наборы, теперь можно сохранить локально, чтобы уменьшить зависимость от внешних серверов и ускорить их загрузку. То же касается иконок &mdash; будь то Font Awesome или ваши собственные библиотеки, модуль обеспечит их мгновенное отображение даже при медленном интернет-соединении.</p>\r\n<p class=\"break-words\" dir=\"auto\">Как это работает на практике?<br>После активации модуля \"Управление кешем\" вы получите доступ к удобному интерфейсу в админ-панели CMS Pro Website Management. Здесь вы сможете включать или отключать кеширование, задавать параметры для отдельных разделов, а также отслеживать статистику использования кеша. Настройки интуитивно понятны и не требуют глубоких технических знаний &mdash; мы постарались сделать процесс максимально простым и удобным для всех пользователей, от новичков до опытных администраторов.</p>\r\n<p class=\"break-words\" dir=\"auto\">Преимущества, которые вы заметите сразу<br>Использование модуля \"Управление кешем\" принесёт вашему сайту ощутимые улучшения:</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Скорость загрузки страниц возрастёт в разы, что особенно важно для мобильных пользователей и SEO-оптимизации.</li>\r\n<li class=\"break-words\">Нагрузка на сервер снизится, позволяя вашему сайту выдерживать большее количество посетителей без дополнительных затрат на хостинг.</li>\r\n<li class=\"break-words\">Гибкость настроек даст вам возможность адаптировать кеширование под уникальные потребности вашего проекта, будь то блог, интернет-магазин или корпоративный портал.</li>\r\n</ul>\r\n<p class=\"break-words\" dir=\"auto\">Готовы попробовать?<br>Новый модуль \"Управление кешем\" уже доступен в последней версии CMS Pro Website Management. Обновите вашу систему через админ-панель или скачайте актуальную версию с нашего сайта. Подробные инструкции по установке и настройке вы найдёте в документации &mdash; мы сделали всё, чтобы процесс был максимально прозрачным.</p>\r\n<p class=\"break-words\" dir=\"auto\">Оптимизируйте свой сайт с CMS Pro Website Management и новым модулем \"Управление кешем\" уже сегодня. Быстрый, надёжный и современный веб-ресурс &mdash; это то, что заслуживают ваши пользователи!</p>\r\n</div>', 'Управление кешем, Кеширование базы данных, Кеширование статических файлов, Кеширование внешних ресурсов', '[\"67f7d14cb7fa0-main..jpg\",\"67f7d14cb82db-mysql..jpg\",\"67f7d14cb8571-reseorse-fonts..jpg\",\"67f7d14cb8884-static..jpg\",\"67f7d14cb8b89-test_speed..jpg\"]', 'cache', '2025-04-10 14:10:20', 4, 'Управление кешем', 'Этот инструмент разработан для того, чтобы ваш сайт работал быстрее, эффективнее и надёжнее, предоставляя вам полный контроль над процессом кеширования данных', 'Управление кешем', 'Этот инструмент разработан для того, чтобы ваш сайт работал быстрее, эффективнее и надёжнее, предоставляя вам полный контроль над процессом кеширования данных', 'Управление кешем', 'Этот инструмент разработан для того, чтобы ваш сайт работал быстрее, эффективнее и надёжнее, предоставляя вам полный контроль над процессом кеширования данных', 1, 0),
(18, 'Управление страницами', 'Мы рады объявить о запуске нового модуля Управление страницами который станет вашим незаменимым помощником в создании, редактировании и организации контента.', '<p class=\"break-words\" dir=\"auto\">CMS Pro Website Management: полный контроль над контентом вашего сайта.<br>demo:</p>\r\n<p class=\"break-words\" dir=\"auto\">Дорогие пользователи CMS Pro Website Management! Мы рады объявить о запуске нового модуля \"Управление страницами\", который станет вашим незаменимым помощником в создании, редактировании и организации контента. Этот модуль разработан для того, чтобы вы могли с лёгкостью управлять всеми страницами вашего сайта, будь то главная страница, информационные разделы или динамические материалы. Давайте расскажем, как \"Управление страницами\" сделает вашу работу удобнее, а сайт &mdash; ещё более функциональным.</p>\r\n<p class=\"break-words\" dir=\"auto\">Что такое \"Управление страницами\"?<br>Модуль \"Управление страницами\" &mdash; это мощный инструмент, который позволяет централизованно управлять всеми страницами вашего сайта. Он идеально подходит как для небольших проектов, где важна простота, так и для крупных порталов с сотнями страниц. С его помощью вы сможете добавлять новые страницы, редактировать существующие, задавать их структуру и управлять отображением &mdash; всё это через удобный интерфейс в админ-панели CMS Pro Website Management.</p>\r\n<p class=\"break-words\" dir=\"auto\">Широкие возможности для создания контента<br>С новым модулем вы получите полный набор инструментов для работы со страницами:</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Создание страниц с нуля: задавайте заголовки, URL, содержимое и мета-теги в несколько кликов.</li>\r\n<li class=\"break-words\">Редактирование в реальном времени: вносите изменения в текст, изображения или прикреплённые файлы без необходимости перезагрузки сайта.</li>\r\n<li class=\"break-words\">Гибкая настройка URL: создавайте красивые и понятные ссылки (например, /about или /services), которые легко читаются пользователями и поисковыми системами.</li>\r\n<li class=\"break-words\">Поддержка вложенности: организуйте страницы в иерархию, создавая подкатегории для лучшей структуры сайта.</li>\r\n</ul>\r\n<p class=\"break-words\" dir=\"auto\">Продуманная организация и навигация<br>\"Управление страницами\" помогает не только создавать контент, но и держать его в порядке. Вы сможете:</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Просматривать полный список страниц с сортировкой по дате, заголовку или статусу.</li>\r\n<li class=\"break-words\">Устанавливать статус публикации: публикуйте страницу сразу, сохраняйте как черновик или планируйте её выход на определённую дату.</li>\r\n<li class=\"break-words\">Добавлять ветку навигации (breadcrumbs): модуль автоматически генерирует цепочку ссылок, например, \"Все страницы &gt; Название страницы\", для удобства пользователей.</li>\r\n</ul>\r\n<p class=\"break-words\" dir=\"auto\">Интеграция с другими функциями CMS<br>Модуль \"Управление страницами\" полностью совместим с остальными возможностями CMS Pro Website Management. Например:</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Кеширование: используйте модуль \"Управление кешем\", чтобы ускорить загрузку страниц.</li>\r\n<li class=\"break-words\">SEO-оптимизация: задавайте мета-описания и ключевые слова для каждой страницы, чтобы улучшить её позиции в поисковых системах.</li>\r\n<li class=\"break-words\">Шаблоны: применяйте готовые шаблоны из папки /templates/default/ или создавайте свои для уникального дизайна.</li>\r\n</ul>\r\n<p class=\"break-words\" dir=\"auto\">Простота и удобство для всех<br>Мы постарались сделать модуль максимально интуитивным. Интерфейс \"Управления страницами\" в админ-панели прост и понятен даже для начинающих пользователей. Вам не нужно быть разработчиком, чтобы добавить новую страницу или изменить существующую &mdash; достаточно нескольких минут, чтобы освоиться. Для опытных администраторов доступны расширенные настройки, такие как управление правами доступа или настройка дополнительных полей для страниц.</p>\r\n<p class=\"break-words\" dir=\"auto\">Преимущества для вашего сайта<br>С внедрением модуля \"Управление страницами\" вы заметите сразу несколько улучшений:</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Быстрое обновление контента: любые изменения на сайте теперь занимают минимум времени.</li>\r\n<li class=\"break-words\">Улучшенная структура: чёткая организация страниц делает ваш сайт удобнее для посетителей и поисковых роботов.</li>\r\n<li class=\"break-words\">Повышение гибкости: создавайте столько страниц, сколько нужно, без ограничений и сложных настроек.</li>\r\n<li class=\"break-words\">Экономия времени: централизованное управление избавляет от необходимости работать с разрозненными инструментами.</li>\r\n</ul>\r\n<p class=\"break-words\" dir=\"auto\">Как начать использовать?<br>Модуль \"Управление страницами\" уже доступен в последней версии CMS Pro Website Management. Просто обновите вашу систему через админ-панель или скачайте обновление с нашего официального сайта. После установки вы найдёте новый раздел в меню админ-панели &mdash; \"Управление страницами\". Оттуда вы сможете сразу приступить к работе: добавлять страницы, редактировать их или просматривать статистику. Подробные инструкции и советы по использованию доступны в нашей обновлённой документации &mdash; мы сделали всё, чтобы ваш старт был комфортным.</p>\r\n<p class=\"break-words\" dir=\"auto\">Будущее вашего сайта начинается сегодня<br>CMS Pro Website Management продолжает развиваться, чтобы предоставлять вам лучшие инструменты для управления сайтом. Новый модуль \"Управление страницами\" &mdash; это ещё один шаг к тому, чтобы ваш веб-ресурс стал современным, удобным и эффективным. Создавайте страницы, которые вдохновляют, информируют и привлекают ваших посетителей, с лёгкостью и уверенностью.</p>\r\n<p class=\"break-words\" dir=\"auto\">Обновите CMS Pro Website Management и откройте для себя все преимущества \"Управления страницами\" уже сегодня! Ваш сайт заслуживает самого лучшего &mdash; и мы здесь, чтобы это обеспечить.</p>\r\n<p class=\"break-words\" dir=\"auto\">Дата публикации: 10 апреля 2025</p>\r\n<hr>\r\n<h3 class=\"\" dir=\"auto\">Как добавить в вашу CMS:</h3>\r\n<ol class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Перейдите в админ-панель (например, <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">/admin/index.php?module=page</span>).</li>\r\n<li class=\"break-words\">Создайте новую страницу:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Заголовок</strong>: \"Новый модуль \'Управление страницами\' в CMS Pro Website Management\".</li>\r\n<li class=\"break-words\"><strong>URL</strong>: Например, <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">page-management-news</span>.</li>\r\n<li class=\"break-words\"><strong>Контент</strong>: Вставьте текст выше.</li>\r\n<li class=\"break-words\"><strong>Дата публикации</strong>: \"10 апреля 2025\".</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\">Сохраните и проверьте страницу (например, <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">/page-management-news</span> или <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">/page-management-news/</span>).</li>\r\n</ol>', 'Управление страницами, Website Management, Сайт визитка, Скрипт управления страницами', '[\"67f7dc4a2eb00-1..jpg\",\"67f7dc4a2ef21-1en..jpg\",\"67f7dc4a2f22b-2..jpg\",\"67f7dc4a2fbc5-2en..jpg\",\"67f7dc4a30007-main-site-page..jpg\"]', 'service-page-control', '2025-04-10 14:57:14', 4, 'Управление страницами', 'Мы рады объявить о запуске нового модуля \\\\\\\"Управление страницами\\\\\\\", который станет вашим незаменимым помощником в создании, редактировании и организации контента.', 'Управление страницами', 'Мы рады объявить о запуске нового модуля \\\\\\\"Управление страницами\\\\\\\", который станет вашим незаменимым помощником в создании, редактировании и организации контента.', 'Управление страницами', 'Мы рады объявить о запуске нового модуля \\\\\\\"Управление страницами\\\\\\\", который станет вашим незаменимым помощником в создании, редактировании и организации контента.', 1, 0);
INSERT INTO `news` (`id`, `title`, `short_desc`, `full_desc`, `keywords`, `image`, `custom_url`, `created_at`, `category_id`, `meta_title`, `meta_desc`, `og_title`, `og_desc`, `twitter_title`, `twitter_desc`, `published`, `reviews_enabled`) VALUES
(19, 'Модуль Настройка API', 'Модуль Настройка API  Включает поддержку текстового редактора TinyMCE, защиты Google reCAPTCHA, а также авторизации через Google, Telegram и Facebook.', '<p><strong><span style=\"background-color: rgb(45, 194, 107);\">demo</span>: <a href=\"index.php?module=api\">/admin/index.php?module=api</a></strong></p>\r\n<div>\r\n<h3 class=\"\" dir=\"auto\">Модуль настроек API в Pro Website Management</h3>\r\n<p class=\"break-words\" dir=\"auto\">Модуль <strong>\"Настройка API\"</strong> в CMS Pro Website Management позволяет подключать и настраивать интеграции с внешними сервисами для расширения функциональности вашего сайта. Включает поддержку текстового редактора TinyMCE, защиты Google reCAPTCHA, а также авторизации через Google, Telegram и Facebook.</p>\r\n<h4 class=\"\" dir=\"auto\">TinyMCE</h4>\r\n<p class=\"break-words\" dir=\"auto\">Интеграция с текстовым редактором TinyMCE для удобного создания и редактирования контента.</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>API ключ TinyMCE</strong>:<br>Поле для ввода вашего API ключа TinyMCE.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">7828719865:AAGAYuhUTItwvW6wW1PA4nR4X--S41bFmUg</span><br><em>Инструкция</em>: Получите ключ на официальном сайте TinyMCE и вставьте его сюда.</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Google reCAPTCHA</h4>\r\n<p class=\"break-words\" dir=\"auto\">Настройка Google reCAPTCHA для защиты форм от спама и ботов.</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Site Key</strong>:<br>Поле для ввода публичного ключа reCAPTCHA.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">6LdofAorAAAAAIfytSRx4PjPSMddxaICHUW9-gC7</span></li>\r\n<li class=\"break-words\"><strong>Secret Key</strong>:<br>Поле для ввода секретного ключа reCAPTCHA.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">6LdofAorAAAAAPTEZbOxqk_Tt9y_qdrI1tqeNpVk</span><br><em>Инструкция</em>: Зарегистрируйтесь в Google reCAPTCHA, чтобы получить оба ключа.</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Google Login</h4>\r\n<p class=\"break-words\" dir=\"auto\">Настройка авторизации через Google для пользователей сайта.</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Client ID</strong>:<br>Поле для ввода идентификатора клиента Google.<br><em>Инструкция</em>: Создайте приложение в Google Developer Console и скопируйте Client ID.</li>\r\n<li class=\"break-words\"><strong>Client Secret</strong>:<br>Поле для ввода секретного ключа клиента Google.<br><em>Инструкция</em>: Получите его в Google Developer Console вместе с Client ID.</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Telegram Login</h4>\r\n<p class=\"break-words\" dir=\"auto\">Интеграция авторизации через Telegram с использованием бота.</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Bot Token</strong>:<br>Поле для ввода токена Telegram-бота.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">7828719865:AAGAYuhUTItwvW6wW1PA4nR4X--S41bFmUg</span><br><em>Инструкция</em>: Создайте бота через @BotFather в Telegram и вставьте токен.</li>\r\n<li class=\"break-words\"><strong>Bot Username</strong>:<br>Поле для ввода имени Telegram-бота.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">@tender_cms_bot</span><br><em>Примечание</em>: Укажите имя в формате <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">@username</span>.</li>\r\n<li class=\"break-words\"><strong>Включить Telegram Login</strong>:<br>Переключатель для активации кнопки Telegram на странице входа.<br><em>Примечание</em>: Включите, чтобы отобразить кнопку авторизации.</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Facebook Login</h4>\r\n<p class=\"break-words\" dir=\"auto\">Настройка авторизации через Facebook.</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>App ID</strong>:<br>Поле для ввода идентификатора приложения Facebook.<br><em>Инструкция</em>: Создайте приложение в Facebook Developer Portal и скопируйте App ID.</li>\r\n<li class=\"break-words\"><strong>App Secret</strong>:<br>Поле для ввода секретного ключа приложения Facebook.<br><em>Инструкция</em>: Получите его в Facebook Developer Portal вместе с App ID.</li>\r\n</ul>\r\n<hr>\r\n<h3 class=\"\" dir=\"auto\">Как использовать модуль</h3>\r\n<ol class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Перейдите в раздел <strong>\"Настройка API\"</strong> в админ-панели CMS.</li>\r\n<li class=\"break-words\">Введите необходимые ключи и токены в соответствующие поля.</li>\r\n<li class=\"break-words\">Сохраните изменения, чтобы активировать интеграции.</li>\r\n<li class=\"break-words\">Проверьте работу функций (например, кнопку входа или reCAPTCHA) на сайте.</li>\r\n</ol>\r\n<p class=\"break-words\" dir=\"auto\">Модуль \"Настройка API\" предоставляет гибкие возможности для интеграции с популярными сервисами, обеспечивая удобство и безопасность работы вашего сайта в рамках CMS Pro Website Management.</p>\r\n</div>', 'Настройка API, API, reCAPTCHA, Google Login, Telegram Login, TinyMCE, Stripe', '[\"67f7e177627ad-api1..jpg\",\"67f7e17762c0c-api2..jpg\",\"67f7e17762f62-api3..jpg\"]', 'api', '2025-04-10 15:19:19', 4, 'Модуль Настройка API', 'Модуль Настройка API  Включает поддержку текстового редактора TinyMCE, защиты Google reCAPTCHA, а также авторизации через Google, Telegram и Facebook.', 'Модуль Настройка API', 'Модуль Настройка API  Включает поддержку текстового редактора TinyMCE, защиты Google reCAPTCHA, а также авторизации через Google, Telegram и Facebook.', 'Модуль Настройка API', 'Модуль Настройка API  Включает поддержку текстового редактора TinyMCE, защиты Google reCAPTCHA, а также авторизации через Google, Telegram и Facebook.', 1, 0),
(20, 'Feedback - Обратная связь', 'Управляйте обращениями в CMS Pro: просмотр сообщений, тем, файлов, ответы пользователям, фильтры и статусы для эффективной работы с обратной связью.', '<p>Демо страницы: <a href=\"../feedback\">/feedback</a><br>Демо админ панели:&nbsp;<a href=\"index.php?module=feedback\">/admin/index.php?module=feedback</a></p>\r\n<div>\r\n<div>\r\n<h4 class=\"\" dir=\"auto\">Функционал админ-панели</h4>\r\n<ol class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Просмотр сообщений</strong>:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Таблица с фильтрацией по дате, теме или статусу (например, \"Новое\", \"Обработано\").</li>\r\n<li class=\"break-words\">Подробный просмотр каждого сообщения с контактной информацией и прикрепленными файлами.</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Управление статусами</strong>:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Отметка сообщений как \"Прочитанные\", \"В работе\" или \"Завершенные\".</li>\r\n<li class=\"break-words\">Возможность добавлять заметки для внутреннего учета.</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Ответ пользователю</strong>:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Встроенная форма для отправки ответа на указанный email или телефон (например, через email-уведомления).</li>\r\n<li class=\"break-words\">Возможность прикрепить файлы в ответное сообщение (если поддерживается).</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Работа с файлами</strong>:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Скачивание прикрепленных пользователями файлов.</li>\r\n<li class=\"break-words\">Удаление файлов или сообщений (при наличии прав доступа).</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Фильтры и поиск</strong>:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Быстрый поиск по имени, email, телефону или тексту сообщения.</li>\r\n<li class=\"break-words\">Сортировка по дате поступления или теме.</li>\r\n</ul>\r\n</li>\r\n</ol>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Настройка модуля</h4>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Уведомления</strong>:<br>Включение/выключение автоматических уведомлений администратора о новых сообщениях (например, на email).</li>\r\n<li class=\"break-words\"><strong>Темы</strong>:<br>Редактирование предустановленного списка тем для формы обратной связи.</li>\r\n<li class=\"break-words\"><strong>Ограничения файлов</strong>:<br>Настройка максимального размера и допустимых форматов прикрепляемых файлов.</li>\r\n</ul>\r\n<hr>\r\n<h3 class=\"\" dir=\"auto\">Как использовать</h3>\r\n<ol class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Перейдите в раздел <strong>\"Обратная связь\"</strong> в админ-панели CMS.</li>\r\n<li class=\"break-words\">Просмотрите список новых сообщений в разделе <strong>Messages</strong>.</li>\r\n<li class=\"break-words\">Откройте нужное обращение, изучите детали и прикрепленные файлы.</li>\r\n<li class=\"break-words\">Ответьте пользователю через встроенную форму или внешние средства связи.</li>\r\n<li class=\"break-words\">Обновите статус сообщения после обработки.</li>\r\n</ol>\r\n<p class=\"break-words\" dir=\"auto\">Модуль \"Обратная связь\" в админ-панели Pro Website Management обеспечивает удобное управление пользовательскими обращениями, помогая оперативно реагировать на запросы и поддерживать связь с аудиторией сайта.</p>\r\n</div>\r\n<h3 class=\"\" dir=\"auto\"><br>Пользовательская часть: Обратная связь</h3>\r\n<p class=\"break-words\" dir=\"auto\">Модуль <strong>\"Обратная связь\"</strong> позволяет посетителям сайта легко связаться с администрацией, отправить свои вопросы, предложения или файлы через удобную форму. Форма доступна на публичной части сайта и включает следующие поля:</p>\r\n<h4 class=\"\" dir=\"auto\">Обратная связь</h4>\r\n<p class=\"break-words\" dir=\"auto\">Форма для отправки сообщений администрации сайта.</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Ваше имя</strong>:<br>Укажите ваше имя, чтобы мы знали, как к вам обращаться.<br><em>Пример</em>: Иван Иванов</li>\r\n<li class=\"break-words\"><strong>Телефон или Email</strong>:<br>Введите ваш номер телефона или адрес электронной почты для обратной связи.<br><em>Пример</em>: +38 (097) 700-14-14 или&nbsp;<a href=\"mailto:ivan@example.com\" target=\"_blank\" rel=\"noopener noreferrer\">ivan@example.com</a></li>\r\n<li class=\"break-words\"><strong>Тема</strong>:<br>Выберите или укажите тему вашего обращения.<br><em>Пример</em>: \"Вопрос по заказу\", \"Техническая поддержка\"</li>\r\n<li class=\"break-words\"><strong>Сообщение</strong>:<br>Напишите текст вашего обращения. Подробно опишите ваш вопрос или предложение.<br><em>Пример</em>: \"Здравствуйте! Хочу уточнить статус моего заказа №123.\"</li>\r\n<li class=\"break-words\"><strong>Прикрепить файл</strong>:<br>Добавьте файл (например, документ, изображение), если это необходимо для вашего обращения.<br><em>Примечание</em>: Поддерживаются популярные форматы (PDF, JPG, PNG и др.).</li>\r\n</ul>\r\n<hr>\r\n<h3 class=\"\" dir=\"auto\">Как работает форма</h3>\r\n<ol class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Заполните все обязательные поля формы.</li>\r\n<li class=\"break-words\">При необходимости прикрепите файл.</li>\r\n<li class=\"break-words\">Нажмите кнопку \"Отправить\", чтобы ваше сообщение было передано администрации.</li>\r\n<li class=\"break-words\">После отправки вы получите подтверждение (если настроено), а мы свяжемся с вами через указанные контакты.</li>\r\n</ol>\r\n<p class=\"break-words\" dir=\"auto\">Модуль \"Обратная связь\" в CMS Pro Website Management делает общение с пользователями простым и удобным, позволяя быстро получать обратную связь и решать их вопросы.</p>\r\n</div>', 'Feedback, Обратная связь, Модуль обратной связи', '[\"67f7e4abcc6e0-site-feedback..jpg\",\"67f7e4b6065f7-admin1..jpg\",\"67f7e4bcc246d-admin_message..jpg\"]', 'feedback-module', '2025-04-10 15:31:38', 4, 'Feedback - Обратная связь', 'Управляйте обращениями в CMS Pro: просмотр сообщений, тем, файлов, ответы пользователям, фильтры и статусы для эффективной работы с обратной связью.', 'Feedback - Обратная связь', 'Управляйте обращениями в CMS Pro: просмотр сообщений, тем, файлов, ответы пользователям, фильтры и статусы для эффективной работы с обратной связью.', 'Feedback - Обратная связь', 'Управляйте обращениями в CMS Pro: просмотр сообщений, тем, файлов, ответы пользователям, фильтры и статусы для эффективной работы с обратной связью.', 1, 0),
(21, 'Сканер безопасности сайта', 'Pro Website Management позволяет администраторам проверять сайт на уязвимости, анализировать безопасность PHP-файлов', '<p>Демо: <a title=\"Сканер\" href=\"index.php?module=security_check\">/admin/index.php?module=security_check</a><br><br></p>\r\n<div>\r\n<h3 class=\"\" dir=\"auto\">Административная часть: Сканер безопасности сайта</h3>\r\n<p class=\"break-words\" dir=\"auto\">Модуль <strong>\"Сканер безопасности сайта\"</strong> в CMS Pro Website Management позволяет администраторам проверять сайт на уязвимости, анализировать безопасность PHP-файлов и получать рекомендации по устранению проблем. Инструмент помогает защитить сайт от атак и повысить его надежность.</p>\r\n<h4 class=\"\" dir=\"auto\">Основной функционал</h4>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Введите URL для сканирования</strong>:<br>Поле для ввода адреса сайта, который нужно проверить.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">https://example.com</span><br><em>Действие</em>: Нажмите кнопку <strong>\"Сканировать URL\"</strong>, чтобы запустить проверку указанного адреса.</li>\r\n<li class=\"break-words\"><strong>Сканировать все PHP-файлы</strong>:<br>Опция для полной проверки всех PHP-файлов на сервере на наличие уязвимостей.<br><em>Примечание</em>: Анализируются потенциальные угрозы в коде.</li>\r\n<li class=\"break-words\"><strong>Отчёт по безопасности</strong>:<br>Результаты сканирования отображаются в виде отчёта.<br><em>Пример отчёта</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">https://masterok.lt/seo.php</span>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>SQL-инъекции</strong>: Чисто (нет уязвимостей).</li>\r\n<li class=\"break-words\"><strong>XSS</strong>: Чисто (нет уязвимостей).</li>\r\n<li class=\"break-words\"><strong>CSRF</strong>: Отсутствует CSRF-токен в одной из форм.</li>\r\n<li class=\"break-words\"><strong>Заголовки безопасности</strong>:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Отсутствует заголовок: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">X-Content-Type-Options</span>.</li>\r\n<li class=\"break-words\">Отсутствует заголовок: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">X-Frame-Options</span>.</li>\r\n<li class=\"break-words\">Отсутствует заголовок: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">X-XSS-Protection</span>.</li>\r\n<li class=\"break-words\">Отсутствует заголовок: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Strict-Transport-Security</span>.</li>\r\n</ul>\r\n</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Советы по безопасности</strong>:<br>Рекомендации для устранения выявленных проблем:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Используйте параметризованные SQL-запросы для защиты от SQL-инъекций.</li>\r\n<li class=\"break-words\">Внедрите CSRF-токены для всех форм.</li>\r\n<li class=\"break-words\">Настройте заголовки безопасности (CSP, HSTS и другие).</li>\r\n<li class=\"break-words\">Фильтруйте и экранируйте пользовательский ввод.</li>\r\n<li class=\"break-words\">Ограничьте загрузку файлов по типу, размеру и расширению.</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Сохранить отчёт</strong>:<br>Кнопка для сохранения результатов проверки в файл для дальнейшего анализа или передачи разработчикам.</li>\r\n<li class=\"break-words\"><strong>Файлы и папки на сервере</strong>:<br>Список файлов и директорий, проверенных сканером, с указанием подозрительных элементов (если обнаружены).</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Как использовать</h4>\r\n<ol class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Перейдите в раздел <strong>\"Сканер безопасности сайта\"</strong> в админ-панели CMS.</li>\r\n<li class=\"break-words\">Введите URL сайта в поле (например, <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">https://example.com</span>) и нажмите <strong>\"Сканировать URL\"</strong>.<br>Или выберите <strong>\"Сканировать все PHP-файлы\"</strong> для полной проверки.</li>\r\n<li class=\"break-words\">Ознакомьтесь с отчётом по безопасности, включая выявленные уязвимости и отсутствующие заголовки.</li>\r\n<li class=\"break-words\">Изучите рекомендации в разделе <strong>\"Советы по безопасности\"</strong> и примените их.</li>\r\n<li class=\"break-words\">Сохраните отчёт с помощью кнопки <strong>\"Сохранить отчёт\"</strong> для документации или передачи команде.</li>\r\n</ol>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Преимущества модуля</h4>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Быстрая проверка сайта на распространённые уязвимости (SQL-инъекции, XSS, CSRF).</li>\r\n<li class=\"break-words\">Анализ заголовков безопасности и рекомендации по их настройке.</li>\r\n<li class=\"break-words\">Подробный отчёт с практическими советами для повышения защиты сайта.</li>\r\n</ul>\r\n<p class=\"break-words\" dir=\"auto\">Модуль \"Сканер безопасности сайта\" в Pro Website Management помогает администраторам оперативно выявлять и устранять угрозы, обеспечивая безопасность сайта и данных пользователей.</p>\r\n</div>', 'SQL-инъекции, XSS, CSRF, Сканер безопасности сайта, Pro Website Management', '[\"67f7e7af13953-scaner_files..jpg\",\"67f7e7af13cc8-scaner1..jpg\"]', 'security-scaner', '2025-04-10 15:45:51', 4, 'Сканер безопасности сайта', 'Pro Website Management позволяет администраторам проверять сайт на уязвимости, анализировать безопасность PHP-файлов', 'Сканер безопасности сайта', 'Pro Website Management позволяет администраторам проверять сайт на уязвимости, анализировать безопасность PHP-файлов', 'Сканер безопасности сайта', 'Pro Website Management позволяет администраторам проверять сайт на уязвимости, анализировать безопасность PHP-файлов', 1, 0),
(22, 'SEO оптимизация главной страницы Open Graph и Twitter Card, meta', 'Оптимизацич главной страницы сайта для поисковых систем, настроить её внешний вид и улучшить отображение в социальных сетях через Open Graph и Twitter Card.', '<p>DEMO: <a href=\"index.php?module=seo\">/admin/index.php?module=seo</a></p>\r\n<div>\r\n<h3 class=\"\" dir=\"auto\">Административная часть: Настройки SEO для главной страницы</h3>\r\n<p class=\"break-words\" dir=\"auto\">Модуль <strong>\"Настройки SEO для главной страницы\"</strong> в CMS Pro Website Management позволяет оптимизировать главную страницу сайта для поисковых систем, настроить её внешний вид и улучшить отображение в социальных сетях через Open Graph и Twitter Card.</p>\r\n<h4 class=\"\" dir=\"auto\">Основные настройки SEO</h4>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Заголовок сайта (Title)</strong>:<br>Укажите заголовок главной страницы для поисковых систем.<br><em>Пример</em>: \"Pro Website Management Engine\"</li>\r\n<li class=\"break-words\"><strong>Описание сайта (Description)</strong>:<br>Краткое описание сайта для мета-тега description.<br><em>Пример</em>: \"Интернет-магазинов, Сайтов услуг, Онлайн-бронирования\"</li>\r\n<li class=\"break-words\"><strong>Ключевые слова (Keywords)</strong>:<br>Список ключевых слов через запятую для мета-тега keywords.<br><em>Пример</em>: \"Скрипт интернет магазина, Pro Website Management, Скрипт интернет магазина 2025, Интернет магазин которым легко управлять\"</li>\r\n<li class=\"break-words\"><strong>Мета-тег Robots</strong>:<br>Управление индексацией страницы поисковиками.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Index, Follow</span> (разрешить индексацию и переход по ссылкам).</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Open Graph (OG) настройки</h4>\r\n<p class=\"break-words\" dir=\"auto\">Настройки для отображения страницы в социальных сетях (например, Facebook).</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>OG Заголовок (Open Graph Title)</strong>:<br>Заголовок для соцсетей.<br><em>Пример</em>: \"Pro Website Management Engine\"</li>\r\n<li class=\"break-words\"><strong>OG Описание (Open Graph Description)</strong>:<br>Описание для соцсетей.<br><em>Пример</em>: \"Интернет-магазинов, Сайтов услуг, Онлайн-бронирования, Досок объявлений, Новостных порталов, Корпоративных и бизнес-сайтов\"</li>\r\n<li class=\"break-words\"><strong>OG Изображение (Open Graph Image)</strong>:<br>Загрузите изображение для превью.<br><em>Статус</em>: Файл не выбран.</li>\r\n<li class=\"break-words\"><strong>OG Тип (Open Graph Type)</strong>:<br>Тип контента.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">website</span></li>\r\n<li class=\"break-words\"><strong>OG URL (Open Graph URL)</strong>:<br>Ссылка на страницу.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">https://masterok.lt</span></li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Twitter Card</h4>\r\n<p class=\"break-words\" dir=\"auto\">Настройки для отображения ссылки в Twitter.</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Тип Twitter Card</strong>:<br>Формат карточки в Twitter.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Summary with Large Image</span> (краткое описание с большим изображением).</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Внешний вид блока</h4>\r\n<p class=\"break-words\" dir=\"auto\">Настройки оформления SEO-блока на главной странице.</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Цвет оформления блока (HEX или CSS-градиент)</strong>:<br>Выберите цвет фона или укажите градиент.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">#e0f6ff</span> или CSS-градиент (например, <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">linear-gradient(to right, #e0f6ff, #ffffff)</span>).</li>\r\n<li class=\"break-words\"><strong>Отступы фона от краёв (px)</strong>:<br>Укажите отступы фона.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">20px</span></li>\r\n<li class=\"break-words\"><strong>Округление углов фона (px)</strong>:<br>Радиус скругления углов фона.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">35px</span></li>\r\n<li class=\"break-words\"><strong>Режим отображения</strong>:<br>Выберите стиль отображения блока.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">С оформлением</span> (включить стилизацию).</li>\r\n<li class=\"break-words\"><strong>Ширина блока (px или авто) и отступы от краёв</strong>:<br>Укажите ширину блока.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Авто (100%)</span>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Отступы от краёв (px)</strong>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">0px</span></li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Округление углов блока (px)</strong>:<br>Радиус скругления углов всего блока.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">40px</span></li>\r\n<li class=\"break-words\"><strong>Дополнительный CSS для блока Meta</strong>:<br>Поле для пользовательских стилей класса <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">.meta-info</span>.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">border: 1px solid #000;</span></li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Как использовать</h4>\r\n<ol class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Перейдите в раздел <strong>\"Настройки SEO для главной страницы\"</strong> в админ-панели.</li>\r\n<li class=\"break-words\">Заполните поля для SEO (Title, Description, Keywords, Robots).</li>\r\n<li class=\"break-words\">Настройте Open Graph и Twitter Card для социальных сетей.</li>\r\n<li class=\"break-words\">Отрегулируйте внешний вид блока (цвет, отступы, скругления, CSS).</li>\r\n<li class=\"break-words\">Сохраните изменения и проверьте главную страницу сайта.</li>\r\n</ol>\r\n<p class=\"break-words\" dir=\"auto\">Модуль \"Настройки SEO для главной страницы\" в Pro Website Management обеспечивает гибкую оптимизацию для поисковиков и соцсетей, а также контроль внешнего вида ключевого блока сайта.</p>\r\n</div>', 'SEO оптимизацич главной страницы, Open Graph, Twitter Card, meta, SEO оптимизированая CMS для сайта', '[\"67f7eacadbc95-admin2..jpg\",\"67f7eacadc108-admin3..jpg\",\"67f7eacadc4dc-admin-en1..jpg\",\"67f7eacadc84b-admin-en2..jpg\",\"67f7eacadcb63-admin-en3..jpg\",\"67f7eacadcf4e-admin-seo-main..jpg\",\"67f7eacadd368-main-site..jpg\",\"67f7eacadd762-main-site-en..jpg\"]', 'seo', '2025-04-10 15:59:06', 4, 'SEO оптимизацич главной страницы Open Graph и Twitter Card, meta', 'Оптимизацич главной страницы сайта для поисковых систем, настроить её внешний вид и улучшить отображение в социальных сетях через Open Graph и Twitter Card.', 'SEO оптимизацич главной страницы Open Graph и Twitter Card, meta', 'Оптимизацич главной страницы сайта для поисковых систем, настроить её внешний вид и улучшить отображение в социальных сетях через Open Graph и Twitter Card.', 'SEO оптимизацич главной страницы Open Graph и Twitter Card, meta', 'Оптимизацич главной страницы сайта для поисковых систем, настроить её внешний вид и улучшить отображение в социальных сетях через Open Graph и Twitter Card.', 1, 0),
(23, 'Управление каруселью брендами', 'Pro Website позволяет создавать и настраивать карусель с логотипами брендов, добавлять слайды, управлять их отображением и задавать параметры прокрутки', '<div>\r\n<h3 class=\"\" dir=\"auto\">Демо: <a href=\"index.php?module=carusel\">/admin/index.php?module=carusel</a><br><br>Административная часть: Управление каруселью брендами</h3>\r\n<p class=\"break-words\" dir=\"auto\">Модуль <strong>\"Управление каруселью брендами\"</strong> в CMS Pro Website Management позволяет создавать и настраивать карусель с логотипами брендов, добавлять слайды, управлять их отображением и задавать параметры прокрутки.</p>\r\n<h4 class=\"\" dir=\"auto\">Добавление слайда</h4>\r\n<p class=\"break-words\" dir=\"auto\">Функция <strong>\"Добавить слайд\"</strong> позволяет создавать новые элементы карусели.</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Изображение (макс. 5MB)</strong>:<br>Загрузите изображение бренда.<br><em>Статус</em>: Файл не выбран.<br><em>Примечание</em>: Максимальный размер &mdash; 5 МБ.</li>\r\n<li class=\"break-words\"><strong>Ссылка</strong>:<br>Укажите URL, на который будет вести слайд.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">https://example.com</span></li>\r\n<li class=\"break-words\"><strong>Подпись</strong>:<br>Текст, отображаемый на слайде (например, название бренда).<br><em>Пример</em>: \"Brand Name\"</li>\r\n<li class=\"break-words\"><strong>Активен</strong>:<br>Переключатель для включения/отключения слайда.<br><em>Примечание</em>: Неактивные слайды не отображаются.</li>\r\n<li class=\"break-words\"><strong>Отображать на главной</strong>:<br>Опция для показа слайда на главной странице.<br><em>Примечание</em>: Включите, если слайд предназначен для главной.</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Настройки карусели</h4>\r\n<p class=\"break-words\" dir=\"auto\">Общие параметры для управления внешним видом и поведением карусели.</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Высота слайдера (px)</strong>:<br>Укажите высоту карусели в пикселях.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">600px</span></li>\r\n<li class=\"break-words\"><strong>Цвет подписи</strong>:<br>Выберите цвет текста подписи (HEX-код).<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">#ffffff</span></li>\r\n<li class=\"break-words\"><strong>Прозрачность подписи (1-100)</strong>:<br>Установите уровень прозрачности подписи (от 1 до 100%).<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">50</span></li>\r\n<li class=\"break-words\"><strong>Округление краёв (0-100px)</strong>:<br>Радиус скругления углов слайдов.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">10px</span></li>\r\n<li class=\"break-words\"><strong>Скорость прокрутки (мс)</strong>:<br>Время смены слайдов в миллисекундах.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">50000</span> (50 секунд)</li>\r\n<li class=\"break-words\"><strong>Автопрокрутка</strong>:<br>Включите/отключите автоматическую смену слайдов.<br><em>Примечание</em>: Если выключено, переключение вручную.</li>\r\n<li class=\"break-words\"><strong>Цвет кнопок</strong>:<br>Выберите цвет кнопок навигации (HEX-код).<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">#000000</span></li>\r\n<li class=\"break-words\"><strong>Отступ от края (px)</strong>:<br>Укажите расстояние карусели от краёв страницы.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">50px</span></li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Список слайдов</h4>\r\n<p class=\"break-words\" dir=\"auto\">Раздел отображает все добавленные слайды в виде таблицы:</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Превью изображения.</li>\r\n<li class=\"break-words\">Ссылка, подпись, статус активности и отображения на главной.</li>\r\n<li class=\"break-words\">Кнопки для редактирования или удаления слайда.</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Как использовать</h4>\r\n<ol class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Перейдите в раздел <strong>\"Управление каруселью брендами\"</strong> в админ-панели.</li>\r\n<li class=\"break-words\">Нажмите <strong>\"Добавить слайд\"</strong>, загрузите изображение, укажите ссылку и подпись, настройте параметры активности.</li>\r\n<li class=\"break-words\">В разделе <strong>\"Настройки карусели\"</strong> задайте высоту, цвета, прозрачность, скорость и другие параметры.</li>\r\n<li class=\"break-words\">Проверьте список слайдов, отредактируйте или удалите их при необходимости.</li>\r\n<li class=\"break-words\">Сохраните изменения и просмотрите карусель на сайте.</li>\r\n</ol>\r\n<p class=\"break-words\" dir=\"auto\">Модуль \"Управление каруселью брендами\" в Pro Website Management предоставляет удобный инструмент для демонстрации логотипов брендов с гибкими настройками внешнего вида и прокрутки.</p>\r\n</div>', 'Управление каруселью брендами, Настройки карусели, Слайдер, Карусель брендов', '[\"67f8174cbc854-admin_brands..jpg\",\"67f8174cbcc85-admin_carusel..jpg\",\"67f8174cbcf85-admin_carusel2..jpg\",\"67f8174cbd2d5-admin_slider..jpg\",\"67f8174cbd6ec-carusel..jpg\",\"67f8174cbdad5-mini_main..jpg\"]', 'carusel', '2025-04-10 19:09:00', 4, 'Управление каруселью брендами', 'Pro Website позволяет создавать и настраивать карусель с логотипами брендов, добавлять слайды, управлять их отображением и задавать параметры прокрутки', 'Управление каруселью брендами', 'Pro Website позволяет создавать и настраивать карусель с логотипами брендов, добавлять слайды, управлять их отображением и задавать параметры прокрутки', 'Управление каруселью брендами', 'Pro Website позволяет создавать и настраивать карусель с логотипами брендов, добавлять слайды, управлять их отображением и задавать параметры прокрутки', 1, 0),
(24, 'Административная часть: Настройки цвета', 'Позволяет администраторам задавать цветовую схему сайта с помощью удобных кнопок выбора цвета. Настройка охватывает элементы шапки, футера, кнопок и слайдера.', '<div>\r\n<h3 class=\"\" dir=\"auto\">Демо: <a href=\"index.php?module=settings_color\">/admin/index.php?module=settings_color</a><br>Административная часть: Настройки цвета</h3>\r\n<p class=\"break-words\" dir=\"auto\">Модуль <strong>\"Настройки цвета\"</strong> в CMS Pro Website Management позволяет администраторам задавать цветовую схему сайта с помощью удобных кнопок выбора цвета. Настройка охватывает элементы шапки, футера, кнопок и слайдера.</p>\r\n<h4 class=\"\" dir=\"auto\">Настройки цветовой схемы</h4>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Цвет текста в шапке</strong>:<br>Выберите цвет текста в шапке сайта с помощью кнопки палитры.<br><em>Пример результата</em>: Чёрный.</li>\r\n<li class=\"break-words\"><strong>Цвет фона шапки</strong>:<br>Укажите фоновый цвет шапки через кнопку выбора цвета.<br><em>Пример результата</em>: Белый.</li>\r\n<li class=\"break-words\"><strong>Цвет полоски в шапке</strong>:<br>Задайте цвет декоративной полоски в шапке кнопкой палитры.<br><em>Пример результата</em>: Светло-голубой.</li>\r\n<li class=\"break-words\"><strong>Прозрачность полоски</strong>:<br>Установите уровень прозрачности полоски (от 0 до 1) вручную.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">0.8</span> (80% непрозрачности).</li>\r\n<li class=\"break-words\"><strong>Цвет ссылок в шапке</strong>:<br>Выберите цвет гиперссылок в шапке с помощью кнопки.<br><em>Пример результата</em>: Синий.</li>\r\n<li class=\"break-words\"><strong>Цвет фона футера</strong>:<br>Определите фоновый цвет футера через кнопку выбора.<br><em>Пример результата</em>: Тёмно-серый.</li>\r\n<li class=\"break-words\"><strong>Цвет кнопок</strong>:<br>Задайте основной цвет кнопок на сайте кнопкой палитры.<br><em>Пример результата</em>: Зелёный.</li>\r\n<li class=\"break-words\"><strong>Цвет кнопок в шапке</strong>:<br>Выберите цвет кнопок в шапке с помощью кнопки.<br><em>Пример результата</em>: Оранжевый.</li>\r\n<li class=\"break-words\"><strong>Цвет кнопки \"Добавить тендер\"</strong>:<br>Укажите цвет кнопки \"Добавить тендер\" через кнопку выбора.<br><em>Пример результата</em>: Красный.</li>\r\n<li class=\"break-words\"><strong>Цвет фона заголовков</strong>:<br>Выберите фоновый цвет блоков заголовков кнопкой палитры.<br><em>Пример результата</em>: Светло-серый.</li>\r\n<li class=\"break-words\"><strong>Цвет кнопок слайдера</strong>:<br>Задайте цвет кнопок навигации слайдера с помощью кнопки.<br><em>Пример результата</em>: Чёрный.</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Как использовать</h4>\r\n<ol class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Перейдите в раздел <strong>\"Настройки цвета\"</strong> в админ-панели CMS.</li>\r\n<li class=\"break-words\">Нажмите кнопку рядом с каждым параметром, чтобы открыть палитру и выбрать нужный цвет.</li>\r\n<li class=\"break-words\">Установите прозрачность полоски в шапке, введя значение от 0 (полная прозрачность) до 1 (полная непрозрачность).</li>\r\n<li class=\"break-words\">Сохраните изменения и проверьте обновлённый дизайн сайта.</li>\r\n</ol>\r\n<p class=\"break-words\" dir=\"auto\">Модуль \"Настройки цвета\" в Pro Website Management упрощает настройку визуального стиля сайта благодаря интуитивным кнопкам выбора цвета, обеспечивая быстрый и удобный процесс дизайна.</p>\r\n</div>', 'Цветовые настройки', '[\"67f818aae197a-color_settings..jpg\"]', 'color-settings', '2025-04-10 19:14:51', 4, 'Модуль \\\\\\\"Настройки цвета\\\\\\\" в CMS Pro Website Management ', 'Позволяет администраторам задавать цветовую схему сайта с помощью удобных кнопок выбора цвета. Настройка охватывает элементы шапки, футера, кнопок и слайдера.', 'Административная часть: Настройки цвета', 'Позволяет администраторам задавать цветовую схему сайта с помощью удобных кнопок выбора цвета. Настройка охватывает элементы шапки, футера, кнопок и слайдера.', 'Административная часть: Настройки цвета', 'Позволяет администраторам задавать цветовую схему сайта с помощью удобных кнопок выбора цвета. Настройка охватывает элементы шапки, футера, кнопок и слайдера.', 1, 0),
(25, 'Управление администраторами Pro Website Management', 'Pro Website Management позволяет добавлять новых администраторов, задавать их учетные данные и управлять списком пользователей с правами доступа к админ-панели', '<div>Демо: <a href=\"index.php?module=admins\">/admin/index.php?module=admins</a></div>\r\n<div>Административная часть: Управление администраторами Pro Website Management\r\n<p class=\"break-words\" dir=\"auto\">Модуль <strong>\"Управление администраторами\"</strong> в CMS Pro Website Management позволяет добавлять новых администраторов, задавать их учетные данные и управлять списком пользователей с правами доступа к админ-панели.</p>\r\n<h4 class=\"\" dir=\"auto\">Добавление администратора</h4>\r\n<p class=\"break-words\" dir=\"auto\">Функция <strong>\"Добавить администратора\"</strong> предназначена для создания нового пользователя с административным доступом.</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Логин</strong>:<br>Укажите уникальное имя пользователя для входа.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">admin1</span></li>\r\n<li class=\"break-words\"><strong>Пароль</strong>:<br>Задайте пароль для нового администратора.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">SecurePass123</span><br><em>Примечание</em>: Используйте сложные пароли для повышения безопасности.</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Список администраторов</h4>\r\n<p class=\"break-words\" dir=\"auto\">Раздел отображает всех администраторов в виде таблицы:</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Логин</strong>: Имя пользователя.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">admin1</span></li>\r\n<li class=\"break-words\"><strong>Действия</strong>: Кнопки для редактирования (изменение пароля) или удаления администратора.<br><em>Примечание</em>: Удаление доступно только суперадминистратору.</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Как использовать</h4>\r\n<ol class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Перейдите в раздел <strong>\"Управление администраторами\"</strong> в админ-панели Pro Website Management.</li>\r\n<li class=\"break-words\">Нажмите <strong>\"Добавить администратора\"</strong>, введите логин и пароль нового пользователя.</li>\r\n<li class=\"break-words\">Сохраните данные &mdash; администратор добавится в <strong>\"Список администраторов\"</strong>.</li>\r\n<li class=\"break-words\">При необходимости отредактируйте или удалите администратора из списка.</li>\r\n</ol>\r\n<p class=\"break-words\" dir=\"auto\">Модуль \"Управление администраторами\" в Pro Website Management обеспечивает удобный и безопасный контроль доступа к админ-панели, упрощая управление командой администраторов.</p>\r\n</div>', 'Управление администраторами, Добавить администратора, Pro Website Management', '[\"67f81ae750235-admins..jpg\"]', 'admins', '2025-04-10 19:24:23', 4, 'Управление администраторами Pro Website Management', 'Pro Website Management позволяет добавлять новых администраторов, задавать их учетные данные и управлять списком пользователей с правами доступа к админ-панели', 'Управление администраторами Pro Website Management', 'Pro Website Management позволяет добавлять новых администраторов, задавать их учетные данные и управлять списком пользователей с правами доступа к админ-панели', 'Управление администраторами Pro Website Management', 'Pro Website Management позволяет добавлять новых администраторов, задавать их учетные данные и управлять списком пользователей с правами доступа к админ-панели', 1, 0),
(26, 'Массовая рассылка EMAIL ', 'Pro Website Management позволяет отправлять email-сообщения всем пользователям или выбранным контактам, настраивать отправителя и загружать списки получателей.', '<div>\r\n<h3 class=\"\" dir=\"auto\">Административная часть: Массовая рассылка</h3>\r\n<p class=\"break-words\" dir=\"auto\">Модуль <strong>\"Массовая рассылка\"</strong> в CMS Pro Website Management позволяет отправлять email-сообщения всем пользователям или выбранным контактам, настраивать отправителя и загружать списки получателей.</p>\r\n<h4 class=\"\" dir=\"auto\">Настройки отправки</h4>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Email отправителя</strong>:<br>Адрес, с которого будет отправлено сообщение.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">default@masterok.lt</span><br><em>Примечание</em>: Изменение доступно в разделе \"Настройки SMTP\".</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Получатели</h4>\r\n<p class=\"break-words\" dir=\"auto\">Выбор аудитории для рассылки:</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Всем пользователям</strong>:<br>Отправка всем зарегистрированным пользователям сайта.</li>\r\n<li class=\"break-words\"><strong>Список контактов</strong>:<br>Ручной выбор или отображение существующих контактов.<br><em>Пример</em>:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Ктотам (<span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">rbilohash@gmail.com</span>)</li>\r\n<li class=\"break-words\">НИК (Company)</li>\r\n<li class=\"break-words\">Без имени (<span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">support@flory.lt</span>)</li>\r\n<li class=\"break-words\">Без имени (<span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Company@test.ru</span>)</li>\r\n<li class=\"break-words\">Без имени (<span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Mail@mail.ru</span>)</li>\r\n<li class=\"break-words\">MeowOGkush ()</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Загрузить список контактов из TXT</strong>:<br>Импорт получателей из файла.<br><em>Формат</em>: Один контакт (email) на строку.<br><em>Статус</em>: Файл не выбран.</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Сообщение</h4>\r\n<p class=\"break-words\" dir=\"auto\">Настройка содержимого рассылки:</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Тема</strong>:<br>Заголовок email-сообщения.<br><em>Пример</em>: \"Новости сайта\"</li>\r\n<li class=\"break-words\"><strong>Текст сообщения</strong>:<br>Основное содержание письма.<br><em>Пример</em>: \"Уважаемые пользователи, рады сообщить о новых функциях!\"</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Как использовать</h4>\r\n<ol class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Перейдите в раздел <strong>\"Массовая рассылка\"</strong> в админ-панели CMS.</li>\r\n<li class=\"break-words\">Проверьте или измените <strong>Email отправителя</strong> в настройках SMTP.</li>\r\n<li class=\"break-words\">Выберите получателей: <strong>\"Всем пользователям\"</strong>, конкретные контакты из списка или загрузите TXT-файл с email-адресами.</li>\r\n<li class=\"break-words\">Заполните поля <strong>\"Тема\"</strong> и <strong>\"Текст сообщения\"</strong>.</li>\r\n<li class=\"break-words\">Нажмите кнопку отправки для запуска рассылки.</li>\r\n</ol>\r\n<p class=\"break-words\" dir=\"auto\">Модуль \"Массовая рассылка\" в Pro Website Management упрощает коммуникацию с пользователями, предоставляя гибкие настройки отправки и управления получателями.</p>\r\n</div>', 'Массовая рассылка Email, Массовая рассылка из TXT, ', '[\"67f81c67d0b99-admin_email1..jpg\",\"67f81c67d0e9a-admin_email2..jpg\"]', 'email-send', '2025-04-10 19:30:47', 4, 'Массовая рассылка EMAIL ', 'Pro Website Management позволяет отправлять email-сообщения всем пользователям или выбранным контактам, настраивать отправителя и загружать списки получателей.', 'Массовая рассылка EMAIL ', 'Pro Website Management позволяет отправлять email-сообщения всем пользователям или выбранным контактам, настраивать отправителя и загружать списки получателей.', 'Массовая рассылка EMAIL ', 'Pro Website Management позволяет отправлять email-сообщения всем пользователям или выбранным контактам, настраивать отправителя и загружать списки получателей.', 1, 0);
INSERT INTO `news` (`id`, `title`, `short_desc`, `full_desc`, `keywords`, `image`, `custom_url`, `created_at`, `category_id`, `meta_title`, `meta_desc`, `og_title`, `og_desc`, `twitter_title`, `twitter_desc`, `published`, `reviews_enabled`) VALUES
(27, 'Управление пользователями', 'Pro Website Management позволяет администраторам искать, просматривать, редактировать и добавлять пользователей сайта, а также управлять их ролями и профилями.', '<div>\r\n<p class=\"break-words\" dir=\"auto\">Вот описание административной части модуля \"Управление пользователями\" для CMS \"Pro Website Management\". Оно предназначено для администраторов, которые управляют списком пользователей, их данными и ролями.</p>\r\n<hr>\r\n<h3 class=\"\" dir=\"auto\">Административная часть: Управление пользователями</h3>\r\n<p class=\"break-words\" dir=\"auto\">Модуль <strong>\"Управление пользователями\"</strong> в CMS Pro Website Management позволяет администраторам искать, просматривать, редактировать и добавлять пользователей сайта, а также управлять их ролями и профилями.</p>\r\n<h4 class=\"\" dir=\"auto\">Поиск пользователей</h4>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Поиск по всему</strong>:<br>Поле для ввода запроса поиска по всем данным пользователей.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Введите запрос...</span><br><em>Функция</em>: Фильтрация списка по введённым данным.</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Список пользователей</h4>\r\n<p class=\"break-words\" dir=\"auto\">Раздел отображает всех пользователей в виде таблицы с колонками:</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>ID и Фото</strong>: Уникальный идентификатор и аватар пользователя.</li>\r\n<li class=\"break-words\"><strong>Контакт</strong>: Email или телефон.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">user@example.com</span></li>\r\n<li class=\"break-words\"><strong>Имя</strong>: Имя пользователя.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Иван</span></li>\r\n<li class=\"break-words\"><strong>Фамилия</strong>: Фамилия пользователя.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Иванов</span></li>\r\n<li class=\"break-words\"><strong>Город</strong>: Местоположение.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Москва</span> или <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Без города</span></li>\r\n<li class=\"break-words\"><strong>Категория</strong>: Тип пользователя или группа.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Заказчик</span> или <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Без категории</span></li>\r\n<li class=\"break-words\"><strong>Заполнен</strong>: Процент заполнения профиля.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">80%</span></li>\r\n<li class=\"break-words\"><strong>Создан</strong>: Дата регистрации.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">10.04.2025</span></li>\r\n<li class=\"break-words\"><strong>Действия</strong>: Кнопки для редактирования или удаления пользователя.</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Добавить нового пользователя</h4>\r\n<p class=\"break-words\" dir=\"auto\">Функция <strong>\"Добавить нового пользователя\"</strong> позволяет вручную создать профиль.</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Контакт</strong>:<br>Email или телефон для входа.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">user@example.com</span></li>\r\n<li class=\"break-words\"><strong>Пароль</strong>:<br>Задайте пароль для пользователя.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">SecurePass123</span></li>\r\n<li class=\"break-words\"><strong>Роль</strong>:<br>Укажите роль пользователя.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Заказчик</span></li>\r\n<li class=\"break-words\"><strong>Имя</strong>:<br>Имя пользователя.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Иван</span></li>\r\n<li class=\"break-words\"><strong>Фамилия</strong>:<br>Фамилия пользователя.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Иванов</span></li>\r\n<li class=\"break-words\"><strong>Никнейм</strong>:<br>Уникальное имя для отображения.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">ivanov_ivan</span></li>\r\n<li class=\"break-words\"><strong>О себе</strong>:<br>Краткое описание пользователя.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Занимаюсь заказами услуг</span></li>\r\n<li class=\"break-words\"><strong>Город</strong>:<br>Местоположение.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Без города</span> (по умолчанию)</li>\r\n<li class=\"break-words\"><strong>Категория</strong>:<br>Тип или группа пользователя.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Без категории</span> (по умолчанию)</li>\r\n<li class=\"break-words\"><strong>Фото профиля</strong>:<br>Загрузите аватар пользователя.<br><em>Статус</em>: Файл не выбран.</li>\r\n<li class=\"break-words\"><strong>Профиль заполнен</strong>:<br>Автоматический показатель заполненности данных (в %).</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Как использовать</h4>\r\n<ol class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Перейдите в раздел <strong>\"Управление пользователями\"</strong> в админ-панели CMS.</li>\r\n<li class=\"break-words\">Используйте <strong>\"Поиск по всему\"</strong> для фильтрации списка пользователей.</li>\r\n<li class=\"break-words\">Просмотрите <strong>\"Список пользователей\"</strong>, отредактируйте или удалите профиль через колонку \"Действия\".</li>\r\n<li class=\"break-words\">Нажмите <strong>\"Добавить нового пользователя\"</strong>, заполните поля (контакт, пароль, роль и др.), сохраните данные.</li>\r\n</ol>\r\n<p class=\"break-words\" dir=\"auto\">Модуль \"Управление пользователями\" в Pro Website Management предоставляет удобный интерфейс для контроля пользовательских данных, ролей и профилей, упрощая администрирование сайта.</p>\r\n</div>', 'Управление пользователями, Список пользователей, Добавить нового пользователя', '[\"67f823dd64899-admin_1..jpg\",\"67f823dd64d30-admin_2..jpg\"]', '/users-admin', '2025-04-10 20:02:37', 4, 'Управление пользователями : Административная часть', 'Pro Website Management позволяет администраторам искать, просматривать, редактировать и добавлять пользователей сайта, а также управлять их ролями и профилями.', 'Управление пользователями : Административная часть', 'Pro Website Management позволяет администраторам искать, просматривать, редактировать и добавлять пользователей сайта, а также управлять их ролями и профилями.', 'Управление пользователями : Административная часть', 'Pro Website Management позволяет администраторам искать, просматривать, редактировать и добавлять пользователей сайта, а также управлять их ролями и профилями.', 1, 0),
(28, 'Управление бэкапами MySQL', 'Управление бэкапами в CMS Pro Website Management позволяет создавать, восстанавливать и настраивать автоматическое сохранение резервных копий базы данных.', '<div>\r\n<h3 class=\"\" dir=\"auto\">Административная часть: Управление бэкапами</h3>\r\n<p class=\"break-words\" dir=\"auto\">Модуль <strong>\"Управление бэкапами\"</strong> в CMS Pro Website Management позволяет создавать, восстанавливать и настраивать автоматическое сохранение резервных копий базы данных. Размер базы: <strong>1.27 MB</strong>.</p>\r\n<h4 class=\"\" dir=\"auto\">Создать бэкап</h4>\r\n<p class=\"break-words\" dir=\"auto\">Функция <strong>\"Создать бэкап\"</strong> позволяет вручную сохранить данные.</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Выберите таблицы (оставьте пустым для всех)</strong>:<br>Укажите таблицы для включения в бэкап или оставьте поле пустым для сохранения всей базы.<br><em>Пример списка таблиц</em>:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">admins</span> (0.03 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">banner_slider</span> (0.02 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">banners</span> (0.02 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">brand_carousel</span> (0.02 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">carousel</span> (0.02 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">carousel_settings</span> (0.02 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">categories</span> (0.03 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">cities</span> (0.02 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">documents</span> (0.03 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">feedback</span> (0.03 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">gallery</span> (0.03 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">news</span> (0.30 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">news_categories</span> (0.05 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">order_history</span> (0.03 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">pages</span> (0.08 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">prices</span> (0.02 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">reviews</span> (0.05 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">settings</span> (0.02 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">shop_categories</span> (0.03 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">shop_delivery_methods</span> (0.02 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">shop_orders</span> (0.05 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">shop_products</span> (0.08 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">tenders</span> (0.05 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">users</span> (0.08 MB)</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">visitor_logs</span> (0.19 MB)</li>\r\n</ul>\r\n</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Восстановить бэкап</h4>\r\n<p class=\"break-words\" dir=\"auto\">Функция <strong>\"Восстановить бэкап\"</strong> позволяет загрузить сохранённые данные.</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Выберите бэкап</strong>:<br>Выберите файл резервной копии из списка доступных.<br><em>По умолчанию</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">-- Выберите файл --</span></li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Настройки автосохранения</h4>\r\n<p class=\"break-words\" dir=\"auto\">Настройка автоматического создания бэкапов.</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Включить автосохранение</strong>:<br>Активируйте автоматическое создание резервных копий.</li>\r\n<li class=\"break-words\"><strong>Частота</strong>:<br>Укажите интервал сохранения.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">Каждые 3 часа</span></li>\r\n<li class=\"break-words\"><strong>Максимум бэкапов</strong>:<br>Ограничение количества хранимых копий.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">20</span> (старые бэкапы удаляются при превышении лимита).</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Инструкция по использованию</h4>\r\n<ol class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Создание бэкапа</strong>:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Перейдите в раздел <strong>\"Управление бэкапами\"</strong> в админ-панели.</li>\r\n<li class=\"break-words\">В блоке <strong>\"Создать бэкап\"</strong> выберите нужные таблицы или оставьте пустым для всей базы.</li>\r\n<li class=\"break-words\">Нажмите кнопку создания &mdash; файл сохранится на сервере.</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Восстановление бэкапа</strong>:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">В блоке <strong>\"Восстановить бэкап\"</strong> выберите сохранённый файл из списка.</li>\r\n<li class=\"break-words\">Подтвердите восстановление &mdash; данные базы будут заменены.</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Настройка автосохранения</strong>:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Включите <strong>\"Автосохранение\"</strong> в соответствующем блоке.</li>\r\n<li class=\"break-words\">Установите <strong>\"Частоту\"</strong> (например, каждые 3 часа) и <strong>\"Максимум бэкапов\"</strong> (например, 20).</li>\r\n<li class=\"break-words\">Сохраните настройки &mdash; система начнёт автоматически создавать копии.</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Проверка</strong>:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Убедитесь, что бэкапы создаются и доступны для восстановления.</li>\r\n<li class=\"break-words\">Регулярно проверяйте размер базы (1.27 MB) и состояние хранилища.</li>\r\n</ul>\r\n</li>\r\n</ol>\r\n<p class=\"break-words\" dir=\"auto\">Модуль \"Управление бэкапами\" в Pro Website Management обеспечивает надежное резервное копирование данных с гибкими настройками для защиты сайта.</p>\r\n</div>', 'Управление бэкапами, MySQL, Backup MySQL, Управление бэкапами MySQL', '[\"67f84c900ae7d-IMG_0992..jpg\",\"67f84c900b728-IMG_0993..jpg\"]', 'backup', '2025-04-10 20:09:03', 4, 'Управление бэкапами MySQL', 'Управление бэкапами в CMS Pro Website Management позволяет создавать, восстанавливать и настраивать автоматическое сохранение резервных копий базы данных.', 'Управление бэкапами MySQL', 'Управление бэкапами в CMS Pro Website Management позволяет создавать, восстанавливать и настраивать автоматическое сохранение резервных копий базы данных.', 'Управление бэкапами MySQL', 'Управление бэкапами в CMS Pro Website Management позволяет создавать, восстанавливать и настраивать автоматическое сохранение резервных копий базы данных.', 1, 0),
(29, 'Файловый менеджер', 'Предоставляет доступ к файловой системе сайта, позволяя просматривать, загружать, редактировать и удалять файлы и папки. Текущая директория: / (корневая).', '<div>\r\n<h3 class=\"\" dir=\"auto\">Административная часть: Файловый менеджер</h3>\r\n<p class=\"break-words\" dir=\"auto\">Модуль <strong>\"Файловый менеджер\"</strong> в CMS Pro Website Management предоставляет доступ к файловой системе сайта, позволяя просматривать, загружать, редактировать и удалять файлы и папки. Текущая директория: <strong>/</strong> (корневая).</p>\r\n<h4 class=\"\" dir=\"auto\">Интерфейс файлового менеджера</h4>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Текущая директория</strong>:<br>Отображает текущий путь в файловой системе.<br><em>Пример</em>: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">/</span></li>\r\n<li class=\"break-words\"><strong>Загрузка файла</strong>:<br>Поле для загрузки нового файла.<br><em>Статус</em>: Файл не выбран.</li>\r\n</ul>\r\n<h4 class=\"\" dir=\"auto\">Список файлов и папок</h4>\r\n<p class=\"break-words\" dir=\"auto\">Раздел отображает содержимое текущей директории в виде таблицы:</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Имя</strong>: Название файла или папки.</li>\r\n<li class=\"break-words\"><strong>Формат</strong>: Тип (папка или расширение файла).</li>\r\n<li class=\"break-words\"><strong>Размер</strong>: Размер файла (для папок &mdash; не отображается).</li>\r\n<li class=\"break-words\"><strong>Дата</strong>: Дата создания или изменения.</li>\r\n<li class=\"break-words\"><strong>Действия</strong>: Кнопки для просмотра, редактирования, скачивания или удаления.</li>\r\n</ul>\r\n<p class=\"break-words\" dir=\"auto\"><em>Пример содержимого</em>:</p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">admin</span> | Папка | &mdash; | 10.04.2025 18:34 | Действия</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">backups</span> | Папка | &mdash; | 05.04.2025 15:58 | Действия</li>\r\n<li class=\"break-words\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">cache</span> | Папка | &mdash; | 08.04.2025 10:56 | Действия</li>\r\n</ul>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Функционал</h4>\r\n<ol class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Навигация</strong>:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Переходите в папки, кликая по их названиям.</li>\r\n<li class=\"break-words\">Возвращайтесь на уровень выше с помощью кнопки \"Вверх\".</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Загрузка</strong>:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Выберите файл в поле загрузки и подтвердите для добавления в текущую директорию.</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Действия</strong>:\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Просмотр</strong>: Открытие текстовых файлов для чтения.</li>\r\n<li class=\"break-words\"><strong>Редактирование</strong>: Изменение содержимого (например, <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">.htaccess</span> или <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">.php</span>).</li>\r\n<li class=\"break-words\"><strong>Скачивание</strong>: Загрузка файла на устройство.</li>\r\n<li class=\"break-words\"><strong>Удаление</strong>: Удаление файла или папки с подтверждением.</li>\r\n</ul>\r\n</li>\r\n</ol>\r\n<hr>\r\n<h4 class=\"\" dir=\"auto\">Как использовать</h4>\r\n<ol class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Перейдите в раздел <strong>\"Файловый менеджер\"</strong> в админ-панели CMS.</li>\r\n<li class=\"break-words\">Просмотрите содержимое текущей директории (например, <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">/</span>).</li>\r\n<li class=\"break-words\">Для перехода в папку (например, <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">uploads</span>) кликните по её имени.</li>\r\n<li class=\"break-words\">Загрузите новый файл, выбрав его в поле загрузки.</li>\r\n<li class=\"break-words\">Используйте кнопки в колонке <strong>\"Действия\"</strong> для управления файлами (редактирование, скачивание, удаление).</li>\r\n</ol>\r\n<p class=\"break-words\" dir=\"auto\">Модуль \"Файловый менеджер\" в Pro Website Management обеспечивает удобный доступ к файловой системе сайта, упрощая управление контентом и конфигурацией.</p>\r\n</div>', 'Файловый менеджер', '[\"67f826db6617d-file_manager..jpg\",\"67f826db6671a-file_manager_en..jpg\"]', 'fille-manager', '2025-04-10 20:15:23', 4, 'Файловый менеджер : Административная часть', 'Предоставляет доступ к файловой системе сайта, позволяя просматривать, загружать, редактировать и удалять файлы и папки. Текущая директория: / (корневая).', 'Файловый менеджер : Административная часть', 'Предоставляет доступ к файловой системе сайта, позволяя просматривать, загружать, редактировать и удалять файлы и папки. Текущая директория: / (корневая).', 'Файловый менеджер : Административная часть', 'Предоставляет доступ к файловой системе сайта, позволяя просматривать, загружать, редактировать и удалять файлы и папки. Текущая директория: / (корневая).', 1, 0),
(30, 'Генерация карты сайта Sitemap.xml ', 'Модуль предназначен для генерации, управления и уведомления поисковых систем о картах сайта (sitemap) для веб-сайта ', '<p class=\"break-words\" dir=\"auto\"><span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">демо: <a href=\"index.php?module=sitemap\">/admin/index.php?module=sitemap</a><br></span><br><br></p>\r\n<p class=\"break-words\" dir=\"auto\">Pro Website Management CMS. Он позволяет администратору создавать XML-файлы sitemap для различных категорий контента, отправлять уведомления в Google, Bing и Yandex, а также удалять существующие карты сайта. Модуль предоставляет удобный интерфейс для мониторинга статуса файлов и настройки приоритетов страниц.</p>\r\n<p class=\"break-words\" dir=\"auto\"><strong>Основные функции:</strong></p>\r\n<ol class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Генерация карт сайта:</strong>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Создание индивидуальных sitemap для категорий: Новости, Категории новостей, Товары, Категории товаров, Страницы, Тендеры, Категории тендеров.</li>\r\n<li class=\"break-words\">Генерация индексного файла <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">sitemap.xml</span>, объединяющего ссылки на все дочерние карты.</li>\r\n<li class=\"break-words\">Поддержка массовой генерации всех карт сайта за один запрос (<span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">action=all</span>).</li>\r\n<li class=\"break-words\">Отображение количества URL в каждой сгенерированной карте.</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Уведомление поисковых систем:</strong>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Отправка пинг-запросов в Google (<span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">https://www.google.com/ping</span>), Bing (<span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">https://www.bing.com/webmaster/ping.aspx</span>) и Yandex (<span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">https://webmaster.yandex.ru/ping</span>) с указанием URL карты сайта.</li>\r\n<li class=\"break-words\">Улучшенная диагностика: отображение HTTP-кодов и ошибок при неудачных запросах (например, \"Ошибка (HTTP 404: No error)\").</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Удаление карт сайта:</strong>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Возможность удаления любого файла sitemap из директории <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">/uploads/</span> через интерфейс.</li>\r\n<li class=\"break-words\">Подтверждение удаления с помощью всплывающего окна для предотвращения случайных действий.</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Мониторинг статуса:</strong>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Таблица статуса файлов с информацией о наличии, количестве URL и дате последнего обновления.</li>\r\n<li class=\"break-words\">Иконки статуса: зеленый чек для существующих файлов, красный крест для отсутствующих.</li>\r\n</ul>\r\n</li>\r\n<li class=\"break-words\"><strong>Настройка приоритетов:</strong>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Интерактивная форма для установки значений приоритета (от 0.1 до 1.0) для каждой категории.</li>\r\n<li class=\"break-words\">Подсказки с рекомендациями по выбору приоритетов для разных типов контента.</li>\r\n</ul>\r\n</li>\r\n</ol>\r\n<p class=\"break-words\" dir=\"auto\"><strong>Технические особенности:</strong></p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Язык:</strong> PHP с использованием MySQLi для запросов к базе данных.</li>\r\n<li class=\"break-words\"><strong>Зависимости:</strong> Файлы <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">db.php</span> и <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">functions.php</span> из директории <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">/includes/</span> для подключения к базе данных и проверки прав администратора.</li>\r\n<li class=\"break-words\"><strong>Вывод:</strong> HTML-интерфейс с использованием Font Awesome для иконок и адаптивного CSS для стилизации.</li>\r\n<li class=\"break-words\"><strong>Логирование:</strong> Запись результатов пинг-запросов в лог ошибок PHP для диагностики.</li>\r\n<li class=\"break-words\"><strong>Безопасность:</strong> Проверка прав администратора через функцию <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">isAdmin()</span> и кодирование URL через <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">urlencode()</span>.</li>\r\n</ul>\r\n<p class=\"break-words\" dir=\"auto\"><strong>Используемые директории:</strong></p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Карты сайта сохраняются в <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">/uploads/</span> (например, <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">/public_html/uploads/</span>).</li>\r\n<li class=\"break-words\">Требуются права: 755 для директории и 644 для файлов, владелец &mdash; пользователь веб-сервера (обычно <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">www-data</span>).</li>\r\n</ul>\r\n<p class=\"break-words\" dir=\"auto\"><strong>Интерфейс:</strong></p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\"><strong>Действия:</strong> Кнопки для генерации всех карт, уведомления поисковых систем и генерации по категориям.</li>\r\n<li class=\"break-words\"><strong>Статус:</strong> Таблица с колонками: \"Файл\", \"Статус\", \"Количество URL\", \"Последнее обновление\", \"Действие\" (с кнопкой \"Удалить\").</li>\r\n<li class=\"break-words\"><strong>Справка:</strong> Раскрывающийся блок с описанием функций модуля.</li>\r\n</ul>\r\n<p class=\"break-words\" dir=\"auto\"><strong>Пример использования:</strong></p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Генерация всех карт: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">https://masterok.lt/admin/?module=sitemap&amp;action=all</span></li>\r\n<li class=\"break-words\">Уведомление поисковиков: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">/admin/?module=sitemap&amp;action=notify</span></li>\r\n<li class=\"break-words\">Удаление файла: <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">/admin/?module=sitemap&amp;action=delete&amp;file=sitemap_news.xml</span></li>\r\n</ul>\r\n<p class=\"break-words\" dir=\"auto\"><strong>Результаты работы:</strong></p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">После генерации: \"Новости: успешно сгенерировано (10 URL)\".</li>\r\n<li class=\"break-words\">После уведомления: \"Уведомления отправлены: Google: Успех, Bing: Успех, Yandex: Успех\".</li>\r\n<li class=\"break-words\">После удаления: \"Файл sitemap_news.xml успешно удален.\"</li>\r\n</ul>\r\n<p class=\"break-words\" dir=\"auto\"><strong>Примечания:</strong></p>\r\n<ul class=\"marker:text-secondary\" dir=\"auto\">\r\n<li class=\"break-words\">Модуль автоматически создает директорию <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">/uploads/</span>, если она отсутствует, с правами 755.</li>\r\n<li class=\"break-words\">Для корректной работы уведомлений требуется доступ к внешним URL через cURL с установленным User-Agent.</li>\r\n<li class=\"break-words\">Рекомендуется проверять права доступа к файлам и директориям при возникновении ошибок записи или удаления.</li>\r\n</ul>', 'Генерация карты сайта, Sitemap.xml, Sitemap, Карта сайта, ', '[\"67f841388220a-main-ru..jpg\",\"67f841494058e-category en..jpg\",\"67f84149409aa-category..jpg\",\"67f8414940d33-main-en..jpg\"]', 'sitemap', '2025-04-10 22:07:52', 4, 'Генерация карты сайта Sitemap.xml ', 'Модуль предназначен для генерации, управления и уведомления поисковых систем о картах сайта (sitemap) для веб-сайта ', 'Генерация карты сайта Sitemap.xml ', 'Модуль предназначен для генерации, управления и уведомления поисковых систем о картах сайта (sitemap) для веб-сайта ', 'Генерация карты сайта Sitemap.xml ', 'Модуль предназначен для генерации, управления и уведомления поисковых систем о картах сайта (sitemap) для веб-сайта ', 1, 0),
(31, 'Интеграция с Новой Почтой в CMS Pro Shop 🚚', 'Интеграция Новой Почты в CMS Pro Shop позволяет автоматически оформлять доставки, печатать ТТН и отслеживать заказы прямо из админки. ', '<p class=\"\" data-start=\"268\" data-end=\"477\">С помощью встроенной интеграции с <strong data-start=\"302\" data-end=\"320\">\"Новой Почтой\"</strong>, вы можете автоматически оформлять и отслеживать отправки прямо из вашей CMS. Всё, что вам нужно &mdash; это активный API-ключ от Nova Poshta и базовая настройка.</p>\r\n<hr class=\"\" data-start=\"479\" data-end=\"482\">\r\n<h3 class=\"\" data-start=\"484\" data-end=\"520\">🔧 Основные параметры интеграции</h3>\r\n<div class=\"pointer-events-none relative left-[50%]! flex w-[100cqw] translate-x-[-50%] justify-center *:pointer-events-auto\">\r\n<div class=\"tableContainer horzScrollShadows\">\r\n<table class=\"min-w-full\" data-start=\"522\" data-end=\"1712\">\r\n<thead data-start=\"522\" data-end=\"545\">\r\n<tr data-start=\"522\" data-end=\"545\">\r\n<th data-start=\"522\" data-end=\"533\">Параметр</th>\r\n<th data-start=\"533\" data-end=\"545\">Описание</th>\r\n</tr>\r\n</thead>\r\n<tbody data-start=\"569\" data-end=\"1712\">\r\n<tr data-start=\"569\" data-end=\"708\">\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)]\" data-start=\"569\" data-end=\"584\"><strong data-start=\"571\" data-end=\"583\">API ключ</strong></td>\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)] min-w-[calc(var(--thread-content-max-width)/2)]\" data-start=\"584\" data-end=\"708\">Уникальный ключ для подключения к сервису Новой Почты. Можно выбрать из списка или добавить новый в разделе <strong data-start=\"694\" data-end=\"705\">\"Ключи\"</strong>.</td>\r\n</tr>\r\n<tr data-start=\"709\" data-end=\"797\">\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)]\" data-start=\"709\" data-end=\"733\"><strong data-start=\"711\" data-end=\"732\">Город отправителя</strong></td>\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)] min-w-[calc(var(--thread-content-max-width)/3)]\" data-start=\"733\" data-end=\"797\">Город, откуда будут отправляться все заказы. Пример: <code data-start=\"788\" data-end=\"794\">Киев</code>.</td>\r\n</tr>\r\n<tr data-start=\"798\" data-end=\"904\">\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)]\" data-start=\"798\" data-end=\"822\"><strong data-start=\"800\" data-end=\"821\">Название магазина</strong></td>\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)] min-w-[calc(var(--thread-content-max-width)/3)]\" data-start=\"822\" data-end=\"904\">Название, которое будет отображаться в TTН (накладной). Пример: <code data-start=\"888\" data-end=\"901\">Мой Магазин</code>.</td>\r\n</tr>\r\n<tr data-start=\"905\" data-end=\"1002\">\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)]\" data-start=\"905\" data-end=\"927\"><strong data-start=\"907\" data-end=\"926\">Тип плательщика</strong></td>\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)] min-w-[calc(var(--thread-content-max-width)/3)]\" data-start=\"927\" data-end=\"1002\">Кто оплачивает доставку: <code data-start=\"954\" data-end=\"966\">Получатель</code>, <code data-start=\"968\" data-end=\"981\">Отправитель</code> или <code data-start=\"986\" data-end=\"999\">Третье лицо</code>.</td>\r\n</tr>\r\n<tr data-start=\"1003\" data-end=\"1074\">\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)]\" data-start=\"1003\" data-end=\"1022\"><strong data-start=\"1005\" data-end=\"1021\">Метод оплаты</strong></td>\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)] min-w-[calc(var(--thread-content-max-width)/3)]\" data-start=\"1022\" data-end=\"1074\">Способ оплаты за доставку: <code data-start=\"1051\" data-end=\"1061\">Наличные</code>, <code data-start=\"1063\" data-end=\"1071\">Безнал</code>.</td>\r\n</tr>\r\n<tr data-start=\"1075\" data-end=\"1194\">\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)]\" data-start=\"1075\" data-end=\"1094\"><strong data-start=\"1077\" data-end=\"1093\">Тип доставки</strong></td>\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)] min-w-[calc(var(--thread-content-max-width)/2)]\" data-start=\"1094\" data-end=\"1194\">Выберите тип услуги: <code data-start=\"1117\" data-end=\"1138\">Отделение-Отделение</code>, <code data-start=\"1140\" data-end=\"1157\">Отделение-Дверь</code>, <code data-start=\"1159\" data-end=\"1176\">Дверь-Отделение</code>, <code data-start=\"1178\" data-end=\"1191\">Дверь-Дверь</code>.</td>\r\n</tr>\r\n<tr data-start=\"1195\" data-end=\"1278\">\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)]\" data-start=\"1195\" data-end=\"1211\"><strong data-start=\"1197\" data-end=\"1210\">Тип груза</strong></td>\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)] min-w-[calc(var(--thread-content-max-width)/3)]\" data-start=\"1211\" data-end=\"1278\">Вид отправляемого товара: <code data-start=\"1239\" data-end=\"1245\">Груз</code>, <code data-start=\"1247\" data-end=\"1256\">Посылка</code>, <code data-start=\"1258\" data-end=\"1269\">Документы</code> и т.д.</td>\r\n</tr>\r\n<tr data-start=\"1279\" data-end=\"1374\">\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)]\" data-start=\"1279\" data-end=\"1301\"><strong data-start=\"1281\" data-end=\"1300\">Ref контрагента</strong></td>\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)] min-w-[calc(var(--thread-content-max-width)/3)]\" data-start=\"1301\" data-end=\"1374\">Уникальный идентификатор получателя в системе НП (при необходимости).</td>\r\n</tr>\r\n<tr data-start=\"1375\" data-end=\"1481\">\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)]\" data-start=\"1375\" data-end=\"1406\"><strong data-start=\"1377\" data-end=\"1405\">Комментарий по умолчанию</strong></td>\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)] min-w-[calc(var(--thread-content-max-width)/3)]\" data-start=\"1406\" data-end=\"1481\">Комментарий, который будет автоматически добавляться к каждой отправке.</td>\r\n</tr>\r\n<tr data-start=\"1482\" data-end=\"1614\">\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)]\" data-start=\"1482\" data-end=\"1506\"><strong data-start=\"1484\" data-end=\"1505\">Обратная доставка</strong></td>\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)] min-w-[calc(var(--thread-content-max-width)/2)]\" data-start=\"1506\" data-end=\"1614\">Если включено &mdash; будет создана обратная накладная (например, возврат документов или наложенного платежа).</td>\r\n</tr>\r\n<tr data-start=\"1615\" data-end=\"1712\">\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)]\" data-start=\"1615\" data-end=\"1630\"><strong data-start=\"1617\" data-end=\"1629\">Упаковка</strong></td>\r\n<td class=\"max-w-[calc(var(--thread-content-max-width)*2/3)] min-w-[calc(var(--thread-content-max-width)/3)]\" data-start=\"1630\" data-end=\"1712\">При активации &mdash; автоматически добавляется стандартная упаковка от Новой Почты.</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>\r\n</div>\r\n<hr class=\"\" data-start=\"1714\" data-end=\"1717\">\r\n<h3 class=\"\" data-start=\"1719\" data-end=\"1744\">🧩 Раздел <strong data-start=\"1733\" data-end=\"1744\">\"Ключи\"</strong></h3>\r\n<p class=\"\" data-start=\"1746\" data-end=\"1771\">В этом разделе вы можете:</p>\r\n<ul data-start=\"1773\" data-end=\"1979\">\r\n<li class=\"\" data-start=\"1773\" data-end=\"1811\">\r\n<p class=\"\" data-start=\"1775\" data-end=\"1811\">Добавлять и редактировать API-ключи;</p>\r\n</li>\r\n<li class=\"\" data-start=\"1812\" data-end=\"1848\">\r\n<p class=\"\" data-start=\"1814\" data-end=\"1848\">Привязывать их к разным магазинам;</p>\r\n</li>\r\n<li class=\"\" data-start=\"1849\" data-end=\"1901\">\r\n<p class=\"\" data-start=\"1851\" data-end=\"1901\">Указывать параметры оплаты, доставки и тип услуги;</p>\r\n</li>\r\n<li class=\"\" data-start=\"1902\" data-end=\"1979\">\r\n<p class=\"\" data-start=\"1904\" data-end=\"1979\">Оставлять комментарии для удобства (например, \"Интернет-магазин на тесте\").</p>\r\n</li>\r\n</ul>\r\n<h5><br><br>Как настроить API Новой Почты</h5>\r\n<ol>\r\n<li>Зарегистрируйтесь на сайте&nbsp;<a href=\"https://novaposhta.ua/\" target=\"_blank\" rel=\"noopener\">Новой Почты</a>.</li>\r\n<li>В личном кабинете получите API ключ.</li>\r\n<li>В разделе \"Ключи\" добавьте новый ключ, указав его и название магазина.</li>\r\n<li>В разделе \"Основные настройки\" выберите активный ключ и укажите город отправителя.</li>\r\n<li>Настройте тип плательщика, метод оплаты и другие параметры.</li>\r\n<li>Активируйте ключ, если он не активен (только один ключ может быть активным).</li>\r\n<li>Проверьте работу доставки в клиентской части магазина.</li>\r\n</ol>\r\n<p><strong>Примечание:</strong> Для автодополнения городов используйте поле \"Город отправителя\".</p>', 'Новая Почта интеграция, CMS Pro Shop, доставка, API ключ Новая Почта, ТТН', '[\"67fd90efdbc70-fullscreen..jpg\",\"67fd9143c165c-admin1..jpg\",\"67fd9143c1a6c-admin2..jpg\",\"67fd9143c1cd6-admin3..jpg\",\"67fd9143c1fba-admin4..jpg\",\"67fd9143c22c9-admin5..jpg\",\"67fd9143c255f-traking..jpg\",\"67fd949ea03f3-admin22..jpg\"]', 'api_newpost', '2025-04-14 22:49:19', 8, 'Интеграция с Новой Почтой в CMS Pro Shop 🚚', 'Интеграция Новой Почты в CMS Pro Shop позволяет автоматически оформлять доставки, печатать ТТН и отслеживать заказы прямо из админки. ', 'Интеграция с Новой Почтой в CMS Pro Shop 🚚', 'Интеграция Новой Почты в CMS Pro Shop позволяет автоматически оформлять доставки, печатать ТТН и отслеживать заказы прямо из админки. ', 'Интеграция с Новой Почтой в CMS Pro Shop 🚚', 'Интеграция Новой Почты в CMS Pro Shop позволяет автоматически оформлять доставки, печатать ТТН и отслеживать заказы прямо из админки. ', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблиці `news_categories`
--

CREATE TABLE `news_categories` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `custom_url` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_desc` text DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_desc` text DEFAULT NULL,
  `twitter_title` varchar(255) DEFAULT NULL,
  `twitter_desc` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `news_categories`
--

INSERT INTO `news_categories` (`id`, `parent_id`, `title`, `description`, `keywords`, `custom_url`, `meta_title`, `meta_desc`, `og_title`, `og_desc`, `twitter_title`, `twitter_desc`, `created_at`) VALUES
(2, NULL, 'Скрипты PHP', 'Скрипт услуг, тендера, сайт услуг, СЕО, SEO 2025', '', 'php', 'Скрипты PHP', 'Скрипт услуг, тендера, сайт услуг, СЕО, SEO 2025', 'Скрипты PHP', 'Скрипт услуг, тендера, сайт услуг, СЕО, SEO 2025', NULL, NULL, '2025-03-30 20:40:27'),
(4, NULL, 'Pro Website Management', 'Pro Website Management CMS (Content Management System) – это система управления контентом, для создания, редактирования и администрирования веб-сайтов.', 'Pro Website Management CMS, Pro Website, ProShop', 'Website-Management', 'Pro Website Management CMS', 'Pro Website Management – это система управления контентом, предназначенная для создания, редактирования и администрирования веб-сайтов.  Основные функции  CMS Управление контентом  Добавление, редактирование и удаление страниц.', 'Pro Website Management CMS', 'Pro Website Management – это система управления контентом, предназначенная для создания, редактирования и администрирования веб-сайтов.  Основные функции  CMS Управление контентом  Добавление, редактирование и удаление страниц.', NULL, NULL, '2025-03-31 20:50:33'),
(6, NULL, 'SEO', 'SEO', '', 'seo', 'SEO', 'SEO', 'SEO', 'SEO', NULL, NULL, '2025-03-31 21:41:31'),
(8, NULL, 'Pro Shop Management Engine', 'Pro Shop Management Engine CMS — это мощная и гибкая система управления контентом, разработанная специально для интернет-магазинов и торговых платформ.', 'Pro Shop Management Engine, Pro Shop, Pro Shop Management, Интернет магазин скрипт 2025', 'pro-shop-management-engine', 'Pro Shop Management Engine', 'Pro Shop Management Engine CMS — это мощная и гибкая система управления контентом, разработанная специально для интернет-магазинов и торговых платформ.', 'Pro Shop Management Engine', 'Pro Shop Management Engine CMS — это мощная и гибкая система управления контентом, разработанная специально для интернет-магазинов и торговых платформ.', NULL, NULL, '2025-04-06 21:11:08');

-- --------------------------------------------------------

--
-- Структура таблиці `news_reviews`
--

CREATE TABLE `news_reviews` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 10),
  `review_text` text NOT NULL,
  `is_approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `guest_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `news_reviews`
--

INSERT INTO `news_reviews` (`id`, `news_id`, `user_id`, `rating`, `review_text`, `is_approved`, `created_at`, `guest_name`) VALUES
(3, 31, NULL, 1, 'Ваш отзыв', 1, '2025-04-17 16:57:18', 'Ruslan'),
(4, 31, NULL, 3, 'Отзыв 2', 1, '2025-04-17 16:58:14', 'Ruslan2'),
(5, 31, 22, 2, '1', 1, '2025-04-17 16:58:43', NULL),
(6, 31, 22, 8, '<p>куц</p>', 0, '2025-04-17 20:12:54', '');

-- --------------------------------------------------------

--
-- Структура таблиці `news_translations`
--

CREATE TABLE `news_translations` (
  `id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL,
  `language_code` varchar(10) NOT NULL,
  `title` varchar(255) NOT NULL,
  `short_desc` text NOT NULL,
  `full_desc` text DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_desc` text DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_desc` text DEFAULT NULL,
  `twitter_title` varchar(255) DEFAULT NULL,
  `twitter_desc` text DEFAULT NULL,
  `custom_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `nova_poshta_cities`
--

CREATE TABLE `nova_poshta_cities` (
  `ref` varchar(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `nova_poshta_settings`
--

CREATE TABLE `nova_poshta_settings` (
  `id` int(11) NOT NULL,
  `city_sender_ref` varchar(36) NOT NULL,
  `city_sender_name` varchar(255) NOT NULL,
  `cargo_type` enum('Cargo','Documents','TiresWheels','Pallet') DEFAULT 'Cargo',
  `service_type_default` enum('WarehouseWarehouse','DoorsWarehouse','WarehouseDoors','DoorsDoors') DEFAULT 'WarehouseWarehouse',
  `redelivery_enabled` tinyint(1) DEFAULT 0,
  `redelivery_cargo_type` enum('Money','Cargo') DEFAULT 'Money',
  `redelivery_amount` decimal(10,2) DEFAULT 0.00,
  `pack_enabled` tinyint(1) DEFAULT 0,
  `pack_ref` varchar(36) DEFAULT '',
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `nova_poshta_settings`
--

INSERT INTO `nova_poshta_settings` (`id`, `city_sender_ref`, `city_sender_name`, `cargo_type`, `service_type_default`, `redelivery_enabled`, `redelivery_cargo_type`, `redelivery_amount`, `pack_enabled`, `pack_ref`, `updated_at`) VALUES
(1, '8d5a980d-391c-11dd-90d9-001a92567626', 'Киев', 'Cargo', 'WarehouseWarehouse', 0, 'Money', 0.00, 0, '', '2025-04-14 21:22:44'),
(2, '8d5a980d-391c-11dd-90d9-001a92567626', 'Киев', 'Cargo', 'WarehouseWarehouse', 0, 'Money', 0.00, 0, '', '2025-04-14 21:33:23'),
(3, '8d5a980d-391c-11dd-90d9-001a92567626', 'Киев', 'Cargo', 'WarehouseWarehouse', 0, 'Money', 0.00, 0, '', '2025-04-14 22:06:02');

-- --------------------------------------------------------

--
-- Структура таблиці `nova_poshta_streets`
--

CREATE TABLE `nova_poshta_streets` (
  `ref` varchar(36) NOT NULL,
  `city_ref` varchar(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `nova_poshta_warehouses`
--

CREATE TABLE `nova_poshta_warehouses` (
  `ref` varchar(36) NOT NULL,
  `city_ref` varchar(36) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `order_history`
--

CREATE TABLE `order_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `changed_by` varchar(100) DEFAULT NULL,
  `changed_at` datetime DEFAULT current_timestamp(),
  `field_name` varchar(50) NOT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `url` varchar(255) DEFAULT NULL,
  `no_index` tinyint(1) DEFAULT 0,
  `file_path` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `pages`
--

INSERT INTO `pages` (`id`, `title`, `content`, `created_at`, `url`, `no_index`, `file_path`, `is_published`) VALUES
(1, 'Политика конфиденциальности', '<p><strong>Политика конфиденциальности Tender CMS</strong></p>\r\n<p><strong>Дата вступления в силу:</strong> 1 апреля 2025 г.</p>\r\n<p>Tender CMS уважает вашу конфиденциальность и обязуется защищать персональные данные пользователей. Настоящая Политика конфиденциальности объясняет, какие данные мы собираем, как их используем и какие у вас есть права в соответствии с <strong>Общим регламентом ЕС по защите данных (GDPR)</strong> и <strong>Законом Украины \"О защите персональных данных\"</strong>.</p>\r\n<hr>\r\n<h3><strong>1. Какие данные мы собираем?</strong></h3>\r\n<p>Мы можем собирать и обрабатывать следующие категории персональных данных:</p>\r\n<ul>\r\n<li>\r\n<p><strong>Контактные данные</strong> (имя, адрес электронной почты, номер телефона);</p>\r\n</li>\r\n<li>\r\n<p><strong>Данные аккаунта</strong> (логин, пароль, IP-адрес, файлы cookie);</p>\r\n</li>\r\n<li>\r\n<p><strong>Платежные данные</strong> (если применимо);</p>\r\n</li>\r\n<li>\r\n<p><strong>Данные об использовании системы</strong> (активность, предпочтения, технические журналы).</p>\r\n</li>\r\n</ul>\r\n<h3><strong>2. Как мы используем ваши данные?</strong></h3>\r\n<p>Мы обрабатываем персональные данные для следующих целей:</p>\r\n<ul>\r\n<li>\r\n<p>Для предоставления доступа к Tender CMS и обеспечения его работы;</p>\r\n</li>\r\n<li>\r\n<p>Для улучшения функционала и безопасности платформы;</p>\r\n</li>\r\n<li>\r\n<p>Для соблюдения наших юридических обязательств;</p>\r\n</li>\r\n<li>\r\n<p>Для связи с вами и поддержки пользователей;</p>\r\n</li>\r\n<li>\r\n<p>Для анализа использования системы и статистики.</p>\r\n</li>\r\n</ul>\r\n<h3><strong>3. Правовые основания обработки данных</strong></h3>\r\n<p>Обработка данных осуществляется на следующих законных основаниях:</p>\r\n<ul>\r\n<li>\r\n<p>С вашего <strong>согласия</strong> (ст. 6(1)(a) GDPR);</p>\r\n</li>\r\n<li>\r\n<p>Для выполнения <strong>договора</strong> с вами (ст. 6(1)(b) GDPR);</p>\r\n</li>\r\n<li>\r\n<p>Для соблюдения <strong>юридических обязательств</strong> (ст. 6(1)(c) GDPR);</p>\r\n</li>\r\n<li>\r\n<p>Для защиты наших <strong>законных интересов</strong>, если это не нарушает ваши права (ст. 6(1)(f) GDPR).</p>\r\n</li>\r\n</ul>\r\n<h3><strong>4. Как мы защищаем ваши данные?</strong></h3>\r\n<p>Мы применяем современные меры безопасности для защиты персональных данных, включая:</p>\r\n<ul>\r\n<li>\r\n<p>Шифрование данных;</p>\r\n</li>\r\n<li>\r\n<p>Ограничение доступа к информации;</p>\r\n</li>\r\n<li>\r\n<p>Защиту от несанкционированного доступа и утечек.</p>\r\n</li>\r\n</ul>\r\n<h3><strong>5. Передача данных третьим лицам</strong></h3>\r\n<p>Мы не передаем персональные данные третьим лицам без вашего согласия, за исключением случаев:</p>\r\n<ul>\r\n<li>\r\n<p>Когда это необходимо для предоставления услуг (например, хостинг, платежные системы);</p>\r\n</li>\r\n<li>\r\n<p>Когда этого требует законодательство;</p>\r\n</li>\r\n<li>\r\n<p>Если это необходимо для защиты наших законных прав.</p>\r\n</li>\r\n</ul>\r\n<h3><strong>6. Ваши права</strong></h3>\r\n<p>Согласно GDPR и законодательству Украины, вы имеете право:</p>\r\n<ul>\r\n<li>\r\n<p>Запрашивать доступ к своим данным;</p>\r\n</li>\r\n<li>\r\n<p>Требовать исправления или удаления данных;</p>\r\n</li>\r\n<li>\r\n<p>Ограничивать обработку данных;</p>\r\n</li>\r\n<li>\r\n<p>Возражать против обработки данных;</p>\r\n</li>\r\n<li>\r\n<p>Переносить данные в другой сервис;</p>\r\n</li>\r\n<li>\r\n<p>Отозвать согласие на обработку данных.</p>\r\n</li>\r\n</ul>\r\n<p>Вы можете реализовать свои права, связавшись с нами по электронной почте: <strong><a href=\"mailto:privacy@tendercms.com\">privacy@tendercms.com</a></strong>.</p>\r\n<h3><strong>7. Использование файлов cookie</strong></h3>\r\n<p>Мы используем файлы cookie для улучшения работы системы. Вы можете управлять настройками cookie через браузер.</p>\r\n<h3><strong>8. Изменения политики конфиденциальности</strong></h3>\r\n<p>Мы можем периодически обновлять настоящую Политику конфиденциальности. Все изменения будут опубликованы на нашем сайте с указанием даты вступления в силу.</p>\r\n<hr>\r\n<p>Если у вас есть вопросы или жалобы, пожалуйста, свяжитесь с нами: <strong><a href=\"mailto:privacy@tendercms.com\">privacy@tendercms.com</a></strong>. - почта пока не работает система в разработке..</p>\r\n<p><strong>Tender CMS &ndash; ваш надежный партнер в создании сайтов!</strong></p>', '2025-03-31 12:59:57', 'privacy-policy', 1, NULL, 1),
(5, 'Правила использования Tender CMS', '<p><strong>Дата вступления в силу:</strong> 1 апреля 2025 г.</p>\r\n<p>Добро пожаловать в Tender CMS! Используя наш программный продукт, вы соглашаетесь с данными Правилами использования. Если вы не согласны с условиями, пожалуйста, не используйте Tender CMS.</p>\r\n<hr>\r\n<h3><strong>1. Общие положения</strong></h3>\r\n<p>Tender CMS предоставляет гибкую систему управления контентом для создания различных типов сайтов. Использование программы регулируется настоящими Правилами и требует соблюдения установленных ограничений.</p>\r\n<h3><strong>2. Ограничения использования</strong></h3>\r\n<ul>\r\n<li>\r\n<p>Использование Tender CMS <strong>в коммерческих целях без лицензионного ключа запрещено</strong>.</p>\r\n</li>\r\n<li>\r\n<p>Бесплатно доступна <strong>только демо-версия</strong> с ограниченным функционалом.</p>\r\n</li>\r\n<li>\r\n<p>Полный доступ к возможностям системы предоставляется только при наличии <strong>действующего лицензионного ключа</strong>.</p>\r\n</li>\r\n<li>\r\n<p>Пользователь <strong>не имеет права</strong> изменять, модифицировать, распространять или перепродавать Tender CMS без официального разрешения.</p>\r\n</li>\r\n</ul>\r\n<h3><strong>3. Лицензионный ключ</strong></h3>\r\n<ul>\r\n<li>\r\n<p>Для использования полной версии Tender CMS требуется приобретение <strong>лицензионного ключа</strong>.</p>\r\n</li>\r\n<li>\r\n<p>Лицензионный ключ является уникальным и не подлежит передаче третьим лицам.</p>\r\n</li>\r\n<li>\r\n<p>Нарушение условий лицензирования может привести к <strong>блокировке</strong> программного обеспечения.</p>\r\n</li>\r\n</ul>\r\n<h3><strong>4. Обновления и поддержка</strong></h3>\r\n<ul>\r\n<li>\r\n<p>Владельцы лицензий получают <strong>доступ к обновлениям</strong> и технической поддержке.</p>\r\n</li>\r\n<li>\r\n<p>Демонстрационная версия <strong>не поддерживает обновления</strong> и техническую поддержку.</p>\r\n</li>\r\n</ul>\r\n<h3><strong>5. Ответственность</strong></h3>\r\n<ul>\r\n<li>\r\n<p>Разработчики Tender CMS не несут ответственности за любые <strong>убытки или потери данных</strong>, вызванные использованием программы.</p>\r\n</li>\r\n<li>\r\n<p>Пользователь соглашается использовать систему <strong>на свой страх и риск</strong>.</p>\r\n</li>\r\n</ul>\r\n<h3><strong>6. Изменения в правилах</strong></h3>\r\n<p>Tender CMS оставляет за собой право <strong>изменять и дополнять</strong> настоящие правила. Обновленная версия будет публиковаться на официальном сайте.</p>\r\n<hr>\r\n<p>Если у вас есть вопросы или требуется поддержка, свяжитесь с нами: <strong><a href=\"mailto:support@tendercms.com\">support@tendercms.com</a></strong>.</p>\r\n<p><strong>Tender CMS &ndash; надежное решение для вашего бизнеса!</strong></p>', '2025-04-01 07:33:38', 'terms-of-use', 1, NULL, 1),
(6, 'Какой сайт можно сделать на Tender CMS', '<p class=\"\" data-start=\"0\" data-end=\"352\"><strong data-start=\"0\" data-end=\"25\">Pro Website Management CMS Universale</strong> &mdash; это мощная и гибкая система управления сайтом, которая позволяет создавать и администрировать веб-ресурсы любого масштаба и сложности. Независимо от того, нужен ли вам небольшой сайт компании или крупный маркетплейс, Pro Website Management<strong data-start=\"245\" data-end=\"270\">&nbsp;CMS Universale</strong> обеспечит удобство управления, безопасность и широкие возможности для развития.</p>\r\n<p class=\"\" data-start=\"354\" data-end=\"406\">С помощью Pro Website Management<strong data-start=\"364\" data-end=\"389\">&nbsp;CMS Universale</strong> можно создать:</p>\r\n<ul data-start=\"407\" data-end=\"1155\">\r\n<li class=\"\" data-start=\"407\" data-end=\"512\">\r\n<p class=\"\" data-start=\"409\" data-end=\"512\"><strong data-start=\"409\" data-end=\"426\">Сайт компании</strong> &ndash; представьте свой бизнес в интернете с удобным интерфейсом и современным дизайном.</p>\r\n</li>\r\n<li class=\"\" data-start=\"513\" data-end=\"611\">\r\n<p class=\"\" data-start=\"515\" data-end=\"611\"><strong data-start=\"515\" data-end=\"529\">Сайт услуг</strong> &ndash; предлагайте услуги клиентам, публикуйте цены, отзывы и контактную информацию.</p>\r\n</li>\r\n<li class=\"\" data-start=\"612\" data-end=\"721\">\r\n<p class=\"\" data-start=\"614\" data-end=\"721\"><strong data-start=\"614\" data-end=\"631\">Фриланс-биржу</strong> &ndash; создайте платформу для заказчиков и исполнителей с удобной системой поиска и заказов.</p>\r\n</li>\r\n<li class=\"\" data-start=\"722\" data-end=\"812\">\r\n<p class=\"\" data-start=\"724\" data-end=\"812\"><strong data-start=\"724\" data-end=\"746\">Корпоративный сайт</strong> &ndash; управляйте внутренними и внешними процессами компании онлайн.</p>\r\n</li>\r\n<li class=\"\" data-start=\"813\" data-end=\"902\">\r\n<p class=\"\" data-start=\"815\" data-end=\"902\"><strong data-start=\"815\" data-end=\"835\">Доску объявлений</strong> &ndash; позвольте пользователям легко размещать и находить объявления.</p>\r\n</li>\r\n<li class=\"\" data-start=\"903\" data-end=\"983\">\r\n<p class=\"\" data-start=\"905\" data-end=\"983\"><strong data-start=\"905\" data-end=\"916\">Аукцион</strong> &ndash; организуйте торги с автоматическими ставками и защитой сделок.</p>\r\n</li>\r\n<li class=\"\" data-start=\"984\" data-end=\"1060\">\r\n<p class=\"\" data-start=\"986\" data-end=\"1060\"><strong data-start=\"986\" data-end=\"1001\">Маркетплейс</strong> &ndash; объедините продавцов и покупателей на одной платформе.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1061\" data-end=\"1155\">\r\n<p class=\"\" data-start=\"1063\" data-end=\"1155\"><strong data-start=\"1063\" data-end=\"1083\">Интернет-магазин</strong> &ndash; продавайте товары с удобной системой управления заказами и оплатой.</p>\r\n</li>\r\n</ul>\r\n<p class=\"\" data-start=\"1157\" data-end=\"1424\"><strong data-start=\"1157\" data-end=\"1182\">Pro Website Management CMS Universale</strong> &mdash; это универсальное решение, которое подойдет как для малого бизнеса, так и для крупных корпораций. Интуитивно понятный интерфейс, гибкость в настройке и высокая производительность делают эту систему идеальным выбором для любых веб-проектов.</p>', '2025-04-01 23:11:08', 'spravka', 0, NULL, 1),
(8, 'Справка описание шаблона', '<div>\r\n<p>только я хочу чтобы в файлах php был полный путь к шаблону а пользователь видел короткий путь.</p>\r\n<p>вот как я хочу: вот все мои файлы я буду по очереди кидать: мы сделаем по другому сделаем файл ..htaccess&nbsp;</p>\r\n<p>templates/default/css/style.css - стили<br>templates/default/css/style.php - стили контроля из админ панели<br>/add_review - добавить отзыв на странице пользователя<br>/add_tender - добавить тендер<br>/cart - корзина товаров<br>templates/default/carusel.php - карусель (отображается на главной)<br>/category - категории тендеров<br>/feedback - обратная связь&nbsp;<br>templates/default/footer.php - подвал сайта<br>templates/default/header.php - шапка сайта<br>/ - главная страница сайта - /<br>/login - страница входа<br>/logout - выход<br>templates/default/meta.php - блок на главной<br>/news - новости<br>/news_category - категории новостей и /news_cat/idномер<br>/news_full/idномер - полная новость<br>/page - сервисные страницы справка - єто страница которая выводит она может называтся как хочешь через админку<br>/profile - страница профиля<br>/register - регистрация пользователя<br>/shop - страница магазина<br>/shop_category/ - страница категорий магазина и /shop_category/idномер<br>/shop_product/ - страница товара<br>/slider.php - слайдер<br>/tenders - страница тендеров<br>templates/default/tenders_full.php - полная &nbsp;страница тендера</p>\r\n<p>Начнем с header.php</p>\r\n<p>&nbsp;</p>\r\n<br>********************************************</div>\r\n<p><br>templates/default/css/style.css - стили<br>templates/default/css/style.php - стили контроля из админ панели<br>templates/default/add_review.php - добавить отзыв на странице пользователя<br>templates/default/add_tender.php - добавить тендер<br>templates/default/cart.php - корзина товаров<br>templates/default/carusel.php - карусель (отображается на главной)<br>templates/default/category.php - категории тендеров<br>templates/default/feedback.php - обратная связь&nbsp;<br>templates/default/footer.php - подвал сайта<br>templates/default/header.php - шапка сайта<br>templates/default/index.php - главная страница сайта<br>templates/default/login.php - страница входа<br>templates/default/logout.php - выход<br>templates/default/meta.php - блок на главной<br>templates/default/news.php - новости<br>templates/default/news_category.php - категории новостей<br>templates/default/news_full.php - полная новость<br>templates/default/page.php - сервисные страницы справка<br>templates/default/profile.php - страница профиля<br>templates/default/register.php - регистрация пользователя<br>templates/default/shop.php - страница магазина<br>templates/default/shop_category.php - страница категорий магазина<br>templates/default/shop_product.php - страница товара<br>templates/default/slider.php - слайдер<br>templates/default/tenders.php - страница тендеров<br>templates/default/tenders_full.php - полная &nbsp;страница тендера<br><br>У структурі є два файли стилів:&nbsp;<span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">style.css</span> (статичні стилі) та <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">style.php</span> (динамічні стилі з адмінки). Якщо <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">style.css</span> не застосовується, це може бути пов&rsquo;язано з неправильним шляхом у <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">header.php</span> або з переписуванням URL через <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">.htaccess</span>.<br><br>Конфлікт між <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">style.css</span> і <span class=\"text-sm px-1 rounded-sm !font-mono bg-sunset/10 text-rust dark:bg-dawn/10 dark:text-dawn\">style.php</span> можливий, якщо вони підключаються одночасно з однаковими селекторами, але з різними пріоритетами.<br><br></p>\r\n<p>RewriteEngine On<br>RewriteBase /</p>\r\n<p>&lt;IfModule mod_rewrite.c&gt;<br>&nbsp; &nbsp; RewriteEngine On<br>&nbsp; &nbsp; RewriteBase /</p>\r\n<p>&nbsp; &nbsp; # Исключение для админ-панели<br>&nbsp; &nbsp; RewriteRule ^admin(/|$) - [L]</p>\r\n<p>&nbsp; &nbsp; # Исключение для search_products.php<br>&nbsp; &nbsp; RewriteRule ^templates/default/search_products.php$ - [L]</p>\r\n<p>&nbsp; &nbsp; # Переписывание для главной страницы магазина<br>&nbsp; &nbsp; RewriteRule ^shop/?$ /templates/default/shop.php [L,QSA]</p>\r\n<p>&nbsp; &nbsp; # Переписывание для корзины<br>&nbsp; &nbsp; RewriteRule ^cart?$ /templates/default/cart.php [L,QSA]</p>\r\n<p>&nbsp; &nbsp; # Переписывание для страниц товаров<br>&nbsp; &nbsp; RewriteRule ^shop/([a-z0-9-]+)$ /templates/default/shop_product.php?url=$1 [L,QSA]</p>\r\n<p>&nbsp; &nbsp; # Переписывание для страницы оформления заказа<br>&nbsp; &nbsp; RewriteRule ^checkout/([0-9]+)$ /templates/default/shop_checkout.php?product_id=$1 [L,QSA]</p>\r\n<p>&nbsp; &nbsp; # Новое правило для shop_success<br>&nbsp; &nbsp; RewriteRule ^shop_success$ /templates/default/shop_success.php [L]</p>\r\n<p>&nbsp; &nbsp; # Новое правило для payment_process<br>&nbsp; &nbsp; RewriteRule ^payment_process$ /templates/default/payment_process.php [L]</p>\r\n<p>&nbsp; &nbsp; # Редирект с /order_success на /shop_success<br>&nbsp; &nbsp; RewriteRule ^order_success$ /shop_success [R=301,L]</p>\r\n<p>&nbsp; &nbsp; # Главная страница<br>&nbsp; &nbsp; RewriteRule ^$ /templates/default/index.php [L]</p>\r\n<p>&nbsp; &nbsp; # Статические страницы (все файлы в /templates/default/)<br>&nbsp; &nbsp; RewriteRule ^add_review$ /templates/default/add_review.php [L]<br>&nbsp; &nbsp; RewriteRule ^add_tender$ /templates/default/add_tender.php [L]<br>&nbsp; &nbsp; RewriteRule ^category$ /templates/default/category.php [L]<br>&nbsp; &nbsp; RewriteRule ^category\\?id=([0-9]+)$ /templates/default/category.php?id=$1 [L,QSA]<br>&nbsp; &nbsp; RewriteRule ^feedback$ /templates/default/feedback.php [L]<br>&nbsp; &nbsp; RewriteRule ^login$ /templates/default/login.php [L]<br>&nbsp; &nbsp; RewriteRule ^logout$ /templates/default/logout.php [L]<br>&nbsp; &nbsp; RewriteRule ^news$ /templates/default/news.php [L]<br>&nbsp; &nbsp; RewriteRule ^news\\?category_id=([0-9]+)$ /templates/default/news.php?category_id=$1 [L,QSA]<br>&nbsp; &nbsp; RewriteRule ^news_category$ /templates/default/news_category.php [L]<br>&nbsp; &nbsp; RewriteRule ^news_cat/([0-9]+)$ /templates/default/news_category.php?id=$1 [L]<br>&nbsp; &nbsp; RewriteRule ^news/([a-zA-Z0-9_-]+)$ /templates/default/news_full.php?custom_url=$1 [L,QSA]<br>&nbsp; &nbsp; RewriteRule ^profile$ /templates/default/profile.php [L]<br>&nbsp; &nbsp; RewriteRule ^register$ /templates/default/register.php [L]<br>&nbsp; &nbsp; RewriteRule ^shop_category$ /templates/default/shop_category.php [L]<br>&nbsp; &nbsp; RewriteRule ^shop_category/([0-9]+)$ /templates/default/shop_category.php?id=$1 [L]<br>&nbsp; &nbsp; RewriteRule ^shop_product/([0-9]+)$ /templates/default/shop_product.php?id=$1 [L]<br>&nbsp; &nbsp; RewriteRule ^tenders$ /templates/default/tenders.php [L]<br>&nbsp; &nbsp; RewriteRule ^tenders\\?city_id=([0-9]+)$ /templates/default/tenders.php?city_id=$1 [L,QSA]<br>&nbsp; &nbsp; RewriteRule ^tenders_full/([0-9]+)$ /templates/default/tenders_full.php?id=$1 [L]</p>\r\n<p>&nbsp; &nbsp; # Доступ к файлам в /templates/default/ напрямую (CSS, JS и т.д.)<br>&nbsp; &nbsp; RewriteRule ^templates/default/(.*)$ /templates/default/$1 [L]</p>\r\n<p>&nbsp; &nbsp; # Удаляем старое правило для name<br>&nbsp; &nbsp; # RewriteRule ^name(.+)$ /templates/default/page.php?name=$1 [L,QSA]</p>\r\n<p>&nbsp; &nbsp; # Обработка динамических страниц (все остальные запросы перенаправляем на page.php)<br>&nbsp; &nbsp; RewriteCond %{REQUEST_FILENAME} !-f<br>&nbsp; &nbsp; RewriteCond %{REQUEST_FILENAME} !-d<br>&nbsp; &nbsp; RewriteRule ^(.+)$ /templates/default/page.php [L,QSA]</p>\r\n<p>&nbsp; &nbsp; # Обработка 404<br>&nbsp; &nbsp; ErrorDocument 404 /templates/default/404.php<br>&lt;/IfModule&gt;</p>\r\n<p># Указываем файл по умолчанию для корневого URL<br>DirectoryIndex /templates/default/index.php</p>', '2025-04-02 20:22:23', 'templates', 1, NULL, 1),
(10, 'Описание для системы кэширования файлов в Tender CMS', '<p class=\"\" data-start=\"63\" data-end=\"489\">В Pro Website Management реализована система&nbsp;<strong data-start=\"100\" data-end=\"122\">кэширования файлов</strong>, которая позволяет значительно ускорить работу сайта, улучшив производительность за счет кеширования различных ресурсов, таких как HTML-страницы, изображения, стили, скрипты и другие файлы шаблона. Все настройки кэширования управляются через административную панель в разделе <code data-start=\"399\" data-end=\"425\">/admin/modules/cache.php</code>, где можно гибко настроить кэширование для разных типов данных.</p>\r\n<p class=\"\" data-start=\"491\" data-end=\"536\"><strong data-start=\"491\" data-end=\"536\">Основные возможности системы кэширования:</strong></p>\r\n<ul data-start=\"537\" data-end=\"847\">\r\n<li class=\"\" data-start=\"537\" data-end=\"625\">\r\n<p class=\"\" data-start=\"539\" data-end=\"625\">Управление кэшированием различных типов контента через удобный интерфейс админ-панели.</p>\r\n</li>\r\n<li class=\"\" data-start=\"626\" data-end=\"707\">\r\n<p class=\"\" data-start=\"628\" data-end=\"707\">Поддержка различных форматов кэша для шаблонов, изображений, скриптов и стилей.</p>\r\n</li>\r\n<li class=\"\" data-start=\"708\" data-end=\"847\">\r\n<p class=\"\" data-start=\"710\" data-end=\"847\">Настройки кэширования хранятся в файле <strong data-start=\"749\" data-end=\"776\">/uploads/site_cache.php</strong>, что позволяет централизованно и легко изменять параметры кэширования.</p>\r\n</li>\r\n</ul>\r\n<p class=\"\" data-start=\"849\" data-end=\"873\"><strong data-start=\"849\" data-end=\"873\">Процесс кэширования:</strong></p>\r\n<ol data-start=\"874\" data-end=\"1505\">\r\n<li class=\"\" data-start=\"874\" data-end=\"1046\">\r\n<p class=\"\" data-start=\"877\" data-end=\"1046\"><strong data-start=\"877\" data-end=\"896\">HTML и шаблоны:</strong> Все динамические страницы сайта, такие как главная страница, страницы категорий, новости и другие, могут быть закэшированы для ускорения их загрузки.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1047\" data-end=\"1213\">\r\n<p class=\"\" data-start=\"1050\" data-end=\"1213\"><strong data-start=\"1050\" data-end=\"1066\">Изображения:</strong> Кэширование изображений (например, логотипов, изображений товаров, тендеров и других) позволяет избежать повторных загрузок одних и тех же файлов.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1214\" data-end=\"1345\">\r\n<p class=\"\" data-start=\"1217\" data-end=\"1345\"><strong data-start=\"1217\" data-end=\"1238\">CSS и JavaScript:</strong> Файлы стилей и скриптов можно кэшировать для ускорения рендеринга страниц и уменьшения нагрузки на сервер.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1346\" data-end=\"1505\">\r\n<p class=\"\" data-start=\"1349\" data-end=\"1505\"><strong data-start=\"1349\" data-end=\"1378\">Документы и прочие файлы:</strong> Поддержка кэширования для различных документов и файлов (например, PDF или Word), что позволяет ускорить доступ к этим данным.</p>\r\n</li>\r\n</ol>\r\n<p class=\"\" data-start=\"1507\" data-end=\"1545\"><strong data-start=\"1507\" data-end=\"1545\">Файлы, которые будут кэшироваться:</strong></p>\r\n<ul data-start=\"1546\" data-end=\"2159\">\r\n<li class=\"\" data-start=\"1546\" data-end=\"1712\">\r\n<p class=\"\" data-start=\"1548\" data-end=\"1712\"><strong data-start=\"1548\" data-end=\"1578\">Шаблоны сайта (HTML-файлы)</strong> &mdash; страницы, которые генерируются динамически, включая главную страницу, страницы категорий, новости, страницы товаров, тендеров и др.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1713\" data-end=\"1829\">\r\n<p class=\"\" data-start=\"1715\" data-end=\"1829\"><strong data-start=\"1715\" data-end=\"1730\">Изображения</strong> &mdash; все изображения, загружаемые на сайт, например, логотипы, фотографии товаров, тендеров и другие.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1830\" data-end=\"1956\">\r\n<p class=\"\" data-start=\"1832\" data-end=\"1956\"><strong data-start=\"1832\" data-end=\"1845\">CSS-файлы</strong> &mdash; стили для фронтенда, включая основной файл <strong data-start=\"1891\" data-end=\"1904\">style.css</strong> и дополнительные стили для админки в <strong data-start=\"1942\" data-end=\"1955\">style.php</strong>.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1957\" data-end=\"2039\">\r\n<p class=\"\" data-start=\"1959\" data-end=\"2039\"><strong data-start=\"1959\" data-end=\"1973\">JavaScript</strong> &mdash; скрипты, обеспечивающие функциональность на клиентской стороне.</p>\r\n</li>\r\n<li class=\"\" data-start=\"2040\" data-end=\"2159\">\r\n<p class=\"\" data-start=\"2042\" data-end=\"2159\"><strong data-start=\"2042\" data-end=\"2070\">Документы и прочие файлы</strong> &mdash; кэширование для файлов, таких как PDF, DOCX и изображения, загружаемые пользователями.</p>\r\n</li>\r\n</ul>\r\n<h3 class=\"\" data-start=\"2161\" data-end=\"2207\">Необходимые файлы для полного кэширования:</h3>\r\n<ol data-start=\"2209\" data-end=\"3705\">\r\n<li class=\"\" data-start=\"2209\" data-end=\"2366\">\r\n<p class=\"\" data-start=\"2212\" data-end=\"2224\"><strong data-start=\"2212\" data-end=\"2224\">Функции:</strong></p>\r\n<ul data-start=\"2228\" data-end=\"2366\">\r\n<li class=\"\" data-start=\"2228\" data-end=\"2363\">\r\n<p class=\"\" data-start=\"2230\" data-end=\"2363\"><strong data-start=\"2230\" data-end=\"2256\">includes/functions.php</strong> &mdash; функции для работы с настройками сайта, загрузкой файлов, генерацией URL, а также функциями кэширования.</p>\r\n</li>\r\n</ul>\r\n</li>\r\n<li class=\"\" data-start=\"2367\" data-end=\"2856\">\r\n<p class=\"\" data-start=\"2370\" data-end=\"2393\"><strong data-start=\"2370\" data-end=\"2393\">Шаблоны и страницы:</strong></p>\r\n<ul data-start=\"2397\" data-end=\"2856\">\r\n<li class=\"\" data-start=\"2397\" data-end=\"2856\">\r\n<p class=\"\" data-start=\"2399\" data-end=\"2425\">Все HTML-шаблоны, включая:</p>\r\n<ul data-start=\"2431\" data-end=\"2856\">\r\n<li class=\"\" data-start=\"2431\" data-end=\"2484\">\r\n<p class=\"\" data-start=\"2433\" data-end=\"2484\"><strong data-start=\"2433\" data-end=\"2468\">templates/default/css/style.css</strong> &mdash; основной CSS.</p>\r\n</li>\r\n<li class=\"\" data-start=\"2490\" data-end=\"2595\">\r\n<p class=\"\" data-start=\"2492\" data-end=\"2595\"><strong data-start=\"2492\" data-end=\"2527\">templates/default/css/style.php</strong> &mdash; динамические стили, которые могут зависеть от настроек в админке.</p>\r\n</li>\r\n<li class=\"\" data-start=\"2601\" data-end=\"2650\">\r\n<p class=\"\" data-start=\"2603\" data-end=\"2650\"><strong data-start=\"2603\" data-end=\"2635\">templates/default/header.php</strong> &mdash; шапка сайта.</p>\r\n</li>\r\n<li class=\"\" data-start=\"2656\" data-end=\"2706\">\r\n<p class=\"\" data-start=\"2658\" data-end=\"2706\"><strong data-start=\"2658\" data-end=\"2690\">templates/default/footer.php</strong> &mdash; подвал сайта.</p>\r\n</li>\r\n<li class=\"\" data-start=\"2712\" data-end=\"2779\">\r\n<p class=\"\" data-start=\"2714\" data-end=\"2779\"><strong data-start=\"2714\" data-end=\"2744\">templates/default/meta.php</strong> &mdash; мета-блоки для главной страницы.</p>\r\n</li>\r\n<li class=\"\" data-start=\"2785\" data-end=\"2856\">\r\n<p class=\"\" data-start=\"2787\" data-end=\"2856\">Страницы для тендеров, новостей, магазина, сервиса и других разделов.</p>\r\n</li>\r\n</ul>\r\n</li>\r\n</ul>\r\n</li>\r\n<li class=\"\" data-start=\"2858\" data-end=\"3252\">\r\n<p class=\"\" data-start=\"2861\" data-end=\"2885\"><strong data-start=\"2861\" data-end=\"2885\">Изображения и файлы:</strong></p>\r\n<ul data-start=\"2889\" data-end=\"3252\">\r\n<li class=\"\" data-start=\"2889\" data-end=\"3252\">\r\n<p class=\"\" data-start=\"2891\" data-end=\"2953\">Все изображения, загружаемые пользователями и администратором:</p>\r\n<ul data-start=\"2959\" data-end=\"3252\">\r\n<li class=\"\" data-start=\"2959\" data-end=\"3000\">\r\n<p class=\"\" data-start=\"2961\" data-end=\"3000\">Изображения товаров: <strong data-start=\"2982\" data-end=\"2999\">/uploads/shop</strong>.</p>\r\n</li>\r\n<li class=\"\" data-start=\"3006\" data-end=\"3051\">\r\n<p class=\"\" data-start=\"3008\" data-end=\"3051\">Изображения тендеров: <strong data-start=\"3030\" data-end=\"3050\">/uploads/tenders</strong>.</p>\r\n</li>\r\n<li class=\"\" data-start=\"3057\" data-end=\"3105\">\r\n<p class=\"\" data-start=\"3059\" data-end=\"3105\">Изображения пользователей: <strong data-start=\"3086\" data-end=\"3104\">/uploads/users</strong>.</p>\r\n</li>\r\n<li class=\"\" data-start=\"3111\" data-end=\"3153\">\r\n<p class=\"\" data-start=\"3113\" data-end=\"3153\">Изображения новостей: <strong data-start=\"3135\" data-end=\"3152\">/uploads/news</strong>.</p>\r\n</li>\r\n<li class=\"\" data-start=\"3159\" data-end=\"3207\">\r\n<p class=\"\" data-start=\"3161\" data-end=\"3207\">Изображения для галереи: <strong data-start=\"3186\" data-end=\"3206\">/uploads/gallery</strong>.</p>\r\n</li>\r\n<li class=\"\" data-start=\"3213\" data-end=\"3249\">\r\n<p class=\"\" data-start=\"3215\" data-end=\"3249\">Документы: <strong data-start=\"3226\" data-end=\"3248\">/uploads/documents</strong>.</p>\r\n</li>\r\n</ul>\r\n</li>\r\n</ul>\r\n</li>\r\n<li class=\"\" data-start=\"3253\" data-end=\"3503\">\r\n<p class=\"\" data-start=\"3256\" data-end=\"3283\"><strong data-start=\"3256\" data-end=\"3283\">Конфигурационные файлы:</strong></p>\r\n<ul data-start=\"3287\" data-end=\"3503\">\r\n<li class=\"\" data-start=\"3287\" data-end=\"3362\">\r\n<p class=\"\" data-start=\"3289\" data-end=\"3362\"><strong data-start=\"3289\" data-end=\"3316\">/uploads/site_cache.php</strong> &mdash; файл, содержащий все настройки кэширования.</p>\r\n</li>\r\n<li class=\"\" data-start=\"3366\" data-end=\"3436\">\r\n<p class=\"\" data-start=\"3368\" data-end=\"3436\"><strong data-start=\"3368\" data-end=\"3398\">/uploads/site_settings.php</strong> &mdash; файл с основными настройками сайта.</p>\r\n</li>\r\n<li class=\"\" data-start=\"3440\" data-end=\"3503\">\r\n<p class=\"\" data-start=\"3442\" data-end=\"3503\"><strong data-start=\"3442\" data-end=\"3472\">/uploads/shop_settings.php</strong> &mdash; файл с настройками магазина.</p>\r\n</li>\r\n</ul>\r\n</li>\r\n<li class=\"\" data-start=\"3505\" data-end=\"3705\">\r\n<p class=\"\" data-start=\"3508\" data-end=\"3529\"><strong data-start=\"3508\" data-end=\"3529\">Папки и загрузки:</strong></p>\r\n<ul data-start=\"3533\" data-end=\"3705\">\r\n<li class=\"\" data-start=\"3533\" data-end=\"3705\">\r\n<p class=\"\" data-start=\"3535\" data-end=\"3705\">Все папки и файлы, которые загружаются и используются в процессе работы сайта: <strong data-start=\"3614\" data-end=\"3633\">/public/uploads</strong>, <strong data-start=\"3635\" data-end=\"3653\">/uploads/users</strong>, <strong data-start=\"3655\" data-end=\"3675\">/uploads/tenders</strong>, <strong data-start=\"3677\" data-end=\"3694\">/uploads/shop</strong>, и другие.</p>\r\n</li>\r\n</ul>\r\n</li>\r\n</ol>\r\n<h3 class=\"\" data-start=\"3707\" data-end=\"3774\">Возможности администрирования через <code data-start=\"3747\" data-end=\"3773\">/admin/modules/cache.php</code>:</h3>\r\n<ul data-start=\"3775\" data-end=\"4057\">\r\n<li class=\"\" data-start=\"3775\" data-end=\"3880\">\r\n<p class=\"\" data-start=\"3777\" data-end=\"3880\">Настройка времени жизни кэша для разных типов файлов (например, для HTML-контента, изображений и т.д.).</p>\r\n</li>\r\n<li class=\"\" data-start=\"3881\" data-end=\"3960\">\r\n<p class=\"\" data-start=\"3883\" data-end=\"3960\">Управление кэшированием для различных ресурсов (стили, скрипты, изображения).</p>\r\n</li>\r\n<li class=\"\" data-start=\"3961\" data-end=\"4022\">\r\n<p class=\"\" data-start=\"3963\" data-end=\"4022\">Включение/выключение кэширования для разных типов контента.</p>\r\n</li>\r\n<li class=\"\" data-start=\"4023\" data-end=\"4057\">\r\n<p class=\"\" data-start=\"4025\" data-end=\"4057\">Очистка кэша через админ-панель.</p>\r\n</li>\r\n</ul>\r\n<h3 class=\"\" data-start=\"4059\" data-end=\"4096\">Пример использования кэширования:</h3>\r\n<ol data-start=\"4097\" data-end=\"4645\">\r\n<li class=\"\" data-start=\"4097\" data-end=\"4292\">\r\n<p class=\"\" data-start=\"4100\" data-end=\"4292\"><strong data-start=\"4100\" data-end=\"4118\">HTML-страницы:</strong> Когда пользователь посещает страницу, система проверяет, существует ли кэш для этой страницы. Если кэш есть и не истек, сайт отдает готовую страницу, ускоряя время загрузки.</p>\r\n</li>\r\n<li class=\"\" data-start=\"4293\" data-end=\"4516\">\r\n<p class=\"\" data-start=\"4296\" data-end=\"4516\"><strong data-start=\"4296\" data-end=\"4312\">Изображения:</strong> Все изображения, загруженные в систему, могут быть автоматически преобразованы в формат WebP для повышения производительности. После этого они кешируются, что ускоряет их загрузку при повторных запросах.</p>\r\n</li>\r\n<li class=\"\" data-start=\"4517\" data-end=\"4645\">\r\n<p class=\"\" data-start=\"4520\" data-end=\"4645\"><strong data-start=\"4520\" data-end=\"4552\">Статические файлы (CSS, JS):</strong> После изменения стилей или скриптов, кэш будет обновлен, чтобы отразить последние изменения.</p>\r\n</li>\r\n</ol>\r\n<p class=\"\" data-start=\"4647\" data-end=\"4819\">Таким образом, система кэширования в Pro Website Management позволяет значительно улучшить производительность сайта и предоставить пользователям более быстрый доступ к содержимому.</p>', '2025-04-03 19:47:26', 'cache', 0, NULL, 1),
(11, 'API теперь ещё больше интеграций', '<p class=\"\" data-start=\"119\" data-end=\"192\"><strong data-start=\"119\" data-end=\"192\">Pro Website Management &mdash; теперь ещё больше интеграций и возможностей!</strong></p>\r\n<p class=\"\" data-start=\"194\" data-end=\"482\">Мы продолжаем развивать <em data-start=\"218\" data-end=\"242\">Pro Website Management</em>, чтобы вы могли управлять своим сайтом максимально эффективно и удобно. В этом обновлении мы добавили ряд мощных интеграций, которые помогут вам улучшить функциональность сайта, упростить взаимодействие с клиентами и усилить защиту данных.</p>\r\n<p class=\"\" data-start=\"484\" data-end=\"499\"><strong data-start=\"484\" data-end=\"499\">Что нового:</strong></p>\r\n<ul data-start=\"501\" data-end=\"1106\">\r\n<li class=\"\" data-start=\"501\" data-end=\"589\">\r\n<p class=\"\" data-start=\"503\" data-end=\"589\"><strong data-start=\"503\" data-end=\"528\">Интеграция с Telegram</strong> &mdash; мгновенные уведомления и авторизация через Telegram Login.</p>\r\n</li>\r\n<li class=\"\" data-start=\"590\" data-end=\"684\">\r\n<p class=\"\" data-start=\"592\" data-end=\"684\"><strong data-start=\"592\" data-end=\"625\">Google Login и Facebook Login</strong> &mdash; быстрая авторизация пользователей через социальные сети.</p>\r\n</li>\r\n<li class=\"\" data-start=\"685\" data-end=\"788\">\r\n<p class=\"\" data-start=\"687\" data-end=\"788\"><strong data-start=\"687\" data-end=\"712\">Google Search Console</strong> &mdash; теперь вы можете легко отслеживать SEO-показатели и индексирование сайта.</p>\r\n</li>\r\n<li class=\"\" data-start=\"789\" data-end=\"883\">\r\n<p class=\"\" data-start=\"791\" data-end=\"883\"><strong data-start=\"791\" data-end=\"810\">Stripe и PayPal</strong> &mdash; подключение самых популярных платёжных систем для приёма онлайн-оплат.</p>\r\n</li>\r\n<li class=\"\" data-start=\"884\" data-end=\"938\">\r\n<p class=\"\" data-start=\"886\" data-end=\"938\"><strong data-start=\"886\" data-end=\"906\">Google reCAPTCHA</strong> &mdash; защита форм от спама и ботов.</p>\r\n</li>\r\n<li class=\"\" data-start=\"939\" data-end=\"1020\">\r\n<p class=\"\" data-start=\"941\" data-end=\"1020\"><strong data-start=\"941\" data-end=\"967\">Новая Почта и УкрПочта</strong> &mdash; автоматизация доставки и отслеживание отправлений.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1021\" data-end=\"1106\">\r\n<p class=\"\" data-start=\"1023\" data-end=\"1106\"><strong data-start=\"1023\" data-end=\"1034\">TinyMCE</strong> &mdash; обновлённый визуальный редактор для удобного редактирования контента.</p>\r\n</li>\r\n</ul>\r\n<p class=\"\" data-start=\"1108\" data-end=\"1235\">Мы делаем всё, чтобы ваш сайт работал стабильно, быстро и был удобен как для администраторов, так и для конечных пользователей.</p>\r\n<p class=\"\" data-start=\"1237\" data-end=\"1337\"><strong data-start=\"1237\" data-end=\"1337\">Обновите Pro Website Management уже сегодня и получите максимум возможностей от современной CMS!</strong></p>\r\n<p class=\"\" data-start=\"1339\" data-end=\"1505\">#ProWebsiteManagement #CMS #GoogleLogin #TelegramLogin #SEO #GoogleSearchConsole #PayPal #Stripe #НоваяПочта #УкрПочта #reCAPTCHA #TinyMCE #вебразработка #авторизация</p>', '2025-04-21 20:40:20', 'api', 0, NULL, 1),
(12, 'полная SEO-оптимизация для продвижения в Google', '<p class=\"\" data-start=\"98\" data-end=\"174\"><strong data-start=\"98\" data-end=\"174\">Pro Website Management &mdash; полная SEO-оптимизация для продвижения в Google</strong></p>\r\n<p class=\"\" data-start=\"176\" data-end=\"433\">Мы провели масштабное обновление <em data-start=\"209\" data-end=\"233\">Pro Website Management</em>, сделав особый акцент на <strong data-start=\"259\" data-end=\"290\">поисковую оптимизацию (SEO)</strong>. Теперь ваш сайт будет не только удобным и быстрым, но и полностью готовым к эффективному продвижению в поисковых системах, особенно в Google.</p>\r\n<p class=\"\" data-start=\"435\" data-end=\"456\"><strong data-start=\"435\" data-end=\"456\">Что нового в SEO:</strong></p>\r\n<ul data-start=\"458\" data-end=\"1167\">\r\n<li class=\"\" data-start=\"458\" data-end=\"588\">\r\n<p class=\"\" data-start=\"460\" data-end=\"588\"><strong data-start=\"460\" data-end=\"498\">Чистый и оптимизированный HTML-код</strong> &mdash; правильная структура заголовков (H1&ndash;H6), ALT-атрибуты для изображений, читаемость кода.</p>\r\n</li>\r\n<li class=\"\" data-start=\"589\" data-end=\"675\">\r\n<p class=\"\" data-start=\"591\" data-end=\"675\"><strong data-start=\"591\" data-end=\"604\">Мета-теги</strong> &mdash; удобное управление Title, Description, Keywords для каждой страницы.</p>\r\n</li>\r\n<li class=\"\" data-start=\"676\" data-end=\"786\">\r\n<p class=\"\" data-start=\"678\" data-end=\"786\"><strong data-start=\"678\" data-end=\"731\">Автоматическая генерация Sitemap.xml и robots.txt</strong> &mdash; облегчённая индексация страниц поисковыми системами.</p>\r\n</li>\r\n<li class=\"\" data-start=\"787\" data-end=\"879\">\r\n<p class=\"\" data-start=\"789\" data-end=\"879\"><strong data-start=\"789\" data-end=\"817\">Микроразметка Schema.org</strong> &mdash; улучшает внешний вид сайта в выдаче Google (rich snippets).</p>\r\n</li>\r\n<li class=\"\" data-start=\"880\" data-end=\"972\">\r\n<p class=\"\" data-start=\"882\" data-end=\"972\"><strong data-start=\"882\" data-end=\"920\">Интеграция с Google Search Console</strong> &mdash; быстрый доступ к аналитике и контроль индексации.</p>\r\n</li>\r\n<li class=\"\" data-start=\"973\" data-end=\"1076\">\r\n<p class=\"\" data-start=\"975\" data-end=\"1076\"><strong data-start=\"975\" data-end=\"1012\">Высокая скорость загрузки страниц</strong> &mdash; оптимизация кода, кеширование, адаптация под Core Web Vitals.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1077\" data-end=\"1167\">\r\n<p class=\"\" data-start=\"1079\" data-end=\"1167\"><strong data-start=\"1079\" data-end=\"1109\">Полная мобильная адаптация</strong> &mdash; соответствует требованиям Google Mobile-First Indexing.</p>\r\n</li>\r\n</ul>\r\n<p class=\"\" data-start=\"1169\" data-end=\"1396\"><strong data-start=\"1169\" data-end=\"1190\">Почему это важно?</strong><br data-start=\"1190\" data-end=\"1193\">SEO &mdash; это залог стабильного и бесплатного трафика на ваш сайт. Благодаря обновлённой CMS <em data-start=\"1282\" data-end=\"1306\">Pro Website Management</em>, вы получаете не просто систему управления сайтом, а мощную платформу для роста в поиске.</p>\r\n<p class=\"\" data-start=\"1398\" data-end=\"1482\"><strong data-start=\"1398\" data-end=\"1482\">Готовы к продвижению? Обновляйте Pro Website Management и выходите в ТОП Google!</strong></p>\r\n<p class=\"\" data-start=\"1484\" data-end=\"1624\">#SEO #ProWebsiteManagement #GoogleSEO #поисковаяоптимизация #CMS #SearchConsole #Sitemap #robots #микроразметка #раскруткасайта #продвижение</p>', '2025-04-21 20:48:58', 'seo', 0, NULL, 1),
(13, 'Более 100 бесплатных модулей', '<p class=\"\" data-start=\"134\" data-end=\"238\"><strong data-start=\"134\" data-end=\"238\">Более 100 бесплатных модулей в Pro Website Management &mdash; и каждую неделю становятся доступными новые!</strong></p>\r\n<p class=\"\" data-start=\"240\" data-end=\"383\">Система <em data-start=\"248\" data-end=\"272\">Pro Website Management</em> &mdash; это не просто CMS, а мощная модульная платформа, которая позволяет легко адаптировать сайт под любые задачи.</p>\r\n<p class=\"\" data-start=\"385\" data-end=\"402\"><strong data-start=\"385\" data-end=\"402\">Что вас ждёт:</strong></p>\r\n<ul data-start=\"404\" data-end=\"974\">\r\n<li class=\"\" data-start=\"404\" data-end=\"553\">\r\n<p class=\"\" data-start=\"406\" data-end=\"553\"><strong data-start=\"406\" data-end=\"446\">Более 100 готовых бесплатных модулей</strong> &mdash; для интернет-магазинов, блогов, форм, авторизации, доставки, платежей, SEO, аналитики и многого другого.</p>\r\n</li>\r\n<li class=\"\" data-start=\"554\" data-end=\"669\">\r\n<p class=\"\" data-start=\"556\" data-end=\"669\"><strong data-start=\"556\" data-end=\"599\">Каждую неделю мы добавляем новые модули</strong> &mdash; вы всегда будете иметь доступ к свежим возможностям и инструментам.</p>\r\n</li>\r\n<li class=\"\" data-start=\"670\" data-end=\"770\">\r\n<p class=\"\" data-start=\"672\" data-end=\"770\"><strong data-start=\"672\" data-end=\"705\">Установка модулей в один клик</strong> &mdash; не нужно быть программистом, чтобы расширить функционал сайта.</p>\r\n</li>\r\n<li class=\"\" data-start=\"771\" data-end=\"974\">\r\n<p class=\"\" data-start=\"773\" data-end=\"814\"><strong data-start=\"773\" data-end=\"812\">Популярные интеграции уже доступны:</strong></p>\r\n<ul data-start=\"817\" data-end=\"974\">\r\n<li class=\"\" data-start=\"817\" data-end=\"854\">\r\n<p class=\"\" data-start=\"819\" data-end=\"854\">Telegram, Google и Facebook Login</p>\r\n</li>\r\n<li class=\"\" data-start=\"857\" data-end=\"876\">\r\n<p class=\"\" data-start=\"859\" data-end=\"876\">Stripe и PayPal</p>\r\n</li>\r\n<li class=\"\" data-start=\"879\" data-end=\"905\">\r\n<p class=\"\" data-start=\"881\" data-end=\"905\">Новая Почта и УкрПочта</p>\r\n</li>\r\n<li class=\"\" data-start=\"908\" data-end=\"945\">\r\n<p class=\"\" data-start=\"910\" data-end=\"945\">Google reCAPTCHA и Search Console</p>\r\n</li>\r\n<li class=\"\" data-start=\"948\" data-end=\"974\">\r\n<p class=\"\" data-start=\"950\" data-end=\"974\">TinyMCE и многое другое!</p>\r\n</li>\r\n</ul>\r\n</li>\r\n</ul>\r\n<p class=\"\" data-start=\"976\" data-end=\"1110\">С <em data-start=\"978\" data-end=\"1002\">Pro Website Management</em> вы получаете не просто сайт, а гибкую экосистему, которую можно легко настраивать под любые задачи бизнеса.</p>\r\n<p class=\"\" data-start=\"1112\" data-end=\"1188\"><strong data-start=\"1112\" data-end=\"1188\">Следите за обновлениями &mdash; каждую неделю вас ждут новые модули и функции!</strong></p>', '2025-04-21 20:52:57', 'modules', 0, NULL, 1),
(14, 'Модуль интернет магазина', '<p class=\"\" data-start=\"121\" data-end=\"212\"><strong data-start=\"121\" data-end=\"212\">Pro Website Management &mdash; полный контроль интернет-магазина с удобной панелью управления</strong></p>\r\n<p class=\"\" data-start=\"214\" data-end=\"417\">В <em data-start=\"216\" data-end=\"240\">Pro Website Management</em> мы предусмотрели всё, чтобы управление интернет-магазином было простым, понятным и эффективным. У вас под рукой &mdash; вся информация и нужные инструменты, собранные в одной панели.</p>\r\n<p class=\"\" data-start=\"419\" data-end=\"458\"><strong data-start=\"419\" data-end=\"458\">Разделы панели управления магазина:</strong></p>\r\n<ul data-start=\"460\" data-end=\"1188\">\r\n<li class=\"\" data-start=\"460\" data-end=\"545\">\r\n<p class=\"\" data-start=\"462\" data-end=\"545\"><strong data-start=\"462\" data-end=\"487\">Информационная панель</strong> &mdash; обзор продаж, заказов, популярных товаров и статистики.</p>\r\n</li>\r\n<li class=\"\" data-start=\"546\" data-end=\"641\">\r\n<p class=\"\" data-start=\"548\" data-end=\"641\"><strong data-start=\"548\" data-end=\"566\">Добавить товар</strong> &mdash; быстрое создание карточки товара с описанием, ценой, фото и параметрами.</p>\r\n</li>\r\n<li class=\"\" data-start=\"642\" data-end=\"736\">\r\n<p class=\"\" data-start=\"644\" data-end=\"736\"><strong data-start=\"644\" data-end=\"658\">Все товары</strong> &mdash; список всех товаров с возможностью редактирования, фильтрации и управления.</p>\r\n</li>\r\n<li class=\"\" data-start=\"737\" data-end=\"820\">\r\n<p class=\"\" data-start=\"739\" data-end=\"820\"><strong data-start=\"739\" data-end=\"752\">Категории</strong> &mdash; удобное управление категориями для структурирования ассортимента.</p>\r\n</li>\r\n<li class=\"\" data-start=\"821\" data-end=\"913\">\r\n<p class=\"\" data-start=\"823\" data-end=\"913\"><strong data-start=\"823\" data-end=\"837\">Заказы</strong>&nbsp;&mdash; отслеживание и обработка заказов, изменение статусов и связь с клиентами.</p>\r\n</li>\r\n<li class=\"\" data-start=\"914\" data-end=\"998\">\r\n<p class=\"\" data-start=\"916\" data-end=\"998\"><strong data-start=\"916\" data-end=\"928\">Доставка</strong> &mdash; настройка способов доставки, интеграция с Новой Почтой и УкрПочтой.</p>\r\n</li>\r\n<li class=\"\" data-start=\"999\" data-end=\"1094\">\r\n<p class=\"\" data-start=\"1001\" data-end=\"1094\"><strong data-start=\"1001\" data-end=\"1023\">Настройки магазина</strong> &mdash; управление валютой, налогами, страницами оплаты и оформления заказа.</p>\r\n</li>\r\n<li class=\"\" data-start=\"1095\" data-end=\"1188\">\r\n<p class=\"\" data-start=\"1097\" data-end=\"1188\"><strong data-start=\"1097\" data-end=\"1116\">Подвал магазина</strong> &mdash; редактирование контактной информации, ссылок и полезных блоков сайта.</p>\r\n</li>\r\n</ul>\r\n<p class=\"\" data-start=\"1190\" data-end=\"1337\">Система разработана так, чтобы даже без технических знаний вы могли полностью управлять своим магазином &mdash; от добавления товара до доставки клиенту.</p>\r\n<p class=\"\" data-start=\"190\" data-end=\"373\">Ваша <strong data-start=\"289\" data-end=\"314\">информационная панель</strong> &mdash; это центр управления всеми процессами интернет-торговли.</p>\r\n<p class=\"\" data-start=\"375\" data-end=\"413\"><strong data-start=\"375\" data-end=\"413\">Что вы увидите на панели магазина:</strong></p>\r\n<ul data-start=\"415\" data-end=\"679\">\r\n<li class=\"\" data-start=\"415\" data-end=\"493\">\r\n<p class=\"\" data-start=\"417\" data-end=\"493\"><strong data-start=\"417\" data-end=\"438\">Статистика продаж</strong> &mdash; наглядные графики, общее количество заказов и доход.</p>\r\n</li>\r\n<li class=\"\" data-start=\"494\" data-end=\"679\">\r\n<p class=\"\" data-start=\"496\" data-end=\"548\"><strong data-start=\"496\" data-end=\"524\">Список последних заказов</strong> с ключевой информацией:</p>\r\n<ul data-start=\"551\" data-end=\"679\">\r\n<li class=\"\" data-start=\"551\" data-end=\"601\">\r\n<p class=\"\" data-start=\"553\" data-end=\"601\"><strong data-start=\"553\" data-end=\"570\">Статус заказа</strong> (новый, в обработке, выполнен)</p>\r\n</li>\r\n<li class=\"\" data-start=\"604\" data-end=\"634\">\r\n<p class=\"\" data-start=\"606\" data-end=\"634\"><strong data-start=\"606\" data-end=\"620\">Покупатель</strong> &mdash; имя клиента</p>\r\n</li>\r\n<li class=\"\" data-start=\"637\" data-end=\"654\">\r\n<p class=\"\" data-start=\"639\" data-end=\"654\"><strong data-start=\"639\" data-end=\"654\">Дата заказа</strong></p>\r\n</li>\r\n<li class=\"\" data-start=\"657\" data-end=\"679\">\r\n<p class=\"\" data-start=\"659\" data-end=\"679\"><strong data-start=\"659\" data-end=\"679\">Категория товара</strong></p>\r\n</li>\r\n</ul>\r\n</li>\r\n</ul>\r\n<p class=\"\" data-start=\"681\" data-end=\"711\"><strong data-start=\"681\" data-end=\"711\">Подробная таблица заказов:</strong></p>\r\n<p class=\"\" data-start=\"713\" data-end=\"791\">| № | Товар | Категория | Заказ | Покупатель | Телефон | Дата и время | Цена |</p>\r\n<p class=\"\" data-start=\"793\" data-end=\"904\">Каждая строка &mdash; это заказ, по которому можно быстро получить всю информацию и оперативно связаться с клиентом.</p>\r\n<p class=\"\" data-start=\"906\" data-end=\"928\"><strong data-start=\"906\" data-end=\"928\">Система позволяет:</strong></p>\r\n<ul data-start=\"929\" data-end=\"1075\">\r\n<li class=\"\" data-start=\"929\" data-end=\"969\">\r\n<p class=\"\" data-start=\"931\" data-end=\"969\">отслеживать заказы в реальном времени,</p>\r\n</li>\r\n<li class=\"\" data-start=\"970\" data-end=\"992\">\r\n<p class=\"\" data-start=\"972\" data-end=\"992\">управлять статусами,</p>\r\n</li>\r\n<li class=\"\" data-start=\"993\" data-end=\"1038\">\r\n<p class=\"\" data-start=\"995\" data-end=\"1038\">фильтровать по дате, категории или клиенту,</p>\r\n</li>\r\n<li class=\"\" data-start=\"1039\" data-end=\"1075\">\r\n<p class=\"\" data-start=\"1041\" data-end=\"1075\">экспортировать данные для отчётов.</p>\r\n</li>\r\n</ul>\r\n<p class=\"\" data-start=\"1077\" data-end=\"1188\">&nbsp;</p>\r\n<p class=\"\" data-start=\"1339\" data-end=\"1429\"><strong data-start=\"1339\" data-end=\"1429\">Управляйте магазином легко с Pro Website Management &mdash; всё под контролем, всё на месте.</strong></p>', '2025-04-22 00:23:52', 'shop', 0, NULL, 1);

-- --------------------------------------------------------

--
-- Структура таблиці `prices`
--

CREATE TABLE `prices` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` varchar(50) DEFAULT NULL,
  `custom_fields` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `created_at` datetime DEFAULT current_timestamp(),
  `is_approved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`image`)),
  `capacity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `status` enum('available','booked') NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `rooms`
--

INSERT INTO `rooms` (`id`, `category_id`, `name`, `image`, `capacity`, `price`, `status`) VALUES
(13, 1, 'Хостел', '[\"\\/uploads\\/booking\\/67f95db50c03f-1..webp\",\"\\/uploads\\/booking\\/67f95db52865b-2..webp\",\"\\/uploads\\/booking\\/67f95db54f7cf-3..webp\",\"\\/uploads\\/booking\\/67f95db56aec9-537538941..webp\",\"\\/uploads\\/booking\\/67f95db586d9c-krasnye-rozy..webp\"]', 6, 100, 'available'),
(14, 1, 'Квартира', '[\"\\/uploads\\/booking\\/67f95e90e7aec-shop-app6999..webp\"]', 2, 123, 'available');

-- --------------------------------------------------------

--
-- Структура таблиці `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `shop_categories`
--

CREATE TABLE `shop_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_desc` text DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_desc` text DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `twitter_title` varchar(255) DEFAULT NULL,
  `twitter_desc` text DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `shop_categories`
--

INSERT INTO `shop_categories` (`id`, `name`, `parent_id`, `status`, `sort_order`, `meta_title`, `meta_desc`, `og_title`, `og_desc`, `keywords`, `twitter_title`, `twitter_desc`, `og_image`) VALUES
(1, 'Электроника и техника', NULL, 1, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Одежда и обувь', NULL, 1, 13, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Дом и сад', NULL, 1, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Красота и здоровье', NULL, 1, 11, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Спорт и отдых', NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'Детские товары', NULL, 1, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'Продукты питания', NULL, 1, 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'Автотовары', NULL, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'Книги, фильмы и музыка', NULL, 1, 10, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(10, 'Хобби и творчество', NULL, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(11, 'Смартфоны и гаджеты', 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(12, 'Компьютеры и ноутбуки', 1, 1, 7, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 'Бытовая техника', 1, 1, 8, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 'Аудио и видео', 1, 1, 9, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 'Мужская одежда', 2, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 'Женская одежда', 2, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 'Детская одежда', 2, 1, 14, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'Обувь', 2, 1, 15, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'Аксессуары', 2, 1, 16, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'Мебель', 3, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'Текстиль', 3, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'Посуда и кухонные принадлежности', 3, 1, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 'Садовый инвентарь', 3, 1, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 'Косметика', 4, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'Парфюмерия', 4, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 'Уход за телом', 4, 1, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 'Медицинские товары', 4, 1, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 'Спортивная одежда и обувь', 5, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 'Тренажеры и оборудование', 5, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 'Туризм и кемпинг', 5, 1, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 'Велосипеды и аксессуары', 5, 1, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 'Игрушки', 6, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 'Одежда для детей', 6, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 'Коляски и автокресла', 6, 1, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 'Товары для новорожденных', 6, 1, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 'Бакалея', 7, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 'Напитки', 7, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 'Сладости и снеки', 7, 1, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 'Здоровое питание', 7, 1, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 'Запчасти', 8, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 'Аксессуары для авто', 8, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 'Шины и диски', 8, 1, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 'Инструменты для ремонта', 8, 1, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 'Книги', 9, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 'Фильмы и сериалы', 9, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 'Музыкальные инструменты', 9, 1, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 'Аудиопродукция', 9, 1, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'Рукоделие', 10, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 'Коллекционирование', 10, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 'Настольные игры', 10, 1, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 'Материалы для творчества', 10, 1, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 'Новая тест', NULL, 1, 17, 'Новая тест', 'Новая тест', 'Новая тест', 'Новая тест', 'Новая тест', 'Новая тест', 'Новая тест', NULL),
(54, 'Ролдьор', NULL, 1, 18, 'Ролдьор', 'Товары в категории Ролдьор', 'Ролдьор', 'Товары в категории Ролдьор', 'Ролдьор', 'Ролдьор', 'Товары в категории Ролдьор', NULL);

-- --------------------------------------------------------

--
-- Структура таблиці `shop_delivery_methods`
--

CREATE TABLE `shop_delivery_methods` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT 0.00,
  `is_enabled` tinyint(1) DEFAULT 1,
  `regions` text DEFAULT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `estimated_time` varchar(100) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `shop_delivery_methods`
--

INSERT INTO `shop_delivery_methods` (`id`, `name`, `description`, `cost`, `is_enabled`, `regions`, `min_order_amount`, `estimated_time`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Вильнюс +30 км.', 'Тестовая доставка для магазина', 10.00, 1, 'Вильнюс', 1.00, '0', 0, '2025-04-06 23:07:12', '2025-04-06 23:07:12'),
(3, 'Новая Почта', 'Доставка через Новую Почту (стоимость рассчитывается при оформлении)', 0.00, 1, '', 0.00, '1', 0, '2025-04-14 14:38:14', '2025-04-14 14:38:49'),
(4, 'Укр пошта', 'Доставка по україні', 100.00, 1, '', 0.00, '0', 0, '2025-04-14 18:47:05', '2025-04-14 18:47:05');

-- --------------------------------------------------------

--
-- Структура таблиці `shop_orders`
--

CREATE TABLE `shop_orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `patronymic` varchar(100) DEFAULT NULL,
  `login` varchar(100) DEFAULT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `delivery_cost` decimal(10,2) DEFAULT 0.00,
  `delivery_method` varchar(100) DEFAULT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `currency_code` varchar(3) NOT NULL DEFAULT 'UAH',
  `paypal_order_id` varchar(50) DEFAULT NULL,
  `status` enum('оплачен','ожидает','отменен') DEFAULT 'ожидает',
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `city` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `region` varchar(255) DEFAULT NULL,
  `nova_poshta_ttn` varchar(20) DEFAULT NULL,
  `nova_poshta_city` varchar(255) DEFAULT NULL,
  `nova_poshta_warehouse` varchar(255) DEFAULT NULL,
  `nova_poshta_street` varchar(255) DEFAULT NULL,
  `building_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблиці `shop_products`
--

CREATE TABLE `shop_products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `delivery_status` tinyint(1) DEFAULT 0 COMMENT '0 - с доставкой, 1 - без доставки',
  `status` varchar(50) DEFAULT 'active' COMMENT 'active, inactive, deleted',
  `customer_name` varchar(100) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `in_stock` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 - в наличии, 0 - нет в наличии',
  `short_desc` text DEFAULT NULL,
  `full_desc` text DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `custom_url` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_desc` text DEFAULT NULL,
  `og_title` varchar(255) DEFAULT NULL,
  `og_desc` text DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT 1.00,
  `volume` decimal(10,6) DEFAULT 0.001000,
  `width` decimal(10,2) DEFAULT 10.00,
  `length` decimal(10,2) DEFAULT 10.00,
  `height` decimal(10,2) DEFAULT 10.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `shop_products`
--

INSERT INTO `shop_products` (`id`, `name`, `created_at`, `delivery_status`, `status`, `customer_name`, `category_id`, `price`, `image`, `description`, `in_stock`, `short_desc`, `full_desc`, `keywords`, `custom_url`, `meta_title`, `meta_desc`, `og_title`, `og_desc`, `weight`, `volume`, `width`, `length`, `height`) VALUES
(23, 'Mercedes-Benz E 220 ', '2025-04-04 21:51:02', 0, 'active', NULL, 8, 2500.00, '[\"67f19ffc086a7-automobiliai..webp\"]', NULL, 1, 'Mercedes-Benz E 220 — это автомобиль, который идеально сочетает в себе изысканный стиль, передовые технологии и выдающуюся производительность. ', '<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;0\\&quot;\" data-end=\"\\&quot;67\\&quot;\"><strong data-start=\"\\&quot;0\\&quot;\" data-end=\"\\&quot;67\\&quot;\">Mercedes-Benz E 220: Элегантность, комфорт и производительность</strong></p>\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;69\\&quot;\" data-end=\"\\&quot;425\\&quot;\">&nbsp;Mercedes-Benz E 220 &mdash; это автомобиль, который идеально сочетает в себе изысканный стиль, передовые технологии и выдающуюся производительность. Этот представительский седан от легендарного немецкого бренда не только гарантирует высокий уровень комфорта, но и предоставляет динамичные характеристики, которые делают каждую поездку незабываемой.</p>\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;427\\&quot;\" data-end=\"\\&quot;455\\&quot;\"><strong data-start=\"\\&quot;427\\&quot;\" data-end=\"\\&quot;455\\&quot;\">Основные характеристики:</strong></p>\r\n<ul data-start=\"\\&quot;456\\&quot;\" data-end=\"\\&quot;718\\&quot;\">\r\n<li class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;456\\&quot;\" data-end=\"\\&quot;507\\&quot;\">\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;458\\&quot;\" data-end=\"\\&quot;507\\&quot;\"><strong data-start=\"\\&quot;458\\&quot;\" data-end=\"\\&quot;472\\&quot;\">Двигатель:</strong> 2,0 литра, 4 цилиндра, турбонаддув</p>\r\n</li>\r\n<li class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;508\\&quot;\" data-end=\"\\&quot;532\\&quot;\">\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;510\\&quot;\" data-end=\"\\&quot;532\\&quot;\"><strong data-start=\"\\&quot;510\\&quot;\" data-end=\"\\&quot;523\\&quot;\">Мощность:</strong> 194 л.с.</p>\r\n</li>\r\n<li class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;533\\&quot;\" data-end=\"\\&quot;596\\&quot;\">\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;535\\&quot;\" data-end=\"\\&quot;596\\&quot;\"><strong data-start=\"\\&quot;535\\&quot;\" data-end=\"\\&quot;551\\&quot;\">Трансмиссия:</strong> 9-ступенчатая автоматическая коробка передач</p>\r\n</li>\r\n<li class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;597\\&quot;\" data-end=\"\\&quot;633\\&quot;\">\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;599\\&quot;\" data-end=\"\\&quot;633\\&quot;\"><strong data-start=\"\\&quot;599\\&quot;\" data-end=\"\\&quot;621\\&quot;\">Разгон 0-100 км/ч:</strong> 7,4 секунды</p>\r\n</li>\r\n<li class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;634\\&quot;\" data-end=\"\\&quot;671\\&quot;\">\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;636\\&quot;\" data-end=\"\\&quot;671\\&quot;\"><strong data-start=\"\\&quot;636\\&quot;\" data-end=\"\\&quot;662\\&quot;\">Максимальная скорость:</strong> 240 км/ч</p>\r\n</li>\r\n<li class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;672\\&quot;\" data-end=\"\\&quot;697\\&quot;\">\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;674\\&quot;\" data-end=\"\\&quot;697\\&quot;\"><strong data-start=\"\\&quot;674\\&quot;\" data-end=\"\\&quot;690\\&quot;\">Тип топлива:</strong> Дизель</p>\r\n</li>\r\n<li class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;698\\&quot;\" data-end=\"\\&quot;718\\&quot;\">\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;700\\&quot;\" data-end=\"\\&quot;718\\&quot;\"><strong data-start=\"\\&quot;700\\&quot;\" data-end=\"\\&quot;711\\&quot;\">Привод:</strong> Задний</p>\r\n</li>\r\n</ul>\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;720\\&quot;\" data-end=\"\\&quot;1108\\&quot;\"><strong data-start=\"\\&quot;720\\&quot;\" data-end=\"\\&quot;741\\&quot;\">Дизайн и комфорт:</strong> Элегантный внешний вид E 220 подчеркивается плавными линиями кузова, стильными фарами и хромированными элементами. В салоне царит роскошь и внимание к деталям: мягкие кожаные сиденья, высококачественные отделочные материалы и просторное пространство для всех пассажиров. Водитель и пассажиры могут наслаждаться тишиной и комфортом, благодаря улучшенной шумоизоляции.</p>\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;1110\\&quot;\" data-end=\"\\&quot;1501\\&quot;\"><strong data-start=\"\\&quot;1110\\&quot;\" data-end=\"\\&quot;1140\\&quot;\">Технологии и безопасность:</strong> Mercedes-Benz E 220 оснащен множеством инновационных технологий, включая систему мультимедиа COMAND, интеллектуальные системы помощи водителю, такие как активный круиз-контроль, система удержания полосы движения и автоматическое экстренное торможение. Эти технологии не только повышают уровень безопасности, но и делают каждую поездку более удобной и приятной.</p>\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;1503\\&quot;\" data-end=\"\\&quot;1682\\&quot;\"><strong data-start=\"\\&quot;1503\\&quot;\" data-end=\"\\&quot;1521\\&quot;\">Экономичность:</strong> Несмотря на высокую мощность, E 220 отличается хорошей топливной экономичностью, что делает его идеальным выбором для долгих поездок и городского использования.</p>\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;1684\\&quot;\" data-end=\"\\&quot;1945\\&quot;\"><strong data-start=\"\\&quot;1684\\&quot;\" data-end=\"\\&quot;1693\\&quot;\">Итог:</strong> Mercedes-Benz E 220 &mdash; это не просто автомобиль, а настоящий образ жизни. Он идеально подходит тем, кто ценит комфорт, стиль и высокие технологии. Этот седан обеспечит вам не только исключительное вождение, но и непередаваемые эмоции от каждой поездки.</p>\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;1947\\&quot;\" data-end=\"\\&quot;1972\\&quot;\"><strong data-start=\"\\&quot;1947\\&quot;\" data-end=\"\\&quot;1972\\&quot;\">Ключевые особенности:</strong></p>\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;1975\\&quot;\" data-end=\"\\&quot;2003\\&quot;\">Стильный и элегантный дизайн</p>\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;2006\\&quot;\" data-end=\"\\&quot;2045\\&quot;\">Высокий уровень комфорта и безопасности</p>\r\n<ul data-start=\"\\&quot;1973\\&quot;\" data-end=\"\\&quot;2137\\&quot;\">\r\n<li class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;2046\\&quot;\" data-end=\"\\&quot;2088\\&quot;\">\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;2048\\&quot;\" data-end=\"\\&quot;2088\\&quot;\">Мощный и экономичный дизельный двигатель</p>\r\n</li>\r\n<li class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;2089\\&quot;\" data-end=\"\\&quot;2137\\&quot;\">\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;2091\\&quot;\" data-end=\"\\&quot;2137\\&quot;\">Передовые технологии и системы помощи водителю</p>\r\n</li>\r\n</ul>\r\n<p class=\"\\&quot;\\&quot;\" data-start=\"\\&quot;2139\\&quot;\" data-end=\"\\&quot;2213\\&quot;\"><strong data-start=\"\\&quot;2139\\&quot;\" data-end=\"\\&quot;2155\\&quot;\">Идеален для:</strong> людей, ценящих качество, стиль и комфорт в каждой детали.</p>', 'Ключевые слова', 'mersedes-e220', 'Mersedes e220', 'Mercedes-Benz E 220 — это автомобиль, который идеально сочетает в себе изысканный стиль, передовые технологии и выдающуюся производительность. ', 'Mersedes e220', 'Mercedes-Benz E 220 — это автомобиль, который идеально сочетает в себе изысканный стиль, передовые технологии и выдающуюся производительность. ', 1.00, 0.001000, 10.00, 10.00, 10.00),
(29, 'Название', '2025-04-06 18:10:50', 0, 'active', 'Иван Петров', 1, 599.99, '[\"67f2cc25e9fae-shop-logo-design-template-440f641c672f64c4419fef8ff82b9c9a_screen..webp\"]', 'Мощный смартфон с AMOLED-экраном и камерой 108 МП.', 1, 'Краткое описание смартфона Galaxy S23.', '<p>Полное описание: Смартфон Galaxy S23 оснащен 6.8-дюймовым экраном, процессором Exynos 2200, 8 ГБ оперативной памяти и батареей на 5000 мАч.</p>', 'смартфон, galaxy, s23, android', 'galaxy-s23', 'Смартфон Galaxy S23 - Купить', 'Мощный смартфон с отличной камерой и экраном.', 'Смартфон Galaxy S23', 'Ознакомьтесь с новым Galaxy S23!', 1.00, 0.001000, 10.00, 10.00, 10.00),
(30, 'Ноутбук Dell XPS 13', '2025-04-06 18:10:50', 1, 'active', NULL, 2, 1299.00, NULL, 'Легкий и производительный ноутбук для работы и учебы.', 1, 'Краткое описание ноутбука Dell XPS 13.', '<p>Полное описание: Смартфон Galaxy S23 оснащен 6.8-дюймовым экраном, процессором Exynos 2200, 8 ГБ оперативной памяти и батареей на 5000 мАч.</p>', 'ноутбук, dell, xps, windows', 'dell-xps-13', 'Ноутбук Dell XPS 13 - Цена', 'Компактный ноутбук с высокой производительностью.', 'Dell XPS 13', 'Легкий ноутбук для профессионалов.', 1.00, 0.001000, 10.00, 10.00, 10.00),
(31, 'Наушники Sony WH-1000XM5', '2025-04-06 18:10:50', 0, 'active', 'Анна Сидорова', 3, 349.50, NULL, 'Беспроводные наушники с шумоподавлением.', 0, 'Краткое описание наушников Sony.', '<p>Полное описание: Sony WH-1000XM5 с активным шумоподавлением, 30 часов автономной работы и качественным звуком.</p>', 'наушники, sony, беспроводные', '/product/sony-wh1000xm5', 'Sony WH-1000XM5 - Купить', 'Лучшие наушники с шумоподавлением.', 'Sony WH-1000XM5', 'Наушники премиум-класса.', 1.00, 0.001000, 10.00, 10.00, 10.00),
(32, 'Кофеварка Philips', '2025-04-06 18:10:50', 1, 'inactive', NULL, 4, 89.99, '/images/philips_coffee.jpg', 'Компактная кофеварка для дома.', 1, 'Краткое описание кофеварки Philips.', 'Полное описание: Кофеварка Philips с мощностью 1000 Вт, объемом 1.2 л и функцией автоотключения.', 'кофеварка, philips, кухня', '/product/philips-coffee', 'Кофеварка Philips - Цена', 'Приготовьте вкусный кофе дома.', 'Philips Coffee', 'Кофеварка для утреннего кофе.', 1.00, 0.001000, 10.00, 10.00, 10.00),
(33, 'Фитнес-браслет Xiaomi Mi Band 8', '2025-04-06 18:10:50', 0, 'active', 'Олег Иванов', 5, 49.99, '/images/mi_band8.jpg', 'Умный браслет для отслеживания активности.', 1, 'Краткое описание Mi Band 8.', 'Полное описание: Xiaomi Mi Band 8 с AMOLED-дисплеем, пульсометром и водозащитой 5ATM.', 'фитнес, xiaomi, браслет', '/product/mi-band-8', 'Xiaomi Mi Band 8 - Купить', 'Умный браслет для спорта.', 'Mi Band 8', 'Фитнес-браслет от Xiaomi.', 1.00, 0.001000, 10.00, 10.00, 10.00),
(34, 'Телевизор LG OLED55', '2025-04-06 18:10:50', 0, 'active', NULL, 6, 1499.00, '/images/lg_oled55.jpg', 'Телевизор с OLED-экраном 55 дюймов.', 1, 'Краткое описание телевизора LG.', 'Полное описание: LG OLED55 с разрешением 4K, поддержкой HDR и Smart TV.', 'телевизор, lg, oled', '/product/lg-oled55', 'LG OLED55 - Цена', 'Телевизор с потрясающим качеством изображения.', 'LG OLED55', 'Телевизор премиум-класса.', 1.00, 0.001000, 10.00, 10.00, 10.00),
(35, 'Электросамокат Ninebot', '2025-04-06 18:10:50', 1, 'active', 'Мария Коваленко', 7, 399.00, '/images/ninebot_scooter.jpg', 'Электросамокат с запасом хода 25 км.', 1, 'Краткое описание электросамоката Ninebot.', 'Полное описание: Ninebot с мощностью 300 Вт, скоростью до 25 км/ч и складной конструкцией.', 'самокат, ninebot, электрический', '/product/ninebot-scooter', 'Ninebot Scooter - Купить', 'Электросамокат для города.', 'Ninebot Scooter', 'Удобный транспорт для прогулок.', 1.00, 0.001000, 10.00, 10.00, 10.00),
(36, 'Dyson', '2025-04-06 18:10:50', 0, 'active', NULL, 8, 699.99, NULL, 'Беспроводной пылесос с высокой мощностью.', 1, 'Краткое описание пылесоса Dyson.', '<p>Полное описание: Sony WH-1000XM5 с активным шумоподавлением, 30 часов автономной работы и качественным звуком.</p>', 'пылесос, dyson, беспроводной', 'dyson-v15', 'Dyson', 'Мощный пылесос для дома.', 'Dyson V15', 'Беспроводной пылесос Dyson.', 1.00, 0.001000, 10.00, 10.00, 10.00),
(37, 'Игровая консоль PS5', '2025-04-06 18:10:50', 0, 'active', 'Дмитрий Соколов', 9, 499.99, '/images/ps5.jpg', 'Игровая консоль нового поколения.', 1, 'Краткое описание PS5.', 'Полное описание: PlayStation 5 с поддержкой 4K, SSD и контроллером DualSense.', 'консоль, ps5, игры', '/product/ps5', 'PlayStation 5 - Купить', 'Игровая консоль нового поколения.', 'PS5', 'Играйте с PS5!', 1.00, 0.001000, 10.00, 10.00, 10.00),
(38, 'Фотоаппарат Canon EOS R6', '2025-04-06 18:10:50', 1, 'active', NULL, 10, 1999.00, '/images/canon_eos_r6.jpg', 'Беззеркальный фотоаппарат для профессионалов.', 1, 'Краткое описание Canon EOS R6.', 'Полное описание: Canon EOS R6 с матрицей 20.1 МП, 4K-видео и стабилизацией изображения.', 'фотоаппарат, canon, беззеркальный', '/product/canon-eos-r6', 'Canon EOS R6 - Цена', 'Фотоаппарат для съемки высокого качества.', 'Canon EOS R6', 'Профессиональная камера Canon.', 1.00, 0.001000, 10.00, 10.00, 10.00),
(39, 'Название (H1, Title)', '2025-04-06 18:42:29', 0, 'active', NULL, 13, 1234.00, '[\"67f2cb137c680-logo..webp\"]', NULL, 1, 'Короткое описание (H2)', '<p>Короткое описание (H2)</p>', '', 'h1-title', 'Название (H1, Title)', 'Короткое описание (H2)', 'Название (H1, Title)', 'Короткое описание (H2)', 1.00, 0.001000, 10.00, 10.00, 10.00),
(40, 'delivery', '2025-04-06 22:13:06', 1, 'active', NULL, 8, 1.00, NULL, NULL, 1, 'delivery', '<p>delivery</p>', '', 'delivery', 'delivery', 'delivery', 'delivery', 'delivery', 1.00, 0.001000, 10.00, 10.00, 10.00),
(41, 'Тестовый товар', '2025-04-14 22:14:58', 0, 'active', NULL, 1, 100.00, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.50, 0.001000, 20.00, 20.00, 20.00);

-- --------------------------------------------------------

--
-- Структура таблиці `tenders`
--

CREATE TABLE `tenders` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `short_desc` text DEFAULT NULL,
  `full_desc` text DEFAULT NULL,
  `budget` decimal(15,2) DEFAULT NULL,
  `owner_type` enum('owner','agent','company') DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('published','moderation','closed') DEFAULT 'moderation',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `images` text DEFAULT NULL,
  `documents` text DEFAULT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `tenders`
--

INSERT INTO `tenders` (`id`, `title`, `short_desc`, `full_desc`, `budget`, `owner_type`, `start_date`, `end_date`, `category_id`, `city`, `user_id`, `status`, `created_at`, `updated_at`, `city_id`, `deadline`, `phone`, `images`, `documents`, `name`) VALUES
(29, 'Название тендера', '<p>Краткое описание<br>перенос строки<br>3я строка</p>', NULL, 12332.00, NULL, NULL, NULL, 8, NULL, 6, 'published', '2025-03-27 17:35:57', NULL, 2, NULL, '+380977001414', '[\"67e58c7d67ab9-krasnye-rozy.png\"]', '[]', 'Руслан Билогаш'),
(30, 'Нужен 2', '<p>Первый релиз Tender CMS Free будет в открытом доступе 10 Апреля 2025 года все кто успеет скачать до 31 Апреля будут пользоватся безоплатно и пожизнено.<br>Все последующие версии будут платные и с обновлениями.</p>', NULL, 1000.00, NULL, NULL, NULL, 8, NULL, 6, 'published', '2025-03-27 23:55:33', NULL, 1, NULL, '03800200000', '[\"67e5e575cb1d7-cyberscooty-trowel-clipart-lg.png\",\"67e5e575cb38f-master.png\",\"67e5e575cb492-master-house.png\",\"67e5e575cb5d0-\\u0421\\u043a\\u0440\\u0438\\u043d\\u0448\\u043e\\u0442 22-03-2025 181253.jpg\"]', '[]', 'Білогаш Руслан Тестович'),
(32, 'Тестовый тендер', '<p>Краткое описание Краткое описание Краткое описание<br>Краткое описание</p>', NULL, 100.00, NULL, NULL, NULL, 2, NULL, NULL, 'published', '2025-04-02 22:37:30', NULL, 1, NULL, '+380980252557', '[\"67edbc2a38b78-\\u0421\\u043a\\u0440\\u0438\\u043d\\u0448\\u043e\\u0442 27-11-2024 212340..jpg\"]', '[]', 'Ruslan Bilohash');

-- --------------------------------------------------------

--
-- Структура таблиці `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','executor','company') NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `nickname` varchar(50) DEFAULT NULL,
  `profile_completed` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `photo` varchar(255) DEFAULT NULL,
  `about` text DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `telegram_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `users`
--

INSERT INTO `users` (`id`, `contact`, `password`, `role`, `first_name`, `last_name`, `nickname`, `profile_completed`, `created_at`, `photo`, `about`, `city_id`, `category_id`, `telegram_id`) VALUES
(1, 'rbilohash@gmail.com', '$2y$10$k9QrIS3i6yHFkoflhX7cAuQ7l6ru.hYMegLwsJpVsxL84cllTRvWu', 'customer', 'Руслан', 'Билогаш', 'Ктотам', 1, '2025-03-25 16:48:09', '/public/uploads/users/1_1742921916.png', 'О себе', 6, NULL, NULL),
(4, 'Company', '$2y$10$tXRFUiEyY0bzh/5OtQ3GM..7xa7MVdiPLDNpFxMBvEw9HuF23V4Vq', 'customer', 'рУС', 'Bilogah', 'НИК', 1, '2025-03-26 08:37:01', '/public/uploads/users/4_1742999361.png', 'О МНЕ', 10, 2, NULL),
(12, 'support@flory.lt', '$2y$10$wsOPojRdLiFFIP1PICJtAOo6NO..3yUW7vHlySBSpuXgzGlIzMaI2', 'customer', NULL, NULL, NULL, 0, '2025-04-01 23:40:44', NULL, NULL, NULL, NULL, NULL),
(13, 'Company@test.ru', '$2y$10$HzljH6rHyk.pi4SsdZ1nXOqCTDpYt88GbChQwt0s0S/peokq/NiIa', 'customer', NULL, NULL, NULL, 0, '2025-04-02 23:37:37', NULL, NULL, NULL, NULL, NULL),
(14, 'Mail@mail.ru', '$2y$10$cFXNnvn6OPV6dgVtFY1fMeMTZCyGQvdspIJOulZZmqCT8CttIgaiW', 'customer', NULL, NULL, NULL, 0, '2025-04-03 13:15:10', NULL, NULL, NULL, NULL, NULL),
(21, '', '', 'customer', 'Алінчес', '', 'MeowOGkush', 0, '2025-04-05 17:24:24', '/public/uploads/users/5003827145_telegram_avatar.jpg', NULL, NULL, NULL, '5003827145'),
(22, '+37012345678', '$2y$10$Cw2F6.3RwL88nNzhyICRcuuN.2lVy1oH01h/1ROIOYAen/1yhf4xC', 'customer', 'Имя', 'Фамилия', 'Никнейм', 1, '2025-04-15 23:42:17', NULL, '', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблиці `visitor_logs`
--

CREATE TABLE `visitor_logs` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `page_url` varchar(255) NOT NULL,
  `visited_at` timestamp NULL DEFAULT current_timestamp(),
  `country_code` varchar(2) DEFAULT NULL,
  `country_name` varchar(100) DEFAULT NULL,
  `device_type` enum('desktop','tablet','mobile','unknown') DEFAULT 'unknown',
  `browser` varchar(50) DEFAULT 'unknown'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп даних таблиці `visitor_logs`
--

INSERT INTO `visitor_logs` (`id`, `ip_address`, `page_url`, `visited_at`, `country_code`, `country_name`, `device_type`, `browser`) VALUES
(0, '84.15.187.161', '/', '2025-04-02 19:02:10', 'LT', 'Lithuania', 'desktop', 'Chrome'),
(0, '84.15.187.161', '/feedback', '2025-04-02 19:03:26', 'LT', 'Lithuania', 'desktop', 'Chrome'),

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Індекси таблиці `api`
--
ALTER TABLE `api`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `api_type` (`api_type`);

--
-- Індекси таблиці `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `banner_slider`
--
ALTER TABLE `banner_slider`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Індекси таблиці `booking_categories`
--
ALTER TABLE `booking_categories`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `brand_carousel`
--
ALTER TABLE `brand_carousel`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `carousel`
--
ALTER TABLE `carousel`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `carousel_settings`
--
ALTER TABLE `carousel_settings`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Індекси таблиці `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Індекси таблиці `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `topic_id` (`topic_id`);

--
-- Індекси таблиці `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Індекси таблиці `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Індекси таблиці `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `custom_url` (`custom_url`),
  ADD UNIQUE KEY `custom_url_2` (`custom_url`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_news_created_at` (`created_at`);

--
-- Індекси таблиці `news_categories`
--
ALTER TABLE `news_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Індекси таблиці `news_reviews`
--
ALTER TABLE `news_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Індекси таблиці `news_translations`
--
ALTER TABLE `news_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `news_id` (`news_id`,`language_code`),
  ADD KEY `language_code` (`language_code`);

--
-- Індекси таблиці `nova_poshta_cities`
--
ALTER TABLE `nova_poshta_cities`
  ADD PRIMARY KEY (`ref`);

--
-- Індекси таблиці `nova_poshta_settings`
--
ALTER TABLE `nova_poshta_settings`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `nova_poshta_streets`
--
ALTER TABLE `nova_poshta_streets`
  ADD PRIMARY KEY (`ref`);

--
-- Індекси таблиці `nova_poshta_warehouses`
--
ALTER TABLE `nova_poshta_warehouses`
  ADD PRIMARY KEY (`ref`);

--
-- Індекси таблиці `order_history`
--
ALTER TABLE `order_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Індекси таблиці `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Індекси таблиці `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `prices`
--
ALTER TABLE `prices`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Індекси таблиці `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Індекси таблиці `shop_categories`
--
ALTER TABLE `shop_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Індекси таблиці `shop_delivery_methods`
--
ALTER TABLE `shop_delivery_methods`
  ADD PRIMARY KEY (`id`);

--
-- Індекси таблиці `shop_orders`
--
ALTER TABLE `shop_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Індекси таблиці `shop_products`
--
ALTER TABLE `shop_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_shop_products_status` (`status`);

--
-- Індекси таблиці `tenders`
--
ALTER TABLE `tenders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `city_id` (`city_id`),
  ADD KEY `idx_tenders_status` (`status`);

--
-- Індекси таблиці `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `contact` (`contact`),
  ADD UNIQUE KEY `telegram_id` (`telegram_id`),
  ADD KEY `city_id` (`city_id`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблиці `api`
--
ALTER TABLE `api`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

--
-- AUTO_INCREMENT для таблиці `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT для таблиці `banner_slider`
--
ALTER TABLE `banner_slider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблиці `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблиці `booking_categories`
--
ALTER TABLE `booking_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблиці `brand_carousel`
--
ALTER TABLE `brand_carousel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблиці `carousel`
--
ALTER TABLE `carousel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT для таблиці `carousel_settings`
--
ALTER TABLE `carousel_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблиці `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT для таблиці `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблиці `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблиці `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблиці `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблиці `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблиці `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT для таблиці `news_categories`
--
ALTER TABLE `news_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблиці `news_reviews`
--
ALTER TABLE `news_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблиці `news_translations`
--
ALTER TABLE `news_translations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблиці `nova_poshta_settings`
--
ALTER TABLE `nova_poshta_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблиці `order_history`
--
ALTER TABLE `order_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблиці `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблиці `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблиці `prices`
--
ALTER TABLE `prices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблиці `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблиці `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблиці `shop_categories`
--
ALTER TABLE `shop_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT для таблиці `shop_delivery_methods`
--
ALTER TABLE `shop_delivery_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблиці `shop_orders`
--
ALTER TABLE `shop_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT для таблиці `shop_products`
--
ALTER TABLE `shop_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT для таблиці `tenders`
--
ALTER TABLE `tenders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT для таблиці `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Обмеження зовнішнього ключа збережених таблиць
--

--
-- Обмеження зовнішнього ключа таблиці `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Обмеження зовнішнього ключа таблиці `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`topic_id`) REFERENCES `feedback` (`id`) ON DELETE SET NULL;

--
-- Обмеження зовнішнього ключа таблиці `gallery`
--
ALTER TABLE `gallery`
  ADD CONSTRAINT `gallery_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `news_categories` (`id`) ON DELETE SET NULL;

--
-- Обмеження зовнішнього ключа таблиці `news_categories`
--
ALTER TABLE `news_categories`
  ADD CONSTRAINT `news_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `news_categories` (`id`) ON DELETE SET NULL;

--
-- Обмеження зовнішнього ключа таблиці `news_reviews`
--
ALTER TABLE `news_reviews`
  ADD CONSTRAINT `news_reviews_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `news_translations`
--
ALTER TABLE `news_translations`
  ADD CONSTRAINT `news_translations_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_translations_ibfk_2` FOREIGN KEY (`language_code`) REFERENCES `languages` (`code`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `order_history`
--
ALTER TABLE `order_history`
  ADD CONSTRAINT `order_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `shop_orders` (`id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `shop_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `shop_products` (`id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `booking_categories` (`id`);

--
-- Обмеження зовнішнього ключа таблиці `shop_categories`
--
ALTER TABLE `shop_categories`
  ADD CONSTRAINT `shop_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `shop_categories` (`id`) ON DELETE SET NULL;

--
-- Обмеження зовнішнього ключа таблиці `shop_orders`
--
ALTER TABLE `shop_orders`
  ADD CONSTRAINT `shop_orders_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `shop_products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shop_orders_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `shop_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `shop_products`
--
ALTER TABLE `shop_products`
  ADD CONSTRAINT `shop_products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `shop_categories` (`id`) ON DELETE SET NULL;

--
-- Обмеження зовнішнього ключа таблиці `tenders`
--
ALTER TABLE `tenders`
  ADD CONSTRAINT `tenders_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`);

--
-- Обмеження зовнішнього ключа таблиці `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
