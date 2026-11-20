<?php
/**
 * Plugin Name: HiWaldorf Steiner Quotes
 * Description: A quiet, one-quote-at-a-time reader and reusable random quote card for the existing Steiner quotes table.
 * Version: 1.0.0
 * Author: HiWaldorf
 * License: GPL-2.0-or-later
 * Text Domain: hiwaldorf-steiner-quotes
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'HSQ_VERSION', '1.0.0' );
define( 'HSQ_URL', plugin_dir_url( __FILE__ ) );

function hsq_table() {
    global $wpdb;
    return $wpdb->prefix . 'steiner_quotes';
}
function hsq_table_exists() {
    global $wpdb;
    $table = hsq_table();
    return $table === $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );
}
function hsq_active_where() {
    return "endif = 1 AND TRIM(content_cn) <> ''";
}
function hsq_quote_url( $code ) {
    return home_url( '/quotes/' . rawurlencode( $code ) . '/' );
}
function hsq_random_quote( $exclude_id = 0 ) {
    global $wpdb;
    if ( ! hsq_table_exists() ) { return null; }
    $table = hsq_table();
    $exclude_id = absint( $exclude_id );
    // A small legacy table (~1,500 records); RAND is safe here.
    $q = $wpdb->get_row( $wpdb->prepare(
        "SELECT id, code, content, content_cn, gaid, note FROM {$table}
         WHERE " . hsq_active_where() . " AND id <> %d ORDER BY RAND() LIMIT 1",
        $exclude_id
    ), ARRAY_A );
    if ( ! $q && $exclude_id ) {
        return hsq_random_quote( 0 );
    }
    return $q ?: null;
}
function hsq_quote_by_code( $code ) {
    global $wpdb;
    if ( ! hsq_table_exists() ) { return null; }
    $table = hsq_table();
    return $wpdb->get_row( $wpdb->prepare(
        "SELECT id, code, content, content_cn, gaid, note FROM {$table}
         WHERE code = %s AND " . hsq_active_where() . " LIMIT 1",
        $code
    ), ARRAY_A );
}
function hsq_clean_text( $value ) {
    return trim( wp_strip_all_tags( (string) $value ) );
}
function hsq_public_quote( $q ) {
    return array(
        'id'       => (int) $q['id'],
        'code'     => (string) $q['code'],
        'cn'       => hsq_clean_text( $q['content_cn'] ),
        'original' => hsq_clean_text( $q['content'] ),
        'gaid'     => hsq_clean_text( $q['gaid'] ),
        'note'     => hsq_clean_text( $q['note'] ),
        'url'      => hsq_quote_url( $q['code'] ),
    );
}
function hsq_register_routes() {
    add_rewrite_rule( '^quotes/?$', 'index.php?hsq_quotes=1', 'top' );
    add_rewrite_rule( '^quotes/([^/]+)/?$', 'index.php?hsq_quotes=1&hsq_code=$matches[1]', 'top' );
}
add_action( 'init', 'hsq_register_routes' );
add_filter( 'query_vars', function( $vars ) {
    $vars[] = 'hsq_quotes';
    $vars[] = 'hsq_code';
    return $vars;
} );
register_activation_hook( __FILE__, function() {
    hsq_register_routes();
    flush_rewrite_rules();
} );
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
add_filter( 'redirect_canonical', function( $url ) {
    return get_query_var( 'hsq_quotes' ) ? false : $url;
} );
function hsq_current_quote() {
    static $loaded = false, $quote = null;
    if ( ! $loaded ) {
        $loaded = true;
        $code = (string) get_query_var( 'hsq_code' );
        $quote = $code !== '' ? hsq_quote_by_code( rawurldecode( $code ) ) : hsq_random_quote();
    }
    return $quote;
}
function hsq_head_metadata() {
    if ( ! get_query_var( 'hsq_quotes' ) ) { return; }
    $q = hsq_current_quote();
    if ( ! $q ) { return; }
    $p = hsq_public_quote( $q );
    $description = wp_trim_words( $p['cn'], 45, '…' );
    echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
    echo '<meta property="og:type" content="article">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr( '施泰纳语录 · HiWaldorf' ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $p['url'] ) . '">' . "\n";
    echo '<link rel="canonical" href="' . esc_url( $p['url'] ) . '">' . "\n";
}
add_action( 'wp_head', 'hsq_head_metadata', 3 );
add_filter( 'pre_get_document_title', function( $title ) {
    return get_query_var( 'hsq_quotes' ) ? '施泰纳语录 - HiWaldorf' : $title;
} );
add_filter( 'wp_title', function( $title ) {
    return get_query_var( 'hsq_quotes' ) ? '施泰纳语录' : $title;
}, 20 );

function hsq_enqueue() {
    wp_enqueue_style( 'hsq', HSQ_URL . 'assets/quotes.css', array(), HSQ_VERSION );
    wp_enqueue_script( 'hsq', HSQ_URL . 'assets/quotes.js', array(), HSQ_VERSION, true );
    wp_localize_script( 'hsq', 'HiSteinerQuotes', array(
        'randomEndpoint' => esc_url_raw( rest_url( 'steiner-quotes/v1/random' ) ),
        'homeUrl' => esc_url_raw( home_url( '/quotes/' ) ),
    ) );
}
function hsq_quote_markup( $q, $compact = false ) {
    $p = hsq_public_quote( $q );
    ob_start();
    ?>
    <div class="hsq-card<?php echo $compact ? ' hsq-card-compact' : ''; ?>" data-quote-id="<?php echo esc_attr( $p['id'] ); ?>">
        <?php if ( $compact ) : ?>
            <div class="hsq-card-kicker">施泰纳语录</div>
        <?php endif; ?>
        <blockquote class="hsq-quotation"><span class="hsq-opening" aria-hidden="true">“</span><span class="hsq-quote-cn"><?php echo esc_html( $p['cn'] ); ?></span><span class="hsq-closing" aria-hidden="true">”</span></blockquote>
        <p class="hsq-author">鲁道夫·施泰纳</p>
        <?php if ( $p['original'] !== '' ) : ?>
            <div class="hsq-original" hidden><p><?php echo nl2br( esc_html( $p['original'] ) ); ?></p></div>
        <?php endif; ?>
        <div class="hsq-meta">
            <?php if ( $p['gaid'] !== '' ) : ?>
                <span class="hsq-gaid">GA <?php echo esc_html( $p['gaid'] ); ?></span>
            <?php endif; ?>
            <?php if ( $p['note'] !== '' ) : ?>
                <span class="hsq-note"><?php echo esc_html( $p['note'] ); ?></span>
            <?php endif; ?>
        </div>
        <div class="hsq-actions">
            <?php if ( $p['original'] !== '' ) : ?><button type="button" class="hsq-original-toggle" aria-expanded="false">查看原文</button><?php endif; ?>
            <button type="button" class="hsq-share" data-url="<?php echo esc_url( $p['url'] ); ?>">分享语录</button>
            <?php if ( $compact ) : ?>
                <a href="<?php echo esc_url( $p['url'] ); ?>">阅读这条语录</a>
            <?php else : ?>
                <button type="button" class="hsq-next">换一条</button>
            <?php endif; ?>
        </div>
        <?php if ( ! $compact ) : ?><p class="hsq-hint">点击空白处或按空格键，遇见下一条语录</p><?php endif; ?>
        <span class="hsq-feedback" role="status" aria-live="polite"></span>
    </div>
    <?php
    return ob_get_clean();
}
function hsq_render_page() {
    if ( ! get_query_var( 'hsq_quotes' ) ) { return; }
    $q = hsq_current_quote();
    if ( ! $q ) {
        status_header( 404 );
        nocache_headers();
        get_header();
        echo '<main class="hsq-empty"><h1>暂时无法显示语录</h1><p>这条语录不存在、尚未完成中文翻译，或数据表尚未准备好。</p><a href="' . esc_url( home_url( '/' ) ) . '">返回首页</a></main>';
        get_footer();
        exit;
    }
    status_header( 200 );
    nocache_headers();
    if ( ! get_query_var( 'hsq_code' ) ) {
        // Random home must not be cached across visitors.
        header( 'Cache-Control: private, no-store, max-age=0', true );
    } else {
        global $wpdb;
        $table = hsq_table();
        $wpdb->query( $wpdb->prepare( "UPDATE {$table} SET views = views + 1, created_at = created_at, updated_at = updated_at WHERE id = %d", (int) $q['id'] ) );
    }
    hsq_enqueue();
    get_header();
    echo '<main class="hsq-reader" aria-label="施泰纳语录"><div class="hsq-reader-inner">';
    echo '<div class="hsq-topline"><a href="' . esc_url( home_url( '/quotes/' ) ) . '">施泰纳语录</a><span>在文字中相遇</span></div>';
    echo hsq_quote_markup( $q );
    echo '</div></main>';
    get_footer();
    exit;
}
add_action( 'template_redirect', 'hsq_render_page', 1 );

function hsq_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'id' => '0' ), $atts, 'steiner_quote_random' );
    $q = hsq_random_quote();
    if ( ! $q ) { return ''; }
    hsq_enqueue();
    return '<div class="hsq-embed">' . hsq_quote_markup( $q, true ) . '</div>';
}
add_shortcode( 'steiner_quote_random', 'hsq_shortcode' );

function hsq_register_rest() {
    register_rest_route( 'steiner-quotes/v1', '/random', array(
        'methods' => 'GET',
        'permission_callback' => '__return_true',
        'callback' => function( WP_REST_Request $request ) {
            $q = hsq_random_quote( absint( $request->get_param( 'exclude' ) ) );
            if ( ! $q ) { return new WP_Error( 'hsq_empty', '没有可显示的语录。', array( 'status' => 404 ) ); }
            $response = rest_ensure_response( hsq_public_quote( $q ) );
            $response->header( 'Cache-Control', 'no-store, no-cache, must-revalidate' );
            return $response;
        },
        'args' => array( 'exclude' => array( 'sanitize_callback' => 'absint' ) ),
    ) );
}
add_action( 'rest_api_init', 'hsq_register_rest' );

function hsq_sitemap_register() {
    if ( ! function_exists( 'wp_register_sitemap_provider' ) || ! class_exists( 'WP_Sitemaps_Provider' ) ) { return; }
    if ( ! hsq_table_exists() ) { return; }
    wp_register_sitemap_provider( 'steiner-quotes', new HSQ_Sitemap_Provider() );
}
add_action( 'init', 'hsq_sitemap_register', 50 );
add_action( 'init', function() {
    if ( ! class_exists( 'WP_Sitemaps_Provider' ) ) { return; }
    if ( class_exists( 'HSQ_Sitemap_Provider' ) ) { return; }
    class HSQ_Sitemap_Provider extends WP_Sitemaps_Provider {
        public function __construct() { $this->name = 'steiner-quotes'; $this->object_type = 'steiner-quotes'; }
        public function get_url_list( $page_num, $object_subtype = '' ) {
            global $wpdb;
            $per_page = 1000;
            $offset = ( max( 1, absint( $page_num ) ) - 1 ) * $per_page;
            $table = hsq_table();
            $rows = $wpdb->get_col( $wpdb->prepare(
                "SELECT code FROM {$table} WHERE " . hsq_active_where() . " ORDER BY id ASC LIMIT %d OFFSET %d",
                $per_page, $offset
            ) );
            return array_map( function( $code ) { return array( 'loc' => hsq_quote_url( $code ) ); }, $rows );
        }
        public function get_max_num_pages( $object_subtype = '' ) {
            global $wpdb;
            $table = hsq_table();
            $count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE " . hsq_active_where() );
            return (int) ceil( $count / 1000 );
        }
    }
}, 1 );

/** Native WP admin, reading and writing the original custom table without migrating rows. */
function hsq_admin_menu() {
    add_menu_page( '施泰纳语录', '施泰纳语录', 'manage_options', 'hsq-quotes', 'hsq_admin_page', 'dashicons-format-quote', 26 );
}
add_action( 'admin_menu', 'hsq_admin_menu' );
function hsq_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    global $wpdb;
    echo '<div class="wrap"><h1>施泰纳语录</h1>';
    if ( ! hsq_table_exists() ) {
        echo '<div class="notice notice-error"><p>找不到数据表 ' . esc_html( hsq_table() ) . '。请先确认 WordPress 数据库前缀和原表是否已导入。</p></div></div>';
        return;
    }
    $table = hsq_table();
    if ( isset( $_POST['hsq_save'] ) ) {
        check_admin_referer( 'hsq_save_quote' );
        $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
        $code = isset( $_POST['code'] ) ? sanitize_title( wp_unslash( $_POST['code'] ) ) : '';
        $cn = isset( $_POST['content_cn'] ) ? sanitize_textarea_field( wp_unslash( $_POST['content_cn'] ) ) : '';
        $original = isset( $_POST['content'] ) ? sanitize_textarea_field( wp_unslash( $_POST['content'] ) ) : '';
        $gaid = isset( $_POST['gaid'] ) ? sanitize_text_field( wp_unslash( $_POST['gaid'] ) ) : '';
        $note = isset( $_POST['note'] ) ? sanitize_text_field( wp_unslash( $_POST['note'] ) ) : '';
        $complete = isset( $_POST['endif'] ) ? 1 : 0;
        $data = array( 'code' => $code, 'content_cn' => $cn, 'content' => $original, 'gaid' => $gaid, 'note' => $note, 'endif' => $complete );
        if ( $code === '' ) {
            echo '<div class="notice notice-error"><p>code 不能为空。</p></div>';
        } else {
            if ( $id ) {
                // Legacy schema has ON UPDATE on created_at; preserve both timestamps.
                $old = $wpdb->get_row( $wpdb->prepare( "SELECT created_at, updated_at FROM {$table} WHERE id = %d", $id ), ARRAY_A );
                if ( $old ) {
                    $data['created_at'] = $old['created_at'];
                    $data['updated_at'] = current_time( 'mysql' );
                    $result = $wpdb->update( $table, $data, array( 'id' => $id ) );
                } else { $result = false; }
            } else {
                $data['views'] = 0;
                $result = $wpdb->insert( $table, $data );
                if ( $result !== false ) { $id = (int) $wpdb->insert_id; }
            }
            echo $result === false ? '<div class="notice notice-error"><p>保存失败。请检查 code 是否重复或数据字段长度。</p></div>' : '<div class="notice notice-success"><p>语录已保存。</p></div>';
        }
    }
    $edit = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0;
    if ( isset( $_GET['new'] ) || $edit ) {
        $q = $edit ? $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $edit ), ARRAY_A ) : null;
        if ( $edit && ! $q ) { echo '<p>未找到语录。</p></div>'; return; }
        $q = $q ?: array( 'id' => 0, 'code' => '', 'content_cn' => '', 'content' => '', 'gaid' => '', 'note' => '', 'endif' => 0 );
        echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=hsq-quotes' ) ) . '">← 返回列表</a></p>';
        echo '<form method="post">';
        wp_nonce_field( 'hsq_save_quote' );
        echo '<input type="hidden" name="id" value="' . esc_attr( $q['id'] ) . '">';
        foreach ( array( 'code' => 'URL Slug (code)', 'content_cn' => '中文译文', 'content' => '原文', 'gaid' => '作品 GA 关联 (gaid)', 'note' => '备注' ) as $field => $label ) {
            echo '<p><label for="hsq-' . esc_attr( $field ) . '"><strong>' . esc_html( $label ) . '</strong></label></p>';
            if ( $field === 'content_cn' || $field === 'content' ) {
                echo '<textarea style="width:100%;max-width:850px" rows="7" id="hsq-' . esc_attr( $field ) . '" name="' . esc_attr( $field ) . '">' . esc_textarea( $q[$field] ) . '</textarea>';
            } else {
                echo '<input class="regular-text" id="hsq-' . esc_attr( $field ) . '" name="' . esc_attr( $field ) . '" value="' . esc_attr( $q[$field] ) . '">';
            }
        }
        echo '<p><label><input type="checkbox" name="endif" value="1" ' . checked( (int) $q['endif'], 1, false ) . '> 已完成中文翻译（公开显示）</label></p>';
        submit_button( $edit ? '保存修改' : '添加语录', 'primary', 'hsq_save' );
        echo '</form></div>';
        return;
    }
    $search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
    $paged = max( 1, isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 );
    $limit = 30;
    $where = '1=1';
    $params = array();
    if ( $search !== '' ) {
        $like = '%' . $wpdb->esc_like( $search ) . '%';
        $where .= ' AND (code LIKE %s OR content_cn LIKE %s OR content LIKE %s OR gaid LIKE %s)';
        $params = array( $like, $like, $like, $like );
    }
    $count_sql = "SELECT COUNT(*) FROM {$table} WHERE {$where}";
    $total = (int) $wpdb->get_var( $params ? $wpdb->prepare( $count_sql, $params ) : $count_sql );
    $params[] = $limit;
    $params[] = ( $paged - 1 ) * $limit;
    $rows = $wpdb->get_results( $wpdb->prepare(
        "SELECT id, code, content_cn, gaid, endif, views FROM {$table} WHERE {$where} ORDER BY id DESC LIMIT %d OFFSET %d",
        $params
    ), ARRAY_A );
    echo '<p><a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=hsq-quotes&new=1' ) ) . '">添加语录</a></p>';
    echo '<form method="get"><input type="hidden" name="page" value="hsq-quotes"><input type="search" name="s" value="' . esc_attr( $search ) . '" placeholder="搜索译文、原文、code、GA"><button class="button">搜索</button></form>';
    echo '<p>共 ' . esc_html( number_format_i18n( $total ) ) . ' 条（每页 30 条）。仅 endif=1 且存在中文译文的语录会公开展示。</p>';
    echo '<table class="widefat striped"><thead><tr><th>ID</th><th>语录</th><th>GA</th><th>状态</th><th>浏览</th><th>操作</th></tr></thead><tbody>';
    foreach ( $rows as $q ) {
        $edit_url = admin_url( 'admin.php?page=hsq-quotes&edit=' . (int) $q['id'] );
        echo '<tr><td>' . (int) $q['id'] . '</td><td>' . esc_html( wp_html_excerpt( $q['content_cn'], 120, '…' ) )
            . '<br><small>' . esc_html( $q['code'] ) . '</small></td><td>' . esc_html( $q['gaid'] )
            . '</td><td>' . ( $q['endif'] ? '已翻译' : '未完成' ) . '</td><td>' . (int) $q['views']
            . '</td><td><a href="' . esc_url( $edit_url ) . '">编辑</a></td></tr>';
    }
    if ( ! $rows ) { echo '<tr><td colspan="6">没有匹配的语录。</td></tr>'; }
    echo '</tbody></table>';
    echo '<div class="tablenav"><div class="tablenav-pages">';
    echo wp_kses_post( paginate_links( array(
        'base' => add_query_arg( array( 'page' => 'hsq-quotes', 's' => $search, 'paged' => '%#%' ), admin_url( 'admin.php' ) ),
        'format' => '', 'current' => $paged, 'total' => max( 1, (int) ceil( $total / $limit ) )
    ) ) );
    echo '</div></div><p><code>[steiner_quote_random]</code> 可用于首页、小工具和文章区域。</p></div>';
}
