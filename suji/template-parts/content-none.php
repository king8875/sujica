<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'suji' ); ?></h1>
	</header>

	<div class="page-content">
		<?php if ( is_search() ) : ?>
			<p><?php esc_html_e( 'Sorry, nothing matched your search terms.', 'suji' ); ?></p>
			<?php get_search_form(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'It looks like nothing was found here.', 'suji' ); ?></p>
		<?php endif; ?>
	</div>
</section>
