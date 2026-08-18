<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function suji_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'suji_theme_options', array(
		'title'    => __( 'Suji Theme Options', 'suji' ),
		'priority' => 130,
	) );

	$wp_customize->add_setting( 'suji_footer_text', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );

	$wp_customize->add_control( 'suji_footer_text', array(
		'label'   => __( 'Footer Text', 'suji' ),
		'section' => 'suji_theme_options',
		'type'    => 'text',
	) );

	$suji_footer_fields = array(
		'suji_footer_address' => __( 'Footer Address', 'suji' ),
		'suji_footer_phone'   => __( 'Footer Phone', 'suji' ),
		'suji_footer_email'   => __( 'Footer Email', 'suji' ),
	);

	foreach ( $suji_footer_fields as $suji_setting_id => $suji_label ) {
		$wp_customize->add_setting( $suji_setting_id, array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( $suji_setting_id, array(
			'label'   => $suji_label,
			'section' => 'suji_theme_options',
			'type'    => 'text',
		) );
	}
}
add_action( 'customize_register', 'suji_customize_register' );
