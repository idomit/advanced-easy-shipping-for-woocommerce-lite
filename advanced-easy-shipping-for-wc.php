<?php
/**
 * Plugin Name:         Advanced Easy Shipping For WooCommerce Lite
 * Plugin URI:          https://idomit.com/advanced-easy-shipping-for-woocommerce
 * Description:         WooCommerce Advanced Easy Shipping Plugin helps make Shipping process easy and convenient for E-commerce Store owners. The plugin makes it easier by offering different options to decide shipping rates based on different criteria.
 * Version:             1.2
 * Author:              idomit
 * Author URI:          https://idomit.com/
 * License:             GPL-2.0+
 * License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:         advanced-easy-shipping-for-woocommerce
 * Domain Path:         /languages
 * WC requires at least: 3.0
 * WC tested up to: 4.3.0
 *
 * @package Advanced_Easy_Shipping_For_WooCommerce_Lite
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ASW_PLUGIN_URL' ) ) {
	define( 'ASW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'ASW_PLUGIN_DIR' ) ) {
	define( 'ASW_PLUGIN_DIR', dirname( __FILE__ ) );
}
if ( ! defined( 'ASW_PLUGIN_DIR_PATH' ) ) {
	define( 'ASW_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'ASW_PLUGIN_BASENAME' ) ) {
	define( 'ASW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

require_once plugin_dir_path( __FILE__ ) . 'settings/asw-constant.php';

/**
 * The code that runs during plugin activation.
 */
function asw_activate() {
	set_transient( 'asw-admin-notice', true );
	set_transient( 'asw_redirect_transist', true, 30 );
	add_option( 'asw_version', ASW_PLUGIN_VERSION );

	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
		wp_die( "<strong>Advanced Easy Shipping For WooCommerce</strong> plugin requires <strong>WooCommerce</strong>. Return to <a href='" . esc_url( get_admin_url( null, 'plugins.php' ) ) . "'>Plugins page</a>." );
	} else {
		update_option( 'chk_enable_logging', 'on' );

	}
}

/**
 * The code that runs during plugin deactivation.
 */
function asw_deactivate() {
	if ( get_transient( 'asw-admin-notice' ) ) {
		delete_transient( 'asw-admin-notice' );
	}
}

register_activation_hook( __FILE__, 'asw_activate' );
register_deactivation_hook( __FILE__, 'asw_deactivate' );

$prefix = is_network_admin() ? 'network_admin_' : '';
add_filter(
	"{$prefix}plugin_action_links_" . ASW_PLUGIN_BASENAME,
	'asw_plugin_action_links',
	10
);
/**
 * Add menu in plugins section.
 *
 * @param array $actions associative array of action names to anchor tags.
 *
 * @return array associative array of plugin action links
 *
 * @since 1.0.0
 */
function asw_plugin_action_links( $actions ) {
	$custom_actions = array(
		'configure' => sprintf(
			'<a href="%s">%s</a>',
			esc_url(
				add_query_arg(
					array(
						'page' => 'asw-main',
						'tab'  => 'general_section',
					),
					admin_url( 'admin.php' )
				)
			),
			esc_html__( 'Settings', 'advanced-easy-shipping-for-woocommerce' )
		),
		'help'      => sprintf(
			'<a href="%s" target="_blank">%s</a>',
			esc_url( 'https://idomit.com/contact-us' ),
			esc_html__( 'Help', 'advanced-easy-shipping-for-woocommerce' )
		),
	);
	return array_merge( $custom_actions, $actions );
}
add_action( 'plugins_loaded', 'asw_load_plugin_text_domain' );
/**
 * Load language file for plugin.
 *
 * @since 1.2
 */
function asw_load_plugin_text_domain() {
	load_plugin_textdomain( 'advanced-easy-shipping-for-woocommerce', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
require_once plugin_dir_path( __FILE__ ) . 'settings/asw-common-function.php';

require_once plugin_dir_path( __FILE__ ) . 'includes/class-asw-admin.php';

if ( ! is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'public/class-asw-shipping-method-public.php';
}
