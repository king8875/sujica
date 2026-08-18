<?php
/**
 * Template Name: 가톨릭 소개
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$suji_page_id = get_queried_object_id();
?>

<main id="primary" class="site-main">

    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>
            </header>

            <?php if (function_exists('have_rows') && have_rows('accordion_items', $suji_page_id)) : ?>
                <div class="accordion">
                    <?php $suji_index = 0; ?>
                    <?php while (have_rows('accordion_items', $suji_page_id)) : the_row(); ?>
                        <?php
                        $suji_index++;
                        $suji_acc_title = get_sub_field('accordion_title');
                        $suji_acc_content = get_sub_field('accordion_content');

                        if (!$suji_acc_title) {
                            continue;
                        }

                        $suji_panel_id = 'accordion-panel-' . $suji_page_id . '-' . $suji_index;
                        $suji_button_id = 'accordion-header-' . $suji_page_id . '-' . $suji_index;
                        ?>
                        <div class="accordion-item">
                            <h2 class="accordion-heading">
                                <button
                                        type="button"
                                        class="accordion-header"
                                        id="<?php echo esc_attr($suji_button_id); ?>"
                                        aria-expanded="false"
                                        aria-controls="<?php echo esc_attr($suji_panel_id); ?>"
                                >
                                    <span class="accordion-number"><?php echo esc_html($suji_index); ?></span>
                                    <span class="accordion-title"><?php echo esc_html($suji_acc_title); ?></span>
                                    <span class="accordion-icon" aria-hidden="true">
									<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<polyline points="18 15 12 9 6 15"></polyline>
									</svg>
								</span>
                                </button>
                            </h2>
                            <div
                                    id="<?php echo esc_attr($suji_panel_id); ?>"
                                    class="accordion-panel"
                                    role="region"
                                    aria-labelledby="<?php echo esc_attr($suji_button_id); ?>"
                            >
                                <div class="accordion-panel-inner">
                                    <div class="accordion-body">
                                        <?php echo apply_filters('the_content', $suji_acc_content); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>
        <?php endwhile; ?>
    </div>
</main>

<?php
get_footer();
