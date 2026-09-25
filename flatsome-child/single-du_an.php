<?php
/**
 * Trang chi tiết dự án.
 *
 * Bố cục: phần tóm tắt (tiêu đề, giá, thông số, tư vấn viên) → thư viện ảnh → mục lục → Đặc điểm dự án
 * → nội dung bài → bản đồ → form nhận báo giá → dự án liên quan. Cột phải có tin mới, loại hình và
 * hộp liên hệ bám theo khi cuộn.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$tp_id      = get_the_ID();
	$tp_specs   = tp_project_specs( $tp_id );
	$tp_gallery = tp_project_gallery_ids( $tp_id );
	$tp_map     = get_post_meta( $tp_id, 'tp_ban_do', true );
	$tp_form    = get_theme_mod( 'tp_project_form', '' );
	$tp_loc     = get_post_meta( $tp_id, 'tp_dia_chi_day_du', true ) ?: get_post_meta( $tp_id, 'tp_dia_chi', true ) ?: tp_term_names( $tp_id, 'khu_vuc' );
	$tp_price   = get_post_meta( $tp_id, 'tp_gia', true );

	list( $tp_content, $tp_toc ) = tp_content_toc( apply_filters( 'the_content', get_the_content() ) );

	$tp_nav = $tp_specs ? array( array( 'dac-diem', 'Đặc điểm' ) ) : array();
	foreach ( $tp_toc as $tp_item ) {
		$tp_nav[] = $tp_item;
	}
	if ( $tp_map ) {
		$tp_nav[] = array( 'ban-do', 'Bản đồ' );
	}
	$tp_nav[] = array( 'tp-lien-he', 'Nhận báo giá' );

	$tp_facts = array();
	foreach ( array( 'tp_dien_tich' => array( 'area', 'Diện tích' ), 'tp_phong_ngu' => array( 'bed', 'Phòng ngủ' ), 'tp_phap_ly' => array( 'file', 'Pháp lý' ) ) as $tp_key => $tp_fact ) {
		if ( $tp_value = get_post_meta( $tp_id, $tp_key, true ) ) {
			$tp_facts[] = array( $tp_fact[0], $tp_fact[1], $tp_value );
		}
	}
	?>
	<div class="tp-project">

		<section class="tp-summary">
			<div class="container tp-summary__inner">
				<div class="tp-summary__main">
					<?php echo tp_breadcrumbs( $tp_id ); ?>
					<div class="tp-summary__badge"><?php echo tp_project_badge( $tp_id ); ?></div>
					<h1 class="tp-summary__title"><?php the_title(); ?></h1>
					<?php if ( $tp_loc ) : ?>
						<p class="tp-summary__loc"><?php echo tp_icon( 'pin', 16 ); ?><span><?php echo esc_html( $tp_loc ); ?></span></p>
					<?php endif; ?>

					<dl class="tp-facts">
						<?php if ( $tp_price ) : ?>
							<div class="tp-fact tp-fact--price"><dt>Giá bán</dt><dd><?php echo esc_html( $tp_price ); ?></dd></div>
						<?php endif; ?>
						<?php foreach ( $tp_facts as list( $tp_icon, $tp_label, $tp_value ) ) : ?>
							<div class="tp-fact"><dt><?php echo tp_icon( $tp_icon, 14 ); ?><?php echo esc_html( $tp_label ); ?></dt><dd><?php echo esc_html( $tp_value ); ?></dd></div>
						<?php endforeach; ?>
					</dl>

					<div class="tp-summary__actions">
						<a class="tp-btn tp-btn--primary" href="#tp-lien-he"><?php echo tp_icon( 'download', 16 ); ?><span>Nhận bảng giá &amp; tài liệu</span></a>
						<a class="tp-btn tp-btn--outline tp-btn--dark" href="<?php echo esc_url( tp_zalo_url() ); ?>" target="_blank" rel="noopener"><?php echo tp_icon( 'chat', 16 ); ?><span>Chat Zalo</span></a>
						<button type="button" class="tp-fav tp-fav--inline" data-tp-fav="<?php echo (int) $tp_id; ?>" aria-pressed="false" aria-label="Lưu dự án"><?php echo tp_icon( 'heart', 18 ); ?></button>
					</div>
				</div>

				<?php echo tp_agent_card(); ?>
			</div>
		</section>

		<div class="container tp-project__body">
			<div class="tp-project__main">

				<?php if ( $tp_gallery ) : ?>
					<div class="tp-gallery" data-tp-gallery>
						<?php $tp_first = $tp_gallery[0]; ?>
						<a class="tp-gallery__main" href="<?php echo esc_url( wp_get_attachment_image_url( $tp_first, 'full' ) ); ?>" data-tp-gallery-open>
							<?php echo wp_get_attachment_image( $tp_first, 'large', false, array( 'alt' => get_the_title(), 'fetchpriority' => 'high', 'loading' => 'eager' ) ); ?>
							<?php if ( count( $tp_gallery ) > 1 ) : ?>
								<span class="tp-gallery__count"><?php echo esc_html( count( $tp_gallery ) ); ?> ảnh</span>
							<?php endif; ?>
						</a>
						<?php if ( count( $tp_gallery ) > 1 ) : ?>
							<div class="tp-gallery__thumbs">
								<?php foreach ( $tp_gallery as $tp_index => $tp_image ) : ?>
									<button type="button" class="tp-gallery__thumb<?php echo 0 === $tp_index ? ' is-active' : ''; ?>"
										data-full="<?php echo esc_url( wp_get_attachment_image_url( $tp_image, 'full' ) ); ?>"
										data-src="<?php echo esc_url( wp_get_attachment_image_url( $tp_image, 'large' ) ); ?>"
										data-srcset="<?php echo esc_attr( (string) wp_get_attachment_image_srcset( $tp_image, 'large' ) ); ?>"
										aria-label="Xem ảnh <?php echo esc_attr( $tp_index + 1 ); ?>">
										<?php echo wp_get_attachment_image( $tp_image, 'tp-thumb', false, array( 'alt' => '' ) ); ?>
									</button>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<nav class="tp-toc" aria-label="Mục lục dự án" data-tp-toc>
					<?php foreach ( $tp_nav as list( $tp_anchor, $tp_label ) ) : ?>
						<a href="#<?php echo esc_attr( $tp_anchor ); ?>"><?php echo esc_html( $tp_label ); ?></a>
					<?php endforeach; ?>
				</nav>

				<?php if ( $tp_specs ) : ?>
					<section class="tp-box" id="dac-diem">
						<h2 class="tp-box__title">Đặc điểm dự án</h2>
						<table class="tp-specs">
							<tbody>
								<?php foreach ( $tp_specs as list( $tp_icon, $tp_label, $tp_value ) ) : ?>
									<tr>
										<th scope="row"><?php echo tp_icon( $tp_icon, 16 ); ?><?php echo esc_html( $tp_label ); ?></th>
										<td><?php echo esc_html( $tp_value ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</section>
				<?php endif; ?>

				<div class="tp-content entry-content">
					<?php echo $tp_content; ?>
				</div>

				<?php if ( $tp_map ) : ?>
					<section class="tp-box" id="ban-do">
						<h2 class="tp-box__title">Vị trí trên bản đồ</h2>
						<div class="tp-map">
							<iframe title="Bản đồ vị trí <?php echo esc_attr( get_the_title() ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
								src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . rawurlencode( $tp_map ) . '&z=15&output=embed' ); ?>"></iframe>
						</div>
					</section>
				<?php endif; ?>

				<section class="tp-quote" id="tp-lien-he">
					<div class="tp-quote__text">
						<?php echo tp_icon( 'file', 28 ); ?>
						<div>
							<h2 class="tp-quote__title">Nhận bảng giá &amp; chính sách mới nhất</h2>
							<p>Để lại thông tin, chuyên viên sẽ gửi bảng giá, mặt bằng và tài liệu <?php the_title(); ?> trong ít phút.</p>
						</div>
					</div>
					<div class="tp-quote__form">
						<?php
						if ( $tp_form ) {
							echo do_shortcode( $tp_form );
						} else {
							echo do_shortcode( '[tp_contact_buttons style="cta" call_text="Gọi ngay ' . esc_attr( tp_hotline() ) . '"]' );
						}
						?>
					</div>
				</section>

				<footer class="tp-project__foot">
					<ul class="tp-terms-inline">
						<?php foreach ( array( 'loai_hinh', 'khu_vuc', 'trang_thai' ) as $tp_tax ) : ?>
							<?php foreach ( (array) get_the_terms( $tp_id, $tp_tax ) as $tp_term ) : ?>
								<?php if ( $tp_term instanceof WP_Term ) : ?>
									<li><a href="<?php echo esc_url( get_term_link( $tp_term ) ); ?>"><?php echo esc_html( $tp_term->name ); ?></a></li>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</ul>
					<?php if ( shortcode_exists( 'share' ) ) : ?>
						<div class="tp-share"><?php echo do_shortcode( '[share]' ); ?></div>
					<?php endif; ?>
				</footer>
			</div>

			<aside class="tp-project__side">
				<?php
				$tp_news = get_posts( array( 'posts_per_page' => 4, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );
				if ( $tp_news ) :
					?>
					<section class="tp-widget">
						<h2 class="tp-widget__title">Tin tức mới nhất</h2>
						<ul class="tp-news__list">
							<?php foreach ( $tp_news as $tp_post ) : ?>
								<li><a class="tp-news__item" href="<?php echo esc_url( get_permalink( $tp_post ) ); ?>">
									<span class="tp-news__thumb"><?php echo tp_post_image( $tp_post->ID, 'tp-thumb' ); ?></span>
									<span class="tp-news__item-text"><span class="tp-news__item-title"><?php echo esc_html( get_the_title( $tp_post ) ); ?></span>
									<time datetime="<?php echo esc_attr( get_the_date( 'c', $tp_post ) ); ?>"><?php echo esc_html( get_the_date( 'd/m/Y', $tp_post ) ); ?></time></span>
								</a></li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>

				<?php
				$tp_types = get_terms( array( 'taxonomy' => 'loai_hinh', 'hide_empty' => true ) );
				if ( $tp_types && ! is_wp_error( $tp_types ) ) :
					?>
					<section class="tp-widget">
						<h2 class="tp-widget__title">Loại hình dự án</h2>
						<ul class="tp-widget__links">
							<?php foreach ( $tp_types as $tp_term ) : ?>
								<li><a href="<?php echo esc_url( get_term_link( $tp_term ) ); ?>"><?php echo esc_html( $tp_term->name ); ?><span><?php echo esc_html( number_format_i18n( $tp_term->count ) ); ?></span></a></li>
							<?php endforeach; ?>
						</ul>
					</section>
				<?php endif; ?>

				<div class="tp-side-sticky">
					<?php echo tp_agent_card( true ); ?>
				</div>
			</aside>
		</div>

		<?php $tp_related = tp_related_project_ids( $tp_id ); ?>
		<?php if ( $tp_related ) : ?>
			<section class="tp-related">
				<div class="container">
					<?php echo do_shortcode( '[tp_heading title="Dự án liên quan" sub="Các dự án cùng loại hình hoặc cùng khu vực" link="' . esc_url( get_post_type_archive_link( 'du_an' ) ) . '" link_text="Xem tất cả dự án"]' ); ?>
					<div class="tp-grid tp-grid--scroll" style="--tp-cols:4">
						<?php foreach ( $tp_related as $tp_related_id ) : ?>
							<?php echo tp_project_card( $tp_related_id ); ?>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( count( $tp_gallery ) > 0 ) : ?>
			<dialog class="tp-lightbox" data-tp-lightbox aria-label="Ảnh dự án">
				<button type="button" class="tp-lightbox__close" data-tp-lightbox-close aria-label="Đóng">×</button>
				<button type="button" class="tp-lightbox__nav tp-lightbox__nav--prev" data-tp-lightbox-step="-1" aria-label="Ảnh trước">‹</button>
				<img src="" alt="">
				<button type="button" class="tp-lightbox__nav tp-lightbox__nav--next" data-tp-lightbox-step="1" aria-label="Ảnh sau">›</button>
			</dialog>
		<?php endif; ?>
	</div>
	<?php
endwhile;

get_footer();
