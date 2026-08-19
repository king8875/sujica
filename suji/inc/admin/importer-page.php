<?php
/**
 * 도구 > 원본 사이트 가져오기 화면.
 *
 * 공유호스팅에서 한 요청에 다 처리할 수 없으므로, 화면의 자바스크립트가
 * ajax 로 조금씩 반복 호출한다. 중간에 창을 닫아도 큐가 남아 이어서 돌린다.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function suji_import_menu() {
	add_management_page(
		__( '원본 사이트 가져오기', 'suji' ),
		__( '원본 가져오기', 'suji' ),
		'manage_options',
		'suji-import',
		'suji_import_render'
	);
}
add_action( 'admin_menu', 'suji_import_menu' );

function suji_import_render() {
	$suji_queue  = (array) get_option( SUJI_IMPORT_QUEUE, array() );
	$suji_enrich = (array) get_option( SUJI_IMPORT_ENRICH, array() );
	$suji_left   = count( $suji_queue ) + count( $suji_enrich );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( '원본 사이트 가져오기', 'suji' ); ?></h1>
		<p class="description">
			sujica.or.kr 의 공개 페이지를 읽어, 아직 없는 글과 본문 사진을 가져옵니다.
			원본 글번호를 남겨두므로 여러 번 눌러도 같은 글이 두 번 들어가지 않습니다.
		</p>

		<h2 class="title"><?php esc_html_e( '1. 연결 확인', 'suji' ); ?></h2>
		<p>
			<button class="button" id="suji-test"><?php esc_html_e( '원본 사이트 접속 확인', 'suji' ); ?></button>
			<span id="suji-test-out"></span>
		</p>

		<h2 class="title"><?php esc_html_e( '2. 훑기', 'suji' ); ?></h2>
		<p>
			<label><?php esc_html_e( '게시판별로 볼 쪽수', 'suji' ); ?>
				<input type="number" id="suji-pages" value="3" min="1" max="200" style="width:5em">
			</label>
			&nbsp;
			<label>
				<input type="checkbox" id="suji-photos">
				<?php esc_html_e( '이미 있는 글의 빠진 사진도 함께 채우기', 'suji' ); ?>
			</label>
		</p>
		<p class="description" style="margin-top:-.4em">
			최근 글만 맞추려면 3쪽으로 충분합니다. 포토앨범 사진까지 전부 가져오려면
			쪽수를 <strong>130</strong> 이상으로 두고 사진 채우기를 켜세요. 원본 목록은 한 쪽에
			12건이라 1,482건을 모두 훑으려면 124쪽이 필요합니다 (오래 걸립니다).
		</p>
		<p>
			<button class="button button-secondary" id="suji-scan"><?php esc_html_e( '훑기 시작', 'suji' ); ?></button>
			<button class="button" id="suji-reset"><?php esc_html_e( '초기화', 'suji' ); ?></button>
			<span id="suji-scan-out"></span>
		</p>

		<h2 class="title"><?php esc_html_e( '3. 가져오기', 'suji' ); ?></h2>
		<p>
			<button class="button button-primary" id="suji-run"
			        <?php disabled( 0 === $suji_left ); ?>><?php esc_html_e( '가져오기 시작', 'suji' ); ?></button>
			<button class="button" id="suji-stop" disabled><?php esc_html_e( '멈춤', 'suji' ); ?></button>
			<span id="suji-run-out">
				<?php if ( $suji_left ) : ?>
					<?php printf( esc_html__( '대기 %d건 (새 글 %d · 사진 %d)', 'suji' ), $suji_left, count( $suji_queue ), count( $suji_enrich ) ); ?>
				<?php endif; ?>
			</span>
		</p>
		<p class="description" style="margin-top:-.4em">
			이 창을 열어둔 채로 진행됩니다. 창을 닫아도 남은 목록은 보관되니 다시 눌러 이어가면 됩니다.
		</p>
		<h2 class="title"><?php esc_html_e( '4. 뒷정리', 'suji' ); ?></h2>
		<p>
			<button class="button" id="suji-fix"><?php esc_html_e( '이미지 주소 https 로 정리', 'suji' ); ?></button>
			<span id="suji-fix-out"></span>
		</p>
		<p>
			<button class="button button-primary" id="suji-dedupe"><?php esc_html_e( '중복 사진 정리', 'suji' ); ?></button>
			<span id="suji-dedupe-out"></span>
		</p>
		<p>
			<button class="button" id="suji-titles"><?php esc_html_e( '제목 정리', 'suji' ); ?></button>
			<span id="suji-titles-out"></span>
		</p>
		<p class="description" style="margin-top:-.4em">
			<strong>이미지 주소 https</strong> — 관리자 화면을 http 로 열어둔 채 가져오기를 돌리면
			이미지 주소가 http 로 박혀 브라우저가 경고를 냅니다.<br>
			<strong>중복 사진 정리</strong> — 같은 사진이 여러 번 올라간 글에서 첫 장만 남기고
			나머지는 첨부까지 삭제합니다. 대표 이미지로 지정된 장은 건드리지 않습니다.<br>
			<strong>제목 정리</strong> — 제목 앞에 원본 게시판의 분류 이름(연도 · 문서 · 공지)이
			붙어 들어간 글을 고칩니다.
		</p>

		<pre id="suji-log" style="max-height:24em;overflow:auto;background:#fff;border:1px solid #dcdcde;padding:.75em;margin-top:1em"></pre>
	</div>

	<script>
	(function () {
		var nonce = <?php echo wp_json_encode( wp_create_nonce( 'suji_import' ) ); ?>;
		var log = document.getElementById('suji-log');
		var stop = false;

		function say(msg) {
			log.textContent += msg + "\n";
			log.scrollTop = log.scrollHeight;
		}

		function call(action, extra) {
			var body = new URLSearchParams(Object.assign({action: action, _wpnonce: nonce}, extra || {}));
			return fetch(ajaxurl, {method: 'POST', credentials: 'same-origin', body: body})
				.then(function (r) { return r.json(); });
		}

		function btn(id) { return document.getElementById(id); }

		btn('suji-test').addEventListener('click', function (e) {
			e.preventDefault();
			var out = btn('suji-test-out');
			out.textContent = ' 확인 중…';
			call('suji_import_test').then(function (r) {
				out.textContent = ' ' + (r.data || '');
			});
		});

		btn('suji-reset').addEventListener('click', function (e) {
			e.preventDefault();
			call('suji_import_reset').then(function (r) {
				btn('suji-scan-out').textContent = ' ' + r.data;
				btn('suji-run-out').textContent = '';
				btn('suji-run').disabled = true;
				say('— 초기화');
			});
		});

		btn('suji-fix').addEventListener('click', function (e) {
			e.preventDefault();
			var out = btn('suji-fix-out');
			out.textContent = ' 정리 중…';
			call('suji_import_fix_scheme').then(function (r) {
				out.textContent = ' ' + (r.data || '');
				say('— ' + (r.data || ''));
			});
		});

		btn('suji-titles').addEventListener('click', function (e) {
			e.preventDefault();
			btn('suji-titles').disabled = true;
			titleStep();
		});

		function titleStep() {
			call('suji_import_fix_titles').then(function (r) {
				(r.data.lines || []).forEach(say);
				btn('suji-titles-out').textContent = ' ' + r.data.count + '건 정리';
				if (r.data.left > 0) {
					setTimeout(titleStep, 200);
				} else {
					say('— 제목 정리 완료');
					btn('suji-titles').disabled = false;
				}
			}).catch(function (err) {
				say('— 제목 정리 오류: ' + err);
				btn('suji-titles').disabled = false;
			});
		}

		btn('suji-dedupe').addEventListener('click', function (e) {
			e.preventDefault();
			stop = false;
			btn('suji-dedupe').disabled = true;
			btn('suji-stop').disabled = false;
			dedupeStep();
		});

		function dedupeStep() {
			if (stop) { btn('suji-dedupe').disabled = false; btn('suji-stop').disabled = true; return; }
			call('suji_import_dedupe').then(function (r) {
				(r.data.lines || []).forEach(say);
				btn('suji-dedupe-out').textContent =
					' 남은 글 ' + r.data.left + '건 · 회수 ' + r.data.freed;
				if (r.data.left > 0) {
					setTimeout(dedupeStep, 200);
				} else {
					btn('suji-dedupe').disabled = false;
					btn('suji-stop').disabled = true;
				}
			}).catch(function (err) {
				say('— 정리 오류: ' + err + ' (다시 누르면 이어서 진행합니다)');
				btn('suji-dedupe').disabled = false;
				btn('suji-stop').disabled = true;
			});
		}

		btn('suji-scan').addEventListener('click', function (e) {
			e.preventDefault();
			stop = false;
			btn('suji-scan').disabled = true;
			btn('suji-stop').disabled = false;
			scanStep();
		});

		function scanStep() {
			if (stop) { btn('suji-scan').disabled = false; btn('suji-stop').disabled = true; return; }
			call('suji_import_scan', {
				pages: btn('suji-pages').value,
				photos: btn('suji-photos').checked ? 1 : 0
			}).then(function (r) {
				(r.data.lines || []).forEach(say);
				btn('suji-scan-out').textContent =
					' 남은 게시판 ' + r.data.left + '개 · 대기 새 글 ' + r.data.queue + '건 · 사진 ' + r.data.enrich + '건';
				btn('suji-run').disabled = (r.data.queue + r.data.enrich) === 0;
				if (!r.data.done) {
					setTimeout(scanStep, 250);
				} else {
					btn('suji-scan').disabled = false;
					btn('suji-stop').disabled = true;
				}
			}).catch(function (err) {
				say('— 훑기 오류: ' + err + ' (다시 누르면 이어서 진행합니다)');
				btn('suji-scan').disabled = false;
				btn('suji-stop').disabled = true;
			});
		}

		btn('suji-stop').addEventListener('click', function (e) {
			e.preventDefault();
			stop = true;
			say('— 멈춤 요청');
		});

		btn('suji-run').addEventListener('click', function (e) {
			e.preventDefault();
			stop = false;
			btn('suji-stop').disabled = false;
			runStep();
		});

		function runStep() {
			if (stop) { btn('suji-stop').disabled = true; return; }
			call('suji_import_run').then(function (r) {
				(r.data.lines || []).forEach(say);
				btn('suji-run-out').textContent = ' 남은 ' + r.data.left + '건';
				if (r.data.left > 0) {
					setTimeout(runStep, 300);
				} else {
					say('— 모두 가져왔습니다.');
					btn('suji-stop').disabled = true;
					btn('suji-run').disabled = true;
				}
			}).catch(function (err) {
				say('— 통신 오류: ' + err + ' (다시 시작을 누르면 이어서 진행합니다)');
				btn('suji-stop').disabled = true;
			});
		}
	})();
	</script>
	<?php
}

function suji_import_check() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( '권한이 없습니다.' );
	}
	check_ajax_referer( 'suji_import' );
}

/**
 * 원본 사이트로 바깥 요청이 되는지 확인. 호스팅이 막아 두면 여기서 걸린다.
 */
