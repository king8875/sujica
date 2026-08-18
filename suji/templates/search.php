<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

global $wp_query;
$suji_query = get_search_query();
$suji_found = (int) $wp_query->found_posts;
?>

<main id="primary" class="site-main board-archive search-results-page">

	<header class="board-archive-header">
		<span class="board-archive-icon" aria-hidden="true">
			<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor"
			     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
				<circle cx="11" cy="11" r="7"></circle>
				<line x1="16.5" y1="16.5" x2="21" y2="21"></line>
			</svg>
		</span>
		<h1 class="page-title">
			검색 결과
			<span class="board-archive-count"><?php
				printf(
					/* translators: %s: 결과 수 */
					esc_html__( '전체 %s건', 'suji' ),
					esc_html( number_format_i18n( $suji_found ) )
				);
			?></span>
		</h1>
		<p class="board-archive-desc">
			<?php if ( $suji_query ) : ?>
				<strong class="search-term"><?php echo esc_html( $suji_query ); ?></strong>
				<?php esc_html_e( '(으)로 검색했습니다.', 'suji' ); ?>
			<?php else : ?>
				<?php esc_html_e( '검색어를 입력해주세요.', 'suji' ); ?>
			<?php endif; ?>
		</p>
	</header>

	<form class="search-refine" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label class="screen-reader-text" for="search-refine-field"><?php esc_html_e( '검색', 'suji' ); ?></label>
		<input type="search" id="search-refine-field" class="search-refine-field" name="s"
		       value="<?php echo esc_attr( $suji_query ); ?>"
		       placeholder="<?php esc_attr_e( '다른 검색어로 다시 찾기', 'suji' ); ?>">
		<button type="submit" class="search-refine-submit"><?php esc_html_e( '검색', 'suji' ); ?></button>
	</form>

	<?php if ( have_posts() ) : ?>
		<div class="board-list">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'search' );
			endwhile;
			?>
		</div>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<div class="search-empty">
			<span class="search-empty-icon" aria-hidden="true">
				<svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor"
				     stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
					<circle cx="11" cy="11" r="7"></circle>
					<line x1="16.5" y1="16.5" x2="21" y2="21"></line>
					<line x1="8.5" y1="11" x2="13.5" y2="11"></line>
				</svg>
			</span>
			<p class="search-empty-title"><?php esc_html_e( '검색 결과가 없습니다', 'suji' ); ?></p>
			<p class="search-empty-desc"><?php esc_html_e( '검색어의 철자를 확인하시거나, 더 짧은 단어로 다시 찾아보세요.', 'suji' ); ?></p>
		</div>
	<?php endif; ?>
</main>

<?php
get_footer();
