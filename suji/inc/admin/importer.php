<?php
/**
 * 원본 사이트(sujica.or.kr)에서 글과 이미지를 가져온다.
 *
 * 그누보드 글은 DB 안에 있어 FTP 로는 못 꺼내지만, 목록과 본문이 공개 페이지로
 * 열려 있고 첨부 이미지도 /data/file/ 로 바로 받아진다. 그래서 공개 페이지를
 * 읽어 옮긴다.
 *
 * 이미 가져온 글은 원본 글번호(_g5_wr_id)로 걸러내므로 몇 번 돌려도 안전하다.
 * 이관 당시 글에는 그 메타가 없어, 그때 넘어온 글은 제목을 정규화해 맞춘다
 * (워드프레스가 '-'를 '–' 로 '...'를 '…' 로 바꿔 놓기 때문).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SUJI_IMPORT_ORIGIN = 'https://sujica.or.kr';
const SUJI_IMPORT_QUEUE  = 'suji_import_queue';

/**
 * 원본 게시판(bo_table) -> 새 글 타입 + 위원회 텀.
 */
function suji_import_map() {
	$suji_map = array();

	foreach ( suji_boards() as $suji_type => $suji_board ) {
		foreach ( $suji_board['from'] as $suji_slug ) {
			$suji_map[ $suji_slug ] = array(
				'type' => $suji_type,
				'term' => empty( $suji_board['taxonomy'] ) ? '' : $suji_slug,
				'name' => $suji_board['name'],
			);
		}
	}

	return $suji_map;
}

function suji_import_get( $url ) {
	$suji_res = wp_remote_get( $url, array(
		'timeout'    => 25,
		'user-agent' => 'suji-theme-importer/1.0',
	) );

	if ( is_wp_error( $suji_res ) ) {
		return $suji_res;
	}

	$suji_code = wp_remote_retrieve_response_code( $suji_res );
	if ( 200 !== (int) $suji_code ) {
		return new WP_Error( 'suji_http', sprintf( 'HTTP %d — %s', $suji_code, $url ) );
	}

	return wp_remote_retrieve_body( $suji_res );
}

/**
 * 제목 비교용 키. wptexturize 로 바뀌는 기호와 공백을 모두 지운다.
 */
function suji_import_key( $title ) {
	$suji_title = html_entity_decode( wp_strip_all_tags( (string) $title ), ENT_QUOTES, 'UTF-8' );
	$suji_title = str_replace(
		array( '–', '—', '−', '…', '‘', '’', '“', '”', "\xc2\xa0" ),
		array( '-', '-', '-', '...', "'", "'", '"', '"', ' ' ),
		$suji_title
	);
	return preg_replace( '/[^0-9A-Za-z가-힣]/u', '', $suji_title );
}

/**
 * 이미 있는 글의 제목 키 -> 글 ID.
 */
function suji_import_existing( $post_type ) {
	static $suji_cache = array();

	if ( isset( $suji_cache[ $post_type ] ) ) {
		return $suji_cache[ $post_type ];
	}

	global $wpdb;
	$suji_rows = $wpdb->get_results( $wpdb->prepare(
		"SELECT ID, post_title FROM {$wpdb->posts}
		  WHERE post_type = %s AND post_status IN ('publish','draft','pending','private')",
		$post_type
	) );

	$suji_index = array();
	foreach ( $suji_rows as $suji_row ) {
		$suji_index[ suji_import_key( $suji_row->post_title ) ] = (int) $suji_row->ID;
	}

	$suji_cache[ $post_type ] = $suji_index;
	return $suji_index;
}

/**
 * 원본 글번호로 이미 가져왔는지 확인.
 */
function suji_import_found_by_wr_id( $bo, $wr_id ) {
	global $wpdb;
	return (int) $wpdb->get_var( $wpdb->prepare(
		"SELECT p.ID FROM {$wpdb->posts} p
		   INNER JOIN {$wpdb->postmeta} m1 ON m1.post_id = p.ID AND m1.meta_key = '_g5_wr_id'
		   INNER JOIN {$wpdb->postmeta} m2 ON m2.post_id = p.ID AND m2.meta_key = '_g5_bo_table'
		  WHERE m1.meta_value = %s AND m2.meta_value = %s LIMIT 1",
		(string) $wr_id,
		$bo
	) );
}

/**
 * 목록 한 쪽에서 (글번호, 제목) 을 뽑는다.
 * 일반 스킨은 표, 포토앨범은 갤러리 스킨이라 두 방식을 함께 본다.
 */
