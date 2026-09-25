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
