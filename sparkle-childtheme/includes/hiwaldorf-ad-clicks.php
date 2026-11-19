<?php
/**
 * Minimal daily click counts for the homepage DongDong Park banner.
 * No visitor identifiers, cookies, external requests, or third-party services.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function hiwaldorf_ad_clicks_install() {
    if ( get_option( 'hiwaldorf_ad_clicks_schema' ) === '1' ) {
        return;
    }
    global $wpdb;
    $table = $wpdb->prefix . 'hiwaldorf_ad_clicks';
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $charset = $wpdb->get_charset_collate();
    dbDelta( "CREATE TABLE {$table} (
        day date NOT NULL,
        ad_slug varchar(80) NOT NULL,
        clicks bigint(20) unsigned NOT NULL DEFAULT 0,
        PRIMARY KEY  (day,ad_slug)
    ) {$charset};" );
    $found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );
    if ( $found === $table ) {
        add_option( 'hiwaldorf_ad_clicks_since', current_time( 'Y-m-d' ), '', false );
        update_option( 'hiwaldorf_ad_clicks_schema', '1', false );
    }
}
add_action( 'init', 'hiwaldorf_ad_clicks_install', 21 );

/**
 * Rewrite only the known homepage ad link; no manual editing of the HTML
 * stored in the theme's H1 advertisement setting is needed.
 */
function hiwaldorf_track_home_ad_link( $html ) {
    if ( ! is_string( $html ) ) {
        return $html;
    }
    $url = home_url( '/?hiwaldorf_ad=dongdongpark' );
    return preg_replace_callback(
        '~(\bhref\s*=\s*)(["\x27])https?://(?:www\.)?dongdongpark\.com/?\2~i',
        static function( $match ) use ( $url ) {
            return $match[1] . $match[2] . esc_url( $url ) . $match[2];
        },
        $html
    );
}
add_filter( 'yt_site_head_ads', 'hiwaldorf_track_home_ad_link' );

/** All destinations are hardcoded: untrusted requests cannot specify redirect URLs. */
function hiwaldorf_handle_ad_click() {
    if ( ! isset( $_GET['hiwaldorf_ad'] ) || ! is_string( $_GET['hiwaldorf_ad'] ) ) {
        return;
    }
    if ( $_GET['hiwaldorf_ad'] !== 'dongdongpark' ) {
        return;
    }

    if ( get_option( 'hiwaldorf_ad_clicks_schema' ) !== '1' ) {
        hiwaldorf_ad_clicks_install();
    }

    // HEAD and automated prefetches are not counted as clicks.
    $method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( $_SERVER['REQUEST_METHOD'] ) : '';
    $prefetch = isset( $_SERVER['HTTP_SEC_PURPOSE'] ) ? strtolower( $_SERVER['HTTP_SEC_PURPOSE'] ) : '';
    $purpose = isset( $_SERVER['HTTP_PURPOSE'] ) ? strtolower( $_SERVER['HTTP_PURPOSE'] ) : '';
    if ( $method === 'GET' && strpos( $prefetch . ' ' . $purpose, 'prefetch' ) === false
        && get_option( 'hiwaldorf_ad_clicks_schema' ) === '1' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'hiwaldorf_ad_clicks';
        $wpdb->query( $wpdb->prepare(
            "INSERT INTO {$table} (day, ad_slug, clicks) VALUES (%s, %s, 1)
             ON DUPLICATE KEY UPDATE clicks = clicks + 1",
            current_time( 'Y-m-d' ),
            'dongdongpark'
        ) );
    }

    nocache_headers();
    header( 'X-Robots-Tag: noindex, nofollow', true );
    wp_redirect( 'https://dongdongpark.com/', 302, 'HiWaldorf Ad' );
    exit;
}
add_action( 'template_redirect', 'hiwaldorf_handle_ad_click', 0 );

function hiwaldorf_ad_clicks_on( $date ) {
    if ( get_option( 'hiwaldorf_ad_clicks_schema' ) !== '1' ) {
        return 0;
    }
    global $wpdb;
    $table = $wpdb->prefix . 'hiwaldorf_ad_clicks';
    return (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT clicks FROM {$table} WHERE day = %s AND ad_slug = %s",
        $date, 'dongdongpark'
    ) );
}

function hiwaldorf_ad_clicks_dashboard() {
    if ( ! current_user_can( 'manage_options' ) ) { return; }
    $now = new DateTimeImmutable( 'today', wp_timezone() );
    $today = $now->format( 'Y-m-d' );
    $yesterday = $now->modify( '-1 day' )->format( 'Y-m-d' );
    $since = get_option( 'hiwaldorf_ad_clicks_since', $today );
    echo '<div class="hiwaldorf-stats-grid">';
    foreach ( array(
        '今日广告点击' => hiwaldorf_ad_clicks_on( $today ),
        '昨日广告点击' => hiwaldorf_ad_clicks_on( $yesterday )
    ) as $label => $count ) {
        echo '<div class="hiwaldorf-stat"><div class="hiwaldorf-stat-label">' . esc_html( $label )
            . '</div><div class="hiwaldorf-stat-number">' . esc_html( number_format_i18n( $count ) )
            . '</div></div>';
    }
    echo '</div><p><strong>动动乐园 · 最近 30 天每日点击</strong></p>';
    echo '<table class="widefat striped"><thead><tr><th>日期</th><th>点击次数</th></tr></thead><tbody>';
    for ( $i = 0; $i < 30; $i++ ) {
        $date = $now->modify( '-' . $i . ' days' )->format( 'Y-m-d' );
        if ( $date < $since ) { break; }
        echo '<tr><td>' . esc_html( $date ) . '</td><td>' .
            esc_html( number_format_i18n( hiwaldorf_ad_clicks_on( $date ) ) ) . '</td></tr>';
    }
    echo '</tbody></table><p class="description">仅统计部署后经本站广告跳转链接产生的请求，不代表独立访客。不会追溯历史点击。</p>';
}
function hiwaldorf_ad_clicks_add_dashboard_widget() {
    if ( current_user_can( 'manage_options' ) ) {
        wp_add_dashboard_widget( 'hiwaldorf_ad_clicks_widget', '每日广告点击', 'hiwaldorf_ad_clicks_dashboard' );
    }
}
add_action( 'wp_dashboard_setup', 'hiwaldorf_ad_clicks_add_dashboard_widget' );