function suji_import_ajax_test() {
	suji_import_check();

	$suji_body = suji_import_get( SUJI_IMPORT_ORIGIN . '/bbs/board.php?bo_table=notice' );
	if ( is_wp_error( $suji_body ) ) {
		wp_send_json_error( '실패 — ' . $suji_body->get_error_message() );
	}

	$suji_items = suji_import_parse_list( $suji_body );
	$suji_files = 0;
	foreach ( $suji_items as $suji_item ) {
		if ( ! empty( $suji_item['files'] ) ) {
			$suji_files++;
		}
	}
	wp_send_json_success( sprintf(
		'성공 — 목록에서 %d건을 읽었습니다. (첨부 있는 글 %d건)',
		count( $suji_items ), $suji_files
	) );
}
add_action( 'wp_ajax_suji_import_test', 'suji_import_ajax_test' );

/**
 * 게시판별로 목록을 훑어 아직 없는 글을 큐에 담는다.
 */
/**
 * 게시판을 하나씩 훑는다. 한 요청에 게시판 하나만 보므로 시간이 넉넉하다.
 *
 * 훑는 동안
 *  - 이미 있는 글에는 원본 글번호를 붙이고, 사진이 없으면 '사진 채우기' 줄에 담는다
 *  - 새 글은 '가져오기' 줄에 담는다
 */
