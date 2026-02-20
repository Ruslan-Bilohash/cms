<?php
header('Content-Type: text/css');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/functions.php';

$settings = get_settings();
if (empty($settings) || !is_array($settings)) {
    $settings = [];
}

$default_settings = [
    'header_color' => '#00ff00',
    'footer_color' => '#ff0000',
    'button_color' => '#0000ff',
    'button_size' => 'medium',
    'button_shape' => 10,
    'show_logo' => 1,
    'site_nav_link_color' => '#ff00ff',
    'show_slider' => 1,
    'navbar_brand_color' => '#ffff00',
    'login_button_size' => 'medium',
    'site_title_color' => '#000000',
    'site_description_color' => '#000000',
    'site_title_align' => 'center',
    'meta_background' => '#f8f1df',
    'meta_background_padding' => '20px',
    'meta_background_border_radius' => '15px',
    'meta_display_mode' => 'formatted',
    'meta_width' => 'auto',
    'meta_padding' => '0px',
    'meta_border_radius' => '0px',
    'header_button_color' => '#007bff',
    'add_tender_button_color' => '#007bff',
    'add_tender_button_size' => 'large',
    'slider_speed' => 5000,
    'slider_height' => '400px',
    'slider_width' => '100%',
    'slider_padding' => '100px',
    'slider_button_color' => '#000000',
    'slider_button_size' => 'medium',
    'header_stripe_color' => '#FFFFFF',
    'header_stripe_opacity' => 0.30,
    'header_stripe_width' => '20px'
];

$settings = array_merge($default_settings, $settings);
$carousel_settings = $settings['carousel'] ?? [
    'height' => 400,
    'caption_color' => '#ffffff',
    'caption_opacity' => 60,
    'border_radius' => 15,
    'speed' => 5000,
    'autoplay' => 1,
];

$button_font_size = ($settings['button_size'] === 'large' ? '1.25rem' : ($settings['button_size'] === 'medium' ? '1rem' : '0.875rem'));
$login_button_font_size = ($settings['login_button_size'] === 'large' ? '1.25rem' : ($settings['login_button_size'] === 'medium' ? '1rem' : '0.875rem'));
$add_tender_button_font_size = ($settings['add_tender_button_size'] === 'large' ? '1.25rem' : ($settings['add_tender_button_size'] === 'medium' ? '1rem' : '0.875rem'));
?>

:root {
    --header-color: <?php echo htmlspecialchars($settings['header_color']); ?>;
    --footer-color: <?php echo htmlspecialchars($settings['footer_color']); ?>;
    --button-color: <?php echo htmlspecialchars($settings['button_color']); ?>;
    --button-shape: <?php echo htmlspecialchars($settings['button_shape']); ?>px;
    --site-nav-link-color: <?php echo htmlspecialchars($settings['site_nav_link_color']); ?>;
    --navbar-brand-color: <?php echo htmlspecialchars($settings['navbar_brand_color']); ?>;
    --site-title-color: <?php echo htmlspecialchars($settings['site_title_color']); ?>;
    --site-description-color: <?php echo htmlspecialchars($settings['site_description_color']); ?>;
    --meta-background: <?php echo htmlspecialchars($settings['meta_background']); ?>;
    --meta-background-padding: <?php echo htmlspecialchars($settings['meta_background_padding']); ?>;
    --meta-background-border-radius: <?php echo htmlspecialchars($settings['meta_background_border_radius']); ?>;
    --meta-width: <?php echo htmlspecialchars($settings['meta_width']); ?>;
    --meta-padding: <?php echo htmlspecialchars($settings['meta_padding']); ?>;
    --meta-border-radius: <?php echo htmlspecialchars($settings['meta_border_radius']); ?>;
    --header-button-color: <?php echo htmlspecialchars($settings['header_button_color']); ?>;
    --add-tender-button-color: <?php echo htmlspecialchars($settings['add_tender_button_color']); ?>;
    --carousel-height: <?php echo htmlspecialchars($carousel_settings['height']); ?>px;
    --carousel-caption-color: <?php echo htmlspecialchars($carousel_settings['caption_color']); ?>;
    --carousel-caption-opacity: <?php echo htmlspecialchars($carousel_settings['caption_opacity']); ?>;
    --carousel-border-radius: <?php echo htmlspecialchars($carousel_settings['border_radius']); ?>px;
}

body {
    background: #f4f4f4;
    font-family: 'Arial', sans-serif;
}

header, .navbar {
    background-color: var(--header-color) !important;
    position: relative;
    overflow: hidden;
}

.header-stripe {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, var(--header-stripe-color), transparent);
    transform: rotate(30deg);
    pointer-events: none;
    opacity: var(--header-stripe-opacity);
    z-index: 1;
}

.navbar {
    position: relative;
    z-index: 2;
}

.header-btn {
    background-color: var(--header-button-color) !important;
    border-color: var(--header-button-color) !important;
    color: white;
    padding: 8px 15px;
    margin-left: 5px;
    margin-right: 10px;
    border-radius: var(--button-shape) !important;
    font-size: <?php echo $login_button_font_size; ?>;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    white-space: nowrap;
    transition: all 0.3s ease;
}

.header-btn:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    background-color: color-mix(in srgb, var(--header-button-color) 90%, black) !important;
}

.nav-item {
    margin-right: 10px;
}

footer.footer {
    background-color: var(--footer-color) !important;
    width: 100vw;
    margin-left: calc(-50vw + 50%);
    margin-right: calc(-50vw + 50%);
    padding: 20px 0;
    position: relative;
    left: 0;
    box-sizing: border-box;
}

.btn {
    background-color: var(--button-color) !important;
    border-color: var(--button-color) !important;
    border-radius: var(--button-shape);
    font-size: <?php echo $button_font_size; ?>;
    color: white;
}

.btn-modern {
    border-radius: 20px;
    padding: 8px 20px;
}

.add-tender-btn {
    background-color: var(--add-tender-button-color) !important;
    border-color: var(--add-tender-button-color) !important;
    font-size: <?php echo $add_tender_button_font_size; ?>;
    border-radius: var(--button-shape) !important;
}

.navbar-nav .nav-link {
    color: var(--site-nav-link-color) !important;
}

.navbar-brand {
    color: var(--navbar-brand-color) !important;
    font-weight: bold;
    font-size: 1.8rem;
}

.site-title {
    color: var(--site-title-color) !important;
    text-align: <?php echo htmlspecialchars($settings['site_title_align']); ?>;
}

.site-description {
    color: var(--site-description-color) !important;
}

.meta-block {
    background-color: var(--meta-background) !important;
    padding: var(--meta-background-padding);
    border-radius: var(--meta-background-border-radius);
    width: var(--meta-width);
}

.meta-content {
    padding: var(--meta-padding);
    border-radius: var(--meta-border-radius);
}



<?php if ($settings['show_logo'] == 0): ?>
.logo {
    display: none !important;
}
<?php endif; ?>

