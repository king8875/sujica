<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$suji_wr_name = suji_board_post_meta( '_g5_wr_name' );
$suji_wr_hit  = suji_board_post_meta( '_g5_wr_hit' );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'board-list-item' ); ?>>
	<h2 class="board-item-title">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h2>
	<div class="board-item-meta">
		<?php if ( $suji_wr_name ) : ?>
			<span class="board-item-author"><?php echo esc_html( $suji_wr_name ); ?></span>
		<?php endif; ?>
		<span class="board-item-date"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></span>
		<?php if ( $suji_wr_hit ) : ?>
			<span class="board-item-hit"><?php echo esc_html( $suji_wr_hit ); ?></span>
		<?php endif; ?>
	</div>
</article>
