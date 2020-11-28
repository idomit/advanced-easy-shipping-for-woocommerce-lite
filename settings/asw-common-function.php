<?php
/**
 * Common functions for plugins.
 *
 * @since      1.0.0
 * @package    Advanced_Easy_Shipping_For_WooCommerce
 * @subpackage Advanced_Easy_Shipping_For_WooCommerce/settings
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Get country list.
 *
 * @return false|string
 */
function asw_get_country_list() {
	$countries     = WC()->countries->get_allowed_countries() + WC()->countries->get_shipping_countries();
	$country_array = array();
	foreach ( $countries as $country_key => $country_val ) {
		$country_array[ $country_key ] = $country_val;
	}
	return wp_json_encode( $countries );
}
/**
 * Get options for based on cart.
 *
 * @return false|string
 */
function asw_get_based_on_cart_options() {
	$gbocopt                 = array();
	$gbocopt['cc_products']  = esc_html__( 'Contains Simple Product', 'advanced-easy-shipping-for-woocommerce' );
	$gbocopt['cc_categorys'] = esc_html__( 'Contains Category', 'advanced-easy-shipping-for-woocommerce' );
	$gbocopt['cc_tags']      = esc_html__( 'Contains Tags', 'advanced-easy-shipping-for-woocommerce' );
	$gbocopt['cc_skus']      = esc_html__( 'Contains SKUs', 'advanced-easy-shipping-for-woocommerce' );
	$gbocopt['cc_variables'] = esc_html__( 'Contains Variable Product', 'advanced-easy-shipping-for-woocommerce' );
	return wp_json_encode( $gbocopt );
}
/**
 * Get options for based on locations.
 *
 * @return false|string
 */
function asw_get_based_on_locations_options() {
	$gbolopt                = array();
	$gbolopt['cc_country']  = esc_html__( 'Contains Country', 'advanced-easy-shipping-for-woocommerce' );
	$gbolopt['cc_state']    = esc_html__( 'Contains State', 'advanced-easy-shipping-for-woocommerce' );
	$gbolopt['cc_zone']     = esc_html__( 'Contains Zone', 'advanced-easy-shipping-for-woocommerce' );
	$gbolopt['cc_city']     = esc_html__( 'Contains City', 'advanced-easy-shipping-for-woocommerce' );
	$gbolopt['cc_postcode'] = esc_html__( 'Contains Postcode', 'advanced-easy-shipping-for-woocommerce' );
	return wp_json_encode( $gbolopt );
}
/**
 * Get options for based on cart specific options.
 *
 * @return false|string
 */
function asw_get_based_on_cart_specific_options() {
	$gbocsopt                        = array();
	$gbocsopt['cc_subtotal_af_disc'] = esc_html__( 'Cart Subtotal(After Coupon Discount)', 'advanced-easy-shipping-for-woocommerce' );
	$gbocsopt['cc_total_qty']        = esc_html__( 'Total Cart Qty', 'advanced-easy-shipping-for-woocommerce' );
	$gbocsopt['cc_total_weight']     = esc_html__( 'Total Cart Weight', 'advanced-easy-shipping-for-woocommerce' );
	$gbocsopt['cc_total_width']      = esc_html__( 'Total Cart Width', 'advanced-easy-shipping-for-woocommerce' );
	$gbocsopt['cc_total_height']     = esc_html__( 'Total Cart Height', 'advanced-easy-shipping-for-woocommerce' );
	$gbocsopt['cc_total_length']     = esc_html__( 'Total Cart Length', 'advanced-easy-shipping-for-woocommerce' );
	$gbocsopt['cc_subtotal_ex_tax']  = esc_html__( 'Cart Subtotal With Ex. Tax', 'advanced-easy-shipping-for-woocommerce' );
	$gbocsopt['cc_subtotal_inc_tax'] = esc_html__( 'Cart Subtotal With Inc. Tax', 'advanced-easy-shipping-for-woocommerce' );
	$gbocsopt['cc_coupon']           = esc_html__( 'Coupon', 'advanced-easy-shipping-for-woocommerce' );
	$gbocsopt['cc_shipping_class']   = esc_html__( 'Contains Shipping Class', 'advanced-easy-shipping-for-woocommerce' );
	return wp_json_encode( $gbocsopt );
}
/**
 * Get options for based on user specific options.
 *
 * @return false|string
 */
function asw_get_based_on_user_specific_options() {
	$gbousopt                 = array();
	$gbousopt['cc_username']  = esc_html__( 'User', 'advanced-easy-shipping-for-woocommerce' );
	$gbousopt['cc_user_role'] = esc_html__( 'User Role', 'advanced-easy-shipping-for-woocommerce' );
	return wp_json_encode( $gbousopt );
}
/**
 * Get options for based on product specific options.
 *
 * @return false|string
 */
