<?php
/**
 * Provide a admin area view for the plugin
 *
 * @link https://wordpress.org/plugins/super-simple-tracking-codes
 * @since 1.0.0
 *
 * @package Super_Simple_Tracking_Codes
 * @subpackage Super_Simple_Tracking_Codes/admin/partials
 */

?>
<div class="wrap">
    <h1><?php esc_html_e( 'Super Simple Tracking Codes', 'super-simple-tracking-codes' ) ?></h1>

    <form action="options.php" method="post" id="sstc-form">

        <h2 class="nav-tab-wrapper" id="sstc-tabs">
            <a class="nav-tab nav-tab-active" id="gdpr-tab" href="#top#gdpr">
                <?php esc_html_e( 'GDPR', 'super-simple-tracking-codes' ); ?>
            </a>
            <a class="nav-tab" id="google-analytics-tab" href="#top#google-analytics">
                <?php esc_html_e( 'Google Analytics', 'super-simple-tracking-codes' ); ?>
            </a>
            <a class="nav-tab" id="google-tag-manager-tab" href="#top#google-tag-manager">
                <?php esc_html_e( 'Google Tag Manager', 'super-simple-tracking-codes' ); ?>
            </a>
            <a class="nav-tab" id="facebook-pixel-tab" href="#top#facebook-pixel">
                <?php esc_html_e( 'Facebook Pixel', 'super-simple-tracking-codes' ); ?>
            </a>
        </h2>

        <div class="tab-panel" id="gdpr">
            <?php settings_fields( $this->plugin_name . '-gdpr-settings-options' ); ?>
            <?php do_settings_sections( $this->plugin_name . '-gdpr-settings-options' ); ?>

            <div id="cookie-consent">
                <?php settings_fields( $this->plugin_name . '-cookie-consent-settings-options' ); ?>
                <?php do_settings_sections( $this->plugin_name . '-cookie-consent-settings-options' ); ?>
            </div>
        </div>

        <div class="tab-panel" id="google-analytics">
            <?php settings_fields( $this->plugin_name . '-google-analytics-settings-options' ); ?>
            <?php do_settings_sections( $this->plugin_name . '-google-analytics-settings-options' ); ?>
        </div>

        <div class="tab-panel" id="google-tag-manager">
            <?php settings_fields( $this->plugin_name . '-google-tag-manager-settings-options' ); ?>
            <?php do_settings_sections( $this->plugin_name . '-google-tag-manager-settings-options' ); ?>
        </div>

        <div class="tab-panel" id="facebook-pixel">
            <?php settings_fields( $this->plugin_name . '-facebook-pixel-settings-options' ); ?>
            <?php do_settings_sections( $this->plugin_name . '-facebook-pixel-settings-options' ); ?>
        </div>

        <?php submit_button(); ?>

    </form>
</div>