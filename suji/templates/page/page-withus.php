<?php
if (!defined('ABSPATH')) {
    exit;
}

$suji_page_id = get_queried_object_id();

/**
 * Normalise a file field value (array / attachment ID / plain URL) into
 * a url + filename pair.
 */
function suji_withus_file_info($suji_file)
{
    $suji_info = array('url' => '', 'name' => '');

    if (is_array($suji_file)) {
        $suji_info['url'] = $suji_file['url'] ?? '';
        $suji_info['name'] = $suji_file['filename'] ?? ($suji_file['title'] ?? '');
    } elseif (is_numeric($suji_file)) {
        $suji_info['url'] = (string)wp_get_attachment_url((int)$suji_file);
        $suji_info['name'] = basename($suji_info['url']);
    } elseif (is_string($suji_file) && '' !== trim($suji_file)) {
        $suji_info['url'] = trim($suji_file);
        $suji_info['name'] = basename(parse_url($suji_info['url'], PHP_URL_PATH));
    }

    return $suji_info;
}

/**
 * Treat a plain URL as a file when it points at a downloadable asset, so
 * external file links (e.g. an .m4a on another server) get the file styling
 * without needing the file field.
 */
function suji_withus_url_is_file($suji_url)
{
    $suji_path = parse_url($suji_url, PHP_URL_PATH);
    if (!$suji_path) {
        return false;
    }

    $suji_ext = strtolower(pathinfo($suji_path, PATHINFO_EXTENSION));

    $suji_file_exts = array(
            'pdf', 'hwp', 'hwpx', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt',
            'zip', 'rar', '7z',
            'mp3', 'm4a', 'wav', 'ogg', 'flac',
            'mp4', 'mov', 'avi', 'wmv', 'mkv',
            'jpg', 'jpeg', 'png', 'gif', 'webp',
    );

    return in_array($suji_ext, $suji_file_exts, true);
}

/**
 * Build the group list. Preferred shape is the nested repeater
 * `withus_groups` → `group_items`. Falls back to the older flat
 * `withus_links` repeater, and finally to raw post meta arrays.
 */
$suji_groups = array();

