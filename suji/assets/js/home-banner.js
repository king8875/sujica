document.addEventListener('DOMContentLoaded', function () {
	var el = document.querySelector('.home-banner-swiper');
	if (!el || typeof Swiper === 'undefined') {
		return;
	}

	new Swiper(el, {
		loop: el.querySelectorAll('.swiper-slide').length > 1,
		autoplay: {
			delay: 4000,
			disableOnInteraction: false,
		},
		pagination: {
			el: '.home-banner-swiper .swiper-pagination',
			clickable: true,
		},
		navigation: {
			nextEl: '.home-banner-swiper .swiper-button-next',
			prevEl: '.home-banner-swiper .swiper-button-prev',
		},
	});
});
