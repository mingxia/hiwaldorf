<?php

/**********************************************************************************************************
 *        __   _______    _    _   _ _____ _   _ _____ __  __ _____ ____         ____ ___  __  __
 *        \ \ / / ____|  / \  | | | |_   _| | | | ____|  \/  | ____/ ___|       / ___/ _ \|  \/  |
 *         \ V /|  _|   / _ \ | |_| | | | | |_| |  _| | |\/| |  _| \___ \      | |  | | | | |\/| |
 *          | | | |___ / ___ \|  _  | | | |  _  | |___| |  | | |___ ___) |  _  | |__| |_| | |  | |
 *          |_| |_____/_/   \_\_| |_| |_| |_| |_|_____|_|  |_|_____|____/  (_)  \____\___/|_|  |_|
 *
 *				custom functions for the theme
 **********************************************************************************************************/
function remove_eazyest_gallery_breadcrumb() {
  remove_action('eazyest_gallery_before_attachment',     'ezg_breadcrumb', 5);
  remove_action('eazyest_gallery_before_folder_content', 'ezg_breadcrumb', 5);
}

add_action( 'eazyest_gallery_ready', 'remove_eazyest_gallery_breadcrumb', 1 );

function needRemoveP() {
	remove_filter('the_excerpt', 'wpautop');
}
add_filter( 'wp_image_editors', 'change_graphic_lib' );
function change_graphic_lib($array) {
  return array( 'WP_Image_Editor_GD', 'WP_Image_Editor_Imagick' );
}

// 最新功能：添加支持优酷视频嵌入及手机观看

wp_embed_unregister_handler('youku');

function wp_iframe_handler_youku( $matches, $attr, $url, $rawattr ) {

    $iframe = '<div style="padding-top: 83.3333%;" class="fluid-width-video-wrapper"><iframe src="http://player.youku.com/embed/'. esc_attr($matches[1]) .'" frameborder=0 allowfullscreen></iframe></div>';

	return apply_filters( 'iframe_youku', $iframe, $matches, $attr, $url, $rawattr );
}

wp_embed_register_handler( 'youku_iframe', '#http://v.youku.com/v_show/id_(.*?).html#i', 'wp_iframe_handler_youku' );

/**
 * 在 WordPress 编辑器添加“下一页”按钮
 * http://www.wpdaxue.com/add-next-page-button-wordpress-post-editor.html
 */
add_filter('mce_buttons','wpdaxue_add_next_page_button');
function wpdaxue_add_next_page_button($mce_buttons) {
	$pos = array_search('wp_more',$mce_buttons,true);
	if ($pos !== false) {
		$tmp_buttons = array_slice($mce_buttons, 0, $pos+1);
		$tmp_buttons[] = 'wp_page';
		$mce_buttons = array_merge($tmp_buttons, array_slice($mce_buttons, $pos+1));
	}
	return $mce_buttons;
}

// 添加语言包到子主题

function my_child_theme_locale() {
    load_theme_textdomain( 'wpthms', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'my_child_theme_locale' );

/**
 * HiWaldorf dashboard metrics.
 *
 * Historical _post_views values remain the source of truth for lifetime views.
 * Daily totals begin only after this feature is deployed; old per-day values
 * cannot be inferred from cumulative per-post counters.
 */
add_action( 'wp_dashboard_setup', 'my_custom_dashboard_widgets' );

function my_custom_dashboard_widgets() {
    wp_add_dashboard_widget( 'custom_help_widget', '核心统计', 'custom_dashboard_help' );
}

/** Sum the existing post view counters without loading each row into PHP. */
function all_view() {
    global $wpdb;

    return (int) $wpdb->get_var(
        "SELECT COALESCE(SUM(CAST(meta_value AS SIGNED)), 0)
         FROM {$wpdb->postmeta} WHERE meta_key = '_post_views'"
    );
}

function view_5000() {
    global $wpdb;

    return (int) $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->postmeta}
         WHERE meta_key = '_post_views' AND CAST(meta_value AS SIGNED) > 5000"
    );
}

/**
 * Dedicated daily aggregate table (one row per WordPress-site-local date).
 * Creating the table on init also covers the first frontend request after
 * deployment; the version option avoids repeated schema checks.
 */
function hiwaldorf_daily_views_install() {
    if ( get_option( 'hiwaldorf_daily_views_schema' ) === '1' ) {
        return;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'hiwaldorf_daily_views';
    $collate = $wpdb->get_charset_collate();

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( "CREATE TABLE {$table} (
        day date NOT NULL,
        views bigint(20) unsigned NOT NULL DEFAULT 0,
        PRIMARY KEY  (day)
    ) {$collate};" );

    // Mark installation only after confirming the table exists.
    $found = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) );
    if ( $found === $table ) {
        if ( ! get_option( 'hiwaldorf_daily_views_since' ) ) {
            add_option( 'hiwaldorf_daily_views_since', current_time( 'Y-m-d' ), '', false );
        }
        update_option( 'hiwaldorf_daily_views_schema', '1', false );
    }
}
add_action( 'init', 'hiwaldorf_daily_views_install', 20 );

