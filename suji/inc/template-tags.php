<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function suji_posted_on() {
	$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	$time_string = sprintf(
		$time_string,
		esc_attr( get_the_date( DATE_W3C ) ),
		esc_html( get_the_date() )
	);

	echo '<span class="posted-on">' . $time_string . '</span>';
}

function suji_entry_footer() {
	if ( ! is_single() ) {
		return;
	}

	$categories_list = get_the_category_list( ', ' );
	if ( $categories_list ) {
		printf( '<span class="cat-links">' . esc_html__( 'Categories: %1$s', 'suji' ) . '</span>', $categories_list );
	}
}
