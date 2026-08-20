<?php
/**
 * 게시판 목록 (공지사항 · 본당 주보 · 사제 게시판 · 문서 자료실 · 단체 게시판).
 * 포토앨범은 사진 격자라 templates/archive-gallery.php 를 따로 쓴다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

global $wp_query;
$suji_term = is_tax( 'board_cat' ) ? get_queried_object() : null;

// 위원회별 목록은 단체 게시판에 속한다
$suji_type = $suji_term ? 'suji_group' : get_query_var( 'post_type' );
if ( is_array( $suji_type ) ) {
	$suji_type = reset( $suji_type );
}
$suji_board = suji_board_of( $suji_type );
if ( ! is_array( $suji_board ) ) {
	$suji_obj   = get_post_type_object( $suji_type );
	$suji_board = array(
		'name' => $suji_obj ? $suji_obj->labels->name : '',
		'desc' => '',
		'slug' => 'notice',
	);
}
?>

<main id="primary" class="site-main board-archive board-archive-<?php echo esc_attr( $suji_board['slug'] ?? 'generic' ); ?>">

	<?php
	get_template_part( 'template-parts/board-archive-header', null, array(
		'name'  => $suji_term ? $suji_term->name : ( $suji_board['name'] ?? '' ),
		'desc'  => $suji_term ? $suji_term->description : ( $suji_board['desc'] ?? '' ),
		'slug'  => $suji_board['slug'] ?? 'notice',
		'count' => $suji_term ? $suji_term->count : (int) $wp_query->found_posts,
	) );
	?>

	<?php if ( function_exists( 'suji_can_write_board' ) && suji_can_write_board( $suji_type ) ) : ?>
		<p class="board-write-bar">
			<a class="board-write-btn" href="<?php echo esc_url( suji_board_form_url( $suji_type ) ); ?>">
				<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
				     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="12" y1="5" x2="12" y2="19"></line>
					<line x1="5" y1="12" x2="19" y2="12"></line>
				</svg>
				<?php esc_html_e( '글쓰기', 'suji' ); ?>
			</a>
		</p>
	<?php endif; ?>

	<?php if ( ! empty( $suji_board['taxonomy'] ) ) : ?>
		<?php
		// 단체 게시판은 위원회를 걸러 볼 수 있게 한다
		// 이 게시판에 속한 위원회만 (board_cat 에는 이관 전 텀도 남아 있다)
		$suji_terms = get_terms( array(
			'taxonomy'   => 'board_cat',
			'slug'       => $suji_board['from'],
			'hide_empty' => false,
			'orderby'    => 'name',
		) );
		?>
		<?php if ( ! is_wp_error( $suji_terms ) && $suji_terms ) : ?>
			<nav class="board-filter" aria-label="<?php esc_attr_e( '위원회 고르기', 'suji' ); ?>">
				<a class="board-filter-link<?php echo $suji_term ? '' : ' is-current'; ?>"
				   href="<?php echo esc_url( suji_board_link( $suji_type ) ); ?>"><?php esc_html_e( '전체', 'suji' ); ?></a>
				<?php foreach ( $suji_terms as $suji_t ) : ?>
					<a class="board-filter-link<?php echo ( $suji_term && $suji_term->term_id === $suji_t->term_id ) ? ' is-current' : ''; ?>"
					   href="<?php echo esc_url( get_term_link( $suji_t ) ); ?>">
						<?php echo esc_html( str_replace( ' 게시판', '', $suji_t->name ) ); ?>
						<span class="board-filter-count"><?php echo (int) $suji_t->count; ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<div class="board-list">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'board' );
			endwhile;
			?>
		</div>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</main>

<?php
get_footer();
