<?php
// templates/default/booking_landing.php
// Повністю перекладена презентаційна сторінка Booking CMS — 26.12.2025

session_start();

$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'ua';
$allowed = ['ua', 'ru', 'en', 'lt', 'no'];
if (!in_array($lang, $allowed)) $lang = 'ua';
$_SESSION['lang'] = $lang;

// Повний масив перекладів — 5 мов
$translations = [
    'ua' => [
        'site_title' => 'Booking CMS — Скрипт бронювання номерів, апартаментів, авто та оголошень 2025',
        'hero_h1' => 'Запустіть власний сайт бронювання за 1 день',
        'hero_sub' => 'Без комісій Booking.com та Airbnb • 5 мов • Повний контроль • Мобільна версія • Разова оплата',
        'cta_order' => 'Замовити за 1 день',
        'cta_contact' => 'Написати розробнику',
        'cta_show_more' => 'Показати ще',
        'features_title' => 'Чому обирають саме Booking CMS?',
        'modules_title' => 'Потужні модулі в комплекті',
        'examples_title' => 'Реальні приклади — що можна запустити вже завтра',
        'faq_title' => 'Часті питання (FAQ)',
        'seo_title' => 'Booking CMS — Готовий скрипт бронювання для України, Росії, США, Литви, Норвегії',
        'seo_desc' => 'Найшвидший спосіб отримати повноцінний сайт бронювання без комісій. Ідеально для України, Росії, США, Литви, Норвегії. Підтримка 5 мов, сучасна адмін-панель, модулі новин, блогу, оренди авто та дошки оголошень. Запуск за 1 день!',
        'meta_keywords' => 'booking cms, скрипт бронювання, готовий сайт готелю, оренда авто, дошка оголошень, онлайн бронювання, booking script 2025, Україна, Росія, США, Литва, Норвегія',

        'module_rooms' => 'Бронювання номерів та апартаментів',
        'module_rooms_desc' => 'Календар вільних дат, галерея до 15 фото, різні категорії, сезонні ціни, автоматичні листи клієнтам, захист від дублювання бронювань, підтримка довгострокового бронювання.',

        'module_cars' => 'Оренда автомобілів',
        'module_cars_desc' => 'Класи авто, додаткові опції (GPS, дитяче крісло, повне страхування), перевірка водійських прав, сезонні тарифи, обмеження пробігу.',

        'module_classifieds' => 'Дошка оголошень / маркетплейс',
        'module_classifieds_desc' => 'Продаж, оренда, послуги, приватні та бізнес-оголошення, фільтри за регіоном/ціною/категорією, модерація, до 20 фото, платне підняття.',

        'module_news_blog' => 'Новини, блог, акції',
        'module_news_blog_desc' => 'SEO-оптимізовані статті, анонси знижок, фото, відео, теги, коментарі, RSS-стрічка, закріплення важливих новин на головній.',

        // Переваги
        'adv1_title' => 'Запуск за 1 день',
        'adv1_desc' => 'Повна установка + налаштування під ваш бренд, логотип, кольори, контакти — за 24 години.',
        'adv2_title' => '0% комісій посередникам',
        'adv2_desc' => 'Усі гроші від бронювань — тільки вам, без Booking.com, Airbnb, Hotels.com та інших платформ.',
        'adv3_title' => '5 повноцінних мов',
        'adv3_desc' => 'Українська, російська, англійська, литовська, норвезька — інтерфейс, email-повідомлення, помилки, все перекладено якісно.',
        'adv4_title' => 'Супершвидка мобільна версія',
        'adv4_desc' => 'Швидкість завантаження < 1.5 секунди, ідеально виглядає на будь-якому смартфоні (тестовано iPhone 16 / Galaxy S25 / Pixel 9).',
        'adv5_title' => 'Сучасна адмін-панель 2025',
        'adv5_desc' => 'Додавання/редагування номерів, цін, фото, бронювань, статистика, експорт в Excel, фільтри, масове завантаження фото.',
        'adv6_title' => 'Захист від овербукингу',
        'adv6_desc' => 'Автоматична перевірка перетинів дат + сповіщення в Telegram та email адміністратору в реальному часі.',
        'adv7_title' => 'Мульти-валюта та мови',
        'adv7_desc' => 'UAH, EUR, USD, PLN, NOK — валюта та мова автоматично підлаштовуються під відвідувача.',
        'adv8_title' => 'SEO-готовність з коробки',
        'adv8_desc' => 'Чисті URL, мета-теги, sitemap.xml, schema.org, швидке завантаження, підтримка Google Structured Data, robots.txt.',
        'adv9_title' => 'Галерея до 15 фото',
        'adv9_desc' => 'Красива карусель, лайтбокс, оптимізація зображень, lazy loading, водяні знаки, можливість додавання відео.',
        'adv10_title' => 'Форма бронювання з капчею',
        'adv10_desc' => 'Захист від спаму, автоматичне підтверджження бронювання на email клієнту та адміністратору, можливість додати поле "Коментар".',
        'adv11_title' => 'Повна статистика та звіти',
        'adv11_desc' => 'Графіки завантаження, топ-номери/апартаментів, експорт CSV/Excel/PDF, фільтри за датами/модулями/клієнтами.',
        'adv12_title' => 'Модуль новин та блогу',
        'adv12_desc' => 'Публікації акцій, новин готелю, SEO-тексти, коментарі, RSS, можливість закріплення важливих новин на головній сторінці.',
        'adv13_title' => 'Разова оплата — сайт назавжди ваш',
        'adv13_desc' => 'Ніякої оренди, підписки чи щомісячних платежів. Ви отримуєте повний вихідний код.',
        'adv14_title' => 'Техпідтримка 30 днів',
        'adv14_desc' => 'Безкоштовні відповіді на всі питання після покупки (email + Telegram + Discord).',
        'adv15_title' => 'Адаптація під 5 країн',
        'adv15_desc' => 'Україна, Росія, США, Литва, Норвегія — локалізовані тексти, валюти, особливості бронювання.',
        'adv16_title' => 'Оновлення 2025 року',
        'adv16_desc' => 'PHP 8.2+, сучасна безпека, захист від SQL-ін’єкцій, XSS, CSRF, GDPR-сумісність.',
        'adv17_title' => 'Інтеграція з Telegram',
        'adv17_desc' => 'Повідомлення про нові бронювання, запити, відміни — прямо в ваш Telegram-бот або канал.',
        'adv18_title' => 'Легке розширення',
        'adv18_desc' => 'Модульний код — додавайте нові функції (оплата LiqPay, WayForPay, Stripe, PayPal, крипта) без болю.',
        'adv19_title' => 'Автоматичні листи клієнтам',
        'adv19_desc' => 'Підтвердження бронювання, нагадування за добу, скасування, відгуки, промокоди — красивий HTML-шаблон.',
        'adv20_title' => 'Багатомовні email-шаблони',
        'adv20_desc' => 'Листи клієнтам та адміністратору надходять на мові відвідувача — повний переклад.',
        'adv21_title' => 'Гнучка система цін',
        'adv21_desc' => 'Сезонні ціни, знижки за довге бронювання, доплати за додаткових гостей/прибирання/тварин, промокоди.',
        'adv22_title' => 'Повний вихідний код',
        'adv22_desc' => 'Ви отримуєте весь код — змінюйте, адаптуйте, продавайте далі як хочете, без обмежень.',

        // FAQ
        'faq_q1' => 'Скільки коштує скрипт?',
        'faq_a1' => 'Разова оплата — ціна залежить від модулів (від 299$ до 799$). Ніяких щомісячних платежів.',
        'faq_q2' => 'Чи є гарантія?',
        'faq_a2' => 'Так — 30 днів повної технічної підтримки + 14 днів на повернення коштів.',
        'faq_q3' => 'Чи можна додати оплату онлайн?',
        'faq_a3' => 'Так — легко інтегрується LiqPay, WayForPay, Stripe, PayPal.',
        'faq_q4' => 'Чи підходить для кількох об’єктів?',
        'faq_a4' => 'Так — підтримує необмежену кількість номерів, апартаментів, авто.',
        'faq_q5' => 'Чи є оренда/підписка?',
        'faq_a5' => 'Ні — разова оплата, сайт стає вашим назавжди.',
        'faq_q6' => 'Чи можна продавати скрипт далі?',
        'faq_a6' => 'Так — отримуєте повний код, можете перепродавати з вашим брендингом.',
        'faq_q7' => 'Які країни найкраще підходять?',
        'faq_a7' => 'Україна, Росія, США, Литва, Норвегія — адаптовані мови, валюти та особливості.',
        'faq_q8' => 'Як отримати скрипт?',
        'faq_a8' => 'Після оплати — миттєве завантаження + інструкція + підтримка 30 днів.',

        // Форма
        'form_name' => 'Ваше ім\'я *',
        'form_contact' => 'Email або Telegram *',
        'form_message' => 'Що вас цікавить? (номери, апартаменти, авто, оголошення, блог, модулі...)',
        'form_captcha' => 'Скільки буде',
        'form_submit' => 'Відправити запит',
        'form_success' => 'Дякуємо! Повідомлення успішно відправлено. Ми зв’яжемося з вами протягом 1–3 годин.',
    ],

    'ru' => [
        'site_title' => 'Booking CMS — Скрипт бронирования номеров, апартаментов, авто и объявлений 2025',
        'hero_h1' => 'Запустите свой сайт бронирования за 1 день',
        'hero_sub' => 'Без комиссий Booking.com и Airbnb • 5 языков • Полный контроль • Мобильная версия • Разовая оплата',
        'cta_order' => 'Заказать за 1 день',
        'cta_contact' => 'Написать разработчику',
        'cta_show_more' => 'Показать еще',
        'features_title' => 'Почему выбирают именно Booking CMS?',
        'modules_title' => 'Мощные модули в комплекте',
        'examples_title' => 'Реальные примеры — что можно запустить завтра',
        'faq_title' => 'Частые вопросы (FAQ)',
        'seo_title' => 'Booking CMS — Готовый скрипт бронирования для Украины, России, США, Литвы, Норвегии',
        'seo_desc' => 'Самый быстрый способ получить полноценный сайт бронирования без комиссий. Идеально для Украины, России, США, Литвы, Норвегии. Поддержка 5 языков, современная админ-панель, модули новостей, блога, аренды авто и доски объявлений. Запуск за 1 день!',
        'meta_keywords' => 'booking cms, скрипт бронирования номеров, готовый сайт отеля, аренда авто php, доска объявлений, онлайн бронирование апартаментов, booking script 2025, Украина, Россия, США, Литва, Норвегия',

        'module_rooms' => 'Бронирование номеров и апартаментов',
        'module_rooms_desc' => 'Календарь свободных дат, галерея до 15 фото, разные категории, сезонные цены, автоматические письма клиентам, защита от овербукинга, поддержка долгосрочного бронирования.',

        'module_cars' => 'Аренда автомобилей',
        'module_cars_desc' => 'Классы авто, доп.опции (GPS, детское кресло, полная страховка), проверка документов, сезонные тарифы, ограничение пробега.',

        'module_classifieds' => 'Доска объявлений / маркетплейс',
        'module_classifieds_desc' => 'Продажа, аренда, услуги, частные и бизнес-объявления, фильтры по региону/цене/категории, модерация, до 20 фото, платное поднятие.',

        'module_news_blog' => 'Новости, блог, акции',
        'module_news_blog_desc' => 'SEO-оптимизированные статьи, анонсы скидок, фото, видео, теги, комментарии, RSS-лента, закрепление важных новостей на главной.',

        'adv1_title' => 'Запуск за 1 день',
        'adv1_desc' => 'Полная установка + настройка под ваш бренд, логотип, цвета, контакты — за 24 часа.',
        'adv2_title' => '0% комиссий посредникам',
        'adv2_desc' => 'Все деньги от бронирований — только вам, без Booking.com, Airbnb, Hotels.com и других платформ.',
        'adv3_title' => '5 полноценных языков',
        'adv3_desc' => 'Украинский, русский, английский, литовский, норвежский — интерфейс, email-уведомления, ошибки, всё переведено качественно.',
        'adv4_title' => 'Супербыстрая мобильная версия',
        'adv4_desc' => 'Скорость загрузки < 1.5 секунды, идеально выглядит на любом смартфоне (протестировано iPhone 16 / Galaxy S25 / Pixel 9).',
        'adv5_title' => 'Современная админ-панель 2025',
        'adv5_desc' => 'Добавление/редактирование номеров, цен, фото, бронирований, статистика, экспорт в Excel, фильтры, массовое загрузка фото.',
        'adv6_title' => 'Защита от овербукинга',
        'adv6_desc' => 'Автоматическая проверка пересечений дат + уведомления в Telegram и email администратору в реальном времени.',
        'adv7_title' => 'Мультивалюта и языки',
        'adv7_desc' => 'UAH, EUR, USD, PLN, NOK — валюта и язык автоматически подстраиваются под посетителя.',
        'adv8_title' => 'SEO-готовность из коробки',
        'adv8_desc' => 'Чистые URL, мета-теги, sitemap.xml, schema.org, быстрая загрузка, поддержка Google Structured Data, robots.txt.',
        'adv9_title' => 'Галерея до 15 фото',
        'adv9_desc' => 'Красивая карусель, лайтбокс, оптимизация изображений, lazy loading, водяные знаки, возможность добавления видео.',
        'adv10_title' => 'Форма бронирования с капчей',
        'adv10_desc' => 'Защита от спама, автоматическое подтверждение бронирования на email клиенту и администратору, поле "Комментарий".',
        'adv11_title' => 'Полная статистика и отчеты',
        'adv11_desc' => 'Графики загрузки, топ-номеров/апартаментов, экспорт CSV/Excel/PDF, фильтры по датам/модулям/клиентам.',
        'adv12_title' => 'Модуль новостей и блога',
        'adv12_desc' => 'Публикации акций, новостей отеля, SEO-тексты, комментарии, RSS, закрепление важных новостей на главной.',
        'adv13_title' => 'Разовая оплата — сайт навсегда ваш',
        'adv13_desc' => 'Никакой аренды, подписки или ежемесячных платежей. Полный исходный код.',
        'adv14_title' => 'Техподдержка 30 дней',
        'adv14_desc' => 'Бесплатные ответы на все вопросы после покупки (email + Telegram + Discord).',
        'adv15_title' => 'Адаптация под 5 стран',
        'adv15_desc' => 'Украина, Россия, США, Литва, Норвегия — локализованные тексты, валюты, особенности бронирования.',
        'adv16_title' => 'Обновления 2025 года',
        'adv16_desc' => 'PHP 8.2+, современная безопасность, защита от SQL-инъекций, XSS, CSRF, GDPR-совместимость.',
        'adv17_title' => 'Интеграция с Telegram',
        'adv17_desc' => 'Уведомления о новых бронированиях, запросах, отменах — прямо в ваш Telegram-бот или канал.',
        'adv18_title' => 'Легкое расширение',
        'adv18_desc' => 'Модульный код — добавляйте оплату LiqPay, WayForPay, Stripe, PayPal, крипту без проблем.',
        'adv19_title' => 'Автоматические письма клиентам',
        'adv19_desc' => 'Подтверждение бронирования, напоминания за сутки, отмена, отзывы, промокоды — красивый HTML-шаблон.',
        'adv20_title' => 'Многоязычные email-шаблоны',
        'adv20_desc' => 'Письма клиентам и администратору приходят на языке посетителя — полный перевод.',
        'adv21_title' => 'Гибкая система цен',
        'adv21_desc' => 'Сезонные цены, скидки за длительное бронирование, доплаты за гостей/уборку/животных, промокоды.',
        'adv22_title' => 'Полный исходный код',
        'adv22_desc' => 'Вы получаете весь код — меняйте, адаптируйте, продавайте дальше как хотите, без ограничений.',

        'faq_q1' => 'Сколько стоит скрипт?',
        'faq_a1' => 'Разовая оплата — цена зависит от модулей (от 299$ до 799$). Никаких ежемесячных платежей.',
        'faq_q2' => 'Есть ли гарантия?',
        'faq_a2' => 'Да — 30 дней полной технической поддержки + 14 дней на возврат средств.',
        'faq_q3' => 'Можно ли добавить онлайн-оплату?',
        'faq_a3' => 'Да — легко интегрируется LiqPay, WayForPay, Stripe, PayPal.',
        'faq_q4' => 'Подходит ли для нескольких объектов?',
        'faq_a4' => 'Да — поддерживает неограниченное количество номеров, апартаментов, авто.',
        'faq_q5' => 'Есть ли аренда/подписка?',
        'faq_a5' => 'Нет — разовая оплата, сайт становится вашим навсегда.',
        'faq_q6' => 'Можно ли продавать скрипт дальше?',
        'faq_a6' => 'Да — получаете полный код, можете перепродавать с вашим брендингом.',
        'faq_q7' => 'Какие страны лучше всего подходят?',
        'faq_a7' => 'Украина, Россия, США, Литва, Норвегия — адаптированные языки, валюты и особенности.',
        'faq_q8' => 'Как получить скрипт?',
        'faq_a8' => 'После оплаты — мгновенная загрузка + инструкция + поддержка 30 дней.',

        'form_name' => 'Ваше имя *',
        'form_contact' => 'Email или Telegram *',
        'form_message' => 'Что вас интересует? (номера, апартаменты, авто, объявления, блог, модули...)',
        'form_captcha' => 'Сколько будет',
        'form_submit' => 'Отправить запрос',
        'form_success' => 'Спасибо! Сообщение успешно отправлено. Мы свяжемся с вами в течение 1–3 часов.',
    ],

    'en' => [
        'site_title' => 'Booking CMS — Booking Script for Rooms, Apartments, Cars & Classifieds 2025',
        'hero_h1' => 'Launch Your Own Booking Website in 1 Day',
        'hero_sub' => 'No Booking.com or Airbnb commissions • 5 Languages • Full Control • Mobile-First • One-Time Payment',
        'cta_order' => 'Order in 1 Day',
        'cta_contact' => 'Contact Developer',
        'cta_show_more' => 'Show More',
        'features_title' => 'Why Choose Booking CMS?',
        'modules_title' => 'Powerful Modules Included',
        'examples_title' => 'Real Examples — What You Can Launch Tomorrow',
        'faq_title' => 'Frequently Asked Questions (FAQ)',
        'seo_title' => 'Booking CMS — Ready Booking Script for Ukraine, Russia, USA, Lithuania, Norway',
        'seo_desc' => 'The fastest way to get a full-featured booking website with zero commissions. Perfect for Ukraine, Russia, USA, Lithuania, Norway. Supports 5 languages, modern admin panel, news/blog, car rental, and classifieds modules. Launch in 1 day!',
        'meta_keywords' => 'booking cms, booking script, hotel booking script, car rental php, classifieds script, online booking, booking script 2025, Ukraine, Russia, USA, Lithuania, Norway',

        'module_rooms' => 'Room & Apartment Booking',
        'module_rooms_desc' => 'Availability calendar, gallery up to 15 photos, multiple categories, seasonal pricing, auto client emails, overbooking protection, long-term booking support.',

        'module_cars' => 'Car Rental',
        'module_cars_desc' => 'Car classes, extra options (GPS, child seat, full insurance), driver license check, seasonal rates, mileage limits.',

        'module_classifieds' => 'Classifieds / Marketplace',
        'module_classifieds_desc' => 'Sales, rentals, services, private & business ads, filters by region/price/category, moderation, up to 20 photos, paid promotion.',

        'module_news_blog' => 'News, Blog, Promotions',
        'module_news_blog_desc' => 'SEO-optimized articles, discount announcements, photos, videos, tags, comments, RSS-feed, pinning important news on homepage.',

        'adv1_title' => 'Launch in 1 Day',
        'adv1_desc' => 'Full installation + branding setup (logo, colors, contacts) — within 24 hours.',
        'adv2_title' => '0% Commissions to Intermediaries',
        'adv2_desc' => 'All booking money goes directly to you — no Booking.com, Airbnb, Hotels.com or other platforms.',
        'adv3_title' => '5 Full Languages',
        'adv3_desc' => 'Ukrainian, Russian, English, Lithuanian, Norwegian — interface, emails, errors, everything fully translated.',
        'adv4_title' => 'Super-Fast Mobile Version',
        'adv4_desc' => 'Loading speed < 1.5 sec, perfect on any smartphone (tested iPhone 16 / Galaxy S25 / Pixel 9).',
        'adv5_title' => 'Modern Admin Panel 2025',
        'adv5_desc' => 'Add/edit rooms, prices, photos, bookings, statistics, Excel export, filters, bulk photo upload.',
        'adv6_title' => 'Overbooking Protection',
        'adv6_desc' => 'Automatic date overlap check + real-time notifications to Telegram & email admin.',
        'adv7_title' => 'Multi-Currency & Multi-Language',
        'adv7_desc' => 'UAH, EUR, USD, PLN, NOK — currency and language auto-adapt to visitor.',
        'adv8_title' => 'SEO-Ready Out of the Box',
        'adv8_desc' => 'Clean URLs, meta tags, sitemap.xml, schema.org, fast loading, Google Structured Data, robots.txt.',
        'adv9_title' => 'Gallery up to 15 Photos',
        'adv9_desc' => 'Beautiful carousel, lightbox, image optimization, lazy loading, watermarks, video support.',
        'adv10_title' => 'Booking Form with Captcha',
        'adv10_desc' => 'Spam protection, auto booking confirmation emails to client & admin, "Comment" field option.',
        'adv11_title' => 'Full Statistics & Reports',
        'adv11_desc' => 'Occupancy charts, top rooms/apartments, export CSV/Excel/PDF, date/module/client filters.',
        'adv12_title' => 'News & Blog Module',
        'adv12_desc' => 'Promotions, hotel news, SEO texts, comments, RSS, pin important news to homepage.',
        'adv13_title' => 'One-Time Payment — Yours Forever',
        'adv13_desc' => 'No rent, subscription or monthly fees. Full source code included.',
        'adv14_title' => '30 Days Technical Support',
        'adv14_desc' => 'Free answers to all questions after purchase (email + Telegram + Discord).',
        'adv15_title' => 'Adapted for 5 Countries',
        'adv15_desc' => 'Ukraine, Russia, USA, Lithuania, Norway — localized texts, currencies, booking specifics.',
        'adv16_title' => '2025 Updates',
        'adv16_desc' => 'PHP 8.2+, modern security, protection against SQLi, XSS, CSRF, GDPR compliant.',
        'adv17_title' => 'Telegram Integration',
        'adv17_desc' => 'New booking, request, cancellation notifications — directly to your Telegram bot/channel.',
        'adv18_title' => 'Easy to Extend',
        'adv18_desc' => 'Modular code — add LiqPay, WayForPay, Stripe, PayPal, crypto payments easily.',
        'adv19_title' => 'Automatic Client Emails',
        'adv19_desc' => 'Booking confirmation, 24h reminder, cancellation, review request, promo codes — nice HTML template.',
        'adv20_title' => 'Multilingual Email Templates',
        'adv20_desc' => 'Client & admin emails sent in visitor’s language — fully translated.',
        'adv21_title' => 'Flexible Pricing System',
        'adv21_desc' => 'Seasonal prices, long-stay discounts, extra guest/cleaning/pet fees, promo codes.',
        'adv22_title' => 'Full Source Code',
        'adv22_desc' => 'You get all the code — modify, adapt, resell as you wish, no restrictions.',

        'faq_q1' => 'How much does the script cost?',
        'faq_a1' => 'One-time payment — price depends on modules (from $299 to $799). No monthly fees.',
        'faq_q2' => 'Is there a guarantee?',
        'faq_a2' => 'Yes — 30 days full technical support + 14 days money-back.',
        'faq_q3' => 'Can I add online payment?',
        'faq_a3' => 'Yes — easy integration with LiqPay, WayForPay, Stripe, PayPal.',
        'faq_q4' => 'Suitable for multiple properties?',
        'faq_a4' => 'Yes — supports unlimited rooms, apartments, cars.',
        'faq_q5' => 'Is there subscription/rental?',
        'faq_a5' => 'No — one-time payment, the site is yours forever.',
        'faq_q6' => 'Can I resell the script?',
        'faq_a6' => 'Yes — full source code, you can resell with your branding.',
        'faq_q7' => 'Which countries fit best?',
        'faq_a7' => 'Ukraine, Russia, USA, Lithuania, Norway — adapted languages, currencies and features.',
        'faq_q8' => 'How do I get the script?',
        'faq_a8' => 'After payment — instant download + instructions + 30 days support.',

        'form_name' => 'Your Name *',
        'form_contact' => 'Email or Telegram *',
        'form_message' => 'What interests you? (rooms, apartments, cars, classifieds, blog, modules...)',
        'form_captcha' => 'How much is',
        'form_submit' => 'Send Request',
        'form_success' => 'Thank you! Message sent successfully. We will contact you within 1–3 hours.',
    ],

    'lt' => [
        'site_title' => 'Booking CMS — Rezervacijų scenarijus kambariams, apartamentams, automobiliams ir skelbimams 2025',
        'hero_h1' => 'Paleiskite savo rezervacijų svetainę per 1 dieną',
        'hero_sub' => 'Be Booking.com ir Airbnb komisinių • 5 kalbos • Pilna kontrolė • Mobilioji versija • Vienkartinis mokėjimas',
        'cta_order' => 'Užsisakyti per 1 dieną',
        'cta_contact' => 'Rašyti kūrėjui',
        'cta_show_more' => 'Rodyti daugiau',
        'features_title' => 'Kodėl renkasi būtent Booking CMS?',
        'modules_title' => 'Galingi moduliai komplekte',
        'examples_title' => 'Tikri pavyzdžiai — ką galite paleisti jau rytoj',
        'faq_title' => 'Dažniausiai užduodami klausimai (DUK)',
        'seo_title' => 'Booking CMS — Paruoštas rezervacijų scenarijus Ukrainai, Rusijai, JAV, Lietuvai, Norvegijai',
        'seo_desc' => 'Greičiausias būdas gauti pilnavertę rezervacijų svetainę be komisinių. Idealiai tinka Ukrainai, Rusijai, JAV, Lietuvai, Norvegijai. Palaikoma 5 kalbos, moderni admino panelė, naujienų, tinklaraščio, automobilių nuomos ir skelbimų moduliai. Paleidimas per 1 dieną!',
        'meta_keywords' => 'booking cms, rezervacijų scenarijus, paruošta viešbučio svetainė, automobilių nuoma php, skelbimų lenta, online rezervacijos, booking script 2025, Ukraina, Rusija, JAV, Lietuva, Norvegija',

        'module_rooms' => 'Kambarių ir apartamentų rezervacija',
        'module_rooms_desc' => 'Laisvų datų kalendorius, galerija iki 15 nuotraukų, skirtingos kategorijos, sezoninės kainos, automatiniai laiškai klientams, apsauga nuo perteklinio rezervavimo, ilgalaikės rezervacijos palaikymas.',

        'module_cars' => 'Automobilių nuoma',
        'module_cars_desc' => 'Automobilių klasės, papildomos paslaugos (GPS, vaikiška kėdutė, pilnas draudimas), vairuotojo pažymėjimo patikrinimas, sezoniniai tarifai, ridos ribojimas.',

        'module_classifieds' => 'Skelbimų lenta / Marketplace',
        'module_classifieds_desc' => 'Pardavimas, nuoma, paslaugos, privatūs ir verslo skelbimai, filtrai pagal regioną/kainą/kategoriją, moderavimas, iki 20 nuotraukų, mokamas pakėlimas.',

        'module_news_blog' => 'Naujienos, tinklaraštis, akcijos',
        'module_news_blog_desc' => 'SEO optimizuoti straipsniai, nuolaidų anonssai, nuotraukos, video, žymės, komentarai, RSS srautas, svarbių naujienų prisegimas prie pagrindinio puslapio.',

        'adv1_title' => 'Paleidimas per 1 dieną',
        'adv1_desc' => 'Pilnas įdiegimas + pritaikymas pagal jūsų prekės ženklą, logotipą, spalvas, kontaktus — per 24 valandas.',
        'adv2_title' => '0% komisinių tarpininkams',
        'adv2_desc' => 'Visi pinigai nuo rezervacijų — tik jums, be Booking.com, Airbnb, Hotels.com ir kitų platformų.',
        'adv3_title' => '5 pilnavertės kalbos',
        'adv3_desc' => 'Ukrainiečių, rusų, anglų, lietuvių, norvegų — sąsaja, el. laiškai, klaidos, viskas kokybiškai išversta.',
        'adv4_title' => 'Itin greita mobilioji versija',
        'adv4_desc' => 'Įkrovimo greitis < 1,5 sek., puikiai atrodo bet kuriame išmaniajame telefone (testuota iPhone 16 / Galaxy S25 / Pixel 9).',
        'adv5_title' => 'Moderni admino panelė 2025',
        'adv5_desc' => 'Kambarių/pridėjimas/redagavimas, kainos, nuotraukos, rezervacijos, statistika, eksportas į Excel, filtrai, masinis nuotraukų įkėlimas.',
        'adv6_title' => 'Apsauga nuo perteklinio rezervavimo',
        'adv6_desc' => 'Automatinis datų persidengimų tikrinimas + realaus laiko pranešimai Telegram ir el. paštu administratoriui.',
        'adv7_title' => 'Kelių valiutų ir kalbų palaikymas',
        'adv7_desc' => 'UAH, EUR, USD, PLN, NOK — valiuta ir kalba automatiškai prisitaiko prie lankytojo.',
        'adv8_title' => 'SEO paruošta iš karto',
        'adv8_desc' => 'Švarūs URL, meta žymės, sitemap.xml, schema.org, greitas įkrovimas, Google Structured Data, robots.txt.',
        'adv9_title' => 'Galerija iki 15 nuotraukų',
        'adv9_desc' => 'Gražus karuselė, šviesos dėžutė, vaizdų optimizacija, lazy loading, vandens ženklai, video galimybė.',
        'adv10_title' => 'Rezervacijos forma su captcha',
        'adv10_desc' => 'Apsauga nuo šlamšto, automatinis patvirtinimas el. paštu klientui ir administratoriui, laukas „Komentaras“.',
        'adv11_title' => 'Pilna statistika ir ataskaitos',
        'adv11_desc' => 'Užimtumo grafikai, populiariausi kambariai/apartamentai, eksportas CSV/Excel/PDF, filtrai pagal datas/modulius/klientus.',
        'adv12_title' => 'Naujienų ir tinklaraščio modulis',
        'adv12_desc' => 'Akcijų publikacijos, viešbučio naujienos, SEO tekstai, komentarai, RSS, svarbių naujienų prisegimas prie pagrindinio puslapio.',
        'adv13_title' => 'Vienkartinis mokėjimas — svetainė visam laikui jūsų',
        'adv13_desc' => 'Jokios nuomos, prenumeratos ar mėnesinių mokesčių. Pilnas šaltinio kodas.',
        'adv14_title' => '30 dienų techninė pagalba',
        'adv14_desc' => 'Nemokami atsakymai į visus klausimus po pirkimo (el. paštas + Telegram + Discord).',
        'adv15_title' => 'Pritaikyta 5 šalims',
        'adv15_desc' => 'Ukraina, Rusija, JAV, Lietuva, Norvegija — lokalizuoti tekstai, valiutos, rezervacijos ypatumai.',
        'adv16_title' => '2025 metų atnaujinimai',
        'adv16_desc' => 'PHP 8.2+, modernus saugumas, apsauga nuo SQL injekcijų, XSS, CSRF, GDPR atitiktis.',
        'adv17_title' => 'Integracija su Telegram',
        'adv17_desc' => 'Pranešimai apie naujas rezervacijas, užklausas, atšaukimus — tiesiai į jūsų Telegram botą ar kanalą.',
        'adv18_title' => 'Lengvas plėtimasis',
        'adv18_desc' => 'Modulinis kodas — lengvai pridėkite LiqPay, WayForPay, Stripe, PayPal, kriptovaliutų mokėjimus.',
        'adv19_title' => 'Automatiniai laiškai klientams',
        'adv19_desc' => 'Rezervacijos patvirtinimas, priminimas prieš parą, atšaukimas, atsiliepimai, promo kodai — gražus HTML šablonas.',
        'adv20_title' => 'Daugiakalbiai el. laiškų šablonai',
        'adv20_desc' => 'Laiškai klientams ir administratoriui siunčiami lankytojo kalba — pilnas vertimas.',
        'adv21_title' => 'Lanksti kainodaros sistema',
        'adv21_desc' => 'Sezoninės kainos, nuolaidos ilgalaikėms rezervacijoms, доплата už papildomus svečius/valymą/gyvūnus, promo kodai.',
        'adv22_title' => 'Pilnas šaltinio kodas',
        'adv22_desc' => 'Gaunate visą kodą — keiskite, pritaikykite, perparduokite kaip norite, be apribojimų.',

        'faq_q1' => 'Kiek kainuoja scenarijus?',
        'faq_a1' => 'Vienkartinis mokėjimas — kaina priklauso nuo modulių (nuo 299$ iki 799$). Jokių mėnesinių mokesčių.',
        'faq_q2' => 'Ar yra garantija?',
        'faq_a2' => 'Taip — 30 dienų pilna techninė pagalba + 14 dienų pinigų grąžinimas.',
        'faq_q3' => 'Ar galima pridėti internetinį mokėjimą?',
        'faq_a3' => 'Taip — lengvai integruojami LiqPay, WayForPay, Stripe, PayPal.',
        'faq_q4' => 'Tinka kelioms objektų?',
        'faq_a4' => 'Taip — palaiko neribotą kiekį kambarių, apartamentų, automobilių.',
        'faq_q5' => 'Ar yra nuoma/prenumerata?',
        'faq_a5' => 'Ne — vienkartinis mokėjimas, svetainė jūsų visam laikui.',
        'faq_q6' => 'Ar galima perparduoti scenarijų?',
        'faq_a6' => 'Taip — gaunate pilną kodą, galite perparduoti su savo prekės ženklu.',
        'faq_q7' => 'Kurioms šalims geriausiai tinka?',
        'faq_a7' => 'Ukraina, Rusija, JAV, Lietuva, Norvegija — pritaikytos kalbos, valiutos ir ypatumai.',
        'faq_q8' => 'Kaip gauti scenarijų?',
        'faq_a8' => 'Po apmokėjimo — momentinis atsisiuntimas + instrukcija + 30 dienų pagalba.',

        'form_name' => 'Jūsų vardas *',
        'form_contact' => 'El. paštas arba Telegram *',
        'form_message' => 'Kas jus domina? (kambariai, apartamentai, automobiliai, skelbimai, tinklaraštis, moduliai...)',
        'form_captcha' => 'Kiek bus',
        'form_submit' => 'Siųsti užklausą',
        'form_success' => 'Ačiū! Žinutė sėkmingai išsiųsta. Susisieksime su jumis per 1–3 valandas.',
    ],

    'no' => [
        'site_title' => 'Booking CMS — Bookingsskript for rom, leiligheter, biler og annonser 2025',
        'hero_h1' => 'Start din egen bookingside på 1 dag',
        'hero_sub' => 'Ingen Booking.com eller Airbnb-provisjoner • 5 språk • Full kontroll • Mobilvennlig • Engangsbetaling',
        'cta_order' => 'Bestill på 1 dag',
        'cta_contact' => 'Kontakt utvikler',
        'cta_show_more' => 'Vis mer',
        'features_title' => 'Hvorfor velge Booking CMS?',
        'modules_title' => 'Kraftige moduler inkludert',
        'examples_title' => 'Ekte eksempler — hva du kan starte i morgen',
        'faq_title' => 'Ofte stilte spørsmål (FAQ)',
        'seo_title' => 'Booking CMS — Ferdig bookingsskript for Ukraina, Russland, USA, Litauen, Norge',
        'seo_desc' => 'Raskeste måten å få en fullverdig bookingside uten provisjoner. Perfekt for Ukraina, Russland, USA, Litauen, Norge. Støtter 5 språk, moderne adminpanel, nyheter/blogg, bilutleie og annonse-moduler. Start på 1 dag!',
        'meta_keywords' => 'booking cms, bookingsskript, hotellbooking script, bilutleie php, klassifiserte annonser, online booking, booking script 2025, Ukraina, Russland, USA, Litauen, Norge',

        'module_rooms' => 'Rom- og leilighetsbooking',
        'module_rooms_desc' => 'Tilgjengelighetskalender, galleri opptil 15 bilder, ulike kategorier, sesongpriser, automatiske e-poster til kunder, beskyttelse mot overbooking, støtte for langtidsbooking.',

        'module_cars' => 'Bilutleie',
        'module_cars_desc' => 'Bilklasser, ekstrautstyr (GPS, barnesete, full forsikring), sjåførkortkontroll, sesongtariffer, kilometerbegrensning.',

        'module_classifieds' => 'Annonser / Marketplace',
        'module_classifieds_desc' => 'Salg, utleie, tjenester, private og bedriftsannonser, filtre etter region/pris/kategori, moderering, opptil 20 bilder, betalt opphøyning.',

        'module_news_blog' => 'Nyheter, blogg, kampanjer',
        'module_news_blog_desc' => 'SEO-optimaliserte artikler, rabattannonser, bilder, video, tagger, kommentarer, RSS-feed, feste av viktige nyheter på forsiden.',

        'adv1_title' => 'Start på 1 dag',
        'adv1_desc' => 'Full installasjon + tilpasning til ditt merke, logo, farger, kontakter — innen 24 timer.',
        'adv2_title' => '0% provisjon til mellomledd',
        'adv2_desc' => 'Alle penger fra bookinger går direkte til deg — ingen Booking.com, Airbnb eller andre plattformer.',
        'adv3_title' => '5 fullverdige språk',
        'adv3_desc' => 'Ukrainsk, russisk, engelsk, litauisk, norsk — grensesnitt, e-poster, feilmeldinger, alt er kvalitetsoverført.',
        'adv4_title' => 'Superrask mobilversjon',
        'adv4_desc' => 'Lastetid < 1,5 sek, perfekt på alle smarttelefoner (testet iPhone 16 / Galaxy S25 / Pixel 9).',
        'adv5_title' => 'Moderne adminpanel 2025',
        'adv5_desc' => 'Legge til/redigere rom, priser, bilder, bookinger, statistikk, Excel-eksport, filtre, masseopplasting av bilder.',
        'adv6_title' => 'Beskyttelse mot overbooking',
        'adv6_desc' => 'Automatisk sjekk av datooverlapp + sanntidsvarsler til Telegram og e-post til admin.',
        'adv7_title' => 'Flere valutaer og språk',
        'adv7_desc' => 'UAH, EUR, USD, PLN, NOK — valuta og språk tilpasses automatisk besøkende.',
        'adv8_title' => 'SEO-klar fra start',
        'adv8_desc' => 'Rene URL-er, meta-tagger, sitemap.xml, schema.org, rask lasting, Google Structured Data, robots.txt.',
        'adv9_title' => 'Galleri opptil 15 bilder',
        'adv9_desc' => 'Fin karusell, lightbox, bildeoptimalisering, lazy loading, vannmerker, videostøtte.',
        'adv10_title' => 'Bookingsskjema med captcha',
        'adv10_desc' => 'Spam-beskyttelse, automatisk bekreftelse på e-post til kunde og admin, valgfritt "Kommentar"-felt.',
        'adv11_title' => 'Full statistikk og rapporter',
        'adv11_desc' => 'Belastningsgrafer, top-rom/leiligheter, eksport CSV/Excel/PDF, filtre etter dato/modul/klient.',
        'adv12_title' => 'Nyheter og bloggmodul',
        'adv12_desc' => 'Kampanjer, hotellnyheter, SEO-tekster, kommentarer, RSS, feste viktige nyheter på forsiden.',
        'adv13_title' => 'Engangsbetaling — siden er din for alltid',
        'adv13_desc' => 'Ingen leie, abonnement eller månedlige avgifter. Full kildekode inkludert.',
        'adv14_title' => '30 dagers teknisk support',
        'adv14_desc' => 'Gratis svar på alle spørsmål etter kjøp (e-post + Telegram + Discord).',
        'adv15_title' => 'Tilpasset 5 land',
        'adv15_desc' => 'Ukraina, Russland, USA, Litauen, Norge — lokaliserte tekster, valutaer og bookingspesifikasjoner.',
        'adv16_title' => '2025-oppdateringer',
        'adv16_desc' => 'PHP 8.2+, moderne sikkerhet, beskyttelse mot SQLi, XSS, CSRF, GDPR-kompatibel.',
        'adv17_title' => 'Telegram-integrasjon',
        'adv17_desc' => 'Varsler om nye bookinger, forespørsler, avbestillinger — direkte til din Telegram-bot/kanal.',
        'adv18_title' => 'Lett å utvide',
        'adv18_desc' => 'Modulær kode — enkelt legge til LiqPay, WayForPay, Stripe, PayPal, kryptobetalinger.',
        'adv19_title' => 'Automatiske e-poster til kunder',
        'adv19_desc' => 'Bookingbekreftelse, 24t påminnelse, avbestilling, omtale-forespørsel, kampanjekoder — pent HTML-mal.',
        'adv20_title' => 'Flerspråklige e-postmaler',
        'adv20_desc' => 'E-poster til kunde og admin sendes på besøkendes språk — full oversettelse.',
        'adv21_title' => 'Fleksibelt prissystem',
        'adv21_desc' => 'Sesongpriser, rabatter for langtidsbooking, tillegg for ekstra gjester/rengjøring/dyr, kampanjekoder.',
        'adv22_title' => 'Full kildekode',
        'adv22_desc' => 'Du får hele koden — endre, tilpasse, videreselge som du vil, uten begrensninger.',

        'faq_q1' => 'Hvor mye koster skriptet?',
        'faq_a1' => 'Engangsbetaling — pris avhenger av moduler (fra 299$ til 799$). Ingen månedlige avgifter.',
        'faq_q2' => 'Er det garanti?',
        'faq_a2' => 'Ja — 30 dager full teknisk support + 14 dager pengene-tilbake.',
        'faq_q3' => 'Kan jeg legge til online betaling?',
        'faq_a3' => 'Ja — enkel integrasjon med LiqPay, WayForPay, Stripe, PayPal.',
        'faq_q4' => 'Passer for flere eiendommer?',
        'faq_a4' => 'Ja — støtter ubegrenset antall rom, leiligheter, biler.',
        'faq_q5' => 'Er det abonnement/leie?',
        'faq_a5' => 'Nei — engangsbetaling, siden er din for alltid.',
        'faq_q6' => 'Kan jeg videreselge skriptet?',
        'faq_a6' => 'Ja — full kildekode, kan videreselges med ditt merke.',
        'faq_q7' => 'Hvilke land passer best?',
        'faq_a7' => 'Ukraina, Russland, USA, Litauen, Norge — tilpassede språk, valutaer og funksjoner.',
        'faq_q8' => 'Hvordan får jeg skriptet?',
        'faq_a8' => 'Etter betaling — umiddelbar nedlasting + instruksjoner + 30 dagers support.',

        'form_name' => 'Ditt navn *',
        'form_contact' => 'E-post eller Telegram *',
        'form_message' => 'Hva er du interessert i? (rom, leiligheter, biler, annonser, blogg, moduler...)',
        'form_captcha' => 'Hvor mye blir',
        'form_submit' => 'Send forespørsel',
        'form_success' => 'Takk! Meldingen er sendt. Vi kontakter deg innen 1–3 timer.',
    ],
];

