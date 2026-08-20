<?php
/**
 * 프론트엔드 글쓰기 · 수정.
 *
 * 게시판 관리자는 워드프레스 관리 화면에 들어가지 않는다. 게시판 목록에 있는
 * ‘글쓰기’ 버튼으로 프론트에서 쓰고 고친다.
 *
 * 주소 규칙
 *   /notice/?suji_form=new              새 글
 *   /notice/?suji_form=edit&post=123    수정
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 이 사람이 이 게시판에 글을 쓸 수 있는가.
 */
function suji_can_write_board( $post_type ) {
	if ( ! is_user_logged_in() ) {
		return false;
	}
	if ( current_user_can( 'manage_options' ) ) {
		return true;
	}
	return current_user_can( 'edit_posts' ) && suji_user_can_board( $post_type );
}

function suji_board_form_url( $post_type, $post_id = 0 ) {
	$suji_base = suji_board_link( $post_type );
	if ( ! $suji_base ) {
		return '';
	}

	return $post_id
		? add_query_arg( array( 'suji_form' => 'edit', 'post' => (int) $post_id ), $suji_base )
		: add_query_arg( 'suji_form', 'new', $suji_base );
}

/**
 * 지금 요청이 글쓰기 · 수정 화면인지 판단하고, 맞으면 대상 정보를 돌려준다.
 */
function suji_board_form_context() {
	static $suji_ctx = false;

	if ( false !== $suji_ctx ) {
		return $suji_ctx;
	}

	$suji_ctx = null;
	$suji_mode = isset( $_GET['suji_form'] ) ? sanitize_key( wp_unslash( $_GET['suji_form'] ) ) : '';

	if ( ! in_array( $suji_mode, array( 'new', 'edit' ), true ) ) {
		return $suji_ctx;
	}

	// 어느 게시판인지 — 목록 화면 또는 수정할 글에서 알아낸다
	$suji_post = null;
	$suji_type = '';

	if ( 'edit' === $suji_mode ) {
		$suji_post = get_post( isset( $_GET['post'] ) ? (int) $_GET['post'] : 0 );
		if ( ! $suji_post ) {
			return $suji_ctx;
		}
		$suji_type = $suji_post->post_type;
	} else {
		$suji_obj  = get_queried_object();
		$suji_type = ( $suji_obj && isset( $suji_obj->name ) ) ? $suji_obj->name : '';
	}

	if ( ! in_array( $suji_type, suji_board_post_types(), true ) ) {
		return $suji_ctx;
	}

	$suji_ctx = array(
		'mode' => $suji_mode,
		'type' => $suji_type,
		'post' => $suji_post,
	);

	return $suji_ctx;
}

/**
 * 저장 · 삭제 처리. 화면을 그리기 전에 돈다.
 */
