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
 * Add a "list" + "write new" admin submenu link per board_cat term under
 * "게시판 글", so each board behaves like its own separately managed section:
 * clicking its list shows only that board's posts, and its "글쓰기" link opens
 * a new post with that board already selected (see suji_preselect_board_cat below).
 */
function suji_board_admin_submenus() {
	global $submenu;

	if ( ! current_user_can( 'edit_posts' ) || ! isset( $submenu['edit.php?post_type=board_post'] ) ) {
		return;
	}

	$suji_terms = get_terms( array(
		'taxonomy'   => 'board_cat',
		'hide_empty' => false,
		'orderby'    => 'name',
	) );

	if ( is_wp_error( $suji_terms ) || empty( $suji_terms ) ) {
		return;
	}

	foreach ( $suji_terms as $suji_term ) {
		$submenu['edit.php?post_type=board_post'][] = array(
			esc_html( $suji_term->name ),
			'edit_posts',
			'edit.php?post_type=board_post&board_cat=' . $suji_term->slug,
		);
		$submenu['edit.php?post_type=board_post'][] = array(
			'&nbsp;&nbsp;↳ ' . esc_html( $suji_term->name ) . ' ' . __( '글쓰기', 'suji' ),
			'edit_posts',
			'post-new.php?post_type=board_post&board_cat=' . $suji_term->slug,
		);
	}
}
add_action( 'admin_menu', 'suji_board_admin_submenus', 999 );

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
