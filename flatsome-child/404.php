<?php
/**
 * Trang 404: báo lỗi thân thiện rồi dẫn khách sang nội dung khác thay vì rời trang.
 *
 * Hình minh hoạ → gợi ý theo địa chỉ khách gõ sai → ô tìm kiếm → loại hình → dự án nổi bật → tin tức.
 */

defined( 'ABSPATH' ) || exit;

$tp_words       = tp_404_words();
$tp_suggestions = tp_404_suggestions( $tp_words );
$tp_art_id      = (int) get_theme_mod( 'tp_404_image', 0 );
$tp_all_url     = get_post_type_archive_link( 'du_an' );
$tp_news_url    = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );

$tp_types = get_terms( array(
	'taxonomy'   => 'loai_hinh',
	'hide_empty' => false,
	'orderby'    => 'count',
	'order'      => 'DESC',
	'number'     => 5,
	'parent'     => 0,
) );
$tp_types = is_wp_error( $tp_types ) ? array() : $tp_types;

$tp_projects = get_posts( array(
	'post_type'      => 'du_an',
	'posts_per_page' => 3,
	'meta_query'     => array( array( 'key' => 'tp_noi_bat', 'value' => '1' ) ),
	'no_found_rows'  => true,
) );
if ( ! $tp_projects ) {
	$tp_projects = get_posts( array( 'post_type' => 'du_an', 'posts_per_page' => 3, 'no_found_rows' => true ) );
}

$tp_news = get_posts( array( 'post_type' => 'post', 'posts_per_page' => 3, 'no_found_rows' => true, 'ignore_sticky_posts' => true ) );