if (function_exists('have_rows') && have_rows('withus_groups', $suji_page_id)) {

    while (have_rows('withus_groups', $suji_page_id)) {
        the_row();

        $suji_group = array(
                'title' => (string)get_sub_field('group_title'),
                'items' => array(),
        );

        if (have_rows('group_items')) {
            while (have_rows('group_items')) {
                the_row();
                $suji_group['items'][] = array(
                        'text' => (string)get_sub_field('item_text'),
                        'url' => (string)get_sub_field('item_url'),
                        'file' => suji_withus_file_info(get_sub_field('item_file')),
                );
            }
        }

        $suji_groups[] = $suji_group;
    }
} elseif (function_exists('have_rows') && have_rows('withus_links', $suji_page_id)) {

    $suji_legacy = array('title' => '', 'items' => array());

    while (have_rows('withus_links', $suji_page_id)) {
        the_row();
        $suji_legacy['items'][] = array(
                'text' => (string)get_sub_field('link_text'),
                'url' => (string)get_sub_field('link_url'),
                'file' => array('url' => '', 'name' => ''),
        );
    }

    $suji_groups[] = $suji_legacy;
} else {

    $suji_raw_groups = get_post_meta($suji_page_id, 'withus_groups', true);

    if (is_array($suji_raw_groups)) {
        foreach ($suji_raw_groups as $suji_raw_group) {
            if (!is_array($suji_raw_group)) {
                continue;
            }

            $suji_group = array(
                    'title' => (string)($suji_raw_group['group_title'] ?? ''),
                    'items' => array(),
            );

            $suji_raw_items = $suji_raw_group['group_items'] ?? array();
            if (is_array($suji_raw_items)) {
                foreach ($suji_raw_items as $suji_raw_item) {
                    if (!is_array($suji_raw_item)) {
                        continue;
                    }
                    $suji_group['items'][] = array(
                            'text' => (string)($suji_raw_item['item_text'] ?? ''),
                            'url' => (string)($suji_raw_item['item_url'] ?? ''),
                            'file' => suji_withus_file_info($suji_raw_item['item_file'] ?? ''),
                    );
                }
            }

            $suji_groups[] = $suji_group;
        }
    }
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class('withus-body'); ?>>
<?php wp_body_open(); ?>

<main class="withus">
    <div class="withus-inner">
        <?php while (have_posts()) : the_post(); ?>
            <?php $suji_content = get_the_content(); ?>
            <div class="withus-intro">
                <h1>WITHUS 자료실</h1>
                <?php if (trim(wp_strip_all_tags($suji_content))) : ?>
                    <?php the_content(); ?>
                <?php endif; ?>
                <h3>
                    2026년 10월 24일
                    가톨릭 성가 페스타
                </h3>
                <h4 id="dday" class="d-day"></h4>
            </div>
        <?php endwhile; ?>

        <?php foreach ($suji_groups as $suji_group) : ?>
            <?php if (empty($suji_group['items']) && '' === trim($suji_group['title'])) {
                continue;
            } ?>

            <section class="withus-group">
                <?php if (trim($suji_group['title'])) : ?>
                    <h2 class="withus-group-title"><?php echo esc_html($suji_group['title']); ?></h2>
                <?php endif; ?>

                <?php if (!empty($suji_group['items'])) : ?>
                    <ul class="withus-links">
                        <?php foreach ($suji_group['items'] as $suji_item) : ?>
                            <?php
                            $suji_text = trim($suji_item['text']);
                            $suji_url = trim($suji_item['url']);
                            $suji_file_url = trim($suji_item['file']['url']);

                            if ('' === $suji_text && '' === $suji_url && '' === $suji_file_url) {
                                continue;
                            }

                            // A file wins over a plain link when both are filled in.
                            $suji_href = '' !== $suji_file_url ? $suji_file_url : $suji_url;

                            // Uploaded file, or a URL that points straight at a file.
                            $suji_is_file = '' !== $suji_file_url || ('' !== $suji_href && suji_withus_url_is_file($suji_href));

                            // The download attribute is ignored cross-origin, so only set it
                            // for files served from this site.
                            $suji_same_origin = 0 === strpos($suji_href, home_url());

                            $suji_label = $suji_text;

                            if ('' === $suji_label) {
                                if ('' !== $suji_file_url) {
                                    $suji_label = $suji_item['file']['name'];
                                } elseif ($suji_is_file) {
                                    $suji_label = urldecode(basename((string)parse_url($suji_href, PHP_URL_PATH)));
                                } else {
                                    $suji_label = $suji_href;
                                }
                            }
                            ?>
                            <li class="withus-link-item">
                                <?php if ($suji_href) : ?>
                                    <a href="<?php echo esc_url($suji_href); ?>"
                                       class="<?php echo $suji_is_file ? 'is-file' : 'is-link'; ?>"
                                            <?php if ($suji_is_file && $suji_same_origin) : ?>
                                                download
                                            <?php else : ?>
                                                target="_blank" rel="noopener noreferrer"
                                            <?php endif; ?>>
                                        <span class="withus-link-text"><?php echo esc_html($suji_label); ?></span>
                                        <span class="withus-link-arrow" aria-hidden="true">
											<?php if ($suji_is_file) : ?>
                                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none"
                                                     stroke="currentColor"
                                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
													<path d="M12 4v11"></path>
													<polyline points="7 11 12 16 17 11"></polyline>
													<path d="M5 19h14"></path>
												</svg>
                                            <?php else : ?>
                                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none"
                                                     stroke="currentColor"
                                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
													<line x1="5" y1="12" x2="19" y2="12"></line>
													<polyline points="13 6 19 12 13 18"></polyline>
												</svg>
                                            <?php endif; ?>
										</span>
                                    </a>
                                <?php else : ?>
                                    <span class="withus-link-plain"><?php echo esc_html($suji_label); ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        <?php endforeach; ?>

        <footer class="withus-footer">
            <p>&copy; <?php echo esc_html(gmdate('Y')); ?> <?php bloginfo('name'); ?></p>
        </footer>

    </div>
</main>
<script>
    function updateDday() {
        const el = document.getElementById("dday");
        if (!el) {
            return;
        }

        const targetDate = new Date("2026-10-24T15:00:00");
        const now = new Date();

        const diff = targetDate - now;

        // 음수/양수 구분
        const isFuture = diff >= 0;
        const absDiff = Math.abs(diff);

        // 시간 계산
        const days = Math.floor(absDiff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((absDiff / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((absDiff / (1000 * 60)) % 60);
        const seconds = Math.floor((absDiff / 1000) % 60);

        const prefix = isFuture ? "전" : "후";

        el.innerText = `${days}일 ${hours}시간 ${prefix}`;
    }

    // 1분마다 업데이트
    setInterval(updateDday, 1000 * 60);

    // 처음 실행
    updateDday();
</script>
<?php wp_footer(); ?>
</body>
</html>
