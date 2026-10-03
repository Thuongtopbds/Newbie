<?php
/**
 * Hai banner dọc ghim hai bên nội dung (trang chủ, trang dự án).
 *
 * Chỉ hiện khi màn hình đủ rộng (≥ 1600px) để không đè lên khung nội dung 1200px. Ảnh khai báo qua
 * <picture><source media>, nên điện thoại và laptop nhỏ không tải ảnh banner. Khách có thể bấm ✕ để ẩn
 * trong phiên truy cập.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {
	$wp_customize->add_section( 'tp_side_banners', array(
		'title'       => 'TOPBDS – Banner hai bên',
		'priority'    => 31,
		'description' => 'Banner dọc ghim hai bên, chỉ hiện trên màn hình rộng từ 1600px. Ảnh nên rộng 320px (hiển thị 160px), cao 900–1200px, định dạng WebP, dưới 80 KB.',
	) );

	foreach ( array( 'left' => 'trái', 'right' => 'phải' ) as $side => $label ) {
		$wp_customize->add_setting( "tp_side_{$side}_img", array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
		$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, "tp_side_{$side}_img", array(
			'label'     => "Ảnh banner bên {$label}",
			'section'   => 'tp_side_banners',
			'mime_type' => 'image',
		) ) );
		$wp_customize->add_setting( "tp_side_{$side}_link", array( 'default' => '', 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( "tp_side_{$side}_link", array(
			'label'       => "Link khi bấm banner bên {$label}",
			'description' => 'VD: /du-an/an-quy-villa-duong-noi/ hoặc https://zalo.me/0977113009',
			'section'     => 'tp_side_banners',
			'type'        => 'url',
		) );
	}

	foreach ( array(
		'tp_side_on_home'    => array( 'Hiện ở trang chủ', true ),
		'tp_side_on_project' => array( 'Hiện ở trang chi tiết dự án', true ),
		'tp_side_on_archive' => array( 'Hiện ở trang danh sách dự án, loại hình, khu vực', false ),
		'tp_side_on_post'    => array( 'Hiện ở bài viết tin tức', false ),
	) as $id => list( $label, $default ) ) {
		$wp_customize->add_setting( $id, array( 'default' => $default, 'sanitize_callback' => 'wp_validate_boolean' ) );
		$wp_customize->add_control( $id, array( 'label' => $label, 'section' => 'tp_side_banners', 'type' => 'checkbox' ) );
	}
} );

/**
 * Trang hiện tại có được bật banner không.
 */
function tp_side_banners_enabled() {
	if ( is_front_page() ) {
		return (bool) get_theme_mod( 'tp_side_on_home', true );
	}
	if ( is_singular( 'du_an' ) ) {
		return (bool) get_theme_mod( 'tp_side_on_project', true );
	}
	if ( is_post_type_archive( 'du_an' ) || is_tax( array( 'loai_hinh', 'khu_vuc', 'trang_thai' ) ) ) {
		return (bool) get_theme_mod( 'tp_side_on_archive', false );
	}
	if ( is_singular( 'post' ) ) {
		return (bool) get_theme_mod( 'tp_side_on_post', false );
	}
	return false;
}

add_action( 'wp_footer', function () {
	if ( is_admin() || ! tp_side_banners_enabled() ) {
		return;
	}

	$html = '';
	foreach ( array( 'left' => 'trái', 'right' => 'phải' ) as $side => $label ) {
		$id  = (int) get_theme_mod( "tp_side_{$side}_img", 0 );
		$src = $id ? wp_get_attachment_image_src( $id, 'full' ) : false;
		if ( ! $src ) {
			continue;
		}
		$link = get_theme_mod( "tp_side_{$side}_link", '' );
		$alt  = get_post_meta( $id, '_wp_attachment_image_alt', true ) ?: 'Quảng cáo dự án';
		$h    = $src[1] ? (int) round( 160 * $src[2] / $src[1] ) : 600;

		// Ảnh thật chỉ nằm trong <source media>; <img> giữ ảnh 1×1 trong suốt nên màn hình nhỏ không tải gì.
		$picture = sprintf(
			'<picture><source media="(min-width: 1600px)" srcset="%1$s"><img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" width="160" height="%2$d" alt="%3$s" class="tp-no-lazy" data-no-lazy="1" decoding="async"></picture>',
			esc_url( $src[0] ),
			$h,
			esc_attr( $alt )
		);
		if ( $link ) {
			$is_external = wp_parse_url( $link, PHP_URL_HOST ) && wp_parse_url( $link, PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST );
			$picture     = sprintf( '<a href="%s"%s>%s</a>', esc_url( $link ), $is_external ? ' target="_blank" rel="noopener sponsored"' : '', $picture );
		}

		$html .= sprintf(
			'<aside class="tp-side tp-side--%1$s" aria-label="Banner bên %2$s">%3$s<button type="button" class="tp-side__close" aria-label="Đóng banner">&times;</button></aside>',
			esc_attr( $side ),
			esc_attr( $label ),
			$picture
		);
	}

	if ( ! $html ) {
		return;
	}
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- các phần đã escape ở trên.
	?>
	<script>
	(function () {
		var key = 'tpSideClosed';
		try { if (sessionStorage.getItem(key)) document.documentElement.classList.add('tp-side-closed'); } catch (e) {}
		document.addEventListener('click', function (e) {
			if (!e.target.closest('.tp-side__close')) return;
			document.documentElement.classList.add('tp-side-closed');
			try { sessionStorage.setItem(key, '1'); } catch (e) {}
		});
	})();
	</script>
	<?php
}, 20 );