function suji_import_ajax_scan() {
	suji_import_check();

	// 구 사이트 목록은 한 쪽에 12건이라, 포토앨범 1,482건을 모두 훑으려면 124쪽이 필요하다
	$suji_pages = max( 1, min( 200, (int) ( $_POST['pages'] ?? 3 ) ) );
	$suji_photos = ! empty( $_POST['photos'] );
	$suji_map   = array_keys( suji_import_map() );
	$suji_info  = suji_import_map();

	$suji_at = (int) get_option( SUJI_IMPORT_SCAN, 0 );
	if ( $suji_at >= count( $suji_map ) ) {
		delete_option( SUJI_IMPORT_SCAN );
		wp_send_json_success( array(
			'done'    => true,
			'left'    => 0,
			'queue'   => count( (array) get_option( SUJI_IMPORT_QUEUE, array() ) ),
			'enrich'  => count( (array) get_option( SUJI_IMPORT_ENRICH, array() ) ),
			'lines'   => array( '— 훑기를 마쳤습니다.' ),
		) );
	}

	$suji_bo    = $suji_map[ $suji_at ];
	$suji_type  = $suji_info[ $suji_bo ]['type'];
	$suji_index = suji_import_existing( $suji_type );

	$suji_queue  = (array) get_option( SUJI_IMPORT_QUEUE, array() );
	$suji_enrich = (array) get_option( SUJI_IMPORT_ENRICH, array() );

	$suji_new = 0;
	$suji_linked = 0;
	$suji_for_photos = 0;

	for ( $suji_p = 1; $suji_p <= $suji_pages; $suji_p++ ) {
		$suji_body = suji_import_get(
			SUJI_IMPORT_ORIGIN . '/bbs/board.php?bo_table=' . $suji_bo . '&page=' . $suji_p
		);
		if ( is_wp_error( $suji_body ) ) {
			break;
		}

		$suji_items = suji_import_parse_list( $suji_body );
		if ( ! $suji_items ) {
			break;
		}

		foreach ( $suji_items as $suji_wr_id => $suji_item ) {
			$suji_title = $suji_item['title'];
			$suji_key   = suji_import_key( $suji_title );

			if ( isset( $suji_index[ $suji_key ] ) ) {
				$suji_post_id = $suji_index[ $suji_key ];

				// 다음 실행부터 제목이 아니라 번호로 정확히 걸러내도록
				if ( ! get_post_meta( $suji_post_id, '_g5_wr_id', true ) ) {
					update_post_meta( $suji_post_id, '_g5_wr_id', (string) $suji_wr_id );
					update_post_meta( $suji_post_id, '_g5_bo_table', $suji_bo );
					$suji_linked++;
				}

				// 사진이 없거나 첨부가 달려 있으면 채우기 대상
				$suji_need = ( ! get_post_thumbnail_id( $suji_post_id ) ) || ! empty( $suji_item['files'] );
				if ( $suji_photos && $suji_need ) {
					$suji_enrich[] = array( 'post' => $suji_post_id, 'bo' => $suji_bo, 'id' => $suji_wr_id );
					$suji_for_photos++;
				}
				continue;
			}

			if ( suji_import_found_by_wr_id( $suji_bo, $suji_wr_id ) ) {
				continue;
			}

			$suji_queue[] = array( 'bo' => $suji_bo, 'id' => $suji_wr_id );
			$suji_new++;
		}
	}

	// 사진이 실제로 들어 있는 게시판을 앞으로 — 포토앨범만 본문에 사진이 있고
	// 공지·주보는 대부분 없다. 순서를 안 바꾸면 '새 사진 없음' 만 1,300줄 지나간다.
	usort( $suji_enrich, function ( $a, $b ) {
		$suji_rank = function ( $bo ) {
			if ( 'gallery' === $bo ) { return 0; }   // 사진이 실제로 들어 있는 곳
			if ( 'docu' === $bo )    { return 1; }   // 첨부 문서가 많은 곳
			if ( 'notice' === $bo )  { return 2; }
			return 3;
		};
		return $suji_rank( $a['bo'] ) <=> $suji_rank( $b['bo'] );
	} );

	update_option( SUJI_IMPORT_QUEUE, $suji_queue, false );
	update_option( SUJI_IMPORT_ENRICH, $suji_enrich, false );
	update_option( SUJI_IMPORT_SCAN, $suji_at + 1, false );

	wp_send_json_success( array(
		'done'   => false,
		'left'   => count( $suji_map ) - $suji_at - 1,
		'queue'  => count( $suji_queue ),
		'enrich' => count( $suji_enrich ),
		'lines'  => array( sprintf(
			'%-10s 새 글 %d건 · 번호 연결 %d건%s',
			$suji_bo, $suji_new, $suji_linked,
			$suji_photos ? sprintf( ' · 사진 채울 글 %d건', $suji_for_photos ) : ''
		) ),
	) );
}
add_action( 'wp_ajax_suji_import_scan', 'suji_import_ajax_scan' );

