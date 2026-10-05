<?php
/**
 * Hàm dùng cho trang chi tiết dự án (single-du_an.php).
 */

defined( 'ABSPATH' ) || exit;

/**
 * Các dòng của bảng "Đặc điểm dự án", bỏ qua dòng chưa nhập.
 *
 * @return array[] Mỗi dòng: [icon, nhãn, giá trị].
 */
function tp_project_specs( $post_id ) {
	$meta = function ( $key ) use ( $post_id ) {
		return (string) get_post_meta( $post_id, $key, true );
	};

	$rows = array(
		array( 'pin', 'Vị trí', $meta( 'tp_dia_chi_day_du' ) ?: $meta( 'tp_dia_chi' ) ?: tp_term_names( $post_id, 'khu_vuc' ) ),
		array( 'building', 'Chủ đầu tư', $meta( 'tp_chu_dau_tu' ) ),
		array( 'grid', 'Loại hình', $meta( 'tp_san_pham' ) ?: tp_term_names( $post_id, 'loai_hinh' ) ),
		array( 'building', 'Quy mô', $meta( 'tp_quy_mo' ) ),
		array( 'area', 'Diện tích', $meta( 'tp_dien_tich' ) ),
		array( 'bed', 'Phòng ngủ', $meta( 'tp_phong_ngu' ) ),
		array( 'file', 'Pháp lý', $meta( 'tp_phap_ly' ) ),
		array( 'key', 'Bàn giao', $meta( 'tp_ban_giao' ) ),
		array( 'tag', 'Giá bán', $meta( 'tp_gia' ) ),
	);

	return array_values( array_filter( $rows, function ( $row ) {
		return '' !== $row[2];
	} ) );
}

/**
 * Gắn id cho các thẻ H2 trong nội dung để làm thanh mục lục.
 *
 * @return array [nội dung đã gắn id, danh sách [id, tiêu đề]]
 */
function tp_content_toc( $content ) {
	$items = array();
	$used  = array();

	$content = preg_replace_callback( '#<h2([^>]*)>(.*?)</h2>#is', function ( $m ) use ( &$items, &$used ) {
		$text = trim( wp_strip_all_tags( $m[2] ) );
		if ( '' === $text ) {
			return $m[0];
		}
		if ( preg_match( '#\sid=["\']([^"\']+)["\']#i', $m[1], $existing ) ) {
			$id = $existing[1];
		} else {
			$base = sanitize_title( $text ) ?: 'muc';
			$id   = $base;
			for ( $i = 2; isset( $used[ $id ] ); $i++ ) {
				$id = $base . '-' . $i;
			}
			$m[1] .= ' id="' . esc_attr( $id ) . '"';
		}
		$used[ $id ] = true;
		$items[]     = array( $id, $text );
		return '<h2' . $m[1] . '>' . $m[2] . '</h2>';
	}, $content );

	return array( $content, $items );
}

/**
 * Breadcrumb: dùng của Rank Math nếu có (kèm schema), nếu không thì tự dựng.
 */
function tp_breadcrumbs( $post_id ) {
	if ( function_exists( 'rank_math_get_breadcrumbs' ) ) {
		$html = rank_math_get_breadcrumbs();
		if ( $html ) {
			return '<div class="tp-crumbs">' . $html . '</div>';
		}
	}

	$links = array(
		'<a href="' . esc_url( home_url( '/' ) ) . '">Trang chủ</a>',
		'<a href="' . esc_url( get_post_type_archive_link( 'du_an' ) ) . '">Dự án</a>',
	);
	$types = get_the_terms( $post_id, 'loai_hinh' );
	if ( $types && ! is_wp_error( $types ) ) {
		$links[] = '<a href="' . esc_url( get_term_link( $types[0] ) ) . '">' . esc_html( $types[0]->name ) . '</a>';
	}
	$links[] = '<span aria-current="page">' . esc_html( get_the_title( $post_id ) ) . '</span>';

	return '<nav class="tp-crumbs" aria-label="Breadcrumb">' . implode( '<span class="tp-crumbs__sep" aria-hidden="true">›</span>', $links ) . '</nav>';
}

