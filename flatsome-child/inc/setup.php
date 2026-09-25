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
} );

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
	<nav class="tp-mobile-bar" aria-label="Liên hệ nhanh">
		<a class="tp-mobile-bar__zalo" href="<?php echo esc_url( tp_zalo_url() ); ?>" target="_blank" rel="noopener">
			<?php echo tp_icon( 'chat' ); ?> Chat Zalo
		</a>
		<a class="tp-mobile-bar__call" href="<?php echo esc_attr( tp_hotline_href() ); ?>">
			<?php echo tp_icon( 'phone' ); ?> Gọi <?php echo esc_html( tp_hotline() ); ?>
		</a>
	</nav>
	<?php
} );
