document.addEventListener('DOMContentLoaded', function () {
    const swiper = new Swiper('.swiper-container', {
        loop: true, // или динамически через PHP
        autoplay: {
            delay: 5000, // должно быть динамическим через PHP
            disableOnInteraction: false
        },
        speed: 600,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        },
        slidesPerView: 1,
        spaceBetween: 0
    });
});

// Проверяем, есть ли уже согласие
if (!document.cookie.includes('cookie_consent')) {
    document.getElementById('cookie-consent').classList.remove('hidden');
}

function acceptCookies() {
    document.cookie = 'cookie_consent=accepted; max-age=31536000; path=/';
    document.getElementById('cookie-consent').classList.add('hidden');
    // Здесь можно активировать аналитические или рекламные скрипты
}

function declineCookies() {
    document.cookie = 'cookie_consent=declined; max-age=31536000; path=/';
    document.getElementById('cookie-consent').classList.add('hidden');
}

function openSettings() {
    // Здесь можно открыть модальное окно с настройками cookies
    alert('Настройки cookies пока в разработке. Вы можете управлять cookies через настройки браузера.');
}
