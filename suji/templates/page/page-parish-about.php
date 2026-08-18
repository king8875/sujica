<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="primary" class="site-main parish-about">

	<header class="pa-hero">
		<h1 class="pa-title">수지 본당 소개</h1>
		<p class="pa-lead">천주교 수원교구 제1대리구 수지(성 장 베르뇌 시메온) 성당</p>
	</header>

	<div class="pa-body">

		<!-- ------------------------------ 본당 개요 ------------------------------ -->
		<section class="pa-section">
			<h2 class="pa-heading">본당 개요</h2>

			<div class="pa-profile">
				<figure class="pa-photo">
					<img src="<?php echo esc_url( SUJI_URI . '/assets/images/parish-intro.jpg' ); ?>"
					     alt="수지성당 성모자 상" width="192" height="313" loading="lazy">
				</figure>

				<dl class="pa-facts">
					<div class="pa-fact">
						<dt>설립일자</dt>
						<dd>1994년 2월 3일</dd>
					</div>
					<div class="pa-fact">
						<dt>봉헌일자</dt>
						<dd>2006년 5월 20일 <span class="pa-fact-sub">주례 최덕기 바오로 주교</span></dd>
					</div>
					<div class="pa-fact pa-fact-wide">
						<dt>소재지</dt>
						<dd>경기도 용인시 수지구 수지로 265 <span class="pa-fact-sub">풍덕천2동 436-5</span></dd>
					</div>
					<div class="pa-fact">
						<dt>주보성인</dt>
						<dd>성 장 베르뇌 시메온 <span class="pa-fact-sub">축일 9월 20일</span></dd>
					</div>
					<div class="pa-fact">
						<dt>모본당</dt>
						<dd>제1대리구 기흥지구 신갈성당</dd>
					</div>
					<div class="pa-fact">
						<dt>유해현황</dt>
						<dd>성 장 베르뇌 시메온</dd>
					</div>
					<div class="pa-fact">
						<dt>분배일자</dt>
						<dd>2006년 3월 27일</dd>
					</div>
					<div class="pa-fact">
						<dt>신자수</dt>
						<dd>6,596명 <span class="pa-fact-sub">2025년 현재</span></dd>
					</div>
				</dl>
			</div>
		</section>

		<!-- --------------------------- 위치와 관할구역 --------------------------- -->
		<section class="pa-section">
			<h2 class="pa-heading">수지성당 위치와 관할구역</h2>

			<div class="pa-map">
				<iframe
					src="https://www.google.com/maps/d/embed?mid=1ZaNT0QiFdJf4dQd6qUZaowF6iO9ImXo&amp;ehbc=2E312F"
					title="수지성당 위치와 관할구역 지도"
					loading="lazy"
					referrerpolicy="no-referrer-when-downgrade"></iframe>
			</div>

			<p class="pa-map-note">경기도 용인시 수지구 수지로 265 (풍덕천2동 436-5)</p>
		</section>

		<!-- --------------------------- 성모신심 미사 --------------------------- -->
		<section class="pa-section">
			<h2 class="pa-heading">성모신심 미사 안내</h2>

			<div class="pa-card">
				<p class="pa-notice">성모신심 미사는 <strong>매월 첫 토요일 10시</strong>에 봉헌됩니다.</p>
				<p class="pa-text">
					성모마리아는 교회의 전형이시며, 신앙의 모범으로서 우리를 대신하여 하느님께 기도해주십니다.
					우리는 성모님을 사랑하고 공경하며 성모님께 의탁하는 신심을 통하여
					하느님께 대한 우리의 사랑을 주저없이 바쳐드립니다.
				</p>
			</div>
		</section>

		<!-- ----------------------------- 성체강복 ----------------------------- -->
		<section class="pa-section">
			<h2 class="pa-heading">성체강복 안내</h2>

			<div class="pa-card">
				<p class="pa-notice">매월 <strong>첫째 주 목요일 19:30 미사 후</strong> 대성당에서 있습니다.</p>

				<a class="pa-download"
				   href="<?php echo esc_url( SUJI_URI . '/assets/files/sungchae.hwp' ); ?>" download>
					<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
					     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M12 3v12"></path>
						<polyline points="7 10 12 15 17 10"></polyline>
						<path d="M5 19h14"></path>
					</svg>
					<span class="pa-download-name">성체현시와 성체강복</span>
					<span class="pa-download-meta">HWP · 32KB</span>
				</a>
			</div>

			<details class="pa-details">
				<summary class="pa-details-summary">
					<span>성체 현시 및 강복 &mdash; 정의와 교회의 가르침</span>
					<svg class="pa-details-icon" viewBox="0 0 24 24" width="20" height="20" fill="none"
					     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
					     aria-hidden="true">
						<polyline points="6 9 12 15 18 9"></polyline>
					</svg>
				</summary>

				<div class="pa-details-body">
					<article class="pa-doc">
						<h3 class="pa-doc-title">Ⅰ. 정의 및 유래</h3>
						<p>
							가톨릭 교회는 초대교회 공동체 때부터 성체(Eucharistia)께 대한 특별한 공경과 경외를 드렸으며,
							이에 따라 성체조배, 성체현시, 성체강복, 성체행렬 등 다양한 성체경배 예식이 생겨나 발전되었다.
							이중에서 성체 현시와 강복은 공동체가 함께 모여 성체조배를 하고 사제가 성체를 성광에 모시고
							분향함으로써 성체께 특별한 찬미와 공경을 드리는 예식이다.
						</p>
					</article>

					<article class="pa-doc">
						<h3 class="pa-doc-title">Ⅱ. 교회의 가르침</h3>
						<p>
							1551년의 뜨리덴틴 공의회는 &ldquo;성체성사에 관한 교령&rdquo;을 통해
							&lsquo;빵과 포도주 안에 실제적으로 현존하시는 그리스도&rsquo;(DS 1652)라는 교의를 확정 수용하였고,
							제2차 바티칸 공의회에서는 &lsquo;그리스도교 생활 전체의 원천이요 절정&rsquo;으로서 성찬의 제사를 이해하며,
							또한 &ldquo;성찬 때에 그리스도의 몸을 받아들임으로써 신자들은 하느님 백성의 일치를 구체적으로 표현한다.&rdquo;고
							가르친다.
						</p>
						<p>
							또한 공의회는 근본적으로 그리스도의 제사 거행에 연결된 방식으로만 성체공경을 실천하도록 촉구하면서,
							&ldquo;전례는 어떤 신심행사보다 우월하므로 그리스도 신자들의 신심행사는 전례와 조화되고 어느 정도
							전례에서 유래되며 또한 신자들을 전례에로 인도하도록 마련되어야 한다&rdquo;고 가르치고 있다(전례헌장 13항).
						</p>
						<p>
							그러므로 성체현시 동안에는 같은 성당 안에서 미사를 거행하지 말아야 한다.
							장시간의 장엄한 현시는 미사 때 현시를 위한 성체를 함께 축성하여 그것을 성광(Ostenorium)에 모시고 시작한다.
							성체현시 중에 기도, 성가, 성경봉독, 충분한 시간의 침묵기도 등을 거행한다.
							현시는 성체를 들어 신자들에게 강복을 줌으로써 끝나며, 강복을 주기 전에 적당한 성가와 기도를 바친다.
							강복 후에 성체는 다시 감실로 모신다.
						</p>
					</article>

					<article class="pa-doc">
						<p>
							기도를 곁들인다면 짧은 시간의 현시와 강복도 가능하나 강복만을 위한 현시는 허락되지 않는다.
							성체현시를 거행할 수 있는 부제 이상의 성직자가 없으면 지방 주교의 허락을 받은 평신도가
							성체를 현시하고 성체를 다시 감실로 모실 수 있다. 단, 강복은 사제만이 할 수 있다.
						</p>
					</article>
				</div>
			</details>
		</section>

	</div>
</main>

<?php
get_footer();
