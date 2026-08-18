<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

    <main id="primary" class="site-main">
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                </header>

                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>

            <?php if ( comments_open() || get_comments_number() ) : comments_template(); endif; ?>
        <?php endwhile; ?>
    </main>

<?php
get_footer();
