<?php
/**
 * 게시판을 글 타입별로 나누는 1회성 이관.
 *
 * board_post + board_cat 하나로 묶여 있던 글 3,000여 건을 게시판별 글 타입으로
 * 옮긴다. 바꾸는 것은 post_type 뿐이고 board_cat 연결은 그대로 남겨두므로,
 * 문제가 생기면 post_type 만 되돌리면 원래 상태가 된다.
 *
 * 공유호스팅에서 한 요청에 3,000건을 처리하면 시간이 모자랄 수 있어 게시판을
 * 하나씩 끊어 처리하고, 진행 상황을 옵션에 남겨 다음 요청에서 이어간다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SUJI_BOARDS_MIGRATED = 'suji_boards_migrated';
const SUJI_BOARDS_PROGRESS = 'suji_boards_migrate_progress';

/**
 * 한 board_cat 슬러그에 속한 board_post 글의 post_type 을 바꾼다.
 * 반환값은 옮긴 건수.
 */
function suji_migrate_board( $suji_slug, $suji_post_type ) {
	global $wpdb;

	$suji_term = get_term_by( 'slug', $suji_slug, 'board_cat' );
	if ( ! $suji_term ) {
		return 0;
	}

	$suji_ids = $wpdb->get_col( $wpdb->prepare(
		"SELECT p.ID
		   FROM {$wpdb->posts} p
		   INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
		  WHERE p.post_type = 'board_post'
		    AND tr.term_taxonomy_id = %d",
		$suji_term->term_taxonomy_id
	) );

	if ( ! $suji_ids ) {
		return 0;
	}

	$suji_in = implode( ',', array_map( 'absint', $suji_ids ) );
	$wpdb->query( $wpdb->prepare(
		"UPDATE {$wpdb->posts} SET post_type = %s WHERE ID IN ({$suji_in})",
		$suji_post_type
	) );

	foreach ( $suji_ids as $suji_id ) {
		clean_post_cache( (int) $suji_id );
	}

	return count( $suji_ids );
}

/**
 * 쓰지 않는 게시판 정리 — 글은 휴지통으로, 텀은 삭제.
 */
function suji_retire_boards() {
	$suji_trashed = 0;
	$suji_names   = array();

	foreach ( suji_retired_boards() as $suji_slug ) {
		$suji_term = get_term_by( 'slug', $suji_slug, 'board_cat' );
		if ( ! $suji_term ) {
			continue;
		}

		$suji_posts = get_posts( array(
			'post_type'      => 'any',
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
			if ( wp_trash_post( $suji_post_id ) ) {
				$suji_trashed++;
			}
		}

		$suji_names[] = $suji_term->name;
		wp_delete_term( $suji_term->term_id, 'board_cat' );
	}

	return array( $suji_names, $suji_trashed );
}

function suji_boards_migrate_step() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( get_option( SUJI_BOARDS_MIGRATED ) ) {
		return;
	}

	$suji_done   = (array) get_option( SUJI_BOARDS_PROGRESS, array() );
	$suji_report = array();

	// 이름 오타 정정 — 슬러그는 그대로 두어 주소가 바뀌지 않게
	$suji_term = get_term_by( 'slug', 'small', 'board_cat' );
	if ( $suji_term && '소공동체위원회 게시판' !== $suji_term->name ) {
		wp_update_term( $suji_term->term_id, 'board_cat', array( 'name' => '소공동체위원회 게시판' ) );
		$suji_report[] = sprintf( '‘%s’ 이름 정정', $suji_term->name );
	}

	// 한 요청에 몇 개씩만 — 게시판당 UPDATE 한 번이라 이 정도는 여유롭다
	$suji_batch = 3;
	$suji_lines = array();

	foreach ( suji_boards() as $suji_type => $suji_board ) {
		foreach ( $suji_board['from'] as $suji_slug ) {
			if ( isset( $suji_done[ $suji_slug ] ) ) {
				continue;
			}

			$suji_count              = suji_migrate_board( $suji_slug, $suji_type );
			$suji_done[ $suji_slug ] = $suji_count;
			$suji_lines[]            = sprintf( '‘%s’ %d건 → %s', $suji_slug, $suji_count, $suji_board['name'] );

			if ( count( $suji_lines ) >= $suji_batch ) {
				update_option( SUJI_BOARDS_PROGRESS, $suji_done, false );
				$suji_lines[] = '남은 게시판은 다음 화면에서 이어서 옮깁니다.';
				set_transient( 'suji_boards_migrate_notice', $suji_lines, 120 );
				return;
			}
		}
	}

	if ( $suji_lines ) {
		update_option( SUJI_BOARDS_PROGRESS, $suji_done, false );
		$suji_report = array_merge( $suji_report, $suji_lines );
	}

	// 모든 게시판을 옮긴 뒤 정리
	list( $suji_names, $suji_trashed ) = suji_retire_boards();
	if ( $suji_names ) {
		$suji_report[] = sprintf(
			'쓰지 않는 게시판 삭제: %s (글 %d건은 휴지통)',
			implode( ', ', $suji_names ),
			$suji_trashed
		);
	}

	$suji_total = array_sum( $suji_done );
	$suji_report[] = sprintf( '게시판 %d개로 나눠 글 %d건 이관 완료', count( suji_boards() ), $suji_total );

	update_option( SUJI_BOARDS_MIGRATED, 1, false );
	delete_option( SUJI_BOARDS_PROGRESS );

	// 새 주소 규칙을 적용한다
	flush_rewrite_rules( false );

	set_transient( 'suji_boards_migrate_notice', $suji_report, 600 );
}
add_action( 'admin_init', 'suji_boards_migrate_step' );

function suji_boards_migrate_notice() {
	$suji_report = get_transient( 'suji_boards_migrate_notice' );
	if ( ! $suji_report ) {
		return;
	}
	delete_transient( 'suji_boards_migrate_notice' );

	$suji_done = get_option( SUJI_BOARDS_MIGRATED );
	printf(
		'<div class="notice notice-%s is-dismissible"><p><strong>게시판 이관</strong></p><ul style="margin:0 0 .5em 1.5em;list-style:disc">',
		$suji_done ? 'success' : 'info'
	);
	foreach ( (array) $suji_report as $suji_line ) {
		echo '<li>' . esc_html( $suji_line ) . '</li>';
	}
	echo '</ul>';
	if ( ! $suji_done ) {
		echo '<p>다른 관리자 화면으로 이동하면 남은 게시판을 이어서 옮깁니다.</p>';
	}
	echo '</div>';
}
add_action( 'admin_notices', 'suji_boards_migrate_notice' );