function asw_get_based_on_product_specific_options() {
	$gbopsopt                    = array();
	$gbopsopt['cc_tc_spec']      = esc_html__( 'Contain Total Cart', 'advanced-easy-shipping-for-woocommerce' );
	$gbopsopt['cc_prd_spec']     = esc_html__( 'Contain Simple Products', 'advanced-easy-shipping-for-woocommerce' );
	$gbopsopt['cc_var_prd_spec'] = esc_html__( 'Contain Variable Products', 'advanced-easy-shipping-for-woocommerce' );
	$gbopsopt['cc_cat_spec']     = esc_html__( 'Contain Category', 'advanced-easy-shipping-for-woocommerce' );
	$gbopsopt['cc_shpc_spec']    = esc_html__( 'Contain Shipping Class', 'advanced-easy-shipping-for-woocommerce' );
	return wp_json_encode( $gbopsopt );
}
/**
 * Get all fields type.
 *
 * @return false|string
 */
function asw_array_type_of_fields() {
	$type_of_arr                        = array();
	$type_of_arr['cc_products']         = 'select';
	$type_of_arr['cc_country']          = 'select';
	$type_of_arr['cc_subtotal_af_disc'] = 'input';
	$type_of_arr['cc_tc_spec']          = 'label';
	return wp_json_encode( $type_of_arr );
}
/**
 * Get conditional operators.
 *
 * @param string $check_condition will work based on condition.
 *
 * @return false|string
 */
function asw_conditional_operator( $check_condition = '' ) {
	$array_type_of_field = json_decode( asw_array_type_of_fields(), true );
	$get_type_of_field   = '';
	if ( ! empty( $check_condition ) ) {
		$get_type_of_field = $array_type_of_field[ $check_condition ];
	}
	$cop                 = array();
	$cop['equal_to']     = esc_html__( 'Equal to', 'advanced-easy-shipping-for-woocommerce' );
	$cop['not_equal_to'] = esc_html__( 'Not equal to', 'advanced-easy-shipping-for-woocommerce' );
	if ( '' !== $check_condition ) {
		if ( 'input' === $get_type_of_field && ! empty( $get_type_of_field ) ) {
			$cop['less_then']        = esc_html__( 'Less Then', 'advanced-easy-shipping-for-woocommerce' );
			$cop['less_equal_to']    = esc_html__( 'Less Then Equal To', 'advanced-easy-shipping-for-woocommerce' );
			$cop['greater_then']     = esc_html__( 'Greater Then', 'advanced-easy-shipping-for-woocommerce' );
			$cop['greater_equal_to'] = esc_html__( 'Greater Then Equal to', 'advanced-easy-shipping-for-woocommerce' );
		}
	} else {
		$cop['less_then']        = esc_html__( 'Less Then', 'advanced-easy-shipping-for-woocommerce' );
		$cop['less_equal_to']    = esc_html__( 'Less Then Equal To', 'advanced-easy-shipping-for-woocommerce' );
		$cop['greater_then']     = esc_html__( 'Greater Then', 'advanced-easy-shipping-for-woocommerce' );
		$cop['greater_equal_to'] = esc_html__( 'Greater Then Equal to', 'advanced-easy-shipping-for-woocommerce' );
	}
	return wp_json_encode( $cop );
}
/**
 * Get apply per unit options.
 *
 * @param string $condition will work based on condition.
 *
 * @return false|string
 */
function asw_per_unit_options( $condition ) {
	$gpuo        = array();
	$gpuo['qty'] = esc_html__( 'QTY', 'advanced-easy-shipping-for-woocommerce' );
	if ( 'cc_tc_spec' === $condition ) {
		$cart_unit_data_fn = asw_per_unit_cart_options();
		$cart_unit_data    = json_decode( $cart_unit_data_fn, true );
		$gpuo              = array_merge( $gpuo, $cart_unit_data );
	} else {
		$item_unit_data_fn = asw_per_unit_item_options();
		$item_unit_data    = json_decode( $item_unit_data_fn, true );
		$gpuo              = array_merge( $gpuo, $item_unit_data );
	}
	$gpuo['weight'] = esc_html__( 'Weight', 'advanced-easy-shipping-for-woocommerce' );
	$gpuo['width']  = esc_html__( 'Width', 'advanced-easy-shipping-for-woocommerce' );
	$gpuo['height'] = esc_html__( 'Height', 'advanced-easy-shipping-for-woocommerce' );
	$gpuo['length'] = esc_html__( 'Length', 'advanced-easy-shipping-for-woocommerce' );
	return wp_json_encode( $gpuo );
}
/**
 * Apply per unit items options.
 *
 * @return false|string
 */
function asw_per_unit_item_options() {
	$gpuo                           = array();
	$gpuo['item_total_with_tax']    = esc_html__( 'Items total with tax', 'advanced-easy-shipping-for-woocommerce' );
	$gpuo['item_total_without_tax'] = esc_html__( 'Items total without tax', 'advanced-easy-shipping-for-woocommerce' );
	return wp_json_encode( $gpuo );
}
/**
 * Apply per unit cart options.
 *
 * @return false|string
 */
