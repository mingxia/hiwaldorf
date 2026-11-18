<?php
/**
 * Widget init
 *
 *
 * @package wpthms
 * @since 1.0
 * @framework 1.0
 */

/**
 * Register Widget
 *
 * @since 1.0
 * @framework 1.0
 */

include_once( 'widgets/widget-ajax-posts-by-category.php' );
include_once( 'widgets/widget-posts-with-thumnail.php' );


function yt_framework_widgets_init( $widgets ) {
	$widgets[] = 'YT_Ajax_Posts_By_Category_Widget';
	$widgets[] = 'YT_Posts_With_Thubnail_Widget';
	return $widgets;
}

add_filter( 'yt_framework_widgets_init', 'yt_framework_widgets_init' );


add_action( 'admin_print_styles-widgets.php', 'yt_framework_widgets_init_css' );
function yt_framework_widgets_init_css() {
    ?><style>
		.yt-scrollable-checklist-wrapper{
		 	min-height: 42px;
			max-height: 200px;
			overflow: auto;
			padding: 0.9em;
			margin-top: 0;
			border-style: solid;
			border-width: 1px;
			border-color: #dfdfdf;
			background-color: #fdfdfd;
		}

		.yt-scrollable-checklist-wrapper ul{
			margin-left: 15px;
			padding-top: 5px;
		}
		</style><?php
}