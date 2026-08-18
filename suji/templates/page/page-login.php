<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$suji_notice     = isset( $_GET['login'] ) ? sanitize_key( wp_unslash( $_GET['login'] ) ) : '';
$suji_registered = isset( $_GET['registered'] );
$suji_redirect   = isset( $_GET['redirect_to'] ) ? esc_url_raw( wp_unslash( $_GET['redirect_to'] ) ) : home_url( '/' );
?>

<main id="primary" class="site-main auth-page">
	<div class="auth-card">
		<header class="auth-header">
			<h1 class="auth-title"><?php esc_html_e( '로그인', 'suji' ); ?></h1>
			<p class="auth-subtitle"><?php esc_html_e( '수지성당 누리집에 오신 것을 환영합니다.', 'suji' ); ?></p>
		</header>

		<?php if ( $suji_registered ) : ?>
			<p class="auth-message auth-message-success"><?php esc_html_e( '회원가입이 완료되었습니다. 로그인해주세요.', 'suji' ); ?></p>
		<?php endif; ?>

		<?php if ( 'failed' === $suji_notice ) : ?>
			<p class="auth-message auth-message-error"><?php esc_html_e( '아이디 또는 비밀번호가 올바르지 않습니다.', 'suji' ); ?></p>
		<?php elseif ( 'empty' === $suji_notice ) : ?>
			<p class="auth-message auth-message-error"><?php esc_html_e( '아이디와 비밀번호를 모두 입력해주세요.', 'suji' ); ?></p>
		<?php endif; ?>

		<form class="auth-form" method="post" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>">
			<div class="auth-field">
				<label for="user_login"><?php esc_html_e( '아이디', 'suji' ); ?></label>
				<input type="text" name="log" id="user_login" autocomplete="username" required>
			</div>

			<div class="auth-field">
				<label for="user_pass"><?php esc_html_e( '비밀번호', 'suji' ); ?></label>
				<input type="password" name="pwd" id="user_pass" autocomplete="current-password" required>
			</div>

			<div class="auth-row">
				<label class="auth-checkbox">
					<input type="checkbox" name="rememberme" value="forever">
					<span><?php esc_html_e( '로그인 상태 유지', 'suji' ); ?></span>
				</label>
				<a class="auth-inline-link" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( '비밀번호 찾기', 'suji' ); ?></a>
			</div>

			<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $suji_redirect ); ?>">
			<button type="submit" class="auth-submit"><?php esc_html_e( '로그인', 'suji' ); ?></button>
		</form>

		<?php if ( get_option( 'users_can_register' ) ) : ?>
			<p class="auth-alt">
				<?php esc_html_e( '아직 회원이 아니신가요?', 'suji' ); ?>
				<a href="<?php echo esc_url( suji_register_page_url() ); ?>"><?php esc_html_e( '회원가입', 'suji' ); ?></a>
			</p>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
