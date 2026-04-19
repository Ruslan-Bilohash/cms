# Tender CMS

**Потужна PHP CMS для тендерів, магазинів, новин та бронювань з інтеграцією Nova Poshta**

![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/license-free-green)

## 🚀 Особливості

- **Тендери** — публікація, пошук, фільтр за містом
- **Інтернет-магазин** — кошик, оформлення замовлення, платежі (Stripe, PayPal, наложений платіж, банк)
- **Новини + сторінки** — з SEO, meta-тегами, відгуками, мультимовністю
- **Бронювання** (bookings) — для послуг, готелів, майстерень тощо
- **Інтеграція Nova Poshta** — міста, відділення, розрахунок доставки
- **Адмін-панель** — повне управління (адміни admin/demo)
- **SEO-оптимізація** — чисті URL, sitemap, meta, Open Graph
- **Шаблони** — легко змінювати дизайн (templates/default/)
- **Кеш, бекапи, крон** — готові папки
- **Багатомовність** — з коробки (встановлювач підтримує)

## 📁 Структура проекту

```
cms/
├── admin/              # Адмін-панель
├── backups/            # Автоматичні бекапи БД
├── cache/              # Кеш
├── cron/               # Задачі cron
├── includes/           # Конфіги, функції, db connect
├── templates/default/  # Основний шаблон сайту
├── uploads/            # Завантажені файли (фото, документи)
├── .htaccess           # Чисті URL
├── install.php         # Встановлювач
├── base.sql            # Повна структура БД
├── 404.php, seo.php, check.php
└── templates.zip       # Готові шаблони (архів)
```

## 🛠 Встановлення (2 хвилини)

1. Завантаж репозиторій на хостинг (PHP 7.4+, MySQL 8.0+).
2. Відкрий у браузері `https://твій-сайт/install.php`
3. Введи дані БД → натисни «Встановити».
4. Система створить:
   - `includes/config.php`
   - всі таблиці з `base.sql`
   - демо-адміністратора (`admin` / пароль з хешу в дампі)
5. Видали файл `install.php` для безпеки!

**Логін в адмінку:** `/admin/`  
Демо: `demo` / відповідний пароль (з таблиці `admins`).

## 🔧 Основні сторінки (через .htaccess)

- `/` — головна
- `/shop` — магазин
- `/tenders` — тендери
- `/news` — новини
- `/booking` — бронювання
- `/cart`, `/checkout` — кошик і оплата
- `/profile`, `/login`, `/register`

## 📝 Як додати свій шаблон

1. Розпакуй `templates.zip` або створи папку `templates/назва/`
2. Скопіюй структуру з `templates/default/`
3. Зміни дизайн — CMS підтягне автоматично.

## 🛡️ Безпека та рекомендації

- Після встановлення видали `install.php`
- Зміни паролі адміністраторів
- Включи SSL
- Налаштуй права на папки `uploads/`, `cache/`, `backups/` (755/644)
- Регулярно роби бекапи (папка `backups/`)

## 📌 Плани розвитку

- Додавання більше мов
- REST API
- Модуль «Профіль користувача» з галереєю
- Плагін-система

---

**Автор:** Ruslan Bilohash  
**Ліцензія:** MIT (можна використовувати комерційно)  
**Зв'язок:** пиши issues або в Telegram (якщо вказав)

Готовий допомогти з доопрацюванням, перекладом чи додаванням фіч! ⭐ Зірочка на GitHub дуже мотивує 🙂
```

### MIT License

Copyright (c) 2025 Ruslan Bilohash

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
