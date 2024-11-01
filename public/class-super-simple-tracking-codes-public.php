<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/super-simple-tracking-codes
 * @since      1.0.0
 *
 * @package    Super_Simple_Tracking_Codes
 * @subpackage Super_Simple_Tracking_Codes/public
 */

class Super_Simple_Tracking_Codes_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version
	 */
	private $version;

	/**
     * Holds the values to be used in the fields callbacks.
	 * 
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $options
     */
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.0
	 * @param	string	$plugin_name	The name of the plugin.
	 * @param	string	$version		The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = get_option( $plugin_name );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'cookieconsent', plugin_dir_url( __FILE__ ) . 'assets/vendor/css/cookieconsent.min.css', array(), '3.1.0', 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
	
		if ( isset( $this->options['gdpr']['enable'] ) && ( 1 == $this->options['gdpr']['enable'] ) ) {
			$cookieconsent = array(
				'type' => ( isset( $this->options['cookie_consent']['type'] ) && ! empty( $this->options['cookie_consent']['type'] ) )
					? esc_attr( $this->options['cookie_consent']['type'] ) : '',
				'position' => ( isset( $this->options['cookie_consent']['position'] ) && ! empty( $this->options['cookie_consent']['position'] ) )
					? esc_attr( $this->options['cookie_consent']['position'] ) : 'bottom',
				'showLink' => ( ! isset( $this->options['cookie_consent']['href'] )
					|| empty( $this->options['cookie_consent']['href'] ) ) ? false : true,
				'content' => array(
					'message' => ( isset( $this->options['cookie_consent']['message'] ) && ! empty( $this->options['cookie_consent']['message'] ) )
						? esc_html( $this->options['cookie_consent']['message'] ) : __( 'This website uses cookies to ensure you get the best experience on our website.', 'super-simple-tracking-codes' ),
					'dismiss' => ( isset( $this->options['cookie_consent']['dismiss'] ) && ! empty( $this->options['cookie_consent']['dismiss'] ) )
						? esc_html( $this->options['cookie_consent']['dismiss'] ) : __( 'Got it!', 'super-simple-tracking-codes' ),
					'deny' => ( isset( $this->options['cookie_consent']['deny'] ) && ! empty( $this->options['cookie_consent']['deny'] ) )
						? esc_html( $this->options['cookie_consent']['deny'] ) : __( 'Decline', 'super-simple-tracking-codes' ),
					'allow' => ( isset( $this->options['cookie_consent']['allow'] ) && ! empty( $this->options['cookie_consent']['allow'] ) )
						? esc_html( $this->options['cookie_consent']['allow'] ) : __( 'Allow cookies', 'super-simple-tracking-codes' ),
					'link' => ( isset( $this->options['cookie_consent']['link'] ) && ! empty( $this->options['cookie_consent']['link'] ) )
						? esc_html( $this->options['cookie_consent']['link'] ) : __( 'Learn more', 'super-simple-tracking-codes' ),
					'href' => ( isset( $this->options['cookie_consent']['href'] ) ) ? esc_attr( $this->options['cookie_consent']['href'] ) : ''
				),
				'palette' => array(
					'popup' => array(
						'text' => ( isset( $this->options['cookie_consent']['popup_text_color'] ) && ! empty( $this->options['cookie_consent']['popup_text_color'] ) )
							? esc_attr( $this->options['cookie_consent']['popup_text_color'] ) : '#ffffff',
						'background' => ( isset( $this->options['cookie_consent']['popup_background_color'] ) && ! empty( $this->options['cookie_consent']['popup_background_color'] ) )
							? esc_attr( $this->options['cookie_consent']['popup_background_color'] ) : '#23282d'
					),
					'button' => array(
						'text' => ( isset( $this->options['cookie_consent']['button_text_color'] ) && ! empty( $this->options['cookie_consent']['button_text_color'] ) )
							? esc_attr( $this->options['cookie_consent']['button_text_color'] ) : '#ffffff',
						'background' => ( isset( $this->options['cookie_consent']['button_background_color'] ) && ! empty( $this->options['cookie_consent']['button_background_color'] ) )
							? esc_attr( $this->options['cookie_consent']['button_background_color'] ) : '#0073aa'
					),
				),
				'expiryDays' => ( isset( $this->options['cookie_consent']['expiry'] ) && ! empty( $this->options['cookie_consent']['expiry'] ) )
					? esc_attr( $this->options['cookie_consent']['expiry'] ) : '1',
				'revokeBtn' => '<div style="display: none;"></div>',
			);

			wp_enqueue_script( 'cookieconsent', plugin_dir_url( __FILE__ ) . 'assets/vendor/js/cookieconsent.min.js', array(), '3.1.0', true );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/super-simple-tracking-codes-public.js', array( 'cookieconsent' ), $this->version, true );
			wp_add_inline_script( $this->plugin_name, "/* <![CDATA[ */\nwindow.sstc_cookieconsent = " . json_encode( $cookieconsent ) . ";\n/* ]]> */", 'after' );
		}

	}

	/**
	 * Prepare the Google Analytics tracking code.
	 * 
	 * @since	1.0.0
	 */
	function add_google_analytics() {

		if ( $this->gdpr_check() && $this->show_tracking_code( 'google_analytics' ) ) {
			$property_id = $this->options['google_analytics']['property_id'];

			echo <<<EOT
<!-- Google Analytics -->
<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
ga('create', '{$property_id}', 'auto');
ga('send', 'pageview');</script>
<!-- End Google Analytics -->\n
EOT;
		}

	}

	/**
	 * Prepare the Google Tag Manager tracking code.
	 * 
	 * @since	1.0.0
	 */
	function add_google_tag_manager() {

		if ( $this->gdpr_check() && $this->show_tracking_code( 'google_tag_manager' ) ) {
			$container_id = $this->options['google_tag_manager']['container_id'];

			echo <<<EOT
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{$container_id}');</script>
<noscript><iframe src='https://www.googletagmanager.com/ns.html?id={$container_id}'
height='0' width='0' style='display:none;visibility:hidden'></iframe></noscript>
<!-- End Google Tag Manager -->\n
EOT;
		}

	}

	/**
	 * Prepare the Facebook Pixel tracking code.
	 * 
	 * @since	1.0.0
	 */
	function add_facebook_pixel() {

		if ( $this->gdpr_check() && $this->show_tracking_code( 'facebook_pixel' ) ) {
			$pixel_id = $this->options['facebook_pixel']['pixel_id'];

			echo <<<EOT
<!-- Facebook Pixel -->
<script>!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{$pixel_id}');
fbq('track', 'PageView');</script>
<noscript><img height='1' width='1' style='display:none'
src='https://www.facebook.com/tr?id={$pixel_id}&ev=PageView&noscript=1'
/></noscript>
<!-- End Facebook Pixel -->\n
EOT;
		}

	}

	/**
	 * Check GDPR status.
	 * 
	 * @since	1.0.0
	 * @return	boolean
	 */
	function gdpr_check() {

		if ( ! isset( $this->options['gdpr']['enable'] ) || ( 1 != $this->options['gdpr']['enable'] ) ) {
			return true;
		}

		if ( isset( $_COOKIE['cookieconsent_status'] ) && ( 'allow' == $_COOKIE['cookieconsent_status'] ) ) {
			return true;
		}

		if ( isset( $_COOKIE['cookieconsent_status'] ) && ( 'deny' == $_COOKIE['cookieconsent_status'] ) ) {
			return false;
		}

		if ( isset( $this->options['cookie_consent']['type'] ) && ( 'info' == $this->options['cookie_consent']['type'] ) ) {
			return true;
		}

		if ( isset( $this->options['cookie_consent']['type'] ) && ( 'opt-out' == $this->options['cookie_consent']['type'] ) ) {
			return true;
		}

		if ( isset( $this->options['cookie_consent']['type'] ) && ( 'opt-in' == $this->options['cookie_consent']['type'] ) ) {
			return false;
		}

		return true;

	}

	/**
	 * Whether or not to show tracking code.
	 * 
	 * @since	1.0.0
	 * @param	string	$service
	 * @return	boolean
	 */
	public function show_tracking_code( $service = '' ) {
		if( empty( $service ) ) {
			return false;
		}

		if( ! isset( $this->options[$service]['enable'] )
			|| $this->options[$service]['enable'] != 1  ) {
			return false;
		}
	
		$current_role = '';
		if( is_user_logged_in() ) {
			$user = wp_get_current_user();
			$role = (array) $user->roles;
			$current_role = $role[0];
		}
	
		$exclude_roles = ( isset( $this->options[$service]['exclude_roles'] ) )
			? (array) $this->options[$service]['exclude_roles'] : [];
		if( in_array( $current_role, array_keys( $exclude_roles, 1 ) ) ) {
			return false;
		}
	
		if( isset( $this->options[$service]['sitewide'] )
			&& $this->options[$service]['sitewide'] == 1 ) {
				return true;
		} else {
			global $post;
			$specific_pages = ( isset( $this->options[$service]['specific_pages'] ) )
				? (array) $this->options[$service]['specific_pages'] : [];
			if( in_array( $post->ID, $specific_pages ) ) {
				return true;
			} else {
				return false;
			}
		}

	}


}
