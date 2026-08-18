<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$suji_term = get_queried_object();
?>

<main id="primary" class="site-main board-archive">
	<header class="page-header">
		<h1 class="page-title"><?php echo esc_html( $suji_term->name ); ?></h1>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="board-list">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content', 'board' );
			endwhile;
			?>
		</div>

		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>

	<p class="board-all-link"><a href="<?php echo esc_url( get_post_type_archive_link( 'board_post' ) ); ?>">&larr; <?php esc_html_e( '전체 게시판 보기', 'suji' ); ?></a></p>
</main>

<?php
get_footer();
