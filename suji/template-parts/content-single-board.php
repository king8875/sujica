<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$suji_wr_name = suji_board_post_meta( '_g5_wr_name' );
$suji_wr_hit  = function_exists( 'suji_board_views' ) ? suji_board_views() : 0;
$suji_label   = suji_board_label();
$suji_archive = suji_board_link( get_post_type() );

// 같은 게시판 안에서의 이전 / 다음 글 (글 타입이 곧 게시판이다)
$suji_prev = get_previous_post();
$suji_next = get_next_post();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'board-single' ); ?>>

	<header class="board-single-header">
		<?php if ( $suji_archive ) : ?>
			<a class="board-single-cat" href="<?php echo esc_url( $suji_archive ); ?>">
				<?php echo esc_html( $suji_label ); ?>
			</a>
		<?php endif; ?>

		<h1 class="board-single-title"><?php the_title(); ?></h1>

		<?php if ( function_exists( 'suji_board_form_url' ) && current_user_can( 'edit_post', get_the_ID() ) ) : ?>
			<p class="board-single-edit">
				<a class="board-write-btn is-small" href="<?php echo esc_url( suji_board_form_url( get_post_type(), get_the_ID() ) ); ?>">
					<?php esc_html_e( '이 글 수정', 'suji' ); ?>
				</a>
			</p>
		<?php endif; ?>

		<div class="board-single-meta">
			<?php if ( $suji_wr_name ) : ?>
				<span class="board-single-author"><?php echo esc_html( $suji_wr_name ); ?></span>
			<?php endif; ?>

			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
				<?php echo esc_html( get_the_date( 'Y.m.d H:i' ) ); ?>
			</time>

			<span class="board-single-hit">
					<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor"
					     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M1.5 12S5 5.5 12 5.5 22.5 12 22.5 12 19 18.5 12 18.5 1.5 12 1.5 12z"></path>
						<circle cx="12" cy="12" r="3"></circle>
					</svg>
					<span class="screen-reader-text"><?php esc_html_e( '조회수', 'suji' ); ?> </span>
				<?php echo esc_html( number_format_i18n( (int) $suji_wr_hit ) ); ?>
			</span>
		</div>
	</header>

	<div class="board-single-content entry-content">
		<?php the_content(); ?>

		<?php // 게시판별 입력칸 — 사진 · 첨부 · 주보 링크 ?>
		<?php get_template_part( 'template-parts/board-extras' ); ?>
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

	<?php if ( $suji_archive ) : ?>
		<footer class="board-single-footer">
			<a class="board-back-link" href="<?php echo esc_url( $suji_archive ); ?>">
				<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
				     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="3" y1="6" x2="17" y2="6"></line>
					<line x1="3" y1="12" x2="17" y2="12"></line>
					<line x1="3" y1="18" x2="13" y2="18"></line>
				</svg>
				<?php echo esc_html( $suji_label ); ?> <?php esc_html_e( '목록', 'suji' ); ?>
			</a>
		</footer>
	<?php endif; ?>
</article>
