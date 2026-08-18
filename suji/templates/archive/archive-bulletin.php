<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$suji_term = get_queried_object();
?>

<main id="primary" class="site-main board-archive board-archive-bulletin">
	<header class="board-archive-header">
		<span class="board-archive-icon" aria-hidden="true">
			<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
				<rect x="4" y="3" width="16" height="18" rx="1"></rect>
				<line x1="8" y1="8" x2="16" y2="8"></line>
				<line x1="8" y1="12" x2="16" y2="12"></line>
				<line x1="8" y1="16" x2="12" y2="16"></line>
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
		<p class="board-archive-desc"><?php esc_html_e( '매주 발행되는 본당 주보를 확인하세요.', 'suji' ); ?></p>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="board-list board-list-bulletin">
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
