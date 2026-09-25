<?php
/**
 * Trang danh sách dự án: /du-an/, trang Loại hình / Khu vực / Trạng thái và kết quả tìm kiếm dự án
 * đều dùng chung templates/archive-du-an.php (lưới thẻ dự án + bộ lọc).
 */

defined( 'ABSPATH' ) || exit;

function tp_is_project_listing() {
	return is_post_type_archive( 'du_an' )
		|| is_tax( array( 'loai_hinh', 'khu_vuc', 'trang_thai' ) )
		|| ( is_search() && 'du_an' === get_query_var( 'post_type' ) );
}

add_action( 'pre_get_posts', function ( WP_Query $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	$is_listing = $query->is_post_type_archive( 'du_an' )
		|| $query->is_tax( array( 'loai_hinh', 'khu_vuc', 'trang_thai' ) )
		|| ( $query->is_search() && 'du_an' === $query->get( 'post_type' ) );

	if ( $is_listing ) {
		$query->set( 'posts_per_page', 12 );
	}
} );

add_filter( 'template_include', function ( $template ) {
	return tp_is_project_listing() ? TP_DIR . '/templates/archive-du-an.php' : $template;
} );
