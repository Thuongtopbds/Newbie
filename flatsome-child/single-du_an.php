<?php
/**
 * Trang chi tiết dự án.
 *
 * Hero ảnh nền (giống trang chủ) với khung kính thông số → thanh mục lục dính → lưới ảnh
 * → Tổng quan dự án → nội dung bài → bản đồ, cùng thẻ "Nhận báo giá" dính ở cột phải
 * → dự án liên quan.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$tp_id      = get_the_ID();
	$tp_specs   = array_values( array_filter( tp_project_specs( $tp_id ), function ( $row ) {
		return 'Giá bán' !== $row[1];
	} ) );
	$tp_facts   = tp_project_facts( $tp_id );
	$tp_photos  = tp_project_gallery_ids( $tp_id );
	$tp_mosaic  = has_post_thumbnail() ? array_slice( $tp_photos, 1 ) : $tp_photos;
	$tp_map     = get_post_meta( $tp_id, 'tp_ban_do', true );
	$tp_price   = get_post_meta( $tp_id, 'tp_gia', true );
	$tp_loc     = get_post_meta( $tp_id, 'tp_dia_chi_day_du', true ) ?: get_post_meta( $tp_id, 'tp_dia_chi', true ) ?: tp_term_names( $tp_id, 'khu_vuc' );
	$tp_types   = get_the_terms( $tp_id, 'loai_hinh' );

	list( $tp_content, $tp_toc ) = tp_content_toc( apply_filters( 'the_content', get_the_content() ) );

	$tp_nav = $tp_specs ? array( array( 'tong-quan', 'Tổng quan' ) ) : array();
	$tp_nav = array_merge( $tp_nav, $tp_toc );
	if ( $tp_map ) {
		$tp_nav[] = array( 'ban-do', 'Bản đồ' );
	}
	?>
	<div class="tp-project">

		<section class="tp-phero">
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="tp-phero__bg"><?php the_post_thumbnail( 'full', array( 'alt' => '', 'fetchpriority' => 'high', 'loading' => 'eager', 'sizes' => '100vw' ) ); ?></div>
			<?php endif; ?>
			<div class="container tp-phero__inner">
				<div class="tp-phero__main">
					<?php echo tp_breadcrumbs( $tp_id ); ?>
					<div class="tp-phero__tags">
						<?php echo tp_project_badge( $tp_id ); ?>
						<?php if ( $tp_types && ! is_wp_error( $tp_types ) ) : ?>
							<?php foreach ( $tp_types as $tp_term ) : ?>
								<a class="tp-phero__tag" href="<?php echo esc_url( get_term_link( $tp_term ) ); ?>"><?php echo esc_html( $tp_term->name ); ?></a>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
					<h1 class="tp-phero__title"><?php the_title(); ?></h1>
					<?php if ( $tp_loc ) : ?>
						<p class="tp-phero__loc"><?php echo tp_icon( 'pin', 18 ); ?><span><?php echo esc_html( $tp_loc ); ?></span></p>
					<?php endif; ?>
					<div class="tp-phero__actions">
						<a class="tp-btn tp-btn--primary" href="#tp-lien-he"><?php echo tp_icon( 'download', 16 ); ?><span>Nhận bảng giá</span></a>
						<a class="tp-btn tp-btn--outline" href="<?php echo esc_url( tp_zalo_url() ); ?>" target="_blank" rel="noopener"><?php echo tp_icon( 'chat', 16 ); ?><span>Chat Zalo</span></a>
						<?php if ( $tp_photos ) : ?>
							<button type="button" class="tp-btn tp-btn--glass" data-tp-photo-open="0"><?php echo tp_icon( 'grid', 16 ); ?><span>Xem <?php echo esc_html( count( $tp_photos ) ); ?> ảnh</span></button>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( $tp_price || $tp_facts ) : ?>
					<dl class="tp-phero__facts">
						<?php if ( $tp_price ) : ?>
							<div class="tp-phero__fact tp-phero__fact--price"><dt>Giá bán</dt><dd><?php echo esc_html( $tp_price ); ?></dd></div>
						<?php endif; ?>
						<?php foreach ( $tp_facts as list( $tp_icon, $tp_label, $tp_value ) ) : ?>
							<div class="tp-phero__fact">
								<span class="tp-trust__icon"><?php echo tp_icon( $tp_icon, 20 ); ?></span>
								<dt><?php echo esc_html( $tp_label ); ?></dt>
								<dd><?php echo esc_html( $tp_value ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>
		</section>

		<?php if ( count( $tp_nav ) > 1 ) : ?>
			<nav class="tp-subnav" aria-label="Mục lục dự án">
				<div class="container tp-subnav__inner">
					<div class="tp-toc" data-tp-toc>
						<?php foreach ( $tp_nav as list( $tp_anchor, $tp_label ) ) : ?>
							<a href="#<?php echo esc_attr( $tp_anchor ); ?>"><?php echo esc_html( $tp_label ); ?></a>
						<?php endforeach; ?>
					</div>
					<a class="tp-btn tp-btn--primary tp-subnav__cta" href="#tp-lien-he">Nhận báo giá</a>
				</div>
			</nav>
		<?php endif; ?>

		<div class="container tp-project__body">
			<div class="tp-project__main">

				<?php if ( $tp_mosaic ) : ?>
					<?php
					$tp_offset = count( $tp_photos ) - count( $tp_mosaic );
					$tp_shown  = array_slice( $tp_mosaic, 0, 5 );
					$tp_more   = count( $tp_mosaic ) - count( $tp_shown );
					?>
					<div class="tp-mosaic tp-mosaic--<?php echo count( $tp_shown ); ?>">
						<?php foreach ( $tp_shown as $tp_index => $tp_image ) : ?>
							<a class="tp-mosaic__item" href="<?php echo esc_url( wp_get_attachment_image_url( $tp_image, 'full' ) ); ?>" data-tp-photo-open="<?php echo (int) ( $tp_index + $tp_offset ); ?>">
								<?php echo wp_get_attachment_image( $tp_image, 0 === $tp_index ? 'large' : 'tp-card', false, array( 'alt' => get_the_title() . ' – ảnh ' . ( $tp_index + 1 + $tp_offset ) ) ); ?>
								<?php if ( $tp_more > 0 && count( $tp_shown ) - 1 === $tp_index ) : ?>
									<span class="tp-mosaic__more">+<?php echo (int) $tp_more; ?> ảnh</span>
								<?php endif; ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( $tp_specs ) : ?>
					<section class="tp-section" id="tong-quan">
						<h2 class="tp-section__title">Tổng quan dự án</h2>
						<dl class="tp-specgrid">
							<?php foreach ( $tp_specs as list( $tp_icon, $tp_label, $tp_value ) ) : ?>
								<div class="tp-specgrid__item">
									<span class="tp-specgrid__icon"><?php echo tp_icon( $tp_icon, 20 ); ?></span>
									<div><dt><?php echo esc_html( $tp_label ); ?></dt><dd><?php echo esc_html( $tp_value ); ?></dd></div>
								</div>
							<?php endforeach; ?>
						</dl>
					</section>
				<?php endif; ?>

				<div class="tp-content entry-content">
					<?php echo $tp_content; ?>
				</div>

				<?php if ( $tp_map ) : ?>
					<section class="tp-section" id="ban-do">
						<h2 class="tp-section__title">Vị trí trên bản đồ</h2>
						<div class="tp-map">
							<iframe title="Bản đồ vị trí <?php echo esc_attr( get_the_title() ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
								src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . rawurlencode( $tp_map ) . '&z=15&output=embed' ); ?>"></iframe>
						</div>
					</section>
				<?php endif; ?>

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
				<?php echo tp_quote_card( $tp_id ); ?>
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

		<?php if ( $tp_photos ) : ?>
			<dialog class="tp-lightbox" data-tp-lightbox aria-label="Ảnh dự án"
				data-photos="<?php echo esc_attr( wp_json_encode( array_map( function ( $id ) { return wp_get_attachment_image_url( $id, 'full' ); }, $tp_photos ) ) ); ?>">
				<button type="button" class="tp-lightbox__close" data-tp-lightbox-close aria-label="Đóng">×</button>
				<button type="button" class="tp-lightbox__nav tp-lightbox__nav--prev" data-tp-lightbox-step="-1" aria-label="Ảnh trước">‹</button>
				<img src="" alt="">
				<p class="tp-lightbox__count" aria-live="polite"></p>
				<button type="button" class="tp-lightbox__nav tp-lightbox__nav--next" data-tp-lightbox-step="1" aria-label="Ảnh sau">›</button>
			</dialog>
		<?php endif; ?>
	</div>
	<?php
endwhile;

get_footer();
