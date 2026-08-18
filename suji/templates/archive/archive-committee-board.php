<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$suji_term = get_queried_object();
?>

<main id="primary" class="site-main board-archive board-archive-committee-board">
	<header class="board-archive-header">
		<span class="board-archive-icon" aria-hidden="true">
			<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
				<circle cx="9" cy="8" r="3"></circle>
				<path d="M3 20v-1a5 5 0 0 1 5-5h2a5 5 0 0 1 5 5v1"></path>
				<circle cx="17" cy="8" r="2.5"></circle>
				<path d="M16 14.5c2.5.3 4 1.8 4 4.5v1"></path>
			</svg>
		</span>
		<h1 class="page-title">
			<?php echo esc_html( $suji_term->name ); ?>
			<?php if ( $suji_term->count ) : ?>
				<span class="board-archive-count"><?php
					printf(
						/* translators: %s: 글 수 */
						esc_html__( '전체 %s건', 'suji' ),
						esc_html( number_format_i18n( $suji_term->count ) )
					);
				?></span>
			<?php endif; ?>
		</h1>
		<p class="board-archive-desc"><?php esc_html_e( '상임위원회의 소식과 회의 내용을 안내합니다.', 'suji' ); ?></p>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="board-list board-list-committee">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'board' );
			endwhile;
			?>
		</div>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>

	<p class="board-all-link"><a href="<?php echo esc_url( get_post_type_archive_link( 'board_post' ) ); ?>">&larr; <?php esc_html_e( '전체 게시판 보기', 'suji' ); ?></a></p>
</main>

<?php
get_footer();
