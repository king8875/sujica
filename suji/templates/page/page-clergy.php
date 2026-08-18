<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$suji_page_id  = get_queried_object_id();
$suji_sections = suji_clergy_sections();

// 섹션별 행을 미리 모아 둔다 — 목차에 인원수를 함께 보여주기 위해서.
foreach ( $suji_sections as $suji_i => $suji_section ) {
	$suji_sections[ $suji_i ]['rows'] = suji_clergy_rows( $suji_section['slug'], $suji_page_id );
}
?>

<main id="primary" class="site-main clergy">

	<header class="cl-hero">
		<h1 class="cl-title">성직자 및 수도자</h1>
		<p class="cl-lead">1994년 본당 설립 이후 수지 본당에서 사목하신 분들입니다.</p>
	</header>

	<nav class="cl-nav" aria-label="섹션 바로가기">
		<?php foreach ( $suji_sections as $suji_section ) : ?>
			<?php if ( ! $suji_section['rows'] ) { continue; } ?>
			<a class="cl-nav-link" href="#<?php echo esc_attr( $suji_section['slug'] ); ?>">
				<?php echo esc_html( $suji_section['title'] ); ?>
				<span class="cl-nav-count"><?php echo count( $suji_section['rows'] ); ?></span>
			</a>
		<?php endforeach; ?>
	</nav>

	<?php foreach ( $suji_sections as $suji_section ) : ?>
		<?php if ( ! $suji_section['rows'] ) { continue; } ?>

		<section class="cl-section" id="<?php echo esc_attr( $suji_section['slug'] ); ?>">
			<h2 class="cl-heading">
				<?php echo esc_html( $suji_section['title'] ); ?>
				<span class="cl-heading-count"><?php echo count( $suji_section['rows'] ); ?>명</span>
			</h2>

			<ul class="cl-grid">
				<?php foreach ( $suji_section['rows'] as $suji_row ) : ?>
					<?php $suji_current = suji_clergy_is_current( $suji_row['term'] ); ?>
					<li class="cl-card<?php echo $suji_current ? ' is-current' : ''; ?>">
						<div class="cl-photo">
							<?php if ( $suji_row['photo'] ) : ?>
								<img src="<?php echo esc_url( $suji_row['photo'] ); ?>"
								     alt="<?php echo esc_attr( $suji_row['name'] ); ?>" loading="lazy">
							<?php endif; ?>
							<?php if ( $suji_current ) : ?>
								<span class="cl-badge">현재</span>
							<?php endif; ?>
						</div>

						<div class="cl-info">
							<?php if ( $suji_row['rank'] ) : ?>
								<span class="cl-rank"><?php echo esc_html( $suji_row['rank'] ); ?></span>
							<?php endif; ?>

							<p class="cl-name"><?php echo esc_html( $suji_row['name'] ); ?></p>

							<?php if ( $suji_row['order'] ) : ?>
								<p class="cl-order"><?php echo esc_html( $suji_row['order'] ); ?></p>
							<?php endif; ?>

							<p class="cl-term">
								<?php if ( 'native' === $suji_section['kind'] ) : ?>
									<span class="cl-term-label">서품일</span>
								<?php endif; ?>
								<?php echo esc_html( $suji_row['term'] ); ?>
							</p>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endforeach; ?>

</main>

<?php
get_footer();
