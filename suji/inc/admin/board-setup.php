<?php
/**
 * 게시판 정리 (1회성).
 *
 * 1. '단체 게시판' 상위 텀을 만들고 위원회 게시판 7개를 그 아래로 옮긴다.
 * 2. 더 쓰지 않는 게시판 4개는 글을 휴지통으로 보내고 텀을 지운다.
 *
 * 분류의 rewrite 는 계층형으로 설정돼 있지 않아, 하위로 옮겨도 주소는
 * /board/{슬러그}/ 그대로다. 글은 완전히 지우지 않고 휴지통에 두므로
 * 30일 안에는 되돌릴 수 있다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SUJI_BOARD_SETUP_FLAG = 'suji_board_setup_done';

function suji_board_setup_once() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( get_option( SUJI_BOARD_SETUP_FLAG ) ) {
		return;
	}

	$suji_structure = suji_board_structure();
	$suji_report    = array();

	// ── 1. 단체 게시판 상위 텀 ──
	$suji_group  = $suji_structure['group'];
	$suji_parent = get_term_by( 'slug', $suji_group['slug'], 'board_cat' );

	if ( ! $suji_parent ) {
		$suji_created = wp_insert_term(
			$suji_group['name'],
			'board_cat',
			array( 'slug' => $suji_group['slug'] )
		);
		if ( is_wp_error( $suji_created ) ) {
			return;   // 다음 요청에서 다시 시도
		}
		$suji_parent = get_term( $suji_created['term_id'], 'board_cat' );
		$suji_report[] = '‘단체 게시판’ 상위 게시판 생성';
	}

	// ── 2. 위원회 게시판 7개를 하위로 ──
	$suji_moved = 0;
	foreach ( $suji_group['children'] as $suji_slug ) {
		$suji_term = get_term_by( 'slug', $suji_slug, 'board_cat' );
		if ( ! $suji_term || (int) $suji_term->parent === (int) $suji_parent->term_id ) {
			continue;
		}
		wp_update_term( $suji_term->term_id, 'board_cat', array( 'parent' => $suji_parent->term_id ) );
		$suji_moved++;
	}
	if ( $suji_moved ) {
		$suji_report[] = sprintf( '위원회 게시판 %d개를 단체 게시판 아래로 이동', $suji_moved );
	}

	// ── 3. 이름 오타 정정 (슬러그는 그대로 두어 주소가 바뀌지 않게) ──
	$suji_fixes = array( 'small' => '소공동체위원회 게시판' );
	foreach ( $suji_fixes as $suji_slug => $suji_name ) {
		$suji_term = get_term_by( 'slug', $suji_slug, 'board_cat' );
		if ( $suji_term && $suji_term->name !== $suji_name ) {
			wp_update_term( $suji_term->term_id, 'board_cat', array( 'name' => $suji_name ) );
			$suji_report[] = sprintf( '‘%s’ → ‘%s’ 이름 정정', $suji_term->name, $suji_name );
		}
	}

	// ── 4. 쓰지 않는 게시판 정리 ──
	$suji_trashed = 0;
	$suji_removed = array();
	foreach ( $suji_structure['retired'] as $suji_slug ) {
		$suji_term = get_term_by( 'slug', $suji_slug, 'board_cat' );
		if ( ! $suji_term ) {
			continue;
		}

		$suji_posts = get_posts( array(
			'post_type'      => 'board_post',
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'fields'         => 'ids',
			'tax_query'      => array(
				array(
					'taxonomy'         => 'board_cat',
					'field'            => 'slug',
					'terms'            => $suji_slug,
					'include_children' => false,
				),
			),
		) );

		foreach ( $suji_posts as $suji_post_id ) {
			// 완전 삭제가 아니라 휴지통 — 되돌릴 수 있게 둔다
			if ( wp_trash_post( $suji_post_id ) ) {
				$suji_trashed++;
			}
		}

		$suji_removed[] = $suji_term->name;
		wp_delete_term( $suji_term->term_id, 'board_cat' );
	}
	if ( $suji_removed ) {
		$suji_report[] = sprintf(
			'게시판 삭제: %s (글 %d건은 휴지통으로 이동)',
			implode( ', ', $suji_removed ),
			$suji_trashed
		);
	}

	update_option( SUJI_BOARD_SETUP_FLAG, 1, false );

	if ( $suji_report ) {
		set_transient( 'suji_board_setup_notice', $suji_report, 600 );
	}
}
add_action( 'admin_init', 'suji_board_setup_once' );

function suji_board_setup_notice() {
	$suji_report = get_transient( 'suji_board_setup_notice' );
	if ( ! $suji_report ) {
		return;
	}
	delete_transient( 'suji_board_setup_notice' );

	echo '<div class="notice notice-success is-dismissible"><p><strong>게시판을 정리했습니다.</strong></p><ul style="margin:0 0 .5em 1.5em;list-style:disc">';
	foreach ( (array) $suji_report as $suji_line ) {
		echo '<li>' . esc_html( $suji_line ) . '</li>';
	}
	echo '</ul><p>지운 글은 <strong>휴지통</strong>에 있어 되돌릴 수 있습니다.</p></div>';
}
add_action( 'admin_notices', 'suji_board_setup_notice' );
