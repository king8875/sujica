<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$suji_wr_name = suji_board_post_meta( '_g5_wr_name' );
$suji_wr_hit  = suji_board_post_meta( '_g5_wr_hit' );
$suji_terms   = get_the_terms( get_the_ID(), 'board_cat' );
$suji_board   = ( $suji_terms && ! is_wp_error( $suji_terms ) ) ? $suji_terms[0] : null;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'board-single' ); ?>>
	<header class="entry-header">
		<?php if ( $suji_board ) : ?>
			<p class="board-single-cat">
				<a href="<?php echo esc_url( get_term_link( $suji_board ) ); ?>"><?php echo esc_html( $suji_board->name ); ?></a>
			</p>
		<?php endif; ?>
		<h1 class="entry-title"><?php the_title(); ?></h1>
		<div class="board-item-meta">
			<?php if ( $suji_wr_name ) : ?>
				<span class="board-item-author"><?php echo esc_html( $suji_wr_name ); ?></span>
			<?php endif; ?>
			<span class="board-item-date"><?php echo esc_html( get_the_date( 'Y.m.d H:i' ) ); ?></span>
			<?php if ( $suji_wr_hit ) : ?>
				<span class="board-item-hit"><?php echo esc_html( $suji_wr_hit ); ?></span>
			<?php endif; ?>
		</div>
	</header>

	<div class="entry-content">
		<?php the_content(); ?>
	</div>

	<?php if ( $suji_board ) : ?>
		<footer class="board-single-footer">
			<a class="board-back-link" href="<?php echo esc_url( get_term_link( $suji_board ) ); ?>">&larr; <?php echo esc_html( $suji_board->name ); ?> <?php esc_html_e( '목록으로', 'suji' ); ?></a>
		</footer>
	<?php endif; ?>
</article>