// Отримання перекладів
$t = $translations[$lang] ?? $translations['ua'];
?><?php
// ... (весь попередній PHP-код з перекладами залишається без змін)
?>

<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= htmlspecialchars($t['site_title']) ?></title>
    <!-- ... (всі попередні meta-теги залишаються) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1d4ed8;
            --primary-dark: #1e40af;
            --accent: #f59e0b;
            --light: #f9fafb;
            --dark: #0f172a;
            --gray: #64748b;
            --success: #10b981;
            --border: #e2e8f0;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.7;
        }
        .container { max-width: 1320px; margin: 0 auto; padding: 0 1.5rem; }
        header { background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 1000; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; padding: 1.2rem 0; }
        .lang-switcher a {
            margin-left: 0.6rem;
            padding: 0.45rem 1rem;
            border-radius: 9999px;
            text-decoration: none;
            color: var(--gray);
            font-weight: 500;
            transition: all 0.25s;
        }
        .lang-switcher a:hover, .lang-switcher a.active { background: var(--primary); color: white; }
        .hero { background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); padding: 9rem 0 7rem; text-align: center; }
        .hero h1 { font-size: clamp(2.8rem, 7vw, 4.8rem); margin-bottom: 1.4rem; line-height: 1.05; }
        .hero p { font-size: 1.5rem; color: #475569; max-width: 760px; margin: 0 auto 2.8rem; }
        .btn {
            display: inline-block;
            padding: 1.1rem 2.6rem;
            background: var(--primary);
            color: white;
            border-radius: 9999px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.15rem;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px rgba(29,78,216,0.22);
        }
        .btn:hover { transform: translateY(-4px); box-shadow: 0 18px 40px rgba(29,78,216,0.3); }
        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
            margin-left: 1.2rem;
        }
        section { padding: 7rem 0; }
        h2 { font-size: 3rem; text-align: center; margin-bottom: 3.5rem; color: var(--dark); }
        .grid-examples {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
            gap: 2.2rem;
        }
        .example-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(0,0,0,0.09);
            transition: all 0.35s ease;
        }
        .example-card:hover { transform: translateY(-14px); box-shadow: 0 24px 60px rgba(0,0,0,0.14); }
        .example-card img {
            width: 100%;
            height: 260px;
            object-fit: cover;
            transition: transform 0.6s ease;
        }
        .example-card:hover img { transform: scale(1.06); }
        .card-content { padding: 1.8rem; }
        .card-content h3 {
            margin: 0 0 1rem;
            font-size: 1.55rem;
            color: var(--primary);
        }
        /* Інші стилі accordion, form, etc. залишаються майже без змін, лише трохи покращено */
        .other-products {
            background: white;
            padding: 6rem 0;
            text-align: center;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        .product-card {
            background: #f8fafc;
            border-radius: 16px;
            padding: 2.2rem;
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
            transition: all 0.3s;
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 40px rgba(0,0,0,0.12);
        }
        .product-card h3 { color: var(--primary); margin-bottom: 1rem; }
        footer {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #e2e8f0;
            padding: 5rem 0 2.5rem;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 3rem;
            margin-bottom: 4rem;
        }
        .footer-title { color: white; font-size: 1.4rem; margin-bottom: 1.4rem; font-weight: 600; }
        .footer-links a {
            color: #cbd5e1;
            text-decoration: none;
            display: block;
            margin-bottom: 0.9rem;
            transition: color 0.25s;
        }
        .footer-links a:hover { color: white; }
        .social-icons a {
            color: #94a3b8;
            font-size: 1.6rem;
            margin-right: 1.4rem;
            transition: all 0.3s;
        }
        .social-icons a:hover { color: white; transform: translateY(-3px); }
        .footer-bottom {
            border-top: 1px solid #334155;
            padding-top: 2.5rem;
            text-align: center;
            font-size: 0.95rem;
            color: #94a3b8;
        }
        @media (max-width: 768px) {
            .hero { padding: 7rem 0 5rem; }
            h2 { font-size: 2.4rem; }
            .grid-examples { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<?php
// templates/default/booking.php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';

// Логирование для отладки
function logDebug($message) {
    error_log("[booking] " . $message . "\n", 3, $_SERVER['DOCUMENT_ROOT'] . '/logs/booking_debug.log');
}

// Загрузка настроек из файла
$settings_file = $_SERVER['DOCUMENT_ROOT'] . '/uploads/booking_settings.php';
$settings = file_exists($settings_file) ? include $settings_file : [
    'currency' => 'UAH',
    'min_price' => 50,
    'max_price' => 5000,
    'items_per_page' => 5,
    'robots' => 'index, follow',
    'description' => 'Бронирование номеров онлайн - найдите идеальное место для отдыха.',
    'keywords' => 'бронирование, номера, отель, отдых, аренда'
];
$currency = $settings['currency'];
$min_price = $settings['min_price'];
$max_price = $settings['max_price'];
$items_per_page = $settings['items_per_page'];
$robots = $settings['robots'];
$description = $settings['description'];
$keywords = $settings['keywords'];

// Параметры поиска (опционально)
$check_in = $_POST['check_in'] ?? '';
$check_out = $_POST['check_out'] ?? '';
$guests = (int)($_POST['guests'] ?? 1);

// Валидация дат
if ($check_in && $check_out) {
    $check_in_date = DateTime::createFromFormat('Y-m-d', $check_in);
    $check_out_date = DateTime::createFromFormat('Y-m-d', $check_out);
    if (!$check_in_date || !$check_out_date || $check_out_date <= $check_in_date) {
        $error = "Неверные даты заезда или выезда.";
        $check_in = $check_out = '';
    }
}

// Поиск доступных номеров
$rooms = [];
$error = '';

// Базовый запрос для отображения номеров по умолчанию
$query = "SELECT r.*, c.name AS category_name 
          FROM rooms r 
          LEFT JOIN booking_categories c ON r.category_id = c.id 
          WHERE r.status = 'available' 
          AND r.price BETWEEN ? AND ? 
          AND r.capacity >= ? 
          LIMIT ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    $error = "Ошибка подготовки запроса: " . $conn->error;
} else {
    $stmt->bind_param("iiii", $min_price, $max_price, $guests, $items_per_page);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $rooms = $result->fetch_all(MYSQLI_ASSOC);
        if (empty($rooms)) {
            $error = "Нет доступных номеров для указанных параметров.";
        }
    } else {
        $error = "Ошибка выполнения запроса: " . $stmt->error;
    }
    $stmt->close();
}

// Если указаны даты, фильтруем дополнительно
if ($check_in && $check_out && empty($error)) {
    $query = "SELECT r.*, c.name AS category_name 
              FROM rooms r 
              LEFT JOIN booking_categories c ON r.category_id = c.id 
              WHERE r.status = 'available' 
              AND r.price BETWEEN ? AND ? 
              AND r.capacity >= ? 
              AND r.id NOT IN (
                  SELECT room_id FROM bookings 
                  WHERE (check_in <= ? AND check_out >= ?) 
                  AND status != 'cancelled'
              ) 
              LIMIT ?";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        $error = "Ошибка подготовки запроса: " . $conn->error;
    } else {
        $stmt->bind_param("iiissi", $min_price, $max_price, $guests, $check_out, $check_in, $items_per_page);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rooms = $result->fetch_all(MYSQLI_ASSOC);
            if (empty($rooms)) {
                $error = "Нет доступных номеров на выбранные даты.";
            }
        } else {
            $error = "Ошибка выполнения запроса: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Функция для получения массива изображений
function getImages($imageJson) {
    $defaultImage = ['/uploads/booking/default_room.webp'];
    if (empty($imageJson)) {
        logDebug("Image JSON is empty");
        return $defaultImage;
    }
    $images = json_decode($imageJson, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($images) || empty($images)) {
        logDebug("Invalid JSON or empty array: " . $imageJson);
        return $defaultImage;
    }
    $validImages = [];
    foreach ($images as $imagePath) {
        if (strpos($imagePath, '/uploads/booking/') === 0) {
            $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
            if (file_exists($fullPath)) {
                $validImages[] = $imagePath;
                logDebug("Valid image found: " . $imagePath);
            } else {
                logDebug("Image file not found: " . $fullPath);
            }
        } else {
            logDebug("Invalid image path: " . $imagePath);
        }
    }
    return !empty($validImages) ? $validImages : $defaultImage;
}

// Отладочный вывод всех комнат
if (isset($_GET['debug'])) {
    echo '<pre>';
    foreach ($rooms as $room) {
        echo "Room ID: {$room['id']}, Image JSON: {$room['image']}\n";
        $images = getImages($room['image']);
        print_r($images);
    }
    echo '</pre>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="<?php echo htmlspecialchars($robots); ?>">
    <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($keywords); ?>">
    <title>Онлайн бронирование - Tender CMS</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #003580;
            --secondary-color: #febb02;
            --success-color: #0071c2;
            --text-color: #333;
            --error-color: #721c24;
            --error-bg: #f8d7da;
        }
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: var(--text-color);
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: var(--primary-color);
            color: white;
            padding: 2rem;
            text-align: center;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .search-form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin: -50px 0 30px;
            position: relative;
            z-index: 1;
        }
        .search-form form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .search-form input, .search-form select {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            flex: 1;
            min-width: 150px;
        }
        .search-form button {
            padding: 12px 20px;
            background: var(--secondary-color);
            color: var(--primary-color);
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
        .search-form button:hover {
            background: #e6a900;
        }
        .rooms-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .room-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .room-card:hover {
            transform: translateY(-5px);
        }
        .room-info {
            padding: 15px;
        }
        .room-info h3 {
            margin: 0 0 10px;
        }
        .room-info h3 a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s;
        }
        .room-info h3 a:hover {
            color: var(--success-color);
        }
        .room-info p {
            margin: 5px 0;
        }
        .room-info .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--success-color);
        }
        .book-btn {
            display: block;
            text-align: center;
            padding: 10px;
            background: var(--success-color);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            transition: background 0.3s;
        }
        .book-btn:hover {
            background: #005ea6;
        }
        .error {
            color: var(--error-color);
            background: var(--error-bg);
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
        /* Стили для мини-галереи */
        .room-gallery {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
        }
        .gallery-main {
            width: 100%;
            height: 100%;
        }
        .gallery-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
            border-radius: 10px 10px 0 0;
        }
        .gallery-main img.active {
            display: block;
        }
        .gallery-thumbs {
            display: flex;
            gap: 5px;
            padding: 10px;
            overflow-x: auto;
            background: #f9f9f9;
        }
        .gallery-thumbs img {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s, transform 0.3s;
        }
        .gallery-thumbs img:hover {
            opacity: 1;
            transform: scale(1.1);
        }
        .gallery-thumbs img.active {
            opacity: 1;
            border: 2px solid var(--primary-color);
        }
        .gallery-nav {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            transform: translateY(-50%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .room-gallery:hover .gallery-nav {
            opacity: 1;
        }
        .gallery-nav button {
            background: rgba(0, 0, 0, 0.5);
            border: none;
            color: white;
            font-size: 1.2rem;
            padding: 10px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .gallery-nav button:hover {
            background: rgba(0, 0, 0, 0.8);
        }
        @media (max-width: 768px) {
            .search-form form {
                flex-direction: column;
            }
            .search-form input, .search-form select, .search-form button {
                width: 100%;
            }
            .rooms-list {
                grid-template-columns: 1fr;
            }
            .room-gallery {
                height: 150px;
            }
            .gallery-thumbs img {
                width: 50px;
                height: 35px;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.room-gallery').forEach(gallery => {
                const mainImages = gallery.querySelectorAll('.gallery-main img');
                const thumbs = gallery.querySelectorAll('.gallery-thumbs img');
                const prevBtn = gallery.querySelector('.gallery-nav .prev');
                const nextBtn = gallery.querySelector('.gallery-nav .next');
                let currentIndex = 0;

                if (mainImages.length === 0) return; // Пропускаем, если нет изображений

                function updateGallery(index) {
                    mainImages.forEach(img => img.classList.remove('active'));
                    thumbs.forEach(thumb => thumb.classList.remove('active'));
                    mainImages[index].classList.add('active');
                    thumbs[index].classList.add('active');
                    currentIndex = index;
                }

                thumbs.forEach((thumb, index) => {
                    thumb.addEventListener('click', () => updateGallery(index));
                });

                if (prevBtn) {
                    prevBtn.addEventListener('click', () => {
                        let newIndex = currentIndex - 1;
                        if (newIndex < 0) newIndex = mainImages.length - 1;
                        updateGallery(newIndex);
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', () => {
                        let newIndex = currentIndex + 1;
                        if (newIndex >= mainImages.length) newIndex = 0;
                        updateGallery(newIndex);
                    });
                }

                updateGallery(0); // Инициализация с первым изображением
            });
        });
    </script>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-hotel"></i> Скрипт онлайн Бронирование</h1>
        <p>Найдите идеальное место для отдыха</p>
    </div>
    <div class="container">
        <div class="search-form">
            <form method="POST">
                <input type="date" name="check_in" value="<?php echo htmlspecialchars($check_in); ?>" required>
                <input type="date" name="check_out" value="<?php echo htmlspecialchars($check_out); ?>" required>
                <select name="guests" required>
                    <?php for ($i = 1; $i <= 10; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo $guests == $i ? 'selected' : ''; ?>><?php echo $i; ?> <?php echo $i == 1 ? 'гость' : 'гостей'; ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit"><i class="fas fa-search"></i> Найти</button>
            </form>
        </div>

        <?php if ($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="rooms-list">
            <?php foreach ($rooms as $room): ?>
                <div class="room-card">
                    <div class="room-gallery">
                        <div class="gallery-main">
                            <?php foreach (getImages($room['image']) as $index => $image): ?>
                                <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>">
                            <?php endforeach; ?>
                        </div>
                        <div class="gallery-nav">
                            <button class="prev"><i class="fas fa-chevron-left"></i></button>
                            <button class="next"><i class="fas fa-chevron-right"></i></button>
                        </div>
                        <div class="gallery-thumbs">
                            <?php foreach (getImages($room['image']) as $index => $image): ?>
                                <img src="<?php echo htmlspecialchars($image); ?>" alt="Thumbnail" class="<?php echo $index === 0 ? 'active' : ''; ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="room-info">
                        <h3><a href="/templates/default/booking_order.php?room_id=<?php echo $room['id']; ?>"><?php echo htmlspecialchars($room['name']); ?></a></h3>
                        <p><i class="fas fa-folder"></i> Категория: <?php echo htmlspecialchars($room['category_name']); ?></p>
                        <p><i class="fas fa-users"></i> Вместимость: <?php echo htmlspecialchars($room['capacity']); ?> гостей</p>
                        <p class="price"><i class="fas fa-money-bill-wave"></i> <?php echo htmlspecialchars($room['price']); ?> <?php echo htmlspecialchars($currency); ?> / ночь</p>
                        <a href="/templates/default/booking_order.php?room_id=<?php echo $room['id']; ?>" class="book-btn"><i class="fas fa-book"></i> Забронировать</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
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

</body>
</html>
<?php include 'booking_footer.php'; ?>

<section class="other-products">
    <div class="container">
        <h2>Інші наші продукти</h2>
        <div class="products-grid">
            <div class="product-card">
                <h3>Shop CMS</h3>
                <p>Сучасний інтернет-магазин з швидким завантаженням, адаптивним дизайном, інтеграцією платежів (LiqPay, WayForPay, Stripe) та SEO з коробки.</p>
                <a href="#" class="btn" style="margin-top:1.2rem;">Дізнатись більше</a>
            </div>
            <div class="product-card">
                <h3>Dating CMS</h3>
                <p>Скрипт знайомств з геолокацією, чатами в реальному часі, верифікацією фото, преміум-підпискою та мобільною версією.</p>
                <a href="#" class="btn" style="margin-top:1.2rem;">Дізнатись більше</a>
            </div>
            <div class="product-card">
                <h3>Tender CMS</h3>
                <p>Гнучка система тендерів та закупівель під будь-які задачі: державні, приватні, фріланс, будівництво, послуги.</p>
                <a href="#" class="btn" style="margin-top:1.2rem;">Дізнатись більше</a>
            </div>
        </div>
    </div>
</section>

<footer>
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="footer-title">Booking CMS</div>
                <p style="margin-bottom:1.5rem;">Система бронювання 2025<br>Без комісій. Повний контроль. Разова оплата.</p>
                <div class="social-icons">
                    <a href="https://github.com/Ruslan-Bilohash" target="_blank"><i class="fab fa-github"></i></a>
                    <a href="https://t.me/+4746255885a" target="_blank"><i class="fab fa-telegram"></i></a>
                    <a href="mailto:rbilohash@gmail.com"><i class="fas fa-envelope"></i></a>
                </div>
            </div>

            <div class="footer-links">
                <div class="footer-title">Розробник</div>
                <a href="mailto:rbilohash@gmail.com">rbilohash@gmail.com</a>
                <a href="https://github.com/Ruslan-Bilohash" target="_blank">GitHub</a>
                <a href="https://t.me/+4746255885a" target="_blank">Telegram: +47 462 55 885</a>
            </div>

            <div class="footer-links">
                <div class="footer-title">Продукти</div>
                <a href="#">Shop CMS — Інтернет-магазин</a>
                <a href="/dating">Dating CMS — Сайт знайомств</a>
                <a href="#">Tender CMS — Система тендерів</a>
                <a href="#">Booking CMS — поточний продукт</a>
            </div>
        </div>

        <div class="footer-bottom">
            © <?= date('Y') ?> Booking CMS • Розроблено для реальних бізнесів • Повна власність, без оренди та підписок
        </div>
    </div>
</footer>

<script>
document.getElementById('show-more-btn')?.addEventListener('click', function() {
    document.getElementById('hidden-spoilers').style.display = 'block';
    this.parentElement.style.display = 'none';
});
</script>
</body>
</html>

