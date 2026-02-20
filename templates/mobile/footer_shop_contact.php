<?php
// /templates/mobile/footer_shop_contact.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
$settings = get_settings();

// Значения по умолчанию
$defaults = [
    'site_title' => 'Tender CMS',
    'site_email' => 'rbilohash@gmail.com',
    'site_phone' => '+37062222411 - Telegram',
    'site_address' => 'Вильнюс, Литва',
    'footer_background' => 'linear-gradient(135deg, #343a40 0%, #495057 100%)',
    'footer_text_color' => '#ffffff',
    'footer_link_color' => '#007bff'
];
$settings = array_merge($defaults, $settings);

// Диагностика
if (empty($settings)) {
    error_log("Ошибка: настройки в mobile/footer_shop_contact.php пусты, использованы значения по умолчанию.");
}
?>

<footer class="footer py-4" style="background: <?php echo htmlspecialchars($settings['footer_background']); ?>; color: <?php echo htmlspecialchars($settings['footer_text_color']); ?>;">
    <div class="container">
        <div class="row g-4">
            <!-- Контакты -->
            <div class="col-12 col-md-4">
                <h5 class="fw-bold mb-3">Контакты</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2"></i>
                        <a href="mailto:<?php echo htmlspecialchars($settings['site_email']); ?>" style="color: <?php echo htmlspecialchars($settings['footer_text_color']); ?>; text-decoration: none;">
                            <?php echo htmlspecialchars($settings['site_email']); ?>
                        </a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone me-2"></i>
                        <a href="tel:<?php echo htmlspecialchars($settings['site_phone']); ?>" style="color: <?php echo htmlspecialchars($settings['footer_text_color']); ?>; text-decoration: none;">
                            <?php echo htmlspecialchars($settings['site_phone']); ?>
                        </a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        <?php echo htmlspecialchars($settings['site_address']); ?>
                    </li>
                </ul>
            </div>
            <!-- Ссылки -->
            <div class="col-12 col-md-4">
                <h5 class="fw-bold mb-3">Навигация</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="/news" style="color: <?php echo htmlspecialchars($settings['footer_text_color']); ?>; text-decoration: none;">
                            <i class="fas fa-newspaper me-2"></i> Новости
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="/shop" style="color: <?php echo htmlspecialchars($settings['footer_text_color']); ?>; text-decoration: none;">
                            <i class="fas fa-shopping-cart me-2"></i> Магазин
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="/feedback" style="color: <?php echo htmlspecialchars($settings['footer_text_color']); ?>; text-decoration: none;">
                            <i class="fas fa-envelope me-2"></i> Обратная связь
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="/api" style="color: <?php echo htmlspecialchars($settings['footer_text_color']); ?>; text-decoration: none;">
                            <i class="fas fa-code me-2"></i> API
                        </a>
                    </li>
                </ul>
            </div>
            <!-- Социальные сети -->
            <div class="col-12 col-md-4">
                <h5 class="fw-bold mb-3">Следите за нами</h5>
                <div class="d-flex gap-3">
                    <a href="https://t.me/meistru_lt" target="_blank" class="social-icon" style="color: <?php echo htmlspecialchars($settings['footer_text_color']); ?>;">
                        <i class="fab fa-telegram-plane fa-2x"></i>
                    </a>
                    <a href="https://wa.me/37062222411" target="_blank" class="social-icon" style="color: <?php echo htmlspecialchars($settings['footer_text_color']); ?>;">
                        <i class="fab fa-whatsapp fa-2x"></i>
                    </a>
                    <a href="https://tiktok.com/mycreative_site" target="_blank" class="social-icon" style="color: <?php echo htmlspecialchars($settings['footer_text_color']); ?>;">
                        <i class="fab fa-tiktok fa-2x"></i>
                    </a>					
                </div>
            </div>
        </div><a class="nav-link" href="?force_mobile=0"><i class="fas fa-desktop me-2"></i>Перейти на десктопную версию</a>
        <!-- Копирайт -->
        <div class="text-center mt-4 pt-3 border-top" style="border-color: rgba(255,255,255,0.2) !important;">
            <p class="mb-0" style="font-size: 0.9rem;">
                &copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($settings['site_title']); ?>. Все права защищены.
            </p>
        </div>
    </div>
</footer>

<style>
    .footer {
        font-family: Roboto, sans-serif;
        line-height: 1.6;
    }
    .footer h5 {
        font-size: 1.1rem;
        color: <?php echo htmlspecialchars($settings['footer_text_color']); ?>;
    }
    .footer a {
        transition: color 0.3s ease;
    }
    .footer a:hover {
        color: <?php echo htmlspecialchars($settings['footer_link_color']); ?>;
        text-decoration: none;
    }
    .footer .social-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        transition: transform 0.3s ease;
    }
    .footer .social-icon:hover {
        transform: scale(1.1);
        color: <?php echo htmlspecialchars($settings['footer_link_color']); ?>;
    }
    .footer ul li {
        font-size: 0.9rem;
    }
    @media (max-width: 576px) {
        .footer {
            padding: 20px 0;
        }
        .footer h5 {
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        .footer ul li {
            font-size: 0.85rem;
        }
        .footer .social-icon {
            width: 36px;
            height: 36px;
            font-size: 1.5rem;
        }
        .footer .social-icon i {
            line-height: 1;
        }
        .footer .text-center p {
            font-size: 0.8rem;
        }
    }
</style>