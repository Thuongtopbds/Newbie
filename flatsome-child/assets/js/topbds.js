/* Trang dự án: xem ảnh phóng to và mục lục đánh dấu mục đang đọc. */
(function () {
	var dialog = document.querySelector('[data-tp-lightbox]');

	if (dialog && typeof dialog.showModal === 'function') {
		var photos = JSON.parse(dialog.getAttribute('data-photos') || '[]');
		var img = dialog.querySelector('img');
		var count = dialog.querySelector('.tp-lightbox__count');
		var title = (document.querySelector('h1') || {}).textContent || '';
		var current = 0;

		var show = function (i) {
			current = (i + photos.length) % photos.length;
			img.src = photos[current];
			img.alt = title.trim() + ' – ảnh ' + (current + 1);
			count.textContent = (current + 1) + ' / ' + photos.length;
		};

		document.addEventListener('click', function (e) {
			var opener = e.target.closest('[data-tp-photo-open]');
			if (!opener || !photos.length) return;
			e.preventDefault();
			show(parseInt(opener.getAttribute('data-tp-photo-open'), 10) || 0);
			dialog.showModal();
		});
		dialog.addEventListener('click', function (e) {
			var step = e.target.closest('[data-tp-lightbox-step]');
			if (step) { show(current + parseInt(step.getAttribute('data-tp-lightbox-step'), 10)); return; }
			if (e.target === dialog || e.target.closest('[data-tp-lightbox-close]')) dialog.close();
		});
		dialog.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowRight') show(current + 1);
			if (e.key === 'ArrowLeft') show(current - 1);
		});
		if (photos.length < 2) dialog.querySelectorAll('[data-tp-lightbox-step]').forEach(function (b) { b.hidden = true; });
	}

	var toc = document.querySelector('[data-tp-toc]');
	if (toc && 'IntersectionObserver' in window) {
		var links = Array.prototype.slice.call(toc.querySelectorAll('a'));
		var targets = links.map(function (a) { return document.getElementById(a.getAttribute('href').slice(1)); }).filter(Boolean);
		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				links.forEach(function (a) {
					var active = a.getAttribute('href') === '#' + entry.target.id;
					a.classList.toggle('is-active', active);
					if (active && toc.scrollWidth > toc.clientWidth) toc.scrollTo({ left: a.offsetLeft - 16, behavior: 'smooth' });
				});
			});
		}, { rootMargin: '-150px 0px -60% 0px' });
		targets.forEach(function (t) { observer.observe(t); });
	}
})();
