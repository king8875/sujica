<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main search-results-page">
	<header class="board-archive-header">
		<span class="board-archive-icon" aria-hidden="true">
			<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
				<circle cx="11" cy="11" r="7"></circle>
				<line x1="16.5" y1="16.5" x2="21" y2="21"></line>
			</svg>
		</span>
		<h1 class="page-title">
			<?php
			printf(
				/* translators: %s: search query. */
				esc_html__( '"%s" 검색 결과', 'suji' ),
				esc_html( get_search_query() )
			);
			?>
		</h1>
		<p class="board-archive-desc">
			<?php
			global $wp_query;
			printf(
				/* translators: %s: number of results. */
				esc_html__( '총 %s건의 결과를 찾았습니다.', 'suji' ),
				esc_html( number_format_i18n( $wp_query->found_posts ) )
			);
			?>
		</p>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="board-list">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'board' );
			endwhile;
			?>
		</div>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<div class="search-no-results">
			<p><?php esc_html_e( '검색 결과가 없습니다. 다른 검색어로 시도해보세요.', 'suji' ); ?></p>
			<?php get_search_form(); ?>
		</div>
	<?php endif; ?>
</main>

<?php
get_footer();