/**
 * Dự án liên quan: cùng loại hình hoặc cùng khu vực, thiếu thì lấy dự án mới.
 */
function tp_related_project_ids( $post_id, $count = 4 ) {
	$tax_query = array( 'relation' => 'OR' );
	foreach ( array( 'loai_hinh', 'khu_vuc' ) as $taxonomy ) {
		$ids = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
		if ( $ids && ! is_wp_error( $ids ) ) {
			$tax_query[] = array( 'taxonomy' => $taxonomy, 'terms' => $ids );
		}
	}

	$base = array(
		'post_type'      => 'du_an',
		'post_status'    => 'publish',
		'posts_per_page' => $count,
		'post__not_in'   => array( $post_id ),
		'fields'         => 'ids',
		'no_found_rows'  => true,
	);

	$ids = count( $tax_query ) > 1 ? get_posts( $base + array( 'tax_query' => $tax_query ) ) : array();
	if ( count( $ids ) < $count ) {
		$base['post__not_in']   = array_merge( array( $post_id ), $ids );
		$base['posts_per_page'] = $count - count( $ids );
		$ids                    = array_merge( $ids, get_posts( $base ) );
	}
	return $ids;
}

/**
 * Ảnh của thư viện: ảnh đại diện + các ảnh trong "Thư viện ảnh".
 */
function tp_project_gallery_ids( $post_id ) {
	$ids = tp_id_list( get_post_meta( $post_id, 'tp_gallery', true ) );
	if ( has_post_thumbnail( $post_id ) ) {
		array_unshift( $ids, get_post_thumbnail_id( $post_id ) );
	}
	return array_values( array_unique( array_filter( $ids, 'wp_attachment_is_image' ) ) );
}

/**
 * Thông số nổi bật hiện trong khung kính ở hero.
 *
 * @return array[] Mỗi dòng: [icon, nhãn, giá trị].
 */
function tp_project_facts( $post_id ) {
	$facts = array();
	foreach ( array(
		'tp_dien_tich' => array( 'area', 'Diện tích' ),
		'tp_phong_ngu' => array( 'bed', 'Phòng ngủ' ),
		'tp_phap_ly'   => array( 'file', 'Pháp lý' ),
		'tp_ban_giao'  => array( 'key', 'Bàn giao' ),
	) as $key => list( $icon, $label ) ) {
		$value = get_post_meta( $post_id, $key, true );
		if ( '' !== (string) $value ) {
			$facts[] = array( $icon, $label, $value );
		}
	}
	return $facts;
}

/**
 * Thẻ "Nhận báo giá" ở cột phải: giá, form Contact Form 7 (hoặc nút Zalo/Gọi), tư vấn viên.
 *
 * Tên, ảnh, lời chào tư vấn viên và shortcode form sửa tại Giao diện → Tuỳ biến → TOPBDS – Liên hệ.
 */
