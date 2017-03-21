<?php
 // This file is not called from WordPress. We don't like that.
! defined( 'ABSPATH' ) and exit;

/**
 * Theme Customizer
 *
 * @author		wpthms
 * @copyright	Copyright ( c ) wpthms
 * @since		Version 1.0
 * @package 	Yeah Includes
 */
if( class_exists( 'WP_Customize_Image_Control' ) ){
class YT_Customize_Image_Control extends WP_Customize_Image_Control {
	public $type = 'image';
	public $get_url;
	public $statuses;
	public $extensions = array( 'jpg', 'jpeg', 'gif', 'png' );

	protected $tabs = array();

	/**
	 * Constructor.
	 *
	 * @since 3.4.0
	 * @uses WP_Customize_Upload_Control::__construct()
	 *
	 * @param WP_Customize_Manager $manager
	 * @param string $id
	 * @param array $args
	 */
	public function __construct( $manager, $id, $args ) {
		$this->statuses = array( '' => __('No Image', 'wpthms') );

		parent::__construct( $manager, $id, $args );

		//$this->add_tab( 'upload-new', __('Upload New'), array( $this, 'tab_upload_new' ) );
		//$this->add_tab( 'uploaded',   __('Uploaded'),   array( $this, 'tab_uploaded' ) );
		$this->remove_tab( 'upload-new' );
		$this->remove_tab( 'uploaded');
		$this->add_tab( 'media_library',   __('Media Library', 'wpthms'),   array( $this, 'tab_media_library' ) );

		// Early priority to occur before $this->manager->prepare_controls();
		add_action( 'customize_controls_init', array( $this, 'prepare_control' ), 5 );
	}

	/**
	 * Render the control's content.
	 *
	 * @since 3.4.0
	 */
	public function render_content() {
		$src = $this->value();

		if( intval( $src ) ){
			$attachment_id = $src;
			$image_attributes = wp_get_attachment_image_src( $attachment_id ); // returns an array
			if( !empty( $image_attributes[0] ) )
				$src = $image_attributes[0];
		}

		if ( isset( $this->get_url ) )
			$src = call_user_func( $this->get_url, $src );

		?>
		<div class="customize-image-picker">
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>

			<div class="customize-control-content">
				<div class="dropdown preview-thumbnail" tabindex="0">
					<div class="dropdown-content">
						<?php if ( empty( $src ) ): ?>
							<img style="display:none;" />
						<?php else: ?>
							<img src="<?php echo esc_url( set_url_scheme( $src ) ); ?>" />
						<?php endif; ?>
						<div class="dropdown-status"></div>
					</div>
					<div class="dropdown-arrow"></div>
				</div>
			</div>

			<div class="library">
				<ul>
					<?php foreach ( $this->tabs as $id => $tab ): ?>
						<li data-customize-tab='<?php echo esc_attr( $id ); ?>' tabindex='0'>
							<?php echo esc_html( $tab['label'] ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php foreach ( $this->tabs as $id => $tab ): ?>
					<div class="library-content" data-customize-tab='<?php echo esc_attr( $id ); ?>'>
						<?php call_user_func( $tab['callback'] ); ?>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="actions">
				<a href="#" class="remove"><?php _e( 'Remove Image', 'wpthms' ); ?></a>
			</div>
		</div>
		<?php
	}


	/**
	 * @since 3.4.0
	 *
	 * @param string $url
	 * @param string $thumbnail_url
	 */
	public function print_tab_image( $url, $thumbnail_url = null ) {
		$url = set_url_scheme( $url );

		$thumbnail_url = ( $thumbnail_url ) ? set_url_scheme( $thumbnail_url ) : $url;
		?>
		<a href="#" class="thumbnail" data-customize-image-value="<?php echo esc_url( $url ); ?>">
			<img src="<?php echo esc_url( $thumbnail_url ); ?>" />
		</a>
		<?php
	}


	public function tab_media_library() {

		//print_r( $this->choices );

		$media_by = isset( $this->choices['media_by'] ) ? $this->choices['media_by'] : 'url';


		?>
		<a class="choose-from-library-link button" data-media-by="<?php esc_attr( $media_by ); ?>" data-controller="<?php esc_attr( str_replace( '[', '-', str_replace( ']', '', $this->id ) ) );?>">
	        <?php _e( 'Open Library', 'wpthms' ); ?>
	    </a>
		<?php
	}
}
}

 
if( class_exists( 'WP_Customize_Control' ) ){
	
	class YT_Customize_Controls extends WP_Customize_Control {
		
		/**
		 * @access public
		 * @var    string
		 */
		public $type = 'textarea';
		
		/**`
		 * @access public
		 * @var    array
		 */
		public $description;
		
		/**
		 * @access public
		 * @var    array
		 */
		public $dimension = array();
		
		/**
		 * @access public
		 * @var    array
		 */
		public $options = array();
		
		/**
		 * Render the control's content.
		 * 
		 * Allows the content to be overriden without having to rewrite the wrapper.
		 * 
		 * @return  void
		 */
		public function render_content() { 
		
			$options = $this->choices;
			$dimension_w = isset( $this->dimension['width'] ) ? $this->dimension['width'] : '';
			$dimension_h = isset( $this->dimension['height'] ) ? $this->dimension['height'] : '';
			
			if( !in_array( $this->type, array( 'toggles' ) ) ){
				
				echo '<label>';	
			
			}
			
			switch( $this->type ){
				
				/*
				 * Textarea control
				 */
				case 'textarea':
				?>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
				<?php
				break;
				
				/*
				 * Textarea control
				 */
				case 'number':
				?>
					<span class="customize-control-title" ><?php echo esc_html( $this->label ); ?></span>
					<input type="numver" style="width:50%;" value="<?php echo intval( $this->value() ); ?>" <?php $this->link(); ?>>
				<?php
				break;
				
				/*
				 * Textarea control
				 */
				case 'checkbox':
				?>
					<label><input type="checkbox" value="1" <?php checked( $this->value(), 1, false );?> <?php $this->link(); ?>><strong><?php echo esc_html( $this->label );?></strong></label>
				<?php
				break;
				
				/*
				 * Select control
				 */
				case 'select':
				?>
					<span class="customize-control-title" ><?php echo esc_html( $this->label ); ?></span>
					<select <?php $this->link(); ?>>
						<?php
						foreach( $options as $k => $v ){
							echo '<option value="' . esc_attr( $k ) . '" ' .  selected( $this->value(), $k, false) . '>' . esc_html( $v ) . '</option>';
						}
						?>
					</select>
				<?php
				break;
				
				/*
				 * Select alt control
				 */
				case 'select_alt':
				?>
					<span class="customize-control-title" ><?php echo esc_html( $this->label ); ?></span>
					<select <?php $this->link(); ?>>
						<?php
						foreach( $options as $k){
							echo '<option value="' . esc_attr( $k ) . '" ' .  selected( $this->value(), $k, false) . '>' . esc_html( $k ) . '</option>';
						}
						?>
					</select>
				<?php
				break;
				
				/*
				 * Toggle control
				 */
				case 'toggles':
				?>
					<span class="customize-control-title" ><?php echo esc_html( $this->label ); ?></span>
					
					<div class="yt-toggles-control-wrapper clear">
						<?php
						foreach( $options as $k => $v ){
							?>
							<label class="button<?php echo checked( $this->value(), $k, false) ? ' button-primary' : '';?>" data-value="<?php echo esc_attr( $k );?>"><?php echo esc_attr( $v ); ?>
							</label>
							<?php
						}
						?>
						<input class="hidden" name="<?php echo esc_attr( $this->id );?>" type="text" value="<?php echo esc_attr( $this->value() );?>" <?php $this->link();?>>
					</div>
				<?php
				break;
				
				/*
				 * Color Block control
				 */
				case 'color_blocks':
				?>
					<span class="customize-control-title" ><?php echo esc_html( $this->label ); ?></span>
					
					<div class="yt-color-blocks-control-wrapper clear">
						<?php
						foreach( $options as $color => $name ){
							?>
							<label<?php echo checked( $this->value(), $color, false) ? ' class="active"' : '';?> style="background-color:<?php echo esc_attr( $color );?>;<?php echo ( $dimension_w && $dimension_h ) ? esc_attr( 'width:' . $dimension_w . ';height:' . $dimension_h . ';') : '' ?> " title="<?php echo esc_attr( $name );?>" data-value="<?php echo esc_attr( $color );?>">
							</label>
							<?php
						}
						?>
						<input class="hidden" name="<?php echo esc_attr( $this->id );?>" type="text" value="<?php echo esc_attr( $this->value() );?>" <?php $this->link();?>>
					</div>
				<?php
				break;
				
				/*
				 * Images control
				 */
				case 'images':
				?>
					<span class="customize-control-title" ><?php echo esc_html( $this->label ); ?></span>
					
					<div class="yt-images-radio-control-wrapper clear">
						<?php
						foreach( $options as $k => $v ){
							?>
							<label<?php echo checked( $this->value(), $k, false) ? ' class="active"' : '';?> data-value="<?php echo esc_attr( $k );?>">
								<img src="<?php echo esc_attr( $v );?>" style="<?php echo esc_attr( $dimension_w && $dimension_h ? 'width:' . $dimension_w . ';height:' . $dimension_h . ';' : '' ); ?>">
							</label>
							<?php
						}
						?>
						<input class="hidden" name="<?php echo esc_attr( $this->id );?>" type="text" value="<?php echo esc_attr( $this->value() );?>" <?php $this->link();?>>
					</div>
				<?php
				break;
				
				/*
				 * Tiles control
				 */
				case 'tiles':
				?>
					<span class="customize-control-title" ><?php echo esc_html( $this->label ); ?></span>
					
					<div class="yt-tiles-radio-control-wrapper clear">
						<?php
						foreach( $options as $k => $v ){
							?>
							<label<?php echo checked( $this->value(), $v, false) ? ' class="active"' : '';?> data-value="<?php echo esc_attr( $v );?>">
								<div style="background:url(<?php echo esc_url( $v ); ?>)"></div>
							</label>
							<?php
						}
						?>
						<input class="hidden" name="<?php echo esc_attr( $this->id );?>" type="text" value="<?php echo esc_attr( $this->value() );?>" <?php $this->link();?>>
					</div>
				<?php
				break;
			}
			
			
			/*
			 * Descriptions
			 */
			if( isset( $this->description ) && $this->description){
				echo '<span class="customize-control-desc">' . esc_html( $this->description ) . '</span>';
			}
			
			if( !in_array( $this->type, array( 'toggles' ) ) ){
				
				echo '</label>';
			
			}
			
			
		}
	}
}


if( !class_exists( 'YT_Theme_Customize' ) ){
	
	class YT_Theme_Customize{
		
		/**
		 * Option key
		 */
		public $_option_key;
		
		/**
		 * Option array
		 */
		public $_option_array = array();

		/**
		 * Option array
		 */
		private $_media_controls = array();
		
		/**
		 * PHP5 constructor method.
		 *
		 * @return    void
		 *
		 * @access    public
		 * @since     1.0.0
		 */
		function __construct() {
			
		}

		/**
		 * Helper function to bypass theme check ;)
		 *
		 * @param     string   	$val
		 * @return    mixed
		 *
		 * @access    public
		 * @since     1.0.0
		 */
		public static function _field_validation( $val ){
			return $val;

		}
		
		/**
		 * Init function
		 *
		 * @param     string   	$option_key
		 * @param     arrray   	$option_array
		 * @return    void
		 *
		 * @access    public
		 * @since     1.0.0
		 */
		public function init( $option_key, $option_array){

			//if( !is_admin() ) return;
			
			$this->_option_key = $option_key;
			
			$this->_option_array = $option_array;
			
			$this->hooks();			
		}
		
		/**
		 * Action hooks
		 *
		 * @return    void
		 *
		 * @access    public
		 * @since     1.0.0
		 */
		public function hooks() {

			
				add_action( 'customize_save_after', array( $this, 'customize_save_after'), 100);
				add_action( 'customize_register', array( $this, 'auto_register_theme_customize_fields') );
				
				add_action( 'customize_controls_print_styles', array( $this, 'theme_customize_frame_print_css' ), 10);
				add_action( 'customize_controls_print_footer_scripts', array( $this, 'theme_customize_frame_print_js' ), 10);
			
		}
		
		/**
		 * Re-update the theme options to make the changes to the WPML string tramslation
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @return    void
		 *
		 * @access    public
		 * @since     1.0.0
		 */

		public function customize_save_after( $wp_customize ) {
			
			$data = yt_get_options();
			do_action( 'yt_customize_after_saving', $data );
		}
		
		/**
		 * Registering fields for theme customize automatically based on $yt_options
		 *
		 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
		 * @return    void
		 *
		 * @access    public
		 * @since     1.0.0
		 */

		public function auto_register_theme_customize_fields( $wp_customize ) {
			
			/**
			 * This is optional, but if you want to reuse some of the defaults
			 * or values you already have built in the options panel, you
			 * can load them into $options for easy reference
			 */
			
			if( !is_array( $this->_option_array ) || !$this->_option_key ) return;
			
			//print_r($yt_data);
			
			$section_id;
			$section_name;
			$heading_name;
			$sub_section_id;
			
			$option_ids = array();
			
			$priority =  200;

			$transportMsgFields = array();
			
			//print_r($option_array);
			
			foreach( ( array ) $this->_option_array as $option ){
				
				$priority++;
				
				if( isset( $option['customize'] ) && $option['customize'] ){
					
					if( in_array( $option['type'], array( 'heading', 'subheading', 'separator' ) ) ){
						
						
						if( $option['type'] === 'heading' ){
							
							$section_id = yt_clean_string( $option['name'], '-', '_' );
							$heading_name = $section_name = $option['name'];
							
						
						}	
						
						if( $option['type'] === 'subheading' || $option['type'] === 'separator' ){
							
							$section_id = $section_id . yt_clean_string( $option['name'], '-', '_' );
							$section_name = $heading_name . ' - ' . $option['name'];

							if( isset( $option['customize_name'] )){

							
								$section_id = yt_clean_string( $option['customize_name'], '-', '_' );
								$section_name = $option['customize_name'];

							}
						
						}
						
						$wp_customize->add_section( $section_id , array(
							'title' => $section_name,
							'priority' => $priority,
						) );
						
					}else{
						
						$option_id = $option['id'];
						$option_name = $option['name'];
						$option_type = $option['type'];
						$option_std = isset( $option['std'] ) ? $option['std'] : '';
						$option_options = isset( $option['options'] ) ? $option['options'] : '';
						$option_desc = isset( $option['desc'] ) ? $option['desc'] : '';
						
						$option_dimension = array();
						if( isset( $option['settings']['width'] ) )
							$option_dimension['width'] = $option['settings']['width'];

						if( isset( $option['settings']['height'] ) )
							$option_dimension['height'] = $option['settings']['height'];


						$option_settings = !empty( $option['settings'] ) ? $option['settings'] : array();

						$option_transport = !empty( $option['transport'] ) && in_array( $option['transport'] , array( 'postMessage', 'refresh') ) ? $option['transport'] : 'refresh';
						
						$option_ids[] = $option_id;
						
						$option_key = $this->_option_key . '[' . $option_id . ']';
						
						/*
						 * Add setting
						 */
						$wp_customize->add_setting( $option_key, array(
							'type' => 'option',
							'default' => $option_std,
							'capability' => 'edit_theme_options',
							'transport' => $option_transport,
							'sanitize_callback' => array( $this, '_field_validation' ),
						) );

						/*Push field using transport msg to array*/
						if( 'postMessage' == $option_transport ){
							$transportMsgFields[] = $option_key;
							
						}
						
						/*
						 * Add Controls
						 */
						 
						
						/*
						 * colorpicker
						 */
						if( $option_type === 'colorpicker' ){
						
							$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $option_id, array(
								'label' => $option_name ,
								'section' => $section_id ,
								'settings' => $option_key,
								'priority'   => $priority
							) ) );	
							
						}
						
						/*
						 * media
						 */
						elseif( $option_type === 'media' ){
							
							$wp_customize->add_control( new YT_Customize_Image_Control( $wp_customize, $option_id, array(
								'label' => $option_name ,
								'section' => $section_id ,
								'settings' => $option_key,
								'priority'   => $priority,
								'choices'	=> $option_settings
							) ) );
						}
						
						/*
						 * textarea, select
						 */
						elseif( in_array( $option_type, array( 'checkbox', 'textarea', 'select', 'select_alt', 'toggles', 'images', 'tiles', 'color_blocks', 'number'  ) )  ){
							
							

							$wp_customize->add_control( new YT_Customize_Controls( $wp_customize, $option_id, array(
								'label' => $option_name ,
								'section' => $section_id ,
								'settings' => $option_key,
								'type' => $option_type,
								'choices' => $option_options,
								'description' => $option_desc,
								'dimension' => $option_dimension,
								'priority'   => $priority
							) ) );
							
						}
						/*
						 * Default: text
						 */
						else{
							
							$wp_customize->add_control( $option_id, array(
								'label' => $option_name ,
								'section' => $section_id,
								'settings' => $option_key,
								'type' => $option_type,
								'priority'   => $priority
							) );
							
						}
						
					}
				
				}
				
			}
			
			
			/*Exclude section from customize*/
			$exclude_sections = apply_filters( 'yt_customize_excluded_sections', array( 'title_tagline' ) );
			
			if( !empty( $exclude_sections ) ){
				
				foreach( (array) $exclude_sections as $exclude_section ){
					
					$wp_customize->remove_section( $exclude_section );
				
				}
			
			}
			/**
			 * Let's make some stuff use live preview JS
			 */

			
			$transportMsgFields = apply_filters( 'yt_customize_transport_message_fields', $transportMsgFields );
			//$transportMsgFields[] = 'blogname';
			//print_r($transportMsgFields);
			if( !empty( $transportMsgFields )){
				foreach ( ( array ) $transportMsgFields as $setting_id ) {
					 $wp_customize->get_setting( $setting_id )->transport = 'postMessage';
				}
			}

			if ( $wp_customize->is_preview()){
				add_action( 'wp_head' , array( $this, 'theme_customize_css' ) );
				add_action( 'wp_footer', array( $this, 'theme_customize_js'), 21);
			}
		}

		/**
		 * Customize in action
		 * @return    void
		 *
		 * @access    public
		 * @since     1.0.0
		 */

		public function theme_customize_css() {
			do_action( 'yt_theme_customize_css', $this->_option_key );
		}
		
		/**
		 * Customize in action
		 * @return    void
		 *
		 * @access    public
		 * @since     1.0.0
		 *
		 *	wp.customize('blogname',function( value ) {
		 *		value.bind(function(to) {
		 *			$('.plain-text-logo a').html(to);
		 *	 	});
		 *	});
		 */
		public function theme_customize_js() {
			$key = $this->_option_key;
			do_action( 'yt_theme_customize_js', $this->_option_key );

			/**
			 * Must set the option transport as "postMessage" 
			 * Access key by using this syntax: "$this->_option_key[plain_logo_text]"
			 */
		} 
		
		/**
		 * Print css for custom controllers
		 * @return    void
		 *
		 * @access    public
		 * @since     1.0.0
		 */
		public function theme_customize_frame_print_css() {
			$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_media();
			wp_enqueue_style('yt-customize', 		wpthms_FRAMEWORK_URI . "admin/assets/css/admin-customize.css" );
			
		}
		
		/**
		 * Print javascript for custom controllers
		 * @return    void
		 *
		 * @access    public
		 * @since     1.0.0
		 */
		public function theme_customize_frame_print_js() {
			?>
			
			<script type="text/javascript">
			( function( $ ){
				// Object for creating WordPress 3.5 media upload menu 
				// for selecting theme images.
				wp.media.ytCustomizerMediaManager = {
				     
				    init: function() {
				        // Create the media frame.
				        this.frame = wp.media.frames.ytCustomizerMediaManager = wp.media({
				            title: <?php echo '"' . esc_js( __('Choose image', 'wpthms' ) ) . '"';?>,
				            library: {
				                type: 'image'
				            },
				            button: {
				                text: <?php echo '"' . esc_js( __('Use this one', 'wpthms' ) ) . '"';?>,
				            }
				        });
				 
				         
				        $('.choose-from-library-link').click( function( event ) {
				            wp.media.ytCustomizerMediaManager.$el = $(this);
				            wp.media.ytCustomizerMediaManager.mediaBy = typeof $(this).data('media-by') != 'undefined' ? $(this).data('media-by') : 'url';
				            var controllerName = $(this).data('controller');
				            event.preventDefault();
				 
				            wp.media.ytCustomizerMediaManager.frame.open();
				        });

				        // When an image is selected, run a callback.
						this.frame.on( 'select', function() {
						    // Grab the selected attachment.
						    var attachment = wp.media.ytCustomizerMediaManager.frame.state().get('selection').first(),
						        controllerName = wp.media.ytCustomizerMediaManager.$el.data('controller');
						     
						    controller = wp.customize.control.instance(controllerName);
						    //console.log(controller);

						    var returnVal = 'url' == wp.media.ytCustomizerMediaManager.mediaBy ? attachment.attributes.url : attachment.attributes.id;
						   	controller.setting.set(returnVal);
						   	
						    controller.thumbnailSrc(attachment.attributes.url);
						});
				         
				    } // end init
				}; // end ytCustomizerMediaManager
				 
				wp.media.ytCustomizerMediaManager.init();

				/**
				 * Toggles
				 */


				$(document).on( 'click', '.yt-toggles-control-wrapper > label', function(e){
					var $el = $(this);
					$el.siblings('label').removeClass('button-primary');
					$el.siblings('input').val( $el.data('value') ).change();
					$el.addClass('button-primary');
					e.preventDefault();
				})
				
				/**
				 * Color blocks
				 */
				.on('click', '.yt-color-blocks-control-wrapper > label, .yt-images-radio-control-wrapper label, .yt-tiles-radio-control-wrapper label', function(e){
					var $el = $(this);
					$el.siblings('label').removeClass('active');
					$el.siblings('input').val( $el.data('value') ).change();
					$el.addClass('active');
					e.preventDefault();
				});
				
			} )( jQuery );
			</script>
			<?php
		}

	}
}