function suji_import_parse_list( $html ) {
	$suji_items = array();

	if ( preg_match( '#<tbody>(.*?)</tbody>#s', $html, $suji_tb ) ) {
		preg_match_all( '#<tr[^>]*>(.*?)</tr>#s', $suji_tb[1], $suji_rows );
		foreach ( $suji_rows[1] as $suji_row ) {
			if ( preg_match( '#class="bo_tit">\s*<a[^>]*wr_id=(\d+)[^>]*>(.*?)</a>#s', $suji_row, $suji_m ) ) {
				$suji_items[ $suji_m[1] ] = trim( wp_strip_all_tags( $suji_m[2] ) );
			}
		}
	}

	if ( ! $suji_items ) {
		preg_match_all( '#wr_id=(\d+)[^>]*>(.*?)</a>#s', $html, $suji_m, PREG_SET_ORDER );
		foreach ( $suji_m as $suji_one ) {
			$suji_title = trim( wp_strip_all_tags( $suji_one[2] ) );
			if ( '' !== $suji_title && ! isset( $suji_items[ $suji_one[1] ] ) ) {
				$suji_items[ $suji_one[1] ] = $suji_title;
			}
		}
	}

	return $suji_items;
}

/**
 * 글 상세에서 필요한 값을 뽑는다.
 */
function suji_import_parse_post( $html ) {
	$suji_out = array( 'title' => '', 'date' => '', 'author' => '', 'hit' => 0, 'content' => '' );

	if ( preg_match( '#<h2[^>]*id="bo_v_title"[^>]*>(.*?)</h2>#s', $html, $suji_m )
		|| preg_match( '#class="bo_v_tit"[^>]*>(.*?)</(?:h1|h2|div|span)>#s', $html, $suji_m ) ) {
		$suji_out['title'] = trim( html_entity_decode( wp_strip_all_tags( $suji_m[1] ), ENT_QUOTES, 'UTF-8' ) );
	}

	// 작성일 — "26-08-15 10:43" 또는 "2026-08-15 10:43"
	if ( preg_match( '#(\d{2,4})-(\d{2})-(\d{2})\s+(\d{2}):(\d{2})#', $html, $suji_d ) ) {
		$suji_year = strlen( $suji_d[1] ) === 2 ? '20' . $suji_d[1] : $suji_d[1];
		$suji_out['date'] = sprintf( '%s-%s-%s %s:%s:00', $suji_year, $suji_d[2], $suji_d[3], $suji_d[4], $suji_d[5] );
	}

	if ( preg_match( '#class="sv_member"[^>]*>(.*?)</#s', $html, $suji_m )
		|| preg_match( '#class="sv_guest"[^>]*>(.*?)</#s', $html, $suji_m ) ) {
		$suji_out['author'] = trim( wp_strip_all_tags( $suji_m[1] ) );
	}

	if ( preg_match( '#조회\s*([\d,]+)#u', $html, $suji_m ) ) {
		$suji_out['hit'] = (int) str_replace( ',', '', $suji_m[1] );
	}

	if ( preg_match( '#id="bo_v_atc"[^>]*>(.*?)<!--\s*\}?\s*본문#s', $html, $suji_m )
		|| preg_match( '#id="bo_v_con"[^>]*>(.*?)</div>\s*</div>#s', $html, $suji_m ) ) {
		$suji_body = $suji_m[1];
		// 그누보드가 감싸는 껍데기 제거
		$suji_body = preg_replace( '#<h2[^>]*>.*?</h2>#s', '', $suji_body );
		$suji_out['content'] = trim( $suji_body );
	}

	return $suji_out;
}

/**
 * 원본 이미지를 미디어 라이브러리로 옮기고 새 주소를 돌려준다.
 */
function suji_import_sideload( $url, $post_id ) {
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$suji_body = suji_import_get( $url );
	if ( is_wp_error( $suji_body ) || '' === $suji_body ) {
		return '';
	}

	$suji_name = sanitize_file_name( rawurldecode( wp_basename( parse_url( $url, PHP_URL_PATH ) ) ) );
	if ( ! preg_match( '/\.(jpe?g|png|gif|webp)$/i', $suji_name ) ) {
		$suji_name .= '.jpg';
	}

	$suji_upload = wp_upload_bits( $suji_name, null, $suji_body );
	if ( ! empty( $suji_upload['error'] ) ) {
		return '';
	}

	$suji_id = wp_insert_attachment( array(
		'post_mime_type' => wp_check_filetype( $suji_upload['file'] )['type'] ?: 'image/jpeg',
		'post_title'     => pathinfo( $suji_name, PATHINFO_FILENAME ),
		'post_status'    => 'inherit',
		'post_parent'    => $post_id,
	), $suji_upload['file'], $post_id );

	if ( is_wp_error( $suji_id ) || ! $suji_id ) {
		return '';
	}

	wp_update_attachment_metadata( $suji_id, wp_generate_attachment_metadata( $suji_id, $suji_upload['file'] ) );

	return wp_get_attachment_url( $suji_id );
}

