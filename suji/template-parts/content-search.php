<?php
/**
 * 검색 결과 한 줄.
 *
 * 목록(content-board.php)과 달리 여러 게시판 결과가 섞이므로,
 * 어느 게시판 글인지 배지로 함께 보여준다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$suji_wr_name = suji_board_post_meta( '_g5_wr_name' );
$suji_wr_hit  = suji_board_post_meta( '_g5_wr_hit' );
$suji_terms   = get_the_terms( get_the_ID(), 'board_cat' );
$suji_board   = ( $suji_terms && ! is_wp_error( $suji_terms ) ) ? $suji_terms[0] : null;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'board-list-item' ); ?>>
	<a class="board-item-link" href="<?php the_permalink(); ?>">
		<h2 class="board-item-title"><?php the_title(); ?></h2>

		<div class="board-item-meta">
			<span class="search-item-board">
				<?php echo esc_html( $suji_board ? $suji_board->name : get_post_type_object( get_post_type() )->labels->singular_name ); ?>
			</span>

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
