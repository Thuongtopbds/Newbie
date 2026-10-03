<?php
/**
 * Hai banner dọc ghim hai bên nội dung (trang chủ, trang dự án).
 *
 * Banner nằm trong khoảng trống hai bên khung nội dung. Độ rộng banner và mốc màn hình tối thiểu được tính
 * theo độ rộng khung (Flatsome → Layout → Container Width): mặc định chọn cỡ lớn nhất vẫn vừa màn hình
 * 1536px (laptop Full HD scale 125%, màn 4K scale 250%). Ảnh khai báo qua <picture><source media>, nên màn
 * hình nhỏ hơn mốc không tải ảnh banner. Khách có thể bấm ✕ để ẩn trong phiên truy cập.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {
	$wp_customize->add_section( 'tp_side_banners', array(
		'title'       => 'TOPBDS – Banner hai bên',
		'priority'    => 31,
		'description' => tp_side_banner_help(),
	) );

	$wp_customize->add_setting( 'tp_side_width', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'tp_side_width', array(
		'label'   => 'Độ rộng banner',
		'section' => 'tp_side_banners',
		'type'    => 'select',
		'choices' => array( 0 => 'Tự động (vừa màn hình 1536px)', 160 => '160px', 140 => '140px', 120 => '120px' ),
	) );
	$wp_customize->add_setting( 'tp_side_site_width', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'tp_side_site_width', array(
		'label'       => 'Độ rộng khung nội dung (px)',
		'description' => 'Để 0: lấy theo Flatsome → Theme Options → Layout → Container Width.',
		'section'     => 'tp_side_banners',
		'type'        => 'number',
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
 * Kích thước tính toán: [độ rộng khung nội dung, độ rộng banner, mốc màn hình tối thiểu].
 * Banner cách khung 16px và cách mép màn hình ít nhất 16px.
 */
function tp_side_banner_layout() {
	$site = (int) get_theme_mod( 'tp_side_site_width', 0 ) ?: (int) get_theme_mod( 'site_width', 1080 ) ?: 1080;
	$w    = (int) get_theme_mod( 'tp_side_width', 0 );
	if ( ! in_array( $w, array( 120, 140, 160 ), true ) ) {
		$w = 120;
		foreach ( array( 160, 140 ) as $try ) {
			if ( $site + 2 * ( $try + 32 ) <= 1536 ) {
				$w = $try;
				break;
			}
		}
	}
	return array( $site, $w, $site + 2 * ( $w + 32 ) );
}

function tp_side_banner_help() {
	list( $site, $w, $bp ) = tp_side_banner_layout();
	return sprintf(
		'Banner dọc ghim hai bên khung nội dung (%1$dpx). Hiện ở độ rộng %2$dpx trên màn hình từ %3$dpx trở lên; màn hình nhỏ hơn và điện thoại không hiện, không tải ảnh. Ảnh nên rộng %4$dpx (gấp đôi để nét), cao khoảng %5$d–%6$dpx, WebP, dưới 80 KB.',
		$site, $w, $bp, $w * 2, $w * 6, $w * 7
	);
}

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

	list( $site, $bw, $bp ) = tp_side_banner_layout();
	$html = '';
	foreach ( array( 'left' => 'trái', 'right' => 'phải' ) as $side => $label ) {
		$id  = (int) get_theme_mod( "tp_side_{$side}_img", 0 );
		$src = $id ? wp_get_attachment_image_src( $id, 'full' ) : false;
		if ( ! $src ) {
			continue;
		}
		$link = get_theme_mod( "tp_side_{$side}_link", '' );
		$alt  = get_post_meta( $id, '_wp_attachment_image_alt', true ) ?: 'Quảng cáo dự án';
		$h    = $src[1] ? (int) round( $bw * $src[2] / $src[1] ) : $bw * 4;

		// Ảnh thật chỉ nằm trong <source media>; <img> giữ ảnh 1×1 trong suốt nên màn hình nhỏ không tải gì.
		$picture = sprintf(
			'<picture><source media="(min-width: %4$dpx)" srcset="%1$s"><img src="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==" width="%5$d" height="%2$d" alt="%3$s" class="tp-no-lazy" data-no-lazy="1" decoding="async"></picture>',
			esc_url( $src[0] ),
			$h,
			esc_attr( $alt ),
			$bp,
			$bw
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
	// Vị trí phụ thuộc độ rộng khung, nên in CSS bố cục ngay tại đây (số nguyên đã tính ở PHP).
	printf(
		'<style>@media (min-width:%1$dpx){.tp-side{display:block;width:%2$dpx}.tp-side img{width:%2$dpx}.tp-side--left{left:calc(50%% - %3$dpx)}.tp-side--right{right:calc(50%% - %3$dpx)}}</style>',
		$bp,
		$bw,
		(int) ( $site / 2 + 16 + $bw )
	);
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
