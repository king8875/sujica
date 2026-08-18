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
	$suji_queue = (array) get_option( SUJI_IMPORT_QUEUE, array() );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( '원본 사이트 가져오기', 'suji' ); ?></h1>
		<p class="description">
			sujica.or.kr 의 공개 페이지를 읽어, 아직 없는 글과 본문 이미지를 가져옵니다.
			이미 가져온 글은 원본 글번호로 걸러내므로 여러 번 눌러도 안전합니다.
		</p>

		<h2 class="title"><?php esc_html_e( '1. 연결 확인', 'suji' ); ?></h2>
		<p>
			<button class="button" id="suji-test"><?php esc_html_e( '원본 사이트 접속 확인', 'suji' ); ?></button>
			<span id="suji-test-out"></span>
		</p>

		<h2 class="title"><?php esc_html_e( '2. 빠진 글 찾기', 'suji' ); ?></h2>
		<p>
			<label><?php esc_html_e( '게시판별로 최근 몇 쪽까지 볼지', 'suji' ); ?>
				<input type="number" id="suji-pages" value="3" min="1" max="60" style="width:5em">
			</label>
			<button class="button button-secondary" id="suji-scan"><?php esc_html_e( '찾기', 'suji' ); ?></button>
			<span id="suji-scan-out"></span>
		</p>

		<h2 class="title"><?php esc_html_e( '3. 가져오기', 'suji' ); ?></h2>
		<p>
			<button class="button button-primary" id="suji-run"
			        <?php disabled( empty( $suji_queue ) ); ?>><?php esc_html_e( '가져오기 시작', 'suji' ); ?></button>
			<button class="button" id="suji-stop" disabled><?php esc_html_e( '멈춤', 'suji' ); ?></button>
			<span id="suji-run-out">
				<?php if ( $suji_queue ) : ?>
					<?php printf( esc_html__( '대기 중인 글 %d건', 'suji' ), count( $suji_queue ) ); ?>
				<?php endif; ?>
			</span>
		</p>
		<pre id="suji-log" style="max-height:22em;overflow:auto;background:#fff;border:1px solid #dcdcde;padding:.75em;margin-top:1em"></pre>
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

		document.getElementById('suji-test').addEventListener('click', function (e) {
			e.preventDefault();
			var out = document.getElementById('suji-test-out');
			out.textContent = ' 확인 중…';
			call('suji_import_test').then(function (r) {
				out.textContent = ' ' + r.data;
			});
		});

		document.getElementById('suji-scan').addEventListener('click', function (e) {
			e.preventDefault();
			var out = document.getElementById('suji-scan-out');
			out.textContent = ' 찾는 중… (게시판이 많아 시간이 걸립니다)';
			call('suji_import_scan', {pages: document.getElementById('suji-pages').value}).then(function (r) {
				out.textContent = ' ' + r.data.message;
				say(r.data.detail);
				document.getElementById('suji-run').disabled = r.data.count === 0;
			});
		});

		document.getElementById('suji-stop').addEventListener('click', function (e) {
			e.preventDefault();
			stop = true;
			say('— 멈춤 요청');
		});

		document.getElementById('suji-run').addEventListener('click', function (e) {
			e.preventDefault();
			stop = false;
			document.getElementById('suji-stop').disabled = false;
			step();
		});

		function step() {
			if (stop) { document.getElementById('suji-stop').disabled = true; return; }
			call('suji_import_run').then(function (r) {
				(r.data.lines || []).forEach(say);
				document.getElementById('suji-run-out').textContent =
					' 남은 글 ' + r.data.left + '건';
				if (r.data.left > 0) {
					setTimeout(step, 300);
				} else {
					say('— 모두 가져왔습니다.');
					document.getElementById('suji-stop').disabled = true;
					document.getElementById('suji-run').disabled = true;
				}
			}).catch(function (err) {
				say('— 통신 오류: ' + err + ' (다시 시작을 누르면 이어서 진행합니다)');
				document.getElementById('suji-stop').disabled = true;
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
	wp_send_json_success( sprintf( '성공 — 목록에서 %d건을 읽었습니다.', count( $suji_items ) ) );
}
add_action( 'wp_ajax_suji_import_test', 'suji_import_ajax_test' );

/**
 * 게시판별로 목록을 훑어 아직 없는 글을 큐에 담는다.
 */
function suji_import_ajax_scan() {
	suji_import_check();

	$suji_pages = max( 1, min( 60, (int) ( $_POST['pages'] ?? 3 ) ) );
	$suji_map   = suji_import_map();
	$suji_queue = array();
	$suji_lines = array();

	foreach ( $suji_map as $suji_bo => $suji_info ) {
		$suji_missing = 0;
		$suji_index   = suji_import_existing( $suji_info['type'] );

		for ( $suji_p = 1; $suji_p <= $suji_pages; $suji_p++ ) {
			$suji_body = suji_import_get(
				SUJI_IMPORT_ORIGIN . '/bbs/board.php?bo_table=' . $suji_bo . '&page=' . $suji_p
			);
			if ( is_wp_error( $suji_body ) ) {
				$suji_lines[] = sprintf( '%s %s쪽: %s', $suji_bo, $suji_p, $suji_body->get_error_message() );
				break;
			}

			$suji_items = suji_import_parse_list( $suji_body );
			if ( ! $suji_items ) {
				break;
			}

			foreach ( $suji_items as $suji_wr_id => $suji_title ) {
				if ( isset( $suji_index[ suji_import_key( $suji_title ) ] ) ) {
					continue;
				}
				if ( suji_import_found_by_wr_id( $suji_bo, $suji_wr_id ) ) {
					continue;
				}
				$suji_queue[] = array( 'bo' => $suji_bo, 'id' => $suji_wr_id );
				$suji_missing++;
			}
		}

		$suji_lines[] = sprintf( '%-10s 빠진 글 %d건', $suji_bo, $suji_missing );
	}

	update_option( SUJI_IMPORT_QUEUE, $suji_queue, false );

	wp_send_json_success( array(
		'count'   => count( $suji_queue ),
		'message' => sprintf( '모두 %d건을 가져올 예정입니다.', count( $suji_queue ) ),
		'detail'  => implode( "\n", $suji_lines ),
	) );
}
add_action( 'wp_ajax_suji_import_scan', 'suji_import_ajax_scan' );

/**
 * 큐에서 몇 건씩 처리한다.
 */
function suji_import_ajax_run() {
	suji_import_check();

	$suji_queue = (array) get_option( SUJI_IMPORT_QUEUE, array() );
	$suji_lines = array();
	$suji_batch = 2;   // 글마다 사진을 받아 올리므로 조금씩

	for ( $suji_i = 0; $suji_i < $suji_batch && $suji_queue; $suji_i++ ) {
		$suji_job = array_shift( $suji_queue );
		$suji_res = suji_import_one( $suji_job['bo'], $suji_job['id'] );
		$suji_lines[] = sprintf( '[%s #%s] %s', $suji_job['bo'], $suji_job['id'], $suji_res );
	}

	update_option( SUJI_IMPORT_QUEUE, $suji_queue, false );

	wp_send_json_success( array(
		'lines' => $suji_lines,
		'left'  => count( $suji_queue ),
	) );
}
add_action( 'wp_ajax_suji_import_run', 'suji_import_ajax_run' );