/**
 * 훑기를 처음부터 다시 하도록 되돌린다.
 */
function suji_import_ajax_reset() {
	suji_import_check();
	delete_option( SUJI_IMPORT_SCAN );
	delete_option( SUJI_IMPORT_QUEUE );
	delete_option( SUJI_IMPORT_ENRICH );
	wp_send_json_success( '초기화했습니다.' );
}
add_action( 'wp_ajax_suji_import_reset', 'suji_import_ajax_reset' );

/**
 * 큐에서 몇 건씩 처리한다.
 */
/**
 * 새 글을 먼저 가져오고, 다 끝나면 기존 글에 사진을 채운다.
 */
function suji_import_ajax_run() {
	suji_import_check();

	$suji_queue  = (array) get_option( SUJI_IMPORT_QUEUE, array() );
	$suji_enrich = (array) get_option( SUJI_IMPORT_ENRICH, array() );
	$suji_lines  = array();
	$suji_batch  = 2;   // 글마다 사진을 받아 올리므로 조금씩

	for ( $suji_i = 0; $suji_i < $suji_batch; $suji_i++ ) {
		if ( $suji_queue ) {
			$suji_job = array_shift( $suji_queue );
			$suji_lines[] = sprintf(
				'[%s #%s] %s',
				$suji_job['bo'], $suji_job['id'],
				suji_import_one( $suji_job['bo'], $suji_job['id'] )
			);
			continue;
		}

		if ( $suji_enrich ) {
			$suji_job = array_shift( $suji_enrich );
			$suji_lines[] = sprintf(
				'[사진 %s #%s] %s',
				$suji_job['bo'], $suji_job['id'],
				suji_import_enrich_one( $suji_job['post'], $suji_job['bo'], $suji_job['id'] )
			);
			continue;
		}

		break;
	}

	update_option( SUJI_IMPORT_QUEUE, $suji_queue, false );
	update_option( SUJI_IMPORT_ENRICH, $suji_enrich, false );

	wp_send_json_success( array(
		'lines' => $suji_lines,
		'left'  => count( $suji_queue ) + count( $suji_enrich ),
	) );
}
add_action( 'wp_ajax_suji_import_run', 'suji_import_ajax_run' );

