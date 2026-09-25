<?php
/**
 * Thông tin dự án (giá, vị trí, sản phẩm, nổi bật, Hot) và ảnh đại diện cho Loại hình / Khu vực.
 *
 * Dùng meta box có sẵn của WordPress nên không cần cài ACF.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Danh sách trường của dự án: key => [nhãn, kiểu, gợi ý].
 */
function tp_project_fields() {
	return array(
		'tp_gia'      => array( 'Giá hiển thị', 'text', 'VD: Từ 27 triệu/m²' ),
		'tp_dia_chi'  => array( 'Vị trí ngắn', 'text', 'VD: Tiên Hải, Hưng Yên (để trống sẽ lấy Khu vực)' ),
		'tp_san_pham' => array( 'Sản phẩm', 'text', 'VD: Liền kề | Shophouse (để trống sẽ lấy Loại hình)' ),
		'tp_noi_bat'  => array( 'Hiện ở mục "Dự án nổi bật"', 'checkbox', '' ),
		'tp_hot'      => array( 'Gắn nhãn "Hot"', 'checkbox', '' ),
	);
}

add_action( 'init', function () {
	foreach ( tp_project_fields() as $key => list( , $type ) ) {
		register_post_meta( 'du_an', $key, array(
			'type'          => $type === 'checkbox' ? 'boolean' : 'string',
			'single'        => true,
			'show_in_rest'  => true,
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		) );
	}
} );

add_action( 'add_meta_boxes_du_an', function () {
	add_meta_box( 'tp_project_info', 'Thông tin hiển thị trên thẻ dự án', 'tp_render_project_box', 'du_an', 'normal', 'high' );
} );

function tp_render_project_box( WP_Post $post ) {
	wp_nonce_field( 'tp_save_project', 'tp_project_nonce' );
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( tp_project_fields() as $key => list( $label, $type, $hint ) ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<tr><th scope="row"><label for="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th><td>';
		if ( 'checkbox' === $type ) {
			printf( '<input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s>', esc_attr( $key ), checked( (bool) $value, true, false ) );
		} else {
			printf( '<input type="text" class="regular-text" id="%1$s" name="%1$s" value="%2$s" placeholder="%3$s">', esc_attr( $key ), esc_attr( $value ), esc_attr( $hint ) );
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
}

add_action( 'save_post_du_an', function ( $post_id ) {
	if ( ! isset( $_POST['tp_project_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['tp_project_nonce'] ), 'tp_save_project' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( tp_project_fields() as $key => list( , $type ) ) {
		if ( 'checkbox' === $type ) {
			update_post_meta( $post_id, $key, ! empty( $_POST[ $key ] ) );
			continue;
		}
		$value = isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
} );

/*
 * Ảnh đại diện cho Loại hình và Khu vực (dùng cho các ô ảnh ở trang chủ).
 */

const TP_TERM_IMAGE_TAXONOMIES = array( 'loai_hinh', 'khu_vuc' );

function tp_term_image_field( $term = null ) {
	$image_id = $term ? (int) get_term_meta( $term->term_id, 'tp_image_id', true ) : 0;
	$preview  = $image_id ? wp_get_attachment_image( $image_id, 'thumbnail' ) : '';
	wp_nonce_field( 'tp_save_term_image', 'tp_term_nonce' );
	?>
	<div class="tp-term-image">
		<input type="hidden" name="tp_image_id" value="<?php echo esc_attr( $image_id ?: '' ); ?>">
		<div class="tp-term-image__preview"><?php echo $preview; ?></div>
		<button type="button" class="button tp-term-image__pick">Chọn ảnh</button>
		<button type="button" class="button-link tp-term-image__remove"<?php echo $image_id ? '' : ' hidden'; ?>>Bỏ ảnh</button>
		<p class="description">Ảnh hiển thị ở mục "Khám phá theo loại hình" / "Tìm kiếm theo khu vực" trên trang chủ. Nên dùng ảnh ngang, tối thiểu 520×400px.</p>
	</div>
	<?php
}

foreach ( TP_TERM_IMAGE_TAXONOMIES as $tp_taxonomy ) {
	add_action( "{$tp_taxonomy}_add_form_fields", function () {
		echo '<div class="form-field"><label>Ảnh đại diện</label>';
		tp_term_image_field();
		echo '</div>';
	} );

	add_action( "{$tp_taxonomy}_edit_form_fields", function ( $term ) {
		echo '<tr class="form-field"><th scope="row"><label>Ảnh đại diện</label></th><td>';
		tp_term_image_field( $term );
		echo '</td></tr>';
	} );

	add_action( "created_{$tp_taxonomy}", 'tp_save_term_image' );
	add_action( "edited_{$tp_taxonomy}", 'tp_save_term_image' );
}

function tp_save_term_image( $term_id ) {
	if ( ! isset( $_POST['tp_term_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['tp_term_nonce'] ), 'tp_save_term_image' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	$image_id = isset( $_POST['tp_image_id'] ) ? absint( $_POST['tp_image_id'] ) : 0;
	if ( $image_id ) {
		update_term_meta( $term_id, 'tp_image_id', $image_id );
	} else {
		delete_term_meta( $term_id, 'tp_image_id' );
	}
}

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	$screen = get_current_screen();
	if ( ! $screen || ! in_array( $screen->taxonomy, TP_TERM_IMAGE_TAXONOMIES, true ) || ! in_array( $hook, array( 'edit-tags.php', 'term.php' ), true ) ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'tp-term-image', TP_URI . '/assets/js/admin-term-image.js', array( 'jquery' ), TP_VERSION, true );
} );
