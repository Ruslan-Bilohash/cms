<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

// Получаем настройки из файла
$settings_file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/site_settings.php';
$settings = file_exists($settings_file) ? include $settings_file : [];
$carousel_settings = $settings['carousel'] ?? [
    'height' => 400,
    'caption_color' => '#ffffff',
    'caption_opacity' => 60,
    'border_radius' => 15,
    'speed' => 5000,
    'autoplay' => 1,
    'button_color' => '#ffffff',
    'margin' => 20,
];

// Получаем активные слайды для главной страницы
$result = $conn->query("SELECT image_path, link, caption FROM carousel WHERE is_active = 1 AND display_on_home = 1 ORDER BY created_at DESC");
$slides = [];
while ($row = $result->fetch_assoc()) {
    $slides[] = $row;
}
$slide_count = count($slides);
?>

<!-- Передача настроек в JavaScript -->
<script>
    window.carouselSettings = {
        autoplay: <?php echo $carousel_settings['autoplay'] ? 'true' : 'false'; ?>,
        speed: <?php echo $carousel_settings['speed']; ?>,
        slideCount: <?php echo $slide_count; ?>
    };
</script>

<div class="swiper-container">
    <div class="swiper-wrapper">
        <?php if (empty($slides)): ?>
            <div class="swiper-slide">
                <p>Нет активных слайдов в карусели.</p>
            </div>
        <?php else: ?>
            <?php foreach ($slides as $slide): ?>
                <div class="swiper-slide">
                    <?php if ($slide['link']): ?>
                        <a href="<?php echo htmlspecialchars($slide['link']); ?>" target="_blank">
                            <img src="<?php echo htmlspecialchars($slide['image_path']); ?>" alt="<?php echo htmlspecialchars($slide['caption']); ?>">
                        </a>
                    <?php else: ?>
                        <img src="<?php echo htmlspecialchars($slide['image_path']); ?>" alt="<?php echo htmlspecialchars($slide['caption']); ?>">
                    <?php endif; ?>
                    <?php if ($slide['caption']): ?>
                        <div class="swiper-caption">
                            <h5><?php echo htmlspecialchars($slide['caption']); ?></h5>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php if ($slide_count > 1): ?>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
    <?php endif; ?>
</div>

<style>
.swiper-container {
    width: 100%;
    height: <?php echo $carousel_settings['height']; ?>px;
    margin: <?php echo $carousel_settings['margin']; ?>px auto;
    position: relative;
    overflow: hidden;
}

.swiper-wrapper {
    height: 100%;
}

.swiper-slide {
    height: 100%;
    position: relative;
    overflow: hidden;
    background: #000;
}

.swiper-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: <?php echo $carousel_settings['border_radius']; ?>px;
    display: block;
}

.swiper-slide:hover img {
    transform: scale(1.05);
    transition: transform 0.5s ease;
}

.swiper-caption {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 15px;
    background: rgba(0, 0, 0, <?php echo $carousel_settings['caption_opacity'] / 100; ?>);
    color: <?php echo $carousel_settings['caption_color']; ?>;
    text-align: center;
    z-index: 10;
    border-radius: 0 0 <?php echo $carousel_settings['border_radius']; ?>px <?php echo $carousel_settings['border_radius']; ?>px;
}

.swiper-button-prev, .swiper-button-next {
    color: <?php echo $carousel_settings['button_color']; ?>;
    width: 40px;
    height: 40px;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.swiper-button-prev:hover, .swiper-button-next:hover {
    opacity: 1;
}

.swiper-button-prev:after, .swiper-button-next:after {
    font-size: 20px;
}

@media (max-width: 768px) {
    .swiper-container {
        margin: <?php echo $carousel_settings['margin'] * 0.75; ?>px auto;
        height: <?php echo $carousel_settings['height'] * 0.6; ?>px;
    }
    .swiper-caption {
        padding: 10px;
        font-size: 0.9rem;
    }
    .swiper-button-prev, .swiper-button-next {
        width: 30px;
        height: 30px;
    }
    .swiper-button-prev:after, .swiper-button-next:after {
        font-size: 16px;
    }
}

@media (max-width: 576px) {
    .swiper-container {
        margin: <?php echo $carousel_settings['margin'] * 0.5; ?>px auto;
        height: <?php echo $carousel_settings['height'] * 0.4; ?>px;
    }
    .swiper-caption {
        padding: 8px;
        font-size: 0.8rem;
    }
    .swiper-button-prev, .swiper-button-next {
        width: 25px;
        height: 25px;
    }
    .swiper-button-prev:after, .swiper-button-next:after {
        font-size: 14px;
    }
}
</style>