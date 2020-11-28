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
/**
 * ASW_Match_Shipping Class
 */
if ( class_exists( 'ASW_Match_Shipping' ) ) {
	return;
}
/**
 * ASW_Match_Shipping class.
 */
class ASW_Match_Shipping {
	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$type_of_field         = json_decode( asw_array_type_of_fields(), true );
		$type_of_field_key_arr = array();
		$type_of_field_arr     = array();
		if ( ! empty( $type_of_field ) ) {
			foreach ( array_keys( $type_of_field ) as $key ) {
				$type_of_field_key_arr[] = $key;
			}
		}
		if ( ! empty( $type_of_field_key_arr ) ) {
			foreach ( $type_of_field_key_arr as $type_of_field_val ) {
				$type_of_field_arr[] = $type_of_field_val;
			}
		}
		if ( ! empty( $type_of_field_arr ) ) {
			foreach ( $type_of_field_arr as $value ) {
				add_filter(
					'idm_asw_match_contains_' . $value,
					array(
						$this,
						'idm_asw_match_contains_fn_' . $value,
					),
					10,
					6
				);
			}
		}
	}
	/**
	 * Check rule with products.
	 *
	 * @param boolean $match        check rule or not.
	 *
	 * @param string  $operator     check operator.
	 *
	 * @param string  $value        Check rules value.
	 *
	 * @param mixed   $package      Getting cart package.
	 *
	 * @param string  $advance_rule check advance rule.
	 *
	 * @param string  $base_price   Getting shipping base price.
	 *
	 * @return array match result.
	 *
	 * @since 1.0
	 */
	public function idm_asw_match_contains_fn_cc_products( $match, $operator, $value, $package, $advance_rule = '', $base_price ) {
		$check_array = array();
		$chk_key     = 'cc_product';
		foreach ( $package['contents'] as $product ) {
			if ( ! empty( $product ) ) {
				$check_array[] = $product['product_id'];
			}
		}
		$check_array     = array_unique( $check_array );
		$check_array     = $this->asw_set_array_value_integer( 'int', $check_array );
		$check_result    = $this->asw_check_rule( $value, $check_array, $operator, $chk_key, 'array' );
		$match_ms_result = $this->idm_check_multiple_seletion_result( $check_result, $chk_key, 'any' );
		$match           = $this->idm_asw_check_match_rule( $match_ms_result, $chk_key, 'all' );
		return array(
			'match' => $match,
		);
	}
	/**
	 * Set type for single var.
	 *
	 * @param string $type     Check variable type.
	 *
	 * @param string $type_var Check variable.
	 *
	 * @return string $type_var
	 *
	 * @since 1.0.0
	 */
	public function asw_set_array_value_integer( $type, $type_var ) {
		if ( ! empty( $type_var ) ) {
			if ( 'string' === $type ) {
				$type_var = array_map( 'strval', $type_var );
			} elseif ( 'int' === $type ) {
				$type_var = array_map( 'intval', $type_var );
			}
		}
		return $type_var;
	}
	/**
	 * Check rule for every condition.
	 *
	 * @param array  $value       Condition.
	 *
	 * @param array  $check_array Cart array.
	 *
	 * @param string $operator    operator function.
	 *
	 * @param string $chk_key     Check rules main key.
	 *
	 * @param string $type checj type.
	 *
	 * @return array $check_result
	 */
	public function asw_check_rule( $value, $check_array, $operator, $chk_key, $type ) {
		
		if ( 'array' === $type ) {
			if ( ! empty( $value ) ) {
				foreach ( $value as $key => $val_id ) {
					$val_id = $this->asw_set_value_integer( 'int', $val_id );
					if ( 'equal_to' === $operator ) {
						if ( in_array( $val_id, $check_array, true ) ) {
							$check_result[ $operator ][ $chk_key ] [ $key ] = 'yes';
						} else {
							$check_result[ $operator ][ $chk_key ] [ $key ] = 'no';
						}
					}
					if ( 'not_equal_to' === $operator ) {
						if ( in_array( $val_id, $check_array, true ) ) {
							$check_result[ $operator ][ $chk_key ] [ $key ] = 'no';
						} else {
							$check_result[ $operator ][ $chk_key ] [ $key ] = 'yes';
						}
					}
				}
			} else {
				if ( 'equal_to' === $operator ) {
					$check_result[ $operator ][ $chk_key ] [ ] = 'yes';
				} else {
					$check_result[ $operator ][ $chk_key ] [ ] = 'no';
				}
			}
		} elseif ( 'single' === $type ) {
			if ( ! empty( $value ) ) {
				if ( 'equal_to' === $operator ) {
					if ( in_array( $check_array, $value, true ) ) {
						$check_result[ $operator ][ $chk_key ] [] = 'yes';
					} else {
						$check_result[ $operator ][ $chk_key ] [] = 'no';
					}
				}
				if ( 'not_equal_to' === $operator ) {
					if ( in_array( $check_array, $value, true ) ) {
						$check_result[ $operator ][ $chk_key ] [] = 'no';
					} else {
						$check_result[ $operator ][ $chk_key ] [] = 'yes';
					}
				}
			} else {
				if ( 'equal_to' === $operator ) {
					$check_result[ $operator ][ $chk_key ] [ ] = 'yes';
				} else {
					$check_result[ $operator ][ $chk_key ] [ ] = 'no';
				}
			}
		} else {
			if ( ! empty( $value ) ) {
				if ( 'equal_to' === $operator ) {
					if ( $value === $check_array ) {
						$check_result[ $operator ][ $chk_key ] [] = 'yes';
					} else {
						$check_result[ $operator ][ $chk_key ] [] = 'no';
					}
				}
				if ( 'not_equal_to' === $operator ) {
					if ( $value !== $check_array ) {
						$check_result[ $operator ][ $chk_key ] [] = 'yes';
					} else {
						$check_result[ $operator ][ $chk_key ] [] = 'no';
					}
				}
				if ( 'less_then' === $operator ) {
					if ( $check_array > $value ) {
						$check_result[ $operator ][ $chk_key ] [] = 'yes';
					} else {
						$check_result[ $operator ][ $chk_key ] [] = 'no';
					}
				}
				if ( 'less_equal_to' === $operator ) {
					if ( $check_array >= $value ) {
						$check_result[ $operator ][ $chk_key ] [] = 'yes';
					} else {
						$check_result[ $operator ][ $chk_key ] [] = 'no';
					}
				}
				if ( 'greater_then' === $operator ) {
					if ( $check_array < $value ) {
						$check_result[ $operator ][ $chk_key ] [] = 'yes';
					} else {
						$check_result[ $operator ][ $chk_key ] [] = 'no';
					}
				}
				if ( 'greater_equal_to' === $operator ) {
					if ( $check_array <= $value ) {
						$check_result[ $operator ][ $chk_key ] [] = 'yes';
					} else {
						$check_result[ $operator ][ $chk_key ] [] = 'no';
					}
				}
			} else {
				$check_result[ $operator ][ $chk_key ] [] = 'no';
			}
		}
		return $check_result;
	}
	/**
	 * Set type for single var.
	 *
	 * @param string $type     Check variable type.
	 *
	 * @param string $type_var Check variable.
	 *
	 * @return string $type_var
	 *
	 * @since 1.0.0
	 */
	public function asw_set_value_integer( $type, $type_var ) {
		if ( ! empty( $type_var ) ) {
			if ( 'string' === $type ) {
				$type_var = strval( $type_var );
			} elseif ( 'int' === $type ) {
				$type_var = intval( $type_var );
			} elseif ( 'double' === $type ) {
				$type_var = doubleval( $type_var );
			}
		}
		return $type_var;
	}
	/**
	 * Check multiple selection result.
	 *
	 * @param string $check_result        check all results condition.
	 *
	 * @param string $check_shipping_rule check all shipping rules condition.
	 *
	 * @param string $rule_type           check rule type is AND or OR.
	 *
	 * @return array $new_check_result match result.
	 *
	 * @since 1.0
	 */
	public function idm_check_multiple_seletion_result( $check_result, $check_shipping_rule, $rule_type ) {
		$new_check_result = array();
		$flag             = array();
		if ( ! empty( $check_result ) ) {
			foreach ( $check_result as $key => $check_value_data ) {
				foreach ( $check_value_data as $check_value ) {
					if ( 'equal_to' === $key ) {
						if ( in_array( 'yes', $check_value, true ) ) {
							$flag[ $key ] = true;
						} else {
							$flag[ $key ] = false;
						}
					} else {
						if ( in_array( 'no', $check_value, true ) ) {
							$flag[ $key ] = false;
						} else {
							$flag[ $key ] = true;
						}
					}
				}
			}
		}
		if ( 'any' === $rule_type ) {
			if ( in_array( true, $flag, true ) ) {
				$new_check_result[][ $check_shipping_rule ] = 'yes';
			} else {
				$new_check_result[][ $check_shipping_rule ] = 'no';
			}
		} else {
			if ( in_array( false, $flag, true ) ) {
				$new_check_result[][ $check_shipping_rule ] = 'no';
			} else {
				$new_check_result[][ $check_shipping_rule ] = 'yes';
			}
		}
		return $new_check_result;
	}
	/**
	 * Check multiple selection result.
	 *
	 * @param string $check_result        check all results condition.
	 *
	 * @param string $check_shipping_rule check all shipping rules condition.
	 *
	 * @param string $rule_type           check rule type is AND or OR.
	 *
	 * @return string $match match result.
	 *
	 * @since 1.0
	 */
	public function idm_asw_check_match_rule( $check_result, $check_shipping_rule, $rule_type ) {
		$match = 'no';
		$flag  = array();
		if ( ! empty( $check_result ) ) {
			foreach ( $check_result as $key => $check_value ) {
				if ( 'yes' === $check_value[ $check_shipping_rule ] ) {
					$flag[ $key ] = true;
				} else {
					$flag[ $key ] = false;
				}
			}
			if ( 'any' === $rule_type ) {
				if ( in_array( true, $flag, true ) ) {
					$match = true;
				} else {
					$match = false;
				}
			} else {
				if ( in_array( false, $flag, true ) ) {
					$match = false;
				} else {
					$match = true;
				}
			}
		}
		return $match;
	}
	/**
	 * Check rule with country.
	 *
	 * @param boolean $match        check rule or not.
	 *
	 * @param string  $operator     check operator.
	 *
	 * @param string  $value        Check rules value.
	 *
	 * @param mixed   $package      Getting cart package.
	 *
	 * @param string  $advance_rule check advance rule.
	 *
	 * @param string  $base_price   Getting shipping base price.
	 *
	 * @return array match result.
	 *
	 * @since 1.0
	 */
	public function idm_asw_match_contains_fn_cc_country( $match, $operator, $value, $package, $advance_rule = '', $base_price ) {
		if ( ! isset( WC()->customer ) ) {
			return array(
				'match' => $match,
			);
		}
		$chk_key         = 'cc_country';
		$check_array     = WC()->customer->get_shipping_country();
		$check_result    = $this->asw_check_rule( $value, $check_array, $operator, $chk_key, 'single' );
		$match_ms_result = $this->idm_check_multiple_seletion_result( $check_result, $chk_key, 'any' );
		$match           = $this->idm_asw_check_match_rule( $match_ms_result, $chk_key, 'all' );
		return array(
			'match' => $match,
		);
	}
	/**
	 * Check rule with subtotal after discounting.
	 *
	 * @param boolean $match        check rule or not.
	 *
	 * @param string  $operator     check operator.
	 *
	 * @param string  $value        Check rules value.
	 *
	 * @param mixed   $package      Getting cart package.
	 *
	 * @param string  $advance_rule check advance rule.
	 *
	 * @param string  $base_price   Getting shipping base price.
	 *
	 * @return array match result.
	 *
	 * @since 1.0
	 */
	public function idm_asw_match_contains_fn_cc_subtotal_af_disc( $match, $operator, $value, $package, $advance_rule = '', $base_price ) {
		if ( ! isset( WC()->cart ) ) {
			return array(
				'match' => $match,
			);
		}
		$chk_key             = 'cc_subtotal_af_disc';
		$check_array         = WC()->cart->get_subtotal();
		$cart_discount_total = WC()->cart->get_discount_total();
		if ( ! empty( $cart_discount_total ) ) {
			$check_array = $check_array - $cart_discount_total;
		}
		$value           = $this->asw_set_value_integer( 'double', $value );
		$check_array     = $this->asw_set_value_integer( 'double', $check_array );
		$check_result    = $this->asw_check_rule( $check_array, $value, $operator, $chk_key, 'txtrule' );
		$match_ms_result = $this->idm_check_multiple_seletion_result( $check_result, $chk_key, 'any' );
		$match           = $this->idm_asw_check_match_rule( $match_ms_result, $chk_key, 'all' );
		return array(
			'match' => $match,
		);
	}
	/**
	 * Check rule with advance total cart rules.
	 *
	 * @param boolean $match         check rule or not.
	 *
	 * @param string  $operator      check operator.
	 *
	 * @param string  $value         Check rules value.
	 *
	 * @param mixed   $package       Getting cart package.
	 *
	 * @param string  $advanced_rule check advance rule.
	 *
	 * @param string  $base_price    Getting shipping base price.
	 *
	 * @return array match result.
	 *
	 * @since 1.0
	 */
	public function idm_asw_match_contains_fn_cc_tc_spec( $match, $operator, $value, $package, $advanced_rule, $base_price ) {
		$check_result                 = array();
		$chk_key                      = 'cc_tc_spec';
		$check_result[0] [ $chk_key ][] = 'yes';
		/*Start - Check advanced rule*/
		$advance_price = 0;
		if ( array_key_exists( 'price', $advanced_rule ) ) {
			$advance_price = $advanced_rule['price'];
		}
		$apsub = 1;
		if ( array_key_exists( 'apsub', $advanced_rule ) ) {
			$apsub = $advanced_rule['apsub'];
		}
		if ( array_key_exists( 'apu', $advanced_rule ) ) {
			$count_total_per_unit = 0;
			$total_per_unit       = 0;
			if ( array_key_exists( 'ut', $advanced_rule ) ) {
				$total_per_unit = $this->idm_asw_get_total_per_unit(
					$chk_key,
					$total_per_unit,
					$apsub,
					$advanced_rule,
					$item       = ''
				);
				$count_total_per_unit = $total_per_unit;
			}
			$advance_price_total = $count_total_per_unit;
		} else {
			$count_total_per_unit_with_min_max = 0;
			$total_per_unit_with_min_max       = 0;
			if ( array_key_exists( 'ut', $advanced_rule ) ) {
				$total_per_unit_with_min_max = $this->idm_asw_get_min_max_price(
					$total_per_unit_with_min_max,
					$advanced_rule,
					$item                    = ''
				);
				$count_total_per_unit_with_min_max += $total_per_unit_with_min_max;
			}
			if ( array_key_exists( 'minval', $advanced_rule ) || array_key_exists( 'maxval', $advanced_rule ) ) {
				$advance_price_total = $this->idm_check_min_max_and_add_price(
					$count_total_per_unit_with_min_max,
					$advanced_rule,
					$advance_price
				);
			}
		}
		/*End - Check advanced rule*/
		$match_ms_result = $this->idm_check_multiple_seletion_result( $check_result, $chk_key, 'any' );
		$match           = $this->idm_asw_check_match_rule( $match_ms_result, $chk_key, 'all', 'advance' );
		return array(
			'match' => $match,
			'price' => $advance_price_total,
		);
	}
	/**
	 * Check rule with min and max qty and add price.
	 *
	 * @param int|float $count_total_per_unit_with_min_max Count.
	 *
	 * @param string    $advanced_rule                     Check advance rule.
	 *
	 * @param string    $advance_price                     Check advance price.
	 *
	 * @return int|float $total_per_unit.
	 *
	 * @since 1.0
	 */
	public function idm_check_min_max_and_add_price( $count_total_per_unit_with_min_max, $advanced_rule, $advance_price ) {
		$min = '';
		$max = '';
		if ( array_key_exists( 'minval', $advanced_rule ) ) {
			$min = $advanced_rule['minval'];
		}
		if ( array_key_exists( 'maxval', $advanced_rule ) ) {
			$max = $advanced_rule['maxval'];
		}
		if ( ( $min <= $count_total_per_unit_with_min_max ) && ( $count_total_per_unit_with_min_max <= $max ) ) {
			$advance_price_total = $advance_price;
		} else {
			$advance_price_total = 0;
		}
		return $advance_price_total;
	}
	/**
	 * Check rule with apply per unit.
	 *
	 * @param string    $chk_key        Check key.
	 *
	 * @param int|float $total_per_unit Count.
	 *
	 * @param string    $apsub          Count.
	 *
	 * @param string    $advanced_rule  check advance rule.
	 *
	 * @param string    $item           Get cart Item.
	 *
	 * @return int|float $total_per_unit.
	 *
	 * @since 1.0
	 */
	public function idm_asw_get_total_per_unit( $chk_key, $total_per_unit, $apsub, $advanced_rule, $item ) {
		$cart_subtotal     = WC()->cart->get_subtotal();
		if ( ! empty( $item ) ) {
			$get_product_data = $item['data'];
			if ( 'st_without_tax_disc' === $advanced_rule['ut'] ) {
				$prd_sub_ec_st_wo_tax_disc = floatval( floatval( $cart_subtotal ) * floatval( $advanced_rule['price'] ) ) / floatval( $apsub );
				$total_per_unit            = $prd_sub_ec_st_wo_tax_disc;
			} elseif ( 'weight' === $advanced_rule['ut'] ) {
				if ( $get_product_data->has_weight() ) {
					$prd_get_weight    = intval( $item['quantity'] ) * floatval( $get_product_data->get_weight() );
					$prd_sub_ec_weight = floatval( floatval( $prd_get_weight ) * floatval( $advanced_rule['price'] ) ) / floatval( $apsub );
					$total_per_unit    = $prd_sub_ec_weight;
				}
			}
		} else {
			$total_cart_qty        = 0;
			$total_cart_line_total = 0;
			$total_cart_line_tax   = 0;
			$total_cart_prd_weight = 0;
			foreach ( WC()->cart->get_cart() as $item ) {
				$total_cart_qty        += intval( $item['quantity'] );
				$total_cart_line_total += $item['line_total'];
				$total_cart_line_tax   += $item['line_tax'];
				$get_product_data       = $item['data'];
				if ( $get_product_data->has_weight() ) {
					$total_cart_prd_weight += ( intval( $item['quantity'] ) * floatval( $get_product_data->get_weight() ) );
				}
			}
			if ( 'st_without_tax_disc' === $advanced_rule['ut'] ) {
				$prd_sub_ec_st_wo_tax_disc = floatval( floatval( $cart_subtotal ) * floatval( $advanced_rule['price'] ) ) / floatval( $apsub );
				$total_per_unit            = $prd_sub_ec_st_wo_tax_disc;
			} elseif ( 'weight' === $advanced_rule['ut'] ) {
				$prd_get_weight    = $total_cart_prd_weight;
				$prd_sub_ec_weight = floatval( floatval( $prd_get_weight ) * floatval( $advanced_rule['price'] ) ) / floatval( $apsub );
				$total_per_unit    = $prd_sub_ec_weight;
			}
		}
		return $total_per_unit;
	}
	/**
	 * Check rule with min and max price.
	 *
	 * @param int|float $total_per_unit_with_min_max Count.
	 *
	 * @param string    $advanced_rule               check advance rule.
	 *
	 * @param string    $item                        Get cart Item.
	 *
	 * @return int|float $total_per_unit.
	 *
	 * @since 1.0
	 */
	public function idm_asw_get_min_max_price( $total_per_unit_with_min_max, $advanced_rule, $item ) {
		$cart_subtotal     = WC()->cart->get_subtotal();
		if ( ! empty( $item ) ) {
			if ( 'st_without_tax_disc' === $advanced_rule['ut'] ) {
				$total_per_unit_with_min_max = $cart_subtotal;
			} elseif ( 'weight' === $advanced_rule['ut'] ) {
				if ( $item->has_weight() ) {
					$total_per_unit_with_min_max = intval( $item['quantity'] ) * floatval( $item->get_weight() );
				}
			}
		} else {
			$total_cart_qty        = 0;
			$total_cart_prd_weight = 0;
			foreach ( WC()->cart->get_cart() as $item ) {
				$total_cart_qty        += intval( $item['quantity'] );
				$get_product_data       = $item['data'];
				if ( $get_product_data->has_weight() ) {
					$total_cart_prd_weight += ( intval( $item['quantity'] ) * floatval( $get_product_data->get_weight() ) );
				}
			}
			if ( 'st_without_tax_disc' === $advanced_rule['ut'] ) {
				$total_per_unit_with_min_max = $cart_subtotal;
			}  elseif ( 'weight' === $advanced_rule['ut'] ) {
				$total_per_unit_with_min_max = intval( $total_cart_qty ) * floatval( $total_cart_prd_weight );
			}
		}
		
		return $total_per_unit_with_min_max;
	}
}