function asw_per_unit_cart_options() {
	$gpuo                        = array();
	$gpuo['st_with_tax']         = esc_html__( 'Subtotal with tax', 'advanced-easy-shipping-for-woocommerce' );
	$gpuo['st_with_disc']        = esc_html__( 'Subtotal with discount', 'advanced-easy-shipping-for-woocommerce' );
	$gpuo['st_without_tax_disc'] = esc_html__( 'Subtotal without tax and discount', 'advanced-easy-shipping-for-woocommerce' );
	$gpuo['st_with_tax_disc']    = esc_html__( 'Subtotal with tax and discount', 'advanced-easy-shipping-for-woocommerce' );
	return wp_json_encode( $gpuo );
}
/**
 * Check array key exists or not.
 *
 * @param string $key   arrays key will work here.
 *
 * @param array  $array arrays.
 *
 * @return string
 */
function asw_check_array_key_exists( $key, $array ) {
	$var_name = '';
	if ( ! empty( $array ) ) {
		if ( array_key_exists( $key, $array ) ) {
			$var_name = $array[ $key ];
		}
	}
	return $var_name;
}
/**
 * Placeholder message for fields
 *
 * @return string
 */
function asw_placeholder_for_fields() {
	$asw_placeholder_arr                     = array();
	$asw_placeholder_arr['country']          = esc_html__( 'Select Countries', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['state']            = esc_html__( 'Select States', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['zone']             = esc_html__( 'Select Zones', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['products']         = esc_html__( 'Select Products', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['prd_spec']         = esc_html__( 'Select Products', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['categorys']        = esc_html__( 'Select Categories', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['cat_spec']         = esc_html__( 'Select Categories', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['tags']             = esc_html__( 'Select Tags', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['skus']             = esc_html__( 'Select SKUs', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['variables']        = esc_html__( 'Select Variable Products', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['var_prd_spec']     = esc_html__( 'Select Variable Products', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['coupon']           = esc_html__( 'Select Coupons', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['shipping_class']   = esc_html__( 'Select Shipping Classes', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['shpc_spec']        = esc_html__( 'Select Shipping Classes', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['username']         = esc_html__( 'Select Users By Username', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['user_role']        = esc_html__( 'Select User Role', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['total_weight']     = esc_html__( 'Enter Total Cart Weight', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['total_width']      = esc_html__( 'Enter Total Cart Width', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['total_qty']        = esc_html__( 'Enter Total Cart Quantity', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['total_height']     = esc_html__( 'Enter Total Cart Height', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['total_length']     = esc_html__( 'Enter Total Cart Length', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['subtotal_ex_tax']  = esc_html__( 'Enter Subtotal With Exclude Tax', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['subtotal_inc_tax'] = esc_html__( 'Enter Subtotal With Include Tax', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['subtotal_af_disc'] = esc_html__( 'Enter Subtotal After Discount', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['city']             = esc_html__( 'Enter City Name With Comma Separate', 'advanced-easy-shipping-for-woocommerce' );
	$asw_placeholder_arr['postcode']         = esc_html__( 'Enter Postcode With Comma Separate', 'advanced-easy-shipping-for-woocommerce' );
	return wp_json_encode( $asw_placeholder_arr );
}
/**
 * Get current site language.
 *
 * @return string $default_lang It will return default language for site.
 *
 * @since 1.2
 */
function idm_asw_current_site_language() {
	$get_site_language = get_bloginfo( 'language' );
	if ( false !== strpos( $get_site_language, '-' ) ) {
		$get_site_language_explode = explode( '-', $get_site_language );
		$default_lang              = $get_site_language_explode[0];
	} else {
		$default_lang = $get_site_language;
	}
	return $default_lang;
}
/**
 * Get default language.
 *
 * @return string $default_lang It will return default language for site.
 *
 * @since 1.2
 */
function idm_asw_get_default_language() {
	global $sitepress;
	if ( ! empty( $sitepress ) ) {
		$default_lang = $sitepress->get_current_language();
	} else {
		$default_lang = idm_asw_current_site_language();
	}
	return $default_lang;
}
/**
 * Get id based on language.
 *
 * @param int    $get_id      Get id.
 *
 * @param string $default_lan Get default language.
 *
 * @return int $get_method_id_bol It will return id based on language.
 *
 * @since 1.2
 */
function asw_get_id_based_on_lan( $get_id, $default_lan ) {
	global $sitepress;
	if ( ! empty( $sitepress ) ) {
		$get_method_id_bol = apply_filters( 'wpml_object_id', $get_id, 'product', true, $default_lan );
	} else {
		$get_method_id_bol = $get_id;
	}
	return $get_method_id_bol;
}
/**
 * Get product id based on language.
 *
 * @param int    $get_id      Get id.
 *
 * @return int $get_id It will return id based on language.
 *
 * @since 1.2
 */
function asw_get_prd_id_based_on_lan( $get_id ) {
	global $sitepress;
	if ( ! isset( $sitepress ) ) {
		return;
	}
	$get_default_language = idm_asw_get_default_language();
	$trid                 = $sitepress->get_element_trid( $get_id, 'post_product' );
	$translations         = $sitepress->get_element_translations( $trid, 'product' );
	foreach ( $translations as $lang => $translation ) {
		if ( $lang === $get_default_language ) {
			$get_id = $translation->element_id;
		}
	}
	return $get_id;
}