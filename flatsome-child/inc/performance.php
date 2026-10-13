<?php
/**
 * Tốc độ tải trang chủ: ảnh nền đầu trang (Largest Contentful Paint) tải sớm và đúng cỡ màn hình.
 *
 * Section đầu trang chủ của Flatsome in ảnh nền cỡ gốc 1920px, không có srcset, nên điện thoại phải tải
 * ảnh lớn nhất và trình duyệt chỉ tìm thấy ảnh sau khi đọc xong CSS. Ở đây:
 * - báo trước ảnh nền trong <head> (preload) kèm các cỡ ảnh, để trình duyệt tải ngay từ đầu;
 * - thêm srcset/sizes và fetchpriority="high" cho chính thẻ ảnh đó, bỏ lazy load của LiteSpeed.
 */

defined( 'ABSPATH' ) || exit;

/**
 * ID ảnh nền của section đầu tiên trên trang chủ (thuộc tính bg="…" trong UX Builder), 0 nếu không có.
 */
function tp_front_hero_image_id() {
	static $id = null;
	if ( null !== $id ) {
		return $id;
	}
	$id      = 0;
	$page_id = (int) get_option( 'page_on_front' );
	if ( $page_id && preg_match( '/\[section\b[^\]]*?\sbg="(\d+)"/', (string) get_post_field( 'post_content', $page_id ), $m ) ) {
		$id = (int) $m[1];
	}
	return $id;
}

add_action( 'wp_head', function () {
	$id = is_front_page() ? tp_front_hero_image_id() : 0;
	if ( ! $id || ! ( $src = wp_get_attachment_image_url( $id, 'full' ) ) ) {
		return;
	}
	$srcset = wp_get_attachment_image_srcset( $id, 'full' );
	printf(
		'<link rel="preload" as="image" href="%s"%s fetchpriority="high">' . "\n",
		esc_url( $src ),
		$srcset ? ' imagesrcset="' . esc_attr( $srcset ) . '" imagesizes="100vw"' : ''
	);
}, 2 );

add_filter( 'wp_get_attachment_image_attributes', function ( $attr, $attachment ) {
	static $done = false;
	if ( $done || is_admin() || ! is_front_page() || (int) $attachment->ID !== tp_front_hero_image_id() ) {
		return $attr;
	}
	if ( false === strpos( ' ' . ( $attr['class'] ?? '' ) . ' ', ' bg ' ) ) {
		return $attr;
	}
	$done   = true;
	$srcset = wp_get_attachment_image_srcset( $attachment->ID, 'full' );
	if ( $srcset ) {
		$attr['srcset'] = $srcset;
		$attr['sizes']  = '100vw';
	}
	$attr['fetchpriority'] = 'high';
	$attr['loading']       = 'eager';
	$attr['decoding']      = 'async';
	$attr['data-no-lazy']  = '1';
	$attr['class']        .= ' tp-no-lazy';
	return $attr;
}, 20, 2 );

/**
 * Flatsome xoá srcset/sizes khỏi ảnh nền section sau bộ lọc trên, nên thêm lại vào HTML của shortcode [section].
 * Thiếu srcset thì thẻ ảnh vẫn tải bản 1920px, lệch với bản cỡ nhỏ đã preload và điện thoại phải tải hai lần.
 */
add_filter( 'do_shortcode_tag', function ( $output, $tag ) {
	static $done = false;
	if ( $done || 'section' !== $tag || is_admin() || ! is_front_page() || ! ( $id = tp_front_hero_image_id() ) ) {
		return $output;
	}
	if ( ! preg_match( '/<img\b[^>]*\bclass="[^"]*\bbg\b[^"]*"[^>]*>/', $output, $m ) || false !== strpos( $m[0], 'srcset=' ) ) {
		return $output;
	}
	$srcset = wp_get_attachment_image_srcset( $id, 'full' );
	if ( ! $srcset || false === strpos( $m[0], (string) wp_get_attachment_image_url( $id, 'full' ) ) ) {
		return $output;
	}
	$done = true;
	$img  = preg_replace( '/^<img\b/', '<img srcset="' . esc_attr( $srcset ) . '" sizes="100vw"', $m[0] );
	return str_replace( $m[0], $img, $output );
}, 20, 2 );

