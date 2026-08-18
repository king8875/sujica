<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function suji_admin_menu() {
	add_theme_page(
		__( 'Suji Theme Options', 'suji' ),
		__( 'Suji Options', 'suji' ),
		'manage_options',
		'suji-theme-options',
		'suji_render_theme_options_page'
	);
}
add_action( 'admin_menu', 'suji_admin_menu' );

function suji_render_theme_options_page() {
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Suji Theme Options', 'suji' ); ?></h1>
		<p><?php esc_html_e( 'Theme settings go here.', 'suji' ); ?></p>
	</div>
	<?php
}
