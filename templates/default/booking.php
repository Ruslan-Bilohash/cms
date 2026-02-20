<?php
session_start();

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'ua';
$allowed = ['ua','ru','en','lt','no'];
if (!in_array($lang, $allowed)) $lang = 'ua';
$_SESSION['lang'] = $lang;

/* КОРОТКО: для примера оставлены ключевые строки.
   Вставь СВОЙ полный массив переводов вместо этого блока —
   дизайн от этого не пострадает */
$translations = [
    'ua' => [
        'site_title' => 'Booking CMS — сучасна система бронювання 2025',
        'hero_h1' => 'Запустіть власний сайт бронювання за 1 день',
        'hero_sub' => 'Без комісій • 5 мов • Повний контроль • One-Time Payment',
        'cta_order' => 'Замовити',
        'cta_contact' => 'Звʼязатися',
        'features_title' => 'Чому Booking CMS',
        'faq_title' => 'FAQ'
    ],
    'ru' => [
        'site_title' => 'Booking CMS — современная система бронирования 2025',
        'hero_h1' => 'Запустите сайт бронирования за 1 день',
        'hero_sub' => 'Без комиссий • 5 языков • Полный контроль',
        'cta_order' => 'Заказать',
        'cta_contact' => 'Связаться',
        'features_title' => 'Почему Booking CMS',
        'faq_title' => 'FAQ'
    ],
    'en' => [
        'site_title' => 'Booking CMS — Modern Booking System 2025',
        'hero_h1' => 'Launch Your Booking Website in 1 Day',
        'hero_sub' => 'No commissions • 5 languages • Full control',
        'cta_order' => 'Order Now',
        'cta_contact' => 'Contact',
        'features_title' => 'Why Booking CMS',
        'faq_title' => 'FAQ'
    ],
    'lt' => [
        'site_title' => 'Booking CMS — Moderni rezervavimo sistema 2025',
        'hero_h1' => 'Paleiskite rezervavimo svetainę per 1 dieną',
        'hero_sub' => 'Be komisinių • 5 kalbos • Pilna kontrolė',
        'cta_order' => 'Užsisakyti',
        'cta_contact' => 'Kontaktai',
        'features_title' => 'Kodėl Booking CMS',
        'faq_title' => 'DUK'
    ],
    'no' => [
        'site_title' => 'Booking CMS — Moderne bookingsystem 2025',
        'hero_h1' => 'Start bookingside på 1 dag',
        'hero_sub' => 'Ingen provisjoner • 5 språk • Full kontroll',
        'cta_order' => 'Bestill',
        'cta_contact' => 'Kontakt',
        'features_title' => 'Hvorfor Booking CMS',
        'faq_title' => 'FAQ'
    ]
];

$t = $translations[$lang];
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($t['site_title']) ?></title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root{
    --primary:#2563eb;
    --accent:#22d3ee;
    --dark:#020617;
    --bg:#f8fafc;
    --glass:rgba(255,255,255,.65);
    --border:rgba(255,255,255,.35);
    --gradient:linear-gradient(135deg,#2563eb,#22d3ee);
}
*{box-sizing:border-box;margin:0;padding:0}
body{
    font-family:Inter,system-ui,sans-serif;
    background:var(--bg);
    color:#020617;
    line-height:1.7;
}
.container{
    max-width:1320px;
    margin:auto;
    padding:0 1.5rem;
}

/* HEADER */
header{
    position:sticky;
    top:0;
    z-index:100;
    backdrop-filter:blur(14px);
    background:rgba(255,255,255,.75);
    border-bottom:1px solid #e5e7eb;
}
.top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:1rem 0;
}
.logo{
    font-weight:800;
    font-size:1.2rem;
    background:var(--gradient);
    -webkit-background-clip:text;
    -webkit-text-fill-color:transparent;
}
.lang a{
    text-decoration:none;
    padding:.4rem .9rem;
    margin-left:.4rem;
    border-radius:999px;
    font-weight:500;
    color:#475569;
}
.lang a.active,
.lang a:hover{
    background:var(--gradient);
    color:#fff;
}

