<?php
/**
 * 게시판별 권한.
 *
 * '게시판 관리자' 역할을 두고, 사용자마다 관리할 게시판을 체크박스로 고른다.
 * 체크한 게시판의 글만 목록에 보이고 쓰기·수정·삭제가 되며, 나머지 게시판은
 * 관리자 메뉴에서 아예 사라진다. 최고관리자(manage_options)는 제한을 받지 않는다.
 *
 * 워드프레스 기본 글 권한(edit_posts 등)을 그대로 쓰고 map_meta_cap 에서
 * 게시판 단위로 걸러낸다. 글 타입마다 권한 세트를 따로 만드는 방법도 있지만,
 * 그러면 체크박스를 켤 때마다 역할을 다시 만들어야 한다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SUJI_BOARD_ROLE   = 'suji_board_manager';
const SUJI_BOARD_ACCESS = 'suji_board_access';
const SUJI_ROLE_VERSION = 2;

/**
 * 역할을 만들거나 권한을 갱신한다. 버전이 오를 때만 다시 쓴다.
 */
function suji_ensure_board_role() {
	if ( (int) get_option( 'suji_board_role_version' ) === SUJI_ROLE_VERSION ) {
		return;
	}

	$suji_caps = array(
		'read'                   => true,
		'upload_files'           => true,
		'edit_posts'             => true,
		'edit_others_posts'      => true,
		'edit_published_posts'   => true,
		'publish_posts'          => true,
		'delete_posts'           => true,
		'delete_others_posts'    => true,
		'delete_published_posts' => true,
	);

	remove_role( SUJI_BOARD_ROLE );
	add_role( SUJI_BOARD_ROLE, __( '게시판 관리자', 'suji' ), $suji_caps );

	update_option( 'suji_board_role_version', SUJI_ROLE_VERSION, false );
}
add_action( 'init', 'suji_ensure_board_role', 1 );

/**
 * 이 사용자가 관리할 수 있는 게시판(글 타입) 목록.
 * 최고관리자는 전부.
 */
function suji_user_boards( $user_id = 0 ) {
	$user_id = $user_id ? (int) $user_id : get_current_user_id();
	if ( ! $user_id ) {
		return array();
	}

	if ( user_can( $user_id, 'manage_options' ) ) {
		return suji_board_post_types();
	}

	$suji_saved = get_user_meta( $user_id, SUJI_BOARD_ACCESS, true );
	if ( ! is_array( $suji_saved ) ) {
		return array();
	}

	return array_values( array_intersect( suji_board_post_types(), $suji_saved ) );
}

function suji_user_can_board( $post_type, $user_id = 0 ) {
	return in_array( $post_type, suji_user_boards( $user_id ), true );
}

/* ------------------------------------------------------------------ *
 * 사용자 화면의 체크박스
 * ------------------------------------------------------------------ */

