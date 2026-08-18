<?php
/**
 * 게시판 정의 — 게시판마다 하나의 사용자 지정 글 타입.
 *
 * 예전에는 board_post 하나에 board_cat 분류로 게시판을 구분했다. 그 구조로는
 * 게시판별로 다른 입력 필드를 줄 수 없고(주보의 e-book 링크, 자료실의 첨부처럼),
 * 관리자 메뉴와 메뉴 편집 화면에서도 게시판이 한 덩어리로만 다뤄졌다.
 *
 * 위원회 게시판 7개는 글이 적고 구조가 같아 'suji_group' 하나로 묶고,
 * 어느 위원회인지는 기존 board_cat 분류로 구분한다. 덕분에 /board/sangim/
 * 같은 주소도 그대로 살아 있다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 게시판 목록. 키는 글 타입 이름, 'from' 은 옮겨올 board_cat 슬러그.
 */
function suji_boards() {
	return array(
		'suji_notice'  => array(
			'name' => '공지사항',
			'slug' => 'notice',
			'desc' => '수지성당의 공지사항을 안내해드립니다.',
			'icon' => 'dashicons-megaphone',
			'from' => array( 'notice' ),
		),
		'suji_bulletin' => array(
			'name' => '본당 주보',
			'slug' => 'bible',
			'desc' => '주일마다 발행되는 본당 주보입니다.',
			'icon' => 'dashicons-book-alt',
			'from' => array( 'bible' ),
		),
		'suji_gallery' => array(
			'name' => '포토앨범',
			'slug' => 'gallery',
			'desc' => '수지성당의 다양한 순간을 사진으로 만나보세요.',
			'icon' => 'dashicons-format-gallery',
			'from' => array( 'gallery' ),
		),
		'suji_story'   => array(
			'name' => '사제 게시판',
			'slug' => 'story',
			'desc' => '신부님들의 말씀을 나누는 공간입니다.',
			'icon' => 'dashicons-testimonial',
			'from' => array( 'story' ),
		),
		'suji_docs'    => array(
			'name' => '문서 자료실',
			'slug' => 'docu',
			'desc' => '본당에서 쓰이는 문서와 서식을 모았습니다.',
			'icon' => 'dashicons-media-document',
			'from' => array( 'docu' ),
		),
		'suji_group'   => array(
			'name'     => '단체 게시판',
			'slug'     => 'group',
			'desc'     => '위원회와 단체의 소식을 전합니다.',
			'icon'     => 'dashicons-groups',
			// 어느 위원회인지는 board_cat 분류로 구분한다
			'taxonomy' => true,
			'from'     => array( 'sangim', 'small', 'mainorg', 'jebun', 'standard', 'teen', 'jejung' ),
		),
	);
}

/**
 * 더 쓰지 않는 게시판 (board_cat 슬러그).
 */
function suji_retired_boards() {
	return array( 'sense', 'cbck', 'free', 'pray' );
}

/**
 * 글 타입 이름 -> 게시판 정의. 없으면 null.
 */
function suji_board_of( $post_type ) {
	$suji_boards = suji_boards();
	return $suji_boards[ $post_type ] ?? null;
}

/**
 * 게시판 글 타입 이름 목록.
 */
function suji_board_post_types() {
	return array_keys( suji_boards() );
}

/**
 * 게시판 머리말에 쓰는 아이콘. 게시판 성격이 한눈에 구분되도록 둔다.
 */
function suji_board_icon( $slug ) {
	$suji_icons = array(
		'notice'  => '<path d="M9 4h9a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V8z"></path><path d="M9 4v4H5"></path><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="13" y2="17"></line>',
		'bible'   => '<path d="M4 5a2 2 0 0 1 2-2h12v18H6a2 2 0 0 1-2-2z"></path><line x1="8" y1="7" x2="14" y2="7"></line><line x1="8" y1="11" x2="14" y2="11"></line>',
		'gallery' => '<rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><path d="M21 15l-5-5L5 21"></path>',
		'story'   => '<path d="M20 15a2 2 0 0 1-2 2H8l-4 4V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2z"></path>',
		'docu'    => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"></path><path d="M14 3v5h5"></path><line x1="9" y1="13" x2="15" y2="13"></line><line x1="9" y1="17" x2="13" y2="17"></line>',
		'group'   => '<path d="M17 20v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9.5" cy="7" r="3.5"></circle><path d="M22 20v-2a4 4 0 0 0-3-3.87"></path><path d="M16.5 3.6a3.5 3.5 0 0 1 0 6.8"></path>',
	);

	return $suji_icons[ $slug ] ?? $suji_icons['notice'];
}

