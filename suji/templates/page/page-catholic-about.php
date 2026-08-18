<?php
/**
 * Template Name: 가톨릭 소개
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$suji_page_id = get_queried_object_id();

$suji_items = array();
if ( function_exists( 'have_rows' ) && have_rows( 'accordion_items', $suji_page_id ) ) {
	while ( have_rows( 'accordion_items', $suji_page_id ) ) {
		the_row();

		$suji_title = get_sub_field( 'accordion_title' );
		if ( ! $suji_title ) {
			continue;
		}

		$suji_items[] = array(
			'title'   => $suji_title,
			'content' => get_sub_field( 'accordion_content' ),
		);
	}
}
?>

<main id="primary" class="site-main catholic-about">

	<?php while ( have_posts() ) : the_post(); ?>

		<header class="ca-hero">
			<h1 class="ca-title"><?php the_title(); ?></h1>
			<p class="ca-lead">천주교의 가르침과 한국 교회가 걸어온 길을 안내합니다.</p>
		</header>

		<?php if ( $suji_items ) : ?>

			<div class="ca-accordion accordion">
				<?php foreach ( $suji_items as $suji_index => $suji_item ) : ?>
					<?php
					// 모두 닫혀 있으면 빈 화면처럼 보여 첫 항목만 펼쳐 둔다.
					$suji_open    = ( 0 === $suji_index );
					$suji_number  = $suji_index + 1;
					$suji_panel   = 'accordion-panel-' . $suji_page_id . '-' . $suji_number;
					$suji_button  = 'accordion-header-' . $suji_page_id . '-' . $suji_number;
					?>
					<div class="accordion-item<?php echo $suji_open ? ' is-open' : ''; ?>">
						<h2 class="accordion-heading">
							<button
									type="button"
									class="accordion-header"
									id="<?php echo esc_attr( $suji_button ); ?>"
									aria-expanded="<?php echo $suji_open ? 'true' : 'false'; ?>"
									aria-controls="<?php echo esc_attr( $suji_panel ); ?>"
							>
								<span class="accordion-number"><?php echo esc_html( sprintf( '%02d', $suji_number ) ); ?></span>
								<span class="accordion-title"><?php echo esc_html( $suji_item['title'] ); ?></span>
								<span class="accordion-icon" aria-hidden="true">
									<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
									     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<polyline points="6 9 12 15 18 9"></polyline>
									</svg>
								</span>
							</button>
						</h2>
						<div
								id="<?php echo esc_attr( $suji_panel ); ?>"
								class="accordion-panel"
								role="region"
								aria-labelledby="<?php echo esc_attr( $suji_button ); ?>"
						>
							<div class="accordion-panel-inner">
								<div class="accordion-body">
									<?php echo apply_filters( 'the_content', $suji_item['content'] ); ?>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

		<?php else : ?>
			<div class="ca-content entry-content">
				<?php the_content(); ?>
			</div>
		<?php endif; ?>

	<?php endwhile; ?>
</main>

<?php
get_footer();
