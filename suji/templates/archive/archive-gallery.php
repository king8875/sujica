<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$suji_term = get_queried_object();
?>

<main id="primary" class="site-main board-archive board-archive-gallery">
	<header class="board-archive-header">
		<span class="board-archive-icon" aria-hidden="true">
			<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
				<rect x="3" y="3" width="18" height="18" rx="2"></rect>
				<circle cx="8.5" cy="8.5" r="1.5"></circle>
				<path d="M21 15l-5-5L5 21"></path>
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
		<p class="board-archive-desc"><?php esc_html_e( '수지성당의 다양한 순간을 사진으로 만나보세요.', 'suji' ); ?></p>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="gallery-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				$suji_thumb = suji_first_image_from_content( get_the_content() );
				?>
				<a href="<?php the_permalink(); ?>" class="gallery-grid-item">
					<div class="gallery-grid-thumb"<?php echo $suji_thumb ? ' style="background-image:url(\'' . esc_url( $suji_thumb ) . '\')"' : ''; ?>>
						<?php if ( ! $suji_thumb ) : ?>
							<span class="gallery-grid-placeholder" aria-hidden="true">
								<svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
									<rect x="3" y="3" width="18" height="18" rx="2"></rect>
									<circle cx="8.5" cy="8.5" r="1.5"></circle>
									<path d="M21 15l-5-5L5 21"></path>
								</svg>
							</span>
						<?php endif; ?>
					</div>
					<div class="gallery-grid-caption">
						<span class="gallery-grid-title"><?php the_title(); ?></span>
						<span class="gallery-grid-date"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
					</div>
				</a>
			<?php endwhile; ?>
		</div>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>

	<p class="board-all-link"><a href="<?php echo esc_url( get_post_type_archive_link( 'board_post' ) ); ?>">&larr; <?php esc_html_e( '전체 게시판 보기', 'suji' ); ?></a></p>
</main>

<?php
get_footer();
