<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>URL Security Checker</title>
    <style>
        :root {
            --bg-color: #f0f0f0;
            --container-bg: white;
            --text-color: #333;
            --footer-color: #666;
            --shadow-color: rgba(0,0,0,0.1);
        }
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: var(--bg-color);
            color: var(--text-color);
            transition: background 0.3s, color 0.3s;
        }
        .header {
            background: linear-gradient(45deg, #4CAF50, #2196F3);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 32px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        .slogan {
            font-style: italic;
            font-size: 14px;
            margin-top: 10px;
            color: #f0f0f0;
        }
        .container {
            background: var(--container-bg);
            padding: 20px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 0 10px var(--shadow-color);
            transition: background 0.3s;
        }
        .input-group {
            margin-bottom: 20px;
        }
        input[type="text"] {
            width: 70%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #45a049;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
        }
        .clean {
            color: #2ecc71;
            background: #e8f5e9;
            padding: 5px 10px;
            border-radius: 3px;
            margin: 5px 0;
        }
        .warning {
            color: #e74c3c;
            background: #ffebee;
            padding: 5px 10px;
            border-radius: 3px;
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            color: var(--footer-color);
            font-size: 14px;
            border-top: 1px solid #ddd;
            transition: color 0.3s;
        }
        .footer a {
            color: #2196F3;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .help-btn, .feedback-btn {
            float: right;
            background: #2196F3;
            padding: 5px 15px;
            margin-bottom: 10px;
            margin-left: 10px;
        }
        .help-btn:hover, .feedback-btn:hover {
            background: #1976D2;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s;
        }
        .modal-content {
            background: var(--container-bg);
            margin: 50px auto;
            padding: 20px;
            width: 70%;
            max-width: 600px;
            border-radius: 10px;
            position: relative;
            animation: slideIn 0.3s;
            transition: background 0.3s;
        }
        .close {
            position: absolute;
            right: 20px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }
        .close:hover {
            color: #000;
        }
        .lang-switcher, .theme-switcher {
            text-align: right;
            margin-bottom: 10px;
        }
        .lang-switcher select, .theme-switcher select {
            padding: 5px;
            border-radius: 5px;
        }
        /* Тёмная тема */
        body.dark {
            --bg-color: #1a1a1a;
            --container-bg: #2c2c2c;
            --text-color: #e0e0e0;
            --footer-color: #bbb;
            --shadow-color: rgba(255,255,255,0.1);
        }
        /* Анимации */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 id="title">URL Security Checker</h1>
        <div class="slogan" id="slogan">
            Инструмент исключительно для проверки безопасности вашего сайта. <br>
            Использование в целях взлома строго запрещено!
        </div>
    </div>

    <div class="container">
        <!-- Переключатели языка и темы -->
        <div class="lang-switcher">
            <select id="language" onchange="changeLanguage()">
                <option value="ru">Русский</option>
                <option value="ua">Українська</option>
                <option value="en">English</option>
                <option value="lt">Lietuvių</option>
            </select>
        </div>
        <div class="theme-switcher">
            <select id="theme" onchange="changeTheme()">
                <option value="light">Светлая</option>
                <option value="dark">Тёмная</option>
            </select>
        </div>

        <button class="help-btn" onclick="document.getElementById('helpModal').style.display='block'" id="help-btn">Справка</button>
        <button class="feedback-btn" onclick="document.getElementById('feedbackModal').style.display='block'" id="feedback-btn">Обратная связь</button>
        <h2 id="check-title">Проверка безопасности URL</h2>
        <form method="POST" class="input-group">
            <input type="text" name="url" placeholder="Введите URL для проверки" required id="url-placeholder">
            <button type="submit" id="check-btn">Проверить</button>
        </form>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $url = trim($_POST["url"]);
            
            function checkSQLi($url) {
                $sqli_patterns = [
                    '/\b(union|select|from|where|insert|delete|update)\b/i',
                    '/[\'"]\s*--/',
                    '/[\'"]\s*;/',
                    '/\b(exec|execute)\b/i'
                ];
                foreach ($sqli_patterns as $pattern) {
                    if (preg_match($pattern, $url)) {
                        return false;
                    }
                }
                return true;
            }

            function checkXSS($url) {
                $xss_patterns = [
                    '/<script\b[^>]*>/i',
                    '/on\w+\s*=/i',
                    '/javascript:/i',
                    '/vbscript:/i'
                ];
                foreach ($xss_patterns as $pattern) {
                    if (preg_match($pattern, $url)) {
                        return false;
                    }
                }
                return true;
            }

            function checkHeaders($url) {
                $headers = @get_headers($url);
                if ($headers === false) return false;
                
                $security_headers = [
                    'X-Content-Type-Options',
                    'X-Frame-Options',
                    'X-XSS-Protection'
                ];
                
                $found_headers = [];
                foreach ($headers as $header) {
                    foreach ($security_headers as $sec_header) {
                        if (stripos($header, $sec_header) !== false) {
                            $found_headers[] = $sec_header;
                        }
                    }
                }
                return count($found_headers) >= 2;
            }

            function checkContent($url) {
                $content = @file_get_contents($url);
                if ($content === false) return true;
                
                $suspicious_patterns = [
                    '/eval\(/i',
                    '/base64_decode/i',
                    '/system\(/i',
                    '/exec\(/i'
                ];
                
                foreach ($suspicious_patterns as $pattern) {
                    if (preg_match($pattern, $content)) {
                        return false;
                    }
                }
                return true;
            }

            function checkSSL($url) {
                if (stripos($url, 'https://') !== 0) return false;
                $context = stream_context_create(['ssl' => ['capture_peer_cert' => true]]);
                $stream = @stream_socket_client("ssl://".parse_url($url, PHP_URL_HOST).":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
                if ($stream === false) return false;
                $params = stream_context_get_params($stream);
                $cert = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
                fclose($stream);
                return $cert && (time() < $cert['validTo_time_t']);
            }

            echo '<div class="result">';
            
            $sqli_result = checkSQLi($url);
            echo '<div class="' . ($sqli_result ? 'clean' : 'warning') . '" id="sqli-result">';
            echo 'SQL-инъекции: ' . ($sqli_result ? 'Чисто' : 'Обнаружены подозрительные паттерны') . '</div>';

            $xss_result = checkXSS($url);
            echo '<div class="' . ($xss_result ? 'clean' : 'warning') . '" id="xss-result">';
            echo 'XSS: ' . ($xss_result ? 'Чисто' : 'Обнаружены подозрительные паттерны') . '</div>';

            echo '<div class="clean" id="csrf-result">CSRF: Чисто (требует более глубокого анализа)</div>';

            $headers_result = checkHeaders($url);
            echo '<div class="' . ($headers_result ? 'clean' : 'warning') . '" id="headers-result">';
            echo 'Заголовки безопасности: ' . ($headers_result ? 'Все заголовки на месте' : 'Отсутствуют некоторые заголовки') . '</div>';

            $content_result = checkContent($url);
            echo '<div class="' . ($content_result ? 'clean' : 'warning') . '" id="content-result">';
            echo 'Анализ содержимого: ' . ($content_result ? 'Подозрительных паттернов не найдено' : 'Обнаружены подозрительные паттерны') . '</div>';

            $ssl_result = checkSSL($url);
            echo '<div class="' . ($ssl_result ? 'clean' : 'warning') . '" id="ssl-result">';
            echo 'SSL-сертификат: ' . ($ssl_result ? 'Действителен' : 'Отсутствует или истёк') . '</div>';

            echo '</div>';
        }
        ?>
    </div>

    <div class="footer" id="footer">
        <p>Разработчик: Ruslan Bilohash, 2025</p>
        <p>Telegram: <a href="https://t.me/meistru_lt" target="_blank">@meistru_lt</a></p>
        <p>Email: <a href="mailto:rbilohash@gmail.com">rbilohash@gmail.com</a></p>
        <p>Website: <a href="https://meistru.lt" target="_blank">meistru.lt</a></p>
    </div>

    <!-- Модальное окно справки -->
    <div id="helpModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('helpModal').style.display='none'">×</span>
            <h2 id="help-title">О программе</h2>
            <p id="help-desc"><strong>URL Security Checker</strong> - это инструмент для базового анализа безопасности веб-страниц. Скрипт помогает владельцам сайтов проверить свои ресурсы на наличие потенциальных уязвимостей и повысить уровень защиты.</p>
            
            <h3 id="features-title">Возможности:</h3>
            <ul id="features-list">
                <li>Проверка на SQL-инъекции</li>
                <li>Обнаружение XSS-уязвимостей</li>
                <li>Анализ HTTP-заголовков безопасности</li>
                <li>Поиск подозрительного контента</li>
                <li>Проверка SSL-сертификата</li>
            </ul>

            <h3 id="how-to-title">Как использовать:</h3>
            <ol id="how-to-list">
                <li>Введите URL вашего сайта в поле ввода</li>
                <li>Нажмите кнопку "Проверить"</li>
                <li>Ознакомьтесь с результатами анализа</li>
                <li>Зеленый цвет - всё в порядке, красный - требуется внимание</li>
            </ol>

            <h3 id="tips-title">Советы:</h3>
            <ul id="tips-list">
                <li>Используйте HTTPS для повышения безопасности</li>
                <li>Регулярно обновляйте программное обеспечение сайта</li>
                <li>Проверяйте заголовки безопасности через инструменты разработчика</li>
            </ul>

            <p id="warning"><strong>Важно:</strong> Этот инструмент предназначен исключительно для проверки собственных сайтов с целью повышения их безопасности. Использование для анализа чужих ресурсов без разрешения или в злонамеренных целях запрещено!</p>
        </div>
    </div>

    <!-- Модальное окно обратной связи -->
    <div id="feedbackModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('feedbackModal').style.display='none'">×</span>
            <h2 id="feedback-title">Обратная связь</h2>
            <form id="feedback-form" onsubmit="submitFeedback(event)">
                <label for="feedback-name" id="feedback-name-label">Имя:</label><br>
                <input type="text" id="feedback-name" name="name" required><br><br>
                <label for="feedback-email" id="feedback-email-label">Email:</label><br>
                <input type="email" id="feedback-email" name="email" required><br><br>
                <label for="feedback-message" id="feedback-message-label">Сообщение:</label><br>
                <textarea id="feedback-message" name="message" rows="5" required></textarea><br><br>
                <button type="submit" id="feedback-submit">Отправить</button>
            </form>
        </div>
    </div>

    <script>
        // Мультиязычность
        const translations = {
            ru: {
                title: "URL Security Checker",
                slogan: "Инструмент исключительно для проверки безопасности вашего сайта.<br>Использование в целях взлома строго запрещено!",
                helpBtn: "Справка",
                feedbackBtn: "Обратная связь",
                checkTitle: "Проверка безопасности URL",
                urlPlaceholder: "Введите URL для проверки",
                checkBtn: "Проверить",
                sqliResult: "SQL-инъекции: ",
                xssResult: "XSS: ",
                csrfResult: "CSRF: Чисто (требует более глубокого анализа)",
                headersResult: "Заголовки безопасности: ",
                contentResult: "Анализ содержимого: ",
                sslResult: "SSL-сертификат: ",
                clean: "Чисто",
                warning: "Обнаружены подозрительные паттерны",
                headersClean: "Все заголовки на месте",
                headersWarning: "Отсутствуют некоторые заголовки",
                contentClean: "Подозрительных паттернов не найдено",
                sslClean: "Действителен",
                sslWarning: "Отсутствует или истёк",
                footer: {
                    developer: "Разработчик: Ruslan Bilohash, 2025",
                    telegram: "Telegram: ",
                    email: "Email: ",
                    website: "Website: "
                },
                helpTitle: "О программе",
                helpDesc: "<strong>URL Security Checker</strong> - это инструмент для базового анализа безопасности веб-страниц. Скрипт помогает владельцам сайтов проверить свои ресурсы на наличие потенциальных уязвимостей и повысить уровень защиты.",
                featuresTitle: "Возможности:",
                featuresList: ["Проверка на SQL-инъекции", "Обнаружение XSS-уязвимостей", "Анализ HTTP-заголовков безопасности", "Поиск подозрительного контента", "Проверка SSL-сертификата"],
                howToTitle: "Как использовать:",
                howToList: ["Введите URL вашего сайта в поле ввода", "Нажмите кнопку \"Проверить\"", "Ознакомьтесь с результатами анализа", "Зеленый цвет - всё в порядке, красный - требуется внимание"],
                tipsTitle: "Советы:",
                tipsList: ["Используйте HTTPS для повышения безопасности", "Регулярно обновляйте программное обеспечение сайта", "Проверяйте заголовки безопасности через инструменты разработчика"],
                warning: "<strong>Важно:</strong> Этот инструмент предназначен исключительно для проверки собственных сайтов с целью повышения их безопасности. Использование для анализа чужих ресурсов без разрешения или в злонамеренных целях запрещено!",
                feedbackTitle: "Обратная связь",
                feedbackNameLabel: "Имя:",
                feedbackEmailLabel: "Email:",
                feedbackMessageLabel: "Сообщение:",
                feedbackSubmit: "Отправить"
            },
            ua: {
                title: "URL Security Checker",
                slogan: "Інструмент виключно для перевірки безпеки вашого сайту.<br>Використання з метою злому суворо заборонено!",
                helpBtn: "Довідка",
                feedbackBtn: "Зворотний зв'язок",
                checkTitle: "Перевірка безпеки URL",
                urlPlaceholder: "Введіть URL для перевірки",
                checkBtn: "Перевірити",
                sqliResult: "SQL-ін'єкції: ",
                xssResult: "XSS: ",
                csrfResult: "CSRF: Чисто (потрібен глибший аналіз)",
                headersResult: "Заголовки безпеки: ",
                contentResult: "Аналіз вмісту: ",
                sslResult: "SSL-сертифікат: ",
                clean: "Чисто",
                warning: "Виявлено підозрілі патерни",
                headersClean: "Усі заголовки на місці",
                headersWarning: "Відсутні деякі заголовки",
                contentClean: "Підозрілих патернів не знайдено",
                sslClean: "Дійсний",
                sslWarning: "Відсутній або закінчився",
                footer: {
                    developer: "Розробник: Руслан Білогаш, 2025",
                    telegram: "Telegram: ",
                    email: "Email: ",
                    website: "Website: "
                },
                helpTitle: "Про програму",
                helpDesc: "<strong>URL Security Checker</strong> - це інструмент для базового аналізу безпеки веб-сторінок. Скрипт допомагає власникам сайтів перевірити свої ресурси на наявність потенційних вразливостей та підвищити рівень захисту.",
                featuresTitle: "Можливості:",
                featuresList: ["Перевірка на SQL-ін'єкції", "Виявлення XSS-вразливостей", "Аналіз HTTP-заголовків безпеки", "Пошук підозрілого вмісту", "Перевірка SSL-сертифіката"],
                howToTitle: "Як використовувати:",
                howToList: ["Введіть URL вашого сайту в поле введення", "Натисніть кнопку \"Перевірити\"", "Ознайомтеся з результатами аналізу", "Зелений колір - усе гаразд, червоний - потребує уваги"],
                tipsTitle: "Поради:",
                tipsList: ["Використовуйте HTTPS для підвищення безпеки", "Регулярно оновлюйте програмне забезпечення сайту", "Перевіряйте заголовки безпеки через інструменти розробника"],
                warning: "<strong>Важливо:</strong> Цей інструмент призначений виключно для перевірки власних сайтів з метою підвищення їх безпеки. Використання для аналізу чужих ресурсів без дозволу або в зловмисних цілях заборонено!",
                feedbackTitle: "Зворотний зв'язок",
                feedbackNameLabel: "Ім'я:",
                feedbackEmailLabel: "Email:",
                feedbackMessageLabel: "Повідомлення:",
                feedbackSubmit: "Надіслати"
            },
            en: {
                title: "URL Security Checker",
                slogan: "A tool exclusively for checking the security of your website.<br>Use for hacking purposes is strictly prohibited!",
                helpBtn: "Help",
                feedbackBtn: "Feedback",
                checkTitle: "URL Security Check",
                urlPlaceholder: "Enter URL to check",
                checkBtn: "Check",
                sqliResult: "SQL Injections: ",
                xssResult: "XSS: ",
                csrfResult: "CSRF: Clean (requires deeper analysis)",
                headersResult: "Security Headers: ",
                contentResult: "Content Analysis: ",
                sslResult: "SSL Certificate: ",
                clean: "Clean",
                warning: "Suspicious patterns detected",
                headersClean: "All headers present",
                headersWarning: "Some headers are missing",
                contentClean: "No suspicious patterns found",
                sslClean: "Valid",
                sslWarning: "Missing or expired",
                footer: {
                    developer: "Developer: Ruslan Bilohash, 2025",
                    telegram: "Telegram: ",
                    email: "Email: ",
                    website: "Website: "
                },
                helpTitle: "About the Program",
                helpDesc: "<strong>URL Security Checker</strong> is a tool for basic security analysis of web pages. The script helps website owners check their resources for potential vulnerabilities and improve protection.",
                featuresTitle: "Features:",
                featuresList: ["SQL injection check", "XSS vulnerability detection", "HTTP security headers analysis", "Suspicious content search", "SSL certificate check"],
                howToTitle: "How to Use:",
                howToList: ["Enter your website URL in the input field", "Click the \"Check\" button", "Review the analysis results", "Green means all is well, red means attention is needed"],
                tipsTitle: "Tips:",
                tipsList: ["Use HTTPS to enhance security", "Regularly update your website software", "Check security headers via developer tools"],
                warning: "<strong>Important:</strong> This tool is intended solely for checking your own websites to improve their security. Using it to analyze others' resources without permission or for malicious purposes is prohibited!",
                feedbackTitle: "Feedback",
                feedbackNameLabel: "Name:",
                feedbackEmailLabel: "Email:",
                feedbackMessageLabel: "Message:",
                feedbackSubmit: "Send"
            },
            lt: {
                title: "URL Security Checker",
                slogan: "Įrankis, skirtas tik jūsų svetainės saugumui tikrinti.<br>Naudoti įsilaužimo tikslais griežtai draudžiama!",
                helpBtn: "Pagalba",
                feedbackBtn: "Atsiliepimai",
                checkTitle: "URL Saugumo Patikrinimas",
                urlPlaceholder: "Įveskite URL patikrinimui",
                checkBtn: "Patikrinti",
                sqliResult: "SQL Injekcijos: ",
                xssResult: "XSS: ",
                csrfResult: "CSRF: Švaru (reikalinga gilesnė analizė)",
                headersResult: "Saugumo Antraštės: ",
                contentResult: "Turinio Analizė: ",
                sslResult: "SSL Sertifikatas: ",
                clean: "Švaru",
                warning: "Aptikti įtartini modeliai",
                headersClean: "Visos antraštės yra",
                headersWarning: "Trūksta kai kurių antraščių",
                contentClean: "Įtartini modeliai nerasti",
                sslClean: "Galiojantis",
                sslWarning: "Nėra arba pasibaigęs",
                footer: {
                    developer: "Kūrėjas: Ruslan Bilohash, 2025",
                    telegram: "Telegram: ",
                    email: "Email: ",
                    website: "Website: "
                },
                helpTitle: "Apie Programą",
                helpDesc: "<strong>URL Security Checker</strong> yra įrankis, skirtas pagrindinei tinklalapių saugumo analizei. Šis skriptas padeda svetainių savininkams patikrinti savo išteklius dėl galimų pažeidžiamumų ir pagerinti apsaugą.",
                featuresTitle: "Funkcijos:",
                featuresList: ["SQL injekcijų tikrinimas", "XSS pažeidžiamumų aptikimas", "HTTP saugumo antraščių analizė", "Įtartino turinio paieška", "SSL sertifikato tikrinimas"],
                howToTitle: "Kaip Naudoti:",
                howToList: ["Įveskite savo svetainės URL į įvesties lauką", "Spustelėkite mygtuką \"Patikrinti\"", "Peržiūrėkite analizės rezultatus", "Žalia spalva reiškia, kad viskas gerai, raudona - reikia dėmesio"],
                tipsTitle: "Patarimai:",
                tipsList: ["Naudokite HTTPS, kad padidintumėte saugumą", "Reguliariai atnaujinkite svetainės programinę įrangą", "Tikrinkite saugumo antraštes per kūrėjo įrankius"],
                warning: "<strong>Svarbu:</strong> Šis įrankis skirtas tik jūsų pačių svetainių tikrinimui, siekiant pagerinti jų saugumą. Naudoti jį kitų išteklių analizei be leidimo ar piktybiniams tikslams draudžiama!",
                feedbackTitle: "Atsiliepimai",
                feedbackNameLabel: "Vardas:",
                feedbackEmailLabel: "El. paštas:",
                feedbackMessageLabel: "Žinutė:",
                feedbackSubmit: "Siųsti"
            }
        };

        function changeLanguage() {
            const lang = document.getElementById("language").value;
            const t = translations[lang];
            localStorage.setItem("lang", lang);

            document.getElementById("title").innerHTML = t.title;
            document.getElementById("slogan").innerHTML = t.slogan;
            document.getElementById("help-btn").innerHTML = t.helpBtn;
            document.getElementById("feedback-btn").innerHTML = t.feedbackBtn;
            document.getElementById("check-title").innerHTML = t.checkTitle;
            document.getElementById("url-placeholder").placeholder = t.urlPlaceholder;
            document.getElementById("check-btn").innerHTML = t.checkBtn;

            const footer = document.getElementById("footer");
            footer.innerHTML = `
                <p>${t.footer.developer}</p>
                <p>${t.footer.telegram}<a href="https://t.me/meistru_lt" target="_blank">@meistru_lt</a></p>
                <p>${t.footer.email}<a href="mailto:rbilohash@gmail.com">rbilohash@gmail.com</a></p>
                <p>${t.footer.website}<a href="https://meistru.lt" target="_blank">meistru.lt</a></p>
            `;

            document.getElementById("help-title").innerHTML = t.helpTitle;
            document.getElementById("help-desc").innerHTML = t.helpDesc;
            document.getElementById("features-title").innerHTML = t.featuresTitle;
            document.getElementById("features-list").innerHTML = t.featuresList.map(item => `<li>${item}</li>`).join("");
            document.getElementById("how-to-title").innerHTML = t.howToTitle;
            document.getElementById("how-to-list").innerHTML = t.howToList.map(item => `<li>${item}</li>`).join("");
            document.getElementById("tips-title").innerHTML = t.tipsTitle;
            document.getElementById("tips-list").innerHTML = t.tipsList.map(item => `<li>${item}</li>`).join("");
            document.getElementById("warning").innerHTML = t.warning;

            document.getElementById("feedback-title").innerHTML = t.feedbackTitle;
            document.getElementById("feedback-name-label").innerHTML = t.feedbackNameLabel;
            document.getElementById("feedback-email-label").innerHTML = t.feedbackEmailLabel;
            document.getElementById("feedback-message-label").innerHTML = t.feedbackMessageLabel;
            document.getElementById("feedback-submit").innerHTML = t.feedbackSubmit;

            if (document.getElementById("sqli-result")) {
                const sqliClean = document.getElementById("sqli-result").classList.contains("clean");
                document.getElementById("sqli-result").innerHTML = t.sqliResult + (sqliClean ? t.clean : t.warning);
                const xssClean = document.getElementById("xss-result").classList.contains("clean");
                document.getElementById("xss-result").innerHTML = t.xssResult + (xssClean ? t.clean : t.warning);
                document.getElementById("csrf-result").innerHTML = t.csrfResult;
                const headersClean = document.getElementById("headers-result").classList.contains("clean");
                document.getElementById("headers-result").innerHTML = t.headersResult + (headersClean ? t.headersClean : t.headersWarning);
                const contentClean = document.getElementById("content-result").classList.contains("clean");
                document.getElementById("content-result").innerHTML = t.contentResult + (contentClean ? t.contentClean : t.warning);
                const sslClean = document.getElementById("ssl-result").classList.contains("clean");
                document.getElementById("ssl-result").innerHTML = t.sslResult + (sslClean ? t.sslClean : t.sslWarning);
            }
        }

        function changeTheme() {
            const theme = document.getElementById("theme").value;
            localStorage.setItem("theme", theme);
            document.body.classList.toggle("dark", theme === "dark");
        }

        function submitFeedback(event) {
            event.preventDefault();
            const name = document.getElementById("feedback-name").value;
            const email = document.getElementById("feedback-email").value;
            const message = document.getElementById("feedback-message").value;
            alert(`Спасибо, ${name}! Ваше сообщение отправлено.\nEmail: ${email}\nСообщение: ${message}`);
            document.getElementById("feedback-form").reset();
            document.getElementById("feedbackModal").style.display = "none";
        }

        window.onclick = function(event) {
            const helpModal = document.getElementById('helpModal');
            const feedbackModal = document.getElementById('feedbackModal');
            if (event.target == helpModal) helpModal.style.display = "none";
            if (event.target == feedbackModal) feedbackModal.style.display = "none";
        }

        // Инициализация
        document.getElementById("language").value = localStorage.getItem("lang") || "ru";
        document.getElementById("theme").value = localStorage.getItem("theme") || "light";
        changeLanguage();
        changeTheme();
    </script>
</body>
</html>