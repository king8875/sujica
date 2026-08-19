<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front-end login / registration pages replacing wp-login.php for visitors.
 * Escape hatch: append ?loginpage=wp to reach the original wp-login.php.
 */

define( 'SUJI_LOGIN_SLUG', 'login' );
define( 'SUJI_REGISTER_SLUG', 'register' );

function suji_login_page_url() {
	$suji_page = get_page_by_path( SUJI_LOGIN_SLUG );
	return $suji_page ? get_permalink( $suji_page ) : wp_login_url();
}

function suji_register_page_url() {
	$suji_page = get_page_by_path( SUJI_REGISTER_SLUG );
	return $suji_page ? get_permalink( $suji_page ) : wp_registration_url();
}

/**
 * Point every wp_login_url() / wp_registration_url() call at the custom pages,
 * so the header links and core notices follow automatically.
 */
function suji_filter_login_url( $login_url, $redirect = '' ) {
	$suji_page = get_page_by_path( SUJI_LOGIN_SLUG );
	if ( ! $suji_page ) {
		return $login_url;
	}

	$suji_url = get_permalink( $suji_page );
	if ( $redirect ) {
		$suji_url = add_query_arg( 'redirect_to', urlencode( $redirect ), $suji_url );
	}

	return $suji_url;
}
add_filter( 'login_url', 'suji_filter_login_url', 10, 2 );

function suji_filter_register_url( $register_url ) {
	$suji_page = get_page_by_path( SUJI_REGISTER_SLUG );
	return $suji_page ? get_permalink( $suji_page ) : $register_url;
}
add_filter( 'register_url', 'suji_filter_register_url' );

/**
 * Send visitors hitting wp-login.php to the themed pages instead.
 * Logout, password reset and all POSTs keep using core so nothing breaks.
 */
function suji_redirect_wp_login() {
	if ( isset( $_GET['loginpage'] ) && 'wp' === $_GET['loginpage'] ) {
		return;
	}

	if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
		return;
	}

	$suji_action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';

	if ( in_array( $suji_action, array( 'logout', 'lostpassword', 'retrievepassword', 'rp', 'resetpass', 'postpass', 'confirmaction' ), true ) ) {
		return;
	}

	if ( 'register' === $suji_action ) {
		wp_safe_redirect( suji_register_page_url() );
		exit;
	}

	if ( '' === $suji_action || 'login' === $suji_action ) {
		$suji_url = suji_login_page_url();
		if ( ! empty( $_GET['redirect_to'] ) ) {
			$suji_url = add_query_arg( 'redirect_to', urlencode( wp_unslash( $_GET['redirect_to'] ) ), $suji_url );
		}
		wp_safe_redirect( $suji_url );
		exit;
	}
}
add_action( 'login_init', 'suji_redirect_wp_login' );

/**
 * Bounce failed / empty logins back to the themed page with an error flag,
 * but only when the attempt came from the front end (never from wp-admin).
 */
function suji_login_came_from_front_end() {
	$suji_referrer = wp_get_referer();
	return $suji_referrer && false === strpos( $suji_referrer, 'wp-login.php' ) && false === strpos( $suji_referrer, 'wp-admin' );
}

function suji_login_failed_redirect() {
	if ( ! suji_login_came_from_front_end() ) {
		return;
	}
	wp_safe_redirect( add_query_arg( 'login', 'failed', suji_login_page_url() ) );
	exit;
}
add_action( 'wp_login_failed', 'suji_login_failed_redirect' );

function suji_login_empty_fields( $user, $username, $password ) {
	if ( ( '' === $username || '' === $password ) && suji_login_came_from_front_end() ) {
		wp_safe_redirect( add_query_arg( 'login', 'empty', suji_login_page_url() ) );
		exit;
	}
	return $user;
}
add_filter( 'authenticate', 'suji_login_empty_fields', 30, 3 );

/**
 * Handle the themed registration form. Errors are collected into a global the
 * template renders; a successful signup redirects to the login page.
 */