/**
 * 본문의 원본 사이트 이미지를 모두 옮기고 주소를 바꾼다. 옮긴 장수를 돌려준다.
 */
function suji_import_localize_images( $post_id ) {
	$suji_post = get_post( $post_id );
	if ( ! $suji_post ) {
		return 0;
	}

	preg_match_all( '#<img[^>]+src="(https?://(?:www\.)?sujica\.or\.kr/[^"]+)"#i', $suji_post->post_content, $suji_m );
	if ( empty( $suji_m[1] ) ) {
		return 0;
	}

	$suji_content = $suji_post->post_content;
	$suji_moved   = 0;
	$suji_first   = '';

	foreach ( array_unique( $suji_m[1] ) as $suji_url ) {
		$suji_new = suji_import_sideload( $suji_url, $post_id );
		if ( ! $suji_new ) {
			continue;
		}
		$suji_content = str_replace( $suji_url, $suji_new, $suji_content );
		$suji_moved++;
		if ( ! $suji_first ) {
			$suji_first = $suji_new;
		}
	}

	if ( $suji_moved ) {
		wp_update_post( array( 'ID' => $post_id, 'post_content' => $suji_content ) );

		// 첫 사진을 대표 이미지로 — 포토앨범 격자가 이걸 쓴다
		if ( $suji_first && ! get_post_thumbnail_id( $post_id ) ) {
			$suji_att = attachment_url_to_postid( $suji_first );
			if ( $suji_att ) {
				set_post_thumbnail( $post_id, $suji_att );
			}
		}
	}

	return $suji_moved;
}

/**
 * 글 하나를 가져온다. 결과 문자열을 돌려준다.
 */
function suji_import_one( $bo, $wr_id ) {
	$suji_map = suji_import_map();
	if ( ! isset( $suji_map[ $bo ] ) ) {
		return 'skip: 알 수 없는 게시판 ' . $bo;
	}

	if ( suji_import_found_by_wr_id( $bo, $wr_id ) ) {
		return 'skip: 이미 있음';
	}

	$suji_html = suji_import_get( SUJI_IMPORT_ORIGIN . '/bbs/board.php?bo_table=' . $bo . '&wr_id=' . $wr_id );
	if ( is_wp_error( $suji_html ) ) {
		return 'error: ' . $suji_html->get_error_message();
	}

	$suji_data = suji_import_parse_post( $suji_html );
	if ( '' === $suji_data['title'] ) {
		return 'error: 제목을 읽지 못했습니다';
	}

	$suji_type     = $suji_map[ $bo ]['type'];
	$suji_existing = suji_import_existing( $suji_type );
	$suji_key      = suji_import_key( $suji_data['title'] );

	// 이관 당시 넘어온 글이면 새로 만들지 않고 글번호만 붙여 둔다
	if ( isset( $suji_existing[ $suji_key ] ) ) {
		update_post_meta( $suji_existing[ $suji_key ], '_g5_wr_id', (string) $wr_id );
		update_post_meta( $suji_existing[ $suji_key ], '_g5_bo_table', $bo );
		return 'link: 기존 글에 원본 번호 연결';
	}

	$suji_post_id = wp_insert_post( array(
		'post_type'    => $suji_type,
		'post_status'  => 'publish',
		'post_title'   => $suji_data['title'],
		'post_content' => wp_kses_post( $suji_data['content'] ),
		'post_date'    => $suji_data['date'] ?: current_time( 'mysql' ),
	), true );

	if ( is_wp_error( $suji_post_id ) ) {
		return 'error: ' . $suji_post_id->get_error_message();
	}

	update_post_meta( $suji_post_id, '_g5_wr_id', (string) $wr_id );
	update_post_meta( $suji_post_id, '_g5_bo_table', $bo );
	if ( $suji_data['author'] ) {
		update_post_meta( $suji_post_id, '_g5_wr_name', $suji_data['author'] );
	}
	if ( $suji_data['hit'] ) {
		update_post_meta( $suji_post_id, '_g5_wr_hit', (string) $suji_data['hit'] );
	}

	if ( $suji_map[ $bo ]['term'] ) {
		wp_set_object_terms( $suji_post_id, $suji_map[ $bo ]['term'], 'board_cat' );
	}

	$suji_imgs = suji_import_localize_images( $suji_post_id );

	return sprintf( 'new: %s%s', $suji_data['title'], $suji_imgs ? " (사진 {$suji_imgs}장)" : '' );
}
