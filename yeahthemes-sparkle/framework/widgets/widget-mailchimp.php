<?php
/**
 *	Plugin Name:  MailChimp Subscription form
 *	Description: Display Mailchimp subscription form
 */
class YT_Mailchimp_Subscription_Form_Widget extends WP_Widget {

	/**
	 * Widget Setup
	 */
 	function __construct() {
		
		$widget_ops = array(
			'classname' => 'yt-mailchimp-subscription-form-widget yt-widget',
			'description' => esc_html__('Display Mailchimp subscription form', 'wpthms')
		);
		
		// Widget control settings
		$control_ops = array(
			'id_base' => 'yt-mailchimp-subscription-form-widget',
			//'width'	=> 400
		);
		
		parent::__construct( 
			'yt-mailchimp-subscription-form-widget', 
			esc_html__('(Theme) Mailchimp Suscription form', 'wpthms'), 
			$widget_ops, 
			$control_ops);
	
		//Then make sure our options will be added in the footer
		add_action( 'yt_ajax_yt-mailchimp-add-member', array( $this, 'ajax_subscribe') );
		add_action( 'yt_ajax_nopriv_yt-mailchimp-add-member', array( $this, 'ajax_subscribe') );
		add_filter( 'yt_refresh_transient_list_after_updating_theme_options', array( $this, 'delete_transient') );
	}

	function delete_transient( $transients ){
		$transients[] = 'yt_mailchimp_widget_list';
		return $transients;
	}

