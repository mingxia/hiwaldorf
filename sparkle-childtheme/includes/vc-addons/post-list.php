<?php
// This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;

class YTVC_ThemePostList extends YTVC_Addons{
  
    function __construct() {
        // We safely integrate with VC with this hook
        add_action( 'init', array( $this, 'init' ) );

        // Use this when creating a shortcode addon
    }

    public function map(){
		vc_map( array(
            'name' => 'Post list',
            'description' => 'List of posts with various style',
            'base' => 'post_list',
            'controls' => 'full',
            'class' => 'post_list',
            'icon' => 'post_list',
            'category' => 'Theme Elements',
            //'admin_enqueue_js' => array(plugins_url('vc_extend.js', __FILE__)), // This will load js file in the VC backend editor
            //'admin_enqueue_css' => array(plugins_url('vc_extend_admin.css', __FILE__)), // This will load css file in the VC backend editor
            'params' => array(
                array(
                  'type' => 'textfield',
                  'holder' => 'h4',
                  'class' => '',
                  'heading' => 'Title',
                  'param_name' => 'title',
                  'value' => '',
                  'description' => 'Title for widget',
          			//'admin_label' => true
				),
				array(
					'type' => 'dropdown',
					'heading' =>  'Thumbnail style',
					'param_name' => 'style',
					'value' => array(
					 'Default (Small)' => 'small',
					 'Large' => 'large',
					 'First large' => 'mixed',
					 'Number (No thumb)' => 'number',
					 'Title only' => 'nothumb',
					 'First item has thumbnail' => 'thumb_first',
					),
					'admin_label' => true
				),
				
				array(
					'type' => 'dropdown',
					'heading' =>  'Posts Direction',
					'param_name' => 'direction',
					'save_always' => true,
					'value' => array(
						 'Vertical (Default)' => 'vertical',
						 'Horizontal' => 'horizontal',					
					),
					// 'admin_label' => true
				),
				
				array(
					'type' => 'dropdown',
					'heading' =>  'Column ( For Horizontal posts)',
					'param_name' => 'column',
					'save_always' => true,
					'value' => array(
						 '2 columns' => '2',
						 '3 columns' => '3',
						 '4 columns' => '4',
						 '6 columns' => '6',					
					),
					// 'admin_label' => true
				),
				array(
					'type' => 'autocomplete',
					'heading' =>  'Categories',
					'param_name' => 'cat',
					'settings' => array(
						'multiple' => true,
						'sortable' => true,
					),
					'save_always' => true,
					'description' =>  'Specify categories',
				),
				array(
					'type' => 'autocomplete',
					'heading' =>  'Tags',
					'param_name' => 'tags',
					'settings' => array(
						'multiple' => true,
						'sortable' => true,
					),
					'save_always' => true,
					'description' =>  'Specify tags',
				),
                array(
					'type' => 'textfield',
					'class' => '',
					'heading' => 'Number of Post',
					'param_name' => 'count',
					'value' => 5,
					'description' => '',
					'save_always' => true,
					//'admin_label' => true
				),
				array(
					'type' => 'dropdown',
					'heading' =>  'Order',
					'param_name' => 'order',
					'save_always' => true,
					'value' => array(
						 'Descending' => 'DESC',
						 'Ascending' => 'ASC',					
					),
					// 'admin_label' => true
				),
				array(
					'type' => 'dropdown',
					'heading' =>  'Order by',
					'param_name' => 'orderby',
					'save_always' => true,
					'value' => array(
						 'Date' => 'date',
						 'Title' => 'title',	
						 'Post slug' => 'name',
						 'Author	' => 'author',	
						 'Number of comments' => 'comment_count',
						 'Last modified date' => 'modified',	
						 'Random order' => 'rand',
						 'Post views' => 'meta_value_num',					
					),
					// 'admin_label' => true
				),
				array(
					'type' => 'dropdown',
					'heading' =>  'Time Period',
					'param_name' => 'time_period',
					'save_always' => true,
					'value' => array(
						 'Default' => 'default',
						 'This week' => 'this_week',		
						 'Last week' => 'last_week',		
						 'This Month' => 'this_month',		
						 'Last Month' => 'last_month',		
						 'Last 30 days' => 'last_30days',					
					),
				),
				array(
					
					'type' => 'checkbox',
					'heading' =>  'Excerpt',
					'param_name' => 'excerpt',
					'save_always' => true,
					'value' => 1,
					// 'admin_label' => true
				),
				array(
					
					'type' => 'checkbox',
					'heading' =>  'Scroll Infinitely',
					'param_name' => 'scroll_infinitely',
					'save_always' => true,
					'value' => 0,
					// 'admin_label' => true
				),

				array(
					'type' => 'checkbox',
					'heading' =>  'Show views/comment counter',
					'param_name' => 'show_icon',
					'save_always' => true,
					'value' => 1,
					// 'admin_label' => true
				),

				array(
					
					'type' => 'checkbox',
					'heading' =>  'Show category tag',
					'param_name' => 'show_cat',
					'save_always' => true,
					'value' => 1,
					// 'admin_label' => true
				),
				array(
					
					'type' => 'checkbox',
					'heading' =>  'Show post date',
					'param_name' => 'show_date',
					'save_always' => true,
					'value' => 1,
					// 'admin_label' => true
				),
				array(
					
					'type' => 'checkbox',
					'heading' =>  'Show Review result',
					'param_name' => 'show_rating',
					'save_always' => true,
					'value' => 1,
					// 'admin_label' => true
				),
				
            )
        ) );

		//Filters For autocomplete param:
		//For suggestion: vc_autocomplete_[shortcode_name]_[param_name]_callback
		add_filter( 'vc_autocomplete_post_list_cat_callback', array(
			&$this,
			'postListCategoryAutocompleteSuggester'
		), 10, 1 ); // Get suggestion(find). Must return an array
		add_filter( 'vc_autocomplete_post_list_cat_render', array(
			&$this,
			'postListCategoryRenderByIdExact'
		), 10, 1 ); // Render exact category by id. Must return an array (label,value)

		//Filters For autocomplete param:
		//For suggestion: vc_autocomplete_[shortcode_name]_[param_name]_callback
		add_filter( 'vc_autocomplete_post_list_tags_callback', array(
			&$this,
			'postListTagAutocompleteSuggester'
		), 10, 1 ); // Get suggestion(find). Must return an array
		add_filter( 'vc_autocomplete_post_list_tags_render', array(
			&$this,
			'postListTagRenderByIdExact'
		), 10, 1 ); // Render exact category by id. Must return an array (label,value)
    }

