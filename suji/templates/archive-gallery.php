<?php
/**
 * 포토앨범 목록 — 사진 격자.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

global $wp_query;
$suji_board = suji_board_of( 'suji_gallery' );
?>

<main id="primary" class="site-main board-archive board-archive-gallery">

	<?php
	get_template_part( 'template-parts/board-archive-header', null, array(
		'name'  => $suji_board['name'],
		'desc'  => $suji_board['desc'],
		'slug'  => 'gallery',
		'count' => (int) $wp_query->found_posts,
	) );
	?>

	<?php if ( have_posts() ) : ?>
		<div class="gallery-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				$suji_thumb = get_the_post_thumbnail_url( null, 'medium_large' );
				if ( ! $suji_thumb ) {
					$suji_thumb = suji_first_image_from_content( get_the_content() );
				}
				?>
				<a href="<?php the_permalink(); ?>" class="gallery-grid-item">
					<div class="gallery-grid-thumb"<?php echo $suji_thumb ? ' style="background-image:url(\'' . esc_url( $suji_thumb ) . '\')"' : ''; ?>>
						<?php if ( ! $suji_thumb ) : ?>
							<span class="gallery-grid-placeholder" aria-hidden="true">
								<svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor"
								     stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
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
</main>

<?php
get_footer();
