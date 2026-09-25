/* Chọn ảnh đại diện cho Loại hình / Khu vực trong trang quản trị. */
jQuery(function ($) {
	var frame;

	$(document).on('click', '.tp-term-image__pick', function (e) {
		e.preventDefault();
		var box = $(this).closest('.tp-term-image');

		frame = wp.media({ title: 'Chọn ảnh đại diện', button: { text: 'Dùng ảnh này' }, multiple: false, library: { type: 'image' } });
		frame.on('select', function () {
			var image = frame.state().get('selection').first().toJSON();
			var url = image.sizes && image.sizes.thumbnail ? image.sizes.thumbnail.url : image.url;
			box.find('input[name="tp_image_id"]').val(image.id);
			box.find('.tp-term-image__preview').html($('<img>', { src: url, alt: '', width: 120 }));
			box.find('.tp-term-image__remove').prop('hidden', false);
		});
		frame.open();
	});

	$(document).on('click', '.tp-term-image__remove', function (e) {
		e.preventDefault();
		var box = $(this).closest('.tp-term-image');
		box.find('input[name="tp_image_id"]').val('');
		box.find('.tp-term-image__preview').empty();
		$(this).prop('hidden', true);
	});

	// Trang "Thêm mới" gửi bằng AJAX: xoá ảnh đã chọn sau khi thêm xong.
	$(document).ajaxSuccess(function (event, xhr, settings) {
		if (settings.data && settings.data.indexOf('action=add-tag') !== -1 && xhr.responseText.indexOf('wp_error') === -1) {
			$('#addtag .tp-term-image__remove').trigger('click');
		}
	});
});
