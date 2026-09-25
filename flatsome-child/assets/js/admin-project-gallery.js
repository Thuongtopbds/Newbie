/* Chọn nhiều ảnh cho "Thư viện ảnh" trong trang sửa dự án. */
jQuery(function ($) {
	$(document).on('click', '.tp-gallery-field__pick', function (e) {
		e.preventDefault();
		var box = $(this).closest('.tp-gallery-field');
		var input = box.find('input[type="hidden"]');
		var ids = input.val() ? input.val().split(',') : [];

		var frame = wp.media({ title: 'Thư viện ảnh dự án', button: { text: 'Dùng các ảnh này' }, multiple: 'add', library: { type: 'image' } });
		frame.on('open', function () {
			var selection = frame.state().get('selection');
			ids.forEach(function (id) {
				var attachment = wp.media.attachment(id);
				attachment.fetch();
				selection.add(attachment);
			});
		});
		frame.on('select', function () {
			var list = box.find('.tp-gallery-field__list').empty();
			var picked = frame.state().get('selection').map(function (item) {
				var image = item.toJSON();
				var url = image.sizes && image.sizes.thumbnail ? image.sizes.thumbnail.url : image.url;
				list.append($('<img>', { src: url, alt: '', width: 72, height: 72, css: { objectFit: 'cover' } }));
				return image.id;
			});
			input.val(picked.join(','));
		});
		frame.open();
	});

	$(document).on('click', '.tp-gallery-field__clear', function (e) {
		e.preventDefault();
		var box = $(this).closest('.tp-gallery-field');
		box.find('input[type="hidden"]').val('');
		box.find('.tp-gallery-field__list').empty();
	});
});
