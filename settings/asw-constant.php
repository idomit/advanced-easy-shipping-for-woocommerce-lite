<?php
/**
 * Constant of all plugins title
 *
 * @package    Advanced_Easy_Shipping_For_WooCommerce
 * @subpackage Advanced_Easy_Shipping_For_WooCommerce/settings
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! defined( 'ASW_PLUGIN_VERSION' ) ) {
	define( 'ASW_PLUGIN_VERSION', '1.2' );
}
if ( ! defined( 'ASW_SLUG' ) ) {
	define( 'ASW_SLUG', 'advanced-easy-shipping-for-woocommerce' );
}
if ( ! defined( 'ASW_PLUGIN_BASENAME' ) ) {
	define( 'ASW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'ASW_PLUGIN_NAME' ) ) {
	define( 'ASW_PLUGIN_NAME', 'Advanced Easy Shipping For WooCommerce' );
}
if ( ! defined( 'ASW_TEXT_DOMAIN' ) ) {
	define( 'ASW_TEXT_DOMAIN', 'advanced-easy-shipping-for-woocommerce' );
}
if ( ! defined( 'ASW_POST_TYPE' ) ) {
	define( 'ASW_POST_TYPE', 'idm_asw' );
}