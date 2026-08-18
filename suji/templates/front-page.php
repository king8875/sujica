<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();


$suji_gallery = get_posts(array(
        'post_type' => 'board_post',
        'posts_per_page' => 6,
        'tax_query' => array(
                array(
                        'taxonomy' => 'board_cat',
                        'field' => 'slug',
                        'terms' => 'gallery',
                ),
        ),
));
?>

    <main id="primary" class="site-main front-page">

        <?php $suji_front_page_id = get_option('page_on_front'); ?>

        <?php if (function_exists('have_rows') && have_rows('home_banner_slides', $suji_front_page_id)) : ?>
            <section class="home-banner">
                <div class="swiper home-banner-swiper">
                    <div class="swiper-wrapper">
                        <?php while (have_rows('home_banner_slides', $suji_front_page_id)) : the_row();
                            $suji_banner_image = get_sub_field('banner_image');
                            $suji_banner_link = get_sub_field('banner_link');
                            $suji_banner_title = get_sub_field('banner_title');
                            $suji_banner_subtitle = get_sub_field('banner_subtitle');

                            if (!$suji_banner_image) {
                                continue;
                            }
                            ?>
                            <div class="swiper-slide">
                                <?php if ($suji_banner_link) : ?>
                                <a target="_blank" href="<?php echo esc_url($suji_banner_link); ?>">
                                    <?php endif; ?>

                                        <img src="<?php echo esc_url($suji_banner_image['url']); ?>"
                                             alt="<?php echo esc_attr($suji_banner_image['alt'] ? $suji_banner_image['alt'] : $suji_banner_title); ?>">

                                        <?php if ($suji_banner_title || $suji_banner_subtitle) : ?>
                                            <div class="home-banner-caption">
                                                <?php if ($suji_banner_title) : ?>
                                                    <h2><?php echo esc_html($suji_banner_title); ?></h2>
                                                <?php endif; ?>
                                                <?php if ($suji_banner_subtitle) : ?>
                                                    <p><?php echo esc_html($suji_banner_subtitle); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($suji_banner_link) : ?>
                                    </a>
                                <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>
                    </div>
                </section>
            <?php endif; ?>

        <div class="container">
            <div class="hero-section">
                <div class="time-table">
                    <div class="notice-section timeline-block">
                        <h2><?php esc_html_e('미사시간안내', 'suji'); ?></h2>
                        <ul class="mass-schedule">
                            <li>
                                <h3>주일미사</h3>
                                <div>
                                    <p>
                                        <span class="days-tx">토요일</span>
                                        <span>15:30 (어린이/첫주는 없음)<br> 18:30 (중고등부, 토요저녁주일)</span>
                                    </p>
                                    <p>
                                        <span class="days-tx">주일</span>
                                        <span>07:00, 09:00, 11:00 (교중),<br> 17:00, 19:00 (청년)</span>
                                    </p>
                                </div>
                            </li>
                            <li>
                                <h3>평일미사</h3>
                                <div>
                                    <p>
                                        <span class="days-tx">월요일</span>
                                        <span>06:30</span>
                                    </p>
                                    <p>
                                        <span class="days-tx">화요일</span>
                                        <span>10:00, 19:30</span>
                                    </p>
                                    <p>
                                        <span class="days-tx">수요일</span>
                                        <span>06:30, 10:00</span>
                                    </p>
                                    <p>
                                        <span class="days-tx">목요일</span>
                                        <span>10:00, 19:30</span>
                                    </p>
                                    <p>
                                        <span class="days-tx">금요일</span>
                                        <span>06:30, 10:00</span>
                                    </p>
                                    <p>
                                        <span class="days-tx">토요일</span>
                                        <span>10:00 (첫째: 성모신심미사, 셋째: 위령미사)</span>
                                    </p>
                                </div>
                            </li>
                            <li>
                                <h3>고해성사</h3>
                                <p>미사 15분 전부터 5분 전까지</p>
                            </li>
                            <li>
                                <h3>병자영성체</h3>
                                <p>매월 첫째 금요일 14시 부터</p>
                            </li>
                            <li>
                                <h3>성시간</h3>
                                <p>매월 첫째 목요일 저녁미사 후</p>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>


            <?php if ($suji_gallery) : ?>
                <section class="home-section home-gallery">
                    <h2><?php esc_html_e('포토앨범', 'suji'); ?></h2>
                    <ul class="home-gallery-grid">
                        <?php foreach ($suji_gallery as $suji_post) : setup_postdata($suji_post); ?>
                            <li>
                                <a href="<?php echo esc_url(get_permalink($suji_post)); ?>"><?php echo esc_html(get_the_title($suji_post)); ?></a>
                            </li>
                        <?php endforeach;
                        wp_reset_postdata(); ?>
                    </ul>
                    <a class="home-more-link"
                       href="<?php echo esc_url(get_term_link('gallery', 'board_cat')); ?>"><?php esc_html_e('더보기', 'suji'); ?>
                        &rarr;</a>
                </section>
            <?php endif; ?>
        </div>
    </main>
<?php
get_footer();
