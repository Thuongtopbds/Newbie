<?php
/**
 * TOPBDS – Flatsome child theme.
 *
 * Mỗi nhóm chức năng nằm trong một file ở thư mục inc/.
 */

defined( 'ABSPATH' ) || exit;

define( 'TP_VERSION', '1.0.0' );
define( 'TP_DIR', get_stylesheet_directory() );
define( 'TP_URI', get_stylesheet_directory_uri() );

foreach ( array( 'setup', 'post-types', 'meta', 'template-tags', 'shortcodes', 'ux-builder', 'search', 'single' ) as $tp_file ) {
	require_once TP_DIR . '/inc/' . $tp_file . '.php';
}
