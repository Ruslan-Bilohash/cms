<?php
if (!isset($settings)) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';
    $settings = get_settings($conn);
}

// Устанавливаем ширину, фон, отступы и округление блока
$width_style = ($settings['meta_width'] ?? 'auto') === 'auto' ? 'width: 100%;' : 'max-width: ' . htmlspecialchars($settings['meta_width'] ?? '900') . 'px; margin-left: auto; margin-right: auto;';
$background_style = 'background: ' . htmlspecialchars($settings['meta_background'] ?? '#f8f9fa') . '; padding: ' . htmlspecialchars($settings['meta_background_padding'] ?? '20px') . '; border-radius: ' . htmlspecialchars($settings['meta_background_border_radius'] ?? '15px') . ';';
$padding_style = 'padding: ' . htmlspecialchars($settings['meta_padding'] ?? '0px') . ';';
$border_radius_style = 'border-radius: ' . htmlspecialchars($settings['meta_border_radius'] ?? '0px') . ';';
?>

<div class="meta-info mt-0 mb-4" style="<?php echo $width_style . ' ' . $padding_style . ' ' . $border_radius_style; ?>" itemscope itemtype="http://schema.org/WebPage">
    <div class="meta-background" style="<?php echo $background_style; ?>">
        <?php if (!empty($settings['site_title'])): ?>
            <?php if (($settings['meta_display_mode'] ?? 'formatted') === 'formatted'): ?>
                <h1 style="color: <?php echo htmlspecialchars($settings['site_title_color'] ?? '#000000'); ?>; text-align: <?php echo htmlspecialchars($settings['site_title_align'] ?? 'center'); ?>;" class="display-4 fw-bold" itemprop="headline"><?php echo htmlspecialchars($settings['site_title']); ?></h1>
            <?php else: ?>
                <div style="color: <?php echo htmlspecialchars($settings['site_title_color'] ?? '#000000'); ?>; text-align: <?php echo htmlspecialchars($settings['site_title_align'] ?? 'center'); ?>;" itemprop="headline"><?php echo htmlspecialchars($settings['site_title']); ?></div>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($settings['site_description'])): ?>
            <?php if (($settings['meta_display_mode'] ?? 'formatted') === 'formatted'): ?>
                <<?php echo htmlspecialchars($settings['site_description_tag'] ?? 'div'); ?> style="color: <?php echo htmlspecialchars($settings['site_description_color'] ?? '#000000'); ?>; text-align: <?php echo htmlspecialchars($settings['site_description_align'] ?? 'center'); ?>;" class="description" itemprop="description">
                    <?php echo $settings['site_description']; // Выводим без htmlspecialchars для корректного рендеринга HTML ?>
                </<?php echo htmlspecialchars($settings['site_description_tag'] ?? 'div'); ?>>
            <?php else: ?>
                <div style="color: <?php echo htmlspecialchars($settings['site_description_color'] ?? '#000000'); ?>; text-align: <?php echo htmlspecialchars($settings['site_description_align'] ?? 'center'); ?>;" itemprop="description">
                    <?php echo $settings['site_description']; // Выводим без htmlspecialchars для корректного рендеринга HTML ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>