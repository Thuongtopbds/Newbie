<?php
/**
 * Nạp CSS/JS, kích thước ảnh, cài đặt liên hệ trong Customizer, thanh liên hệ trên mobile.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {
	add_image_size( 'tp-card', 640, 420, true );
	add_image_size( 'tp-tile', 520, 400, true );
	add_image_size( 'tp-news', 880, 500, true );
	add_image_size( 'tp-thumb', 240, 160, true );
} );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'topbds', TP_URI . '/assets/css/topbds.css', array(), TP_VERSION );
	wp_enqueue_script( 'topbds', TP_URI . '/assets/js/topbds.js', array(), TP_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
}, 20 );

/**
 * Hotline và link Zalo dùng chung cho nút header, khối CTA và thanh liên hệ mobile.
 * Sửa tại Giao diện → Tùy biến → TOPBDS – Liên hệ.
 */
add_action( 'customize_register', function ( WP_Customize_Manager $wp_customize ) {
	$wp_customize->add_section( 'tp_contact', array(
		'title'    => 'TOPBDS – Liên hệ',
		'priority' => 30,
	) );

	$fields = array(
		'tp_hotline'    => array( 'Hotline (hiển thị)', '0977 113 009', 'sanitize_text_field' ),
		'tp_zalo'       => array( 'Link Zalo', 'https://zalo.me/0977113009', 'esc_url_raw' ),
		'tp_mobile_bar' => array( 'Hiện thanh Zalo/Gọi cố định trên mobile', true, 'wp_validate_boolean' ),
		'tp_agent_name' => array( 'Tên tư vấn viên (trang dự án)', '', 'sanitize_text_field' ),
		'tp_agent_note' => array( 'Lời chào của tư vấn viên', 'Luôn sẵn sàng giải đáp mọi thắc mắc về dự án, hỗ trợ 24/7.', 'sanitize_text_field' ),
		'tp_project_form' => array( 'Form nhận báo giá (shortcode Contact Form 7)', '', 'sanitize_text_field' ),
	);

	foreach ( $fields as $id => list( $label, $default, $sanitize ) ) {
		$wp_customize->add_setting( $id, array(
			'default'           => $default,
			'sanitize_callback' => $sanitize,
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $label,
			'section' => 'tp_contact',
			'type'    => is_bool( $default ) ? 'checkbox' : 'text',
		) );
	}

	$wp_customize->add_setting( 'tp_agent_photo', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'tp_agent_photo', array(
		'label'     => 'Ảnh tư vấn viên',
		'section'   => 'tp_contact',
		'mime_type' => 'image',
	) ) );

	$wp_customize->add_setting( 'tp_archive_intro', array( 'default' => tp_archive_intro_default(), 'sanitize_callback' => 'wp_kses_post' ) );
	$wp_customize->add_control( 'tp_archive_intro', array(
		'label'       => 'Giới thiệu trang "Tất cả dự án" (/du-an/)',
		'description' => 'Hiện dưới tiêu đề trang danh sách dự án, 100–150 chữ. Để trống để ẩn.',
		'section'     => 'tp_contact',
		'type'        => 'textarea',
	) );

	$wp_customize->add_setting( 'tp_blog_title', array( 'default' => 'Tin tức bất động sản', 'sanitize_callback' => 'sanitize_text_field' ) );
	$wp_customize->add_control( 'tp_blog_title', array(
		'label'       => 'Tiêu đề H1 trang Tin tức',
		'description' => 'Đoạn giới thiệu dưới tiêu đề lấy từ ô Tóm tắt của trang Tin tức.',
		'section'     => 'tp_contact',
		'type'        => 'text',
	) );

	$wp_customize->add_setting( 'tp_404_image', array( 'default' => 0, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( new WP_Customize_Media_Control( $wp_customize, 'tp_404_image', array(
		'label'       => 'Ảnh trang 404',
		'description' => 'Để trống: dùng hình minh hoạ động có sẵn. Nên dùng ảnh 1120×840.',
		'section'     => 'tp_contact',
		'mime_type'   => 'image',
	) ) );
} );

function tp_archive_intro_default() {
	return 'Tổng hợp các dự án bất động sản đang mở bán, sắp mở bán và đã bàn giao tại Hà Nội, Hưng Yên, Quảng Ninh, TP. Hồ Chí Minh và nhiều tỉnh thành khác. Mỗi dự án trên TOPBDS.VN đều có thông tin vị trí, chủ đầu tư, quy mô, pháp lý, tiến độ và giá bán được cập nhật thường xuyên, giúp bạn dễ dàng so sánh căn hộ chung cư, biệt thự, liền kề, shophouse hay đất nền phù hợp với nhu cầu ở và đầu tư.' . "\n\n" . 'Dùng bộ lọc bên dưới để tìm theo loại hình, khu vực, hoặc để lại số điện thoại tại từng dự án để nhận bảng giá và chính sách mới nhất qua Zalo.';
}

function tp_archive_intro() {
	return trim( (string) get_theme_mod( 'tp_archive_intro', tp_archive_intro_default() ) );
}

function tp_hotline() {
	return get_theme_mod( 'tp_hotline', '0977 113 009' );
}

function tp_hotline_href() {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', tp_hotline() );
}

function tp_zalo_url() {
	return get_theme_mod( 'tp_zalo', 'https://zalo.me/0977113009' );
}

add_action( 'wp_footer', function () {
	if ( ! get_theme_mod( 'tp_mobile_bar', true ) ) {
		return;
	}
	?>
	<nav class="tp-mobile-bar<?php echo is_singular( 'du_an' ) ? ' tp-mobile-bar--3' : ''; ?>" aria-label="Liên hệ nhanh">
		<a class="tp-mobile-bar__zalo" href="<?php echo esc_url( tp_zalo_url() ); ?>" target="_blank" rel="noopener">
			<?php echo tp_icon( 'chat' ); ?> Chat Zalo
		</a>
		<a class="tp-mobile-bar__call" href="<?php echo esc_attr( tp_hotline_href() ); ?>">
			<?php echo tp_icon( 'phone' ); ?> Gọi <?php echo is_singular( 'du_an' ) ? '' : esc_html( tp_hotline() ); ?>
		</a>
		<?php if ( is_singular( 'du_an' ) ) : ?>
			<a class="tp-mobile-bar__quote" href="#tp-lien-he"><?php echo tp_icon( 'file', 18 ); ?> Báo giá</a>
		<?php endif; ?>
	</nav>
	<?php
} );

/**
 * Tiêu đề trang chuyên mục / thẻ của Flatsome: bỏ tiền tố "Lưu trữ danh mục:", "Lưu trữ thẻ:" để H1 chỉ còn tên chuyên mục.
 */
add_filter( 'gettext_flatsome', function ( $translation, $text ) {
	if ( in_array( $text, array( 'Category Archives: %s', 'Tag Archives: %s' ), true ) ) {
		return '%s';
	}
	return $translation;
}, 10, 2 );
