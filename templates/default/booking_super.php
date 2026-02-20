<?php
// templates/default/booking_super.php — Головна лендінг-сторінка Booking CMS
// Повністю мультимовна версія — 5 мов (UA, RU, EN, LT, NO)
// Оновлено: 26 грудня 2025

session_start();

// Дозволені мови
$allowed_langs = ['ua', 'ru', 'en', 'lt', 'no'];
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'ua';
if (!in_array($lang, $allowed_langs)) $lang = 'ua';
$_SESSION['lang'] = $lang;

// Повний масив перекладів — усі 5 мов
$translations = [
    'ua' => [
        'title' => 'Booking CMS 2025 — Скрипт онлайн-бронювання номерів, апартаментів, авто',
        'hero_h1' => 'Запустіть власний сайт бронювання за 1 день',
        'hero_sub' => 'Без комісій Booking.com та Airbnb • Повний контроль • 5 мов • Разова оплата + 365 днів безкоштовних оновлень',
        'cta_order' => 'Замовити за 1 день',
        'cta_contact' => 'Написати розробнику',
        'cta_individual' => 'Обговорити індивідуальну розробку',
        'features_title' => 'Чому обирають Booking CMS у 2025 році?',
        'individual_title' => 'Індивідуальна розробка під ваш бізнес',
        'individual_desc' => 'Не підходить стандартне рішення? Замовте унікальний сайт бронювання, повністю адаптований під ваш бренд, готель, віллу чи автопрокат.',
        'popular_cities_title' => 'Де наш скрипт вже працюють або дуже потрібний',
        'github_text' => 'Переглянути код на GitHub',
        'year_free_updates' => 'Разова оплата + 365 днів безкоштовних оновлень',
        'shop_button' => 'Інтернет-магазин',
        'booking_button' => 'Система бронювання',

        'no_commissions_title' => 'Без комісій',
        'no_commissions_desc'  => 'Усі гроші від бронювань — тільки вам',
        'five_languages_title' => '5 повноцінних мов',
        'five_languages_desc'  => 'Українська, російська, англійська, литовська, норвезька',
        'one_time_payment_title' => 'Разова оплата + 365 днів безкоштовних оновлень',
        'one_time_payment_desc'  => 'Один платіж — рік безкоштовних оновлень',

        'unique_design' => 'Унікальний дизайн під ваш бренд та корпоративні кольори',
        'payment_systems' => 'Платіжні системи LiqPay, Stripe, Fondy, PayPal, WayForPay',
        'integrations' => 'Інтеграції CRM, чат-боти, канали дистрибуції, Telegram',
        'timeline' => 'Терміни від 7 до 25 робочих днів',
    ],

    'ru' => [
        'title' => 'Booking CMS 2025 — Скрипт онлайн-бронирования номеров и апартаментов',
        'hero_h1' => 'Запустите свой сайт бронирования за 1 день',
        'hero_sub' => 'Без комиссий Booking.com и Airbnb • Полный контроль • 5 языков • Разовая оплата + 365 дней бесплатных обновлений',
        'cta_order' => 'Заказать за 1 день',
        'cta_contact' => 'Написать разработчику',
        'cta_individual' => 'Обсудить индивидуальную разработку',
        'features_title' => 'Почему выбирают Booking CMS в 2025 году?',
        'individual_title' => 'Индивидуальная разработка под ваш бизнес',
        'individual_desc' => 'Не подходит стандартное решение? Закажите уникальный сайт бронирования, полностью адаптированный под ваш бренд, отель, виллу или автопрокат.',
        'popular_cities_title' => 'Где наш скрипт уже работает или очень нужен',
        'github_text' => 'Посмотреть код на GitHub',
        'year_free_updates' => 'Разовая оплата + 365 дней бесплатных обновлений',
        'shop_button' => 'Интернет-магазин',
        'booking_button' => 'Система бронирования',

        'no_commissions_title' => 'Без комиссий',
        'no_commissions_desc'  => 'Все деньги от бронирований — только вам',
        'five_languages_title' => '5 полноценных языков',
        'five_languages_desc'  => 'Украинский, русский, английский, литовский, норвежский',
        'one_time_payment_title' => 'Разовая оплата + 365 дней бесплатных обновлений',
        'one_time_payment_desc'  => 'Один платёж — год бесплатных обновлений',

        'unique_design' => 'Уникальный дизайн под ваш бренд и корпоративные цвета',
        'payment_systems' => 'Платёжные системы LiqPay, Stripe, Fondy, PayPal, WayForPay',
        'integrations' => 'Интеграции CRM, чат-боты, каналы дистрибуции, Telegram',
        'timeline' => 'Сроки от 7 до 25 рабочих дней',
    ],

    'en' => [
        'title' => 'Booking CMS 2025 — Online Booking Script for Hotels & Apartments',
        'hero_h1' => 'Launch Your Booking Website in Just 1 Day',
        'hero_sub' => 'Zero Booking.com / Airbnb commissions • Full control • 5 languages • One-time payment + 365 days free updates',
        'cta_order' => 'Order in 1 Day',
        'cta_contact' => 'Contact Developer',
        'cta_individual' => 'Discuss Custom Development',
        'features_title' => 'Why Choose Booking CMS in 2025?',
        'individual_title' => 'Custom Development for Your Business',
        'individual_desc' => 'Need something unique? Order a fully custom booking website tailored to your brand, hotel, villa or car rental business.',
        'popular_cities_title' => 'Where our script is already working or highly needed',
        'github_text' => 'View source code on GitHub',
        'year_free_updates' => 'One-time payment + 365 days of free updates',
        'shop_button' => 'Online Shop',
        'booking_button' => 'Booking System',

        'no_commissions_title' => 'No commissions',
        'no_commissions_desc'  => 'All money from bookings — only yours',
        'five_languages_title' => '5 full languages',
        'five_languages_desc'  => 'Ukrainian, Russian, English, Lithuanian, Norwegian',
        'one_time_payment_title' => 'One-time payment + 365 days of free updates',
        'one_time_payment_desc'  => 'One payment — a year of free updates',

        'unique_design' => 'Unique design tailored to your brand and corporate colors',
        'payment_systems' => 'Payment systems LiqPay, Stripe, Fondy, PayPal, WayForPay',
        'integrations' => 'Integrations CRM, chatbots, distribution channels, Telegram',
        'timeline' => 'Timeline from 7 to 25 working days',
    ],

    'lt' => [
        'title' => 'Booking CMS 2025 — Internetinis kambarių rezervavimo skriptas',
        'hero_h1' => 'Paleiskite savo rezervacijos svetainę per 1 dieną',
        'hero_sub' => 'Be Booking.com ir Airbnb komisinių • Pilna kontrolė • 5 kalbos • Vienkartinis mokėjimas + 365 nemokami atnaujinimai',
        'cta_order' => 'Užsisakyti per 1 dieną',
        'cta_contact' => 'Rašyti kūrėjui',
        'cta_individual' => 'Aptarti individualią kūrimą',
        'features_title' => 'Kodėl renkasi Booking CMS 2025 metais?',
        'individual_title' => 'Individualus kūrimas pagal jūsų verslą',
        'individual_desc' => 'Netinka standartinis sprendimas? Užsisakykite unikalų rezervacijos tinklalapį, pritaikytą jūsų prekės ženklui, viešbučiui ar automobilių nuomai.',
        'popular_cities_title' => 'Kur mūsų skriptas jau veikia arba labai reikalingas',
        'github_text' => 'Peržiūrėti kodą GitHub',
        'year_free_updates' => 'Vienkartinis mokėjimas + 365 nemokamų atnaujinimų dienos',
        'shop_button' => 'Internetinė parduotuvė',
        'booking_button' => 'Rezervacijos sistema',

        'no_commissions_title' => 'Be komisinių',
        'no_commissions_desc'  => 'Visi pinigai iš rezervacijų — tik jums',
        'five_languages_title' => '5 pilnos kalbos',
        'five_languages_desc'  => 'Ukrainiečių, rusų, anglų, lietuvių, norvegų',
        'one_time_payment_title' => 'Vienkartinis mokėjimas + 365 nemokamų atnaujinimų',
        'one_time_payment_desc'  => 'Vienas mokėjimas — metų nemokami atnaujinimai',

        'unique_design' => 'Unikalus dizainas pagal jūsų prekės ženklą ir korporacines spalvas',
        'payment_systems' => 'Mokėjimo sistemos LiqPay, Stripe, Fondy, PayPal, WayForPay',
        'integrations' => 'Integracijos CRM, pokalbių robotai, platinimo kanalai, Telegram',
        'timeline' => 'Terminai nuo 7 iki 25 darbo dienų',
    ],

    'no' => [
        'title' => 'Booking CMS 2025 — Online bestillingsskript for hoteller og leiligheter',
        'hero_h1' => 'Start din egen bestillingsnettside på 1 dag',
        'hero_sub' => 'Ingen Booking.com / Airbnb provisjon • Full kontroll • 5 språk • Engangsbetaling + 365 dager gratis oppdateringer',
        'cta_order' => 'Bestill på 1 dag',
        'cta_contact' => 'Kontakt utvikler',
        'cta_individual' => 'Diskuter skreddersydd utvikling',
        'features_title' => 'Hvorfor velger man Booking CMS i 2025?',
        'individual_title' => 'Skreddersydd utvikling for din bedrift',
        'individual_desc' => 'Trenger du noe unikt? Bestill en helt tilpasset bestillingsnettside for ditt hotell, villa eller bilutleie.',
        'popular_cities_title' => 'Hvor skriptet vårt allerede fungerer eller er svært etterspurt',
        'github_text' => 'Se kildekoden på GitHub',
        'year_free_updates' => 'Engangsbetaling + 365 dager med gratis oppdateringer',
        'shop_button' => 'Nettbutikk',
        'booking_button' => 'Bestillingssystem',

        'no_commissions_title' => 'Ingen provisjoner',
        'no_commissions_desc'  => 'Alle penger fra bestillinger — kun dine',
        'five_languages_title' => '5 fullverdige språk',
        'five_languages_desc'  => 'Ukrainsk, russisk, engelsk, litauisk, norsk',
        'one_time_payment_title' => 'Engangsbetaling + 365 dager gratis oppdateringer',
        'one_time_payment_desc'  => 'Én betaling — et år med gratis oppdateringer',

        'unique_design' => 'Unik design tilpasset ditt merke og firmarfarger',
        'payment_systems' => 'Betalingssystemer LiqPay, Stripe, Fondy, PayPal, WayForPay',
        'integrations' => 'Integrasjoner CRM, chatboter, distribusjonskanaler, Telegram',
        'timeline' => 'Tidsramme fra 7 til 25 arbeidsdager',
    ]
];

