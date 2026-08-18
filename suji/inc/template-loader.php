<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Auto-load templates/page/page-{slug}.php for a page, without needing a
 * matching root-level file or a manually assigned "Template" in wp-admin.
 * A page with an explicitly assigned custom Template always takes priority.
 */
function suji_page_slug_template( $template ) {
	if ( ! is_page() ) {
		return $template;
	}

	if ( get_page_template_slug( get_queried_object_id() ) ) {
		return $template;
	}

	$suji_slug      = get_queried_object()->post_name;
	$suji_candidate = SUJI_DIR . '/templates/page/page-' . $suji_slug . '.php';

	if ( file_exists( $suji_candidate ) ) {
		return $suji_candidate;
	}

	return $template;
}
add_filter( 'template_include', 'suji_page_slug_template' );

/**
 * 게시판 글 타입의 목록 · 상세 · 위원회별 목록을 templates/ 아래 파일로 보낸다.
 * 테마 루트에 글 타입마다 빈 파일을 두지 않기 위해 여기서 한 번에 처리한다.
 */
function suji_board_template( $template ) {
	// 이관 전 글(board_post)도 같은 화면으로 보여 준다
	$suji_types = array_merge( suji_board_post_types(), array( 'board_post' ) );

	// 포토앨범 목록은 사진 격자
	if ( is_post_type_archive( 'suji_gallery' ) ) {
		return SUJI_DIR . '/templates/archive-gallery.php';
	}

	// 나머지 게시판 목록 + 위원회별 목록
	if ( is_post_type_archive( $suji_types ) || is_tax( 'board_cat' ) ) {
		return SUJI_DIR . '/templates/archive-board.php';
	}

	// 게시판 글 상세
	if ( is_singular( $suji_types ) ) {
		return SUJI_DIR . '/templates/single-board.php';
	}

	return $template;
}
add_filter( 'template_include', 'suji_board_template' );