function suji_render_board_access_field( $user ) {
	if ( ! current_user_can( 'promote_users' ) ) {
		return;
	}

	$suji_mine = (array) get_user_meta( $user->ID, SUJI_BOARD_ACCESS, true );
	$suji_all  = user_can( $user->ID, 'manage_options' );
	?>
	<h2><?php esc_html_e( '관리할 게시판', 'suji' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th scope="row"><?php esc_html_e( '게시판', 'suji' ); ?></th>
			<td>
				<?php if ( $suji_all ) : ?>
					<p class="description"><?php esc_html_e( '최고관리자는 모든 게시판을 관리합니다.', 'suji' ); ?></p>
				<?php else : ?>
					<fieldset>
						<legend class="screen-reader-text"><?php esc_html_e( '관리할 게시판', 'suji' ); ?></legend>
						<?php foreach ( suji_boards() as $suji_type => $suji_board ) : ?>
							<label style="display:block;margin-bottom:.35em">
								<input type="checkbox" name="<?php echo esc_attr( SUJI_BOARD_ACCESS ); ?>[]"
								       value="<?php echo esc_attr( $suji_type ); ?>"
									<?php checked( in_array( $suji_type, $suji_mine, true ) ); ?>>
								<?php echo esc_html( $suji_board['name'] ); ?>
							</label>
						<?php endforeach; ?>
					</fieldset>
					<p class="description">
						<?php esc_html_e( '체크한 게시판만 관리자 화면에 보이고 글을 쓸 수 있습니다. 역할을 ‘게시판 관리자’로 두세요.', 'suji' ); ?>
					</p>
				<?php endif; ?>
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'suji_render_board_access_field' );
add_action( 'edit_user_profile', 'suji_render_board_access_field' );

function suji_save_board_access_field( $user_id ) {
	if ( ! current_user_can( 'promote_users' ) ) {
		return;
	}

	$suji_raw = isset( $_POST[ SUJI_BOARD_ACCESS ] ) ? (array) wp_unslash( $_POST[ SUJI_BOARD_ACCESS ] ) : array();
	$suji_ok  = array_values( array_intersect( suji_board_post_types(), array_map( 'sanitize_key', $suji_raw ) ) );

	update_user_meta( $user_id, SUJI_BOARD_ACCESS, $suji_ok );
}
add_action( 'personal_options_update', 'suji_save_board_access_field' );
add_action( 'edit_user_profile_update', 'suji_save_board_access_field' );

/* ------------------------------------------------------------------ *
 * 권한 판정
 * ------------------------------------------------------------------ */

/**
 * 글 단위 권한을 게시판 기준으로 다시 판정한다.
 *
 * 게시판 관리자는 edit_others_posts 를 갖고 있어 그대로 두면 모든 글을 손댈 수
 * 있다. 여기서 자신에게 허용된 게시판이 아니면 막는다. 글·페이지 같은 다른
 * 글 타입도 함께 막는다.
 */
function suji_map_board_caps( $caps, $cap, $user_id, $args ) {
	$suji_watch = array( 'edit_post', 'delete_post', 'publish_post', 'edit_post_meta', 'delete_post_meta', 'add_post_meta' );

	if ( ! in_array( $cap, $suji_watch, true ) ) {
		return $caps;
	}
	if ( user_can( $user_id, 'manage_options' ) ) {
		return $caps;
	}
	$suji_mine = suji_user_boards( $user_id );
	if ( ! $suji_mine ) {
		return $caps;   // 게시판 관리자가 아니면 기본 판정에 맡긴다
	}

	$suji_post_id = isset( $args[0] ) ? (int) $args[0] : 0;
	$suji_type    = $suji_post_id ? get_post_type( $suji_post_id ) : '';

	if ( ! $suji_type ) {
		return $caps;
	}

	if ( ! in_array( $suji_type, $suji_mine, true ) ) {
		return array( 'do_not_allow' );
	}

	return $caps;
}
add_filter( 'map_meta_cap', 'suji_map_board_caps', 10, 4 );

/**
 * 허용되지 않은 게시판의 목록 · 글쓰기 화면은 아예 열리지 않게 한다.
 */
function suji_block_other_board_screens() {
	if ( ! is_admin() || wp_doing_ajax() || current_user_can( 'manage_options' ) ) {
		return;
	}

	$suji_mine = suji_user_boards();
	if ( ! $suji_mine ) {
		return;
	}

	$suji_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';

	// 글 수정 화면은 글 타입이 주소에 없다
	if ( ! $suji_type && ! empty( $_GET['post'] ) ) {
		$suji_type = (string) get_post_type( (int) $_GET['post'] );
	}

	// 첨부파일은 미디어 화면에서 다루므로 막지 않는다
	if ( ! $suji_type || 'attachment' === $suji_type || in_array( $suji_type, $suji_mine, true ) ) {
		return;
	}

	// 게시판이 아닌 글 타입(글·페이지)도 이 역할에는 열지 않는다
	wp_die(
		esc_html__( '이 게시판을 관리할 권한이 없습니다.', 'suji' ),
		esc_html__( '권한 없음', 'suji' ),
		array( 'response' => 403, 'back_link' => true )
	);
}
add_action( 'admin_init', 'suji_block_other_board_screens' );

/**
 * 관리자 메뉴에서 허용되지 않은 게시판을 감춘다.
 */
function suji_hide_other_board_menus() {
	if ( current_user_can( 'manage_options' ) ) {
		return;
	}

	$suji_mine = suji_user_boards();
	if ( ! $suji_mine ) {
		return;
	}

	foreach ( suji_board_post_types() as $suji_type ) {
		if ( ! in_array( $suji_type, $suji_mine, true ) ) {
			remove_menu_page( 'edit.php?post_type=' . $suji_type );
		}
	}

	// 이 역할에는 기본 '글'도 필요 없다
	remove_menu_page( 'edit.php' );
	remove_menu_page( 'edit-comments.php' );
}
add_action( 'admin_menu', 'suji_hide_other_board_menus', 999 );

/**
 * 목록 화면에서도 남의 게시판 글이 섞이지 않게 한다.
 */
function suji_restrict_admin_board_list( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() || current_user_can( 'manage_options' ) ) {
		return;
	}

	$suji_mine = suji_user_boards();
	if ( ! $suji_mine ) {
		return;
	}

	$suji_type = $query->get( 'post_type' );
	if ( ! $suji_type || 'any' === $suji_type ) {
		$query->set( 'post_type', $suji_mine );
	}
}
add_action( 'pre_get_posts', 'suji_restrict_admin_board_list' );

/* ------------------------------------------------------------------ *
 * 일반 회원
 * ------------------------------------------------------------------ */

/**
 * 글을 쓸 수 없는 회원에게는 상단 워드프레스 바를 보이지 않는다.
 */
function suji_hide_admin_bar_for_members( $show ) {
	return current_user_can( 'edit_posts' ) ? $show : false;
}
add_filter( 'show_admin_bar', 'suji_hide_admin_bar_for_members' );

/**
 * 일반 회원이 wp-admin 으로 들어오면 홈으로 돌려보낸다.
 * ajax 와 프로필 화면은 건드리지 않는다.
 */
function suji_keep_members_out_of_admin() {
	if ( ! is_admin() || wp_doing_ajax() || ! is_user_logged_in() ) {
		return;
	}
	if ( current_user_can( 'edit_posts' ) ) {
		return;
	}

	global $pagenow;
	if ( 'profile.php' === $pagenow ) {
		return;
	}

	wp_safe_redirect( home_url( '/' ) );
	exit;
}
add_action( 'admin_init', 'suji_keep_members_out_of_admin' );
