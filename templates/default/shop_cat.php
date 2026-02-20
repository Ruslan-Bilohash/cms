<?php
// Получение всех категорий для карусели
$categories_stmt = $conn->prepare('SELECT id, name FROM shop_categories WHERE status = 1 ORDER BY sort_order ASC');
$categories_stmt->execute();
$categories = $categories_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$categories_stmt->close();
?>

<style>
.carousel-categories {
    padding: 20px 0;
    overflow: hidden;
    position: relative;
}

.carousel-inner {
    display: flex;
    gap: 15px;
}

.category-item {
    flex: 0 0 auto;
    width: 150px;
    text-align: center;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
}

.category-item:hover {
    background: #e9ecef;
}

.category-name {
    font-size: 14px;
    color: #333;
    text-decoration: none;
    display: block;
    margin: 0;
}

.carousel-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px;
    height: 40px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.carousel-nav:hover {
    background: #f0f0f0;
}

.carousel-prev {
    left: 10px;
}

.carousel-next {
    right: 10px;
}

.carousel-nav i {
    font-size: 18px;
    color: #666;
}

@media (max-width: 768px) {
    .category-item {
        width: 120px;
        padding: 10px;
    }
    
    .category-name {
        font-size: 12px;
    }
    
    .carousel-nav {
        width: 30px;
        height: 30px;
    }
    
    .carousel-nav i {
        font-size: 14px;
    }
}
</style>

<div class="container">
    <section class="carousel-categories">
        <button class="carousel-nav carousel-prev"><i class="fas fa-chevron-left"></i></button>
        <div class="carousel-inner" id="categoriesCarousel">
            <?php foreach ($categories as $category): ?>
                <div class="category-item">
                    <a href="/shop/category?id=<?php echo (int)$category['id']; ?>" class="category-name">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-nav carousel-next"><i class="fas fa-chevron-right"></i></button>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.getElementById('categoriesCarousel');
    const prevBtn = document.querySelector('.carousel-prev');
    const nextBtn = document.querySelector('.carousel-next');
    const itemWidth = 165;

    prevBtn.addEventListener('click', () => {
        carousel.scrollBy({ left: -itemWidth * 2, behavior: 'smooth' });
    });

    nextBtn.addEventListener('click', () => {
        carousel.scrollBy({ left: itemWidth * 2, behavior: 'smooth' });
    });

    const items = carousel.children;
    let totalWidth = 0;
    for (let item of items) {
        totalWidth += item.offsetWidth + 15;
    }
    carousel.style.minWidth = `${totalWidth}px`;
});
</script>