<?php
/**
 * Đưa các shortcode TOPBDS vào UX Builder (nhóm "TOPBDS") để kéo thả và sửa tuỳ chọn như phần tử gốc của Flatsome.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'ux_builder_setup', function () {
	if ( ! function_exists( 'add_ux_builder_shortcode' ) ) {
		return;
	}

	$text   = function ( $heading, $default = '', $description = '' ) {
		return array( 'type' => 'textfield', 'heading' => $heading, 'default' => $default, 'description' => $description );
	};
	$number = function ( $heading, $default, $min, $max ) {
		return array( 'type' => 'slider', 'heading' => $heading, 'default' => $default, 'min' => $min, 'max' => $max );
	};
	$yes_no = function ( $heading, $default = 'yes' ) {
		return array( 'type' => 'select', 'heading' => $heading, 'default' => $default, 'options' => array( 'yes' => 'Có', 'no' => 'Không' ) );
	};
	$icons  = array( 'shield' => 'Khiên (uy tín)', 'headset' => 'Tai nghe (tư vấn)', 'users' => 'Người (đồng hành)', 'bulb' => 'Bóng đèn', 'chat' => 'Tin nhắn', 'phone' => 'Điện thoại', 'building' => 'Toà nhà', 'search' => 'Tìm kiếm', 'mail' => 'Email' );

	add_ux_builder_shortcode( 'tp_heading', array(
		'name'     => 'Tiêu đề mục',
		'category' => 'TOPBDS',
		'options'  => array(
			'title'     => $text( 'Tiêu đề', 'Dự án nổi bật' ),
			'sub'       => $text( 'Dòng mô tả' ),
			'link'      => $text( 'Link "Xem tất cả"' ),
			'link_text' => $text( 'Chữ của link', 'Xem tất cả' ),
			'tag'       => array( 'type' => 'select', 'heading' => 'Thẻ tiêu đề (SEO)', 'default' => 'h2', 'options' => array( 'h2' => 'H2', 'h3' => 'H3', 'h1' => 'H1' ) ),
			'light'     => $yes_no( 'Chữ trắng (trên nền tối)', 'no' ),
		),
	) );

	add_ux_builder_shortcode( 'tp_hero_search', array(
		'name'     => 'Ô tìm kiếm dự án',
		'category' => 'TOPBDS',
		'options'  => array(
			'placeholder' => $text( 'Chữ gợi ý', 'Nhập tên dự án, khu vực, loại hình hoặc từ khóa...' ),
			'button'      => $text( 'Chữ trên nút', 'Tìm kiếm' ),
			'chips'       => $text( 'Nút loại hình bên dưới', 'can-ho,biet-thu,lien-ke,nha-pho,dat-nen,khu-do-thi', 'Slug Loại hình, cách nhau bằng dấu phẩy. Để trống để ẩn.' ),
		),
	) );

	add_ux_builder_shortcode( 'tp_projects', array(
		'name'     => 'Danh sách dự án',
		'category' => 'TOPBDS',
		'options'  => array(
			'filter'     => array(
				'type'    => 'select',
				'heading' => 'Lấy dự án',
				'default' => 'all',
				'options' => array( 'featured' => 'Nổi bật (đã tick)', 'latest' => 'Mới cập nhật', 'selling' => 'Đang mở bán', 'all' => 'Mới đăng' ),
			),
			'count'      => $number( 'Số dự án', 4, 1, 24 ),
			'columns'    => $number( 'Số cột (máy tính)', 4, 1, 6 ),
			'badge'      => $yes_no( 'Hiện nhãn (Hot / Đang mở bán)' ),
			'button'     => $yes_no( 'Hiện nút "Xem chi tiết"' ),
			'loai_hinh'  => $text( 'Chỉ lấy loại hình', '', 'Slug, VD: can-ho,biet-thu' ),
			'khu_vuc'    => $text( 'Chỉ lấy khu vực', '', 'Slug, VD: ha-noi' ),
			'trang_thai' => $text( 'Chỉ lấy trạng thái', '', 'Slug, VD: sap-mo-ban' ),
		),
	) );

	add_ux_builder_shortcode( 'tp_terms', array(
		'name'     => 'Ô Loại hình / Khu vực',
		'category' => 'TOPBDS',
		'options'  => array(
			'taxonomy' => array( 'type' => 'select', 'heading' => 'Hiển thị', 'default' => 'loai_hinh', 'options' => array( 'loai_hinh' => 'Loại hình', 'khu_vuc' => 'Khu vực' ) ),
			'include'  => $text( 'Chọn mục (theo thứ tự)', '', 'Slug, VD: biet-thu,can-ho. Để trống: lấy các mục nhiều dự án nhất.' ),
			'count'    => $number( 'Số ô', 5, 1, 12 ),
			'columns'  => $number( 'Số cột (0 = bằng số ô)', 0, 0, 8 ),
			'style'    => array( 'type' => 'select', 'heading' => 'Chiều cao ô', 'default' => 'tall', 'options' => array( 'tall' => 'Cao (loại hình)', 'short' => 'Thấp (khu vực)' ) ),
			'arrow'    => $yes_no( 'Hiện mũi tên' ),
			'more'     => $text( 'Ô cuối "xem thêm"', '', 'VD: Các tỉnh khác. Để trống để ẩn.' ),
		),
	) );

	add_ux_builder_shortcode( 'tp_news', array(
		'name'     => 'Tin tức (1 lớn + danh sách)',
		'category' => 'TOPBDS',
		'options'  => array(
			'count'    => $number( 'Số bài', 4, 2, 10 ),
			'category' => $text( 'Chuyên mục (slug)', '', 'Để trống để lấy mọi chuyên mục.' ),
			'excerpt'  => $number( 'Số chữ tóm tắt bài lớn', 28, 0, 80 ),
		),
	) );

	add_ux_builder_shortcode( 'tp_contact_buttons', array(
		'name'     => 'Nút Zalo + Gọi',
		'category' => 'TOPBDS',
		'options'  => array(
			'style'     => array( 'type' => 'select', 'heading' => 'Kiểu', 'default' => 'cta', 'options' => array( 'cta' => 'Lớn (khối CTA)', 'header' => 'Nhỏ (header)' ) ),
			'zalo_text' => $text( 'Chữ nút Zalo', 'Chat Zalo' ),
			'call_text' => $text( 'Chữ nút gọi', '', 'Để trống: tự lấy hotline.' ),
		),
	) );

	add_ux_builder_shortcode( 'tp_trust_item', array(
		'name'     => 'Dòng cam kết (icon + chữ)',
		'category' => 'TOPBDS',
		'options'  => array(
			'icon'  => array( 'type' => 'select', 'heading' => 'Icon', 'default' => 'shield', 'options' => $icons ),
			'title' => $text( 'Tiêu đề', 'Thông tin chính xác' ),
			'text'  => $text( 'Mô tả', 'Cập nhật liên tục' ),
		),
	) );

	add_ux_builder_shortcode( 'tp_icon', array(
		'name'     => 'Icon',
		'category' => 'TOPBDS',
		'options'  => array(
			'name'  => array( 'type' => 'select', 'heading' => 'Icon', 'default' => 'bulb', 'options' => $icons ),
			'size'  => $number( 'Cỡ', 28, 12, 64 ),
			'badge' => $yes_no( 'Nền tròn màu cam', 'yes' ),
		),
	) );
} );
