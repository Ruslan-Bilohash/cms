<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/db.php';

// Получаем изображения для карусели брендов
$stmt = $conn->prepare('SELECT image_path, link FROM brand_carousel WHERE is_active = 1');
$stmt->execute();
$brands = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<style>
.brand-carousel {
    padding: 20px 0;
    overflow-x: hidden; /* Убираем ползунок прокрутки */
    position: relative;
}

.brand-carousel-inner {
    display: flex;
    gap: 15px;
    white-space: nowrap;
}

.brand-item {
    flex: 0 0 auto;
    width: 200px;
    height: 100px; /* Фиксированная высота контейнера */
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
}

.brand-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.brand-image:hover {
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .brand-item {
        width: 150px;
        height: 80px;
    }
}
</style>

<div class="container">
    <section class="brand-carousel">
        <div class="brand-carousel-inner" id="brandCarousel">
		
            <?php if (empty($brands)): ?>
                <p>Нет активных брендов в карусели.</p>
            <?php else: ?>
                <?php foreach ($brands as $brand): ?>
                    <div class="brand-item">
                        <?php if ($brand['link']): ?>
                            <a href="<?php echo htmlspecialchars($brand['link']); ?>" target="_blank">
                                <img src="<?php echo htmlspecialchars($brand['image_path']); ?>" class="brand-image" alt="Brand">
                            </a>
                        <?php else: ?>
                            <img src="<?php echo htmlspecialchars($brand['image_path']); ?>" class="brand-image" alt="Brand">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.getElementById('brandCarousel');
    if (!carousel || carousel.children.length === 0) {
        console.warn('Карусель пуста или не найдена');
        return;
    }

    const items = Array.from(carousel.children);
    const itemWidth = items[0].offsetWidth + 15; // Ширина элемента + gap
    const speed = 1; // Пиксели в кадр (регулирует скорость прокрутки)

    // Дублируем элементы для бесконечности
    items.forEach(item => {
        const clone = item.cloneNode(true);
        carousel.appendChild(clone);
    });

    let position = 0;

    function scrollCarousel() {
        position -= speed; // Двигаем влево
        carousel.style.transform = `translateX(${position}px)`;

        // Когда первый элемент полностью уходит за левый край, перемещаем его в конец
        if (Math.abs(position) >= itemWidth) {
            position += itemWidth; // Сдвигаем позицию
            carousel.appendChild(carousel.firstElementChild); // Перемещаем первый элемент в конец
            carousel.style.transform = `translateX(${position}px)`; // Обновляем позицию
        }

        requestAnimationFrame(scrollCarousel); // Бесконечный цикл
    }

    // Запускаем прокрутку
    requestAnimationFrame(scrollCarousel);

    // При наведении можно остановить (опционально)
    carousel.addEventListener('mouseenter', () => {
        // Можно добавить паузу, убрав requestAnimationFrame
    });
    carousel.addEventListener('mouseleave', () => {
        requestAnimationFrame(scrollCarousel);
    });
});
</script>