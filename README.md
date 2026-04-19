# 🚀 Modules Administration Panel — Мощная Бесплатная PHP CMS

**Открытая модульная админ-панель на PHP 8.1+** — всё для управления сайтом, интернет-магазином, тендерами, новостями, бронированием и SEO в одном месте.

**Полностью бесплатная • Open Source • Без ограничений**

![GitHub stars](https://img.shields.io/github/stars/Ruslan-Bilohash/cms?style=social)
![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4)
![License](https://img.shields.io/badge/license-MIT-green)

---

## ✨ Что такое Modules?

**Modules** — это современная, быстрая и очень гибкая **модульная CMS** на чистом PHP.  
Она создана для тех, кто хочет иметь полный контроль над своим сайтом, не переплачивая за SaaS-решения.

Вы можете подключать только те модули, которые вам нужны: магазин, тендеры, новости, бронирование, SEO, ИИ и многое другое.

### 🔥 Главные преимущества
- Полностью бесплатная и открытая
- Модульная архитектура (подключай только нужное)
- Современный glassmorphism-дизайн 2026 года
- Встроенная поддержка ИИ (xAI, ChatGPT, генерация контента)
- Высокая безопасность (Prepared Statements, 2FA, бэкапы)
- Отличная производительность + кэширование (Redis, MySQL, static)
- Полная мультиязычность (русский, украинский, английский, норвежский)

---

## 📋 Полный список возможностей

### 🧑‍💼 **Управление пользователями**
- Полная система ролей и прав доступа
- Реал-тайм статистика посещений
- 2FA (двухфакторная аутентификация)
- История всех действий пользователей
- Удобная аналитика и отчёты

### 🛒 **Интернет-магазин (Shop CMS)**
- Полноценный каталог товаров
- Управление заказами и статусами
- Платежи и интеграции
- Доставка (Почта России, Новая Почта и др.)
- Корзина, скидки, промокоды
- Подробная статистика продаж

### 📰 **Контент-менеджмент и Новости**
- Публикация новостей и статей
- Древовидные категории
- Отзывы и фидбек
- Баннеры и слайдеры
- Автоматические мета-теги

### 📅 **Система бронирования (Booking CMS)**
- Календари и расписание
- Управление записями
- Автоматические уведомления клиентам
- Менеджеры и администраторы

### 🔨 **Тендеры и Аукционы (Tender CMS)**
- Публикация тендеров
- Приём и обработка заявок
- Статусы и отслеживание

### 🔍 **Мощные SEO-инструменты**
- Автоматическая генерация `sitemap.xml`
- Управление title, description, keywords
- Open Graph и Twitter Cards
- SEO-оптимизация страниц

### ⚡ **Кэширование и производительность**
- MySQL кэш
- Redis кэширование
- Статический кэш страниц
- Мониторинг скорости сайта

### 🛡️ **Безопасность и утилиты**
- Защита от SQL-инъекций, XSS, CSRF
- Автоматические бэкапы базы данных
- SMTP-рассылки
- REST API для всех модулей
- Логирование действий

### 🤖 **Встроенный ИИ**
- **xAI Чат-Консультант** (Grok)
- **ChatGPT Консультант**
- **ИИ-генерация новостей и описаний товаров**
- Автоматические рекомендации по SEO и продажам

### 🌍 **Мультиязычность**
Полная поддержка:
- Русский
- Украинский
- English
- Norsk

---

## 🛠 Технический стек

- **PHP** 8.1+
- **MySQL** / MariaDB
- **Bootstrap 5.3** + современный glassmorphism
- **Font Awesome 6**
- **Redis** (опционально)
- **Tailwind CSS** (в новых блоках)
- Подготовленные Prepared Statements
- Полная защита от распространённых уязвимостей

---

## 🚀 Быстрая установка (3 минуты)

```bash
git clone https://github.com/Ruslan-Bilohash/cms.git
cd cms

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
