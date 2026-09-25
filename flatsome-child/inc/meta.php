<?php
/**
 * Thông tin dự án (thẻ dự án + trang chi tiết) và ảnh đại diện cho Loại hình / Khu vực.
 *
 * Dùng meta box có sẵn của WordPress nên không cần cài ACF.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Danh sách trường của dự án: key => [nhãn, kiểu, gợi ý, nhóm].
 *
 * Kiểu: text, checkbox, gallery (danh sách ID ảnh, cách nhau dấu phẩy).
 */
function tp_project_fields() {
	return array(
		'tp_gia'             => array( 'Giá hiển thị', 'text', 'VD: Từ 27 triệu/m²', 'card' ),
		'tp_dia_chi'         => array( 'Vị trí ngắn', 'text', 'VD: Tiên Hải, Hưng Yên (để trống sẽ lấy Khu vực)', 'card' ),
		'tp_san_pham'        => array( 'Sản phẩm', 'text', 'VD: Liền kề | Shophouse (để trống sẽ lấy Loại hình)', 'card' ),
		'tp_noi_bat'         => array( 'Hiện ở mục "Dự án nổi bật"', 'checkbox', '', 'card' ),
		'tp_hot'             => array( 'Gắn nhãn "Hot"', 'checkbox', '', 'card' ),
		'tp_dia_chi_day_du'  => array( 'Địa chỉ đầy đủ', 'text', 'VD: Khu đô thị Phương Đông, đặc khu Vân Đồn, Quảng Ninh', 'detail' ),
		'tp_chu_dau_tu'      => array( 'Chủ đầu tư', 'text', '', 'detail' ),
		'tp_quy_mo'          => array( 'Quy mô', 'text', 'VD: 2 toà, 500 căn, 4.718 m²', 'detail' ),
		'tp_dien_tich'       => array( 'Diện tích', 'text', 'VD: 30 – 97 m²', 'detail' ),
		'tp_phong_ngu'       => array( 'Phòng ngủ', 'text', 'VD: Studio, 1PN, 2PN, Penthouse', 'detail' ),
		'tp_phap_ly'         => array( 'Pháp lý', 'text', 'VD: Sổ hồng lâu dài', 'detail' ),
		'tp_ban_giao'        => array( 'Bàn giao', 'text', 'VD: Quý IV/2026, bàn giao cơ bản', 'detail' ),
		'tp_gallery'         => array( 'Thư viện ảnh', 'gallery', '', 'detail' ),
		'tp_ban_do'          => array( 'Vị trí trên bản đồ', 'text', 'Địa chỉ hoặc toạ độ, VD: 21.0305,105.7823. Để trống để ẩn bản đồ.', 'detail' ),
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
	add_meta_box( 'tp_project_info', 'Thông tin dự án', 'tp_render_project_box', 'du_an', 'normal', 'high' );
} );

function tp_render_project_box( WP_Post $post ) {
	wp_nonce_field( 'tp_save_project', 'tp_project_nonce' );
	$groups = array(
		'card'   => 'Hiển thị trên thẻ dự án',
		'detail' => 'Trang chi tiết: bảng "Đặc điểm dự án", ảnh, bản đồ',
	);

	foreach ( $groups as $group => $title ) {
		echo '<h3 style="margin:16px 0 0">' . esc_html( $title ) . '</h3><table class="form-table" role="presentation"><tbody>';
		foreach ( tp_project_fields() as $key => list( $label, $type, $hint, $field_group ) ) {
			if ( $field_group !== $group ) {
				continue;
			}
			$value = get_post_meta( $post->ID, $key, true );
			echo '<tr><th scope="row"><label for="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th><td>';
			if ( 'checkbox' === $type ) {
				printf( '<input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s>', esc_attr( $key ), checked( (bool) $value, true, false ) );
			} elseif ( 'gallery' === $type ) {
				echo '<div class="tp-gallery-field"><input type="hidden" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '"><div class="tp-gallery-field__list" style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:8px">';
				foreach ( tp_id_list( $value ) as $image_id ) {
					echo wp_get_attachment_image( $image_id, array( 72, 72 ) );
				}
				echo '</div><button type="button" class="button tp-gallery-field__pick">Chọn ảnh</button> <button type="button" class="button-link tp-gallery-field__clear">Xoá hết</button>';
				echo '<p class="description">Ảnh đại diện luôn là ảnh đầu tiên. Ảnh chọn ở đây hiện thành dãy ảnh nhỏ bên dưới.</p></div>';
			} else {
				printf( '<input type="text" class="regular-text" id="%1$s" name="%1$s" value="%2$s" placeholder="%3$s">', esc_attr( $key ), esc_attr( $value ), esc_attr( $hint ) );
				if ( 'tp_ban_do' === $key ) {
					echo '<p class="description">' . esc_html( $hint ) . '</p>';
				}
			}
			echo '</td></tr>';
		}
		echo '</tbody></table>';
	}
}

/**
 * "12,34, 56" → [12, 34, 56]
 */
function tp_id_list( $value ) {
	return array_values( array_filter( array_map( 'absint', explode( ',', (string) $value ) ) ) );
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
		if ( 'gallery' === $type ) {
			$value = implode( ',', tp_id_list( $value ) );
		}
		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) && 'du_an' === get_current_screen()->post_type ) {
		wp_enqueue_media();
		wp_enqueue_script( 'tp-project-gallery', TP_URI . '/assets/js/admin-project-gallery.js', array( 'jquery' ), TP_VERSION, true );
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
