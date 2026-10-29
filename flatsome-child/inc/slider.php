<?php
/**
 * Slide ảnh có hàng ảnh nhỏ bên dưới (ảnh lớn, nút mũi tên cam, thanh chú thích, dải thumbnail).
 *
 * Hai cách dùng:
 * - Khối "Thư viện ảnh" (Gallery) của trình soạn thảo, thêm lớp CSS "tp-slider" ở mục Nâng cao.
 * - Shortcode [tp_slider ids="12,34,56"] (UX Builder hoặc khối Shortcode).
 */

defined( 'ABSPATH' ) || exit;

/**
 * @param array $items Mỗi phần tử: array( 'id' => ID ảnh, 'caption' => chú thích HTML ).
 */
function tp_slider_html( $items ) {
	$items = array_values( array_filter( $items, function ( $item ) {
		return ! empty( $item['id'] ) && wp_attachment_is_image( $item['id'] );
	} ) );
	if ( ! $items ) {
		return '';
	}

	$total  = count( $items );
	$slides = '';
	$thumbs = '';

	foreach ( $items as $i => $item ) {
		$id      = (int) $item['id'];
		$alt     = trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
		$caption = isset( $item['caption'] ) ? trim( wp_kses_post( $item['caption'] ) ) : '';
		if ( '' === $alt ) {
			$alt = wp_strip_all_tags( $caption ? $caption : get_the_title( $id ) );
		}

		$slides .= sprintf(
			'<figure class="tp-slider__slide" data-caption="%s">%s</figure>',
			esc_attr( $caption ),
			wp_get_attachment_image( $id, 'large', false, array(
				'alt'     => $alt,
				'sizes'   => '(max-width: 1200px) 100vw, 1200px',
				'loading' => $i ? 'lazy' : false,
			) )
		);
		$thumbs .= sprintf(
			'<button type="button" class="tp-slider__thumb%s" data-tp-slider-go="%d" aria-label="Xem ảnh %d"%s>%s</button>',
			$i ? '' : ' is-active',
			$i,
			$i + 1,
			$i ? '' : ' aria-current="true"',
			wp_get_attachment_image( $id, 'medium', false, array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '240px' ) )
		);
	}

	$first_caption = isset( $items[0]['caption'] ) ? trim( wp_kses_post( $items[0]['caption'] ) ) : '';
	$nav           = $total > 1;

	$html  = '<div class="tp-slider" data-tp-slider>';
	$html .= '<div class="tp-slider__stage">';
	$html .= '<div class="tp-slider__track" data-tp-slider-track tabindex="0" aria-label="Bộ ảnh, dùng phím mũi tên để chuyển ảnh">' . $slides . '</div>';
	if ( $nav ) {
		$html .= '<button type="button" class="tp-slider__arrow tp-slider__arrow--prev" data-tp-slider-step="-1" aria-label="Ảnh trước">' . tp_icon( 'prev', 26 ) . '</button>';
		$html .= '<button type="button" class="tp-slider__arrow tp-slider__arrow--next" data-tp-slider-step="1" aria-label="Ảnh sau">' . tp_icon( 'next', 26 ) . '</button>';
	}
	$html .= '</div>';
	$html .= '<div class="tp-slider__bar"><span class="tp-slider__caption" data-tp-slider-caption>' . $first_caption . '</span>';
	$html .= '<span class="tp-slider__count" data-tp-slider-count>1 / ' . $total . '</span></div>';
	if ( $nav ) {
		$html .= '<div class="tp-slider__thumbs" data-tp-slider-thumbs>' . $thumbs . '</div>';
	}
	$html .= '</div>';

	return $html;
}

// Khối Thư viện ảnh có lớp "tp-slider": thay HTML lưới ảnh mặc định bằng slide.
add_filter( 'render_block_core/gallery', function ( $content, $block ) {
	$class = isset( $block['attrs']['className'] ) ? $block['attrs']['className'] : '';
	if ( ! preg_match( '/(^|\s)tp-slider(\s|$)/', $class ) ) {
		return $content;
	}

	$items = array();
	foreach ( $block['innerBlocks'] as $inner ) {
		if ( 'core/image' !== $inner['blockName'] || empty( $inner['attrs']['id'] ) ) {
			continue;
		}
		$caption = preg_match( '#<figcaption[^>]*>(.*?)</figcaption>#s', $inner['innerHTML'], $m ) ? $m[1] : '';
		$items[] = array( 'id' => $inner['attrs']['id'], 'caption' => $caption );
	}

	// Gallery kiểu cũ (trước WordPress 5.9) lưu danh sách ID ngay trong khối.
	if ( ! $items && ! empty( $block['attrs']['ids'] ) ) {
		foreach ( (array) $block['attrs']['ids'] as $id ) {
			$items[] = array( 'id' => $id, 'caption' => wp_get_attachment_caption( $id ) );
		}
	}

	$html = tp_slider_html( $items );
	return $html ? $html : $content;
}, 10, 2 );

/**
 * [tp_slider ids="12,34,56"]
 */
add_shortcode( 'tp_slider', function ( $atts ) {
	$a     = shortcode_atts( array( 'ids' => '' ), $atts );
	$items = array();
	foreach ( wp_parse_id_list( $a['ids'] ) as $id ) {
		$items[] = array( 'id' => $id, 'caption' => wp_get_attachment_caption( $id ) );
	}
	return tp_slider_html( $items );
} );