/* HERO */
.hero{
    position:relative;
    padding:9rem 0 7rem;
    background:
        radial-gradient(circle at 15% 20%,#38bdf8 0%,transparent 35%),
        radial-gradient(circle at 85% 30%,#6366f1 0%,transparent 40%),
        #020617;
    color:#fff;
    text-align:center;
    overflow:hidden;
}
.hero h1{
    font-size:clamp(2.8rem,6vw,4.8rem);
    line-height:1.05;
    margin-bottom:1.5rem;
}
.hero p{
    max-width:760px;
    margin:0 auto 3rem;
    font-size:1.35rem;
    color:#c7d2fe;
}
.hero-actions a{
    display:inline-block;
    padding:1.1rem 2.6rem;
    border-radius:999px;
    font-weight:600;
    text-decoration:none;
    margin:.5rem;
    transition:.35s;
}
.btn{
    background:var(--gradient);
    color:#fff;
    box-shadow:0 15px 40px rgba(37,99,235,.45);
}
.btn:hover{transform:translateY(-4px)}
.btn-glass{
    background:rgba(255,255,255,.15);
    border:1px solid var(--border);
    backdrop-filter:blur(12px);
    color:#fff;
}

/* FEATURES */
section{padding:7rem 0}
h2{
    text-align:center;
    font-size:3rem;
    margin-bottom:4rem;
}
.features{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
    gap:2.5rem;
}
.feature{
    background:var(--glass);
    backdrop-filter:blur(16px);
    border:1px solid var(--border);
    border-radius:22px;
    padding:2.5rem;
    transition:.4s;
}
.feature:hover{
    transform:translateY(-10px);
    box-shadow:0 25px 60px rgba(0,0,0,.15);
}
.feature h3{
    margin-bottom:1rem;
    color:var(--primary);
}

/* FAQ */
.faq{
    max-width:860px;
    margin:auto;
}
.faq details{
    background:#fff;
    border-radius:16px;
    padding:1.4rem 1.8rem;
    margin-bottom:1.2rem;
    box-shadow:0 8px 25px rgba(0,0,0,.08);
}
.faq summary{
    font-weight:600;
    cursor:pointer;
}

/* FOOTER */
footer{
    background:#020617;
    color:#94a3b8;
    padding:4rem 0 2rem;
    text-align:center;
}
footer b{color:#fff}

@media(max-width:768px){
    h2{font-size:2.3rem}
}
</style>
</head>
<body>

<header>
<div class="container top">
    <div class="logo">Booking CMS</div>
    <div class="lang">
        <?php foreach($allowed as $l): ?>
            <a href="?lang=<?= $l ?>" class="<?= $l===$lang?'active':'' ?>"><?= strtoupper($l) ?></a>
        <?php endforeach; ?>
    </div>
</div>
</header>

<section class="hero">
<div class="container">
    <h1><?= $t['hero_h1'] ?></h1>
    <p><?= $t['hero_sub'] ?></p>
    <div class="hero-actions">
        <a href="templates/default/booking2.php" class="btn"><?= $t['cta_order'] ?></a>
        <a href="#contact" class="btn-glass"><?= $t['cta_contact'] ?></a>
    </div>
</div>
</section>

<section>
<div class="container">
<h2><?= $t['features_title'] ?></h2>
<div class="features">
    <div class="feature"><h3>⚡ Fast Launch</h3><p>Ready in 24 hours with full branding.</p></div>
    <div class="feature"><h3>🌍 Multi-Language</h3><p>5 languages out of the box.</p></div>
    <div class="feature"><h3>💰 No Fees</h3><p>No Booking.com or Airbnb commissions.</p></div>
    <div class="feature"><h3>🔒 Secure</h3><p>PHP 8.2+, GDPR, CSRF, XSS protection.</p></div>
</div>
</div>
</section>

<section>
<div class="container faq">
<h2><?= $t['faq_title'] ?></h2>
<details>
    <summary>Скільки коштує?</summary>
    <p>Разова оплата, без підписок.</p>
</details>
<details>
    <summary>Чи можна змінювати код?</summary>
    <p>Так, ви отримуєте повний вихідний код.</p>
</details>
</div>
</section>

<footer>
<div class="container">
    <p><b>Booking CMS</b> © <?= date('Y') ?> • Modern SaaS Booking System</p>
</div>
</footer>

</body>
</html>
