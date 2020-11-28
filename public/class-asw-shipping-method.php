<?php
/**
 * The front-specific functionality of the plugin.
 *
 * @package    Advanced_Easy_Shipping_For_WooCommerce
 * @subpackage Advanced_Easy_Shipping_For_WooCommerce/public
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( class_exists( 'ASW_Shipping_Method' ) ) {
	return;
}
/**
 * AFRSM_Init_Shipping_Methods class.
 */
class ASW_Shipping_Method extends WC_Shipping_Method {
	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$this->id                 = 'advanced_easy_shipping';
		$this->title              = esc_html__( 'Advanced Easy Shipping', 'woocommerce-advanced-shipping' );
		$this->method_title       = esc_html__( 'Advanced Easy Shipping', 'woocommerce-advanced-shipping' );
		$this->method_description = esc_html__( 'Configure WooCommerce Advanced Easy Shipping', 'woocommerce-advanced-shipping' );
		$this->init();
		do_action( 'woocommerce_advanced_shipping_method_init' );
	}
	/**
	 * Init shipping at front side.
	 *
	 * @since 1.0
	 */
	public function init() {
		require_once plugin_dir_path( __DIR__ ) . 'public/class-asw-match-shipping.php';
		$this->match = new ASW_Match_Shipping();
	}
	/**
	 * Calculate shipping method.
	 *
	 * @param array $package List containing all products for this method.
	 *
	 * @since 1.0
	 */
	public function calculate_shipping( $package = array() ) {
		$this->matched_methods = $this->idm_asw_check_shipping_methods( $package );
		if ( false === $this->matched_methods || ! is_array( $this->matched_methods ) || 'no' === $this->enabled ) {
			return;
		}
		$idm_asw_general_option  = get_option( 'idm_asw_general_option' );
		$shipping_selection_cost = 'user_selection_cost';
		if ( ! empty( $idm_asw_general_option ) ) {
			$shipping_selection_cost = asw_check_array_key_exists( 'shipping_selection_cost', $idm_asw_general_option );
			if ( $shipping_selection_cost ) {
				$shipping_selection_cost = $shipping_selection_cost;
			} else {
				$shipping_selection_cost = 'user_selection_cost';
			}
		}
		/*Start - Add Extra Cost*/
		$matched_method_array = array();
		foreach ( $this->matched_methods as $method_id => $method_cost ) {
			$asw_easy_shipping_data     = get_post_meta( $method_id, 'asw_easy_shipping_data', true );
			$asw_easy_shipping_data_dcd = json_decode( $asw_easy_shipping_data, true );
			$asw_gss                    = asw_check_array_key_exists( 'asw_gss', $asw_easy_shipping_data_dcd );
			$asw_cost_per               = '';
			if ( ! empty( $asw_gss ) ) {
				$asw_shipping_cost = asw_check_array_key_exists( 'asw_shipping_cost', $asw_easy_shipping_data_dcd['asw_gss'] );
				$asw_cost_per      = asw_check_array_key_exists( 'asw_cost_per', $asw_easy_shipping_data_dcd['asw_gss'] );
			} else {
				$asw_shipping_cost = 0;
			}
			if ( 'per-order' === $asw_cost_per ) {
				$asw_shipping_cost = $asw_shipping_cost;
			}
			if ( empty( $method_cost ) ) {
				$asw_shipping_cost = $asw_shipping_cost;
			} else {
				$asw_shipping_cost += $method_cost;
			}
			$matched_method_array[ $method_id ] = $asw_shipping_cost;
		}
		/*End - Add Extra Cost*/
		$matched_methods_arr = array();
		if ( 'user_selection_cost' === $shipping_selection_cost ) {
			foreach ( $matched_method_array as $method_id => $method_cost ) {
				$matched_methods_arr[ $method_id ] = $method_cost;
			}
		} elseif ( 'maximum_cost' === $shipping_selection_cost ) {
			$maximum_cost_shipping_arr = array();
			foreach ( $matched_method_array as $method_id => $method_cost ) {
				$maximum_cost_shipping_arr[ $method_id ] = $method_cost;
			}
			if ( ! empty( $maximum_cost_shipping_arr ) ) {
				$max_cost                              = max( $maximum_cost_shipping_arr );
				$max_value_key                         = array_search( $max_cost, $maximum_cost_shipping_arr, true );
				$matched_methods_arr[ $max_value_key ] = $max_cost;
			}
		} elseif ( 'minimum_cost' === $shipping_selection_cost ) {
			$minimum_cost_shipping_arr = array();
			foreach ( $matched_method_array as $method_id => $method_cost ) {
				$minimum_cost_shipping_arr[ $method_id ] = $method_cost;
			}
			if ( ! empty( $minimum_cost_shipping_arr ) ) {
				$min_cost                              = min( $minimum_cost_shipping_arr );
				$min_value_key                         = array_search( $min_cost, $minimum_cost_shipping_arr, true );
				$matched_methods_arr[ $min_value_key ] = $min_cost;
			}
		}
		if ( ! empty( $matched_methods_arr ) ) {
			foreach ( $matched_methods_arr as $method_id => $method_cost ) {
				$shipping_title              = get_the_title( $method_id );
				$asw_easy_shipping_data      = get_post_meta( $method_id, 'asw_easy_shipping_data', true );
				$asw_easy_shipping_data_dcd  = json_decode( $asw_easy_shipping_data, true );
				$asw_gss                     = asw_check_array_key_exists( 'asw_gss', $asw_easy_shipping_data_dcd );
				$asw_shipping_tooltip        = '';
				$asw_shipping_handling_price = '';
				if ( ! empty( $asw_gss ) ) {
					$asw_shipping_tooltip        = asw_check_array_key_exists( 'asw_shipping_tooltip', $asw_easy_shipping_data_dcd['asw_gss'] );
					$asw_shipping_handling_price = asw_check_array_key_exists( 'asw_shipping_handling_price', $asw_easy_shipping_data_dcd['asw_gss'] );
				}
				$label                = $shipping_title;
				$this->taxable        = $asw_shipping_handling_price;
				$this->shipping_costs = $method_cost;
				$rate                 = apply_filters(
					'idm_shipping_rate',
					array(
						'id'        => $method_id,
						'label'     => ( null === $label ) ? esc_html__( 'Shipping', 'woocommerce-advanced-shipping' ) : $label,
						'cost'      => $this->shipping_costs,
						'taxes'     => ( 'yes' === $this->taxable ) ? '' : false,
						'meta_data' => ( ! empty( $asw_shipping_tooltip ) ) ?
							array(
								'tooltip_description' =>
									apply_filters(
										'idm_asw_shipping_tooltip',
										$asw_shipping_tooltip
									),
							) : array(),
						'package'   => $package,
					),
					$package,
					$this
				);
				$this->add_rate( $rate );
			}
		}
	}
	/**
	 * Check shipping methods.
	 *
	 * @param array $package List containing all products for this method.
	 *
	 * @return array $matched_methods return all matched method.
	 *
	 * @since 1.0
	 */
	public function idm_asw_check_shipping_methods( $package ) {
		global $sitepress;
		$default_lan     = idm_asw_get_default_language();
		$matched_methods = array();
		$args = array(
			'posts_per_page'   => '-1',
			'post_type'        => ASW_POST_TYPE,
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
			'post_status'      => 'publish',
			'suppress_filters' => false,
		);
		if ( $sitepress ) {
			$args['meta_query'] = array(
				array(
					'key'     => 'asw_esd_lang_key',
					'value'   => $default_lan,
					'compare' => '=',
				),
			);
		}
		$methods = get_posts( $args );
		foreach ( $methods as $method ) {
			$get_method_id_bol          = asw_get_id_based_on_lan( $method->ID, $default_lan );
			$asw_easy_shipping_data     = get_post_meta( $get_method_id_bol, 'asw_easy_shipping_data', true );
			$asw_easy_shipping_data_dcd = json_decode( $asw_easy_shipping_data, true );
			$rule_status_array          = array();
			if ( ! empty( $asw_easy_shipping_data_dcd ) ) {
				if ( array_key_exists( 'asw_rule_status', $asw_easy_shipping_data_dcd ) ) {
					if ( ! empty( $asw_easy_shipping_data_dcd['asw_rule_status'] ) ) {
						foreach ( $asw_easy_shipping_data_dcd['asw_rule_status'] as $key => $asw_value ) {
							$rule_status_array[ $key ] = $asw_value;
						}
					}
				}
			}
			$new_asw_easy_shipping_arr = array();
			if ( ! empty( $asw_easy_shipping_data_dcd ) ) {
				if ( array_key_exists( 'asw_condition_name', $asw_easy_shipping_data_dcd ) ) {
					$asw_gss = asw_check_array_key_exists( 'asw_gss', $asw_easy_shipping_data_dcd );
					if ( ! empty( $asw_gss ) ) {
						$new_asw_easy_shipping_arr['base_price'] = asw_check_array_key_exists( 'asw_shipping_cost', $asw_easy_shipping_data_dcd['asw_gss'] );
					}
					if ( ! empty( $asw_easy_shipping_data_dcd['asw_condition_name'] ) ) {
						foreach ( $asw_easy_shipping_data_dcd['asw_condition_name'] as $key => $asw_value ) {
							$check_rule_key = 'asw_' . $key . '_rule_status';
							if ( array_key_exists( $check_rule_key, $rule_status_array ) ) {
								foreach ( $asw_value as $value ) {
									$new_asw_easy_shipping_arr['rule'][] = $value;
								}
							}
						}
					} else {
						$matched_methods[ $method->ID ] = '';
					}
				}
			}
			$match = $this->idm_asw_check_conitions( $new_asw_easy_shipping_arr, $package );
			if ( is_array( $match ) ) {
				if ( array_key_exists( 'match', $match ) ) {
					if ( true === $match['match'] ) {
						if ( array_key_exists( 'price', $match ) ) {
							$matched_methods[ $method->ID ] = $match['price'];
						} else {
							$matched_methods[ $method->ID ] = '';
						}
					}
				}
			} else {
				if ( true === $match ) {
					$matched_methods[] = $method->ID;
				}
			}
		}
		return $matched_methods;
	}
	/**
	 * Check shipping conditions.
	 *
	 * @param array $asw_easy_shipping_data_dcd check extra shipping.
	 *
	 * @param array $package                    List containing all products for this method.
	 *
	 * @return boolean
	 *
	 * @since 1.0
	 */
	public function idm_asw_check_conitions( $asw_easy_shipping_data_dcd = array(), $package = array() ) {
		if ( empty( $asw_easy_shipping_data_dcd ) ) {
			return false;
		}
		if ( $asw_easy_shipping_data_dcd ) {
			$match_array = array();
			if ( array_key_exists( 'rule', $asw_easy_shipping_data_dcd ) ) {
				foreach ( $asw_easy_shipping_data_dcd['rule'] as $shipping_conditions ) {
					$operator_rule = '';
					if ( array_key_exists( 'operator', $shipping_conditions ) ) {
						$operator_rule = $shipping_conditions['operator'];
					}
					$advanced_rule = '';
					if ( array_key_exists( 'advanced', $shipping_conditions ) ) {
						$advanced_rule = $shipping_conditions['advanced'];
					}
					$shipping_value = '';
					if ( array_key_exists( 'value', $shipping_conditions ) ) {
						$shipping_value = $shipping_conditions['value'];
					}
					$match         = apply_filters(
						'idm_asw_match_contains_' . $shipping_conditions['condition'],
						false,
						$operator_rule,
						$shipping_value,
						$package,
						$advanced_rule,
						$asw_easy_shipping_data_dcd['base_price']
					);
					$match_array[] = $match;
				}
			} else {
				$match['match'] = true;
				$match_array[]  = $match;
			}
			$check_match_array = array();
			if ( ! empty( $match_array ) ) {
				$total_price = 0;
				foreach ( $match_array as $match_value ) {
					if ( array_key_exists( 'match', $match_value ) ) {
						if ( false === $match_value['match'] ) {
							return false;
						} else {
							if ( true === $match_value['match'] ) {
								$check_match_array['match'] = $match_value['match'];
								if ( array_key_exists( 'price', $match_value ) ) {
									if ( ! empty( $match_value['price'] ) ) {
										$total_price                += $match_value['price'];
										$check_match_array['price'] = $total_price;
									}
								}
							}
						}
					}
				}
			}
			return $check_match_array;
		}
		return false;
	}
}