get_header();
?>
<div class="tp-404">

	<section class="tp-404__hero">
		<div class="container tp-404__hero-inner">
			<div class="tp-404__text">
				<p class="tp-404__code" aria-hidden="true">404<span class="tp-404__spark"><i></i><i></i><i></i></span></p>
				<h1 class="tp-404__heading">Rất tiếc! Trang bạn tìm kiếm không tồn tại.</h1>
				<p class="tp-404__lead">Trang có thể đã được di chuyển, đổi tên hoặc không còn nữa. Đừng lo, còn rất nhiều dự án đang chờ bạn khám phá.</p>
				<div class="tp-404__actions">
					<a class="tp-btn tp-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo tp_icon( 'home', 18 ); ?>Về trang chủ</a>
					<a class="tp-btn tp-btn--ghost" href="<?php echo esc_url( $tp_all_url ); ?>"><?php echo tp_icon( 'building', 18 ); ?>Xem tất cả dự án</a>
				</div>
				<p class="tp-404__help">
					Cần hỗ trợ ngay? Gọi <a href="<?php echo esc_attr( tp_hotline_href() ); ?>"><?php echo esc_html( tp_hotline() ); ?></a>
					hoặc <a href="<?php echo esc_url( tp_zalo_url() ); ?>" target="_blank" rel="noopener">chat Zalo</a>.
				</p>
			</div>
			<div class="tp-404__art">
				<?php
				echo $tp_art_id
					? wp_get_attachment_image( $tp_art_id, 'large', false, array( 'alt' => '', 'class' => 'tp-404-photo tp-no-lazy', 'data-no-lazy' => '1' ) )
					: tp_404_illustration(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh của theme.
				?>
			</div>
		</div>
	</section>

	<div class="container">

		<?php if ( $tp_suggestions ) : ?>
			<section class="tp-404__maybe" aria-labelledby="tp-404-maybe">
				<h2 id="tp-404-maybe" class="tp-404__maybe-title"><?php echo tp_icon( 'bulb', 20 ); ?>Có phải bạn đang tìm:</h2>
				<ul>
					<?php foreach ( $tp_suggestions as $tp_id ) : ?>
						<li><a href="<?php echo esc_url( get_permalink( $tp_id ) ); ?>"><?php echo esc_html( get_the_title( $tp_id ) ); ?><?php echo tp_icon( 'arrow', 14 ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>

		<section class="tp-404__search">
			<span class="tp-404__search-icon"><?php echo tp_icon( 'search', 26 ); ?></span>
			<div class="tp-404__search-text">
				<h2>Bạn đang tìm kiếm điều gì?</h2>
				<p>Tìm theo tên dự án, khu vực, loại hình hoặc chủ đầu tư.</p>
			</div>
			<form class="tp-404__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="tp-404-s">Tìm kiếm dự án</label>
				<input type="search" id="tp-404-s" name="s" value="<?php echo esc_attr( implode( ' ', $tp_words ) ); ?>" placeholder="VD: căn hộ Hà Nội, biệt thự, đất nền…">
				<input type="hidden" name="post_type" value="du_an">
				<button type="submit"><?php echo tp_icon( 'search', 18 ); ?><span>Tìm kiếm</span></button>
			</form>
		</section>

		<section class="tp-404__block" aria-labelledby="tp-404-types">
			<h2 id="tp-404-types" class="tp-404__title">Khám phá bất động sản</h2>
			<div class="tp-404__types">
				<a class="tp-404__type" href="<?php echo esc_url( $tp_all_url ); ?>">
					<span class="tp-404__type-icon"><?php echo tp_icon( 'building', 30 ); ?></span>
					<strong>Tất cả dự án</strong>
					<span><?php echo esc_html( sprintf( '%d dự án', wp_count_posts( 'du_an' )->publish ) ); ?></span>
				</a>
				<?php foreach ( $tp_types as $tp_type ) : ?>
					<a class="tp-404__type" href="<?php echo esc_url( get_term_link( $tp_type ) ); ?>">
						<span class="tp-404__type-icon"><?php echo tp_icon( tp_404_type_icon( $tp_type->slug ), 30 ); ?></span>
						<strong><?php echo esc_html( $tp_type->name ); ?></strong>
						<span><?php echo esc_html( sprintf( '%d dự án', $tp_type->count ) ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</section>

		<?php if ( $tp_projects ) : ?>
			<section class="tp-404__block" aria-labelledby="tp-404-projects">
				<h2 id="tp-404-projects" class="tp-404__title">Dự án đang được quan tâm</h2>
				<div class="tp-grid tp-grid--scroll" style="--tp-cols:3">
					<?php
					foreach ( $tp_projects as $tp_project ) {
						echo tp_project_card( $tp_project->ID, array( 'heading' => 'h3' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
				<p class="tp-404__more"><a class="tp-btn tp-btn--line" href="<?php echo esc_url( $tp_all_url ); ?>">Xem thêm dự án <?php echo tp_icon( 'arrow', 16 ); ?></a></p>
			</section>
		<?php endif; ?>

		<?php if ( $tp_news ) : ?>
			<section class="tp-404__block" aria-labelledby="tp-404-news">
				<h2 id="tp-404-news" class="tp-404__title">Tin tức bất động sản</h2>
				<div class="tp-404__news">
					<?php foreach ( $tp_news as $tp_post ) : ?>
						<a class="tp-404__post" href="<?php echo esc_url( get_permalink( $tp_post ) ); ?>">
							<span class="tp-404__post-thumb"><?php echo tp_post_image( $tp_post->ID, 'tp-thumb' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<span class="tp-404__post-body">
								<strong><?php echo esc_html( get_the_title( $tp_post ) ); ?></strong>
								<span class="tp-404__post-date"><?php echo tp_icon( 'calendar', 14 ); ?><?php echo esc_html( get_the_date( 'd/m/Y', $tp_post ) ); ?></span>
							</span>
						</a>
					<?php endforeach; ?>
				</div>
				<p class="tp-404__more"><a class="tp-btn tp-btn--line" href="<?php echo esc_url( $tp_news_url ); ?>">Xem thêm tin tức <?php echo tp_icon( 'arrow', 16 ); ?></a></p>
			</section>
		<?php endif; ?>

	</div>
</div>
<?php
get_footer();
