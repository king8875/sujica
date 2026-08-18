<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function suji_register_board_post_type() {
	register_post_type( 'board_post', array(
		'labels' => array(
			'name'          => __( '게시판 글', 'suji' ),
			'singular_name' => __( '게시판 글', 'suji' ),
			'add_new_item'  => __( '게시판 글 추가', 'suji' ),
			'edit_item'     => __( '게시판 글 수정', 'suji' ),
			'search_items'  => __( '게시판 글 검색', 'suji' ),
			'not_found'     => __( '게시글이 없습니다', 'suji' ),
		),
		'public'       => true,
		'has_archive'  => 'board-list',
		'rewrite'      => array( 'slug' => 'entry', 'with_front' => false ),
		'supports'     => array( 'title', 'editor' ),
		'menu_icon'    => 'dashicons-forms',
		'show_in_menu' => true,
		'show_in_rest' => true,
	) );

	register_taxonomy( 'board_cat', 'board_post', array(
		'labels' => array(
			'name'          => __( '게시판', 'suji' ),
			'singular_name' => __( '게시판', 'suji' ),
		),
		'public'            => true,
		'hierarchical'      => true,
		'rewrite'           => array( 'slug' => 'board', 'with_front' => false ),
		'show_admin_column' => true,
		'show_in_rest'      => true,
	) );
}
add_action( 'init', 'suji_register_board_post_type' );

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
 * 한 게시판(board_cat 슬러그)의 최근 글을 가져온다.
 * 홈 화면의 소식 섹션에서 쓴다.
 */
function suji_board_recent( $suji_slug, $suji_count = 5 ) {
	return get_posts( array(
		'post_type'        => 'board_post',
		'posts_per_page'   => $suji_count,
		'ignore_sticky_posts' => true,
		'no_found_rows'    => true,
		'tax_query'        => array(
			array(
				'taxonomy' => 'board_cat',
				'field'    => 'slug',
				'terms'    => $suji_slug,
			),
		),
	) );
}

/**
 * board_cat 슬러그의 아카이브 주소. 텀이 없으면 빈 문자열.
 */
function suji_board_link( $suji_slug ) {
	$suji_link = get_term_link( $suji_slug, 'board_cat' );
	return is_wp_error( $suji_link ) ? '' : $suji_link;
}

/**
 * 게시판 구성. 관리자 메뉴 순서와 1회성 정리(inc/admin/board-setup.php)가
 * 이 정의 하나를 함께 본다.
 */
function suji_board_structure() {
	return array(
		// 단독 게시판 — 표시 순서대로
		'main'    => array( 'notice', 'bible', 'gallery', 'story', 'docu' ),

		// 단체 게시판 — 상위 텀 하나 아래로 묶는다
		'group'   => array(
			'slug'     => 'groups',
			'name'     => '단체 게시판',
			'children' => array( 'sangim', 'small', 'mainorg', 'jebun', 'standard', 'teen', 'jejung' ),
		),

		// 더 쓰지 않는 게시판
		'retired' => array( 'sense', 'cbck', 'free', 'pray' ),
	);
}

/**
 * "게시판 글" 아래에 게시판별 목록을 순서대로 붙인다.
 *
 * 예전에는 텀마다 목록 + 글쓰기 두 줄을 만들어 16개 게시판이 32줄로 늘어났다.
 * 지금은 게시판당 한 줄만 두고, 글쓰기는 목록 화면의 "새로 추가" 버튼이
 * 해당 게시판을 미리 고르도록 해서 대신한다(suji_board_add_new_link).
 */
function suji_board_admin_submenus() {
	global $submenu;

	if ( ! current_user_can( 'edit_posts' ) || ! isset( $submenu['edit.php?post_type=board_post'] ) ) {
		return;
	}

	$suji_structure = suji_board_structure();
	$suji_menu_slug = 'edit.php?post_type=board_post';

	$suji_add = function ( $suji_slug, $suji_prefix = '' ) use ( &$submenu, $suji_menu_slug ) {
		$suji_term = get_term_by( 'slug', $suji_slug, 'board_cat' );
		if ( ! $suji_term ) {
			return;
		}

		$submenu[ $suji_menu_slug ][] = array(
			$suji_prefix . esc_html( $suji_term->name )
				. ' <span class="suji-board-count">' . (int) $suji_term->count . '</span>',
			'edit_posts',
			$suji_menu_slug . '&board_cat=' . $suji_term->slug,
		);
	};

	foreach ( $suji_structure['main'] as $suji_slug ) {
		$suji_add( $suji_slug );
	}

	// 상위 텀을 누르면 하위 게시판 글이 모두 나온다 (계층형 분류는 자식을 포함한다)
	$suji_add( $suji_structure['group']['slug'] );

	foreach ( $suji_structure['group']['children'] as $suji_slug ) {
		$suji_add( $suji_slug, '<span class="suji-board-child">↳</span> ' );
	}
}
add_action( 'admin_menu', 'suji_board_admin_submenus', 999 );

/**
 * 게시판으로 걸러 본 목록 화면에서는 "새로 추가" 버튼도 그 게시판을 달고
 * 열리게 한다. 메뉴에 게시판마다 글쓰기 줄을 따로 두지 않기 위한 것.
 */
function suji_board_add_new_link() {
	$suji_screen = get_current_screen();
	if ( ! $suji_screen || 'edit-board_post' !== $suji_screen->id ) {
		return;
	}

	$suji_slug = isset( $_GET['board_cat'] ) ? sanitize_title( wp_unslash( $_GET['board_cat'] ) ) : '';
	if ( ! $suji_slug ) {
		return;
	}

	$suji_url = add_query_arg(
		array( 'post_type' => 'board_post', 'board_cat' => $suji_slug ),
		admin_url( 'post-new.php' )
	);
	?>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			var btn = document.querySelector('.wrap .page-title-action');
			if (btn) {
				btn.setAttribute('href', <?php echo wp_json_encode( $suji_url ); ?>);
			}
		});
	</script>
	<style>
		.suji-board-child { opacity: .45; margin-right: 2px; }
		.suji-board-count { opacity: .5; font-size: 11px; margin-left: 4px; }
	</style>
	<?php
}
add_action( 'admin_footer', 'suji_board_add_new_link' );

/**
 * 메뉴에 쓰는 작은 스타일은 모든 관리자 화면에서 필요하다.
 */
function suji_board_admin_menu_style() {
	echo '<style>#adminmenu .suji-board-child{opacity:.45;margin-right:2px}'
		. '#adminmenu .suji-board-count{opacity:.45;font-size:11px;margin-left:4px}</style>';
}
add_action( 'admin_head', 'suji_board_admin_menu_style' );

/**
 * When opening "post-new.php?...&board_cat={slug}" (from the submenu links
 * above), pre-check that board in the checklist so it's already selected —
 * no manual ticking needed. Only applies to new, not-yet-tagged posts.
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
