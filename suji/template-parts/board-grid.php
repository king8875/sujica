<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$suji_terms = get_terms( array(
	'taxonomy'   => 'board_cat',
	'hide_empty' => false,
	'orderby'    => 'name',
) );

if ( empty( $suji_terms ) || is_wp_error( $suji_terms ) ) {
	return;
}
?>
<ul class="board-grid">
	<?php foreach ( $suji_terms as $suji_term ) : ?>
		<li class="board-grid-item">
			<a href="<?php echo esc_url( get_term_link( $suji_term ) ); ?>">
				<span class="board-grid-title"><?php echo esc_html( $suji_term->name ); ?></span>
				<span class="board-grid-count"><?php echo esc_html( number_format_i18n( $suji_term->count ) ); ?><?php esc_html_e( '개의 글', 'suji' ); ?></span>
			</a>
		</li>
	<?php endforeach; ?>
</ul>
