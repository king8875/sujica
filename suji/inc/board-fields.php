<?php
/**
 * 게시판별 입력칸.
 *
 * 사무실에서 글을 쓸 때 본문에 HTML 을 넣지 않아도 되도록, 게시판마다 필요한
 * 칸만 따로 둔다. 서식·정렬·목록 썸네일은 템플릿이 맡는다.
 *
 * 필드는 코드로 등록한다(lof_add_local_field_group). 사무실이 고칠 구조가
 * 아니라 테마가 정하는 구조이고, 관리자에서 JSON 동기화를 누르지 않아도
 * 바로 나타나야 하기 때문이다.
 *
 * 이관된 글 3,000여 건은 이 칸들이 비어 있다. 템플릿은 칸이 채워져 있으면
 * 그것을 쓰고, 비어 있으면 지금처럼 본문을 그린다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function suji_field_file_rows( $prefix ) {
	return array(
		array(
			'key'   => 'field_' . $prefix . '_file',
			'label' => '파일',
			'name'  => 'file',
			'type'  => 'file',
			'return_format' => 'array',
			'wrapper' => array( 'width' => '45' ),
		),
		array(
			'key'   => 'field_' . $prefix . '_label',
			'label' => '표시할 이름',
			'name'  => 'label',
			'type'  => 'text',
			'instructions' => '비워두면 파일 이름을 그대로 씁니다.',
			'wrapper' => array( 'width' => '55' ),
		),
	);
}

function suji_register_board_fields() {
	if ( ! function_exists( 'lof_add_local_field_group' ) ) {
		return;
	}

	// ------------------------------ 포토앨범 ------------------------------
	lof_add_local_field_group( array(
		'key'      => 'group_suji_gallery',
		'title'    => '사진',
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'suji_gallery' ) ) ),
		'fields'   => array(
			array(
				'key'          => 'field_suji_gallery_photos',
				'label'        => '사진',
				'name'         => 'gallery_photos',
				'type'         => 'gallery',
				'instructions' => '여러 장을 한 번에 올릴 수 있습니다. 첫 장이 목록의 대표 사진이 됩니다.',
				'return_format' => 'array',
			),
		),
	) );

	// ---------------------------- 문서 자료실 ----------------------------
	lof_add_local_field_group( array(
		'key'      => 'group_suji_docs',
		'title'    => '첨부 파일',
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'suji_docs' ) ) ),
		'fields'   => array(
			array(
				'key'          => 'field_suji_docs_files',
				'label'        => '첨부 파일',
				'name'         => 'board_files',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => '파일 추가',
				'instructions' => '내려받기 목록이 글 아래에 자동으로 만들어집니다.',
				'sub_fields'   => suji_field_file_rows( 'docs' ),
			),
		),
	) );

	// ----------------------------- 본당 주보 -----------------------------
	lof_add_local_field_group( array(
		'key'      => 'group_suji_bulletin',
		'title'    => '주보',
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'suji_bulletin' ) ) ),
		'fields'   => array(
			array(
				'key'          => 'field_suji_bulletin_url',
				'label'        => '주보 보기 링크',
				'name'         => 'bulletin_url',
				'type'         => 'url',
				'instructions' => '교구 주보 e-book 주소를 붙여넣으세요.',
			),
			array(
				'key'          => 'field_suji_bulletin_image',
				'label'        => '주보 이미지',
				'name'         => 'bulletin_image',
				'type'         => 'image',
				'instructions' => '주보를 이미지로 올릴 경우에만 씁니다. 목록의 대표 사진이 됩니다.',
				'return_format' => 'array',
			),
		),
	) );

	// ----------------------------- 공지사항 -----------------------------
	lof_add_local_field_group( array(
		'key'      => 'group_suji_notice',
		'title'    => '공지 설정',
		'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'suji_notice' ) ) ),
		'fields'   => array(
			array(
				'key'          => 'field_suji_notice_pinned',
				'label'        => '목록 맨 위에 고정',
				'name'         => 'notice_pinned',
				'type'         => 'true_false',
				'ui'           => 1,
				'instructions' => '켜면 공지사항 목록 첫 줄에 계속 보입니다.',
			),
			array(
				'key'          => 'field_suji_notice_files',
				'label'        => '첨부 파일',
				'name'         => 'board_files',
				'type'         => 'repeater',
				'layout'       => 'table',
				'button_label' => '파일 추가',
				'sub_fields'   => suji_field_file_rows( 'notice' ),
			),
		),
	) );
}
/*
 * lofields 는 plugins_loaded 에서 lof/init 을 발동한다. 테마 functions.php 는
 * 그보다 나중에 읽히므로 lof/init 에만 걸면 이미 지나간 훅이 되어 필드가
 * 나타나지 않는다. 실제로 그랬다. init 에서 직접 등록하고, 진짜 ACF 를 쓰는
 * 경우를 위해 두 훅도 함께 남겨 둔다(같은 key 로 다시 등록해도 덮어쓰기다).
 */
