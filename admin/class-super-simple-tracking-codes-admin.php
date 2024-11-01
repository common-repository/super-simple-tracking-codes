<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/super-simple-tracking-codes
 * @since      1.0.0
 *
 * @package    Super_Simple_Tracking_Codes
 * @subpackage Super_Simple_Tracking_Codes/admin
 */

class Super_Simple_Tracking_Codes_Admin {

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
     * Holds the plugin options.
	 * 
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $options
     */
	private $options;

	/**
     * Holds the site pages.
	 * 
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $pages
     */
	private $pages;

	/**
     * olds the user roles.
	 * 
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $roles
     */
	private $roles;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string    $plugin_name		The name of this plugin.
	 * @param    string    $version    		The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->options = get_option( $plugin_name );

		$this->pages = $this->get_pages();
		$this->roles = $this->get_roles();

	}

	/**
	 * Get a list of all site pages.
	 * 
	 * @since    1.0.0
	 * @access   private
	 */
	private function get_pages() {

		$list = array();

		$pages = get_pages();

		if ( count( $pages ) > 0 ) {
			foreach( $pages as $page ) {
				$depth = count( get_post_ancestors( $page->ID ) );
				$pad = str_repeat( '&nbsp;', $depth * 3 );
				$list[$page->ID] = $pad . $page->post_title;
			}
		}

		return $list;

	}

	/**
	 * Get a list of all user roles.
	 * 
	 * @since    1.0.0
	 * @access   private
	 */
	private function get_roles() {

		$list = array();

		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once ABSPATH . 'wp-admin/includes/user.php';
		}

		$roles = get_editable_roles();

		if ( count( $roles ) > 0 ) {
			foreach( $roles as $role => $details ) {
				$list[$role] = translate_user_role( $details['name'] );
			}
		}

		return $list;

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {

		if( 'settings_page_' . $this->plugin_name != $hook ) {
			return;
		}

		wp_enqueue_script( 'jquery' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/super-simple-tracking-codes-admin.js', array( 'wp-color-picker', 'jquery' ), $this->version, true );

	}

	/**
	 * Adds a settings page link to a menu
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function add_menu() {

		add_submenu_page(
			'options-general.php',
			esc_html__( 'Super Simple Tracking Codes', 'super-simple-tracking-codes' ),
			esc_html__( 'Tracking codes', 'super-simple-tracking-codes' ),
			'manage_options',
			$this->plugin_name,
			array( $this, 'settings_page' )
		);

	}

	/**
	 * Create the settings page.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function settings_page() {

		include( plugin_dir_path( __FILE__ ) . 'partials/super-simple-tracking-codes-admin-display.php' );

	}

	/**
	 * Register settings sections.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function register_sections() {

		add_settings_section(
			$this->plugin_name . '-gdpr-settings-section',
			esc_html__( 'GDPR', 'super-simple-tracking-codes' ),
			array( $this, 'gdpr_settings_section_callback' ),
			$this->plugin_name . '-gdpr-settings-options'
		);

		add_settings_section(
			$this->plugin_name . '-cookie-consent-settings-section',
			'',
			'',
			$this->plugin_name . '-cookie-consent-settings-options'
		);

		add_settings_section(
			$this->plugin_name . '-google-analytics-settings-section',
			esc_html__( 'Google Analytics', 'super-simple-tracking-codes' ),
			array( $this, 'google_analytics_settings_section_callback' ),
			$this->plugin_name . '-google-analytics-settings-options'
		);

		add_settings_section(
			$this->plugin_name . '-google-tag-manager-settings-section',
			esc_html__( 'Google Tag Manager', 'super-simple-tracking-codes' ),
			array( $this, 'google_tag_manager_settings_section_callback' ),
			$this->plugin_name . '-google-tag-manager-settings-options'
		);

		add_settings_section(
			$this->plugin_name . '-facebook-pixel-settings-section',
			esc_html__( 'Facebook Pixel', 'super-simple-tracking-codes' ),
			array( $this, 'facebook_pixel_settings_section_callback' ),
			$this->plugin_name . '-facebook-pixel-settings-options'
		);

	}

	/**
	 * Register settings fields.
	 * 
	 * @since	1.0.0
	 * @return	void
	 */
	public function register_fields() {

		// GDPR
		add_settings_field(
			$this->plugin_name . '-gdpr-enable-settings-field',
			esc_html__( 'Enable', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-gdpr-settings-options',
			$this->plugin_name . '-gdpr-settings-section',
			array(
				'type' => 'checkbox',
				'id' => 'gdpr_enable',
				'name' => $this->plugin_name . '[gdpr][enable]',
				'value' => ( isset( $this->options['gdpr']['enable'] ) )
					? $this->options['gdpr']['enable'] : '',
				'title' => __( 'Enable', 'super-simple-tracking-codes' ),
				'description' => __( 'If checked a cookie consent will be shown on the front-end of your site.', 'super-simple-tracking-codes' )
			)
		);

		// Cookie Consent
		add_settings_field(
			$this->plugin_name . '-cookie-consent-type-settings-field',
			esc_html__( 'Cookie consent type', 'super-simple-tracking-codes' ),
			array( $this, 'select_field_callback' ),
			$this->plugin_name . '-cookie-consent-settings-options',
			$this->plugin_name . '-cookie-consent-settings-section',
			array(
				'id' => 'cookie_consent_type',
				'name' => $this->plugin_name . '[cookie_consent][type]',
				'options' => [
					'info' => __( 'Informational', 'super-simple-tracking-codes' ),
					'opt-out' => __( 'Opt-out', 'super-simple-tracking-codes' ),
					'opt-in' => __( 'Opt-in', 'super-simple-tracking-codes' ),
				],
				'value' => ( isset( $this->options['cookie_consent']['type'] ) )
					? $this->options['cookie_consent']['type'] : '',
				'description' => sprintf( __( 'More info: %s', 'super-simple-tracking-codes' ), 'https://wordpress.org/plugins/super-simple-tracking-codes/#cookie%20consent%20type' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-cookie-consent-position-settings-field',
			esc_html__( 'Cookie consent position', 'super-simple-tracking-codes' ),
			array( $this, 'select_field_callback' ),
			$this->plugin_name . '-cookie-consent-settings-options',
			$this->plugin_name . '-cookie-consent-settings-section',
			array(
				'id' => 'cookie_consent_position',
				'name' => $this->plugin_name . '[cookie_consent][position]',
				'options' => [
					'top' => __( 'Top (banner)', 'super-simple-tracking-codes' ),
					'bottom' => __( 'Bottom (banner)', 'super-simple-tracking-codes' ),
					'bottom-left' => __( 'Bottom Left (window)', 'super-simple-tracking-codes' ),
					'bottom-right' => __( 'Bottom Right (window)', 'super-simple-tracking-codes' ),
				],
				'value' => ( isset( $this->options['cookie_consent']['position'] ) )
					? $this->options['cookie_consent']['position'] : '',
				'description' => __( 'Position of the cookie consent banner/window popup.', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-cookie-consent-message-settings-field',
			esc_html__( 'Message', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-cookie-consent-settings-options',
			$this->plugin_name . '-cookie-consent-settings-section',
			array(
				'type' => 'text',
				'id' => 'cookie_consent_message',
				'name' => $this->plugin_name . '[cookie_consent][message]',
				'value' => ( isset( $this->options['cookie_consent']['message'] ) )
					? $this->options['cookie_consent']['message'] : '',
				'el_class' => 'regular-text',
				'description' => __( 'Message shown on the cookie consent. (e.g. This website uses cookies to ensure you get the best experience on our website.)', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-cookie-consent-dismiss-settings-field',
			esc_html__( 'Dismiss button text', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-cookie-consent-settings-options',
			$this->plugin_name . '-cookie-consent-settings-section',
			array(
				'type' => 'text',
				'id' => 'cookie_consent_dismiss',
				'name' => $this->plugin_name . '[cookie_consent][dismiss]',
				'value' => ( isset( $this->options['cookie_consent']['dismiss'] ) )
					? $this->options['cookie_consent']['dismiss'] : '',
				'el_class' => 'regular-text',
				'description' => __( 'Text shown on the dismiss button. (e.g. Got it!)', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-cookie-consent-deny-settings-field',
			esc_html__( 'Deny button text', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-cookie-consent-settings-options',
			$this->plugin_name . '-cookie-consent-settings-section',
			array(
				'type' => 'text',
				'id' => 'cookie_consent_deny',
				'name' => $this->plugin_name . '[cookie_consent][deny]',
				'value' => ( isset( $this->options['cookie_consent']['deny'] ) )
					? $this->options['cookie_consent']['deny'] : '',
				'el_class' => 'regular-text',
				'description' => __( 'Text shown on the deny button. (e.g. Decline)', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-cookie-consent-allow-settings-field',
			esc_html__( 'Allow button text', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-cookie-consent-settings-options',
			$this->plugin_name . '-cookie-consent-settings-section',
			array(
				'type' => 'text',
				'id' => 'cookie_consent_allow',
				'name' => $this->plugin_name . '[cookie_consent][allow]',
				'value' => ( isset( $this->options['cookie_consent']['allow'] ) )
					? $this->options['cookie_consent']['allow'] : '',
				'el_class' => 'regular-text',
				'description' => __( 'Text shown on the allow button. (e.g. Allow cookies)', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-cookie-consent-link-settings-field',
			esc_html__( 'Policy link text', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-cookie-consent-settings-options',
			$this->plugin_name . '-cookie-consent-settings-section',
			array(
				'type' => 'text',
				'id' => 'cookie_consent_link',
				'name' => $this->plugin_name . '[cookie_consent][link]',
				'value' => ( isset( $this->options['cookie_consent']['link'] ) )
					? $this->options['cookie_consent']['link'] : '',
				'el_class' => 'regular-text',
				'description' => __( 'Text shown on the link to your privacy policy page. (e.g. Learn more)', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-cookie-consent-href-settings-field',
			esc_html__( 'Policy link URL', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-cookie-consent-settings-options',
			$this->plugin_name . '-cookie-consent-settings-section',
			array(
				'type' => 'text',
				'id' => 'cookie_consent_href',
				'name' => $this->plugin_name . '[cookie_consent][href]',
				'placeholder' => 'http://',
				'value' => ( isset( $this->options['cookie_consent']['href'] ) )
					? $this->options['cookie_consent']['href'] : '',
				'el_class' => 'regular-text',
				'description' => __( 'Link to your privacy policy page. (e.g. www.example.com/cookiepolicy)', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-cookie-consent-expiry-settings-field',
			esc_html__( 'Expiry Days', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-cookie-consent-settings-options',
			$this->plugin_name . '-cookie-consent-settings-section',
			array(
				'type' => 'number',
				'min' => '1',
				'max' => '365',
				'step' => '1',
				'id' => 'cookie_consent_expiry',
				'name' => $this->plugin_name . '[cookie_consent][expiry]',
				'placeholder' => '1',
				'value' => ( isset( $this->options['cookie_consent']['expiry'] ) )
					? $this->options['cookie_consent']['expiry'] : '',
				'title' => __( 'Day(s)', 'super-simple-tracking-codes' ),
				'description' => __( 'How many days the cookie consent should not be displayed after user accepts the consent.', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-cookie-consent-popup-text-color-settings-field',
			esc_html__( 'Popup text color', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-cookie-consent-settings-options',
			$this->plugin_name . '-cookie-consent-settings-section',
			array(
				'type' => 'text',
				'id' => 'cookie_consent_popup_text_color',
				'name' => $this->plugin_name . '[cookie_consent][popup_text_color]',
				'value' => ( isset( $this->options['cookie_consent']['popup_text_color'] ) )
					? $this->options['cookie_consent']['popup_text_color'] : '',
				'el_class' => 'sstc-color-field',
				'description' => ''
			)
		);

		add_settings_field(
			$this->plugin_name . '-cookie-consent-popup-background-color-settings-field',
			esc_html__( 'Popup background color', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-cookie-consent-settings-options',
			$this->plugin_name . '-cookie-consent-settings-section',
			array(
				'type' => 'text',
				'id' => 'cookie_consent_popup_background_color',
				'name' => $this->plugin_name . '[cookie_consent][popup_background_color]',
				'value' => ( isset( $this->options['cookie_consent']['popup_background_color'] ) )
					? $this->options['cookie_consent']['popup_background_color'] : '',
				'el_class' => 'sstc-color-field',
				'description' => ''
			)
		);

		add_settings_field(
			$this->plugin_name . '-cookie-consent-button-text-color-settings-field',
			esc_html__( 'Button text color', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-cookie-consent-settings-options',
			$this->plugin_name . '-cookie-consent-settings-section',
			array(
				'type' => 'text',
				'id' => 'cookie_consent_button_text_color',
				'name' => $this->plugin_name . '[cookie_consent][button_text_color]',
				'value' => ( isset( $this->options['cookie_consent']['button_text_color'] ) )
					? $this->options['cookie_consent']['button_text_color'] : '',
				'el_class' => 'sstc-color-field',
				'description' => ''
			)
		);

		add_settings_field(
			$this->plugin_name . '-cookie-consent-button-background-color-settings-field',
			esc_html__( 'Button background color', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-cookie-consent-settings-options',
			$this->plugin_name . '-cookie-consent-settings-section',
			array(
				'type' => 'text',
				'id' => 'cookie_consent_button_background_color',
				'name' => $this->plugin_name . '[cookie_consent][button_background_color]',
				'value' => ( isset( $this->options['cookie_consent']['button_background_color'] ) )
					? $this->options['cookie_consent']['button_background_color'] : '',
				'el_class' => 'sstc-color-field',
				'description' => ''
			)
		);

		// Google Analytics
		add_settings_field(
			$this->plugin_name . '-google-analytics-enable-settings-field',
			esc_html__( 'Enable', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-google-analytics-settings-options',
			$this->plugin_name . '-google-analytics-settings-section',
			array(
				'type' => 'checkbox',
				'id' => 'google_analytics_enable',
				'name' => $this->plugin_name . '[google_analytics][enable]',
				'value' => ( isset( $this->options['google_analytics']['enable'] ) )
					? $this->options['google_analytics']['enable'] : '',
				'title' => __( 'Enable', 'super-simple-tracking-codes' ),
				'description' => __( 'If this is not checked, the tracking code will not be placed on your site. (All settings below will stay saved!)', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-google-analytics-property-id-settings-field',
			esc_html__( 'Property ID', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-google-analytics-settings-options',
			$this->plugin_name . '-google-analytics-settings-section',
			array(
				'type' => 'text',
				'id' => 'google_analytics_property_id',
				'name' => $this->plugin_name . '[google_analytics][property_id]',
				'value' => ( isset( $this->options['google_analytics']['property_id'] ) )
					? $this->options['google_analytics']['property_id'] : '',
				'el_class' => 'regular-text',
				'description' => __( 'Your Property ID of the Google Analytics Property you wish to track. (e.g. UA-XXXXXXXXX-X)', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-google-analytics-sitewide-settings-field',
			esc_html__( 'Use sitewide?', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-google-analytics-settings-options',
			$this->plugin_name . '-google-analytics-settings-section',
			array(
				'type' => 'checkbox',
				'id' => 'google_analytics_sitewide',
				'name' => $this->plugin_name . '[google_analytics][sitewide]',
				'value' => ( isset( $this->options['google_analytics']['sitewide'] ) )
					? $this->options['google_analytics']['sitewide'] : '',
				'title' => __( 'Sitewide', 'super-simple-tracking-codes' ),
				'description' => __( 'If checked the tracking code will be added to every page.', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-google-analytics-pecific-pages-settings-field',
			esc_html__( 'Select specific pages', 'super-simple-tracking-codes' ),
			array( $this, 'select_field_callback' ),
			$this->plugin_name . '-google-analytics-settings-options',
			$this->plugin_name . '-google-analytics-settings-section',
			array(
				'id' => 'google_analytics_pecific_pages',
				'name' => $this->plugin_name . '[google_analytics][pecific_pages]',
				'size' => 10,
				'multiple' => true,
				'options' => $this->pages,
				'value' => ( isset( $this->options['google_analytics']['pecific_pages'] ) )
					? $this->options['google_analytics']['pecific_pages'] : '',
				'description' => __( 'Selected specific pages where you want the tracking code to be added.', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-google-analytics-exclude-roles-settings-field',
			esc_html__( 'Exclude user roles from tracking', 'super-simple-tracking-codes' ),
			array( $this, 'exclude_roles_field_callback' ),
			$this->plugin_name . '-google-analytics-settings-options',
			$this->plugin_name . '-google-analytics-settings-section',
			array(
				'id' => 'google_analytics_exclude_roles',
				'name' => $this->plugin_name . '[google_analytics][exclude_roles]',
				'value' => ( isset( $this->options['google_analytics']['exclude_roles'] ) )
					? $this->options['google_analytics']['exclude_roles'] : '',
				'description' => __( 'If a user with a checked roles in logged in, the tracking code will not be placed on your site.', 'super-simple-tracking-codes' )
			)
		);

		// Google Tag Manager
		add_settings_field(
			$this->plugin_name . '-google-tag-manager-enable-settings-field',
			esc_html__( 'Enable?', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-google-tag-manager-settings-options',
			$this->plugin_name . '-google-tag-manager-settings-section',
			array(
				'type' => 'checkbox',
				'id' => 'google_tag_manager_enable',
				'name' => $this->plugin_name . '[google_tag_manager][enable]',
				'value' => ( isset( $this->options['google_tag_manager']['enable'] ) )
					? $this->options['google_tag_manager']['enable'] : '',
				'title' => __( 'Enable', 'super-simple-tracking-codes' ),
				'description' => __( 'If this is not checked, the tracking code will not be placed on your site. (All settings below will stay saved!)', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-google-tag-manager-container-id-settings-field',
			esc_html__( 'Container ID', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-google-tag-manager-settings-options',
			$this->plugin_name . '-google-tag-manager-settings-section',
			array(
				'type' => 'text',
				'id' => 'google_tag_manager_container_id',
				'name' => $this->plugin_name . '[google_tag_manager][container_id]',
				'value' => ( isset( $this->options['google_tag_manager']['container_id'] ) )
					? $this->options['google_tag_manager']['container_id'] : '',
				'el_class' => 'regular-text',
				'description' => __( 'Your Container ID of the Google Tag Manager Container you wish to track. (e.g. GTM-XXXXXX)', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-google-tag-manager-sitewide-settings-field',
			esc_html__( 'Use sitewide?', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-google-tag-manager-settings-options',
			$this->plugin_name . '-google-tag-manager-settings-section',
			array(
				'type' => 'checkbox',
				'id' => 'google_tag_manager_sitewide',
				'name' => $this->plugin_name . '[google_tag_manager][sitewide]',
				'value' => ( isset( $this->options['google_tag_manager']['sitewide'] ) )
					? $this->options['google_tag_manager']['sitewide'] : '',
				'title' => __( 'Sitewide', 'super-simple-tracking-codes' ),
				'description' => __( 'If checked the tracking code will be added to every page.', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-google-tag-manager-pecific-pages-settings-field',
			esc_html__( 'Select specific pages', 'super-simple-tracking-codes' ),
			array( $this, 'select_field_callback' ),
			$this->plugin_name . '-google-tag-manager-settings-options',
			$this->plugin_name . '-google-tag-manager-settings-section',
			array(
				'id' => 'google_tag_manager_pecific_pages',
				'name' => $this->plugin_name . '[google_tag_manager][pecific_pages]',
				'size' => 10,
				'multiple' => true,
				'options' => $this->pages,
				'value' => ( isset( $this->options['google_tag_manager']['pecific_pages'] ) )
					? $this->options['google_tag_manager']['pecific_pages'] : '',
				'description' => __( 'Selected specific pages where you want the tracking code to be added.', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-google-tag-manager-exclude-roles-settings-field',
			esc_html__( 'Exclude user roles from tracking', 'super-simple-tracking-codes' ),
			array( $this, 'exclude_roles_field_callback' ),
			$this->plugin_name . '-google-tag-manager-settings-options',
			$this->plugin_name . '-google-tag-manager-settings-section',
			array(
				'id' => 'google_tag_manager_exclude_roles',
				'name' => $this->plugin_name . '[google_tag_manager][exclude_roles]',
				'value' => ( isset( $this->options['google_tag_manager']['exclude_roles'] ) )
					? $this->options['google_tag_manager']['exclude_roles'] : '',
				'description' => __( 'If a user with a checked roles in logged in, the tracking code will not be placed on your site.', 'super-simple-tracking-codes' )
			)
		);

		// Facebook Pixel
		add_settings_field(
			$this->plugin_name . '-facebook-pixel-enable-settings-field',
			esc_html__( 'Enable?', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-facebook-pixel-settings-options',
			$this->plugin_name . '-facebook-pixel-settings-section',
			array(
				'type' => 'checkbox',
				'id' => 'facebook_pixel_enable',
				'name' => $this->plugin_name . '[facebook_pixel][enable]',
				'value' => ( isset( $this->options['facebook_pixel']['enable'] ) )
					? $this->options['facebook_pixel']['enable'] : '',
				'title' => __( 'Enable', 'super-simple-tracking-codes' ),
				'description' => __( 'If this is not checked, the tracking code will not be placed on your site. (All settings below will stay saved!)', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-facebook-pixel-pixel-id-settings-field',
			esc_html__( 'Pixel ID', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-facebook-pixel-settings-options',
			$this->plugin_name . '-facebook-pixel-settings-section',
			array(
				'type' => 'text',
				'id' => 'facebook_pixel_pixel_id',
				'name' => $this->plugin_name . '[facebook_pixel][pixel_id]',
				'value' => ( isset( $this->options['facebook_pixel']['pixel_id'] ) )
					? $this->options['facebook_pixel']['pixel_id'] : '',
				'el_class' => 'regular-text',
				'description' => __( 'Your Pixel ID of the Facebook Pixel you wish to track.', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-facebook-pixel-sitewide-settings-field',
			esc_html__( 'Use sitewide?', 'super-simple-tracking-codes' ),
			array( $this, 'input_field_callback' ),
			$this->plugin_name . '-facebook-pixel-settings-options',
			$this->plugin_name . '-facebook-pixel-settings-section',
			array(
				'type' => 'checkbox',
				'id' => 'facebook_pixel_sitewide',
				'name' => $this->plugin_name . '[facebook_pixel][sitewide]',
				'value' => ( isset( $this->options['facebook_pixel']['sitewide'] ) )
					? $this->options['facebook_pixel']['sitewide'] : '',
				'title' => __( 'Sitewide', 'super-simple-tracking-codes' ),
				'description' => __( 'If checked the tracking code will be added to every page.', 'super-simple-tracking-codes' )
			)
		);

		add_settings_field(
			$this->plugin_name . '-facebook-pixel-pecific-pages-settings-field',
			esc_html__( 'Select specific pages', 'super-simple-tracking-codes' ),
			array( $this, 'select_field_callback' ),
			$this->plugin_name . '-facebook-pixel-settings-options',
			$this->plugin_name . '-facebook-pixel-settings-section',
			array(
				'id' => 'facebook_pixel_pecific_pages',
				'name' => $this->plugin_name . '[facebook_pixel][pecific_pages]',
				'size' => 10,
				'multiple' => true,
				'options' => $this->pages,
				'value' => ( isset( $this->options['facebook_pixel']['pecific_pages'] ) )
					? $this->options['facebook_pixel']['pecific_pages'] : '',
				'description' => __( 'Selected specific pages where you want the tracking code to be added.', 'super-simple-tracking-codes' )
			)
		);
	
		add_settings_field(
			$this->plugin_name . '-facebook-pixel-exclude-roles-settings-field',
			esc_html__( 'Exclude user roles from tracking', 'super-simple-tracking-codes' ),
			array( $this, 'exclude_roles_field_callback' ),
			$this->plugin_name . '-facebook-pixel-settings-options',
			$this->plugin_name . '-facebook-pixel-settings-section',
			array(
				'id' => 'facebook_pixel_exclude_roles',
				'name' => $this->plugin_name . '[facebook_pixel][exclude_roles]',
				'value' => ( isset( $this->options['facebook_pixel']['exclude_roles'] ) )
					? $this->options['facebook_pixel']['exclude_roles'] : '',
				'description' => __( 'If a user with a checked roles in logged in, the tracking code will not be placed on your site.', 'super-simple-tracking-codes' )
			)
		);

	}

	/**
	 * Register plugin settings.
	 *
	 * @since	1.0.0
	 * @return	void
	 */
	public function register_settings() {

        register_setting(
            $this->plugin_name . '-gdpr-settings-options',
            $this->plugin_name,
            array( $this, 'sanitize' )
		);

        register_setting(
            $this->plugin_name . '-cookie-consent-settings-options',
            $this->plugin_name,
            array( $this, 'sanitize' )
		);

        register_setting(
            $this->plugin_name . '-google-analytics-settings-options',
            $this->plugin_name,
            array( $this, 'sanitize' )
		);

        register_setting(
            $this->plugin_name . '-google-tag-manager-settings-options',
            $this->plugin_name,
            array( $this, 'sanitize' )
		);

        register_setting(
            $this->plugin_name . '-facebook-pixel-settings-options',
            $this->plugin_name,
            array( $this, 'sanitize' )
		);
	
	}

    /**
     * Sanitize each setting field as needed
     *
     * @param	array	$input	Contains all settings fields as array keys.
     */
	public function sanitize( $input ) {

		$output = array();
		
		return apply_filters( 'sanitize_options', $input, $output );

	}

	/**
	 * Callback function for GDPR settings section.
	 * 
	 * @since	1.0.0
     * @return	string
	 */
	public function gdpr_settings_section_callback() {

		echo '<p>' . esc_html__( 'Settings for GDPR.', 'super-simple-tracking-codes' ) . '</p>';

	}

	/**
	 * Callback function for Cookie Consent settings section.
	 * 
	 * @since	1.0.0
     * @return	string
	 */
	public function cookie_consent_settings_section_callback() {

		echo '<p>' . esc_html__( 'Settings for Cookie Consent.', 'super-simple-tracking-codes' ) . '</p>';

	}

	/**
	 * Callback function for Google Analytics settings section.
	 * 
	 * @since	1.0.0
     * @return	string
	 */
	public function google_analytics_settings_section_callback() {

		echo '<p>' . esc_html__( 'Settings for Google Analytics.', 'super-simple-tracking-codes' ) . '</p>';

	}

	/**
	 * Callback function for Google Tag Manager settings section.
	 * 
	 * @since	1.0.0
     * @return	string
	 */
	public function google_tag_manager_settings_section_callback() {

		echo '<p>' . esc_html__( 'Settings for Google Tag Manager.', 'super-simple-tracking-codes' ) . '</p>';

	}

	/**
	 * Callback function for Facebook Pixel settings section.
	 * 
	 * @since	1.0.0
     * @return	string
	 */
	public function facebook_pixel_settings_section_callback() {

		echo '<p>' . esc_html__( 'Settings for Facebook Pixel.', 'super-simple-tracking-codes' ) . '</p>';

	}

	/**
	 * Callback function for input field.
	 * 
	 * @since	1.0.0
     * @param	array	$args
     * @return	string
	 */
	public function input_field_callback( $args ) {

		echo ( isset( $args['type'] ) && ( 'checkbox' == $args['type'] ) ) ? '<label>' : '';
		echo '<input';
			echo ( isset( $args['type'] ) && ! empty( $args['type'] ) ) ? ' type="' . esc_attr( $args['type'] ) . '"' : ' type="text"';
			echo ( isset( $args['name'] ) && ! empty( $args['name'] ) ) ? ' name="' . esc_attr( $args['name'] ) . '"' : '';
			echo ( isset( $args['id'] ) && ! empty( $args['id'] ) ) ? ' id="' . esc_attr( $args['id'] ) . '"' : '';
			echo ( isset( $args['placeholder'] ) && ! empty( $args['placeholder'] ) ) ? ' placeholder="' . esc_attr( $args['placeholder'] ) . '"' : '';
			echo ( isset( $args['type'] ) && ( 'checkbox' == $args['type'] ) ) ? ' value="1"' :
				( ( isset( $args['value'] ) && ! empty( $args['value'] ) ) ? ' value="' . esc_attr( $args['value'] ) . '"' : '' );
			echo ( isset( $args['min'] ) && ! empty( $args['min'] ) ) ? ' min="' . esc_attr( $args['min'] ) . '"' : '';
			echo ( isset( $args['max'] ) && ! empty( $args['max'] ) ) ? ' max="' . esc_attr( $args['max'] ) . '"' : '';
			echo ( isset( $args['step'] ) && ! empty( $args['step'] ) ) ? ' step="' . esc_attr( $args['step'] ) . '"' : '';
			echo ( isset( $args['el_class'] ) && ! empty( $args['el_class'] ) ) ? ' class="' . esc_attr( $args['el_class'] ) . '"' : '';
			echo ( isset( $args['type'] ) && ( 'checkbox' == $args['type'] ) ) ? ' ' . checked( 1 == $args['value'], true, false ) : '';
		echo '>';
		echo ( isset( $args['title'] ) && ! empty( $args['title'] ) ) ? ' ' . esc_attr( $args['title'] ) : '';
		echo ( isset( $args['type'] ) && ( 'checkbox' == $args['type'] ) ) ? '</label>' : '';

		if ( isset( $args['description'] ) && ! empty( $args['description'] ) ) {
			echo '<p class="description">' . esc_html__( $args['description'] ) . '</p>';
		}

	}

	/**
	 * Callback function for select field.
	 * 
	 * @since	1.0.0
     * @param	array	$args
     * @return	string
	 */
	public function select_field_callback( $args ) {

		echo '<select';
			echo ( isset( $args['name'] ) && ! empty( $args['name'] ) ) ? ' name="' . esc_attr( $args['name'] ) . '"' : '';
			echo ( isset( $args['id'] ) && ! empty( $args['id'] ) ) ? ' id="' . esc_attr( $args['id'] ) . '"' : '';
			echo ( isset( $args['size'] ) && ! empty( $args['size'] ) ) ? ' size="' . esc_attr( $args['size'] ) . '"' : '';
			echo ( isset( $args['multiple'] ) && $args['multiple'] ) ? ' multiple' : '';
			echo ' style="min-width:25em"';
		echo '>';
			foreach ( $args['options'] as $key => $val ) {
				echo '<option value="' . esc_attr( $key ) . '" ' . selected( $args['value'] == $key, true, false ) . '>';
					esc_html_e( $val );
				echo '</option>';
			}
		echo '</select>';

		if ( isset( $args['description'] ) && ! empty( $args['description'] ) ) {
			echo '<p class="description">' . esc_html__( $args['description'] ) . '</p>';
		}

	}

	/**
	 * Callback function for exclude roles settings field.
	 * 
	 * @since	1.0.0
     * @param	array	$args
     * @return	string
	 */
	public function exclude_roles_field_callback( $args ) {

		foreach( $this->roles as $role => $name ) {
			echo '<label>';
				echo '<input type="checkbox" value="1"';
					echo ' name="' . esc_attr( $args['name'] ) . '[' . esc_attr( $role ) . ']" ';
					echo ' ' . checked( isset( $args['value'][$role] ) && $args['value'][$role] == 1, true, false );
				echo '>&nbsp;' . esc_html__( $name );
			echo '</label><br>';
		}

		if ( isset( $args['description'] ) && ! empty( $args['description'] ) ) {
			echo '<p class="description">' . esc_html__( $args['description'] ) . '</p>';
		}

	}

}
