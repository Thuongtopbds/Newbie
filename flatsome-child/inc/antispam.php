<?php
/**
 * Chặn bot gửi form Contact Form 7 mà không cần captcha.
 *
 * Mỗi form có thêm 2 ô ẩn:
 * - tp_hp: ô bẫy, người không thấy nên để trống; bot điền mọi ô nên điền vào.
 * - tp_t:  số giây từ lúc trang mở tới lúc bấm gửi, do assets/js/topbds.js điền.
 *          Bot gửi thẳng tới máy chủ không chạy JS nên ô này trống; bot chạy JS thì gửi gần như ngay.
 * Lượt gửi bị coi là spam: không gửi email, không lưu, khách thấy thông báo lỗi chung của CF7.
 */

defined( 'ABSPATH' ) || exit;

const TP_FORM_MIN_SECONDS = 3;

add_filter( 'wpcf7_form_elements', function ( $html ) {
	return $html
		. '<span class="tp-hp" aria-hidden="true"><label>Website <input type="text" name="tp_hp" value="" tabindex="-1" autocomplete="off"></label></span>'
		. '<input type="hidden" name="tp_t" value="">';
} );

/**
 * Lý do coi một lượt gửi là spam, hoặc '' nếu hợp lệ.
 */
function tp_form_spam_reason( $data ) {
	if ( '' !== trim( (string) ( $data['tp_hp'] ?? '' ) ) ) {
		return 'honeypot';
	}
	$seconds = $data['tp_t'] ?? '';
	if ( '' === $seconds || ! is_numeric( $seconds ) ) {
		return 'no-js';
	}
	if ( (float) $seconds < TP_FORM_MIN_SECONDS ) {
		return 'too-fast';
	}
	return '';
}

add_filter( 'wpcf7_spam', function ( $spam, $submission = null ) {
	if ( $spam ) {
		return $spam;
	}
	$reason = tp_form_spam_reason( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.NonceVerification
	if ( '' === $reason ) {
		return false;
	}
	if ( $submission && method_exists( $submission, 'add_spam_log' ) ) {
		$submission->add_spam_log( array( 'agent' => 'topbds', 'reason' => $reason ) );
	}
	return true;
}, 10, 2 );