	function ajax_subscribe(){

		check_ajax_referer( 'yt-mailchimp-subscribe', 'nonce' );
		$apis = yt_get_3rd_party_api_keys();
		$mailchimp_api_key = $apis['mailchimp'];
		$email = strtolower($_POST['email']);
		$checking = trim($_POST['checking']) === '';
		$list =  trim($_POST['list']);
		$fname =  trim($_POST['fname']);
		$lname =  trim($_POST['lname']);
		$list_id;
		/*Honey pot checker*/

		if( !$checking ){
			esc_html_e( 'There was an error submitting the form.', 'wpthms');
			die(-1);
		}

		if( !$list ){
			/* Display this msg for administrator only*/
			if( current_user_can( 'edit_theme_options' ) )
				printf(
					__('No list found, please go back to %s choose one for this widget.', 'wpthms'), 
					sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'widgets.php' ) ), esc_html__('Widgets', 'wpthms' ) )
				) ;
			else
				esc_html_e('There was something wrong with subscribe function, please contact an Administrator ', 'wpthms');

			die(-1);
		}else{
			$list_id = yt_decode( $list );
		}
		/*If email is empty*/
		if( empty( $email ) ){
			echo '<span class="text-danger">' . esc_html__( 'Please enter your email address.', 'wpthms' ) . '</span>';
		    die(-1);
		}

		/*If email is invalid*/
		if( !filter_var( $email, FILTER_VALIDATE_EMAIL) ) {
		    echo '<span class="text-danger">' . esc_html__( 'Please enter a valid email address', 'wpthms' ) . '</span>';
		    die(-1);
		} else{

			
			if ( !class_exists( 'MailChimp' ))
				load_template( wpthms_FRAMEWORK_DIR . 'extended/class.mailchimp-api.php' );

			$MailChimp = new MailChimp( $mailchimp_api_key);
			
			$result = $MailChimp->call('lists/subscribe', array(
                'id'                => $list_id,
                'email'             => array('email'=> $email ),
                'merge_vars'        => array( 'FNAME'=> $fname, 'LNAME'=> $lname),
                'double_optin'      => false,
                'update_existing'   => false,
                'replace_interests' => false,
                'send_welcome'      => true,
            ));

            //print_r( $result);

			if( !empty( $result->status ) && 'error' == $result->status && !empty( $result->status ) ) {
				$error_msg = '';
				if( 'List_AlreadySubscribed' == $result->name)
					$error_msg = esc_html__('Oops! This email address is already subscribed!', 'wpthms');
				elseif( 'Email_NotExists' == $result->name )
					$error_msg = esc_html__('Email address does not exist', 'wpthms');
				elseif( 'List_DoesNotExist' == $result->name )
					$error_msg = current_user_can( 'edit_theme_options' ) ? esc_html__('List does not exist, please choose a valid list.', 'wpthms') : esc_html__( 'There was something wrong on adding your email to our system, please contact an Administrator', 'wpthms');
				// An error ocurred, return error message	
				echo '<span class="text-danger">' . esc_html__('Error: ', 'wpthms') . $error_msg .'</span>';
	    		
			}else{
				echo '<span class="text-success">' . esc_html__( 'Boom! You have been added to the list, thank you for subscribing! Please check our email for the confirmation.', 'wpthms' ) . '</span>';
				die(1);
			}

		}

		die();

	}
	/**
	 * Display Widget
	 */
	function widget( $args, $instance ) {
	
		// outputs the content of the widget
		
		extract( $args );
		// Our variables from the widget settings
		$title = apply_filters('widget_title', !empty( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		
		echo !empty( $before_widget ) ? $before_widget : '';
		
		//Echo widget title
		echo !empty( $title ) ? ( $before_title . $title . $after_title ) : '';
		$apis = yt_get_3rd_party_api_keys();
		

		?>

		<div class="yt-mailchimp-subscription-form-content">
			<?php

			$description = !empty( $instance['description'] ) ? yt_wpml_icl_t('Theme widget', 'Mailchimp desc', $instance['description'] ) : '';

			echo !empty( $description ) ? wpautop( $description ) : ''; 

			?>
			<form method="post" class="yt-mailchimp-subscribe-form" action="javascript:void(0)">
				<div class="form-group hidden">
					<input type="hidden" name="yt_mailchimp_subscribe_nonce" value="<?php echo esc_attr( wp_create_nonce( 'yt-mailchimp-subscribe' ) );?>">
					<input type="hidden" name="yt_mailchimp_subscribe_list" value="<?php echo esc_attr( yt_encode( $instance['list'] ) );?>">
			    	<input type="text" name="yt_mailchimp_subscribe_check" class="form-control"  value="" placeholder="<?php esc_attr_e('If you want to submit this form, do not enter anything in this field', 'wpthms');?>">
				</div>
				<?php if( !empty( $instance['show_name'] ) ):?>
				<div class="row form-group">
					<div class="col-xs-6">
						<input name="yt_mailchimp_subscribe_fname" type="text" class="form-control" placeholder="<?php esc_attr_e('First Name', 'wpthms');?>">
					</div>
					<div class="col-xs-6">
						<input name="yt_mailchimp_subscribe_lname" type="text" class="form-control" placeholder="<?php esc_attr_e('Last Name', 'wpthms');?>">
					</div>
				</div>
				<?php endif;?>
				<div class="input-group form-group" style="margin-bottom:0;">
			      <input type="email" name="yt_mailchimp_subscribe_email" class="form-control" placeholder="<?php esc_attr_e( 'Enter your E-mail...', 'wpthms' );?>">
			      <span class="input-group-btn">
			        <input class="btn btn-primary" type="submit" value="<?php esc_attr_e('Submit', 'wpthms');?>">
			      </span>
			    </div><!-- /input-group -->	

			</form>
			<div class="yt-mailchimp-subscription-result" style="margin-top:15px; display:none;"></div>

		</div>
		<?php
		echo !empty( $after_widget ) ? $after_widget : '';
	}

	/**
	 * Widget Settings
	 */
	function form( $instance ) {
		
		// Set up some default widget settings
		$defaults = array(
			'title' => __('Subscribe!','wpthms'),
			'list' => '',
			'description' => '',
			'show_name' => 0
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$apis = yt_get_3rd_party_api_keys();
		$mailchimp_api_key = $apis['mailchimp'];

	    ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:','wpthms')?></label>
			<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		<p>
			
			<?php
			if( !empty( $mailchimp_api_key ) ):
			?>
			<label for="<?php echo esc_attr( $this->get_field_id('list') ); ?>"><strong><?php esc_html_e( 'Choose a list:', 'wpthms' ); ?></strong></label>
			<select name="<?php echo esc_attr( $this->get_field_name('list') ); ?>" id="<?php echo esc_attr( $this->get_field_id('list') ); ?>" class="widefat">
				<?php
					
					if ( !class_exists( 'MailChimp' ))
						load_template( wpthms_FRAMEWORK_DIR . 'extended/class.mailchimp-api.php' );

					$mc_lists = array();
				
					//we *could* support paging, but few users have that many lists (and shouldn't)
					
					$mc_lists_transient = get_transient( 'yt_mailchimp_widget_list' );
					if ( empty( $mc_lists_transient ) ){
						$MailChimp = new MailChimp( $mailchimp_api_key);
					
						$result = $MailChimp->call('lists/list');
						if( !empty( $result->data )){
							foreach ( $result->data as $list ) {
								$mc_lists[$list->id] = $list->name;
							}
						}
					   // we have a transient return/assign the results
					   set_transient('yt_mailchimp_widget_list', $mc_lists, HOUR_IN_SECONDS );
					} else {
					   // Handle the false if you want
						$mc_lists = $mc_lists_transient;
					}

					if( !empty( $mc_lists ) ){
						foreach ( $mc_lists as $list_id => $list_name ) {
							if( !empty( $list_id  )):
							?>
							<option value="<?php echo esc_attr( $list_id );?>"<?php selected( $instance['list'], $list_id ); ?>><?php echo esc_html( $list_name );?></option>
							<?php
							endif;
						}
					}else{
						?>
						<option value=""><?php esc_html_e( 'No list found!', 'wpthms'); ?></option>
						<?php
					}
				?>
			</select>
			<?php
				else:
				?>
				<em><?php printf( 
					esc_html__('Mailchimp API key not found, enter it from %s->Subscribe & Connect->API','wpthms'), 
					sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=yt-theme-options' ), esc_html__( 'Theme Options', 'wpthms' ) )
				);?></em>
				<?php
				endif;
				?>
		</p>
		<p>
	    	<label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"><?php esc_html_e('Description:','wpthms')?></label>
	    	<textarea rows="5" name="<?php echo esc_attr( $this->get_field_name('description') ); ?>" class="widefat" id="<?php echo esc_attr( $this->get_field_id('description') ); ?>"><?php echo !empty( $instance['description'] ) ? esc_textarea( $instance['description'] ) : '';?></textarea>
	    </p>

	    <p>
	    	<input id="<?php echo esc_attr( $this->get_field_id( 'show_name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_name' ) ); ?>" type="checkbox" <?php checked( $instance['show_name'], 'on' ); ?>/>
	    	<label for="<?php echo esc_attr( $this->get_field_id('show_name') ); ?>"><?php esc_html_e( 'Show name field', 'wpthms' ); ?></label>
	    </p>
	  

		<?php
	}

	/**
	 * Update Widget
	 */
	function update( $new_instance, $old_instance ) {
		
		// processes widget options to be saved
		$instance = $old_instance;

		//Strip tags for title and name to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['list'] = $new_instance['list'];
		$instance['description'] = wp_kses_post( $new_instance['description'] );
			yt_wpml_register_string( 'Theme widget', 'Mailchimp desc', $instance['description']);
		$instance['show_name'] = $new_instance['show_name'] ? 'on' : 0 ;
		
		return $instance;
	}
}