/**
 * 이미 본문에 http 로 박힌 업로드 주소를 사이트 기본 스킴으로 바꾼다.
 * 관리자 화면을 http 로 열어둔 채 가져오기를 돌린 글이 대상이다.
 */
function suji_import_ajax_fix_scheme() {
	suji_import_check();

	global $wpdb;

	$suji_https = trailingslashit( home_url( '/wp-content/uploads' ) );
	$suji_http  = set_url_scheme( $suji_https, 'http' );
	$suji_https = set_url_scheme( $suji_https, parse_url( home_url(), PHP_URL_SCHEME ) );

	if ( $suji_http === $suji_https ) {
		wp_send_json_success( '사이트가 http 라 바꿀 것이 없습니다.' );
	}

	$suji_rows = $wpdb->query( $wpdb->prepare(
		"UPDATE {$wpdb->posts}
		    SET post_content = REPLACE( post_content, %s, %s )
		  WHERE post_content LIKE %s",
		$suji_http,
		$suji_https,
		'%' . $wpdb->esc_like( $suji_http ) . '%'
	) );

	wp_cache_flush();

	wp_send_json_success( sprintf( '글 %d건의 이미지 주소를 https 로 바꿨습니다.', (int) $suji_rows ) );
}
add_action( 'wp_ajax_suji_import_fix_scheme', 'suji_import_ajax_fix_scheme' );

