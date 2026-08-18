<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main board-overview">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( '전체 게시판', 'suji' ); ?></h1>
	</header>

	<?php get_template_part( 'template-parts/board-grid' ); ?>
</main>

<?php
get_footer();
