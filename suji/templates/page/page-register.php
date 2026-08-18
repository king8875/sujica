<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

global $suji_register_errors;
$suji_can_register = get_option( 'users_can_register' );

$suji_old_login = isset( $_POST['user_login'] ) ? esc_attr( wp_unslash( $_POST['user_login'] ) ) : '';
$suji_old_email = isset( $_POST['user_email'] ) ? esc_attr( wp_unslash( $_POST['user_email'] ) ) : '';
$suji_old_name  = isset( $_POST['display_name'] ) ? esc_attr( wp_unslash( $_POST['display_name'] ) ) : '';
?>

<main id="primary" class="site-main auth-page">
	<div class="auth-card">
		<header class="auth-header">
			<h1 class="auth-title"><?php esc_html_e( '회원가입', 'suji' ); ?></h1>
			<p class="auth-subtitle"><?php esc_html_e( '수지성당 누리집 회원으로 가입해주세요.', 'suji' ); ?></p>
		</header>

		<?php if ( ! $suji_can_register ) : ?>
			<p class="auth-message auth-message-error"><?php esc_html_e( '현재 회원가입을 받고 있지 않습니다.', 'suji' ); ?></p>
		<?php else : ?>

			<?php if ( is_wp_error( $suji_register_errors ) && $suji_register_errors->has_errors() ) : ?>
				<div class="auth-message auth-message-error">
					<?php foreach ( $suji_register_errors->get_error_messages() as $suji_message ) : ?>
						<p><?php echo esc_html( $suji_message ); ?></p>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<form class="auth-form" method="post">
				<?php wp_nonce_field( 'suji_register', 'suji_register_nonce' ); ?>

				<div class="auth-field">
					<label for="user_login"><?php esc_html_e( '아이디', 'suji' ); ?></label>
					<input type="text" name="user_login" id="user_login" value="<?php echo $suji_old_login; ?>" autocomplete="username" required>
				</div>

				<div class="auth-field">
					<label for="display_name"><?php esc_html_e( '이름 (세례명)', 'suji' ); ?></label>
					<input type="text" name="display_name" id="display_name" value="<?php echo $suji_old_name; ?>" placeholder="<?php esc_attr_e( '예: 홍길동 (토마스)', 'suji' ); ?>">
				</div>

				<div class="auth-field">
					<label for="user_email"><?php esc_html_e( '이메일', 'suji' ); ?></label>
					<input type="email" name="user_email" id="user_email" value="<?php echo $suji_old_email; ?>" autocomplete="email" required>
				</div>

				<div class="auth-field">
					<label for="user_pass"><?php esc_html_e( '비밀번호', 'suji' ); ?></label>
					<input type="password" name="user_pass" id="user_pass" autocomplete="new-password" required>
					<span class="auth-hint"><?php esc_html_e( '8자 이상 입력해주세요.', 'suji' ); ?></span>
				</div>

				<div class="auth-field">
					<label for="user_pass2"><?php esc_html_e( '비밀번호 확인', 'suji' ); ?></label>
					<input type="password" name="user_pass2" id="user_pass2" autocomplete="new-password" required>
				</div>

				<button type="submit" class="auth-submit"><?php esc_html_e( '가입하기', 'suji' ); ?></button>
			</form>
		<?php endif; ?>

		<p class="auth-alt">
			<?php esc_html_e( '이미 계정이 있으신가요?', 'suji' ); ?>
			<a href="<?php echo esc_url( suji_login_page_url() ); ?>"><?php esc_html_e( '로그인', 'suji' ); ?></a>
		</p>
	</div>
</main>

<?php
get_footer();
