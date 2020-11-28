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
 * ASW_Shipping_Method_Public class.
 */
if ( ! class_exists( 'ASW_Shipping_Method_Public' ) ) {
	/**
	 * ASW_Shipping_Method_Public class.
	 */
	class ASW_Shipping_Method_Public {
		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->init();
		}
		/**
		 * Call actions and filters.
		 *
		 * @since 1.0.0
		 */
		public function init() {
			add_action( 'wp_enqueue_scripts', array( $this, 'idm_asw_enqueue_scripts' ), 10, 2 );
			add_filter( 'woocommerce_shipping_methods', array( $this, 'asw_add_shipping_method' ) );
			add_action( 'woocommerce_shipping_init', array( $this, 'asw_start_shipping_section' ) );
			add_action( 'woocommerce_after_shipping_rate', array( $this, 'asw_display_option_description' ), 10, 2 );
		}
		/**
		 * Enqueue front side css.
		 *
		 * @since 1.0.0
		 */
		public function idm_asw_enqueue_scripts() {
			wp_enqueue_style( 'idm-aws-public-css', plugin_dir_url( __DIR__ ) . 'assets/css/asw-public.css', array(), 'all' );
		}
		/**
		 * Register plugins shipping method.
		 *
		 * @param array $methods Setting plugins shipping method.
		 *
		 * @return array  $methods Setting plugins shipping method.
		 *
		 * @since 1.0.0
		 */
		public function asw_add_shipping_method( $methods ) {
			$methods[] = 'ASW_Shipping_Method';
			return $methods;
		}
		/**
		 * Front side shipping section.
		 *
		 * @since 1.0.0
		 */
		public function asw_start_shipping_section() {
			require_once plugin_dir_path( __DIR__ ) . 'public/class-asw-shipping-method.php';
		}
		/**
		 * Display description in cart.
		 *
		 * @param mixed $method get shipping method.
		 *
		 * @param int   $index  Getting index for method.
		 *
		 * @since  1.0
		 */
		public function asw_display_option_description( $method, $index ) {
			$meta_data = $method->get_meta_data();
			if ( isset( $meta_data['tooltip_description'] ) && ! empty( $meta_data['tooltip_description'] ) ) {
				echo '<div class="tooltip">
    					<i class="fa fa-question-circle fa-lg"></i>
                        <div class="tooltiptext">';
				if ( 'idm_combine_shipping' === $method->id ) {
					if ( false !== strpos( $meta_data['tooltip_description'], ',' ) ) {
						$explode_data = explode( ',', $meta_data['tooltip_description'] );
					}
					if ( ! empty( $explode_data ) ) {
						foreach ( $explode_data as $key => $explode_value ) {
							$key ++;
							if ( ! empty( $explode_value ) ) {
								echo '<span>' . wp_kses_post( $key ) . ' - ' . wp_kses_post( stripslashes( $explode_value ) ) . '</span>';
							}
						}
					}
				} else {
					echo '<span>' . wp_kses_post( stripslashes( $meta_data['tooltip_description'] ) ) . '</span>';
				}
				echo '</div>
                       </div>';
			}
		}
	}
}
$asw_shipping_method_public = new ASW_Shipping_Method_Public();

