<?php
/**
 * Plugin Name: HiWaldorf Steiner Quotes
 * Description: 施泰纳语录：沉浸式随机阅读、固定链接、分享及后台管理。使用现有 wp_steiner_quotes 数据。
 * Version: 1.0.7
 * Requires PHP: 7.4
 * Text Domain: hiwaldorf-steiner-quotes
 */
if (!defined('ABSPATH')) { exit; }

final class HiWaldorf_Steiner_Quotes {
    private const VERSION = '1.0.7';
    private const BASE = 'quotes';
    private const PALETTE = array('#a24c59', '#466f70', '#655c80', '#91654e', '#376a7e', '#746446');

    public static function init(): void {
        add_action('init', array(__CLASS__, 'routes'));
        add_filter('query_vars', array(__CLASS__, 'query_vars'));
        add_action('template_redirect', array(__CLASS__, 'render_route'), 1);
        add_filter('pre_get_document_title', array(__CLASS__, 'document_title'));
        // The current HiWaldorf theme outputs <title> via legacy wp_title().
        add_filter('wp_title', array(__CLASS__, 'legacy_document_title'), 99, 3);
        add_filter('body_class', array(__CLASS__, 'body_class'));
        add_action('wp_head', array(__CLASS__, 'meta'), 1);
        add_shortcode('steiner_quote_random', array(__CLASS__, 'shortcode'));
        add_action('wp_ajax_hw_steiner_random', array(__CLASS__, 'ajax_random'));
        add_action('wp_ajax_nopriv_hw_steiner_random', array(__CLASS__, 'ajax_random'));
        add_action('wp_ajax_hw_steiner_card', array(__CLASS__, 'ajax_card'));
        add_action('wp_ajax_nopriv_hw_steiner_card', array(__CLASS__, 'ajax_card'));
        add_action('admin_menu', array(__CLASS__, 'admin_menu'));
        add_action('admin_post_hw_steiner_save', array(__CLASS__, 'save'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'assets'));
        add_action('init', array(__CLASS__, 'register_sitemap'), 20);
    }
    public static function table(): string { global $wpdb; return $wpdb->prefix . 'steiner_quotes'; }
    public static function ready(): bool {
        global $wpdb;
        $table = self::table();
        return $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table))) === $table;
    }
    public static function routes(): void {
        add_rewrite_rule('^quotes/?$', 'index.php?hw_steiner=home', 'top');
        add_rewrite_rule('^quotes/([^/]+)/?$', 'index.php?hw_steiner=detail&hw_steiner_code=$matches[1]', 'top');
    }
    public static function activate(): void { self::routes(); flush_rewrite_rules(); }
    public static function deactivate(): void { flush_rewrite_rules(); }
    public static function query_vars(array $vars): array { $vars[] = 'hw_steiner'; $vars[] = 'hw_steiner_code'; return $vars; }
    private static function permalink(string $code): string { return home_url('/quotes/' . rawurlencode($code) . '/'); }
    private static function eligible_sql(): string { return 'endif = 1 AND TRIM(content_cn) <> \'\''; }
    private static function random_row(int $exclude = 0): ?array {
        global $wpdb;
        if (!self::ready()) { return null; }
        $table = self::table();
        $where = self::eligible_sql();
        $sql = "SELECT id, code, content, content_cn, gaid, note FROM {$table} WHERE {$where}";
        if ($exclude > 0) { $sql = $wpdb->prepare($sql . ' AND id <> %d', $exclude); }
        $row = $wpdb->get_row($sql . ' ORDER BY RAND() LIMIT 1', ARRAY_A);
        if (!$row && $exclude > 0) { return self::random_row(0); }
        return $row ?: null;
    }
    private static function by_code(string $code): ?array {
        global $wpdb;
        if (!self::ready()) { return null; }
        $table = self::table();
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT id, code, content, content_cn, gaid, note, endif FROM {$table} WHERE code = %s LIMIT 1", $code
        ), ARRAY_A);
        return $row ?: null;
    }
    private static function is_public(array $row): bool {
        return !empty($row['endif']) && trim((string) $row['content_cn']) !== '';
    }
    private static function tone(int $id): string { return self::PALETTE[abs($id) % count(self::PALETTE)]; }
    /** Normalize legacy gaid forms such as 293, GA293 or GA 293 for display only. */
    private static function ga_label($value): string {
        $value = trim((string) $value);
        if ($value === '') { return ''; }
        // A GA prefix may already be stored in the database. Never print it twice.
        $value = preg_replace('/^(?:GA\s*)+/iu', '', $value);
        $value = trim($value);
        return $value === '' ? '' : 'GA ' . $value;
    }

    public static function assets(): void {
        $route = get_query_var('hw_steiner');
        wp_register_style('hw-steiner', plugins_url('assets/quotes.css', __FILE__), array(), self::VERSION);
        wp_register_script('hw-steiner', plugins_url('assets/quotes.js', __FILE__), array(), self::VERSION, true);
        wp_localize_script('hw-steiner', 'HWSteiner', array('ajax' => admin_url('admin-ajax.php')));
        wp_enqueue_style('hw-steiner');
        wp_enqueue_script('hw-steiner');
    }
    public static function body_class(array $classes): array {
        if (in_array((string) get_query_var('hw_steiner'), array('home', 'detail'), true)) {
            $classes[] = 'hw-steiner-route';
        }
        return $classes;
    }
    /** A per-quote shareable title for fixed links, without changing the random reader title. */
    private static function quote_page_title(): string {
        $route = (string) get_query_var('hw_steiner');
        if ($route === 'home') {
            return '施泰纳语录 · 随机阅读 - HiWaldorf';
        }
        if ($route === 'detail') {
            $row = self::by_code((string) get_query_var('hw_steiner_code'));
            if ($row && self::is_public($row)) {
                $quote = trim(preg_replace('/\s+/u', ' ', wp_strip_all_tags((string) $row['content_cn'])));
                $excerpt = function_exists('mb_strimwidth')
                    ? mb_strimwidth($quote, 0, 72, '…', 'UTF-8')
                    : wp_html_excerpt($quote, 48, '…');
                return $excerpt . ' - 施泰纳语录 - HiWaldorf';
            }
            return '语录未找到 - HiWaldorf';
        }
        return '';
    }
    public static function document_title(string $title): string {
        $custom = self::quote_page_title();
        return $custom !== '' ? $custom : $title;
    }
    /** Compatibility with the legacy <title><?php wp_title(...); ?></title> in this theme. */
    public static function legacy_document_title($title, $sep = '|', $seplocation = ''): string {
        $custom = self::quote_page_title();
        return $custom !== '' ? $custom : (string) $title;
    }
    public static function meta(): void {
        $route = get_query_var('hw_steiner');
        if (!$route) { return; }
        if ($route === 'home') {
            echo '<meta name="robots" content="noindex,follow">' . "\n";
            return;
        }
        $row = self::by_code((string) get_query_var('hw_steiner_code'));
        if (!$row || !self::is_public($row)) { return; }
        $url = self::permalink($row['code']);
        $desc = wp_html_excerpt(wp_strip_all_tags($row['content_cn']), 135, '…');
        echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
        echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
        echo '<meta property="og:type" content="article">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr(self::quote_page_title()) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    }
    /** Count one quote selected for display using the existing lifetime counter. */
    private static function count_display(array $row): void {
        $id = (int) ($row['id'] ?? 0);
        if ($id < 1) { return; }
        global $wpdb;
        $table = self::table();
        // The existing schema auto-updates timestamps; preserve them for count-only updates.
        $wpdb->query($wpdb->prepare(
            "UPDATE {$table} SET views = views + 1,
             created_at = created_at, updated_at = updated_at WHERE id = %d", $id
        ));
    }

    public static function render_route(): void {
        $route = (string) get_query_var('hw_steiner');
        if (!in_array($route, array('home', 'detail'), true)) { return; }
        $row = $route === 'detail' ? self::by_code((string) get_query_var('hw_steiner_code')) : self::random_row();
        if ($route === 'detail' && (!$row || !self::is_public($row))) {
            global $wp_query; $wp_query->set_404(); status_header(404); nocache_headers(); return;
        }
        if (!self::ready()) { status_header(503); }
        if ($row) { self::count_display($row); }
        // Guard against canonical redirect treating the custom rewrite as a post URL.
        remove_action('template_redirect', 'redirect_canonical');
        // The site's front-page query may still be classified as is_home() for
        // this custom URL. Prevent homepage-only hero and ad output in header.php.
        // Keep theme hooks functional, but visually hide masthead/footer on quote routes only.
        remove_action('yt_main_start', 'yt_site_featured_news', 1);
        remove_action('yt_main_start', 'yt_site_head_ads', 2);
        get_header();
        // The reader takes over the whole site canvas without changing other routes.
        // Set the current quote tone before the reader markup is painted.
        $scene_tone = $row ? self::tone((int) $row['id']) : '#466f70';
        echo '<style id="hw-steiner-scene-tone">body.hw-steiner-route{--hw-steiner-scene:' . esc_html($scene_tone) . '}</style>';
        echo '<main id="hw-steiner-page" class="hw-steiner-page" aria-label="施泰纳语录">';
        echo '<div class="hw-steiner-top"><a class="hw-steiner-home" href="' . esc_url(home_url('/')) . '" aria-label="返回 HiWaldorf 首页">← HiWaldorf</a><span class="hw-steiner-eyebrow">施泰纳语录</span><a class="hw-steiner-random" href="' . esc_url(home_url('/quotes/')) . '">随机阅读</a></div>';
        if (!$row) {
            echo '<div class="hw-steiner-empty">暂无已完成中文翻译的语录。请检查数据表及翻译状态。</div>';
        } else {
            echo self::card($row, true);
        }
        echo '</main>';
        get_footer();
        exit;
    }
    private static function card(array $row, bool $immersive): string {
        $text = trim((string) ($row['content_cn'] ?? ''));
        $original = trim((string) ($row['content'] ?? ''));
        $url = self::permalink((string) $row['code']);
        $style = '--hw-quote-bg:' . self::tone((int) $row['id']);
        ob_start(); ?>
        <section class="hw-steiner-card <?php echo $immersive ? 'hw-steiner-immersive' : 'hw-steiner-small'; ?>" style="<?php echo esc_attr($style); ?>" data-quote-id="<?php echo (int) $row['id']; ?>" data-quote-url="<?php echo esc_url($url); ?>">
            <div class="hw-steiner-stage" <?php echo $immersive ? 'role="button" tabindex="0" aria-label="点击空白处切换语录"' : ''; ?>>
                <div class="hw-steiner-copy" role="group" aria-label="语录内容">
                    <div class="hw-steiner-mark" aria-hidden="true">“</div>
                    <blockquote class="hw-steiner-quote" lang="zh-CN"><?php echo esc_html($text); ?></blockquote>
                    <p class="hw-steiner-author">鲁道夫·施泰纳</p>
                    <?php $ga_label = self::ga_label($row['gaid'] ?? ''); ?>
                    <?php if ($ga_label !== '') : ?>
                        <p class="hw-steiner-ga"><?php echo esc_html($ga_label); ?></p>
                    <?php endif; ?>
                    <?php if ($original !== '') : ?>
                        <button type="button" class="hw-steiner-toggle" aria-expanded="false">查看原文</button>
                        <div class="hw-steiner-original" hidden><?php echo nl2br(esc_html($original)); ?></div>
                    <?php endif; ?>
                    <div class="hw-steiner-actions">
                        <a class="hw-steiner-link" href="<?php echo esc_url($url); ?>">永久链接</a>
                        <button type="button" class="hw-steiner-share">分享</button>
                        <button type="button" class="hw-steiner-image-share">分享图片</button>
                        <?php if (!$immersive) : ?><a href="<?php echo esc_url(home_url('/quotes/')); ?>">阅读更多</a><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php if ($immersive) : ?>
                <p class="hw-steiner-hint">点击空白处或按空格键，遇见下一条语录</p>
                <span class="hw-steiner-status" role="status" aria-live="polite"></span>
            <?php endif; ?>
        </section>
        <?php return (string) ob_get_clean();
    }
    public static function shortcode($atts = array()): string {
        $row = self::random_row();
        if (!$row) { return ''; }
        self::count_display($row);
        wp_enqueue_style('hw-steiner'); wp_enqueue_script('hw-steiner');
        return '<div class="hw-steiner-shortcode">' . self::card($row, false) . '</div>';
    }
    public static function ajax_random(): void {
        if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? '')) !== 'POST') { wp_send_json_error('Method not allowed', 405); }
        $exclude = isset($_POST['exclude']) ? absint($_POST['exclude']) : 0;
        $row = self::random_row($exclude);
        if (!$row) { wp_send_json_error('暂无可用语录', 404); }
        self::count_display($row);
        wp_send_json_success(array(
            'id' => (int) $row['id'], 'content' => (string) $row['content_cn'],
            'original' => (string) $row['content'], 'gaid' => (string) $row['gaid'],
            'url' => self::permalink((string) $row['code']), 'color' => self::tone((int) $row['id'])
        ));
    }
    /** One cached PNG per public quote, produced only on demand. Never touches `views`. */
    public static function ajax_card(): void {
        if (strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? '')) !== 'POST') {
            wp_send_json_error(array('message' => 'Method not allowed'), 405);
        }
        $code = isset($_POST['code']) && is_string($_POST['code']) ? sanitize_text_field(wp_unslash($_POST['code'])) : '';
        if (strlen($code) > 160 || $code === '' || !preg_match('/^[a-zA-Z0-9_-]+$/', $code)) {
            wp_send_json_error(array('message' => 'Invalid quote code'), 400);
        }
        $row = self::by_code($code);
        if (!$row || !self::is_public($row)) {
            wp_send_json_error(array('message' => '语录不存在'), 404);
        }
        // The server path is optional: fall back to the browser when GD/fonts/uploads
        // are unavailable. Never attempt shell commands or download fonts at runtime.
        $font = self::card_font();
        if (!function_exists('imagecreatetruecolor') || !function_exists('imagettftext') || !$font) {
            wp_send_json_success(array('mode' => 'browser'));
        }
        $upload = wp_upload_dir(null, false);
        if (!empty($upload['error']) || empty($upload['basedir']) || empty($upload['baseurl'])) {
            wp_send_json_success(array('mode' => 'browser'));
        }
        $dir = trailingslashit($upload['basedir']) . 'hw-steiner-cards';
        if (!is_dir($dir) && !wp_mkdir_p($dir)) {
            wp_send_json_success(array('mode' => 'browser'));
        }
        // Static PNG files are intentionally public. The quote code is safe as a basename.
        // Content fingerprint keeps edited translations and GA labels from reusing stale PNGs.
        $fingerprint = substr(hash('sha256', (string) $row['content_cn'] . '|' . (string) $row['gaid'] . '|' . self::tone((int) $row['id'])), 0, 12);
        $basename = $code . '-v1-' . $fingerprint . '.png';
        $file = $dir . '/' . $basename;
        $url = trailingslashit($upload['baseurl']) . 'hw-steiner-cards/' . rawurlencode($basename);
        if (is_file($file) && filesize($file) > 1000) {
            wp_send_json_success(array('mode' => 'server', 'url' => esc_url_raw($url)));
        }
        // Prevent concurrent identical render jobs, even from many visitors.
        $lock = @fopen($dir . '/.' . $code . '.lock', 'c');
        if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
            if (is_resource($lock)) { fclose($lock); }
            wp_send_json_success(array('mode' => 'browser'));
        }
        if (!is_file($file)) {
            $tmp = $file . '.' . getmypid() . '.tmp';
            $ok = self::draw_card_png($row, $font, $tmp);
            if ($ok) { @rename($tmp, $file); }
            if (is_file($tmp)) { @unlink($tmp); }
        }
        flock($lock, LOCK_UN); fclose($lock);
        if (is_file($file) && filesize($file) > 1000) {
            wp_send_json_success(array('mode' => 'server', 'url' => esc_url_raw($url)));
        }
        wp_send_json_success(array('mode' => 'browser'));
    }

    /** Prefer a locally installed CJK serif face; keep a sans-serif fallback. */
    private static function card_font(): string {
        $candidates = array(
            '/usr/share/fonts/opentype/noto/NotoSerifCJK-Regular.ttc',
            '/usr/share/fonts/opentype/noto/NotoSansCJK-Regular.ttc',
            '/usr/share/fonts/truetype/wqy/wqy-microhei.ttc',
            '/usr/share/fonts/truetype/arphic/ukai.ttc',
            '/usr/share/fonts/truetype/arphic/uming.ttc',
            '/usr/local/share/fonts/NotoSerifCJK-Regular.ttc',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf'
        );
        // DejaVu alone cannot draw Han glyphs; never silently output tofu boxes.
        array_pop($candidates);
        foreach ($candidates as $path) {
            if (is_readable($path)) { return $path; }
        }
        return '';
    }

    /** Measure width using exactly the same TrueType font as the final render. */
    private static function text_width(string $text, int $size, string $font): float {
        $rect = @imagettfbbox($size, 0, $font, $text);
        return is_array($rect) ? (float) (max($rect[0], $rect[2], $rect[4], $rect[6]) - min($rect[0], $rect[2], $rect[4], $rect[6])) : INF;
    }
    private static function wrap_card_text(string $text, int $size, string $font, int $maxWidth): array {
        $text = str_replace(array("\r\n", "\r"), "\n", trim($text));
        $result = array();
        foreach (explode("\n", $text) as $paragraph) {
            $characters = preg_split('//u', $paragraph, -1, PREG_SPLIT_NO_EMPTY);
            if (!is_array($characters)) { return array(); }
            $line = '';
            foreach ($characters as $ch) {
                // Space at beginning of a new line contributes no meaning.
                if ($line === '' && preg_match('/^\s$/u', $ch)) { continue; }
                if ($line !== '' && self::text_width($line . $ch, $size, $font) > $maxWidth) {
                    $result[] = rtrim($line);
                    $line = $ch;
                } else {
                    $line .= $ch;
                }
            }
            if ($line !== '') { $result[] = rtrim($line); }
        }
        return $result;
    }
    private static function draw_card_png(array $row, string $font, string $file): bool {
        $text = trim(wp_strip_all_tags((string) $row['content_cn']));
        if ($text === '' || !function_exists('imagepng')) { return false; }
        $image = @imagecreatetruecolor(1080, 1350);
        if (!$image) { return false; }
        $hex = ltrim(self::tone((int) $row['id']), '#');
        $bg = imagecolorallocate($image, hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));
        $white = imagecolorallocate($image, 255, 254, 250);
        $soft = imagecolorallocate($image, 220, 233, 230);
        imagefilledrectangle($image, 0, 0, 1080, 1350, $bg);
        $size = 57; $lines = array();
        for (; $size >= 29; $size -= 2) {
            $lines = self::wrap_card_text($text, $size, $font, 844);
            $step = (int) round($size * 1.76);
            // Fixed header, attribution, footer and generous margins.
            if ($lines && count($lines) <= 19 && count($lines) * $step <= 855) { break; }
        }
        if ($size < 29 || !$lines || count($lines) > 19) { imagedestroy($image); return false; }
        $draw = static function ($value, $size, $y, $color, $center = true) use ($image, $font): void {
            $width = self::text_width($value, $size, $font);
            $x = $center ? (int) ((1080 - $width) / 2) : 100;
            @imagettftext($image, $size, 0, $x, $y, $color, $font, $value);
        };
        $draw('施泰纳语录', 25, 113, $soft, false);
        $step = (int) round($size * 1.76);
        $blockHeight = count($lines) * $step;
        $firstBaseline = (int) (602 - $blockHeight / 2 + $size);
        foreach ($lines as $i => $line) { $draw($line, $size, $firstBaseline + $i * $step, $white); }
        $draw('鲁道夫·施泰纳', 29, 1090, $white);
        $ga = self::ga_label($row['gaid'] ?? '');
        if ($ga !== '') { $draw($ga, 22, 1138, $soft); }
        $brandWidth = self::text_width('HiWaldorf', 24, $font);
        @imagettftext($image, 24, 0, (int) (980 - $brandWidth), 1272, $soft, $font, 'HiWaldorf');
        $ok = @imagepng($image, $file, 6);
        imagedestroy($image);
        return (bool) $ok;
    }

    public static function admin_menu(): void {
        add_menu_page('施泰纳语录', '施泰纳语录', 'manage_options', 'hw-steiner-quotes', array(__CLASS__, 'admin_page'), 'dashicons-format-quote', 25);
    }
    public static function admin_page(): void {
        if (!current_user_can('manage_options')) { wp_die('无权限'); }
        global $wpdb;
        $table = self::table();
        echo '<div class="wrap"><h1>施泰纳语录</h1>';
        if (!self::ready()) {
            echo '<div class="notice notice-error"><p>未找到数据表：' . esc_html($table) . '。请确认 WordPress 表前缀及数据已导入。</p></div></div>';
            return;
        }
        if (isset($_GET['saved'])) { echo '<div class="notice notice-success"><p>语录已保存。</p></div>'; }
        $edit = isset($_GET['edit']) ? absint($_GET['edit']) : 0;
        if ($edit || isset($_GET['new'])) {
            $row = $edit ? $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $edit), ARRAY_A) : null;
            if ($edit && !$row) { echo '<p>记录不存在。</p></div>'; return; }
            $row = wp_parse_args((array) $row, array('id' => 0, 'code' => '', 'content' => '', 'content_cn' => '', 'gaid' => '', 'note' => '', 'endif' => 0));
            echo '<h2>' . ($edit ? '编辑语录 #' . $edit : '新增语录') . '</h2><form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
            wp_nonce_field('hw_steiner_save');
            echo '<input type="hidden" name="action" value="hw_steiner_save"><input type="hidden" name="id" value="' . (int) $row['id'] . '">';
            echo '<table class="form-table"><tbody>';
            foreach (array('code' => '永久链接 Slug', 'gaid' => 'GA 作品编号', 'note' => '备注') as $key => $label) {
                echo '<tr><th><label for="hw-' . esc_attr($key) . '">' . esc_html($label) . '</label></th><td><input class="regular-text" type="text" id="hw-' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($row[$key]) . '" ' . ($key === 'code' ? 'required' : '') . '></td></tr>';
            }
            foreach (array('content' => '原文', 'content_cn' => '中文译文') as $key => $label) {
                echo '<tr><th><label for="hw-' . esc_attr($key) . '">' . esc_html($label) . '</label></th><td><textarea id="hw-' . esc_attr($key) . '" name="' . esc_attr($key) . '" rows="7" class="large-text">' . esc_textarea($row[$key]) . '</textarea></td></tr>';
            }
            echo '<tr><th>翻译完成</th><td><label><input type="checkbox" name="endif" value="1" ' . checked((int) $row['endif'], 1, false) . '> 已完成中文翻译（可在前台展示）</label></td></tr>';
            echo '</tbody></table>'; submit_button('保存语录');
            echo '</form><p><a href="' . esc_url(admin_url('admin.php?page=hw-steiner-quotes')) . '">返回列表</a></p></div>';
            return;
        }
        $q = isset($_GET['s']) && is_string($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';
        $page = isset($_GET['paged']) ? max(1, absint($_GET['paged'])) : 1;
        $per = 30;
        $where = '1=1'; $args = array();
        if ($q !== '') { $like = '%' . $wpdb->esc_like($q) . '%'; $where = '(code LIKE %s OR content LIKE %s OR content_cn LIKE %s OR gaid LIKE %s)'; $args = array($like, $like, $like, $like); }
        $count_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where}";
        $total = (int) $wpdb->get_var($args ? $wpdb->prepare($count_sql, $args) : $count_sql);
        $list_sql = "SELECT id, code, content_cn, gaid, endif, views FROM {$table} WHERE {$where} ORDER BY id DESC LIMIT %d OFFSET %d";
        $list = $wpdb->get_results($wpdb->prepare($list_sql, array_merge($args, array($per, ($page - 1) * $per))), ARRAY_A);
        echo '<p><a class="button button-primary" href="' . esc_url(admin_url('admin.php?page=hw-steiner-quotes&new=1')) . '">新增语录</a> <a class="button" href="' . esc_url(home_url('/quotes/')) . '" target="_blank">打开语录页</a></p>';
        echo '<form method="get"><input type="hidden" name="page" value="hw-steiner-quotes"><input type="search" name="s" value="' . esc_attr($q) . '" placeholder="搜索语录、Slug、GA"> '; submit_button('搜索', 'secondary', '', false); echo '</form>';
        echo '<p>符合条件的记录：' . (int) $total . ' 条。仅 endif=1 且有中文内容的记录公开展示。</p>';
        echo '<table class="widefat striped"><thead><tr><th>ID</th><th>Slug</th><th>中文内容</th><th>GA</th><th>翻译</th><th>浏览</th></tr></thead><tbody>';
        foreach ($list as $row) {
            $url = admin_url('admin.php?page=hw-steiner-quotes&edit=' . (int) $row['id']);
            echo '<tr><td>' . (int) $row['id'] . '</td><td><a href="' . esc_url($url) . '">' . esc_html($row['code']) . '</a></td><td>' . esc_html(wp_trim_words($row['content_cn'], 35, '…')) . '</td><td>' . esc_html($row['gaid']) . '</td><td>' . ((int) $row['endif'] === 1 ? '完成' : '未完成') . '</td><td>' . (int) $row['views'] . '</td></tr>';
        }
        echo '</tbody></table>';
        echo '<p>';
        for ($i = 1, $end = (int) ceil($total / $per); $i <= $end && $i <= 100; $i++) {
            if ($i === $page || ($i !== 1 && $i !== $end && abs($i - $page) > 2)) { continue; }
            echo '<a style="margin-right:12px" href="' . esc_url(add_query_arg(array('page' => 'hw-steiner-quotes', 's' => $q, 'paged' => $i), admin_url('admin.php'))) . '">' . (int) $i . '</a>';
        }
        echo '</p></div>';
    }
    public static function save(): void {
        if (!current_user_can('manage_options')) { wp_die('无权限', '', array('response' => 403)); }
        check_admin_referer('hw_steiner_save');
        if (!self::ready()) { wp_die('数据表不存在'); }
        global $wpdb;
        $table = self::table();
        $id = isset($_POST['id']) ? absint($_POST['id']) : 0;
        $raw_code = isset($_POST['code']) && is_string($_POST['code']) ? wp_unslash($_POST['code']) : '';
        $code = sanitize_title($raw_code);
        if (!$code) { wp_die('Slug 不能为空'); }
        $values = array();
        foreach (array('content', 'content_cn', 'gaid', 'note') as $key) {
            $value = isset($_POST[$key]) && is_string($_POST[$key]) ? wp_unslash($_POST[$key]) : '';
            $values[$key] = $key === 'content' || $key === 'content_cn' ? sanitize_textarea_field($value) : sanitize_text_field($value);
        }
        $values['gaid'] = substr($values['gaid'], 0, 32);
        $values['note'] = substr($values['note'], 0, 255);
        $values['endif'] = isset($_POST['endif']) ? 1 : 0;
        $dup = (int) $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE code = %s LIMIT 1", $code));
        if ($dup && $dup !== $id) { wp_die('此 Slug 已被使用，请选择其他名称。'); }
        if ($id) {
            // Both original timestamps use ON UPDATE; preserve creation time explicitly.
            $result = $wpdb->query($wpdb->prepare(
                "UPDATE {$table} SET code=%s, content=%s, content_cn=%s, gaid=%s, note=%s, endif=%d, created_at=created_at WHERE id=%d",
                $code, $values['content'], $values['content_cn'], $values['gaid'], $values['note'], $values['endif'], $id
            ));
        } else {
            $result = $wpdb->insert($table, array_merge(array('code' => $code, 'views' => 0), $values), array('%s', '%d', '%s', '%s', '%s', '%s', '%d'));
        }
        if ($result === false) { wp_die('保存失败：请检查字段长度或数据库错误。'); }
        wp_safe_redirect(admin_url('admin.php?page=hw-steiner-quotes&saved=1'));
        exit;
    }
    public static function register_sitemap(): void {
        if (!function_exists('wp_register_sitemap_provider') || !class_exists('HiWaldorf_Steiner_Sitemap')) { return; }
        wp_register_sitemap_provider('steiner-quotes', new HiWaldorf_Steiner_Sitemap());
    }
}

