<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function suji_setup() {
	load_theme_textdomain( 'suji', SUJI_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'suji' ),
		'footer'  => __( 'Footer Menu', 'suji' ),
	) );
}
add_action( 'after_setup_theme', 'suji_setup' );

function suji_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'suji' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'suji_widgets_init' );
