<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

$settings_file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/shop_settings.php';
$settings = file_exists($settings_file) ? require $settings_file : [];
$footer = $settings['footer'] ?? [];
?>

<footer>
    <div class="content-wrapper">
        <div class="nav-columns">
            <?php foreach ($footer['columns'] as $column): ?>
                <div class="column">
                    <?php if (!empty($column['show_title'])): ?>
                        <h4><i class="fas <?php echo htmlspecialchars($column['title_icon']); ?>" style="font-size: <?php echo (int)$column['title_icon_size']; ?>px; color: <?php echo htmlspecialchars($column['title_icon_color']); ?>;"></i> <?php echo htmlspecialchars($column['title']); ?></h4>
                    <?php endif; ?>
                    <?php foreach ($column['items'] as $item): ?>
                        <div class="footer-link-wrapper">
                            <a href="<?php echo htmlspecialchars($item['link']); ?>" class="footer-link">
                                <i class="fas <?php echo htmlspecialchars($item['icon']); ?>" style="font-size: <?php echo (int)$item['icon_size']; ?>px; color: <?php echo htmlspecialchars($item['icon_color']); ?>;"></i>
                                <span><?php echo htmlspecialchars($item['text']); ?></span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="logo-info-container">
            <?php if (!empty($footer['logo_url']) && !empty($footer['show_logo'])): ?>
                <div class="logo-container">
                    <img src="<?php echo htmlspecialchars($footer['logo_url']); ?>" 
                         alt="Логотип" 
                         style="width: <?php echo (int)$footer['logo_width']; ?>px; height: <?php echo (int)$footer['logo_height']; ?>px;">
                </div>
            <?php endif; ?>
			
            <div class="info">
                <div class="info-row info-row-1">
                    <?php if (!empty($footer['show_text_1'])): ?>
                        <div class="info-item"><i class="fas <?php echo htmlspecialchars($footer['text_1_icon']); ?>" style="font-size: <?php echo (int)$footer['text_1_icon_size']; ?>px; color: <?php echo htmlspecialchars($footer['text_1_icon_color']); ?>;"></i> <?php echo htmlspecialchars($footer['text_1']); ?></div>
                    <?php endif; ?>
					<!-- Кнопки переключения версии -->
                
                    <a class="nav-link" href="?force_mobile=1"><i class="fas fa-mobile-alt me-2"></i>Перейти на мобильную версию</a>

              
                    <?php if (!empty($footer['show_phone'])): ?>
                        <div class="info-item"><i class="fas <?php echo htmlspecialchars($footer['phone_icon']); ?>" style="font-size: <?php echo (int)$footer['phone_icon_size']; ?>px; color: <?php echo htmlspecialchars($footer['phone_icon_color']); ?>;"></i> <?php echo htmlspecialchars($footer['phone']); ?></div>
                    <?php endif; ?>
                </div>
                <div class="info-row info-row-2">
                    <?php if (!empty($footer['show_address'])): ?>
                        <div class="info-item"><i class="fas <?php echo htmlspecialchars($footer['address_icon']); ?>" style="font-size: <?php echo (int)$footer['address_icon_size']; ?>px; color: <?php echo htmlspecialchars($footer['address_icon_color']); ?>;"></i> <?php echo htmlspecialchars($footer['address']); ?></div>
                   =<?php endif; ?>
                    <?php if (!empty($footer['show_email'])): ?>
                        <div class="info-item"><i class="fas <?php echo htmlspecialchars($footer['email_icon']); ?>" style="font-size: <?php echo (int)$footer['email_icon_size']; ?>px; color: <?php echo htmlspecialchars($footer['email_icon_color']); ?>;"></i> <?php echo htmlspecialchars($footer['email']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($footer['show_working_hours'])): ?>
                        <div class="info-item"><i class="fas <?php echo htmlspecialchars($footer['working_hours_icon']); ?>" style="font-size: <?php echo (int)$footer['working_hours_icon_size']; ?>px; color: <?php echo htmlspecialchars($footer['working_hours_icon_color']); ?>;"></i> <?php echo htmlspecialchars($footer['working_hours']); ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="highlight"></div>
	</body>
</footer>
<div class="developer-block text-center mt-4 p-4 bg-dark rounded shadow-lg">
            <h5 class="mb-4 text-white fw-bold">Связаться с разработчиком</h5>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="mailto:rbilohash@gmail.com" class="btn btn-outline-primary btn-lg d-flex align-items-center gap-2 hover-scale">
                    <i class="fas fa-envelope fa-lg"></i>
                    <span>Написать разработчику Pro Shop</span>
                </a>
                <a href="https://t.me/meistru_lt" class="btn btn-outline-info btn-lg d-flex align-items-center gap-2 hover-scale">
                    <i class="fab fa-telegram-plane fa-lg"></i>
                    <span>Telegram</span>
                </a>
                <a href="https://github.com/Ruslan-Bilohash" class="btn btn-outline-dark btn-lg d-flex align-items-center gap-2 hover-scale">
                    <i class="fab fa-github fa-lg"></i>
                    <span>GitHub</span>
                </a>
            </div>
        </div>

