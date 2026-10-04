<?php
/**
 * Trang Tin tức (trang hiển thị bài viết, /tin-tuc/).
 *
 * Giữ nguyên bố cục blog của Flatsome và chèn phần đầu trang giống trang chuyên mục: tiêu đề H1,
 * đoạn giới thiệu lấy từ ô Tóm tắt của trang Tin tức và các nút chuyển chuyên mục. Flatsome mặc định
 * không in H1 ở trang này nên tên bài viết (H2) thành tiêu đề đầu tiên.
 */

defined( 'ABSPATH' ) || exit;

$tp_parent = file_exists( get_template_directory() . '/home.php' ) ? get_template_directory() . '/home.php' : get_template_directory() . '/index.php';

ob_start();
require $tp_parent;
$tp_html = ob_get_clean();

$tp_header = tp_blog_header();
$tp_anchor = '/(<div id="content" class="[^"]*blog-wrapper[^"]*"[^>]*>)/';
if ( preg_match( $tp_anchor, $tp_html ) ) {
	$tp_html = preg_replace( $tp_anchor, '$1' . str_replace( array( '\\', '$' ), array( '\\\\', '\$' ), $tp_header ), $tp_html, 1 );
} else {
	$tp_html = preg_replace( '/(<main id="main"[^>]*>)/', '$1' . str_replace( array( '\\', '$' ), array( '\\\\', '\$' ), $tp_header ), $tp_html, 1 );
}

echo $tp_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML của theme Flatsome và phần đầu trang đã escape.
