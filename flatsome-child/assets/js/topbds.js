/* TOPBDS: nút lưu dự án (trái tim). Danh sách lưu nằm trong trình duyệt của khách (localStorage). */
(function () {
	var KEY = 'tp_saved_projects';

	function read() {
		try {
			return JSON.parse(localStorage.getItem(KEY)) || [];
		} catch (e) {
			return [];
		}
	}

	function write(ids) {
		try {
			localStorage.setItem(KEY, JSON.stringify(ids));
		} catch (e) {}
	}

	function sync() {
		var saved = read();
		document.querySelectorAll('[data-tp-fav]').forEach(function (btn) {
			btn.setAttribute('aria-pressed', saved.indexOf(btn.getAttribute('data-tp-fav')) !== -1 ? 'true' : 'false');
		});
	}

	document.addEventListener('click', function (e) {
		var btn = e.target.closest('[data-tp-fav]');
		if (!btn) return;
		var id = btn.getAttribute('data-tp-fav');
		var saved = read();
		var i = saved.indexOf(id);
		if (i === -1) saved.push(id); else saved.splice(i, 1);
		write(saved);
		sync();
	});

	sync();
})();

/* Trang dự án: thư viện ảnh (đổi ảnh lớn + xem phóng to) và mục lục đánh dấu mục đang đọc. */
(function () {
	var gallery = document.querySelector('[data-tp-gallery]');
	var dialog = document.querySelector('[data-tp-lightbox]');

	if (gallery) {
		var main = gallery.querySelector('[data-tp-gallery-open]');
		var mainImg = main.querySelector('img');
		var thumbs = Array.prototype.slice.call(gallery.querySelectorAll('.tp-gallery__thumb'));
		var urls = thumbs.length ? thumbs.map(function (t) { return t.getAttribute('data-full'); }) : [main.getAttribute('href')];
		var current = 0;

		var show = function (i) {
			current = i;
			var thumb = thumbs[i];
			if (thumb) {
				mainImg.removeAttribute('srcset');
				mainImg.src = thumb.getAttribute('data-src');
				if (thumb.getAttribute('data-srcset')) mainImg.srcset = thumb.getAttribute('data-srcset');
				main.href = urls[i];
				thumbs.forEach(function (t) { t.classList.toggle('is-active', t === thumb); });
			}
		};

		thumbs.forEach(function (thumb, i) {
			thumb.addEventListener('click', function () { show(i); });
		});

		if (dialog && typeof dialog.showModal === 'function') {
			var big = dialog.querySelector('img');
			var open = function (i) {
				current = (i + urls.length) % urls.length;
				big.src = urls[current];
				big.alt = mainImg.alt + ' – ảnh ' + (current + 1) + '/' + urls.length;
			};
			main.addEventListener('click', function (e) {
				e.preventDefault();
				open(current);
				dialog.showModal();
			});
			dialog.addEventListener('click', function (e) {
				var step = e.target.closest('[data-tp-lightbox-step]');
				if (step) { open(current + parseInt(step.getAttribute('data-tp-lightbox-step'), 10)); return; }
				if (e.target === dialog || e.target.closest('[data-tp-lightbox-close]')) dialog.close();
			});
			dialog.addEventListener('keydown', function (e) {
				if (e.key === 'ArrowRight') open(current + 1);
				if (e.key === 'ArrowLeft') open(current - 1);
			});
			dialog.addEventListener('close', function () { show(current); });
			if (urls.length < 2) dialog.querySelectorAll('[data-tp-lightbox-step]').forEach(function (b) { b.hidden = true; });
		}
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