if (class_exists('WP_Sitemaps_Provider')) {
    class HiWaldorf_Steiner_Sitemap extends WP_Sitemaps_Provider {
        public function __construct() { $this->name = 'steiner-quotes'; $this->object_type = 'steiner-quotes'; }
        public function get_object_subtypes() { return array(); }
        public function get_max_num_pages($object_subtype = ''): int {
            if (!HiWaldorf_Steiner_Quotes::ready()) { return 0; }
            global $wpdb; $table = HiWaldorf_Steiner_Quotes::table();
            $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE endif=1 AND TRIM(content_cn) <> ''");
            return (int) ceil($total / 1000);
        }
        public function get_url_list($page_num, $object_subtype = ''): array {
            if (!HiWaldorf_Steiner_Quotes::ready()) { return array(); }
            global $wpdb; $table = HiWaldorf_Steiner_Quotes::table();
            $rows = $wpdb->get_col($wpdb->prepare("SELECT code FROM {$table} WHERE endif=1 AND TRIM(content_cn) <> '' ORDER BY id ASC LIMIT %d OFFSET %d", 1000, (max(1, (int) $page_num)-1)*1000));
            return array_map(static function($code) { return array('loc' => home_url('/quotes/' . rawurlencode($code) . '/')); }, $rows);
        }
    }
}

register_activation_hook(__FILE__, array('HiWaldorf_Steiner_Quotes', 'activate'));
register_deactivation_hook(__FILE__, array('HiWaldorf_Steiner_Quotes', 'deactivate'));
HiWaldorf_Steiner_Quotes::init();