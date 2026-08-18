<?php
/**
 * 역대 성직자 / 수도자 초기 데이터 심기 (1회성).
 *
 * inc/clergy-data.php 의 71명을 lofields 리피터로 옮긴다. 사진은 테마의
 * assets/images/clergy/ 에서 미디어 라이브러리로 등록하고 그 첨부 ID 를 쓴다.
 * 한 번 끝나면 옵션 플래그가 남아 다시는 돌지 않으므로, 이후 사무실에서
 * 관리자 화면으로 자유롭게 수정할 수 있다.
 *
 * 공유호스팅에서 한 요청에 71명 + 사진 58장을 처리하면 시간이 모자랄 수 있어
 * 관리자 페이지를 볼 때마다 사진을 조금씩 나눠 등록하고, 다 모이면 그때
 * 리피터를 채운다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SUJI_CLERGY_SEED_FLAG  = 'suji_clergy_seeded';
const SUJI_CLERGY_SEED_STATE = 'suji_clergy_seed_state';
const SUJI_CLERGY_SEED_BATCH = 12;   // 한 요청에 등록할 사진 수

/**
 * 아직 심을 게 남았는지, 심을 수 있는 상태인지 확인한다.
 * 필드 그룹(JSON 동기화)이 아직 없으면 아무것도 하지 않는다.
 */
function suji_clergy_seed_can_run() {
	if ( get_option( SUJI_CLERGY_SEED_FLAG ) ) {
		return false;
	}
	if ( ! function_exists( 'lof_get_field_object' ) || ! function_exists( 'update_field' ) ) {
		return false;
	}
	if ( ! lof_get_field_object( 'clergy_pastors' ) ) {
		return false;   // 필드 그룹 미동기화
	}
	return (bool) get_page_by_path( 'clergy' );
}

/**
 * 사진 파일 하나를 미디어 라이브러리에 등록하고 첨부 ID 를 돌려준다.
 */
function suji_clergy_import_photo( $filename ) {
	$path = SUJI_DIR . '/assets/images/clergy/' . $filename;
	if ( ! file_exists( $path ) ) {
		return 0;
	}

	$upload = wp_upload_bits( $filename, null, file_get_contents( $path ) );
	if ( ! empty( $upload['error'] ) ) {
		return 0;
	}

	$attachment_id = wp_insert_attachment(
		array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => pathinfo( $filename, PATHINFO_FILENAME ),
			'post_status'    => 'inherit',
		),
		$upload['file']
	);

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		return 0;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	wp_update_attachment_metadata(
		$attachment_id,
		wp_generate_attachment_metadata( $attachment_id, $upload['file'] )
	);

	return (int) $attachment_id;
}

/**
 * 관리자 화면을 열 때마다 조금씩 진행한다.
 */
function suji_clergy_seed_step() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( ! suji_clergy_seed_can_run() ) {
		return;
	}

	$sections = require SUJI_DIR . '/inc/clergy-data.php';
	$state    = get_option( SUJI_CLERGY_SEED_STATE, array( 'photos' => array() ) );
	$photos   = is_array( $state['photos'] ?? null ) ? $state['photos'] : array();

	// 필요한 사진 목록 (중복 제거 — none.jpg 는 여러 명이 함께 쓴다)
	$needed = array();
	foreach ( $sections as $section ) {
		foreach ( $section['rows'] as $row ) {
			$needed[ $row['photo'] ] = true;
		}
	}
	$needed = array_keys( $needed );

	$remaining = array_values( array_diff( $needed, array_keys( $photos ) ) );

	if ( $remaining ) {
		foreach ( array_slice( $remaining, 0, SUJI_CLERGY_SEED_BATCH ) as $filename ) {
			$photos[ $filename ] = suji_clergy_import_photo( $filename );
		}
		update_option( SUJI_CLERGY_SEED_STATE, array( 'photos' => $photos ), false );

		$done  = count( $photos );
		$total = count( $needed );
		if ( $done < $total ) {
			set_transient( 'suji_clergy_seed_notice', sprintf( '성직자 사진 등록 중… (%d/%d)', $done, $total ), 60 );
			return;   // 다음 요청에서 이어서
		}
	}

	// 사진이 다 모였으면 리피터를 채운다.
	$page_id = get_page_by_path( 'clergy' )->ID;
	$counts  = array();

	foreach ( $sections as $section ) {
		$rows = array();
		foreach ( $section['rows'] as $row ) {
			$rows[] = array(
				'photo' => $photos[ $row['photo'] ] ?? 0,
				'rank'  => $row['rank'] ?? '',
				'name'  => $row['name'] ?? '',
				'order' => $row['order'] ?? '',
				'term'  => $row['term'] ?? '',
			);
		}
		update_field( 'clergy_' . $section['slug'], $rows, $page_id );
		$counts[] = $section['title'] . ' ' . count( $rows ) . '명';
	}

	update_option( SUJI_CLERGY_SEED_FLAG, 1, false );
	delete_option( SUJI_CLERGY_SEED_STATE );

	set_transient(
		'suji_clergy_seed_notice',
		'역대 성직자 / 수도자 초기 데이터를 등록했습니다 — ' . implode( ', ', $counts ) . '. 이제 페이지 편집 화면에서 수정할 수 있습니다.',
		300
	);
}
add_action( 'admin_init', 'suji_clergy_seed_step' );

/**
 * 진행 상황 / 완료 안내.
 */
function suji_clergy_seed_notice() {
	$message = get_transient( 'suji_clergy_seed_notice' );
	if ( ! $message ) {
		return;
	}
	delete_transient( 'suji_clergy_seed_notice' );

	printf(
		'<div class="notice notice-info is-dismissible"><p>%s</p></div>',
		esc_html( $message )
	);
}
add_action( 'admin_notices', 'suji_clergy_seed_notice' );
