<?php
/**
 * 전용 템플릿이 없는 게시판(단체 게시판 등)의 목록 화면.
 * 머리말·목록·페이지 넘기기를 전용 템플릿들과 같은 형식으로 둔다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$suji_term = get_queried_object();
?>

<main id="primary" class="site-main board-archive board-archive-generic">
	<header class="board-archive-header">
		<span class="board-archive-icon" aria-hidden="true">
			<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor"
			     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
				<rect x="4" y="4" width="16" height="16" rx="2"></rect>
				<line x1="8" y1="9" x2="16" y2="9"></line>
				<line x1="8" y1="13" x2="16" y2="13"></line>
				<line x1="8" y1="17" x2="13" y2="17"></line>
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
		<?php if ( $suji_term->description ) : ?>
			<p class="board-archive-desc"><?php echo esc_html( $suji_term->description ); ?></p>
		<?php endif; ?>
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
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>

	<p class="board-all-link"><a href="<?php echo esc_url( get_post_type_archive_link( 'board_post' ) ); ?>">&larr; <?php esc_html_e( '전체 게시판 보기', 'suji' ); ?></a></p>
</main>

<?php
get_footer();
