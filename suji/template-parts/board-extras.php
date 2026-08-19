<?php
/**
 * 게시판별 입력칸을 글 아래에 그린다 — 사진 · 첨부 · 주보 링크.
 *
 * 칸이 비어 있으면 아무것도 그리지 않는다. 이관된 글은 본문에 이미 사진과
 * 첨부 목록이 들어 있어 이 부분이 조용히 넘어간다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'get_field' ) ) {
	return;
}

$suji_id = get_the_ID();

/* -------------------------------- 링크 -------------------------------- */
$suji_links = get_field( 'board_links', $suji_id );

// 예전에 쓰던 주보 전용 칸에 값이 남아 있으면 함께 보여 준다
$suji_legacy = (string) get_field( 'bulletin_url', $suji_id );
if ( $suji_legacy ) {
	$suji_links = array_merge(
		is_array( $suji_links ) ? $suji_links : array(),
		array( array( 'url' => $suji_legacy, 'label' => '주보 보기' ) )
	);
}

if ( is_array( $suji_links ) && $suji_links ) : ?>
	<p class="board-extra-links">
		<?php foreach ( $suji_links as $suji_link ) : ?>
			<?php
			$suji_url = trim( (string) ( $suji_link['url'] ?? '' ) );
			if ( '' === $suji_url ) {
				continue;
			}
			$suji_text = trim( (string) ( $suji_link['label'] ?? '' ) );
			if ( '' === $suji_text ) {
				$suji_text = __( '바로가기', 'suji' );
			}
			?>
			<a class="board-link-btn" href="<?php echo esc_url( $suji_url ); ?>"
			   target="_blank" rel="noopener noreferrer">
				<?php echo esc_html( $suji_text ); ?>
				<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
				     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="5" y1="12" x2="19" y2="12"></line>
					<polyline points="13 6 19 12 13 18"></polyline>
				</svg>
			</a>
		<?php endforeach; ?>
	</p>
<?php endif; ?>

<?php
/* ------------------------------- 주보 이미지 ------------------------------- */
$suji_bulletin_img = get_field( 'bulletin_image', $suji_id );
if ( is_array( $suji_bulletin_img ) && ! empty( $suji_bulletin_img['url'] ) ) : ?>
	<figure class="board-extra-image">
		<img src="<?php echo esc_url( $suji_bulletin_img['sizes']['large'] ?? $suji_bulletin_img['url'] ); ?>"
		     alt="<?php echo esc_attr( $suji_bulletin_img['alt'] ?: get_the_title( $suji_id ) ); ?>">
	</figure>
<?php endif; ?>

<?php
/* -------------------------------- 사진들 -------------------------------- */
$suji_photos = get_field( 'gallery_photos', $suji_id );
if ( is_array( $suji_photos ) && $suji_photos ) : ?>
	<ul class="board-photos">
		<?php foreach ( $suji_photos as $suji_photo ) : ?>
			<?php
			if ( ! is_array( $suji_photo ) || empty( $suji_photo['url'] ) ) {
				continue;
			}
			$suji_full  = $suji_photo['url'];
			$suji_shown = $suji_photo['sizes']['large'] ?? $suji_full;
			?>
			<li>
				<a href="<?php echo esc_url( $suji_full ); ?>" target="_blank" rel="noopener noreferrer">
					<img src="<?php echo esc_url( $suji_shown ); ?>"
					     alt="<?php echo esc_attr( $suji_photo['alt'] ?: get_the_title( $suji_id ) ); ?>"
					     loading="lazy">
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php
/* -------------------------------- 첨부 파일 -------------------------------- */
$suji_files = get_field( 'board_files', $suji_id );
if ( is_array( $suji_files ) && $suji_files ) : ?>
	<ul class="board-files">
		<?php foreach ( $suji_files as $suji_row ) : ?>
			<?php
			$suji_file = $suji_row['file'] ?? null;
			if ( ! is_array( $suji_file ) || empty( $suji_file['url'] ) ) {
				continue;
			}
			$suji_name = trim( (string) ( $suji_row['label'] ?? '' ) );
			if ( '' === $suji_name ) {
				$suji_name = $suji_file['filename'] ?? basename( $suji_file['url'] );
			}
			?>
			<li>
				<a href="<?php echo esc_url( $suji_file['url'] ); ?>" download><?php echo esc_html( $suji_name ); ?></a>
				<?php if ( ! empty( $suji_file['filesize'] ) ) : ?>
					<span class="board-file-size"><?php echo esc_html( size_format( (int) $suji_file['filesize'] ) ); ?></span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