function tp_quote_card( $post_id ) {
	$price    = get_post_meta( $post_id, 'tp_gia', true );
	$form     = get_theme_mod( 'tp_project_form', '' );
	$name     = get_theme_mod( 'tp_agent_name', '' ) ?: 'Chuyên viên tư vấn TOPBDS';
	$note     = get_theme_mod( 'tp_agent_note', 'Luôn sẵn sàng giải đáp mọi thắc mắc về dự án, hỗ trợ 24/7.' );
	$photo_id = (int) get_theme_mod( 'tp_agent_photo', 0 );
	$photo    = $photo_id
		? wp_get_attachment_image( $photo_id, 'thumbnail', false, array( 'alt' => $name, 'class' => 'tp-quote-card__photo' ) )
		: '<span class="tp-quote-card__photo tp-quote-card__photo--empty">' . tp_icon( 'headset', 22 ) . '</span>';

	ob_start();
	?>
	<section class="tp-quote-card" id="tp-lien-he" aria-labelledby="tp-quote-title">
		<?php if ( $price ) : ?>
			<p class="tp-quote-card__price"><span>Giá bán</span><strong><?php echo esc_html( $price ); ?></strong></p>
		<?php endif; ?>
		<h2 class="tp-quote-card__title" id="tp-quote-title">Nhận bảng giá &amp; chính sách mới nhất</h2>
		<p class="tp-quote-card__sub">Gửi bảng giá, mặt bằng và tài liệu dự án qua Zalo trong ít phút.</p>
		<div class="tp-quote-card__form">
			<?php
			echo $form
				? do_shortcode( $form )
				: do_shortcode( '[tp_contact_buttons style="cta" call_text="Gọi ' . esc_attr( tp_hotline() ) . '"]' );
			?>
		</div>
		<div class="tp-quote-card__agent">
			<?php echo $photo; ?>
			<div>
				<p class="tp-quote-card__name"><?php echo esc_html( $name ); ?></p>
				<p class="tp-quote-card__note"><?php echo esc_html( $note ); ?></p>
			</div>
		</div>
		<a class="tp-quote-card__hotline" href="<?php echo esc_attr( tp_hotline_href() ); ?>"><?php echo tp_icon( 'phone', 16 ); ?><span><?php echo esc_html( tp_hotline() ); ?></span></a>
	</section>
	<?php
	return ob_get_clean();
}

/**
 * Rank Math chỉ nhận mục lục từ plugin nên báo lỗi "không dùng Table of Contents plugin", dù trang dự án
 * (thanh mục lục dính) và trang văn bản (mục lục cột trái) đã tự tạo mục lục từ các H2.
 * Khai báo mục lục của theme cho đúng hai loại trang này; bài viết tin tức không có mục lục nên vẫn giữ cảnh báo.
 */
add_filter( 'rank_math/researches/toc_plugins', function ( $plugins ) {
	$post = get_post();
	if ( $post && ( 'du_an' === $post->post_type || 'page-van-ban.php' === get_page_template_slug( $post ) ) ) {
		$plugins['seo-by-rank-math/rank-math.php'] = 'Mục lục tự động của theme TOPBDS';
	}
	return $plugins;
} );

/**
 * Bài viết tin tức dùng header thường như các trang khác. Header "transparent" của Flatsome nằm đè lên
 * nội dung (position: absolute) nên che mất tiêu đề bài, vì bài viết không có ảnh nền phía trên.
 */
add_filter( 'flatsome_header_class', function ( $classes ) {
	if ( ! is_singular( 'post' ) ) {
		return $classes;
	}
	$drop = array( 'transparent', 'has-transparent', 'nav-dark', 'toggle-nav-dark' );
	$keep = array();
	foreach ( (array) $classes as $class ) {
		$tokens = array_diff( preg_split( '/\s+/', (string) $class, -1, PREG_SPLIT_NO_EMPTY ), $drop );
		if ( $tokens ) {
			$keep[] = implode( ' ', $tokens );
		}
	}
	return $keep;
}, 999 );

/**
 * Phần đầu trang Tin tức: H1, giới thiệu (ô Tóm tắt của trang Tin tức) và các nút chuyên mục.
 * Dùng cùng khung "archive-page-header" của Flatsome để giống trang chuyên mục.
 */
