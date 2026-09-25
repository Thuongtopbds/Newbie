<?php
/**
 * Post type "Dự án" và 3 phân loại: Loại hình, Khu vực, Trạng thái.
 *
 * URL: /du-an/ten-du-an/, /loai-hinh/can-ho/, /khu-vuc/ha-noi/, /trang-thai/dang-mo-ban/
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'tp_register_post_types' );

function tp_register_post_types() {
	register_post_type( 'du_an', array(
		'labels'        => array(
			'name'               => 'Dự án',
			'singular_name'      => 'Dự án',
			'add_new'            => 'Thêm dự án',
			'add_new_item'       => 'Thêm dự án mới',
			'edit_item'          => 'Sửa dự án',
			'all_items'          => 'Tất cả dự án',
			'search_items'       => 'Tìm dự án',
			'not_found'          => 'Chưa có dự án nào',
			'not_found_in_trash' => 'Không có dự án nào trong thùng rác',
		),
		'public'        => true,
		'has_archive'   => true,
		'rewrite'       => array( 'slug' => 'du-an', 'with_front' => false ),
		'menu_icon'     => 'dashicons-building',
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
		'show_in_rest'  => true,
	) );

	$taxonomies = array(
		'loai_hinh'  => array( 'Loại hình', 'loai-hinh', false ),
		'khu_vuc'    => array( 'Khu vực', 'khu-vuc', true ),
		'trang_thai' => array( 'Trạng thái', 'trang-thai', false ),
	);

	foreach ( $taxonomies as $taxonomy => list( $name, $slug, $hierarchical ) ) {
		register_taxonomy( $taxonomy, 'du_an', array(
			'labels'            => array(
				'name'          => $name,
				'singular_name' => $name,
				'all_items'     => 'Tất cả',
				'edit_item'     => 'Sửa ' . mb_strtolower( $name ),
				'add_new_item'  => 'Thêm ' . mb_strtolower( $name ),
			),
			'hierarchical'      => true, // Hiện dạng ô tick trong trang sửa dự án.
			'rewrite'           => array( 'slug' => $slug, 'with_front' => false, 'hierarchical' => $hierarchical ),
			'show_admin_column' => true,
			'show_in_rest'      => true,
		) );
	}
}

/**
 * Tạo sẵn các mục phân loại theo thiết kế khi kích hoạt theme (mục đã có thì bỏ qua).
 */
add_action( 'after_switch_theme', function () {
	tp_register_post_types();

	$defaults = array(
		'loai_hinh'  => array( 'Căn hộ', 'Biệt thự', 'Liền kề', 'Nhà phố', 'Đất nền', 'Khu đô thị', 'Nhà vườn', 'Shophouse' ),
		'khu_vuc'    => array( 'Hà Nội', 'TP. Hồ Chí Minh', 'Hải Phòng', 'Hưng Yên', 'Bắc Ninh' ),
		'trang_thai' => array( 'Đang mở bán', 'Sắp mở bán', 'Đã bàn giao' ),
	);

	foreach ( $defaults as $taxonomy => $names ) {
		foreach ( $names as $name ) {
			if ( ! term_exists( $name, $taxonomy ) ) {
				wp_insert_term( $name, $taxonomy );
			}
		}
	}

	flush_rewrite_rules();
} );