function suji_handle_board_form() {
	if ( empty( $_POST['suji_board_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['suji_board_nonce'] ) ), 'suji_board_form' ) ) {
		return;
	}

	$suji_type = isset( $_POST['suji_type'] ) ? sanitize_key( wp_unslash( $_POST['suji_type'] ) ) : '';
	$suji_id   = isset( $_POST['suji_post_id'] ) ? (int) $_POST['suji_post_id'] : 0;

	if ( ! in_array( $suji_type, suji_board_post_types(), true ) || ! suji_can_write_board( $suji_type ) ) {
		wp_die( esc_html__( '이 게시판에 글을 쓸 권한이 없습니다.', 'suji' ), '', array( 'response' => 403 ) );
	}

	if ( $suji_id ) {
		$suji_existing = get_post( $suji_id );
		if ( ! $suji_existing || $suji_existing->post_type !== $suji_type || ! current_user_can( 'edit_post', $suji_id ) ) {
			wp_die( esc_html__( '이 글을 고칠 권한이 없습니다.', 'suji' ), '', array( 'response' => 403 ) );
		}
	}

	// ------------------------------ 삭제 ------------------------------
	if ( ! empty( $_POST['suji_delete'] ) && $suji_id ) {
		if ( ! current_user_can( 'delete_post', $suji_id ) ) {
			wp_die( esc_html__( '이 글을 지울 권한이 없습니다.', 'suji' ), '', array( 'response' => 403 ) );
		}
		wp_trash_post( $suji_id );
		wp_safe_redirect( add_query_arg( 'suji_done', 'deleted', suji_board_link( $suji_type ) ) );
		exit;
	}

	// ------------------------------ 저장 ------------------------------
	global $suji_form_errors;
	$suji_form_errors = new WP_Error();

	$suji_title = isset( $_POST['suji_title'] ) ? sanitize_text_field( wp_unslash( $_POST['suji_title'] ) ) : '';
	if ( '' === $suji_title ) {
		$suji_form_errors->add( 'title', __( '제목을 입력해주세요.', 'suji' ) );
		return;
	}

	$suji_content = isset( $_POST['suji_content'] ) ? wp_kses_post( wp_unslash( $_POST['suji_content'] ) ) : '';

	$suji_args = array(
		'post_type'    => $suji_type,
		'post_status'  => 'publish',
		'post_title'   => $suji_title,
		'post_content' => $suji_content,
	);

	if ( $suji_id ) {
		$suji_args['ID'] = $suji_id;
		$suji_saved = wp_update_post( $suji_args, true );
	} else {
		$suji_args['post_author'] = get_current_user_id();
		$suji_saved = wp_insert_post( $suji_args, true );
	}

	if ( is_wp_error( $suji_saved ) ) {
		$suji_form_errors->add( 'save', $suji_saved->get_error_message() );
		return;
	}

	$suji_id = (int) $suji_saved;

	// 작성자 이름은 목록에 쓰이므로 함께 남긴다
	if ( ! get_post_meta( $suji_id, '_g5_wr_name', true ) ) {
		$suji_user = wp_get_current_user();
		update_post_meta( $suji_id, '_g5_wr_name', $suji_user->display_name );
	}

	// 고정
	update_post_meta( $suji_id, 'board_pinned', empty( $_POST['suji_pinned'] ) ? '0' : '1' );

	suji_form_save_links( $suji_id );
	suji_form_save_uploads( $suji_id, 'suji_files', 'board_files' );
	suji_form_save_photos( $suji_id );

	wp_safe_redirect( add_query_arg( 'suji_done', $suji_args['ID'] ?? 0 ? 'updated' : 'created', get_permalink( $suji_id ) ) );
	exit;
}
add_action( 'template_redirect', 'suji_handle_board_form', 5 );

/**
 * 링크 줄들을 정리해 저장한다.
 */
function suji_form_save_links( $post_id ) {
	$suji_urls   = isset( $_POST['suji_link_url'] ) ? (array) wp_unslash( $_POST['suji_link_url'] ) : array();
	$suji_labels = isset( $_POST['suji_link_label'] ) ? (array) wp_unslash( $_POST['suji_link_label'] ) : array();

	$suji_rows = array();
	foreach ( $suji_urls as $suji_i => $suji_url ) {
		$suji_url = esc_url_raw( trim( (string) $suji_url ) );
		if ( '' === $suji_url ) {
			continue;
		}
		$suji_rows[] = array(
			'url'   => $suji_url,
			'label' => sanitize_text_field( (string) ( $suji_labels[ $suji_i ] ?? '' ) ),
		);
	}

	if ( function_exists( 'update_field' ) ) {
		update_field( 'board_links', $suji_rows, $post_id );
	}
}

/**
 * 올린 파일을 미디어에 등록하고, 이미 있던 줄과 합쳐 저장한다.
 */
function suji_form_save_uploads( $post_id, $input, $field ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	// 지우기로 표시한 줄을 빼고 남긴다
	$suji_keep = isset( $_POST['suji_keep_file'] ) ? array_map( 'intval', (array) wp_unslash( $_POST['suji_keep_file'] ) ) : array();
	$suji_rows = array();

	foreach ( $suji_keep as $suji_att ) {
		if ( $suji_att && get_post( $suji_att ) ) {
			$suji_rows[] = array( 'file' => $suji_att, 'label' => '' );
		}
	}

	if ( ! empty( $_FILES[ $input ]['name'] ) && is_array( $_FILES[ $input ]['name'] ) ) {
		$suji_count = count( $_FILES[ $input ]['name'] );

		for ( $suji_i = 0; $suji_i < $suji_count; $suji_i++ ) {
			if ( empty( $_FILES[ $input ]['name'][ $suji_i ] ) ) {
				continue;
			}

			$_FILES['suji_one'] = array(
				'name'     => $_FILES[ $input ]['name'][ $suji_i ],
				'type'     => $_FILES[ $input ]['type'][ $suji_i ],
				'tmp_name' => $_FILES[ $input ]['tmp_name'][ $suji_i ],
				'error'    => $_FILES[ $input ]['error'][ $suji_i ],
				'size'     => $_FILES[ $input ]['size'][ $suji_i ],
			);

			$suji_att = media_handle_upload( 'suji_one', $post_id );
			if ( ! is_wp_error( $suji_att ) ) {
				$suji_rows[] = array( 'file' => $suji_att, 'label' => '' );
			}
		}
		unset( $_FILES['suji_one'] );
	}

	if ( function_exists( 'update_field' ) ) {
		update_field( $field, $suji_rows, $post_id );
	}
}

/**
 * 포토앨범의 사진 칸.
 */
function suji_form_save_photos( $post_id ) {
	if ( 'suji_gallery' !== get_post_type( $post_id ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$suji_ids = isset( $_POST['suji_keep_photo'] ) ? array_map( 'intval', (array) wp_unslash( $_POST['suji_keep_photo'] ) ) : array();
	$suji_ids = array_values( array_filter( $suji_ids, function ( $suji_id ) {
		return $suji_id && get_post( $suji_id );
	} ) );

	if ( ! empty( $_FILES['suji_photos']['name'] ) && is_array( $_FILES['suji_photos']['name'] ) ) {
		$suji_count = count( $_FILES['suji_photos']['name'] );

		for ( $suji_i = 0; $suji_i < $suji_count; $suji_i++ ) {
			if ( empty( $_FILES['suji_photos']['name'][ $suji_i ] ) ) {
				continue;
			}

			$_FILES['suji_photo_one'] = array(
				'name'     => $_FILES['suji_photos']['name'][ $suji_i ],
				'type'     => $_FILES['suji_photos']['type'][ $suji_i ],
				'tmp_name' => $_FILES['suji_photos']['tmp_name'][ $suji_i ],
				'error'    => $_FILES['suji_photos']['error'][ $suji_i ],
				'size'     => $_FILES['suji_photos']['size'][ $suji_i ],
			);

			$suji_att = media_handle_upload( 'suji_photo_one', $post_id );
			if ( ! is_wp_error( $suji_att ) ) {
				$suji_ids[] = $suji_att;
			}
		}
		unset( $_FILES['suji_photo_one'] );
	}

	if ( function_exists( 'update_field' ) ) {
		update_field( 'gallery_photos', $suji_ids, $post_id );
	}

	if ( $suji_ids ) {
		set_post_thumbnail( $post_id, (int) $suji_ids[0] );
	}
}

/**
 * 글쓰기 · 수정 화면을 전용 템플릿으로 보낸다.
 */
function suji_board_form_template( $template ) {
	$suji_ctx = suji_board_form_context();
	if ( ! $suji_ctx ) {
		return $template;
	}

	if ( ! suji_can_write_board( $suji_ctx['type'] ) ) {
		return $template;
	}
	if ( 'edit' === $suji_ctx['mode'] && ! current_user_can( 'edit_post', $suji_ctx['post']->ID ) ) {
		return $template;
	}

	return SUJI_DIR . '/templates/board-form.php';
}
add_filter( 'template_include', 'suji_board_form_template', 20 );
