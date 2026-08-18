<?php
/**
 * 게시판 공용 기능. 글 타입 등록은 inc/boards.php 에 있다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 그누보드에서 옮겨온 메타 (작성자 이름 · 조회수).
 */
function suji_board_post_meta( $key, $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	return get_post_meta( $post_id, $key, true );
}

function suji_first_image_from_content( $content ) {
	if ( preg_match( '/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $suji_matches ) ) {
		return $suji_matches[1];
	}
	return '';
}

/**
 * 한 게시판의 최근 글. 홈 화면에서 쓴다.
 */
function suji_board_recent( $post_type, $count = 5 ) {
	return get_posts( array(
		'post_type'           => $post_type,
		'posts_per_page'      => $count,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	) );
}

/**
 * 게시판 목록 주소.
 */
function suji_board_link( $post_type ) {
	$suji_link = get_post_type_archive_link( $post_type );
	return $suji_link ? $suji_link : '';
}

/**
 * 글이 속한 게시판 이름. 검색 결과처럼 여러 게시판이 섞이는 곳에서 쓴다.
 * 단체 게시판은 위원회 이름까지 보여준다.
 */
function suji_board_label( $post_id = 0 ) {
	$post_id     = $post_id ? $post_id : get_the_ID();
	$suji_type   = get_post_type( $post_id );
	$suji_board  = suji_board_of( $suji_type );

	if ( ! $suji_board ) {
		$suji_obj = get_post_type_object( $suji_type );
		return $suji_obj ? $suji_obj->labels->singular_name : '';
	}

	if ( ! empty( $suji_board['taxonomy'] ) ) {
		$suji_terms = get_the_terms( $post_id, 'board_cat' );
		if ( $suji_terms && ! is_wp_error( $suji_terms ) ) {
			return $suji_terms[0]->name;
		}
	}

	return $suji_board['name'];
}

/**
 * 단체 게시판 아래에 위원회별 목록을 붙인다.
 * 나머지 게시판은 글 타입마다 최상위 메뉴가 생기므로 따로 손댈 것이 없다.
 */
function suji_group_admin_submenus() {
	global $submenu;

	$suji_menu = 'edit.php?post_type=suji_group';

	if ( ! current_user_can( 'edit_posts' ) || ! isset( $submenu[ $suji_menu ] ) ) {
		return;
	}

	foreach ( suji_boards()['suji_group']['from'] as $suji_slug ) {
		$suji_term = get_term_by( 'slug', $suji_slug, 'board_cat' );
		if ( ! $suji_term ) {
			continue;
		}

		$submenu[ $suji_menu ][] = array(
			'<span class="suji-board-child">↳</span> ' . esc_html( $suji_term->name )
				. ' <span class="suji-board-count">' . (int) $suji_term->count . '</span>',
			'edit_posts',
			$suji_menu . '&board_cat=' . $suji_term->slug,
		);
	}
}
add_action( 'admin_menu', 'suji_group_admin_submenus', 999 );

/**
 * 위원회로 걸러 본 목록에서는 '새로 추가' 버튼도 그 위원회를 달고 열리게 한다.
 */
function suji_group_add_new_link() {
	$suji_screen = get_current_screen();
	if ( ! $suji_screen || 'edit-suji_group' !== $suji_screen->id ) {
		return;
	}

	$suji_slug = isset( $_GET['board_cat'] ) ? sanitize_title( wp_unslash( $_GET['board_cat'] ) ) : '';
	if ( ! $suji_slug ) {
		return;
	}

	$suji_url = add_query_arg(
		array( 'post_type' => 'suji_group', 'board_cat' => $suji_slug ),
		admin_url( 'post-new.php' )
	);
	?>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			var btn = document.querySelector('.wrap .page-title-action');
			if (btn) { btn.setAttribute('href', <?php echo wp_json_encode( $suji_url ); ?>); }
		});
	</script>
	<?php
}
add_action( 'admin_footer', 'suji_group_add_new_link' );

/**
 * 새 글을 쓸 때 주소에 실린 위원회를 미리 골라 둔다.
 */
function suji_preselect_board_cat_checklist( $args, $post_id ) {
	if ( ( $args['taxonomy'] ?? '' ) !== 'board_cat' ) {
		return $args;
	}

	if ( $post_id && wp_get_post_terms( $post_id, 'board_cat' ) ) {
		return $args;
	}

	$suji_slug = isset( $_GET['board_cat'] ) ? sanitize_title( wp_unslash( $_GET['board_cat'] ) ) : '';
	if ( ! $suji_slug ) {
		return $args;
	}

	$suji_term = get_term_by( 'slug', $suji_slug, 'board_cat' );
	if ( $suji_term ) {
		$args['selected_cats'] = array( $suji_term->term_id );
	}

	return $args;
}
add_filter( 'wp_terms_checklist_args', 'suji_preselect_board_cat_checklist', 10, 2 );

/**
 * 외모 > 메뉴 화면에서 게시판 상자들을 기본으로 펼쳐 둔다.
 *
 * 워드프레스는 그 화면의 부가 상자를 기본으로 접어두기 때문에, '화면 옵션'을
 * 직접 열지 않으면 게시판 글이나 목록(아카이브)을 메뉴에 넣을 수 없었다.
 */
function suji_show_board_nav_menu_boxes( $hidden, $screen ) {
	if ( empty( $screen->id ) || 'nav-menus' !== $screen->id ) {
		return $hidden;
	}

	$suji_show = array( 'add-board_cat' );
	foreach ( suji_board_post_types() as $suji_type ) {
		$suji_show[] = 'add-post-type-' . $suji_type;
	}

	return array_values( array_diff( (array) $hidden, $suji_show ) );
}
add_filter( 'hidden_meta_boxes', 'suji_show_board_nav_menu_boxes', 10, 2 );

function suji_board_admin_menu_style() {
	echo '<style>#adminmenu .suji-board-child{opacity:.45;margin-right:2px}'
		. '#adminmenu .suji-board-count{opacity:.45;font-size:11px;margin-left:4px}</style>';
}
add_action( 'admin_head', 'suji_board_admin_menu_style' );

/**
 * 검색은 게시판 글 전체를 대상으로 한다.
 * 예전 board_post 는 exclude_from_search 로 빠져 있다.
 */
function suji_search_all_boards( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}

	if ( ! $query->get( 'post_type' ) ) {
		$query->set( 'post_type', array_merge( suji_board_post_types(), array( 'page' ) ) );
	}
}
add_action( 'pre_get_posts', 'suji_search_all_boards' );