<style>
    footer {
        background: linear-gradient(135deg, <?php echo htmlspecialchars($footer['bg_color_1']); ?>, <?php echo htmlspecialchars($footer['bg_color_2']); ?>);
        color: <?php echo htmlspecialchars($footer['text_color']); ?>;
        padding: 60px;
        font-family: 'Arial', sans-serif;
        font-size: <?php echo (int)$footer['font_size']; ?>px;
        position: relative;
        overflow: hidden;
        border-top: 5px solid #e74c3c;
    }
    .content-wrapper {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        width: 100%;
    }
    .logo-container img {
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
    }
    .logo-info-container {
        display: flex;
        align-items: flex-start;
    }
    .logo-container {
        margin-right: 20px;
    }
    .info {
        display: flex;
        flex-direction: column;
    }
    .info-row {
        display: flex;
        flex-wrap: wrap;
        margin: 5px 0;
    }
    .info-row-1, .info-row-2 {
        align-items: center;
    }
    .info-item {
        margin: 5px 15px 5px 0;
        display: flex;
        align-items: center;
    }
    .info-item i {
        margin-right: 8px;
    }
    .nav-columns {
        display: flex;
        justify-content: space-between;
        flex-grow: 1;
        max-width: 900px;
        margin-right: 50px;
    }
    .column {
        flex: 1;
        margin: 0 20px;
    }
    .column h4 {
        color: <?php echo htmlspecialchars($column['title_color'] ?? '#fff'); ?>;
        font-size: <?php echo (int)($column['title_font_size'] ?? 20); ?>px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }
    .column h4 i {
        margin-right: 8px;
    }
    .footer-link-wrapper {
        margin: 10px 0;
    }
    .footer-link {
        color: <?php echo htmlspecialchars($footer['link_color']); ?>;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        white-space: nowrap;
    }
    .footer-link i {
        margin-right: 8px;
    }
    .footer-link span {
        display: inline;
    }
    .footer-link:hover {
        color: #fff;
        text-shadow: 0 0 10px <?php echo htmlspecialchars($footer['link_color']); ?>, 0 0 20px <?php echo htmlspecialchars($footer['link_color']); ?>;
    }
    .highlight {
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.1);
        transform: skewX(-20deg);
        animation: shine 4s infinite;
    }
    @keyframes shine {
        0% { left: -100%; }
        20% { left: 100%; }
        100% { left: 100%; }
    }

    @media (max-width: 991px) and (min-width: 769px) {
        footer {
            padding: 40px;
        }
        .content-wrapper {
            flex-direction: column;
            align-items: center;
        }
        .nav-columns {
            flex-direction: row;
            justify-content: space-around;
            width: 100%;
            margin-right: 0;
            margin-bottom: 20px;
        }
        .column {
            margin: 0 10px;
        }
        .column h4 {
            font-size: <?php echo (int)($column['title_font_size'] ?? 20) * 0.9; ?>px;
            justify-content: center;
        }
        .footer-link {
            justify-content: center;
        }
        .logo-info-container {
            flex-direction: row;
            justify-content: center;
            width: 100%;
            text-align: left;
        }
        .logo-container {
            margin-right: 20px;
        }
        .info-row {
            justify-content: flex-start;
        }
        .info-item {
            margin: 5px 15px 5px 0;
        }
    }

    @media (max-width: 768px) {
        footer {
            flex-direction: column;
            text-align: center;
            padding: 30px;
        }
        .content-wrapper {
            flex-direction: column;
            align-items: center;
        }
        .nav-columns {
            flex-direction: column;
            margin-bottom: 20px;
            width: 100%;
        }
        .column {
            margin: 20px 0;
            width: 100%;
        }
        .column h4 {
            font-size: <?php echo (int)($column['title_font_size'] ?? 20) * 0.9; ?>px;
            justify-content: center;
        }
        .footer-link {
            justify-content: center;
        }
        .logo-info-container {
            flex-direction: column;
            align-items: center;
        }
        .logo-container {
            margin-right: 0;
            margin-bottom: 20px;
        }
        .info-row {
            justify-content: center;
        }
        .info-item {
            margin: 5px 0;
            justify-content: center;
        }
    }
</style>