/**
 * 중복으로 들어간 사진을 글 단위로 정리한다. 한 번 호출에 몇 글씩.
 */
function suji_import_ajax_dedupe() {
	suji_import_check();

	$suji_queue = get_option( 'suji_import_dedupe_queue', null );

	// 처음 호출이면 대상 글 목록을 만든다
	if ( null === $suji_queue ) {
		$suji_queue = get_posts( array(
			'post_type'      => suji_board_post_types(),
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'fields'         => 'ids',
			'orderby'        => 'ID',
			'order'          => 'ASC',
		) );
		update_option( 'suji_import_dedupe_queue', $suji_queue, false );
		update_option( 'suji_import_dedupe_stat', array( 0, 0 ), false );
	}

	$suji_queue = (array) $suji_queue;
	$suji_stat  = (array) get_option( 'suji_import_dedupe_stat', array( 0, 0 ) );
	$suji_lines = array();

	for ( $suji_i = 0; $suji_i < 25 && $suji_queue; $suji_i++ ) {
		$suji_id = (int) array_shift( $suji_queue );
		list( $suji_n, $suji_b ) = suji_import_dedupe_post( $suji_id );
		if ( $suji_n ) {
			$suji_stat[0] += $suji_n;
			$suji_stat[1] += $suji_b;
			$suji_lines[] = sprintf( '[%s] 중복 %d장 삭제 (%s)',
				get_the_title( $suji_id ) ?: ( '#' . $suji_id ), $suji_n, size_format( $suji_b ) );
		}
	}

	update_option( 'suji_import_dedupe_queue', $suji_queue, false );
	update_option( 'suji_import_dedupe_stat', $suji_stat, false );

	$suji_left = count( $suji_queue );
	if ( 0 === $suji_left ) {
		delete_option( 'suji_import_dedupe_queue' );
		delete_option( 'suji_import_dedupe_stat' );
		$suji_lines[] = sprintf( '— 정리 완료: 중복 %d장 삭제, %s 회수',
			(int) $suji_stat[0], size_format( (int) $suji_stat[1] ) );
	}

	wp_send_json_success( array(
		'lines' => $suji_lines,
		'left'  => $suji_left,
		'freed' => size_format( (int) $suji_stat[1] ),
	) );
}
add_action( 'wp_ajax_suji_import_dedupe', 'suji_import_ajax_dedupe' );

/**
 * 제목에 분류 이름이 붙어 들어간 글을 정리한다.
 */
function suji_import_ajax_fix_titles() {
	suji_import_check();

	list( $suji_count, $suji_lines ) = suji_import_fix_titles( 40 );

	wp_send_json_success( array(
		'lines' => $suji_lines,
		'left'  => $suji_count >= 40 ? 1 : 0,
		'count' => $suji_count,
	) );
}
add_action( 'wp_ajax_suji_import_fix_titles', 'suji_import_ajax_fix_titles' );