$t = $translations[$lang];

// Список популярних міст (однаковий для всіх мов)
$popular_cities = ['Multilang', 'Берген', 'Oslo', 'Norway', 'Lithuanian', 'Ukrainian', ];
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>" data-lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= htmlspecialchars($t['title']) ?></title>
    <meta name="description" content="Готовий скрипт бронювання готелів та апартаментів 2025. Без комісій. 5 мов. Індивідуальна розробка. Осло, Берген, Вільнюс, Київ, Львів." />
    
    <!-- Open Graph + hreflang -->
    <meta property="og:title" content="<?= htmlspecialchars($t['title']) ?>">
    <meta property="og:description" content="Найшвидший спосіб запустити власну систему бронювання без посередників у 2025 році">
    <meta property="og:type" content="website">
    <?php foreach ($allowed_langs as $l): ?>
        <link rel="alternate" hreflang="<?= $l ?>" href="?lang=<?= $l ?>" />
    <?php endforeach; ?>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #1a2980;
            --primary-dark: #0f1a5a;
            --accent: #00d4ff;
            --light: #f8f9ff;
            --dark: #0a0f2c;
            --gray: #64748b;
            --success: #10b981;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Inter', sans-serif;
            color: #1f2937;
            background: #f9fafb;
            line-height: 1.6;
        }
        .container { max-width: 1280px; margin: 0 auto; padding: 0 1.5rem; }

        header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 5.5rem 0 4rem;
            text-align: center;
        }

        .hero h1 { font-size: clamp(2.6rem, 7vw, 4.5rem); margin-bottom: 1.2rem; }
        .hero .subtitle { font-size: 1.45rem; max-width: 820px; margin: 0 auto 2.8rem; opacity: 0.92; }

        .btn {
            display: inline-block;
            padding: 1rem 2.5rem;
            background: var(--accent);
            color: #0f172a;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s;
            margin: 0.6rem;
        }
        .btn:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,212,255,0.35); }
        .btn-outline { background: transparent; border: 2px solid white; color: white; }

        .lang-switcher {
            position: fixed;
            top: 1.8rem;
            right: 1.8rem;
            z-index: 1000;
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(12px);
            padding: 0.7rem 1.2rem;
            border-radius: 50px;
        }
        .lang-switcher a {
            color: white;
            margin: 0 0.5rem;
            text-decoration: none;
            font-weight: 500;
        }
        .lang-switcher a.active { font-weight: 700; }

        section { padding: 7rem 0; }
        h2 { text-align: center; font-size: 3rem; margin-bottom: 3.5rem; }

        .grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2.2rem; }

        .card {
            background: white;
            border-radius: 18px;
            padding: 2.2rem;
            box-shadow: 0 12px 35px rgba(0,0,0,0.09);
            transition: transform 0.35s;
        }
        .card:hover { transform: translateY(-12px); }

        .cities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 1.2rem;
        }
        .city-item {
            background: #f0f7ff;
            padding: 1.4rem;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            color: var(--primary);
            font-size: 1.25rem;
        }

        footer {
            background: var(--primary-dark);
            color: white;
            padding: 5rem 0 3rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            header { padding: 5rem 0 3.5rem; }
            h2 { font-size: 2.4rem; }
            .hero .subtitle { font-size: 1.25rem; }
        }
    </style>
