<?php
/**
 * 프론트엔드 글쓰기 · 수정 화면.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$suji_ctx   = suji_board_form_context();
$suji_type  = $suji_ctx['type'];
$suji_post  = $suji_ctx['post'];
$suji_board = suji_board_of( $suji_type );
$suji_edit  = 'edit' === $suji_ctx['mode'];
$suji_id    = $suji_post ? (int) $suji_post->ID : 0;

global $suji_form_errors;

// 기존 값
$suji_title   = $suji_post ? $suji_post->post_title : '';
$suji_content = $suji_post ? $suji_post->post_content : '';
$suji_pinned  = $suji_id ? suji_board_is_pinned( $suji_id ) : false;

$suji_links  = ( $suji_id && function_exists( 'get_field' ) ) ? (array) get_field( 'board_links', $suji_id ) : array();
$suji_files  = ( $suji_id && function_exists( 'get_field' ) ) ? (array) get_field( 'board_files', $suji_id ) : array();
$suji_photos = ( $suji_id && function_exists( 'get_field' ) ) ? (array) get_field( 'gallery_photos', $suji_id ) : array();
?>

<main id="primary" class="site-main board-form-page">

	<?php
	get_template_part( 'template-parts/board-archive-header', null, array(
		'name' => ( $suji_board['name'] ?? '' ) . ' ' . ( $suji_edit ? __( '글 수정', 'suji' ) : __( '글쓰기', 'suji' ) ),
		'desc' => $suji_edit ? '' : __( '필요한 칸만 채우고 저장하세요.', 'suji' ),
		'slug' => $suji_board['slug'] ?? 'notice',
	) );
	?>

	<?php if ( $suji_form_errors instanceof WP_Error && $suji_form_errors->has_errors() ) : ?>
		<div class="board-form-errors">
			<?php foreach ( $suji_form_errors->get_error_messages() as $suji_msg ) : ?>
				<p><?php echo esc_html( $suji_msg ); ?></p>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<form class="board-form" method="post" enctype="multipart/form-data"
	      action="<?php echo esc_url( suji_board_form_url( $suji_type, $suji_id ) ); ?>">

		<?php wp_nonce_field( 'suji_board_form', 'suji_board_nonce' ); ?>
		<input type="hidden" name="suji_type" value="<?php echo esc_attr( $suji_type ); ?>">
		<input type="hidden" name="suji_post_id" value="<?php echo esc_attr( $suji_id ); ?>">

		<div class="board-form-field">
			<label for="suji_title"><?php esc_html_e( '제목', 'suji' ); ?></label>
			<input type="text" id="suji_title" name="suji_title" required
			       value="<?php echo esc_attr( $suji_title ); ?>">
		</div>

		<div class="board-form-field">
			<label for="suji_content"><?php esc_html_e( '내용', 'suji' ); ?></label>
			<textarea id="suji_content" name="suji_content" rows="12"><?php echo esc_textarea( $suji_content ); ?></textarea>
			<p class="board-form-hint"><?php esc_html_e( '줄바꿈은 그대로 반영됩니다. 사진과 파일은 아래 칸을 쓰세요.', 'suji' ); ?></p>
		</div>

		<?php if ( 'suji_gallery' === $suji_type ) : ?>
			<div class="board-form-field">
				<label for="suji_photos"><?php esc_html_e( '사진', 'suji' ); ?></label>

				<?php if ( $suji_photos ) : ?>
					<ul class="board-form-thumbs">
						<?php foreach ( $suji_photos as $suji_photo ) : ?>
							<?php
							$suji_att_id = is_array( $suji_photo ) ? (int) ( $suji_photo['ID'] ?? 0 ) : (int) $suji_photo;
							if ( ! $suji_att_id ) {
								continue;
							}
							$suji_src = wp_get_attachment_image_url( $suji_att_id, 'thumbnail' );
							?>
							<li>
								<img src="<?php echo esc_url( $suji_src ); ?>" alt="">
								<label class="board-form-keep">
									<input type="checkbox" name="suji_keep_photo[]"
									       value="<?php echo esc_attr( $suji_att_id ); ?>" checked>
									<?php esc_html_e( '유지', 'suji' ); ?>
								</label>
							</li>
						<?php endforeach; ?>
					</ul>
					<p class="board-form-hint"><?php esc_html_e( '지울 사진은 ‘유지’ 체크를 끄세요.', 'suji' ); ?></p>
				<?php endif; ?>

				<input type="file" id="suji_photos" name="suji_photos[]" accept="image/*" multiple>
				<p class="board-form-hint"><?php esc_html_e( '여러 장을 한 번에 고를 수 있습니다. 첫 장이 목록 대표 사진이 됩니다.', 'suji' ); ?></p>
			</div>
		<?php endif; ?>

		<div class="board-form-field">
			<label><?php esc_html_e( '링크', 'suji' ); ?></label>

			<div id="suji-link-rows">
				<?php foreach ( $suji_links as $suji_link ) : ?>
					<div class="board-form-row">
						<input type="url" name="suji_link_url[]" placeholder="https://"
						       value="<?php echo esc_attr( $suji_link['url'] ?? '' ); ?>">
						<input type="text" name="suji_link_label[]" placeholder="<?php esc_attr_e( '버튼에 쓸 글자', 'suji' ); ?>"
						       value="<?php echo esc_attr( $suji_link['label'] ?? '' ); ?>">
						<button type="button" class="board-form-remove" aria-label="<?php esc_attr_e( '줄 삭제', 'suji' ); ?>">&times;</button>
					</div>
				<?php endforeach; ?>
			</div>

			<button type="button" class="board-form-add" id="suji-add-link"><?php esc_html_e( '＋ 링크 추가', 'suji' ); ?></button>
		</div>

		<div class="board-form-field">
			<label for="suji_files"><?php esc_html_e( '첨부파일', 'suji' ); ?></label>

			<?php if ( $suji_files ) : ?>
				<ul class="board-form-files">
					<?php foreach ( $suji_files as $suji_row ) : ?>
						<?php
						$suji_file = $suji_row['file'] ?? null;
						$suji_att_id = is_array( $suji_file ) ? (int) ( $suji_file['ID'] ?? 0 ) : (int) $suji_file;
						if ( ! $suji_att_id ) {
							continue;
						}
						?>
						<li>
							<label class="board-form-keep">
								<input type="checkbox" name="suji_keep_file[]"
								       value="<?php echo esc_attr( $suji_att_id ); ?>" checked>
								<?php echo esc_html( get_the_title( $suji_att_id ) ); ?>
							</label>
						</li>
					<?php endforeach; ?>
				</ul>
				<p class="board-form-hint"><?php esc_html_e( '지울 파일은 체크를 끄세요.', 'suji' ); ?></p>
			<?php endif; ?>

			<input type="file" id="suji_files" name="suji_files[]" multiple>
		</div>

		<div class="board-form-field">
			<label class="board-form-check">
				<input type="checkbox" name="suji_pinned" value="1" <?php checked( $suji_pinned ); ?>>
				<?php esc_html_e( '목록 맨 위에 고정', 'suji' ); ?>
			</label>
		</div>

		<div class="board-form-actions">
			<button type="submit" class="board-form-submit">
				<?php echo esc_html( $suji_edit ? __( '수정 저장', 'suji' ) : __( '등록', 'suji' ) ); ?>
			</button>

			<a class="board-form-cancel" href="<?php echo esc_url( suji_board_link( $suji_type ) ); ?>">
				<?php esc_html_e( '취소', 'suji' ); ?>
			</a>

			<?php if ( $suji_edit && current_user_can( 'delete_post', $suji_id ) ) : ?>
				<button type="submit" name="suji_delete" value="1" class="board-form-delete"
				        onclick="return confirm('<?php echo esc_js( __( '이 글을 삭제할까요? 휴지통으로 옮겨집니다.', 'suji' ) ); ?>');">
					<?php esc_html_e( '삭제', 'suji' ); ?>
				</button>
			<?php endif; ?>
		</div>
	</form>
</main>

<script>
(function () {
	var rows = document.getElementById('suji-link-rows');
	var add = document.getElementById('suji-add-link');
	if (!rows || !add) { return; }

	add.addEventListener('click', function () {
		var row = document.createElement('div');
		row.className = 'board-form-row';
		row.innerHTML =
			'<input type="url" name="suji_link_url[]" placeholder="https://">' +
			'<input type="text" name="suji_link_label[]" placeholder="<?php echo esc_js( __( '버튼에 쓸 글자', 'suji' ) ); ?>">' +
			'<button type="button" class="board-form-remove" aria-label="<?php echo esc_js( __( '줄 삭제', 'suji' ) ); ?>">&times;</button>';
		rows.appendChild(row);
	});

	rows.addEventListener('click', function (e) {
		if (e.target.classList.contains('board-form-remove')) {
			e.target.closest('.board-form-row').remove();
		}
	});
})();
</script>

<?php
get_footer();
