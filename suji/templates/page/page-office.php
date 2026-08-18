<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/**
 * 원본(sujica.or.kr/page/info.php)은 7개 탭 구조였다. 사무실 업무는 방문자가
 * 한 가지 절차만 찾아 읽는 조회형 내용이라, 탭 대신 상단 목차 + 전체 펼침으로
 * 두어 브라우저 검색·링크 공유·인쇄가 모두 되게 했다.
 */
$suji_office_nav = array(
	'work'            => '업무안내',
	'registry'        => '교적안내',
	'infant-baptism'  => '유아세례',
	'marriage'        => '혼인성사',
	'funeral'         => '장례',
	'sick'            => '병자영성체',
	'blessing'        => '축복(준성사)',
);
?>

<main id="primary" class="site-main office">

	<header class="of-hero">
		<h1 class="of-title">사무실 업무</h1>
		<p class="of-lead">교적·성사·예물·교무금 등 본당 사무실에서 처리하는 업무를 안내합니다.</p>
	</header>

	<nav class="of-nav" aria-label="업무 바로가기">
		<?php foreach ( $suji_office_nav as $suji_id => $suji_label ) : ?>
			<a class="of-nav-link" href="#<?php echo esc_attr( $suji_id ); ?>"><?php echo esc_html( $suji_label ); ?></a>
		<?php endforeach; ?>
	</nav>

	<!-- ------------------------------ 업무안내 ------------------------------ -->
	<section class="of-section" id="work">
		<h2 class="of-heading">업무안내</h2>

		<div class="of-work">
			<div class="of-card of-hours">
				<h3 class="of-subheading">근무시간</h3>
				<dl class="of-rows">
					<div class="of-row">
						<dt>주일</dt>
						<dd>07:00 ~ 20:00</dd>
					</div>
					<div class="of-row">
						<dt>토요일</dt>
						<dd>09:00 ~ 20:00</dd>
					</div>
					<div class="of-row">
						<dt>평일</dt>
						<dd>
							<span class="of-day"><b>월</b> 휴무</span>
							<span class="of-day"><b>화 · 목</b> 09:00 ~ 20:30</span>
							<span class="of-day"><b>수</b> 09:00 ~ 18:00</span>
							<span class="of-day"><b>금</b> 09:00 ~ 17:00</span>
						</dd>
					</div>
					<div class="of-row">
						<dt>공휴일</dt>
						<dd>
							<span class="of-day"><b>화 · 목</b> 18:00 ~ 20:30</span>
							<span class="of-day"><b>수 · 금</b> 09:00 ~ 12:00</span>
						</dd>
					</div>
					<div class="of-row">
						<dt>점심시간</dt>
						<dd>
							<span class="of-day"><b>월~토</b> 12:00 ~ 13:30</span>
							<span class="of-day"><b>주일</b> 12:30 ~ 14:00</span>
						</dd>
					</div>
				</dl>
			</div>

			<div class="of-side">
				<div class="of-card of-contact">
					<h3 class="of-subheading">연락처</h3>
					<ul class="of-contact-list">
						<li>
							<span class="of-contact-label">전화</span>
							<a href="tel:031-265-2101">031-265-2101~2</a>
						</li>
						<li>
							<span class="of-contact-label">FAX</span>
							<span>031-265-2103</span>
						</li>
						<li>
							<span class="of-contact-label">E-mail</span>
							<a href="mailto:suji@casuwon.or.kr">suji@casuwon.or.kr</a>
						</li>
					</ul>
				</div>

				<div class="of-card">
					<h3 class="of-subheading">주요업무</h3>
					<p class="of-text">교적 정리, 성사에 관한 업무, 미사예물 접수, 교무금 및 재정(회계)에 관한 업무, 기타.</p>
				</div>
			</div>
		</div>
	</section>

	<!-- ------------------------------ 교적안내 ------------------------------ -->
	<section class="of-section" id="registry">
		<h2 class="of-heading">교적안내</h2>

		<p class="of-intro">
			교적은 신자의 주민등록과 같은 것입니다. 세례, 견진, 혼인, 판공성사 등을 기록하고 관리합니다.
			이사를 할 경우 주소지 관할성당에서 전출과 전입을 신고해야 합니다.
		</p>

		<div class="of-block">
			<h3 class="of-subheading">전출입 교적정리</h3>
			<ul class="of-list">
				<li>전·출입 교적신청은 전입·전출자가 직접 사무실에 방문하여 신청하거나 전화로 신청합니다.</li>
				<li>전입교적은 &ldquo;이사온 주소, 전화번호(핸드폰), 지역구역반&rdquo;을 확인하며, 전출교적 발송은 &ldquo;이사 가는 주소, 전화번호(핸드폰), 이사가는 곳의 주소지 관할성당&rdquo;을 기재합니다.</li>
				<li>전입교우는 주일 교중미사 후 주임신부님과 전입면담을 통하여 신앙생활의 안내와 협조를 받습니다.</li>
			</ul>
		</div>

		<div class="of-block">
			<h3 class="of-subheading">새 교적작성</h3>
			<ul class="of-list">
				<li>세례성사를 받으면 세례대장을 근거로 교적이 작성됩니다.</li>
				<li>교적은 세례받은 본당에서 작성됨이 원칙이나 세례대장을 근거로 거주지 관할 본당에서도 교적을 작성할 수 있습니다.</li>
			</ul>
		</div>

		<div class="of-block">
			<h3 class="of-subheading">교적 재작성</h3>
			<ul class="of-list">
				<li>교적이 분실되거나 작성이 안된 경우 세례대장을 근거로 본당신부님의 재가를 받아서 교적을 재작성합니다.</li>
			</ul>
		</div>
	</section>

	<!-- ---------------------------- 유아세례안내 ---------------------------- -->
	<section class="of-section" id="infant-baptism">
		<h2 class="of-heading">유아세례안내</h2>

		<p class="of-intro">
			복음을 전하고 세례를 베풀 사명을 받은 교회는 일찍이 초세기부터 어른들뿐 아니라 어린이들에게까지 세례를 주어왔습니다.
			이유는 &ldquo;물과 성령으로 새로 나지 않으면 아무도 하느님 나라에 들어갈 수 없다.&rdquo;(요한 3, 5) 하신 주님의 말씀을
			어린이들에게도 적용된다고 알아들었기 때문이며, 어린이들도 원죄로 타락하고 더러워진 인간의 본성을 지니고 태어나므로
			세례를 통하여 죄에서 해방되어 하느님의 자녀로 다시 태어나야 하기 때문입니다.
			<span class="of-cite">가톨릭교회 교리서 1213항 · 1250항 참조</span>
		</p>
		<p class="of-intro">
			부모는 자녀들에게 생명을 주었으므로 그들을 교육할 지극히 중대한 의무와 권리가 있으며,
			따라서 신자 부모는 우선적으로 교회의 전승된 가르침에 따른 자녀들의 신앙 교육에 힘써야 할 소임이 있습니다.
			<span class="of-cite">교회법 제226조 2항 참조</span>
		</p>

		<div class="of-block">
			<h3 class="of-subheading">한국 천주교 사목지침서의 유아 세례 규정</h3>
			<div class="of-split">
				<figure class="of-figure">
					<img src="<?php echo esc_url( SUJI_URI . '/assets/images/infant-baptism.jpg' ); ?>"
					     alt="유아세례" width="180" height="185" loading="lazy">
				</figure>
				<ul class="of-list of-list-quote">
					<li>부모는 아기의 출생 후 될 수 있는 대로 빨리 세례받게 하여야 하고 100일을 넘기지 말아야 한다</li>
					<li>아기가 죽을 위험이 있으면 지체 없이 세례받게 하여야 한다. 아기는 그 부모가 비가톨릭 신자이거나 원치 않더라도 세례받게 할 수 있다</li>
					<li>버려진 아기나 주운 아기는 세례받은 사실이 불확실하면 세례받게 하여야 한다</li>
					<li>유산된 태아가 살아 있으면 기형이나 형태를 갖추지 못하였어도 세례받게 하여야 한다</li>
				</ul>
			</div>
		</div>

		<div class="of-block">
			<h3 class="of-subheading">사전 신청안내</h3>
			<ul class="of-list">
				<li><b>3, 6, 9, 12월 첫 토요일 오전 11:00</b> 성당에서 유아세례가 있습니다.</li>
				<li>1세 ~ 만 7세까지의 유아가 해당되며 부모 중 한 분이라도 수지성당에 교적이 있는 신자이어야 합니다.</li>
				<li>사무실에서 성사생활과 교무금 납부 확인을 해주시기 바랍니다.</li>
				<li>아이의 세례명을 정하시고, 대부 또는 대모님을 정하셔서 유아세례 하루 전날까지 사무실에 신청서를 작성하여 제출해 주시기 바랍니다.</li>
			</ul>

			<a class="of-download" href="<?php echo esc_url( SUJI_URI . '/assets/files/infant-baptism-application.hwp' ); ?>" download>
				<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
				     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M12 3v12"></path>
					<polyline points="7 10 12 15 17 10"></polyline>
					<path d="M5 19h14"></path>
				</svg>
				<span class="of-download-name">유아세례 신청서</span>
				<span class="of-download-meta">HWP · 53KB</span>
			</a>
		</div>

		<div class="of-block">
			<h3 class="of-subheading">당일 준비사항</h3>
			<ul class="of-list">
				<li>개인초는 본당에서 준비해 드리며 가족께서는 미사보, 가톨릭성가집, 그리고 신부님과 기념촬영하실 분은 카메라를 준비하시기 바랍니다.</li>
				<li>세례 당일 30분 전까지 사무실로 오셔서 명찰을 받으시기 바랍니다.</li>
				<li>대부모께서는 15분 전까지 오시기 바랍니다.</li>
			</ul>
		</div>

		<div class="of-block">
			<h3 class="of-subheading">유아세례를 받은 후</h3>
			<ul class="of-list">
				<li>7세부터는 주일학교에 등록하시어 어린이에 맞는 신앙생활을 할 수 있도록 도와줍니다.</li>
				<li>초등학교 3학년 때 첫영성체교리를 통하여 첫영성체식을 하게 됩니다.</li>
			</ul>
		</div>
	</section>

	<!-- ---------------------------- 혼인성사안내 ---------------------------- -->
	<section class="of-section" id="marriage">
		<h2 class="of-heading">혼인성사안내</h2>

		<p class="of-intro">
			그리스도교 신자인 남녀가 서로 사랑하면서 한 가정을 이루어 행복한 생활을 하도록 하느님의 축복을 받는 성사입니다.
			교회의 7성사 가운데 다른 성사들은 그것을 받는 사람의 개인적인 성사라면, 혼인성사는 결혼을 통하여 한몸을 이루고
			부부로 맺어지는 남녀가 공동으로 받는 성사인 것입니다. 그러므로 혼인성사는 남녀가 결합하여 이루는 한 가정
			공동체를 위한 은사이며, 남편과 아내의 역할을 잘 수행할 수 있게 해줍니다.
		</p>

		<!-- 원본은 1318px 짜리 GIF 도식이었다. 좁은 화면에서 글자가 읽히지 않고
		     검색도 되지 않아 같은 내용을 마크업으로 다시 만들었다. -->
		<div class="of-block">
			<h3 class="of-subheading">혼인성사의 절차</h3>
			<ol class="of-steps">
				<li class="of-step">
					<span class="of-step-no">Step 1</span>
					<b class="of-step-name">면담신청</b>
					<span class="of-step-desc">본당 사무실에서 신부님과의 면담을 신청합니다.</span>
				</li>
				<li class="of-step">
					<span class="of-step-no">Step 2</span>
					<b class="of-step-name">서류준비</b>
					<span class="of-step-desc">혼인성사에 필요한 서류를 준비합니다.</span>
				</li>
				<li class="of-step">
					<span class="of-step-no">Step 3</span>
					<b class="of-step-name">혼인교리</b>
					<span class="of-step-desc">교구에서 실시하는 혼인교리에 참여합니다.</span>
				</li>
				<li class="of-step">
					<span class="of-step-no">Step 4</span>
					<b class="of-step-name">면담</b>
					<span class="of-step-desc">신부님과 면담</span>
				</li>
				<li class="of-step">
					<span class="of-step-no">Step 5</span>
					<b class="of-step-name">혼인성사</b>
					<span class="of-step-desc">주례신부님을 모시고 혼인성사를 받습니다.</span>
				</li>
			</ol>
		</div>

		<h3 class="of-part">혼인성사</h3>

		<div class="of-block">
			<h3 class="of-subheading">혼인서류준비</h3>
			<ul class="of-list">
				<li>혼인을 위해 필요한 서류를 준비한 후, 교적이 있는 본당 신부님과 면담하여 완비된 혼인 서류를 <b>혼인 예정일 15일 전까지</b> 제출해 주십시오.</li>
				<li>
					구비서류 (모든 혼배서류는 원본으로 준비하세요.)
					<ol class="of-sublist">
						<li>혼인관계증명서 &mdash; 상세 <span class="of-note">이혼경력 유무 포함, 동주민센터 또는 인터넷 발급, 남 · 녀 각각</span></li>
						<li>혼인 강좌 이수증</li>
						<li>세례 증명서 <span class="of-note">사무실</span></li>
						<li>혼인 신청서 <span class="of-note">사무실</span></li>
					</ol>
				</li>
				<li>혼인 강좌는 교육 일주일 전 월요일까지 온라인으로 신청합니다.</li>
			</ul>

			<div class="of-actions">
				<a class="of-download" href="<?php echo esc_url( SUJI_URI . '/assets/files/marriage-application.hwp' ); ?>" download>
					<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
					     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<path d="M12 3v12"></path>
						<polyline points="7 10 12 15 17 10"></polyline>
						<path d="M5 19h14"></path>
					</svg>
					<span class="of-download-name">혼인 신청서</span>
					<span class="of-download-meta">HWP · 12KB</span>
				</a>

				<a class="of-link" href="http://family.casuwon.or.kr/" target="_blank" rel="noopener noreferrer">
					혼인 강좌 신청
					<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
					     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
						<line x1="5" y1="12" x2="19" y2="12"></line>
						<polyline points="13 6 19 12 13 18"></polyline>
					</svg>
				</a>
			</div>
		</div>

		<div class="of-block">
			<h3 class="of-subheading">혼인 면담 및 성사</h3>
			<ul class="of-list">
				<li>화 · 목요일 저녁미사 전후 또는 토요일 오전 <span class="of-note">사무실과 협의 후 조정 가능</span></li>
			</ul>
		</div>

		<div class="of-block">
			<h3 class="of-subheading">혼인 성사 당일 준비사항</h3>
			<ul class="of-list">
				<li><b>증인</b> 신자 여부 및 성별과 관계없이, 직계 가족이 아닌 분으로 신랑 측과 신부 측에서 각각 1명씩 선정</li>
				<li><b>예물 반지</b> 남 · 여 각 1개</li>
				<li>혼인 성사 예물 준비 <span class="of-note">사무실로 접수</span></li>
			</ul>
		</div>

		<h3 class="of-part">혼배미사</h3>

		<div class="of-block">
			<h3 class="of-subheading">혼인예약</h3>
			<ul class="of-list">
				<li><b>예약가능시간</b> 시간 및 요일 사무실과 협의 후 정함</li>
				<li><b>장소안내</b> 미사 &mdash; 3층 대성당 / 신부 대기실 &mdash; 3층 유아실 / 폐백 &mdash; 2층 유아실 / 피로연 &mdash; 지하 식당</li>
				<li><b>예약 및 접수</b> 한 달 전까지 사무실로 문의 <a href="tel:031-265-2101">031-265-2101</a></li>
				<li>타 본당 신자의 경우 장소만 제공되며, 주례 사제는 직접 섭외해야 합니다. 또한 소속 본당에서 혼인 면담을 한 후, 혼배 미사 일주일 전까지 혼인 서류를 수지성당 사무실에 제출해야 합니다.</li>
			</ul>
		</div>

		<div class="of-block">
			<h3 class="of-subheading">혼인 면담</h3>
			<ul class="of-list">
				<li>
					준비서류
					<ol class="of-sublist">
						<li>혼인 신청서 <span class="of-note">사무실 또는 아래에서 내려받기</span></li>
						<li>세례 증명서 <span class="of-note">사무실</span></li>
						<li>혼인관계증명서(상세) <span class="of-note">남 · 녀 각각 1통, 동주민센터 또는 온라인 발급</span></li>
						<li>혼인강좌 이수증</li>
					</ol>
				</li>
				<li>혼배미사 한 달 전 서류 접수 후 일정 조율하여 신부님과 면담</li>
			</ul>
		</div>

		<div class="of-block">
			<h3 class="of-subheading">혼인비용</h3>
			<p class="of-text">계약 시 50만원을 납부하고 잔금은 혼배미사 당일 납부합니다.</p>

			<div class="of-table-wrap">
				<table class="of-table">
					<thead>
					<tr>
						<th scope="col">내용</th>
						<th scope="col">비용</th>
						<th scope="col">비고</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td data-label="내용">제대꽃</td>
						<td data-label="비용">800,000원</td>
						<td data-label="비고"></td>
					</tr>
					<tr>
						<td data-label="내용">성가대</td>
						<td data-label="비용">400,000원</td>
						<td data-label="비고"></td>
					</tr>
					<tr>
						<td data-label="내용">시설사용료</td>
						<td data-label="비용">1,000,000원</td>
						<td data-label="비고">냉난방 포함</td>
					</tr>
					<tr>
						<td data-label="내용">미사예물</td>
						<td data-label="비용">300,000원</td>
						<td data-label="비고"></td>
					</tr>
					<tr>
						<td data-label="내용">주례 감사예물</td>
						<td data-label="비용">정성껏</td>
						<td data-label="비고">주례사제께 직접 봉헌</td>
					</tr>
					<tr>
						<td data-label="내용">꽃 길</td>
						<td data-label="비용">이벤트 업체에서 직접장식</td>
						<td data-label="비고"></td>
					</tr>
					<tr>
						<td data-label="내용">피로연장 시설사용료</td>
						<td data-label="비용">관례대로</td>
						<td data-label="비고">피로연 업체에서 봉헌</td>
					</tr>
					<tr>
						<td data-label="내용">폐 백</td>
						<td data-label="비용">장소만 제공</td>
						<td data-label="비고">이벤트 업체에서 수행</td>
					</tr>
					<tr class="of-table-total">
						<td data-label="내용">합계</td>
						<td data-label="비용">2,500,000원</td>
						<td data-label="비고">2024년 기준</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="of-block">
			<h3 class="of-subheading">혼인성사 당일 준비사항</h3>
			<ul class="of-list">
				<li><b>증인</b> 신자 여부 및 성별과 관계없이 직계 가족이 아닌 분으로 신랑 측과 신부 측에서 각각 1명씩 선정</li>
				<li><b>혼인반지</b> 미사 전 수녀님께 전달 &mdash; 14K 또는 금반지 <span class="of-note">고가가 아닌 반지 권장</span></li>
				<li>방명록, 예식용 장갑, 부케(혼주용 코사지 포함), 축의금 봉투, 폐백음식 &mdash; 혼주께서 직접 준비하십니다.</li>
			</ul>
		</div>

		<div class="of-block">
			<h3 class="of-subheading">기타 협조사항</h3>
			<ul class="of-list">
				<li>성당 내에서 예의를 갖춰주시고, 미사 중 정숙해 주시기 바랍니다.</li>
				<li>화환은 1층에 진열할 수 있으며, 미사 후 직접 수거하시거나 별도의 처리비용(개당 만원)을 부담하셔야 합니다.</li>
				<li>혼배 미사 중 과도한 개인 사진 촬영 및 제대 위에서의 촬영은 삼가 주시기 바랍니다.</li>
			</ul>
		</div>
	</section>

	<!-- ------------------------------ 장례안내 ------------------------------ -->
	<section class="of-section" id="funeral">
		<h2 class="of-heading">장례안내</h2>

		<div class="of-card of-quote-card">
			<h3 class="of-subheading">장례미사란?</h3>
			<p class="of-text">
				그리스도교 장례는 두 가지 주제를 반영합니다.
				첫째 주제는 죽은 이의 지상 여정이 완성되고 그리스도와 일치가 시작되는 기쁨입니다.
				둘째 주제는 산 이들의 기도와 성찬례에 의해 그들이 곧 안식을 누릴 것이라는 희망입니다.
				성찬례는 죽은 이들을 위한 기도와 청원으로 그들에게 도움을 주고, 산 이들에게는 위로와 희망을 줍니다.
			</p>
			<p class="of-text">
				그리스도교 장례 미사는 죽은 이를 위해 거행하는 예식 가운데 매우 중요한 예식입니다.
				그래서 장례 미사는 의무 축일과 대림 시기, 사순 시기, 부활 시기의 주일, 재의 수요일, 성삼일에만 금지됩니다.
				사망일과 장례일 그리고 1주기에는 의무 기념일에도 장례 미사가 허용됩니다.
				의무 기념일보다 낮은 등급의 미사가 봉헌될 경우 연중 시기에 매일 죽은 이를 위한 연미사를 드릴 수 있습니다.
			</p>
		</div>

		<div class="of-block">
			<h3 class="of-subheading">선종 시 절차</h3>
			<ul class="of-list">
				<li>
					가족 선종 시 유가족께서는 고인의 교적상 주소, 선종일시, 빈소 등을 확인 후
					연령회 또는 사무실로 연락합니다.
					<span class="of-note">연령회장 <a href="tel:010-4227-1544">010-4227-1544</a> · 사무실 <a href="tel:031-265-2101">031-265-2101</a></span>
				</li>
				<li>
					연령회와 장례절차에 대한 사항을 협의합니다.
					<span class="of-note">입관 · 출관 · 장례미사 등 전례에 관한 사항 / 화장 · 매장 · 천주교 묘원 안장 등에 필요한 서류 및 절차</span>
				</li>
			</ul>
			<p class="of-notice">수지 본당 교우의 선종 시에는 단체 및 구역반에 공지하여 고인을 위한 연도가 진행됩니다.</p>
		</div>
	</section>

	<!-- --------------------------- 병자영성체안내 --------------------------- -->
	<section class="of-section" id="sick">
		<h2 class="of-heading">병자영성체안내</h2>

		<p class="of-intro">
			질병 때문에 육체적, 정신적으로 중대한 위험에 처한 환자들에게 병을 이겨나갈 힘과 용기를 불어넣어주는 성사입니다.
			사람의 몸과 마음이 최고의 상태를 유지할 수 없는 경우 그 &lsquo;치유의 성사&rsquo;가
			&lsquo;고해성사&rsquo;와 &lsquo;병자성사(종부성사)&rsquo;입니다.
		</p>

		<div class="of-card">
			<ul class="of-list">
				<li>노령 또는 병환 중인 교우들을 신부님께서 가정으로 방문하여 월 1회 성체를 모실 수 있도록 합니다.</li>
			</ul>
			<p class="of-notice">병자영성체 일시 &mdash; 매월 첫 금요일 14시부터 <span class="of-note">1월과 8월은 쉽니다</span></p>
		</div>
	</section>

	<!-- -------------------------- 축복(준성사)안내 -------------------------- -->
	<section class="of-section" id="blessing">
		<h2 class="of-heading">축복(준성사)안내</h2>

		<p class="of-intro">
			준성사는 교회가 신자들의 영적인 이익을 돕기 위하여 교회가 만든 것으로 우리의 삶에서 그리스도의 현존을 더욱 깊이
			알아차리도록 도와주는 것들이나 행동을 말합니다. 준성사는 성사를 풍요롭게 하고 성사를 준비하는 과정이라고 말할 수 있고,
			성사의 은총을 보존시키는 데 도움이 되는 것이라 할 수 있습니다.
		</p>
		<p class="of-intro">
			준성사 중 축복은 사람과 물건에 대해서 하느님의 특별한 은총과 복을 비는 것입니다.
			물건의 축복은 물건을 축복함으로써 그 물건을 통하여 또는 사용함으로써 사용하는 사람에게 교회가 지향하는
			하느님의 축복이 전해지기를 비는 것입니다. 십자고상이나 성상, 성수, 묵주, 메달 등을 축복하고 집, 차, 배, 농장,
			사무실, 점포, 기계 등의 물건을 축복하는 이유는 그것들을 하느님께 봉헌하고, 교회의 중재로 하느님의 축복을 비는 데 있습니다.
		</p>

		<div class="of-block">
			<h3 class="of-subheading">차축복</h3>
			<ul class="of-list">
				<li>교적이 있는 신자 누구나 사무실에 오셔서 미리 신청해 주시고, 차축복 예물은 정성껏 사무실에 당일 접수해 주십시오.</li>
			</ul>
		</div>

		<div class="of-block">
			<h3 class="of-subheading">집 · 상가축복</h3>
			<ul class="of-list">
				<li>교적이 있는 신자께서 사무실에 신청해 주시기 바랍니다.</li>
				<li>축복 예물은 정성껏 사무실에 당일 접수해 주십시오.</li>
			</ul>
		</div>
	</section>

</main>

<?php
get_footer();