function suji_handle_registration() {
	if ( empty( $_POST['suji_register_nonce'] ) ) {
		return;
	}

	global $suji_register_errors;
	$suji_register_errors = new WP_Error();

	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['suji_register_nonce'] ) ), 'suji_register' ) ) {
		$suji_register_errors->add( 'nonce', __( '잘못된 요청입니다. 다시 시도해주세요.', 'suji' ) );
		return;
	}

	if ( ! get_option( 'users_can_register' ) ) {
		$suji_register_errors->add( 'closed', __( '현재 회원가입을 받고 있지 않습니다.', 'suji' ) );
		return;
	}

	$suji_login = isset( $_POST['user_login'] ) ? sanitize_user( wp_unslash( $_POST['user_login'] ) ) : '';
	$suji_email = isset( $_POST['user_email'] ) ? sanitize_email( wp_unslash( $_POST['user_email'] ) ) : '';
	$suji_name  = isset( $_POST['display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['display_name'] ) ) : '';
	$suji_pass  = isset( $_POST['user_pass'] ) ? (string) wp_unslash( $_POST['user_pass'] ) : '';
	$suji_pass2 = isset( $_POST['user_pass2'] ) ? (string) wp_unslash( $_POST['user_pass2'] ) : '';

	if ( '' === $suji_login ) {
		$suji_register_errors->add( 'user_login', __( '아이디를 입력해주세요.', 'suji' ) );
	} elseif ( ! validate_username( $suji_login ) ) {
		$suji_register_errors->add( 'user_login', __( '사용할 수 없는 아이디입니다.', 'suji' ) );
	} elseif ( username_exists( $suji_login ) ) {
		$suji_register_errors->add( 'user_login', __( '이미 사용 중인 아이디입니다.', 'suji' ) );
	}

	if ( '' === $suji_email ) {
		$suji_register_errors->add( 'user_email', __( '이메일을 입력해주세요.', 'suji' ) );
	} elseif ( ! is_email( $suji_email ) ) {
		$suji_register_errors->add( 'user_email', __( '올바른 이메일 형식이 아닙니다.', 'suji' ) );
	} elseif ( email_exists( $suji_email ) ) {
		$suji_register_errors->add( 'user_email', __( '이미 가입된 이메일입니다.', 'suji' ) );
	}

	if ( strlen( $suji_pass ) < 8 ) {
		$suji_register_errors->add( 'user_pass', __( '비밀번호는 8자 이상이어야 합니다.', 'suji' ) );
	} elseif ( $suji_pass !== $suji_pass2 ) {
		$suji_register_errors->add( 'user_pass2', __( '비밀번호가 서로 일치하지 않습니다.', 'suji' ) );
	}

	if ( $suji_register_errors->has_errors() ) {
		return;
	}

	$suji_user_id = wp_create_user( $suji_login, $suji_pass, $suji_email );

	if ( is_wp_error( $suji_user_id ) ) {
		$suji_register_errors->add( 'create', $suji_user_id->get_error_message() );
		return;
	}

	/*
	 * 역할을 코드에서 못 박는다. wp_create_user 는 '새 사용자 기본 역할' 설정을
	 * 따르는데, 그 값이 글을 쓸 수 있는 역할로 바뀌어 있으면 홈페이지에서
	 * 가입한 사람이 관리 권한을 얻는다.
	 */
	$suji_user = new WP_User( $suji_user_id );
	$suji_user->set_role( 'subscriber' );

	if ( $suji_name ) {
		wp_update_user( array(
			'ID'           => $suji_user_id,
			'display_name' => $suji_name,
			'nickname'     => $suji_name,
		) );
	}

	wp_new_user_notification( $suji_user_id, null, 'both' );

	wp_safe_redirect( add_query_arg( 'registered', '1', suji_login_page_url() ) );
	exit;
}
add_action( 'template_redirect', 'suji_handle_registration' );

/**
 * Logged-in visitors have no use for these pages.
 */
function suji_redirect_logged_in_from_auth_pages() {
	if ( ! is_user_logged_in() || ! is_page() ) {
		return;
	}

	if ( is_page( array( SUJI_LOGIN_SLUG, SUJI_REGISTER_SLUG ) ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
}
add_action( 'template_redirect', 'suji_redirect_logged_in_from_auth_pages' );