function tp_blog_header() {
	$page_id = (int) get_option( 'page_for_posts' );
	$title   = trim( (string) get_theme_mod( 'tp_blog_title', 'Tin tức bất động sản' ) );
	$intro   = $page_id && ! is_paged() ? get_post_field( 'post_excerpt', $page_id ) : '';
	$cats    = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC' ) );
	$cats    = is_wp_error( $cats ) ? array() : array_filter( $cats, function ( $cat ) {
		return ! in_array( $cat->slug, array( 'uncategorized', 'chua-phan-loai', 'khong-phan-loai' ), true );
	} );

	$html  = '<header class="archive-page-header tp-blog-head"><div class="row"><div class="large-12 text-center col">';
	$html .= '<h1 class="page-title is-large uppercase"><span>' . esc_html( $title ?: 'Tin tức' ) . '</span></h1>';
	if ( $intro ) {
		$html .= '<div class="taxonomy-description"><p>' . esc_html( $intro ) . '</p></div>';
	}
	if ( $cats ) {
		$html .= '<ul class="tp-blog-cats" aria-label="Chuyên mục tin tức">';
		foreach ( $cats as $cat ) {
			$html .= '<li><a href="' . esc_url( get_term_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a></li>';
		}
		$html .= '</ul>';
	}
	return $html . '</div></div></header>';
}

/**
 * Câu hỏi thường gặp của trang dự án, lấy từ nội dung bài: mục H2 "Câu hỏi thường gặp" (hoặc "Hỏi đáp", "FAQ"),
 * mỗi câu hỏi là một H3, câu trả lời là phần nội dung ngay sau H3 đó.
 *
 * @return array[] Mỗi dòng: [câu hỏi, câu trả lời].
 */
function tp_project_faq( $post_id ) {
	$content = (string) get_post_field( 'post_content', $post_id );
	if ( ! preg_match( '#<h2[^>]*>\s*(?:<[^>]+>\s*)*(?:câu hỏi thường gặp|hỏi đáp|faq)\b.*?</h2>(.*?)(?=<h2[\s>]|$)#isu', $content, $section ) ) {
		return array();
	}

	// wpautop: nội dung lưu bằng trình soạn thảo cổ điển chưa có thẻ <p>.
	$parts = preg_split( '#<h3[^>]*>(.*?)</h3>#is', wpautop( $section[1] ), -1, PREG_SPLIT_DELIM_CAPTURE );
	$faq   = array();
	for ( $i = 1; $i + 1 < count( $parts ); $i += 2 ) {
		$question = trim( wp_strip_all_tags( $parts[ $i ] ) );
		// Chỉ lấy đoạn <p> đầu tiên để dòng ghi chú cuối mục (VD "Xem video…") không lọt vào câu trả lời cuối.
		$answer = preg_match( '#<p[^>]*>(.*?)</p>#is', $parts[ $i + 1 ], $first ) ? $first[1] : $parts[ $i + 1 ];
		$answer = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( strip_shortcodes( $answer ) ) ) );
		if ( '' !== $question && '' !== $answer ) {
			$faq[] = array( html_entity_decode( $question, ENT_QUOTES, 'UTF-8' ), html_entity_decode( $answer, ENT_QUOTES, 'UTF-8' ) );
		}
	}
	return $faq;
}

/**
 * Schema FAQPage cho trang dự án, dựng từ mục "Câu hỏi thường gặp" (xem tp_project_faq).
 * Thêm vào @graph của Rank Math; bỏ qua nếu trang đã có FAQPage (VD dùng khối FAQ by Rank Math).
 */
function tp_project_faq_schema( $post_id ) {
	$faq = tp_project_faq( $post_id );
	if ( ! $faq ) {
		return array();
	}
	return array(
		'@type'      => 'FAQPage',
		'@id'        => get_permalink( $post_id ) . '#faq',
		'mainEntity' => array_map( function ( $row ) {
			return array(
				'@type'          => 'Question',
				'name'           => $row[0],
				'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $row[1] ),
			);
		}, $faq ),
	);
}

add_filter( 'rank_math/json_ld', function ( $data ) {
	if ( ! is_singular( 'du_an' ) ) {
		return $data;
	}
	foreach ( (array) $data as $entity ) {
		if ( is_array( $entity ) && 'FAQPage' === ( $entity['@type'] ?? '' ) ) {
			return $data;
		}
	}
	$schema = tp_project_faq_schema( get_queried_object_id() );
	if ( $schema ) {
		$data['tpFaq'] = $schema;
	}
	return $data;
}, 99 );

// Không có Rank Math thì tự in schema FAQPage.
add_action( 'wp_head', function () {
	if ( defined( 'RANK_MATH_VERSION' ) || ! is_singular( 'du_an' ) ) {
		return;
	}
	$schema = tp_project_faq_schema( get_queried_object_id() );
	if ( $schema ) {
		echo '<script type="application/ld+json">' . wp_json_encode( array( '@context' => 'https://schema.org' ) + $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "</script>\n";
	}
} );
