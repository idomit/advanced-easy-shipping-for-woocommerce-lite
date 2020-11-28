<?php
/**
 * Admin section.
 *
 * @package    Advanced_Easy_Shipping_For_WooCommerce
 * @subpackage Advanced_Easy_Shipping_For_WooCommerce/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * ASW_Admin class.
 */
if ( ! class_exists( 'ASW_Admin' ) ) {
	/**
	 * ASW_Admin class.
	 */
	class ASW_Admin {
		/**
		 * The name of this plugin.
		 *
		 * @since    1.0.0
		 * @var      string $plugin_name The ID of this plugin.
		 */
		private $plugin_name;
		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @var      string $version The current version of this plugin.
		 */
		private $version;
		/**
		 * Default language.
		 *
		 * @since 1.2
		 * @var      string $default_language The default language of site.
		 */
		private $default_language;
		/**
		 * Define the plugins name and versions and also call admin section.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			$this->plugin_name = 'Advance Easy Shipping';
			$this->version     = ASW_PLUGIN_VERSION;
			$this->default_language = idm_asw_get_default_language();
			$this->idm_asw_init();
		}
		/**
		 * Register actions and filters.
		 *
		 * @since    1.0.0
		 */
		public function idm_asw_init() {
			$prefix = is_network_admin() ? 'network_admin_' : '';
			add_action( 'admin_menu', array( $this, 'idm_asw_menu' ) );
			add_action( 'admin_notices', array( $this, 'idm_admin_review_notice' ) );
			add_action( 'init', array( $this, 'idm_asw_post_type' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'idm_enqueue_scripts' ) );
			add_filter(
				'idm_asw_getting_page',
				array(
					$this,
					'idm_asw_getting_page_fn',
				),
				10
			);
			add_action( 'wp_ajax_aws_get_value_based_on_products', array( $this, 'aws_get_value_based_on_products' ) );
			add_action(
				'idm_asw_admin_action_current_tab',
				array(
					$this,
					'idm_asw_admin_action_tab_fn',
				)
			);
			add_filter(
				'idm_asw_admin_tab_ft',
				array(
					$this,
					'idm_asw_admin_tab',
				),
				10
			);
			add_filter(
				"{$prefix}plugin_action_links_" . plugin_basename( __FILE__ ),
				array(
					$this,
					'asw_plugin_action_links',
				),
				10
			);
		}
		/**
		 * Display admin notice.
		 *
		 * @since 1.2
		 */
		public function idm_admin_review_notice() {
			if ( !in_array( 'advanced-easy-shipping-for-woocommerce/advanced-easy-shipping-for-woocommerce.php', apply_filters( 'active_plugins', get_option('active_plugins')), true ) ) {
				?>
				<div class="notice notice-info is-dismissible waef-review-notice">
					<p><?php _e( 'You are currently using the Community version of the Advanced Easy Shipping For WooCommerce Lite Plugin. Premium users are provided with an additional features and priority support. Upgrade to Premium today to reap all the benefits.' ); ?></p>
					<p><a style="background: #0176b2;padding: 7px;color: #fff;border-radius: 3px;cursor: pointer;text-decoration: none;" href="<?php echo esc_url( 'https://bit.ly/2YCYA2R' ); ?>" target="_blank"><?php echo esc_html__('Buy Now', 'advanced-easy-shipping-for-woocommerce'); ?></a></p>
				</div>
				<?php
				if ( get_option( 'asw_review_notice_dismissed' ) !== false ) {
					return;
				} else {
					if ( isset( $_GET['asw_dismis_review'] ) ) {
						update_option( 'asw_review_notice_dismissed', true );
						return;
					}
				}
				?>
				<div class="notice notice-info is-dismissible waef-review-notice">
					<p><?php esc_html_e( 'It would mean a lot to us if you would quickly give our plugin a 5-star rating. Your Review is very important to us as it helps us to grow more!', 'advanced-easy-shipping-for-woocommerce' ); ?></p>
					<ul>
						<li><a href="<?php echo esc_url( 'https://wordpress.org/support/plugin/advanced-easy-shipping-for-wc-lite/reviews/?rate=5#new-post' ); ?>" class="button" target="_blank"><?php _e( 'Yes you deserve it!', 'advanced-easy-shipping-for-woocommerce' ); ?></span></a></li>
						<li><a href="<?php echo esc_url( add_query_arg( 'asw_dismis_review', true ) ); ?>" class="waef-dismiss"><?php _e( 'Hide this message', 'advanced-easy-shipping-for-woocommerce' ); ?> / <?php _e( 'Already did!', 'advanced-easy-shipping-for-woocommerce' ); ?></a></li>
						<li><a href="mailto:info@idomit.com?Subject=Here%20is%20how%20I%20think%20you%20can%20do%20better" target="_blank"><?php _e( 'Actually, I need a help...', 'advanced-easy-shipping-for-woocommerce' ); ?></a></li>
					</ul>
				</div>
				<?php
			}
		}
		/**
		 * Register Post type.
		 *
		 * @since 1.2
		 */
		public function idm_asw_post_type() {
			register_post_type(
				ASW_POST_TYPE,
				array(
					'labels' => array(
						'name'          => esc_html__( 'Easy Shipping Method', 'advanced-easy-shipping-for-woocommerce' ),
						'singular_name' => esc_html__( 'Easy Shipping Method', 'advanced-easy-shipping-for-woocommerce' ),
					),
				)
			);
		}
		/**
		 * Using tab array.
		 *
		 * @return array $tab_array
		 *
		 * @since 1.0.0
		 */
		public static function idm_asw_admin_action_tab_fn() {
			$current_tab_array = array(
				'general_section'  => esc_html__( 'Shipping Setting', 'advanced-easy-shipping-for-woocommerce' ),
				'shipping_section' => esc_html__( 'Shipping Listing', 'advanced-easy-shipping-for-woocommerce' ),
			);
			return $current_tab_array;
		}
		/**
		 * Getting Tab array.
		 *
		 * @param array $aon_tab_array Checking array tab.
		 *
		 * @return array $tab_array Checking array tab.
		 *
		 * @since 1.0.0
		 */
		public function idm_asw_admin_tab( $aon_tab_array ) {
			$current_tab_array = $this->idm_asw_admin_action_tab_fn();
			if ( ! empty( $aon_tab_array ) ) {
				$tab_array = array_merge( $current_tab_array, $aon_tab_array );
			} else {
				$tab_array = $current_tab_array;
			}
			return $tab_array;
		}
		/**
		 * Add menu in woocommerce main menu.
		 *
		 * @since 1.0.0
		 */
		public function idm_asw_menu() {
			add_submenu_page(
				'woocommerce',
				'Easy Shipping',
				'Easy Shipping',
				'manage_options',
				'asw-main',
				array(
					$this,
					'idm_asw_main',
				)
			);
		}
		/**
		 * Enqueue plugins css and js for admin purpose.
		 *
		 * @param string $hook using this var we can get current page name.
		 *
		 * @since 1.0.0
		 */
		public function idm_enqueue_scripts( $hook ) {
			wp_enqueue_style( 'idm-aws-admin-css', plugin_dir_url( __DIR__ ) . 'assets/css/asw-admin.css', array(), 'all' );
			wp_enqueue_style( 'select2-min-css', plugin_dir_url( __DIR__ ) . 'assets/css/select2.min.css', array(), 'all' );
			wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), 'all' );
			wp_enqueue_script(
				'select2-min-js',
				plugin_dir_url( __DIR__ ) . 'assets/js/select2.full.min.js',
				array(
					'jquery',
					'jquery-ui-datepicker',
				),
				$this->version,
				true
			);
			wp_enqueue_script(
				'idm-aws-admin-js',
				plugin_dir_url( __DIR__ ) . 'assets/js/asw-admin.js',
				array(
					'jquery',
					'jquery-tiptip',
				),
				$this->version,
				true
			);
			wp_localize_script(
				'idm-aws-admin-js',
				'aws_var',
				array(
					'ajaxurl'              => admin_url( 'admin-ajax.php' ),
					'country_obj'          => asw_get_country_list(),
					'cart_option'          => asw_get_based_on_cart_options(),
					'location_option'      => asw_get_based_on_locations_options(),
					'cart_specific_option' => asw_get_based_on_cart_specific_options(),
					'conditional_op_more'  => asw_conditional_operator(),
					'type_of_field'        => asw_array_type_of_fields(),
					'user_option'          => asw_get_based_on_user_specific_options(),
					'product_option'       => asw_get_based_on_product_specific_options(),
					'per_unit_option'      => asw_per_unit_options( 'cc_tc_spec' ),
					'currency_symbol'      => get_woocommerce_currency_symbol(),
					'weight_unit'          => get_option( 'woocommerce_weight_unit' ),
					'dimension_unit'       => get_option( 'woocommerce_dimension_unit' ),
					'placeholder_arr'      => asw_placeholder_for_fields()
					
				)
			);
		}
		/**
		 * Shipping List Page
		 *
		 * @since    1.0.0
		 */
		public function idm_asw_main() {
			require_once ASW_PLUGIN_DIR . '/settings/asw-main.php';
		}
		/**
		 * Getting dynamic url.
		 *
		 * @param string $page_name Getting page name.
		 *
		 * @param string $tab_name  Getting tab name.
		 *
		 * @param string $action    Getting action.
		 *
		 * @param string $post_id   Getting current post id.
		 *
		 * @param string $nonce Checking nonce if available in url.
		 *
		 * @param string $message Checking if any dynamic messages pass in url.
		 *
		 * @return mixed $idm_url return url.
		 *
		 * @since 1.0.0
		 */
		public function idm_dynamic_url( $page_name, $tab_name, $action = '', $post_id = '', $nonce = '', $message = '' ) {
			$url_param = array();
			if ( ! empty( $page_name ) ) {
				$url_param['page'] = $page_name;
			}
			if ( ! empty( $tab_name ) ) {
				$url_param['tab'] = $tab_name;
			}
			if ( ! empty( $action ) ) {
				$url_param['action'] = $action;
			}
			if ( ! empty( $post_id ) ) {
				$url_param['post'] = $post_id;
			}
			if ( ! empty( $nonce ) ) {
				$url_param['_wpnonce'] = $nonce;
			}
			if ( ! empty( $message ) ) {
				$url_param['message'] = $message;
			}
			$idm_url = add_query_arg(
				$url_param,
				admin_url( 'admin.php' )
			);
			return $idm_url;
		}
		/**
		 * Getting Page.
		 *
		 * @param string $current_tab Getting current tab name.
		 *
		 * @since 1.0.0
		 */
		public function idm_asw_getting_page_fn( $current_tab ) {
			if ( 'shipping_section' === $current_tab ) {
				require_once ASW_PLUGIN_DIR . '/includes/class-asw-shipping-method-setting.php';
				$asw_sms = new ASW_Shipping_Method_Setting();
				$asw_sms->asw_sms_output();
			} elseif ( 'general_section' === $current_tab ) {
				require_once ASW_PLUGIN_DIR . '/settings/asw-common-setting.php';
			}
		}
		/**
		 * Get current page.
		 *
		 * @return string $current_page Getting current page name.
		 *
		 * @since 1.0.0
		 */
		public function idm_asw_current_page() {
			$current_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
			return $current_page;
		}
		/**
		 * Get current tab.
		 *
		 * @return string $current_tab Getting current tab name.
		 *
		 * @since 1.0.0
		 */
		public function idm_asw_current_tab() {
			$current_tab = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
			if ( ! isset( $current_tab ) ) {
				$current_tab = 'general_section';
			}
			return $current_tab;
		}
		/**
		 * Validate message for plugins form.
		 *
		 * @param string $message        Custom Validate message for plugins form.
		 *
		 * @param string $tab            Get current tab for current page.
		 *
		 * @param string $validation_msg Display validation error.
		 *
		 * @return bool
		 *
		 * @since 1.0.0
		 */
		public function idm_asw_updated_message( $message, $tab, $validation_msg ) {
			if ( empty( $message ) ) {
				return false;
			}
			if ( 'shipping_section' === $tab ) {
				if ( 'created' === $message ) {
					$updated_message = esc_html__( 'Shipping method successfully created.', 'advanced-easy-shipping-for-woocommerce' );
				} elseif ( 'saved' === $message ) {
					$updated_message = esc_html__( 'Shipping method successfully updated.', 'advanced-easy-shipping-for-woocommerce' );
				} elseif ( 'deleted' === $message ) {
					$updated_message = esc_html__( 'Shipping method deleted.', 'advanced-easy-shipping-for-woocommerce' );
				} elseif ( 'duplicated' === $message ) {
					$updated_message = esc_html__( 'Shipping method duplicated.', 'advanced-easy-shipping-for-woocommerce' );
				} elseif ( 'disabled' === $message ) {
					$updated_message = esc_html__( 'Shipping method disabled.', 'advanced-easy-shipping-for-woocommerce' );
				} elseif ( 'enabled' === $message ) {
					$updated_message = esc_html__( 'Shipping method enabled.', 'advanced-easy-shipping-for-woocommerce' );
				}
				if ( 'failed' === $message ) {
					$failed_messsage = esc_html__( 'There was an error with saving data.', 'advanced-easy-shipping-for-woocommerce' );
				} elseif ( 'nonce_check' === $message ) {
					$failed_messsage = esc_html__( 'There was an error with security check.', 'advanced-easy-shipping-for-woocommerce' );
				}
				if ( 'validated' === $message ) {
					$validated_messsage = esc_html( $validation_msg );
				}
			} else {
				if ( 'saved' === $message ) {
					$updated_message = esc_html__( 'Settings save successfully', 'advanced-easy-shipping-for-woocommerce' );
				}
				if ( 'nonce_check' === $message ) {
					$failed_messsage = esc_html__( 'There was an error with security check.', 'advanced-easy-shipping-for-woocommerce' );
				}
				if ( 'validated' === $message ) {
					$validated_messsage = esc_html( $validation_msg );
				}
			}
			if ( ! empty( $updated_message ) ) {
				echo sprintf( '<div id="message" class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( $updated_message ) );
				return false;
			}
			if ( ! empty( $failed_messsage ) ) {
				echo sprintf( '<div id="message" class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html( $failed_messsage ) );
				return false;
			}
			if ( ! empty( $validated_messsage ) ) {
				echo sprintf( '<div id="message" class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html( $validated_messsage ) );
				return false;
			}
		}
		/**
		 * Get Product list based on search value.
		 *
		 * @param string $search_value Getting search value based on enter in admin forms.
		 *
		 * @return string
		 *
		 * @since 1.0.0
		 */
		public function get_product_list( $search_value ) {
			$product_args = array(
				'post_type'      => 'product',
				'posts_per_page' => - 1,
				'post_status'    => 'publish',
				'orderby'        => 'title',
				'order'          => 'ASC',
			);
			if ( isset( $search_value ) ) {
				$product_args['search_pro_title'] = $search_value;
				add_filter( 'posts_where', array( $this, 'idm_asw_posts_where' ), 10, 2 );
				$get_wp_query = new WP_Query( $product_args );
				remove_filter( 'posts_where', array( $this, 'idm_asw_posts_where' ), 10, 2 );
			} else {
				$get_wp_query = new WP_Query( $product_args );
			}
			if ( $get_wp_query->have_posts() ) {
				$fetch_all_products = $get_wp_query->posts;
			} else {
				$fetch_all_products = '';
			}
			return $fetch_all_products;
		}
		/**
		 * Where condition for post title.
		 *
		 * @param string $where    searching search value.
		 *
		 * @param string $wp_query Find search title using $wp_query.
		 *
		 * @return string $where return search title.
		 *
		 * @since 1.0.0
		 */
		public function idm_asw_posts_where( $where, $wp_query ) {
			global $wpdb;
			$search_term = $wp_query->get( 'search_pro_title' );
			if ( ! empty( $search_term ) ) {
				$search_term_like = $wpdb->esc_like( $search_term );
				$where           .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\'';
			}
			return $where;
		}
		/**
		 * Display value based on products.
		 *
		 * @since 1.0.0
		 */
		public function aws_get_value_based_on_products() {
			$products_array     = array();
			$request_value      = filter_input( INPUT_GET, 'value', FILTER_SANITIZE_STRING );
			$post_value         = isset( $request_value ) ? sanitize_text_field( $request_value ) : '';
			$product_ids        = array();
			$fetch_all_products = $this->get_product_list( $post_value );
			if ( isset( $fetch_all_products ) && ! empty( $fetch_all_products ) ) {
				foreach ( $fetch_all_products as $fetch_all_product ) {
					$_product = wc_get_product( $fetch_all_product->ID );
					if ( ! ( $_product->is_virtual( 'yes' ) ) ) {
						if ( $_product->is_type( 'simple' ) ) {
							$product_ids[] = $fetch_all_product->ID;
						}
					}
				}
			}
			if ( isset( $product_ids ) && ! empty( $product_ids ) ) {
				foreach ( $product_ids as $get_product_id ) {
					$get_product_id   = asw_get_id_based_on_lan( $get_product_id, $this->default_language );
					$products_array[] = array( $get_product_id, get_the_title( $get_product_id ) );
				}
			}
			echo wp_json_encode( $products_array );
			wp_die();
		}
	}
}
$asw_admin = new ASW_Admin();