/** Called only when YT Simple Post Views Tracker successfully counts a view. */
function hiwaldorf_record_daily_view( $post_id ) {
    if ( ! get_option( 'hiwaldorf_daily_views_schema' ) ) {
        hiwaldorf_daily_views_install();
    }

    if ( get_option( 'hiwaldorf_daily_views_schema' ) !== '1' ) {
        return; // Never break the original cumulative counter.
    }

    global $wpdb;
    $table = $wpdb->prefix . 'hiwaldorf_daily_views';
    $day = current_time( 'Y-m-d' );

    // Atomic increment; concurrent requests cannot overwrite each other.
    $wpdb->query( $wpdb->prepare(
        "INSERT INTO {$table} (day, views) VALUES (%s, 1)
         ON DUPLICATE KEY UPDATE views = views + 1",
        $day
    ) );
}
add_action( 'yt_simple_post_views_recorded', 'hiwaldorf_record_daily_view' );

function hiwaldorf_daily_views_for( $day ) {
    global $wpdb;
    if ( get_option( 'hiwaldorf_daily_views_schema' ) !== '1' ) {
        return 0;
    }
    $table = $wpdb->prefix . 'hiwaldorf_daily_views';
    return (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT views FROM {$table} WHERE day = %s", $day
    ) );
}

function custom_dashboard_help() {
    $today = current_time( 'Y-m-d' );
    $yesterday = ( new DateTimeImmutable( 'today', wp_timezone() ) )->modify( '-1 day' )->format( 'Y-m-d' );
    $since = get_option( 'hiwaldorf_daily_views_since', '' );

    $metrics = array(
        array( 'label' => '累计文章访问量', 'value' => number_format_i18n( all_view() ), 'accent' => true ),
        array( 'label' => '今日访问量', 'value' => number_format_i18n( hiwaldorf_daily_views_for( $today ) ), 'accent' => false ),
        array( 'label' => '昨日访问量', 'value' => $since && $yesterday < $since ? '—' : number_format_i18n( hiwaldorf_daily_views_for( $yesterday ) ), 'accent' => false ),
        array( 'label' => '访问超 5,000 次的文章/页面', 'value' => number_format_i18n( view_5000() ), 'accent' => false ),
    );

    echo '<div class="hiwaldorf-stats-grid">';
    foreach ( $metrics as $metric ) {
        $class = ! empty( $metric['accent'] ) ? ' hiwaldorf-stat-highlight' : '';
        echo '<div class="hiwaldorf-stat' . esc_attr( $class ) . '">';
        echo '<div class="hiwaldorf-stat-label">' . esc_html( $metric['label'] ) . '</div>';
        echo '<div class="hiwaldorf-stat-number">' . esc_html( $metric['value'] ) . '</div>';
        echo '</div>';
    }
    echo '</div>';
    echo '<p class="hiwaldorf-stats-note">统计口径：文章及页面浏览次数，非独立访客。每日数据从 '
        . esc_html( $since ? $since : $today )
        . ' 起记录；此前的每日访问量无法从历史累计数据还原。</p>';
}

/** Dashboard-only styles; frontend appearance remains unchanged. */
function hiwaldorf_dashboard_stats_styles() {
    echo '<style id="hiwaldorf-dashboard-stats">
        #custom_help_widget .inside { margin: 0; padding: 14px; }
        .hiwaldorf-stats-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .hiwaldorf-stat { min-width: 0; padding: 14px; border: 1px solid #ebe7e1; border-radius: 8px; background: #faf9f7; }
        .hiwaldorf-stat-highlight { background: #f7f3ee; }
        .hiwaldorf-stat-label { color: #74706a; font-size: 12px; line-height: 1.5; }
        .hiwaldorf-stat-number { margin-top: 7px; color: #292827; font-size: clamp(20px, 2.2vw, 29px); font-weight: 650; letter-spacing: -.035em; line-height: 1.25; font-variant-numeric: tabular-nums; overflow-wrap: anywhere; }
        .hiwaldorf-stat-highlight .hiwaldorf-stat-number { color: #9c533c; }
        .hiwaldorf-stats-note { margin: 12px 1px 0 !important; color: #8b8882; font-size: 11px; line-height: 1.7; }
        @media (max-width: 400px) {
            .hiwaldorf-stats-grid { gap: 7px; }
            .hiwaldorf-stat { padding: 10px; }
            .hiwaldorf-stat-number { font-size: 21px; }
        }
    </style>';
}
add_action( 'admin_head-index.php', 'hiwaldorf_dashboard_stats_styles' );
