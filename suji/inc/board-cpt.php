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

/**
 * 메뉴에서 지금 보고 있는 게시판을 강조한다.
 *
 * 메뉴 항목이 예전 분류 주소(/board/notice/)로 들어가 있으면 워드프레스가
 * 지금 화면(글 타입 목록)과 같은 것으로 보지 못해 current 클래스를 붙이지
 * 않는다. 주소를 옮겨간 곳으로 환산해 직접 대조한다. 메뉴를 새 아카이브
 * 항목으로 바꿔 두어도 그대로 동작한다.
 */
function suji_menu_canonical_url( $url ) {
	$suji_path = trim( (string) parse_url( $url, PHP_URL_PATH ), '/' );

	if ( 0 === strpos( $suji_path, 'board/' ) ) {
		$suji_slug = basename( $suji_path );
		foreach ( suji_boards() as $suji_type => $suji_board ) {
			if ( ! empty( $suji_board['taxonomy'] ) ) {
				continue;   // 위원회별 목록은 주소가 그대로다
			}
			if ( in_array( $suji_slug, $suji_board['from'], true ) ) {
				return untrailingslashit( (string) get_post_type_archive_link( $suji_type ) );
			}
		}
	}

	return untrailingslashit( $url );
}

function suji_mark_current_board_menu( $items ) {
	if ( is_admin() || ! is_array( $items ) ) {
		return $items;
	}

	$suji_types   = suji_board_post_types();
	$suji_current = '';

	if ( is_post_type_archive( $suji_types ) ) {
		$suji_obj     = get_queried_object();
		$suji_current = ( $suji_obj && isset( $suji_obj->name ) )
			? (string) get_post_type_archive_link( $suji_obj->name )
			: '';
	} elseif ( is_singular( $suji_types ) ) {
		$suji_current = (string) get_post_type_archive_link( get_post_type() );
	} elseif ( is_tax( 'board_cat' ) ) {
		$suji_link    = get_term_link( get_queried_object() );
		$suji_current = is_wp_error( $suji_link ) ? '' : (string) $suji_link;
	}

	if ( ! $suji_current ) {
		return $items;
	}

	$suji_current = untrailingslashit( $suji_current );
	$suji_hits    = array();
	$suji_by_id   = array();

	foreach ( $items as $suji_item ) {
		$suji_by_id[ $suji_item->ID ] = $suji_item;
		if ( suji_menu_canonical_url( $suji_item->url ) === $suji_current ) {
			$suji_hits[] = $suji_item;
		}
	}

	if ( ! $suji_hits ) {
		return $items;
	}

	// 최상위와 하위가 같은 곳을 가리키는 경우가 있다(본당 소식 = 공지 사항).
	// 그때는 하위 항목을 현재 위치로 본다.
	$suji_match = $suji_hits[0];
	foreach ( $suji_hits as $suji_hit ) {
		if ( (int) $suji_hit->menu_item_parent ) {
			$suji_match = $suji_hit;
			break;
		}
	}

	$suji_match->classes[] = 'current-menu-item';

	$suji_parent = (int) $suji_match->menu_item_parent;
	while ( $suji_parent && isset( $suji_by_id[ $suji_parent ] ) ) {
		$suji_by_id[ $suji_parent ]->classes[] = 'current-menu-ancestor';
		$suji_by_id[ $suji_parent ]->classes[] = 'current-menu-parent';
		$suji_parent = (int) $suji_by_id[ $suji_parent ]->menu_item_parent;
	}

	// 워드프레스가 이미 붙여 둔 것과 겹칠 수 있다
	foreach ( $items as $suji_item ) {
		if ( is_array( $suji_item->classes ) ) {
			$suji_item->classes = array_values( array_unique( $suji_item->classes ) );
		}
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'suji_mark_current_board_menu' );