 	public function postListCategoryAutocompleteSuggester( $query, $slug = false ) {
		$terms = get_terms( 'category', array( 'name__like' => $query, 'fields' => 'id=>name', 'hide_empty' => false ) );

		$result = array();

		if( !empty( $terms )){
			foreach ($terms as $key => $value) {
				$result[] = array(
					'value' => $key,
					'label' => $value
				);
			}
		}

		return $result;
	}
	public function postListCategoryRenderByIdExact( $query ) {
		global $wpdb;
		$query = $query['value'];
		$query = trim( $query );
		$term = get_term_by( 'id', $query, 'category' );

		return $this->postCategoryTermOutput( $term );
	}

 	public function postListTagAutocompleteSuggester( $query, $slug = false ) {
		$terms = get_terms( 'post_tag', array( 'name__like' => $query, 'fields' => 'id=>name', 'hide_empty' => false ) );

		$result = array();

		if( !empty( $terms )){
			foreach ($terms as $key => $value) {
				$result[] = array(
					'value' => $key,
					'label' => $value
				);
			}
		}

		return $result;
	}
	public function postListTagRenderByIdExact( $query ) {
		global $wpdb;
		$query = $query['value'];
		$query = trim( $query );
		$term = get_term_by( 'id', $query, 'post_tag' );

		return $this->postCategoryTermOutput( $term );
	}

	/**
	 * Return product category value|label array
	 *
	 * @param $term
	 *
	 * @since 4.4
	 * @return array|bool
	 */
	protected function postCategoryTermOutput( $term ) {
		$term_title = $term->name;
		$term_id = $term->term_id;

		$term_title_display =$term_id . ' - ' . $term_title;

		$data = array();
		$data['value'] = $term_id;
		$data['label'] = $term_title_display;

		return ! empty( $data ) ? $data : false;
	}
    /*
    Shortcode logic how it should be rendered
    */
    public function shortcode( $atts, $content = null ) {
     
       
    }
 
    /**
     * Admin head stuff
     */
    public function admin_head() {
    	$style = '<style type="text/css">
		[data-element_type="yt_iconbox"] .wpb_element_wrapper .wpb_element_title{
			display: none;
		}
		[data-element_type="yt_iconbox"] .wpb_element_wrapper [name="image"]{
			max-width: 64px;
			margin: 0 15px 0 0;
			float: left;
			display: inline-block;
		}
		[data-element_type="yt_iconbox"] .wpb_element_wrapper [name="title"]{
			margin: 5px 0;
		}
		[data-element_type="yt_iconbox"] .wpb_element_wrapper [name="subline"]{
			clear: right;
			display: block;
		}
		[data-element_type="yt_iconbox"] .wpb_element_wrapper [name="content"]{
			clear: both;
			margin-top: 15px;
		}
		</style>';


		/*Inline css :P */
		echo str_replace(array("\r", "\n", "\t"), "", $style) . "\n";
    }
 
    /**
     * Frontend Scripts
     */
    public function enqueue_scripts() {
    }

    /**
     * Load plugin css and javascript files which you may need on front end of your site
     */
    public function admin_footer_scripts() {
    }
 
    /**
     * Show notice if your plugin is activated but Visual Composer is not
     */
    public function show_notice() {
        echo '
        <div class="updated">
          <p><strong>Addons</strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.</p>
        </div>';
    }
}
