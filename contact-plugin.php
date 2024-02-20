<?php
/**
 * Plugin Name: Listing Plugin
 * Description: Test plugin.
 * Version: 1.0
 * Author: Sohan
 * License: GPL2
 * Text Domain: listing-plugin
 * Domain Path: /languages
 */

define( 'Carbon_Fields_Plugin\PLUGIN_FILE', __FILE__ );

define( 'Carbon_Fields_Plugin\RELATIVE_PLUGIN_FILE', basename( dirname( \Carbon_Fields_Plugin\PLUGIN_FILE ) ) . '/' . basename( \Carbon_Fields_Plugin\PLUGIN_FILE ) );

add_action( 'after_setup_theme', 'carbon_fields_boot_plugin' );
function carbon_fields_boot_plugin() {
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require( __DIR__ . '/vendor/autoload.php' );
	}
	\Carbon_Fields\Carbon_Fields::boot();

	if ( is_admin() ) {
		\Carbon_Fields_Plugin\Libraries\Plugin_Update_Warning\Plugin_Update_Warning::boot();
	}
}


if ( !class_exists('ContactPlugin') ) {

    class ContactPlugin {

        public function __construct () {

            define ( 'MY_PLUGIN_PATH', plugin_dir_path ( __FILE__) );
            define ( 'MY_PLUGIN_URL', plugin_dir_url ( __FILE__) );

            require_once ( MY_PLUGIN_PATH .'vendor/autoload.php' );
        }

        public function initialize() {
            include_once MY_PLUGIN_PATH . 'includes/utilities.php';
            include_once MY_PLUGIN_PATH . 'includes/options-page.php';
            include_once MY_PLUGIN_PATH . 'includes/contact-form.php';
           // 
        }

    }
    $contactPlugin = new ContactPlugin();
    $contactPlugin->initialize();

}

// include custom jQuery
function cp_include_custom_jquery() {

    wp_register_script('custom-js', plugins_url('assets/custom-js.php', __FILE__), array('jquery'), '1.0', true);
    wp_enqueue_script('custom-js');

}
add_action('wp_enqueue_scripts', 'cp_include_custom_jquery');






