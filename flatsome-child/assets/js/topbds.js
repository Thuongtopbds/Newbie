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
	var tocWrap = document.querySelector('[data-tp-toc-wrap]');

	// Thanh mục lục dài hơn màn hình: nút mũi tên, kéo bằng chuột, lăn chuột để cuộn ngang.
	if (toc && tocWrap) {
		var prev = tocWrap.querySelector('[data-tp-toc-step="-1"]');
		var next = tocWrap.querySelector('[data-tp-toc-step="1"]');
		var update = function () {
			var max = toc.scrollWidth - toc.clientWidth;
			var canPrev = toc.scrollLeft > 2;
			var canNext = toc.scrollLeft < max - 2;
			prev.hidden = !canPrev;
			next.hidden = !canNext;
			tocWrap.classList.toggle('can-prev', canPrev);
			tocWrap.classList.toggle('can-next', canNext);
		};
		tocWrap.addEventListener('click', function (e) {
			var btn = e.target.closest('[data-tp-toc-step]');
			if (!btn) return;
			toc.scrollBy({ left: parseInt(btn.getAttribute('data-tp-toc-step'), 10) * toc.clientWidth * 0.7, behavior: 'smooth' });
		});
		toc.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update);
		update();

		toc.addEventListener('wheel', function (e) {
			if (toc.scrollWidth <= toc.clientWidth || Math.abs(e.deltaX) > Math.abs(e.deltaY)) return;
			e.preventDefault();
			toc.scrollLeft += e.deltaY;
		}, { passive: false });

		var drag = null;
		toc.addEventListener('pointerdown', function (e) {
			if (e.pointerType !== 'mouse' || e.button !== 0) return;
			drag = { x: e.clientX, left: toc.scrollLeft, moved: false };
		});
		window.addEventListener('pointermove', function (e) {
			if (!drag) return;
			var dx = e.clientX - drag.x;
			if (!drag.moved && Math.abs(dx) < 5) return;
			drag.moved = true;
			toc.classList.add('is-dragging');
			toc.scrollLeft = drag.left - dx;
		});
		window.addEventListener('pointerup', function () {
			if (!drag) return;
			var moved = drag.moved;
			drag = null;
			// Bỏ cú click ngay sau khi kéo để không nhảy tới mục vừa thả chuột lên.
			setTimeout(function () { toc.classList.remove('is-dragging'); }, moved ? 50 : 0);
		});
		toc.addEventListener('dragstart', function (e) { e.preventDefault(); });
	}

	if (toc && 'IntersectionObserver' in window) {
		var links = Array.prototype.slice.call(toc.querySelectorAll('a'));
		var targets = links.map(function (a) { return document.getElementById(a.getAttribute('href').slice(1)); }).filter(Boolean);
		var observer = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				links.forEach(function (a) {
					var active = a.getAttribute('href') === '#' + entry.target.id;
					a.classList.toggle('is-active', active);
					if (active && toc.scrollWidth > toc.clientWidth) toc.scrollTo({ left: a.offsetLeft - (toc.clientWidth - a.offsetWidth) / 2, behavior: 'smooth' });
				});
			});
		}, { rootMargin: '-150px 0px -60% 0px' });
		targets.forEach(function (t) { observer.observe(t); });
	}
})();
