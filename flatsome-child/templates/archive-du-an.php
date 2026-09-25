<?php
/**
 * Danh sách dự án: /du-an/, trang Loại hình / Khu vực / Trạng thái và kết quả tìm kiếm dự án.
 */

defined( 'ABSPATH' ) || exit;

if ( is_search() ) {
	$tp_title = get_search_query() ? sprintf( 'Kết quả tìm kiếm: “%s”', get_search_query() ) : 'Tìm kiếm dự án';
} elseif ( is_tax() ) {
	$tp_title = 'Dự án ' . single_term_title( '', false );
} else {
	$tp_title = 'Tất cả dự án';
}

get_header();
?>
<div class="tp-archive">
	<div class="container">
		<header class="tp-archive__head">
			<h1 class="tp-archive__title"><?php echo esc_html( $tp_title ); ?></h1>
			<?php if ( is_tax() && term_description() ) : ?>
				<div class="tp-archive__desc"><?php echo wp_kses_post( term_description() ); ?></div>
			<?php endif; ?>
		</header>

		<form class="tp-filter" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="hidden" name="post_type" value="du_an">
			<label>
				<span>Từ khoá</span>
				<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Tên dự án, khu vực...">
			</label>
			<?php foreach ( array( 'loai_hinh' => 'Loại hình', 'khu_vuc' => 'Khu vực' ) as $tp_tax => $tp_label ) : ?>
				<label>
					<span><?php echo esc_html( $tp_label ); ?></span>
					<?php
					wp_dropdown_categories( array(
						'taxonomy'        => $tp_tax,
						'name'            => $tp_tax,
						'value_field'     => 'slug',
						'selected'        => get_query_var( $tp_tax ),
						'show_option_all' => 'Tất cả',
						'hierarchical'    => true,
						'hide_empty'      => false,
						'orderby'         => 'name',
					) );
					?>
				</label>
			<?php endforeach; ?>
			<button type="submit" class="tp-btn tp-btn--primary"><?php echo tp_icon( 'search', 16 ); ?><span>Lọc dự án</span></button>
		</form>

		<?php if ( have_posts() ) : ?>
			<p class="tp-archive__count"><?php echo esc_html( sprintf( '%s dự án', number_format_i18n( $wp_query->found_posts ) ) ); ?></p>
			<div class="tp-grid" style="--tp-cols:4">
				<?php
				while ( have_posts() ) {
					the_post();
					echo tp_project_card( get_the_ID(), array( 'heading' => 'h2' ) );
				}
				?>
			</div>
			<?php
			the_posts_pagination( array(
				'class'     => 'tp-pagination',
				'prev_text' => '‹ Trước',
				'next_text' => 'Sau ›',
			) );
			?>
		<?php else : ?>
			<p class="tp-empty">Không tìm thấy dự án phù hợp. Hãy thử từ khoá khác hoặc bỏ bớt bộ lọc.</p>
		<?php endif; ?>
	</div>
</div>
<?php
get_footer();
