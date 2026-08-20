<?php
/**
 * 게시판 조회수.
 *
 * 원본 사이트에서 넘어온 값이 _g5_wr_hit 에 들어 있어 그 키를 그대로 이어
 * 쓴다. 새 사이트에서 열릴 때마다 1씩 올라가므로 원본 숫자에서 이어진다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SUJI_VIEW_META = '_g5_wr_hit';

/**
 * 같은 사람이 새로 고칠 때마다 오르지 않도록 몇 시간 동안 기억한다.
 * 로그인 여부와 무관하게 세도록 IP + 사용자 ID 로 열쇠를 만든다.
 */
function suji_view_seen_key( $post_id ) {
	$suji_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) $_SERVER['REMOTE_ADDR'] : '';
	return 'suji_seen_' . md5( $post_id . '|' . $suji_ip . '|' . get_current_user_id() );
}

function suji_count_board_view() {
	if ( ! is_singular( suji_board_post_types() ) ) {
		return;
	}
	// 미리보기 · 관리 화면 · 로봇 요청은 세지 않는다
	if ( is_preview() || is_admin() || wp_doing_ajax() ) {
		return;
	}

	$suji_id = get_queried_object_id();
	if ( ! $suji_id ) {
		return;
	}

	// 글쓴이 · 관리자가 확인하러 들어온 것은 세지 않는다
	if ( current_user_can( 'edit_post', $suji_id ) ) {
		return;
	}

	$suji_key = suji_view_seen_key( $suji_id );
	if ( get_transient( $suji_key ) ) {
		return;
	}
	set_transient( $suji_key, 1, 6 * HOUR_IN_SECONDS );

	$suji_now = (int) get_post_meta( $suji_id, SUJI_VIEW_META, true );
	update_post_meta( $suji_id, SUJI_VIEW_META, (string) ( $suji_now + 1 ) );
}
add_action( 'template_redirect', 'suji_count_board_view', 20 );

/**
 * 화면에 쓸 조회수. 값이 없으면 0.
 */
function suji_board_views( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	return (int) get_post_meta( $post_id, SUJI_VIEW_META, true );
}
