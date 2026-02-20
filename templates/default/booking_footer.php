<?php
// templates/default/booking_footer.php
$settings_file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/booking_settings.php';
$settings = file_exists($settings_file) ? include $settings_file : [
    'footer_phone' => '+38 (012) 345-67-89',
    'footer_email' => 'info@example.com',
    'footer_address' => 'г. Киев, ул. Примерная, 10',
    'footer_facebook' => 'https://facebook.com',
    'footer_instagram' => 'https://instagram.com',
    'footer_twitter' => 'https://twitter.com',
    'footer_telegram' => 'https://telegram.me',
    'footer_site_name' => 'Website 🚀 Management',
    'footer_navigation' => [
        ['url' => '/', 'text' => 'Главная', 'icon' => 'fas fa-home'],
        ['url' => '/templates/default/booking.php', 'text' => 'Бронирование', 'icon' => 'fas fa-hotel']
    ]
];
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #003580;
            --secondary-color: #febb02;
            --success-color: #0071c2;
            --text-color: #333;
            --footer-bg: #f4f4f4;
            --footer-text: #555;
        }
        .footer {
            background: var(--footer-bg);
            color: var(--footer-text);
            padding: 40px 20px;
            font-family: 'Arial', sans-serif;
            border-top: 4px solid var(--primary-color);
            margin-top: 40px;
        }
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        .footer-column {
            flex: 1;
            min-width: 200px;
        }
        .footer-column h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .footer-column ul {
            list-style: none;
            padding: 0;
        }
        .footer-column ul li {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .footer-column ul li a {
            color: var(--footer-text);
            text-decoration: none;
            transition: color 0.3s;
        }
        .footer-column ul li a:hover {
            color: var(--success-color);
        }
        .footer-column .social-links a {
            font-size: 1.5rem;
            margin-right: 15px;
            color: var(--primary-color);
            transition: color 0.3s, transform 0.3s;
        }
        .footer-column .social-links a:hover {
            color: var(--secondary-color);
            transform: scale(1.2);
        }
        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            margin-top: 20px;
            font-size: 0.9rem;
        }
        .footer-bottom a {
            color: var(--success-color);
            text-decoration: none;
        }
        .footer-bottom a:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .footer-container {
                flex-direction: column;
                text-align: center;
            }
            .footer-column {
                margin-bottom: 20px;
            }
            .footer-column h3 {
                justify-content: center;
            }
            .footer-column ul li {
                justify-content: center;
            }
            .footer-column .social-links {
                display: flex;
                justify-content: center;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-column">
                <h3><i class="fas fa-address-book"></i> Контакты</h3>
                <ul>
                    <li><i class="fas fa-phone"></i> <a href="tel:<?php echo htmlspecialchars($settings['footer_phone']); ?>"><?php echo htmlspecialchars($settings['footer_phone']); ?></a></li>
                    <li><i class="fas fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($settings['footer_email']); ?>"><?php echo htmlspecialchars($settings['footer_email']); ?></a></li>
                    <li><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($settings['footer_address']); ?></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3><i class="fas fa-share-alt"></i> Социальные сети</h3>
                <div class="social-links">
                    <a href="<?php echo htmlspecialchars($settings['footer_facebook']); ?>" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?php echo htmlspecialchars($settings['footer_instagram']); ?>" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="<?php echo htmlspecialchars($settings['footer_twitter']); ?>" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="<?php echo htmlspecialchars($settings['footer_telegram']); ?>" target="_blank" aria-label="Telegram"><i class="fab fa-telegram-plane"></i></a>
                </div>
            </div>
            <div class="footer-column">
                <h3><i class="fas fa-info-circle"></i> Навигация</h3>
                <ul>
                    <?php foreach ($settings['footer_navigation'] as $nav): ?>
                        <li><i class="<?php echo htmlspecialchars($nav['icon']); ?>"></i> <a href="<?php echo htmlspecialchars($nav['url']); ?>"><?php echo htmlspecialchars($nav['text']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            © <?php echo date('Y'); ?> <?php echo htmlspecialchars($settings['footer_site_name']); ?>. Все права защищены. <br>
            Разработано <a href="mailto:rbilohash@gmail.com" target="_blank">Ruslan Bilohash</a>.
        </div>
    </footer>
</body>
</html>