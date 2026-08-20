<?php
/**
 * 오른쪽 아래 바로가기.
 *
 * 원본 사이트도 모든 화면에 같은 묶음을 띄웠다. 스크롤을 따라 붙어 있어야
 * 뜻이 있으므로 홈에만 두지 않고 전체에 둔다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$suji_quick = array(
	array(
		'key'   => 'youtube',
		'label' => __( '유튜브', 'suji' ),
		'url'   => 'https://www.youtube.com/@suji-catholic',
		'icon'  => '<path d="M21.6 7.2a2.8 2.8 0 0 0-2-2C17.9 4.8 12 4.8 12 4.8s-5.9 0-7.6.4a2.8 2.8 0 0 0-2 2A29 29 0 0 0 2 12a29 29 0 0 0 .4 4.8 2.8 2.8 0 0 0 2 2c1.7.4 7.6.4 7.6.4s5.9 0 7.6-.4a2.8 2.8 0 0 0 2-2A29 29 0 0 0 22 12a29 29 0 0 0-.4-4.8z" fill="currentColor" stroke="none"/><path d="M10.2 15.1V8.9l5.3 3.1z" fill="#fff" stroke="none"/>',
	),
	array(
		'key'   => 'kakao',
		'label' => __( '카카오톡', 'suji' ),
		'url'   => 'https://pf.kakao.com/_xfUEnxl',
		'icon'  => '<path d="M12 3.6c-4.8 0-8.7 3-8.7 6.7 0 2.4 1.6 4.5 4 5.7l-.9 3.4c-.1.3.2.5.5.4l4-2.6c.4 0 .7.1 1.1.1 4.8 0 8.7-3 8.7-6.7S16.8 3.6 12 3.6z" fill="currentColor" stroke="none"/>',
	),
	array(
		'key'   => 'missa',
		'label' => __( '오늘의 미사', 'suji' ),
		'url'   => 'https://missa.cbck.or.kr/DailyMissa',
		'icon'  => '<path d="M4 5.5A1.5 1.5 0 0 1 5.5 4H11v16H5.5A1.5 1.5 0 0 1 4 18.5z"/><path d="M20 5.5A1.5 1.5 0 0 0 18.5 4H13v16h5.5A1.5 1.5 0 0 0 20 18.5z"/><line x1="12" y1="4" x2="12" y2="20"/>',
	),
);
?>
<nav class="suji-quick" aria-label="<?php esc_attr_e( '바로가기', 'suji' ); ?>">
	<?php foreach ( $suji_quick as $suji_item ) : ?>
		<a class="suji-quick-link is-<?php echo esc_attr( $suji_item['key'] ); ?>"
		   href="<?php echo esc_url( $suji_item['url'] ); ?>"
		   target="_blank" rel="noopener noreferrer">
			<span class="suji-quick-icon" aria-hidden="true">
				<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor"
				     stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
					<?php echo $suji_item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</svg>
			</span>
			<span class="suji-quick-label"><?php echo esc_html( $suji_item['label'] ); ?></span>
		</a>
	<?php endforeach; ?>
</nav>
