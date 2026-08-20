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

	<?php if ( function_exists( 'suji_can_write_board' ) && suji_can_write_board( 'suji_gallery' ) ) : ?>
		<p class="board-write-bar">
			<a class="board-write-btn" href="<?php echo esc_url( suji_board_form_url( 'suji_gallery' ) ); ?>">
				<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
				     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="12" y1="5" x2="12" y2="19"></line>
					<line x1="5" y1="12" x2="19" y2="12"></line>
				</svg>
				<?php esc_html_e( '글쓰기', 'suji' ); ?>
			</a>
		</p>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<div class="gallery-grid">
			<?php
			while ( have_posts() ) :
				the_post();
				$suji_thumb = get_the_post_thumbnail_url( null, 'medium_large' );

				// 대표 이미지가 없으면 사진 칸의 첫 장, 그것도 없으면 본문 첫 사진
				if ( ! $suji_thumb && function_exists( 'get_field' ) ) {
					$suji_photos = get_field( 'gallery_photos' );
					if ( is_array( $suji_photos ) && $suji_photos ) {
						$suji_one   = reset( $suji_photos );
						$suji_thumb = is_array( $suji_one )
							? ( $suji_one['sizes']['medium_large'] ?? $suji_one['url'] ?? '' )
							: '';
					}
				}

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
						<span class="gallery-grid-meta">
							<span class="gallery-grid-date"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
							<span class="gallery-grid-hit">
								<svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor"
								     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
									<path d="M1.5 12S5 5.5 12 5.5 22.5 12 22.5 12 19 18.5 12 18.5 1.5 12 1.5 12z"></path>
									<circle cx="12" cy="12" r="3"></circle>
								</svg>
								<span class="screen-reader-text"><?php esc_html_e( '조회수', 'suji' ); ?> </span>
								<?php echo esc_html( number_format_i18n( suji_board_views() ) ); ?>
							</span>
						</span>
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
