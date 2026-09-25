<?php
/**
 * Shortcode cho các khối lấy dữ liệu từ WordPress (dự án, loại hình, khu vực, tin tức)
 * và vài khối nhỏ dùng chung (tiêu đề mục, nút liên hệ, icon).
 *
 * Tất cả đều được đăng ký làm phần tử UX Builder trong inc/ux-builder.php.
 */

defined( 'ABSPATH' ) || exit;

function tp_bool( $value ) {
	return ! in_array( strtolower( (string) $value ), array( '', '0', 'no', 'false', 'off' ), true );
}

function tp_slugs( $value ) {
	return array_filter( array_map( 'sanitize_title', explode( ',', (string) $value ) ) );
}

/**
 * [tp_heading title="Dự án nổi bật" sub="..." link="/du-an/" link_text="Xem tất cả dự án"]
 */
add_shortcode( 'tp_heading', function ( $atts ) {
	$a = shortcode_atts( array(
		'title'     => '',
		'sub'       => '',
		'link'      => '',
		'link_text' => 'Xem tất cả',
		'tag'       => 'h2',
		'light'     => 'no',
	), $atts );

	$tag  = in_array( $a['tag'], array( 'h1', 'h2', 'h3' ), true ) ? $a['tag'] : 'h2';
	$html = '<div class="tp-head' . ( tp_bool( $a['light'] ) ? ' tp-head--light' : '' ) . '"><div class="tp-head__text">';
	$html .= sprintf( '<%1$s class="tp-head__title">%2$s</%1$s>', $tag, esc_html( $a['title'] ) );
	if ( $a['sub'] ) {
		$html .= '<p class="tp-head__sub">' . esc_html( $a['sub'] ) . '</p>';
	}
	$html .= '</div>';
	if ( $a['link'] ) {
		$html .= sprintf( '<a class="tp-head__more" href="%s">%s %s</a>', esc_url( $a['link'] ), esc_html( $a['link_text'] ), tp_icon( 'arrow', 14 ) );
	}
	return $html . '</div>';
} );

/**
 * [tp_hero_search placeholder="..." button="Tìm kiếm" chips="can-ho,biet-thu,lien-ke,nha-pho,dat-nen,khu-do-thi"]
 *
 * Tìm trong post type Dự án; các nút bên dưới dẫn tới trang Loại hình tương ứng.
 */
add_shortcode( 'tp_hero_search', function ( $atts ) {
	$a = shortcode_atts( array(
		'placeholder' => 'Nhập tên dự án, khu vực, loại hình hoặc từ khóa...',
		'button'      => 'Tìm kiếm',
		'chips'       => 'can-ho,biet-thu,lien-ke,nha-pho,dat-nen,khu-do-thi',
	), $atts );

	$id   = wp_unique_id( 'tp-search-' );
	$html = '<form class="tp-search" role="search" method="get" action="' . esc_url( home_url( '/' ) ) . '">';
	$html .= '<label class="screen-reader-text" for="' . esc_attr( $id ) . '">Tìm kiếm dự án</label>';
	$html .= '<input type="search" id="' . esc_attr( $id ) . '" name="s" placeholder="' . esc_attr( $a['placeholder'] ) . '" value="' . esc_attr( get_search_query() ) . '">';
	$html .= '<input type="hidden" name="post_type" value="du_an">';
	$html .= '<button type="submit">' . tp_icon( 'search', 18 ) . '<span>' . esc_html( $a['button'] ) . '</span></button>';
	$html .= '</form>';

	$slugs = tp_slugs( $a['chips'] );
	if ( $slugs ) {
		$terms = get_terms( array( 'taxonomy' => 'loai_hinh', 'slug' => $slugs, 'hide_empty' => false, 'orderby' => 'slug__in' ) );
		if ( $terms && ! is_wp_error( $terms ) ) {
			$html .= '<ul class="tp-chips" aria-label="Tìm theo loại hình">';
			foreach ( $terms as $term ) {
				$html .= '<li><a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a></li>';
			}
			$html .= '</ul>';
		}
	}

	return $html;
} );

