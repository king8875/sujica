<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</div><!-- #content -->

<?php
$suji_footer_text    = get_theme_mod( 'suji_footer_text' );
$suji_footer_address = get_theme_mod( 'suji_footer_address' );
$suji_footer_phone   = get_theme_mod( 'suji_footer_phone' );
$suji_footer_email   = get_theme_mod( 'suji_footer_email' );
?>
<footer id="colophon" class="site-footer">
	<div class="footer-inner">
		<div class="footer-top">
			<div class="footer-brand">
				<p class="footer-site-name"><?php bloginfo( 'name' ); ?></p>

				<?php if ( $suji_footer_address || $suji_footer_phone || $suji_footer_email ) : ?>
					<ul class="footer-contact">
						<?php if ( $suji_footer_address ) : ?>
							<li><?php echo esc_html( $suji_footer_address ); ?></li>
						<?php endif; ?>
						<?php if ( $suji_footer_phone ) : ?>
							<li><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $suji_footer_phone ) ); ?>"><?php echo esc_html( $suji_footer_phone ); ?></a></li>
						<?php endif; ?>
						<?php if ( $suji_footer_email ) : ?>
							<li><a href="mailto:<?php echo esc_attr( $suji_footer_email ); ?>"><?php echo esc_html( $suji_footer_email ); ?></a></li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</div>

			<nav id="footer-navigation" class="footer-navigation">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'footer',
					'menu_id'        => 'footer-menu',
					'fallback_cb'    => false,
				) );
				?>
			</nav>
		</div>

		<div class="footer-bottom">
			<p class="site-info">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.
				<?php if ( $suji_footer_text ) : ?>
					<span class="footer-text"><?php echo esc_html( $suji_footer_text ); ?></span>
				<?php endif; ?>
			</p>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
