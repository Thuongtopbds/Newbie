<?php
/**
 * Hàm dựng HTML dùng chung: icon SVG, thẻ dự án, ô loại hình/khu vực.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Icon SVG nội tuyến (nét theo bộ Lucide), đổi màu theo `color` của phần tử cha.
 */
function tp_icon( $name, $size = 18 ) {
	static $paths = array(
		'search'   => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
		'pin'      => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
		'tag'      => '<path d="M12.6 2.6A2 2 0 0 0 11.2 2H4a2 2 0 0 0-2 2v7.2a2 2 0 0 0 .6 1.4l8.7 8.7a2.4 2.4 0 0 0 3.4 0l6.6-6.6a2.4 2.4 0 0 0 0-3.4Z"/><circle cx="7.5" cy="7.5" r="1.2"/>',
		'building' => '<path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4M10 10h4M10 14h4M10 18h4"/>',
		'grid'     => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>',
		'arrow'    => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
		'heart'    => '<path d="M19 14c1.5-1.5 3-3.2 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.8 0-3 .5-4.5 2-1.5-1.5-2.7-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4 3 5.5l7 7Z"/>',
		'phone'    => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/>',
		'chat'     => '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/><path d="M8 12h.01M12 12h.01M16 12h.01"/>',
		'shield'   => '<path d="M20 13c0 5-3.5 7.5-7.7 9a1 1 0 0 1-.6 0C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.2-2.7a1.2 1.2 0 0 1 1.6 0C14.5 3.8 17 5 19 5a1 1 0 0 1 1 1Z"/><path d="m9 12 2 2 4-4"/>',
		'headset'  => '<path d="M3 11h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5Zm0 0a9 9 0 1 1 18 0m0 0v5a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3Z"/><path d="M21 16v2a4 4 0 0 1-4 4h-5"/>',
		'users'    => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m16 11 2 2 4-4"/>',
		'calendar' => '<rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
		'mail'     => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-9 5.7a2 2 0 0 1-2 0L2 7"/>',
		'bulb'     => '<path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6M10 22h4"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg class="tp-icon tp-icon--%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $name ),
		(int) $size,
		$paths[ $name ]
	);
}

/**
 * Ảnh của bài viết/dự án, có khung giữ chỗ khi chưa có ảnh đại diện.
 */
function tp_post_image( $post_id, $size ) {
	if ( has_post_thumbnail( $post_id ) ) {
		return get_the_post_thumbnail( $post_id, $size, array( 'alt' => get_the_title( $post_id ) ) );
	}
	return '<span class="tp-noimg" aria-hidden="true"></span>';
}

/**
 * Tên các mục phân loại của một dự án, nối bằng " | ".
 */
function tp_term_names( $post_id, $taxonomy ) {
	$terms = get_the_terms( $post_id, $taxonomy );
	return $terms && ! is_wp_error( $terms ) ? implode( ' | ', wp_list_pluck( $terms, 'name' ) ) : '';
}

/**
 * Nhãn góc ảnh: "Hot" nếu được tick, nếu không thì theo Trạng thái (VD: "Đang mở bán").
 */
function tp_project_badge( $post_id ) {
	if ( get_post_meta( $post_id, 'tp_hot', true ) ) {
		return '<span class="tp-badge tp-badge--hot">Hot</span>';
	}
	$status = get_the_terms( $post_id, 'trang_thai' );
	if ( $status && ! is_wp_error( $status ) ) {
		return '<span class="tp-badge">' . esc_html( $status[0]->name ) . '</span>';
	}
	return '';
}

/**
 * Thẻ dự án.
 *
 * @param array $args badge (bool), button (bool), heading (thẻ tiêu đề: h3/h2).
 */
function tp_project_card( $post_id, $args = array() ) {
	$args = wp_parse_args( $args, array( 'badge' => true, 'button' => true, 'heading' => 'h3' ) );
	$tag  = in_array( $args['heading'], array( 'h2', 'h3', 'h4' ), true ) ? $args['heading'] : 'h3';

	$link     = get_permalink( $post_id );
	$title    = get_the_title( $post_id );
	$location = get_post_meta( $post_id, 'tp_dia_chi', true ) ?: tp_term_names( $post_id, 'khu_vuc' );
	$price    = get_post_meta( $post_id, 'tp_gia', true );
	$products = get_post_meta( $post_id, 'tp_san_pham', true ) ?: tp_term_names( $post_id, 'loai_hinh' );

	ob_start();
	?>
	<article class="tp-card">
		<a class="tp-card__media" href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true">
			<?php echo tp_post_image( $post_id, 'tp-card' ); ?>
		</a>
		<?php if ( $args['badge'] ) { echo tp_project_badge( $post_id ); } ?>
		<button type="button" class="tp-fav" data-tp-fav="<?php echo (int) $post_id; ?>" aria-pressed="false" aria-label="Lưu dự án <?php echo esc_attr( $title ); ?>">
			<?php echo tp_icon( 'heart', 16 ); ?>
		</button>
		<div class="tp-card__body">
			<<?php echo $tag; ?> class="tp-card__title">
				<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a>
			</<?php echo $tag; ?>>
			<?php if ( $location ) : ?>
				<p class="tp-card__row"><?php echo tp_icon( 'pin', 14 ); ?><span><?php echo esc_html( $location ); ?></span></p>
			<?php endif; ?>
			<?php if ( $price ) : ?>
				<p class="tp-card__row tp-card__price"><?php echo tp_icon( 'tag', 14 ); ?><span><?php echo esc_html( $price ); ?></span></p>
			<?php endif; ?>
			<?php if ( $products ) : ?>
				<p class="tp-card__row tp-card__products"><?php echo tp_icon( 'grid', 14 ); ?><span><?php echo esc_html( $products ); ?></span></p>
			<?php endif; ?>
			<?php if ( $args['button'] ) : ?>
				<a class="tp-card__btn" href="<?php echo esc_url( $link ); ?>">Xem chi tiết<span class="screen-reader-text"> <?php echo esc_html( $title ); ?></span> <?php echo tp_icon( 'arrow', 14 ); ?></a>
			<?php endif; ?>
		</div>
	</article>
	<?php
	return ob_get_clean();
}

/**
 * Ô ảnh của một Loại hình / Khu vực, kèm số dự án.
 */
function tp_term_tile( WP_Term $term, $arrow = true ) {
	$image_id = (int) get_term_meta( $term->term_id, 'tp_image_id', true );
	$image    = $image_id ? wp_get_attachment_image( $image_id, 'tp-tile', false, array( 'alt' => '' ) ) : '<span class="tp-noimg" aria-hidden="true"></span>';

	return sprintf(
		'<a class="tp-tile" href="%1$s">%2$s<span class="tp-tile__body"><span class="tp-tile__name">%3$s</span><span class="tp-tile__count">%4$s dự án</span></span>%5$s</a>',
		esc_url( get_term_link( $term ) ),
		$image,
		esc_html( $term->name ),
		esc_html( number_format_i18n( $term->count ) ),
		$arrow ? '<span class="tp-tile__arrow">' . tp_icon( 'arrow', 16 ) . '</span>' : ''
	);
}
