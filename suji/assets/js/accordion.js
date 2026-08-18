document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('.accordion-header').forEach(function (button) {
		button.addEventListener('click', function () {
			var item = button.closest('.accordion-item');
			var isOpen = button.getAttribute('aria-expanded') === 'true';

			button.setAttribute('aria-expanded', String(!isOpen));
			item.classList.toggle('is-open', !isOpen);
		});
	});
});
