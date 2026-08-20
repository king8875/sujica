/**
 * 홈 '본당 소식' 탭.
 * 화살표 키로도 넘길 수 있게 해 둔다(role="tablist" 규약).
 */
document.addEventListener('DOMContentLoaded', function () {
	var list = document.querySelector('.home-tabs');
	if (!list) {
		return;
	}

	var tabs = Array.prototype.slice.call(list.querySelectorAll('.home-tab'));
	if (!tabs.length) {
		return;
	}

	function show(tab, focus) {
		tabs.forEach(function (one) {
			var panel = document.getElementById(one.getAttribute('aria-controls'));
			var on = one === tab;

			one.classList.toggle('is-active', on);
			one.setAttribute('aria-selected', on ? 'true' : 'false');
			one.setAttribute('tabindex', on ? '0' : '-1');

			if (panel) {
				panel.classList.toggle('is-active', on);
				panel.hidden = !on;
			}
		});

		if (focus) {
			tab.focus();
		}
	}

	tabs.forEach(function (tab) {
		tab.addEventListener('click', function () {
			show(tab, false);
		});

		tab.addEventListener('keydown', function (e) {
			var at = tabs.indexOf(tab);
			var next = null;

			if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
				next = tabs[(at + 1) % tabs.length];
			} else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
				next = tabs[(at - 1 + tabs.length) % tabs.length];
			} else if (e.key === 'Home') {
				next = tabs[0];
			} else if (e.key === 'End') {
				next = tabs[tabs.length - 1];
			}

			if (next) {
				e.preventDefault();
				show(next, true);
			}
		});
	});
});
