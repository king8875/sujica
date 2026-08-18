<?php
/**
 * 게시판 글 상세.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main">
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content-single', 'board' );
	endwhile;
	?>
</main>

<?php
get_footer();
