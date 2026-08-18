<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function suji_register_acf_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
		'key'    => 'group_suji_home_banner',
		'title'  => '홈 배너',
		'fields' => array(
			array(
				'key'          => 'field_suji_home_banner_slides',
				'label'        => '배너 슬라이드',
				'name'         => 'home_banner_slides',
				'type'         => 'repeater',
				'layout'       => 'block',
				'button_label' => '슬라이드 추가',
				'sub_fields'   => array(
					array(
						'key'           => 'field_suji_banner_image',
						'label'         => '이미지',
						'name'          => 'banner_image',
						'type'          => 'image',
						'return_format' => 'array',
						'preview_size'  => 'medium',
						'required'      => 1,
					),
					array(
						'key'   => 'field_suji_banner_link',
						'label' => '링크 URL',
						'name'  => 'banner_link',
						'type'  => 'url',
					),
					array(
						'key'   => 'field_suji_banner_title',
						'label' => '제목',
						'name'  => 'banner_title',
						'type'  => 'text',
					),
					array(
						'key'   => 'field_suji_banner_subtitle',
						'label' => '부제목',
						'name'  => 'banner_subtitle',
						'type'  => 'text',
					),
				),
			),
		),
		'location' => array(
			array(
				array(
					'param'    => 'page_type',
					'operator' => '==',
					'value'    => 'front_page',
				),
			),
		),
	) );
}
add_action( 'acf/init', 'suji_register_acf_fields' );
