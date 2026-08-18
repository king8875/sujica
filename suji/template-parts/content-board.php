<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$suji_wr_name = suji_board_post_meta( '_g5_wr_name' );
$suji_wr_hit  = suji_board_post_meta( '_g5_wr_hit' );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'board-list-item' ); ?>>
	<a class="board-item-link" href="<?php the_permalink(); ?>">
		<h2 class="board-item-title"><?php the_title(); ?></h2>

		<div class="board-item-meta">
			<?php if ( $suji_wr_name ) : ?>
				<span class="board-item-author"><?php echo esc_html( $suji_wr_name ); ?></span>
			<?php endif; ?>

			<time class="board-item-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
				<?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?>
			</time>

			<?php if ( $suji_wr_hit ) : ?>
				<span class="board-item-hit">
					<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor"
					     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M1.5 12S5 5.5 12 5.5 22.5 12 22.5 12 19 18.5 12 18.5 1.5 12 1.5 12z"></path>
						<circle cx="12" cy="12" r="3"></circle>
					</svg>
					<span class="screen-reader-text"><?php esc_html_e( '조회수', 'suji' ); ?> </span>
					<?php echo esc_html( number_format_i18n( (int) $suji_wr_hit ) ); ?>
				</span>
			<?php endif; ?>
		</div>
	</a>
</article>
