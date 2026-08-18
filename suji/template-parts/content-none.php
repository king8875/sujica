<?php
/**
 * 글이 없을 때.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="board-empty">
	<span class="board-empty-icon" aria-hidden="true">
		<svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor"
		     stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
			<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"></path>
			<path d="M14 3v5h5"></path>
		</svg>
	</span>
	<p class="board-empty-title"><?php esc_html_e( '등록된 글이 없습니다', 'suji' ); ?></p>
</div>
