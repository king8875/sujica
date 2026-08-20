<?php
if (!defined('ABSPATH')) {
    exit;
}

get_header();


/**
 * 홈에 띄울 게시판 — 슬러그, 표시 이름, 짧은 설명.
 */
$suji_home_boards = array( 'suji_notice', 'suji_bulletin', 'suji_story', 'suji_docs' );

$suji_gallery = suji_board_recent( 'suji_gallery', 6 );

// 최근 글에 사진이 하나도 없으면 썸네일 격자 대신 목록으로 그린다.
$suji_gallery_has_thumb = false;
foreach ( $suji_gallery as $suji_post ) {
        if ( get_post_thumbnail_id( $suji_post ) || suji_first_image_from_content( $suji_post->post_content ) ) {
                $suji_gallery_has_thumb = true;
                break;
        }
}
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


                <?php // 원본 사이트가 쓰던 구글 캘린더 (본당 일정 · 주요 행사) ?>
                <div class="home-calendar">
                    <h2 class="home-calendar-title"><?php esc_html_e( '본당 일정', 'suji' ); ?></h2>
                    <div class="home-calendar-frame">
                        <iframe
                            src="https://calendar.google.com/calendar/embed?height=600&amp;wkst=1&amp;ctz=Asia%2FSeoul&amp;showPrint=0&amp;mode=AGENDA&amp;showCalendars=0&amp;showTz=0&amp;src=M2ZkNGIwNGExYWNkZGE1NzU4NGE0NmE5YWJmOTdlNDkzMDJlOWU0ZDEyM2M5YTdjMzk2NTY1YTVhNzI3NzI1MEBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&amp;src=ZGYwMWNlMGRiOWM3YTU4YWE3NDllMGY0NjM0YmI2NWZjODY1ZmY2YzIzM2FlMGEwYWJmMWI5NDdhMjdlZjUyOEBncm91cC5jYWxlbmRhci5nb29nbGUuY29t&amp;color=%23ec0000&amp;color=%23a79b8e"
                            title="<?php esc_attr_e( '본당 일정 달력', 'suji' ); ?>"
                            loading="lazy" scrolling="no"></iframe>
                    </div>
                </div>
            </div>


            <!-- ------------------------------ 본당 소식 ------------------------------ -->
            <?php
            // 탭으로 넘겨 보므로 한 게시판씩 넉넉히 보여 준다
            $suji_tabs = array();
            foreach ($suji_home_boards as $suji_type) {
                $suji_def = suji_board_of($suji_type);
                if (!$suji_def) {
                    continue;
                }
                $suji_tabs[] = array(
                    'type'  => $suji_type,
                    'name'  => $suji_def['name'],
                    'link'  => suji_board_link($suji_type),
                    'posts' => suji_board_recent($suji_type, 6),
                );
            }
            ?>

            <?php if ($suji_tabs) : ?>
                <section class="home-section home-boards">
                    <div class="home-tabs" role="tablist" aria-label="<?php esc_attr_e('본당 소식', 'suji'); ?>">
                        <?php foreach ($suji_tabs as $suji_i => $suji_tab) : ?>
                            <button type="button"
                                    class="home-tab<?php echo 0 === $suji_i ? ' is-active' : ''; ?>"
                                    id="home-tab-<?php echo esc_attr($suji_tab['type']); ?>"
                                    role="tab"
                                    aria-selected="<?php echo 0 === $suji_i ? 'true' : 'false'; ?>"
                                    aria-controls="home-panel-<?php echo esc_attr($suji_tab['type']); ?>"
                                    tabindex="<?php echo 0 === $suji_i ? '0' : '-1'; ?>">
                                <?php echo esc_html($suji_tab['name']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <?php foreach ($suji_tabs as $suji_i => $suji_tab) : ?>
                        <div class="home-panel<?php echo 0 === $suji_i ? ' is-active' : ''; ?>"
                             id="home-panel-<?php echo esc_attr($suji_tab['type']); ?>"
                             role="tabpanel"
                             aria-labelledby="home-tab-<?php echo esc_attr($suji_tab['type']); ?>"
                            <?php echo 0 === $suji_i ? '' : 'hidden'; ?>>

                            <?php if ($suji_tab['posts']) : ?>
                                <ul class="home-board-list">
                                    <?php foreach ($suji_tab['posts'] as $suji_post) : ?>
                                        <li>
                                            <a href="<?php echo esc_url(get_permalink($suji_post)); ?>">
                                                <span class="home-board-post-title"><?php echo esc_html(get_the_title($suji_post)); ?></span>
                                                <time class="home-board-date"
                                                      datetime="<?php echo esc_attr(get_the_date('c', $suji_post)); ?>"><?php echo esc_html(get_the_date('Y.m.d', $suji_post)); ?></time>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else : ?>
                                <p class="home-board-empty"><?php esc_html_e('아직 등록된 글이 없습니다.', 'suji'); ?></p>
                            <?php endif; ?>

                            <?php if ($suji_tab['link']) : ?>
                                <p class="home-panel-more">
                                    <a class="home-board-more" href="<?php echo esc_url($suji_tab['link']); ?>">
                                        <?php echo esc_html($suji_tab['name']); ?> 전체 보기
                                        <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor"
                                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <polyline points="9 6 15 12 9 18"></polyline>
                                        </svg>
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>

            <!-- ------------------------------ 포토앨범 ------------------------------ -->
            <?php if ($suji_gallery) : ?>
                <?php $suji_gallery_link = suji_board_link('suji_gallery'); ?>
                <section class="home-section home-gallery">
                    <div class="home-board-head">
                        <h2 class="home-board-title"><?php esc_html_e('포토앨범', 'suji'); ?></h2>
                        <?php if ($suji_gallery_link) : ?>
                            <a class="home-board-more" href="<?php echo esc_url($suji_gallery_link); ?>">
                                더보기
                                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <polyline points="9 6 15 12 9 18"></polyline>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>

                    <ul class="<?php echo $suji_gallery_has_thumb ? 'home-gallery-grid' : 'home-gallery-list'; ?>">
                        <?php foreach ($suji_gallery as $suji_post) : ?>
                            <?php
                            $suji_thumb = get_the_post_thumbnail_url($suji_post, 'medium_large');
                            if (!$suji_thumb) {
                                $suji_thumb = suji_first_image_from_content($suji_post->post_content);
                            }
                            ?>
                            <li>
                                <a href="<?php echo esc_url(get_permalink($suji_post)); ?>">
                                    <?php if ($suji_gallery_has_thumb) : ?>
                                        <span class="home-gallery-thumb"<?php echo $suji_thumb ? ' style="background-image:url(\'' . esc_url($suji_thumb) . '\')"' : ''; ?>></span>
                                    <?php endif; ?>
                                    <span class="home-gallery-caption">
                                        <span class="home-gallery-title"><?php echo esc_html(get_the_title($suji_post)); ?></span>
                                        <time class="home-board-date"
                                              datetime="<?php echo esc_attr(get_the_date('c', $suji_post)); ?>"><?php echo esc_html(get_the_date('Y.m.d', $suji_post)); ?></time>
                                    </span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>
        </div>
    </main>
<?php
get_footer();
