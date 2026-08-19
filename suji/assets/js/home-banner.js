document.addEventListener('DOMContentLoaded', function () {
	var el = document.querySelector('.home-banner-swiper');
	if (!el || typeof Swiper === 'undefined') {
		return;
	}

	var count = el.querySelectorAll('.swiper-slide').length;

	// Swiper 11 의 loop 는 슬라이드를 복제해 돌리는데, 2장뿐이면 복제를 만들지
	// 못하고 조용히 마지막 장에 멈춘다(넘김 버튼도 먹지 않는다). 장수가 적을
	// 때는 마지막에서 처음으로 되돌아가는 rewind 를 쓴다.
	new Swiper(el, {
		loop: count > 2,
		rewind: count === 2,
		autoplay: count > 1 ? {
			delay: 4000,
			disableOnInteraction: false,
		} : false,
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
