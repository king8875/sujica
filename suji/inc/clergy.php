<?php
/**
 * 역대 성직자 / 수도자 데이터 접근.
 *
 * 화면은 lofields 리피터를 우선 읽고, 리피터가 비어 있으면 inc/clergy-data.php
 * 의 초기 데이터를 대신 쓴다. 덕분에 JSON 동기화나 시드 전에도 페이지가
 * 정상 동작하고, 관리자에서 한 번 채우고 나면 그쪽이 유일한 출처가 된다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 섹션 정의. slug 는 리피터 이름(clergy_{slug})과 앵커 ID 에 함께 쓴다.
 */
function suji_clergy_sections() {
	return array(
		array( 'slug' => 'pastors', 'title' => '역대 주임신부', 'kind' => 'priest' ),
		array( 'slug' => 'assistants', 'title' => '역대 보좌신부', 'kind' => 'priest' ),
		array( 'slug' => 'lead_sisters', 'title' => '역대 책임전교수녀', 'kind' => 'sister' ),
		array( 'slug' => 'sisters', 'title' => '역대 전교수녀', 'kind' => 'sister' ),
		array( 'slug' => 'natives', 'title' => '본당 출신 사제', 'kind' => 'native' ),
	);
}

/**
 * 초기 데이터(파일)를 한 번만 읽어 slug 로 색인해 둔다.
 */
function suji_clergy_seed_data() {
	static $seed = null;

	if ( null === $seed ) {
		$seed = array();
		$raw  = require SUJI_DIR . '/inc/clergy-data.php';
		foreach ( (array) $raw as $section ) {
			$seed[ $section['slug'] ] = $section['rows'];
		}
	}

	return $seed;
}

/**
 * 한 섹션의 행 목록. 각 행은 photo(URL) / rank / name / order / term 을 갖는다.
 */
function suji_clergy_rows( $slug, $page_id = 0 ) {
	$field = 'clergy_' . $slug;
	$rows  = array();

	if ( function_exists( 'have_rows' ) && have_rows( $field, $page_id ) ) {
		while ( have_rows( $field, $page_id ) ) {
			the_row();

			$photo = get_sub_field( 'photo' );
			$url   = '';
			if ( is_array( $photo ) ) {
				$url = isset( $photo['sizes']['medium'] ) && $photo['sizes']['medium']
					? $photo['sizes']['medium']
					: ( $photo['url'] ?? '' );
			} elseif ( is_string( $photo ) ) {
				$url = $photo;
			}

			$rows[] = array(
				'photo' => $url,
				'rank'  => (string) get_sub_field( 'rank' ),
				'name'  => (string) get_sub_field( 'name' ),
				'order' => (string) get_sub_field( 'order' ),
				'term'  => (string) get_sub_field( 'term' ),
			);
		}

		return $rows;
	}

	// 리피터가 비어 있으면 초기 데이터로 그린다.
	$seed = suji_clergy_seed_data();
	foreach ( $seed[ $slug ] ?? array() as $row ) {
		$rows[] = array(
			'photo' => SUJI_URI . '/assets/images/clergy/' . $row['photo'],
			'rank'  => $row['rank'] ?? '',
			'name'  => $row['name'] ?? '',
			'order' => $row['order'] ?? '',
			'term'  => $row['term'] ?? '',
		);
	}

	return $rows;
}

/**
 * 재임기간이 '현재'로 끝나면 현직으로 본다.
 */
function suji_clergy_is_current( $term ) {
	return (bool) preg_match( '/현재\s*$/u', $term );
}
