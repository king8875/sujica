<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Auto-load templates/page/page-{slug}.php for a page, without needing a
 * matching root-level file or a manually assigned "Template" in wp-admin.
 * A page with an explicitly assigned custom Template always takes priority.
 */
function suji_page_slug_template( $template ) {
	if ( ! is_page() ) {
		return $template;
	}

	if ( get_page_template_slug( get_queried_object_id() ) ) {
		return $template;
	}

	$suji_slug      = get_queried_object()->post_name;
	$suji_candidate = SUJI_DIR . '/templates/page/page-' . $suji_slug . '.php';

	if ( file_exists( $suji_candidate ) ) {
		return $suji_candidate;
	}

	return $template;
}
add_filter( 'template_include', 'suji_page_slug_template' );

/**
 * Auto-load templates/archive/archive-{name}.php for specific board_cat
 * terms, so each listed board can have its own dedicated archive design.
 * Terms not in the map keep using the generic taxonomy-board_cat.php.
 */
function suji_board_archive_template( $template ) {
	if ( ! is_tax( 'board_cat' ) ) {
		return $template;
	}

	$suji_term = get_queried_object();
	if ( empty( $suji_term->slug ) ) {
		return $template;
	}

	$suji_archive_map = array(
		'notice' => 'notice',
		'bible'  => 'bulletin',
		'gallery' => 'gallery',
		'story'  => 'priest-board',
		'sangim' => 'committee-board',
	);

	if ( ! isset( $suji_archive_map[ $suji_term->slug ] ) ) {
		return $template;
	}

	$suji_candidate = SUJI_DIR . '/templates/archive/archive-' . $suji_archive_map[ $suji_term->slug ] . '.php';

	if ( file_exists( $suji_candidate ) ) {
		return $suji_candidate;
	}

	return $template;
}
add_filter( 'template_include', 'suji_board_archive_template' );
