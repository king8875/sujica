<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/**
 * 원본(sujica.or.kr/page/commitee.php)은 7개 탭이었다. 사무실 업무 페이지와
 * 같은 이유로 상단 목차 + 전체 펼침으로 두어 검색·링크 공유·인쇄가 되게 했다.
 * 역대 총회장도 캐러셀 대신 그리드로 전원을 보인다.
 */
$suji_committee = require SUJI_DIR . '/inc/committee-data.php';

$suji_volunteer_url = 'https://sujica.or.kr/bbs/write.php?bo_table=form';
?>

<main id="primary" class="site-main committee">

	<header class="cm-hero">
		<h1 class="cm-title">상임위원회</h1>
		<p class="cm-lead">주임신부님의 사목방침에 따라 본당을 운영하는 사목평의회의 자문기구입니다.</p>
	</header>

	<nav class="cm-nav" aria-label="위원회 바로가기">
		<?php foreach ( $suji_committee['sections'] as $suji_section ) : ?>
			<a class="cm-nav-link" href="#<?php echo esc_attr( $suji_section['slug'] ); ?>"><?php echo esc_html( $suji_section['title'] ); ?></a>
		<?php endforeach; ?>
	</nav>

	<?php foreach ( $suji_committee['sections'] as $suji_section ) : ?>
		<section class="cm-section" id="<?php echo esc_attr( $suji_section['slug'] ); ?>">
			<h2 class="cm-heading"><?php echo esc_html( $suji_section['title'] ); ?></h2>

			<?php if ( $suji_section['intro'] ) : ?>
				<p class="cm-intro"><?php echo esc_html( $suji_section['intro'] ); ?></p>
			<?php endif; ?>

			<?php if ( 'standing' === $suji_section['slug'] ) : ?>

				<div class="cm-block">
					<h3 class="cm-subheading">수지성당 조직표</h3>
					<figure class="cm-chart">
						<img src="<?php echo esc_url( SUJI_URI . '/assets/images/committee/commit-img.png' ); ?>"
						     alt="수지성당 조직표 — 주임신부 아래 총회장과 상임위원회, 그 아래 여성·남성소공동체위원회, 제분과위원회, 전례위원회, 평신도사도직단체협의회, 청소년위원회, 재정관리위원회, 시설관리위원회, 홍보팀"
						     width="630" height="840" loading="lazy">
					</figure>
				</div>

				<div class="cm-block">
					<h3 class="cm-subheading">
						역대 총회장
						<span class="cm-count"><?php echo count( $suji_committee['chairs'] ); ?>대</span>
					</h3>
					<ul class="cm-grid">
						<?php foreach ( $suji_committee['chairs'] as $suji_chair ) : ?>
							<?php $suji_now = (bool) preg_match( '/현재\s*$/u', $suji_chair['term'] ); ?>
							<li class="cm-card<?php echo $suji_now ? ' is-current' : ''; ?>">
								<div class="cm-photo">
									<img src="<?php echo esc_url( SUJI_URI . '/assets/images/committee/' . $suji_chair['photo'] ); ?>"
									     alt="<?php echo esc_attr( $suji_chair['name'] ); ?>" loading="lazy">
									<?php if ( $suji_now ) : ?>
										<span class="cm-badge">현재</span>
									<?php endif; ?>
								</div>
								<div class="cm-info">
									<span class="cm-rank"><?php echo esc_html( $suji_chair['rank'] ); ?></span>
									<p class="cm-name"><?php echo esc_html( $suji_chair['name'] ); ?></p>
									<p class="cm-term"><?php echo esc_html( $suji_chair['term'] ); ?></p>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

			<?php elseif ( 'small-community' === $suji_section['slug'] ) : ?>

				<div class="cm-block">
					<h3 class="cm-subheading">구역 지도</h3>
					<p class="cm-note-line">색 영역을 클릭하면 구역명을 확인하실 수 있습니다.</p>
					<div class="cm-map">
						<iframe
							src="https://www.google.com/maps/d/embed?mid=1cwmEsj5Xy0g7dwMRwRswCi3Gwj_OfjE&amp;ehbc=2E312F"
							title="수지성당 구역 지도"
							loading="lazy"
							referrerpolicy="no-referrer-when-downgrade"></iframe>
					</div>
				</div>

				<div class="cm-block">
					<h3 class="cm-subheading">구역명 찾기</h3>
					<p class="cm-note-line">도로명을 선택하면 해당하는 구역을 알려드립니다.</p>
					<div class="cm-lookup">
						<iframe src="<?php echo esc_url( SUJI_URI . '/assets/juso-lookup.html' ); ?>"
						        title="도로명으로 구역명 찾기" loading="lazy"></iframe>
					</div>
				</div>

				<div class="cm-block">
					<h3 class="cm-subheading">
						지역 · 구역
						<span class="cm-count"><?php echo count( $suji_committee['areas'] ); ?>개 지역 ·
							<?php
							$suji_total = 0;
							foreach ( $suji_committee['areas'] as $suji_area ) {
								$suji_total += count( $suji_area['items'] );
							}
							echo (int) $suji_total;
							?>개 구역</span>
					</h3>

					<div class="cm-areas">
						<?php foreach ( $suji_committee['areas'] as $suji_area ) : ?>
							<div class="cm-area">
								<h4 class="cm-area-name"><?php echo esc_html( $suji_area['name'] ); ?></h4>
								<dl class="cm-area-list">
									<?php foreach ( $suji_area['items'] as $suji_item ) : ?>
										<div class="cm-area-row">
											<dt><?php echo esc_html( $suji_item['name'] ); ?></dt>
											<dd><?php echo esc_html( $suji_item['range'] ); ?></dd>
										</div>
									<?php endforeach; ?>
								</dl>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

			<?php endif; ?>

			<?php if ( $suji_section['groups'] ) : ?>
				<div class="cm-block">
					<ul class="cm-groups">
						<?php foreach ( $suji_section['groups'] as $suji_group ) : ?>
							<li class="cm-group">
								<h3 class="cm-group-name"><?php echo esc_html( $suji_group['title'] ); ?></h3>
								<p class="cm-group-desc"><?php echo nl2br( esc_html( $suji_group['desc'] ) ); ?></p>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php if ( 'standing' !== $suji_section['slug'] && 'small-community' !== $suji_section['slug'] ) : ?>
				<a class="cm-volunteer" href="<?php echo esc_url( $suji_volunteer_url ); ?>"
				   target="_blank" rel="noopener noreferrer">
					봉사자 신청
					<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
					     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<line x1="5" y1="12" x2="19" y2="12"></line>
						<polyline points="13 6 19 12 13 18"></polyline>
					</svg>
				</a>
			<?php endif; ?>
		</section>
	<?php endforeach; ?>

</main>

<?php
get_footer();
