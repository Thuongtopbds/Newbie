<?php
/**
 * Template Name: TOPBDS – Văn bản (chính sách, điều khoản)
 *
 * Trang văn bản dài: đầu trang xanh navy (tiêu đề + ngày cập nhật tự động), mục lục dính bên trái
 * lấy từ các tiêu đề H2, nội dung một cột dễ đọc.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	list( $tp_content, $tp_toc ) = tp_content_toc( apply_filters( 'the_content', get_the_content() ) );
	?>
	<div class="tp-doc">
		<section class="tp-pagehero tp-doc__hero">
			<div class="container">
				<nav class="tp-crumbs" aria-label="Breadcrumb">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Trang chủ</a>
					<span class="tp-crumbs__sep" aria-hidden="true">›</span>
					<span aria-current="page"><?php the_title(); ?></span>
				</nav>
				<h1><?php the_title(); ?></h1>
				<p class="tp-doc__meta"><?php echo tp_icon( 'calendar', 16 ); ?>Cập nhật lần cuối: <?php echo esc_html( get_the_modified_date( 'd/m/Y' ) ); ?></p>
			</div>
		</section>

		<div class="container tp-doc__body<?php echo $tp_toc ? '' : ' tp-doc__body--single'; ?>">
			<?php if ( $tp_toc ) : ?>
				<aside class="tp-doc__toc" aria-label="Mục lục">
					<p class="tp-doc__toc-title">Nội dung</p>
					<ol>
						<?php foreach ( $tp_toc as list( $tp_anchor, $tp_label ) ) : ?>
							<li><a href="#<?php echo esc_attr( $tp_anchor ); ?>"><?php echo esc_html( preg_replace( '/^\d+\.\s*/u', '', $tp_label ) ); ?></a></li>
						<?php endforeach; ?>
					</ol>
				</aside>
			<?php endif; ?>

			<article class="tp-doc__content">
				<?php echo $tp_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nội dung bài đã qua the_content. ?>
			</article>
		</div>
	</div>
	<?php
endwhile;

get_footer();
