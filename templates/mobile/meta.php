<?php
// /templates/mobile/meta.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
$settings = get_settings();

// Значения по умолчанию для мобильной версии
$defaults = [
    'meta_width' => '100%',
    'meta_background' => '#ffffff',
    'meta_background_padding' => '10px',
    'meta_background_border_radius' => '8px',
    'meta_padding' => '5px',
    'meta_border_radius' => '8px',
    'site_title' => 'CMS Pro Website Management 🚀 ',
    'site_title_color' => '#000000',
    'site_title_align' => 'center',
    'site_description' => '<p>Добро пожаловать в Pro Website Management 🚀</p>',
    'site_description_color' => '#333333',
    'site_description_align' => 'left',
    'site_description_tag' => 'div',
    'meta_display_mode' => 'formatted'
];

// Объединяем настройки с дефолтными
$settings = array_merge($defaults, $settings);

// Устанавливаем стили
$width_style = $settings['meta_width'] === 'auto' ? 'width: 100%;' : 'max-width: ' . htmlspecialchars($settings['meta_width']) . '; margin: 0 auto;';
$background_style = 'background: ' . htmlspecialchars($settings['meta_background']) . '; padding: ' . htmlspecialchars($settings['meta_background_padding']) . '; border-radius: ' . htmlspecialchars($settings['meta_background_border_radius']) . ';';
$padding_style = 'padding: ' . htmlspecialchars($settings['meta_padding']) . ';';
$border_radius_style = 'border-radius: ' . htmlspecialchars($settings['meta_border_radius']) . ';';

// Простая очистка HTML для предотвращения XSS
function sanitize_html($html) {
    // Удаляем опасные теги и атрибуты
    $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i', '', $html);
    $html = preg_replace('/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/i', '', $html);
    $html = preg_replace('/on\w+="[^"]*"/i', '', $html); // Удаляем JS-события
    return $html;
}

// Диагностика
if (empty($settings)) {
    error_log("Ошибка: настройки в mobile/meta.php пусты, использованы значения по умолчанию.");
}
?>

<div class="meta-info mt-2 mb-3" style="<?php echo $width_style . ' ' . $padding_style . ' ' . $border_radius_style; ?>" itemscope itemtype="http://schema.org/WebPage">
    <div class="meta-background" style="<?php echo $background_style; ?>">
        <?php if (!empty($settings['site_title'])): ?>
            <?php if ($settings['meta_display_mode'] === 'formatted'): ?>
                <h1 style="color: <?php echo htmlspecialchars($settings['site_title_color']); ?>; text-align: <?php echo htmlspecialchars($settings['site_title_align']); ?>; font-size: 1.5rem;" class="fw-bold" itemprop="headline"><?php echo htmlspecialchars($settings['site_title']); ?></h1>
            <?php else: ?>
                <div style="color: <?php echo htmlspecialchars($settings['site_title_color']); ?>; text-align: <?php echo htmlspecialchars($settings['site_title_align']); ?>; font-size: 1.25rem;" itemprop="headline"><?php echo htmlspecialchars($settings['site_title']); ?></div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($settings['site_description'])): ?>
            <?php if ($settings['meta_display_mode'] === 'formatted'): ?>
                <<?php echo htmlspecialchars($settings['site_description_tag']); ?> style="color: <?php echo htmlspecialchars($settings['site_description_color']); ?>; text-align: <?php echo htmlspecialchars($settings['site_description_align']); ?>; font-size: 0.9rem;" class="description" itemprop="description">
                    <?php echo sanitize_html($settings['site_description']); ?>
                </<?php echo htmlspecialchars($settings['site_description_tag']); ?>>
            <?php else: ?>
                <div style="color: <?php echo htmlspecialchars($settings['site_description_color']); ?>; text-align: <?php echo htmlspecialchars($settings['site_description_align']); ?>; font-size: 0.9rem;" itemprop="description">
                    <?php echo sanitize_html($settings['site_description']); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    .meta-info {
        box-sizing: border-box;
    }
    .meta-info h1, .meta-info h2 {
        font-size: 1.5rem;
        margin: 0.5rem 0;
    }
    .meta-info p {
        font-size: 0.9rem;
        margin: 0.3rem 0;
    }
    .meta-info ul {
        padding-left: 1.2rem;
        font-size: 0.9rem;
    }
    .meta-info blockquote {
        border-left: 3px solid #007bff;
        padding-left: 0.8rem;
        font-style: italic;
        font-size: 0.9rem;
        margin: 0.5rem 0;
    }
    @media (max-width: 576px) {
        .meta-info {
            margin: 0.5rem 0;
        }
        .meta-background {
            padding: 8px !important;
            border-radius: 6px !important;
        }
        .meta-info h1, .meta-info h2 {
            font-size: 1.2rem !important;
        }
        .meta-info p, .meta-info ul, .meta-info blockquote {
            font-size: 0.8rem !important;
        }
        .meta-info .description {
            font-size: 0.8rem !important;
        }
    }
</style>