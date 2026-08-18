<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="masthead" class="site-header">
    <div class="header-inner">
        <div class="site-branding">
            <div class="site-title">
                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                    <img class="site-logo" src="/wp-content/uploads/2026/08/sujilogo-scaled.png"
                         alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
                </a>
            </div>
        </div>

        <div class="header-search">
            <form role="search" method="get" class="header-search-form" action="<?php echo esc_url(home_url('/')); ?>">
                <label class="screen-reader-text" for="header-search-field"><?php esc_html_e('검색', 'suji'); ?></label>
                <input type="search"
                       id="header-search-field"
                       class="header-search-field"
                       name="s"
                       value="<?php echo esc_attr(get_search_query()); ?>"
                       placeholder="<?php esc_attr_e('검색어를 입력하세요', 'suji'); ?>">
                <button type="submit" class="header-search-submit">
                    <span class="screen-reader-text"><?php esc_html_e('검색', 'suji'); ?></span>
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <line x1="16.5" y1="16.5" x2="21" y2="21"></line>
                    </svg>
                </button>
            </form>
        </div>

        <div class="header-actions">
            <?php if (is_user_logged_in()) : ?>
                <?php $suji_current_user = wp_get_current_user(); ?>
                <span class="header-user"><?php echo esc_html($suji_current_user->display_name); ?>님</span>
                <a class="header-link"
                   href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>"><?php esc_html_e('로그아웃', 'suji'); ?></a>
            <?php else : ?>
                <a class="header-link"
                   href="<?php echo esc_url(wp_login_url()); ?>"><?php esc_html_e('로그인', 'suji'); ?></a>
                <a class="header-link header-link-signup"
                   href="<?php echo esc_url(wp_registration_url()); ?>"><?php esc_html_e('회원가입', 'suji'); ?></a>
            <?php endif; ?>
        </div>

        <nav id="site-navigation" class="main-navigation">
            <?php
            wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id' => 'primary-menu',
            ));
            ?>
        </nav>
    </div>
</header>

<div id="content" class="site-content">
