<?php
/**
 * Trang 404 (404.php): gợi ý từ địa chỉ khách gõ sai, icon cho từng loại hình, hình minh hoạ động.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Các từ lấy từ đoạn cuối của địa chỉ không tồn tại, VD: /du-an/an-lac-green-symphony/ → "an lac green symphony".
 */
function tp_404_words() {
	$path     = trim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );
	$segments = array_filter( explode( '/', urldecode( $path ) ) );
	$last     = sanitize_title( (string) end( $segments ) );
	$last     = preg_replace( '/\.(html?|php)$|-\d+$/', '', $last );

	return array_slice( array_values( array_filter( explode( '-', $last ), function ( $word ) {
		return mb_strlen( $word ) >= 2 && ! is_numeric( $word );
	} ) ), 0, 6 );
}

/**
 * Dự án / bài viết có đường dẫn gần giống địa chỉ sai (so khớp từng từ trong slug), tối đa $limit kết quả.
 */
function tp_404_suggestions( $words, $limit = 4 ) {
	global $wpdb;

	$words = array_filter( $words, function ( $word ) {
		return mb_strlen( $word ) >= 3;
	} );
	if ( ! $words ) {
		return array();
	}

	$like = array();
	foreach ( $words as $word ) {
		$like[] = $wpdb->prepare( 'post_name LIKE %s', '%' . $wpdb->esc_like( $word ) . '%' );
	}
	$needed = max( 1, (int) ceil( count( $like ) * 0.6 ) );

	// Mỗi từ khớp được 1 điểm; lấy các bài khớp ít nhất 60% số từ, nhiều điểm xếp trước.
	$score = '(' . implode( ') + (', $like ) . ')';
	$sql   = "SELECT ID FROM {$wpdb->posts}
		WHERE post_status = 'publish' AND post_type IN ('du_an', 'post', 'page')
		AND {$score} >= {$needed}
		ORDER BY {$score} DESC, post_type = 'du_an' DESC, post_date DESC
		LIMIT " . (int) $limit;

	return array_map( 'intval', $wpdb->get_col( $sql ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- các phần LIKE đã prepare ở trên.
}

/**
 * Icon cho ô loại hình theo slug.
 */
function tp_404_type_icon( $slug ) {
	$map = array(
		'can-ho'      => 'building',
		'chung-cu'    => 'building',
		'biet-thu'    => 'home',
		'nha-vuon'    => 'trees',
		'lien-ke'     => 'homes',
		'nha-pho'     => 'homes',
		'shophouse'   => 'homes',
		'dat-nen'     => 'map',
		'khu-do-thi'  => 'grid',
		'cho-thue'    => 'key',
	);
	return $map[ $slug ] ?? 'building';
}

/**
 * Hình minh hoạ: phố cao tầng mọc lên, cửa sổ sáng nhấp nháy, cần cẩu đang "xây lại" trang, mây trôi.
 * Chỉ dùng khi không chọn ảnh riêng ở Tuỳ biến → TOPBDS – Liên hệ → Ảnh trang 404.
 */
function tp_404_illustration() {
	ob_start();
	?>
	<svg class="tp-404-art" viewBox="0 0 560 420" role="img" aria-labelledby="tp-404-art-title">
		<title id="tp-404-art-title">Khu đô thị đang xây dựng với dòng chữ 404</title>
		<defs>
			<linearGradient id="tp404Sky" x1="0" y1="0" x2="0" y2="1">
				<stop offset="0" stop-color="#eaf1fa"/>
				<stop offset="1" stop-color="#f8fafc"/>
			</linearGradient>
			<linearGradient id="tp404Tower" x1="0" y1="0" x2="1" y2="0">
				<stop offset="0" stop-color="#1c3558"/>
				<stop offset="1" stop-color="#0f1e33"/>
			</linearGradient>
			<linearGradient id="tp404TowerLight" x1="0" y1="0" x2="1" y2="0">
				<stop offset="0" stop-color="#3a5a85"/>
				<stop offset="1" stop-color="#26426a"/>
			</linearGradient>
			<linearGradient id="tp404Text" x1="0" y1="0" x2="0" y2="1">
				<stop offset="0" stop-color="#ffffff"/>
				<stop offset="1" stop-color="#e3e9f1"/>
			</linearGradient>
			<pattern id="tp404Win" width="14" height="16" patternUnits="userSpaceOnUse">
				<rect x="3" y="3" width="8" height="9" rx="1" fill="#9fb6d3" opacity=".55"/>
			</pattern>
			<pattern id="tp404WinSmall" width="10" height="12" patternUnits="userSpaceOnUse">
				<rect x="2" y="2" width="6" height="7" rx="1" fill="#c9d7e8" opacity=".6"/>
			</pattern>
			<clipPath id="tp404Clip"><rect width="560" height="420" rx="24"/></clipPath>
		</defs>

		<g clip-path="url(#tp404Clip)">
			<rect width="560" height="420" fill="url(#tp404Sky)"/>
			<circle class="tp-404-sun" cx="440" cy="92" r="46" fill="#ffe3cf"/>

			<g class="tp-404-cloud tp-404-cloud--1" fill="#fff">
				<ellipse cx="120" cy="70" rx="42" ry="16"/><ellipse cx="148" cy="60" rx="26" ry="18"/><ellipse cx="96" cy="64" rx="20" ry="13"/>
			</g>
			<g class="tp-404-cloud tp-404-cloud--2" fill="#fff" opacity=".85">
				<ellipse cx="330" cy="46" rx="34" ry="12"/><ellipse cx="352" cy="38" rx="20" ry="13"/>
			</g>

			<!-- Phố xa -->
			<g fill="#d6e0ec">
				<rect x="10" y="190" width="40" height="160"/><rect x="56" y="160" width="30" height="190"/><rect x="92" y="205" width="44" height="145"/>
				<rect x="410" y="175" width="36" height="175"/><rect x="452" y="150" width="30" height="200"/><rect x="488" y="195" width="62" height="155"/>
			</g>

			<!-- Toà cao tầng mọc lên -->
			<g class="tp-404-tower" style="--d:.05s">
				<rect x="150" y="150" width="58" height="200" fill="url(#tp404TowerLight)"/>
				<rect x="150" y="150" width="58" height="200" fill="url(#tp404WinSmall)"/>
			</g>
			<g class="tp-404-tower" style="--d:.2s">
				<rect x="214" y="92" width="72" height="258" fill="url(#tp404Tower)"/>
				<rect x="214" y="92" width="72" height="258" fill="url(#tp404Win)"/>
				<rect x="244" y="70" width="12" height="22" fill="#0f1e33"/>
				<circle class="tp-404-beacon" cx="250" cy="66" r="4" fill="#f47521"/>
			</g>
			<g class="tp-404-tower" style="--d:.35s">
				<rect x="292" y="128" width="64" height="222" fill="url(#tp404TowerLight)"/>
				<rect x="292" y="128" width="64" height="222" fill="url(#tp404Win)"/>
			</g>
			<g class="tp-404-tower" style="--d:.5s">
				<rect x="362" y="176" width="46" height="174" fill="url(#tp404Tower)"/>
				<rect x="362" y="176" width="46" height="174" fill="url(#tp404WinSmall)"/>
			</g>

			<!-- Cửa sổ sáng đèn -->
			<g fill="#ffc48f">
				<rect class="tp-404-light" style="--d:0s" x="231" y="127" width="8" height="9" rx="1"/>
				<rect class="tp-404-light" style="--d:1.1s" x="259" y="175" width="8" height="9" rx="1"/>
				<rect class="tp-404-light" style="--d:2.2s" x="309" y="163" width="8" height="9" rx="1"/>
				<rect class="tp-404-light" style="--d:.6s" x="323" y="227" width="8" height="9" rx="1"/>
				<rect class="tp-404-light" style="--d:1.7s" x="174" y="196" width="6" height="7" rx="1"/>
				<rect class="tp-404-light" style="--d:2.8s" x="382" y="218" width="6" height="7" rx="1"/>
			</g>

			<!-- Cần cẩu -->
			<g class="tp-404-crane">
				<rect x="470" y="100" width="8" height="250" fill="#f47521"/>
				<path d="M470 110h8M470 130h8M470 150h8M470 170h8M470 190h8M470 210h8M470 230h8M470 250h8M470 270h8M470 290h8M470 310h8M470 330h8" stroke="#d95f12" stroke-width="2"/>
				<rect x="360" y="96" width="160" height="7" fill="#f47521"/>
				<rect x="500" y="103" width="18" height="14" fill="#0f1e33"/>
				<path d="M474 96 L474 80 L380 96 M474 80 L516 96" stroke="#d95f12" stroke-width="2" fill="none"/>
				<g class="tp-404-hook">
					<line x1="392" y1="103" x2="392" y2="168" stroke="#0f1e33" stroke-width="1.5"/>
					<path d="M386 168h12v6c0 4-3 7-6 7s-6-3-6-7" fill="none" stroke="#0f1e33" stroke-width="2"/>
					<rect x="378" y="180" width="28" height="18" rx="2" fill="#f47521"/>
					<text x="392" y="193" text-anchor="middle" font-size="10" font-weight="800" fill="#fff" font-family="inherit">?</text>
				</g>
			</g>

			<!-- Mặt đất, cây, đường -->
			<rect x="0" y="346" width="560" height="74" fill="#e6efe4"/>
			<path d="M-10 420 C 140 360, 330 372, 570 350 L 570 420 Z" fill="#cfd8e3"/>
			<path d="M20 420 C 160 372, 330 382, 570 364" stroke="#fff" stroke-width="3" stroke-dasharray="14 12" fill="none" class="tp-404-road"/>
			<g class="tp-404-trees">
				<circle cx="30" cy="340" r="16" fill="#7fb26a"/><circle cx="54" cy="344" r="12" fill="#6aa257"/>
				<circle cx="140" cy="342" r="14" fill="#7fb26a"/><circle cx="420" cy="342" r="15" fill="#6aa257"/>
				<circle cx="446" cy="345" r="11" fill="#7fb26a"/><circle cx="530" cy="340" r="16" fill="#6aa257"/>
			</g>

			<!-- Chữ 404 nổi khối -->
			<g class="tp-404-digits" font-family="inherit" font-weight="800" font-size="150" text-anchor="middle">
				<text x="286" y="352" fill="#b9c6d6">404</text>
				<text x="280" y="346" fill="url(#tp404Text)" stroke="#d3dce7" stroke-width="1.5">404</text>
			</g>
		</g>
	</svg>
	<?php
	return ob_get_clean();
}
