document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.carousel');
    const carouselInner = carousel.querySelector('.carousel-inner');
    const carouselItems = carousel.querySelectorAll('.carousel-item');
    const prevButton = carousel.querySelector('.carousel-prev');
    const nextButton = carousel.querySelector('.carousel-next');
    const itemWidth = carouselItems[0].offsetWidth;
    let currentIndex = 0;

    prevButton.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + carouselItems.length) % carouselItems.length;
        carouselInner.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
    });

    nextButton.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % carouselItems.length;
        carouselInner.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
    });

    // Autoplay
    let autoplayInterval = setInterval(() => {
        currentIndex = (currentIndex + 1) % carouselItems.length;
        carouselInner.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
    }, 5000);

    // Pause autoplay on hover
    carousel.addEventListener('mouseenter', () => {
        clearInterval(autoplayInterval);
    });

    // Resume autoplay on mouse leave
    carousel.addEventListener('mouseleave', () => {
        autoplayInterval = setInterval(() => {
            currentIndex = (currentIndex + 1) % carouselItems.length;
            carouselInner.style.transform = `translateX(-${currentIndex * itemWidth}px)`;
        }, 5000);
    });
});
