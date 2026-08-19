<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function suji_asset_version( $relative_path ) {
	$file = SUJI_DIR . $relative_path;
	return file_exists( $file ) ? filemtime( $file ) : SUJI_VERSION;
}

function suji_enqueue_assets() {
	wp_enqueue_style( 'suji-style', get_stylesheet_uri(), array(), suji_asset_version( '/style.css' ) );

	wp_enqueue_style( 'suji-pretendard', 'https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.css', array(), null );

	wp_enqueue_style( 'suji-reset', SUJI_URI . '/assets/css/base/reset.css', array(), suji_asset_version( '/assets/css/base/reset.css' ) );
	wp_enqueue_style( 'suji-fonts', SUJI_URI . '/assets/css/base/fonts.css', array( 'suji-reset', 'suji-pretendard' ), suji_asset_version( '/assets/css/base/fonts.css' ) );
	wp_enqueue_style( 'suji-common', SUJI_URI . '/assets/css/base/common.css', array( 'suji-fonts' ), suji_asset_version( '/assets/css/base/common.css' ) );

	wp_enqueue_style( 'suji-header', SUJI_URI . '/assets/css/layout/header.css', array( 'suji-common' ), suji_asset_version( '/assets/css/layout/header.css' ) );
	wp_enqueue_style( 'suji-footer', SUJI_URI . '/assets/css/layout/footer.css', array( 'suji-common' ), suji_asset_version( '/assets/css/layout/footer.css' ) );

	wp_enqueue_style( 'suji-board-grid', SUJI_URI . '/assets/css/components/board-grid.css', array( 'suji-common' ), suji_asset_version( '/assets/css/components/board-grid.css' ) );
	wp_enqueue_style( 'suji-board-post', SUJI_URI . '/assets/css/components/board-post.css', array( 'suji-common' ), suji_asset_version( '/assets/css/components/board-post.css' ) );

	wp_enqueue_style( 'suji-page-home', SUJI_URI . '/assets/css/pages/home.css', array( 'suji-board-grid' ), suji_asset_version( '/assets/css/pages/home.css' ) );
	wp_enqueue_style( 'suji-page-board', SUJI_URI . '/assets/css/pages/board.css', array( 'suji-board-post' ), suji_asset_version( '/assets/css/pages/board.css' ) );

	if ( is_page( array( SUJI_LOGIN_SLUG, SUJI_REGISTER_SLUG ) ) ) {
		wp_enqueue_style( 'suji-auth', SUJI_URI . '/assets/css/pages/auth.css', array( 'suji-common' ), suji_asset_version( '/assets/css/pages/auth.css' ) );
	}

	if ( is_page( 'withus' ) ) {
		wp_enqueue_style( 'suji-withus', SUJI_URI . '/assets/css/pages/withus.css', array( 'suji-common' ), suji_asset_version( '/assets/css/pages/withus.css' ) );
	}

	if ( is_page( 'pastoral-letter' ) ) {
		wp_enqueue_style( 'suji-pastoral-letter', SUJI_URI . '/assets/css/pages/pastoral-letter.css', array( 'suji-common' ), suji_asset_version( '/assets/css/pages/pastoral-letter.css' ) );
	}

	if ( is_page( 'catechumen' ) ) {
		wp_enqueue_style( 'suji-catechumen', SUJI_URI . '/assets/css/pages/catechumen.css', array( 'suji-common' ), suji_asset_version( '/assets/css/pages/catechumen.css' ) );
	}

	if ( is_page( 'bible-study' ) ) {
		wp_enqueue_style( 'suji-bible-study', SUJI_URI . '/assets/css/pages/bible-study.css', array( 'suji-common' ), suji_asset_version( '/assets/css/pages/bible-study.css' ) );
	}

	if ( is_page( 'parish-about' ) ) {
		wp_enqueue_style( 'suji-parish-about', SUJI_URI . '/assets/css/pages/parish-about.css', array( 'suji-common' ), suji_asset_version( '/assets/css/pages/parish-about.css' ) );
	}

	if ( is_page( 'patron-saint' ) ) {
		wp_enqueue_style( 'suji-patron-saint', SUJI_URI . '/assets/css/pages/patron-saint.css', array( 'suji-common' ), suji_asset_version( '/assets/css/pages/patron-saint.css' ) );
	}

	if ( is_page( 'clergy' ) ) {
		wp_enqueue_style( 'suji-clergy', SUJI_URI . '/assets/css/pages/clergy.css', array( 'suji-common' ), suji_asset_version( '/assets/css/pages/clergy.css' ) );
	}

	if ( is_page( 'office' ) ) {
		wp_enqueue_style( 'suji-office', SUJI_URI . '/assets/css/pages/office.css', array( 'suji-common' ), suji_asset_version( '/assets/css/pages/office.css' ) );
	}

	if ( is_page( 'committee' ) ) {
		wp_enqueue_style( 'suji-committee', SUJI_URI . '/assets/css/pages/committee.css', array( 'suji-common' ), suji_asset_version( '/assets/css/pages/committee.css' ) );
	}

	if ( is_page( 'catholic-about' ) ) {
		wp_enqueue_style( 'suji-catholic-about', SUJI_URI . '/assets/css/pages/catholic-about.css', array( 'suji-common' ), suji_asset_version( '/assets/css/pages/catholic-about.css' ) );
	}

	if ( is_post_type_archive( suji_board_post_types() ) || is_tax( 'board_cat' ) || is_search() ) {
		wp_enqueue_style( 'suji-board-archive', SUJI_URI . '/assets/css/pages/board-archive.css', array( 'suji-board-post' ), suji_asset_version( '/assets/css/pages/board-archive.css' ) );
	}

	/*
	 * 여기서 have_rows() 를 쓰면 안 된다. wp_enqueue_scripts 는 템플릿보다 먼저
	 * 도는데, have_rows() 첫 호출은 리피터 커서를 열어 둔 채 true 를 돌려준다.
	 * 그래서 템플릿의 if ( have_rows() ) 가 두 번째 호출이 되어 커서를 한 칸
	 * 밀어 버리고, 첫 행이 화면에서 통째로 빠졌다. get_field() 는 커서를
	 * 건드리지 않으므로 존재 여부만 볼 때는 이쪽을 쓴다.
	 */
	if ( is_page() && function_exists( 'get_field' ) && ! empty( get_field( 'accordion_items', get_queried_object_id() ) ) ) {
		wp_enqueue_style( 'suji-accordion', SUJI_URI . '/assets/css/components/accordion.css', array( 'suji-common' ), suji_asset_version( '/assets/css/components/accordion.css' ) );
		wp_enqueue_script( 'suji-accordion', SUJI_URI . '/assets/js/accordion.js', array(), suji_asset_version( '/assets/js/accordion.js' ), true );
	}

	if ( is_front_page() && function_exists( 'get_field' ) && ! empty( get_field( 'home_banner_slides', get_option( 'page_on_front' ) ) ) ) {
		wp_enqueue_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), null );
		wp_enqueue_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), null, true );
		wp_enqueue_script( 'suji-home-banner', SUJI_URI . '/assets/js/home-banner.js', array( 'swiper' ), suji_asset_version( '/assets/js/home-banner.js' ), true );
	}

	wp_enqueue_script( 'suji-main', SUJI_URI . '/assets/js/main.js', array(), suji_asset_version( '/assets/js/main.js' ), true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'suji_enqueue_assets' );