/**
 * [tp_projects filter="featured|latest|selling|all" count="4" columns="4" badge="yes" button="yes"]
 *
 * Lọc thêm theo slug: loai_hinh="can-ho" khu_vuc="ha-noi" trang_thai="dang-mo-ban".
 */
add_shortcode( 'tp_projects', function ( $atts ) {
	$a = shortcode_atts( array(
		'filter'     => 'all',
		'count'      => 4,
		'columns'    => 4,
		'badge'      => 'yes',
		'button'     => 'yes',
		'loai_hinh'  => '',
		'khu_vuc'    => '',
		'trang_thai' => '',
		'empty'      => 'Chưa có dự án phù hợp.',
	), $atts );

	$query = array(
		'post_type'           => 'du_an',
		'post_status'         => 'publish',
		'posts_per_page'      => max( 1, min( 24, (int) $a['count'] ) ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'tax_query'           => array(),
	);

	switch ( $a['filter'] ) {
		case 'featured':
			$query['meta_query'] = array( array( 'key' => 'tp_noi_bat', 'value' => '1' ) );
			break;
		case 'latest':
			$query['orderby'] = 'modified';
			break;
		case 'selling':
			$a['trang_thai'] = $a['trang_thai'] ?: 'dang-mo-ban';
			break;
	}

	foreach ( array( 'loai_hinh', 'khu_vuc', 'trang_thai' ) as $taxonomy ) {
		if ( $slugs = tp_slugs( $a[ $taxonomy ] ) ) {
			$query['tax_query'][] = array( 'taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $slugs );
		}
	}

	$posts = get_posts( $query );
	if ( ! $posts ) {
		return '<p class="tp-empty">' . esc_html( $a['empty'] ) . '</p>';
	}

	$html = '<div class="tp-grid tp-grid--scroll" style="--tp-cols:' . max( 1, min( 6, (int) $a['columns'] ) ) . '">';
	foreach ( $posts as $post ) {
		$html .= tp_project_card( $post->ID, array( 'badge' => tp_bool( $a['badge'] ), 'button' => tp_bool( $a['button'] ) ) );
	}
	return $html . '</div>';
} );

/**
 * [tp_terms taxonomy="loai_hinh" include="biet-thu,can-ho" count="5" arrow="yes" more=""]
 *
 * Với Khu vực: mặc định chỉ lấy cấp tỉnh/thành; more="Các tỉnh khác" thêm một ô cuối
 * đếm các dự án không thuộc những khu vực đã hiện.
 */
add_shortcode( 'tp_terms', function ( $atts ) {
	$a = shortcode_atts( array(
		'taxonomy' => 'loai_hinh',
		'include'  => '',
		'count'    => 5,
		'columns'  => '',
		'arrow'    => 'yes',
		'more'     => '',
		'style'    => 'tall',
	), $atts );

	if ( ! in_array( $a['taxonomy'], array( 'loai_hinh', 'khu_vuc', 'trang_thai' ), true ) ) {
		return '';
	}

	$args = array(
		'taxonomy'   => $a['taxonomy'],
		'hide_empty' => false,
		'pad_counts' => true,
		'number'     => max( 1, (int) $a['count'] ),
		'orderby'    => 'count',
		'order'      => 'DESC',
	);
	if ( $slugs = tp_slugs( $a['include'] ) ) {
		$args['slug']    = $slugs;
		$args['orderby'] = 'slug__in';
		$args['order']   = 'ASC';
	} elseif ( is_taxonomy_hierarchical( $a['taxonomy'] ) ) {
		$args['parent'] = 0;
	}

	$terms = get_terms( $args );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return '';
	}

	$tiles = array();
	foreach ( $terms as $term ) {
		$tiles[] = tp_term_tile( $term, tp_bool( $a['arrow'] ) );
	}

	if ( $a['more'] ) {
		$rest = new WP_Query( array(
			'post_type'      => 'du_an',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'tax_query'      => array( array(
				'taxonomy' => $a['taxonomy'],
				'terms'    => wp_list_pluck( $terms, 'term_id' ),
				'operator' => 'NOT IN',
			) ),
		) );
		$tiles[] = sprintf(
			'<a class="tp-tile tp-tile--more" href="%s"><span class="tp-noimg" aria-hidden="true"></span><span class="tp-tile__body"><span class="tp-tile__name">%s</span><span class="tp-tile__count">%s dự án</span></span></a>',
			esc_url( get_post_type_archive_link( 'du_an' ) ),
			esc_html( $a['more'] ),
			esc_html( number_format_i18n( $rest->found_posts ) )
		);
	}

	$columns = (int) $a['columns'] ?: count( $tiles );
	return sprintf(
		'<div class="tp-tiles tp-tiles--%s" style="--tp-cols:%d">%s</div>',
		'short' === $a['style'] ? 'short' : 'tall',
		max( 1, min( 8, $columns ) ),
		implode( '', $tiles )
	);
} );

/**
 * [tp_news count="4" category="" excerpt="28"]
 *
 * Bài đầu tiên hiện lớn bên trái, các bài còn lại xếp thành danh sách bên phải.
 */
add_shortcode( 'tp_news', function ( $atts ) {
	$a = shortcode_atts( array(
		'count'    => 4,
		'category' => '',
		'excerpt'  => 28,
	), $atts );

	$posts = get_posts( array(
		'post_type'           => 'post',
		'posts_per_page'      => max( 1, min( 10, (int) $a['count'] ) ),
		'category_name'       => sanitize_title( $a['category'] ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	) );
	if ( ! $posts ) {
		return '';
	}

	$lead       = array_shift( $posts );
	$categories = get_the_category( $lead->ID );

	$html  = '<div class="tp-news">';
	$html .= '<article class="tp-news__lead">';
	$html .= '<a class="tp-news__media" href="' . esc_url( get_permalink( $lead ) ) . '" tabindex="-1" aria-hidden="true">' . tp_post_image( $lead->ID, 'tp-news' ) . '</a>';
	if ( $categories ) {
		$html .= '<span class="tp-badge">' . esc_html( $categories[0]->name ) . '</span>';
	}
	$html .= '<div class="tp-news__body">';
	$html .= '<h3 class="tp-news__title"><a href="' . esc_url( get_permalink( $lead ) ) . '">' . esc_html( get_the_title( $lead ) ) . '</a></h3>';
	$html .= '<p class="tp-news__date">' . tp_icon( 'calendar', 13 ) . '<time datetime="' . esc_attr( get_the_date( 'c', $lead ) ) . '">' . esc_html( get_the_date( 'd/m/Y', $lead ) ) . '</time></p>';
	$html .= '<p class="tp-news__excerpt">' . esc_html( wp_trim_words( get_the_excerpt( $lead ), (int) $a['excerpt'] ) ) . '</p>';
	$html .= '</div></article>';

	if ( $posts ) {
		$html .= '<ul class="tp-news__list">';
		foreach ( $posts as $post ) {
			$html .= '<li><a class="tp-news__item" href="' . esc_url( get_permalink( $post ) ) . '">';
			$html .= '<span class="tp-news__thumb">' . tp_post_image( $post->ID, 'tp-thumb' ) . '</span>';
			$html .= '<span class="tp-news__item-text"><span class="tp-news__item-title">' . esc_html( get_the_title( $post ) ) . '</span>';
			$html .= '<time datetime="' . esc_attr( get_the_date( 'c', $post ) ) . '">' . esc_html( get_the_date( 'd/m/Y', $post ) ) . '</time></span>';
			$html .= '</a></li>';
		}
		$html .= '</ul>';
	}

	return $html . '</div>';
} );

/**
 * [tp_contact_buttons style="header|cta" zalo_text="Chat Zalo" call_text=""]
 *
 * Số điện thoại và link Zalo lấy từ Giao diện → Tùy biến → TOPBDS – Liên hệ.
 */
add_shortcode( 'tp_contact_buttons', function ( $atts ) {
	$a = shortcode_atts( array(
		'style'     => 'header',
		'zalo_text' => 'Chat Zalo',
		'call_text' => '',
	), $atts );

	$call_text = $a['call_text'] ?: ( 'cta' === $a['style'] ? 'Gọi ngay ' . tp_hotline() : tp_hotline() );

	return sprintf(
		'<div class="tp-contact tp-contact--%1$s"><a class="tp-btn tp-btn--primary" href="%2$s" target="_blank" rel="noopener">%3$s<span>%4$s</span></a><a class="tp-btn tp-btn--outline" href="%5$s">%6$s<span>%7$s</span></a></div>',
		'cta' === $a['style'] ? 'cta' : 'header',
		esc_url( tp_zalo_url() ),
		'cta' === $a['style'] ? '' : tp_icon( 'chat', 16 ),
		esc_html( $a['zalo_text'] ),
		esc_attr( tp_hotline_href() ),
		tp_icon( 'phone', 16 ),
		esc_html( $call_text )
	);
} );

/**
 * [tp_trust_item icon="shield" title="Thông tin chính xác" text="Cập nhật liên tục"]
 */
add_shortcode( 'tp_trust_item', function ( $atts ) {
	$a = shortcode_atts( array( 'icon' => 'shield', 'title' => '', 'text' => '' ), $atts );
	return sprintf(
		'<div class="tp-trust"><span class="tp-trust__icon">%s</span><span class="tp-trust__text"><strong>%s</strong>%s</span></div>',
		tp_icon( sanitize_key( $a['icon'] ), 22 ),
		esc_html( $a['title'] ),
		$a['text'] ? '<span>' . esc_html( $a['text'] ) . '</span>' : ''
	);
} );

/**
 * [tp_icon name="bulb" size="28" badge="yes"]
 */
add_shortcode( 'tp_icon', function ( $atts ) {
	$a    = shortcode_atts( array( 'name' => 'bulb', 'size' => 24, 'badge' => 'no' ), $atts );
	$icon = tp_icon( sanitize_key( $a['name'] ), (int) $a['size'] );
	return tp_bool( $a['badge'] ) ? '<span class="tp-icon-badge">' . $icon . '</span>' : $icon;
} );

/**
 * [tp_mega_links taxonomy="loai_hinh" count="0" hide_empty="yes" more="" more_link=""]
 *
 * Danh sách link Loại hình / Khu vực / Trạng thái kèm số dự án (nhiều dự án xếp trước), dùng trong
 * UX Block mega menu. Khu vực chỉ lấy cấp tỉnh/thành. count="0": lấy tất cả.
 */
add_shortcode( 'tp_mega_links', function ( $atts ) {
	$a = shortcode_atts( array(
		'taxonomy'  => 'loai_hinh',
		'count'      => 0,
		'hide_empty' => 'yes',
		'more'       => '',
		'more_link' => '',
	), $atts );

	if ( ! in_array( $a['taxonomy'], array( 'loai_hinh', 'khu_vuc', 'trang_thai' ), true ) ) {
		return '';
	}

	$args = array(
		'taxonomy'   => $a['taxonomy'],
		'hide_empty' => tp_bool( $a['hide_empty'] ),
		'pad_counts' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => max( 0, (int) $a['count'] ),
	);
	if ( is_taxonomy_hierarchical( $a['taxonomy'] ) ) {
		$args['parent'] = 0;
	}

	$terms = get_terms( $args );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return '';
	}

	$html = '<ul class="tp-mega__links">';
	foreach ( $terms as $term ) {
		$html .= sprintf(
			'<li><a href="%s"><span>%s</span><span class="tp-mega__count">%s</span></a></li>',
			esc_url( get_term_link( $term ) ),
			esc_html( $term->name ),
			esc_html( number_format_i18n( $term->count ) )
		);
	}
	$html .= '</ul>';

	if ( $a['more'] ) {
		$html .= sprintf(
			'<a class="tp-mega__more" href="%s">%s %s</a>',
			esc_url( $a['more_link'] ?: get_post_type_archive_link( 'du_an' ) ),
			esc_html( $a['more'] ),
			tp_icon( 'arrow', 14 )
		);
	}

	return $html;
} );
