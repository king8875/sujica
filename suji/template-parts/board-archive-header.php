<?php
/**
 * 게시판 목록 머리말 (아이콘 + 이름 + 전체 건수 + 설명).
 *
 * $args['name'] / ['desc'] / ['slug'] / ['count'] 를 받는다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$suji_name  = $args['name'] ?? '';
$suji_desc  = $args['desc'] ?? '';
$suji_slug  = $args['slug'] ?? 'notice';
$suji_count = (int) ( $args['count'] ?? 0 );
?>
<header class="board-archive-header">
	<span class="board-archive-icon" aria-hidden="true">
		<svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor"
		     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
			<?php echo suji_board_icon( $suji_slug ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</svg>
	</span>
	<h1 class="page-title">
		<?php echo esc_html( $suji_name ); ?>
		<?php if ( $suji_count ) : ?>
			<span class="board-archive-count"><?php
				printf(
					/* translators: %s: 글 수 */
					esc_html__( '전체 %s건', 'suji' ),
					esc_html( number_format_i18n( $suji_count ) )
				);
			?></span>
		<?php endif; ?>
	</h1>
	<?php if ( $suji_desc ) : ?>
		<p class="board-archive-desc"><?php echo esc_html( $suji_desc ); ?></p>
	<?php endif; ?>
</header>