add_action( 'init', 'suji_register_board_fields', 5 );
add_action( 'lof/init', 'suji_register_board_fields' );
add_action( 'acf/init', 'suji_register_board_fields' );

/**
 * 사진 칸의 첫 장을 대표 이미지로 맞춰 둔다.
 *
 * 목록 격자가 대표 이미지를 보기 때문에, 사람이 따로 지정하지 않으면 회색
 * 상자가 나온다. 이관된 글에서 실제로 그랬다.
 */
function suji_sync_board_thumbnail( $post_id ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	if ( ! function_exists( 'get_field' ) ) {
		return;
	}

	$suji_type = get_post_type( $post_id );
	$suji_first = 0;

	if ( 'suji_gallery' === $suji_type ) {
		$suji_photos = get_field( 'gallery_photos', $post_id );
		if ( is_array( $suji_photos ) && $suji_photos ) {
			$suji_one   = reset( $suji_photos );
			$suji_first = is_array( $suji_one ) ? (int) ( $suji_one['ID'] ?? 0 ) : (int) $suji_one;
		}
	} elseif ( 'suji_bulletin' === $suji_type ) {
		$suji_image = get_field( 'bulletin_image', $post_id );
		if ( $suji_image ) {
			$suji_first = is_array( $suji_image ) ? (int) ( $suji_image['ID'] ?? 0 ) : (int) $suji_image;
		}
	}

	if ( $suji_first ) {
		set_post_thumbnail( $post_id, $suji_first );
	}
}
add_action( 'save_post', 'suji_sync_board_thumbnail', 20 );

/**
 * 올리는 사진이 너무 크면 가로 1600px 로 줄인다.
 *
 * 원본을 그대로 두면 한 장에 3~5MB 씩 쌓인다. 웹에서 보는 데는 1600px 이면
 * 넉넉하고, 워드프레스가 만드는 썸네일까지 함께 줄어든다.
 */
function suji_shrink_uploaded_image( $upload ) {
	if ( empty( $upload['file'] ) || empty( $upload['type'] ) ) {
		return $upload;
	}
	if ( ! preg_match( '#^image/(jpe?g|png|webp)$#i', $upload['type'] ) ) {
		return $upload;
	}

	$suji_size = @getimagesize( $upload['file'] );
	if ( ! $suji_size || $suji_size[0] <= SUJI_MAX_IMAGE_WIDTH ) {
		return $upload;
	}

	$suji_editor = wp_get_image_editor( $upload['file'] );
	if ( is_wp_error( $suji_editor ) ) {
		return $upload;
	}

	$suji_editor->resize( SUJI_MAX_IMAGE_WIDTH, null, false );
	$suji_editor->set_quality( 86 );
	$suji_editor->save( $upload['file'] );

	return $upload;
}
add_filter( 'wp_handle_upload', 'suji_shrink_uploaded_image' );

/**
 * 공지사항 목록에서 고정된 글을 맨 위로.
 *
 * meta_key 를 set() 하면 그 메타가 없는 글이 목록에서 빠져 버린다(내부적으로
 * INNER JOIN 이 된다). 공지 677건 중 대부분은 이 값이 없으므로, LEFT JOIN 을
 * 직접 붙여 정렬에만 쓴다.
 */
function suji_notice_pinned_first( $clauses, $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return $clauses;
	}
	if ( ! $query->is_post_type_archive( 'suji_notice' ) ) {
		return $clauses;
	}

	global $wpdb;

	$clauses['join'] .= " LEFT JOIN {$wpdb->postmeta} AS suji_pin"
		. " ON suji_pin.post_id = {$wpdb->posts}.ID"
		. " AND suji_pin.meta_key = 'notice_pinned' ";

	$clauses['orderby'] = " ( suji_pin.meta_value = '1' ) DESC, {$wpdb->posts}.post_date DESC ";

	return $clauses;
}
add_filter( 'posts_clauses', 'suji_notice_pinned_first', 10, 2 );
