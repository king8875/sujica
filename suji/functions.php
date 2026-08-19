<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SUJI_VERSION', '1.0.0' );
define( 'SUJI_DIR', get_template_directory() );
define( 'SUJI_URI', get_template_directory_uri() );

// 올리는 사진의 가로 최대 폭 — 이보다 크면 줄여서 저장한다
define( 'SUJI_MAX_IMAGE_WIDTH', 1600 );

require SUJI_DIR . '/inc/setup.php';
require SUJI_DIR . '/inc/template-tags.php';
require SUJI_DIR . '/inc/boards.php';
require SUJI_DIR . '/inc/board-cpt.php';
require SUJI_DIR . '/inc/board-fields.php';
require SUJI_DIR . '/inc/template-loader.php';
require SUJI_DIR . '/inc/clergy.php';
require SUJI_DIR . '/inc/auth.php';
require SUJI_DIR . '/inc/board-roles.php';
// Home banner ACF field group is managed directly in wp-admin (Custom Fields), not in code.
require SUJI_DIR . '/inc/enqueue/enqueue.php';
require SUJI_DIR . '/inc/customizer/customizer.php';

if ( is_admin() ) {
	require SUJI_DIR . '/inc/admin/admin.php';
	require SUJI_DIR . '/inc/admin/seed-clergy.php';
	require SUJI_DIR . '/inc/admin/board-migrate.php';
	require SUJI_DIR . '/inc/admin/importer.php';
	require SUJI_DIR . '/inc/admin/importer-page.php';
}
