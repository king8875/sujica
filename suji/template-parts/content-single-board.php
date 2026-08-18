<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$suji_wr_name = suji_board_post_meta( '_g5_wr_name' );
$suji_wr_hit  = suji_board_post_meta( '_g5_wr_hit' );
$suji_terms   = get_the_terms( get_the_ID(), 'board_cat' );
$suji_board   = ( $suji_terms && ! is_wp_error( $suji_terms ) ) ? $suji_terms[0] : null;

// 같은 게시판 안에서의 이전 / 다음 글
$suji_prev = $suji_board ? get_previous_post( true, '', 'board_cat' ) : null;
$suji_next = $suji_board ? get_next_post( true, '', 'board_cat' ) : null;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'board-single' ); ?>>

	<header class="board-single-header">
		<?php if ( $suji_board ) : ?>
			<a class="board-single-cat" href="<?php echo esc_url( get_term_link( $suji_board ) ); ?>">
				<?php echo esc_html( $suji_board->name ); ?>
			</a>
		<?php endif; ?>

		<h1 class="board-single-title"><?php the_title(); ?></h1>

		<div class="board-single-meta">
			<?php if ( $suji_wr_name ) : ?>
				<span class="board-single-author"><?php echo esc_html( $suji_wr_name ); ?></span>
			<?php endif; ?>

			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
				<?php echo esc_html( get_the_date( 'Y.m.d H:i' ) ); ?>
			</time>

			<?php if ( $suji_wr_hit ) : ?>
				<span class="board-single-hit">
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
	</header>

	<div class="board-single-content entry-content">
		<?php the_content(); ?>
	</div>

	<?php if ( $suji_prev || $suji_next ) : ?>
		<nav class="board-single-nav" aria-label="<?php esc_attr_e( '이전 다음 글', 'suji' ); ?>">
			<?php if ( $suji_prev ) : ?>
				<a class="board-single-nav-item is-prev" href="<?php echo esc_url( get_permalink( $suji_prev ) ); ?>">
					<span class="board-single-nav-label">
						<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor"
						     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<polyline points="15 18 9 12 15 6"></polyline>
						</svg>
						<?php esc_html_e( '이전 글', 'suji' ); ?>
					</span>
					<span class="board-single-nav-title"><?php echo esc_html( get_the_title( $suji_prev ) ); ?></span>
				</a>
			<?php endif; ?>

			<?php if ( $suji_next ) : ?>
				<a class="board-single-nav-item is-next" href="<?php echo esc_url( get_permalink( $suji_next ) ); ?>">
					<span class="board-single-nav-label">
						<?php esc_html_e( '다음 글', 'suji' ); ?>
						<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor"
						     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<polyline points="9 6 15 12 9 18"></polyline>
						</svg>
					</span>
					<span class="board-single-nav-title"><?php echo esc_html( get_the_title( $suji_next ) ); ?></span>
				</a>
			<?php endif; ?>
		</nav>
	<?php endif; ?>

	<?php if ( $suji_board ) : ?>
		<footer class="board-single-footer">
			<a class="board-back-link" href="<?php echo esc_url( get_term_link( $suji_board ) ); ?>">
				<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
				     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="3" y1="6" x2="17" y2="6"></line>
					<line x1="3" y1="12" x2="17" y2="12"></line>
					<line x1="3" y1="18" x2="13" y2="18"></line>
				</svg>
				<?php echo esc_html( $suji_board->name ); ?> <?php esc_html_e( '목록', 'suji' ); ?>
			</a>
		</footer>
	<?php endif; ?>
</article>