function suji_register_boards() {
	foreach ( suji_boards() as $suji_type => $suji_board ) {
		register_post_type( $suji_type, array(
			'labels'       => array(
				'name'          => $suji_board['name'],
				'singular_name' => $suji_board['name'],
				'add_new'       => __( '글쓰기', 'suji' ),
				'add_new_item'  => $suji_board['name'] . ' ' . __( '글쓰기', 'suji' ),
				'edit_item'     => $suji_board['name'] . ' ' . __( '글 수정', 'suji' ),
				'search_items'  => $suji_board['name'] . ' ' . __( '검색', 'suji' ),
				'not_found'     => __( '게시글이 없습니다', 'suji' ),
				'all_items'     => __( '글 목록', 'suji' ),
				// 외모 > 메뉴 화면의 아카이브 항목 이름
				'archives'      => $suji_board['name'] . ' ' . __( '목록', 'suji' ),
			),
			'public'       => true,
			'has_archive'  => $suji_board['slug'],
			'rewrite'      => array( 'slug' => $suji_board['slug'], 'with_front' => false ),
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
			'menu_icon'    => $suji_board['icon'],
			'show_in_menu' => true,
			'show_in_rest' => true,
		) );
	}

	// 위원회 구분용 분류 — 주소(/board/{슬러그}/)를 그대로 유지한다
	register_taxonomy( 'board_cat', array( 'suji_group' ), array(
		'labels'            => array(
			'name'          => __( '위원회', 'suji' ),
			'singular_name' => __( '위원회', 'suji' ),
			'all_items'     => __( '모든 위원회', 'suji' ),
		),
		'public'            => true,
		'hierarchical'      => true,
		'rewrite'           => array( 'slug' => 'board', 'with_front' => false ),
		'show_admin_column' => true,
		'show_in_rest'      => true,
	) );

	// 이관이 끝나기 전까지는 예전 글 타입도 살려 둔다 (원본이자 되돌릴 여지)
	register_post_type( 'board_post', array(
		'labels'       => array(
			'name'          => __( '이전 게시판 글', 'suji' ),
			'singular_name' => __( '이전 게시판 글', 'suji' ),
		),
		'public'              => true,
		'exclude_from_search' => true,
		'has_archive'         => 'board-list',
		'rewrite'             => array( 'slug' => 'entry', 'with_front' => false ),
		'supports'            => array( 'title', 'editor' ),
		'menu_icon'           => 'dashicons-archive',
		// 이관이 끝나면 관리자 화면에서 감춘다
		'show_ui'             => ! get_option( 'suji_boards_migrated' ),
		'show_in_menu'        => ! get_option( 'suji_boards_migrated' ),
		'show_in_rest'        => true,
	) );
}
add_action( 'init', 'suji_register_boards' );

/**
 * 게시판 구성(주소 규칙)이 바뀌면 리라이트 규칙을 한 번 다시 만든다.
 * 관리자 화면에 들어가지 않아도 스스로 맞춰지도록 init 에 걸어 둔다.
 */
function suji_maybe_flush_board_rewrites() {
	$suji_slugs = array();
	foreach ( suji_boards() as $suji_type => $suji_board ) {
		$suji_slugs[] = $suji_type . ':' . $suji_board['slug'];
	}
	$suji_sig = md5( implode( '|', $suji_slugs ) );

	if ( get_option( 'suji_boards_rewrite_sig' ) === $suji_sig ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'suji_boards_rewrite_sig', $suji_sig, false );
}
add_action( 'init', 'suji_maybe_flush_board_rewrites', 99 );

/**
 * 예전 주소를 새 주소로 넘긴다.
 *
 * 게시판을 글 타입으로 나누기 전에는 목록이 /board/{슬러그}/ (분류 archive),
 * 글이 /entry/{제목}/ 이었다. board_cat 분류는 이제 단체 게시판에만 붙어 있어
 * /board/notice/ 같은 주소는 글이 0건인 빈 화면이 된다. 메뉴에 그 주소가
 * 들어가 있거나 링크를 공유한 경우가 있으므로 301 로 넘긴다.
 */
function suji_redirect_legacy_urls() {
	// 목록: /board/{슬러그}/ -> 해당 게시판 목록
	if ( is_tax( 'board_cat' ) ) {
		$suji_term = get_queried_object();
		if ( empty( $suji_term->slug ) ) {
			return;
		}

		$suji_group_type = '';

		foreach ( suji_boards() as $suji_type => $suji_board ) {
			// 단체 게시판은 위원회별 목록(/board/sangim/ 등)을 그대로 쓴다
			if ( ! empty( $suji_board['taxonomy'] ) ) {
				$suji_group_type = $suji_type;
				if ( in_array( $suji_term->slug, $suji_board['from'], true ) ) {
					return;
				}
				continue;
			}

			if ( in_array( $suji_term->slug, $suji_board['from'], true ) ) {
				$suji_to = get_post_type_archive_link( $suji_type );
				if ( $suji_to ) {
					wp_safe_redirect( $suji_to, 301 );
					exit;
				}
			}
		}

		// 위원회도 아니고 옮겨간 게시판도 아닌 텀(예: 묶음용으로 만든 'groups')
		if ( $suji_group_type ) {
			$suji_to = get_post_type_archive_link( $suji_group_type );
			if ( $suji_to ) {
				wp_safe_redirect( $suji_to, 301 );
				exit;
			}
		}
		return;
	}

	// 글: /entry/{제목}/ -> 옮겨간 글
	if ( ! is_404() ) {
		return;
	}

	$suji_path = trim( parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ) ?: '', '/' );
	if ( 0 !== strpos( $suji_path, 'entry/' ) ) {
		return;
	}

	$suji_slug = sanitize_title( basename( $suji_path ) );
	if ( ! $suji_slug ) {
		return;
	}

	$suji_found = get_posts( array(
		'post_type'      => suji_board_post_types(),
		'name'           => $suji_slug,
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'fields'         => 'ids',
	) );

	if ( $suji_found ) {
		wp_safe_redirect( get_permalink( $suji_found[0] ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'suji_redirect_legacy_urls' );