</head>
<body>

    <!-- Перемикач мов -->
    <div class="lang-switcher">
        <?php foreach ($allowed_langs as $code): ?>
            <a href="?lang=<?= $code ?>" class="<?= $lang === $code ? 'active' : '' ?>">
                <?= strtoupper($code) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <header>
        <div class="container">
            <div class="hero">
                <h1><?= htmlspecialchars($t['hero_h1']) ?></h1>
                <p class="subtitle"><?= htmlspecialchars($t['hero_sub']) ?></p>

                <div>
                    <a href="mailto:rbilohash@gmail.com?subject=Замовлення%20Booking%20CMS" class="btn">
                        <?= htmlspecialchars($t['cta_order']) ?> →
                    </a>
                    <a href="https://t.me/+4746255885a" target="_blank" class="btn btn-outline">
                        <?= htmlspecialchars($t['cta_contact']) ?>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Індивідуальна розробка -->
    <section style="background:#f0f9ff;">
        <div class="container">
            <h2><?= htmlspecialchars($t['individual_title']) ?></h2>
            <p style="font-size:1.35rem; max-width:920px; margin:0 auto 3.5rem; text-align:center; line-height:1.6;">
                <?= htmlspecialchars($t['individual_desc']) ?>
            </p>

            <div class="grid-3">
                <div class="card"><strong><?= htmlspecialchars($t['unique_design']) ?></strong></div>
                <div class="card"><strong><?= htmlspecialchars($t['payment_systems']) ?></strong></div>
                <div class="card"><strong><?= htmlspecialchars($t['integrations']) ?></strong></div>
                <div class="card"><strong><?= htmlspecialchars($t['timeline']) ?></strong></div>
            </div>

            <div style="text-align:center; margin-top:3.5rem;">
                <a href="mailto:rbilohash@gmail.com?subject=Індивідуальна%20розробка%20Booking" class="btn" style="font-size:1.45rem; padding:1.3rem 3.2rem;">
                    <?= htmlspecialchars($t['cta_individual']) ?> →
                </a>
            </div>
        </div>
    </section>

    <!-- Переваги -->
    <section>
        <div class="container">
            <h2><?= htmlspecialchars($t['features_title']) ?></h2>

            <div class="grid-3">
                <div class="card">
                    <i class="fas fa-money-bill-wave fa-3x" style="color:#10b981; margin-bottom:1.2rem;"></i>
                    <h3><?= htmlspecialchars($t['no_commissions_title']) ?></h3>
                    <p><?= htmlspecialchars($t['no_commissions_desc']) ?></p>
                </div>

                <div class="card">
                    <i class="fas fa-globe fa-3x" style="color:#3b82f6; margin-bottom:1.2rem;"></i>
                    <h3><?= htmlspecialchars($t['five_languages_title']) ?></h3>
                    <p><?= htmlspecialchars($t['five_languages_desc']) ?></p>
                </div>

                <div class="card">
                    <i class="fas fa-tag fa-3x" style="color:#f59e0b; margin-bottom:1.2rem;"></i>
                    <h3><?= htmlspecialchars($t['one_time_payment_title']) ?></h3>
                    <p><?= htmlspecialchars($t['one_time_payment_desc']) ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Популярні міста -->
    <section style="background:#f8fafc;">
        <div class="container">
            <h2><?= htmlspecialchars($t['popular_cities_title']) ?></h2>

            <div class="cities-grid">
                <?php foreach ($popular_cities as $city): ?>
                    <div class="city-item"><?= htmlspecialchars($city) ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <p style="font-size:1.4rem; margin-bottom:2rem;">
                Booking CMS 2025 — система бронювання без посередників
            </p>

            <p style="margin:2rem 0;">
                <a href="https://github.com/Ruslan-Bilohash" target="_blank" style="color:#60a5fa; margin:0 2rem; font-size:1.3rem;">
                    <i class="fab fa-github"></i> <?= htmlspecialchars($t['github_text']) ?>
                </a>
                <a href="https://t.me/+4746255885a" target="_blank" style="color:#60a5fa; margin:0 2rem; font-size:1.3rem;">
                    <i class="fab fa-telegram-plane"></i> Telegram
                </a>
            </p>

            <p style="opacity:0.8; margin-top:3rem;">
                © <?= date('Y') ?> Booking CMS • Розроблено для реальних бізнесів • Повна власність
            </p>
        </div>
    </footer>

</body>
</html>