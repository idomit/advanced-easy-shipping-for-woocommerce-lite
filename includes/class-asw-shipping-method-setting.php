<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Advanced_Easy_Shipping_For_WooCommerce
 * @subpackage Advanced_Easy_Shipping_For_WooCommerce/includes
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * ASW_Shipping_Method_Setting class.
 */
if ( ! class_exists( 'ASW_Shipping_Method_Setting' ) ) {
	/**
	 * ASW_Shipping_Method_Setting class.
	 */
	class ASW_Shipping_Method_Setting {
		/**
		 * Post type.
		 *
		 * @since 1.0.0
		 * @var $post_type Stroe post type.
		 */
		private static $post_type = null;
		/**
		 * Admin object call.
		 *
		 * @since    1.0.0
		 * @var      string $asw_admin_obj The class of external plugin.
		 */
		private static $asw_admin_obj = null;
		/**
		 * Get current page.
		 *
		 * @since 1.0.0
		 * @var $current_page Stroe current page.
		 */
		private static $current_page = null;
		/**
		 * Get current tab.
		 *
		 * @since 1.0.0
		 * @var $current_tab Stroe current tab.
		 */
		private static $current_tab = null;
		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			self::$asw_admin_obj = new ASW_Admin();
			self::$current_page  = self::$asw_admin_obj->idm_asw_current_page();
			self::$current_tab   = self::$asw_admin_obj->idm_asw_current_tab();
			self::$post_type     = ASW_POST_TYPE;
			add_action( 'idm_cart_products_specific_rule', array( $this, 'idm_asw_based_on_cart' ), 10, 2 );
			add_action( 'idm_location_specific_rule', array( $this, 'idm_asw_based_on_location' ), 10, 2 );
			add_action( 'idm_cart_specific_rule', array( $this, 'idm_asw_based_on_cart_specific' ), 10, 2 );
			add_action( 'idm_user_specific_rule', array( $this, 'idm_asw_based_on_user_specific' ), 10, 2 );
			add_action( 'idm_product_specific_rule', array( $this, 'idm_asw_based_on_product_specific' ), 10, 2 );
			add_action( 'idm_add_new_shipping_btn', array( $this, 'idm_add_new_shipping_btn_fn' ), 10, 2 );
		}
		/**
		 * Display output.
		 *
		 * @since    1.0.0
		 *
		 * @uses     asw_sms_save_method
		 * @uses     asw_sms_add_shipping_method_form
		 * @uses     asw_sms_edit_method_screen
		 * @uses     asw_sms_delete_method
		 * @uses     asw_sms_duplicate_method
		 * @uses     asw_sms_list_methods_screen
		 * @uses     ASW_Admin::idm_asw_updated_message()
		 */
		public static function asw_sms_output() {
			$action          = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );
			$post_id_request = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
			$asw_nonce       = filter_input( INPUT_GET, 'asw_nonce', FILTER_SANITIZE_STRING );
			$get_asw_add     = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING );
			$get_tab         = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
			$message         = filter_input( INPUT_GET, 'message', FILTER_SANITIZE_STRING );
			if ( isset( $action ) && ! empty( $action ) ) {
				if ( 'add' === $action ) {
					self::asw_sms_save_method();
					self::asw_sms_add_shipping_method_form();
				} elseif ( 'edit' === $action ) {
					if ( isset( $asw_nonce ) && ! empty( $asw_nonce ) ) {
						$getnonce = wp_verify_nonce( $asw_nonce, 'edit_' . $post_id_request );
						if ( isset( $getnonce ) && 1 === $getnonce ) {
							self::asw_sms_edit_method_screen( $post_id_request );
						} else {
							wp_safe_redirect(
								add_query_arg(
									array(
										'page' => 'asw-start-page',
										'tab'  => 'advance_shipping_method',
									),
									admin_url( 'admin.php' )
								)
							);
							exit;
						}
					} elseif ( isset( $get_asw_add ) && ! empty( $get_asw_add ) ) {
						if ( ! wp_verify_nonce( $get_asw_add, 'asw_add' ) ) {
							$message = 'nonce_check';
						} else {
							self::asw_sms_edit_method_screen( $post_id_request );
						}
					}
				} elseif ( 'delete' === $action ) {
					self::asw_sms_delete_method( $post_id_request );
				} elseif ( 'duplicate' === $action ) {
					self::asw_sms_duplicate_method( $post_id_request );
				} else {
					self::asw_sms_list_methods_screen();
				}
			} else {
				self::asw_sms_list_methods_screen();
			}
			if ( isset( $message ) && ! empty( $message ) ) {
				self::$asw_admin_obj->idm_asw_updated_message( $message, $get_tab, '' );
			}
		}
		/**
		 * Delete shipping method.
		 *
		 * @param int $id Get shipping method id.
		 *
		 * @uses     Advanced_Easy_Shipping_For_WooCommerce::idm_asw_updated_message()
		 *
		 * @since    1.0.0
		 */
		public function asw_sms_delete_method( $id ) {
			$asw_nonce = filter_input( INPUT_GET, 'asw_nonce', FILTER_SANITIZE_STRING );
			$get_tab   = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
			$getnonce  = wp_verify_nonce( $asw_nonce, 'del_' . $id );
			if ( isset( $getnonce ) && 1 === $getnonce ) {
				wp_delete_post( $id );
				$delet_action_redirect_url = self::$asw_admin_obj->idm_dynamic_url( self::$current_page, self::$current_tab, '', '', '', 'deleted' );
				wp_safe_redirect( $delet_action_redirect_url );
				exit;
			} else {
				self::$asw_admin_obj->idm_asw_updated_message( 'nonce_check', $get_tab, '' );
			}
		}
		/**
		 * Duplicate shipping method.
		 *
		 * @param int $id Get shipping method id.
		 *
		 * @uses     Advanced_Easy_Shipping_For_WooCommerce::idm_asw_updated_message()
		 *
		 * @since    1.0.0
		 */
		public function asw_sms_duplicate_method( $id ) {
			$asw_nonce   = filter_input( INPUT_GET, 'asw_nonce', FILTER_SANITIZE_STRING );
			$get_tab     = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
			$getnonce    = wp_verify_nonce( $asw_nonce, 'duplicate_' . $id );
			$asw_add     = wp_create_nonce( 'asw_add' );
			$post_id     = isset( $id ) ? absint( $id ) : '';
			$new_post_id = '';
			if ( isset( $getnonce ) && 1 === $getnonce ) {
				if ( ! empty( $post_id ) || '' !== $post_id ) {
					$post            = get_post( $post_id );
					$current_user    = wp_get_current_user();
					$new_post_author = $current_user->ID;
					if ( isset( $post ) && null !== $post ) {
						$args           = array(
							'comment_status' => $post->comment_status,
							'ping_status'    => $post->ping_status,
							'post_author'    => $new_post_author,
							'post_content'   => $post->post_content,
							'post_excerpt'   => $post->post_excerpt,
							'post_name'      => $post->post_name,
							'post_parent'    => $post->post_parent,
							'post_password'  => $post->post_password,
							'post_status'    => 'draft',
							'post_title'     => $post->post_title . '-duplicate',
							'post_type'      => self::$post_type,
							'to_ping'        => $post->to_ping,
							'menu_order'     => $post->menu_order,
						);
						$new_post_id    = wp_insert_post( $args );
						$post_meta_data = get_post_meta( $post_id );
						if ( 0 !== count( $post_meta_data ) ) {
							foreach ( $post_meta_data as $meta_key => $meta_data ) {
								if ( '_wp_old_slug' === $meta_key ) {
									continue;
								}
								if ( is_array( $meta_data[0] ) ) {
									$meta_value = maybe_unserialize( $meta_data[0] );
								} else {
									$meta_value = $meta_data[0];
								}
								update_post_meta( $new_post_id, $meta_key, $meta_value );
							}
						}
					}
					$duplicat_action_redirect_url = self::$asw_admin_obj->idm_dynamic_url( self::$current_page, self::$current_tab, 'edit', $new_post_id, esc_attr( $asw_add ), 'duplicated' );
					wp_safe_redirect( $duplicat_action_redirect_url );
					exit();
				} else {
					$action_redirect_url = self::$asw_admin_obj->idm_dynamic_url( self::$current_page, self::$current_tab, '', '', '', 'failed' );
					wp_safe_redirect( $action_redirect_url );
					exit();
				}
			} else {
				self::$asw_admin_obj->idm_asw_updated_message( 'nonce_check', $get_tab, '' );
			}
		}
		/**
		 * Count total shipping method.
		 *
		 * @return int $count_method Count total shipping method ID.
		 *
		 * @since    1.0.0
		 */
		public static function aswsmp_sm_count_method() {
			$shipping_method_args = array(
				'post_type'      => self::$post_type,
				'post_status'    => array( 'publish', 'draft' ),
				'posts_per_page' => - 1,
				'orderby'        => 'ID',
				'order'          => 'DESC',
			);
			$sm_post_query        = new WP_Query( $shipping_method_args );
			$shipping_method_list = $sm_post_query->posts;
			return count( $shipping_method_list );
		}
		/**
		 * Save shipping method when add or edit.
		 *
		 * @param int $method_id Shipping method id.
		 *
		 * @return bool false when nonce is not verified.
		 * @uses     aswsmp_sm_count_method()
		 *
		 * @since    1.0.0
		 *
		 * @uses     Advanced_Easy_Shipping_For_WooCommerce::idm_asw_updated_message()
		 */
		private static function asw_sms_save_method( $method_id = 0 ) {
			global $sitepress;
			$action                        = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );
			$get_tab                       = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
			$asw_save                      = filter_input( INPUT_POST, 'asw_save', FILTER_SANITIZE_STRING );
			$woocommerce_save_method_nonce = filter_input( INPUT_POST, 'woocommerce_save_method_nonce', FILTER_SANITIZE_STRING );
			if ( ( isset( $action ) && ! empty( $action ) ) ) {
				if ( isset( $asw_save ) ) {
					if ( empty( $woocommerce_save_method_nonce ) || ! wp_verify_nonce( sanitize_text_field( $woocommerce_save_method_nonce ), 'woocommerce_save_method' ) ) {
						self::$asw_admin_obj->idm_asw_updated_message( 'nonce_check', $get_tab, '' );
					}
					$asw_shipping_status     = filter_input( INPUT_POST, 'asw_shipping_status', FILTER_SANITIZE_STRING );
					$asw_shipping_title      = filter_input( INPUT_POST, 'asw_shipping_title', FILTER_SANITIZE_STRING );
					$get_asw_gss             = filter_input( INPUT_POST, 'asw_gss', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
					$get_asw_rule_status     = filter_input( INPUT_POST, 'asw_rule_status', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
					$get_asw_condition_name  = filter_input( INPUT_POST, 'asw_condition_name', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
					$get_asw_rule_status_arr = array();
					if ( ! empty( $get_asw_rule_status ) ) {
						foreach ( $get_asw_rule_status as $get_asw_rule_key ) {
							$get_asw_rule_status_arr[] = $get_asw_rule_key;
						}
					}
					$get_asw_condition_name_arr = array();
					if ( ! empty( $get_asw_condition_name ) ) {
						foreach ( $get_asw_condition_name as $get_asw_condition_key ) {
							$get_asw_condition_name_arr[] = $get_asw_condition_key;
						}
					}
					if ( ! empty( $get_asw_rule_status_arr ) && ! empty( $get_asw_condition_name_arr ) ) {
						foreach ( $get_asw_rule_status_arr as $asw_rule_status_value ) {
							foreach ( $get_asw_condition_name_arr as $asw_condition_name ) {
								if ( strpos( $asw_rule_status_value, $asw_condition_name ) !== false ) {
									unset( $get_asw_condition_name_arr['asw_condition_name'][ $asw_rule_status_value ] );
								}
							}
						}
					}
					$shipping_method_count = self::aswsmp_sm_count_method();
					$method_id             = (int) $method_id;
					if ( isset( $asw_shipping_status ) ) {
						$post_status = 'publish';
					} else {
						$post_status = 'draft';
					}
					if ( '' !== $method_id && 0 !== $method_id ) {
						$fee_post  = array(
							'ID'          => $method_id,
							'post_title'  => sanitize_text_field( $asw_shipping_title ),
							'post_status' => $post_status,
							'menu_order'  => $shipping_method_count + 1,
							'post_type'   => self::$post_type,
						);
						$method_id = wp_update_post( $fee_post );
					} else {
						$fee_post  = array(
							'post_title'  => sanitize_text_field( $asw_shipping_title ),
							'post_status' => $post_status,
							'menu_order'  => $shipping_method_count + 1,
							'post_type'   => self::$post_type,
						);
						$method_id = wp_insert_post( $fee_post );
					}
					if ( '' !== $method_id && 0 !== $method_id ) {
						if ( $method_id > 0 ) {
							$easy_shipping_data                       = array();
							$easy_shipping_data['asw_gss']            = $get_asw_gss;
							$easy_shipping_data['asw_rule_status']    = $get_asw_rule_status;
							$easy_shipping_data['asw_condition_name'] = $get_asw_condition_name;
							update_post_meta( $method_id, 'asw_easy_shipping_data', wp_json_encode( $easy_shipping_data ) );
							$get_default_lang = idm_asw_get_default_language();
							if ( isset( $sitepress ) ) {
								update_post_meta( $method_id, 'asw_esd_lang_key', $get_default_lang );
							}
						}
					} else {
						echo '<div class="updated error"><p>' . esc_html__( 'Error saving shipping method.', 'advanced-easy-shipping-for-woocommerce' ) . '</p></div>';
						return false;
					}
					$asw_add = wp_create_nonce( 'asw_add' );
					if ( 'add' === $action ) {
						$add_action_redirect_url = self::$asw_admin_obj->idm_dynamic_url( self::$current_page, self::$current_tab, 'edit', $method_id, esc_attr( $asw_add ), 'created' );
						wp_safe_redirect( $add_action_redirect_url );
						exit();
					}
					if ( 'edit' === $action ) {
						$edit_action_redirect_url = self::$asw_admin_obj->idm_dynamic_url( self::$current_page, self::$current_tab, 'edit', $method_id, esc_attr( $asw_add ), 'saved' );
						wp_safe_redirect( $edit_action_redirect_url );
						exit();
					}
				}
			}
		}
		/**
		 * Edit shipping method screen.
		 *
		 * @param string $id Get shipping method id.
		 *
		 * @uses     asw_sms_save_method()
		 * @uses     asw_sms_edit_method()
		 *
		 * @since    1.0.0
		 */
		public static function asw_sms_edit_method_screen( $id ) {
			self::asw_sms_save_method( $id );
			self::asw_sms_edit_method();
		}
		/**
		 * Edit shipping method.
		 *
		 * @since    1.0.0
		 */
		public static function asw_sms_edit_method() {
			include ASW_PLUGIN_DIR . '/settings/asw-shipping-settings.php';
		}
		/**
		 * Add new shipping button in shipping list section.
		 *
		 * @param string $link_method_url Link method url.
		 *
		 * @param string $text            button text.
		 */
		public function idm_add_new_shipping_btn_fn( $link_method_url, $text ) {
			?>
			<a href="<?php echo esc_url( $link_method_url ); ?>"
			   class="page-title-action"><?php echo esc_html( $text ); ?>
			</a>
			<?php
		}
		/**
		 * List_shipping_methods function.
		 *
		 * @since    1.0.0
		 *
		 * @uses     ASW_Shipping_Method_Table class
		 * @uses     ASW_Shipping_Method_Table::process_bulk_action()
		 * @uses     ASW_Shipping_Method_Table::prepare_items()
		 * @uses     ASW_Shipping_Method_Table::search_box()
		 * @uses     ASW_Shipping_Method_Table::display()
		 */
		public static function asw_sms_list_methods_screen() {
			if ( ! class_exists( 'ASW_Shipping_Method_Table' ) ) {
				require_once ASW_PLUGIN_DIR . '/includes/class-asw-shipping-method-table.php';
			}
			$link_method_url = self::$asw_admin_obj->idm_dynamic_url( self::$current_page, self::$current_tab, 'add', '', '', '' );
			?>
			<h1 class="wp-heading-inline">
				<?php
				echo esc_html( esc_html__( 'Shipping Listing', 'advanced-easy-shipping-for-woocommerce' ) );
				?>
			</h1>
			<?php
			do_action( 'idm_before_add_new_shipping_btn' );
			do_action( 'idm_add_new_shipping_btn', $link_method_url, 'Add New' );
			do_action( 'idm_after_add_new_shipping_btn' );
			$request_s = filter_input( INPUT_POST, 's', FILTER_SANITIZE_STRING );
			if ( isset( $request_s ) && ! empty( $request_s ) ) {
				?>
				<span class="subtitle">
					<?php
					echo esc_html__( 'Search results for ', 'advanced-easy-shipping-for-woocommerce' ) . '&#8220;' . esc_html( $request_s ) . '&#8221;';
					?>
				</span>
				<?php
			}
			?>
			<hr class="wp-header-end">
			<?php
			$wc_shipping_methods_table = new ASW_Shipping_Method_Table();
			$wc_shipping_methods_table->process_bulk_action();
			$wc_shipping_methods_table->prepare_items();
			$wc_shipping_methods_table->search_box(
				esc_html__(
					'Search Shipping Method',
					'advanced-easy-shipping-for-woocommerce'
				),
				'asw-shipping'
			);
			$wc_shipping_methods_table->display();
		}
		/**
		 * Add_shipping_method_form function.
		 *
		 * @since    1.0.0
		 */
		public static function asw_sms_add_shipping_method_form() {
			include ASW_PLUGIN_DIR . '/settings/asw-shipping-settings.php';
		}
		/**
		 * Get Select attr for selection field.
		 */
		public function asw_get_select_attr_for_selection( $key ) {
			$explode_key_re = '';
			if ( strpos( 'cc_', $key ) === false ) {
				$explode_key = explode( 'cc_', $key );
				if ( array_key_exists( '1', $explode_key ) ) {
					$explode_key_re = $explode_key[1];
				}
			}
			return $explode_key_re;
		}
		/**
		 * Based on cart function.
		 *
		 * @param string $asw_rule_status    Check rule status.
		 *
		 * @param string $asw_condition_name Check rule status.
		 *
		 * @since    1.0.0
		 */
		public function idm_asw_based_on_cart( $asw_rule_status, $asw_condition_name ) {
			global $sitepress;
			$data_gb = 'cb';
			if ( ! empty( $asw_rule_status ) ) {
				$asw_cb_rule_status = asw_check_array_key_exists( 'asw_cb_rule_status', $asw_rule_status );
			}
			$asw_cb_rule_status = ( ! empty( $asw_cb_rule_status ) && 'on' === $asw_cb_rule_status ) ? 'checked' : '';
			?>
			<tr>
				<th scope="row">
					<label for="asw_cb_rule_status">
						<?php esc_html_e( 'Based on Cart', 'advanced-easy-shipping-for-woocommerce' ); ?>
					</label>
					<?php echo wp_kses_post( wc_help_tip( esc_html__( 'In this option, you can apply shipping based on simple product, variable product, categories, tags and skus.', 'advanced-easy-shipping-for-woocommerce' ) ) ); ?>
				</th>
				<td class="forminp">
					<input type="checkbox"
					       id="asw_<?php echo esc_attr( $data_gb ); ?>_rule_status"
					       name="asw_rule_status[asw_<?php echo esc_attr( $data_gb ); ?>_rule_status]"
					       class="asw_rule_chk_status"
					       value="on" <?php echo esc_attr( $asw_cb_rule_status ); ?>
					       data-gb="<?php echo esc_attr( $data_gb ); ?>" data-name="products">
				</td>
			</tr>
			<?php
			if ( 'checked' === $asw_cb_rule_status ) {
				?>
				<tr class="asw-extra-rule" id="asw_<?php echo esc_attr( $data_gb ); ?>_rule_options"
				    data-gb="<?php echo esc_attr( $data_gb ); ?>">
					<td class="tbl_td">
						<table class="asw-extra-rule-div">
							<?php
							if ( ! empty( $asw_condition_name ) ) {
								if ( array_key_exists( $data_gb, $asw_condition_name ) ) {
									foreach ( $asw_condition_name[ $data_gb ] as $asw_cn_key => $asw_cn_value ) {
										$get_asw_cn_key         = $this->asw_get_select_attr_for_selection( $asw_cn_value['condition'] );
										$asw_placeholder_decode = json_decode( asw_placeholder_for_fields(), true );
										$placeholder_key        = '';
										if ( $asw_placeholder_decode ) {
											if ( $get_asw_cn_key ) {
												$placeholder_key = $asw_placeholder_decode[ $get_asw_cn_key ];
											}
										}
										?>
										<tr id="<?php echo esc_attr( $asw_cn_key ); ?>"
										    class="tr_clone">
											<td class="asw_<?php echo esc_attr( $data_gb ); ?>_condition_name_div asw_sub_div"
											    id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_name_div_<?php echo esc_attr( $asw_cn_key ); ?>">
												<select name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][condition]"
												        id=" asw_<?php echo esc_attr( $data_gb ); ?>_condition_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"
												        class="asw_<?php echo esc_attr( $data_gb ); ?>_condition_nt_rmd_data asw_condition_nt_rmd_data">
													<?php
													$get_cart_opt = json_decode( asw_get_based_on_cart_options() );
													foreach ( $get_cart_opt as $key => $value ) {
														$disabled_attr = '';
														if ( 'cc_products' !== $key ) {
															$disabled_attr = 'disabled="disabled"';
														}
														?>
														<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $asw_cn_value['condition'], $key ); ?> <?php echo esc_attr( $disabled_attr ); ?>><?php echo wp_kses_post( $value ); ?></option>
														<?php
													}
													?>
												</select>
											</td>
											<td class="asw_<?php echo esc_attr( $data_gb ); ?>_operator_div asw_sub_div"
											    id="asw_<?php echo esc_attr( $data_gb ); ?>_operator_id_<?php echo esc_attr( $asw_cn_key ); ?>">
												<select name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][operator]"
												        id="asw_<?php echo esc_attr( $data_gb ); ?>_operator_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"
												        class="asw_<?php echo esc_attr( $data_gb ); ?>_operator_nt_rmd_data asw_operator_nt_rmd_data">
													<?php
													$get_conditional_opt = json_decode( asw_conditional_operator( $asw_cn_value['condition'] ) );
													foreach ( $get_conditional_opt as $key => $value ) {
														?>
														<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $asw_cn_value['operator'], $key ); ?>><?php echo wp_kses_post( $value ); ?></option>
														<?php
													}
													?>
												</select>
											</td>
											<td class="asw_<?php echo esc_attr( $data_gb ); ?>_condition_value_div asw_sub_div"
											    id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_value_id_<?php echo esc_attr( $asw_cn_key ); ?>">
												<?php
												if ( 'cc_products' === $asw_cn_value['condition'] ) {
													?>
													<select name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][value][]"
													        id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_value_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"
													        multiple="multiple"
													        class="asw_<?php echo esc_attr( $data_gb ); ?>_products_condition_value_cls multiselect2"
													        data-sel-attr="products"
													        data-placeholder="<?php echo esc_html( $placeholder_key ); ?>">
														<?php
														if ( ! empty( $asw_cn_value['value'] ) ) {
															foreach ( $asw_cn_value['value'] as $asw_value_id ) {
																if ( isset( $sitepress ) ) {
																	$asw_value_id = asw_get_prd_id_based_on_lan( $asw_value_id );
																}
																?>
																<option value="<?php echo esc_attr( $asw_value_id ); ?>" <?php selected( $asw_value_id, $asw_value_id ); ?>><?php echo wp_kses_post( get_the_title( $asw_value_id ) ); ?></option>
																<?php
															}
														}
														?>
													</select>
													<?php
												}
												?>
											</td>
											<td class="asw_condition_add"
											    id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_add_id">
												<a class="button add-aws-condition" data-group="0"
												   href="javascript:void(0);">+</a>
											</td>
											<?php
											if ( 0 !== $asw_cn_key ) {
												?>
												<td class="asw_condition_remove"
												    id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_remove_id">
													<a class="button remove-aws-condition" data-group="0"
													   href="javascript:void(0);">-</a>
												</td>
												<?php
											}
											?>
										</tr>
										<?php
									}
								}
							}
							?>
						</table>
					</td>
				</tr>
				<?php
			}
		}
		/**
		 * Based on locations function.
		 *
		 * @param string $asw_rule_status    Check rule status.
		 *
		 * @param string $asw_condition_name Check rule status.
		 *
		 * @since    1.0.0
		 */
		public function idm_asw_based_on_location( $asw_rule_status, $asw_condition_name ) {
			$data_gb = 'lb';
			if ( ! empty( $asw_rule_status ) ) {
				$asw_lb_rule_status = asw_check_array_key_exists( 'asw_lb_rule_status', $asw_rule_status );
			}
			$asw_lb_rule_status = ( ! empty( $asw_lb_rule_status ) && 'on' === $asw_lb_rule_status ) ? 'checked' : '';
			?>
			<tr>
				<th scope="row">
					<label for="asw_<?php echo esc_attr( $data_gb ); ?>_rule_status">
						<?php esc_html_e( 'Based on Location', 'advanced-easy-shipping-for-woocommerce' ); ?>
					</label>
					<?php echo wp_kses_post( wc_help_tip( esc_html__( 'In this option, you can apply shipping based on location - Country, State, City, Postcode and Zone wise.', 'advanced-easy-shipping-for-woocommerce' ) ) ); ?>
				</th>
				<td class="forminp">
					<input type="checkbox"
					       id="asw_<?php echo esc_attr( $data_gb ); ?>_rule_status"
					       name="asw_rule_status[asw_<?php echo esc_attr( $data_gb ); ?>_rule_status]"
					       class="asw_rule_chk_status"
					       value="on" <?php echo esc_attr( $asw_lb_rule_status ); ?>
					       data-gb="<?php echo esc_attr( $data_gb ); ?>">
				</td>
			</tr>
			<?php
			if ( 'checked' === $asw_lb_rule_status ) {
				?>
				<tr class="asw-extra-rule" id="asw_<?php echo esc_attr( $data_gb ); ?>_rule_options"
				    data-gb="<?php echo esc_attr( $data_gb ); ?>">
					<td class="tbl_td">
						<table class="asw-extra-rule-div">
							<?php
							if ( ! empty( $asw_condition_name ) ) {
								if ( array_key_exists( $data_gb, $asw_condition_name ) ) {
									foreach ( $asw_condition_name[ $data_gb ] as $asw_cn_key => $asw_cn_value ) {
										$get_asw_cn_key         = $this->asw_get_select_attr_for_selection( $asw_cn_value['condition'] );
										$asw_placeholder_decode = json_decode( asw_placeholder_for_fields(), true );
										$placeholder_key        = '';
										if ( $asw_placeholder_decode ) {
											if ( $get_asw_cn_key ) {
												$placeholder_key = $asw_placeholder_decode[ $get_asw_cn_key ];
											}
										}
										?>
										<tr id="<?php echo esc_attr( $asw_cn_key ); ?>"
										    class="tr_clone">
											<td class="asw_<?php echo esc_attr( $data_gb ); ?>_condition_name_div asw_sub_div"
											    id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_name_div_<?php echo esc_attr( $asw_cn_key ); ?>">
												<select name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][condition]"
												        id=" asw_<?php echo esc_attr( $data_gb ); ?>_condition_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"
												        class="asw_<?php echo esc_attr( $data_gb ); ?>_condition_nt_rmd_data asw_condition_nt_rmd_data">
													<?php
													$get_cart_opt = json_decode( asw_get_based_on_locations_options() );
													foreach ( $get_cart_opt as $key => $value ) {
														$disabled_attr = '';
														if ( 'cc_country' !== $key ) {
															$disabled_attr = 'disabled="disabled"';
														}
														?>
														<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $asw_cn_value['condition'], $key ); ?> <?php echo esc_attr( $disabled_attr ); ?>><?php echo wp_kses_post( $value ); ?></option>
														<?php
													}
													?>
												</select>
											</td>
											<td class="asw_<?php echo esc_attr( $data_gb ); ?>_operator_div asw_sub_div"
											    id="asw_<?php echo esc_attr( $data_gb ); ?>_operator_id_<?php echo esc_attr( $asw_cn_key ); ?>">
												<select name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][operator]"
												        id="asw_<?php echo esc_attr( $data_gb ); ?>_operator_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"
												        class="asw_<?php echo esc_attr( $data_gb ); ?>_operator_nt_rmd_data asw_operator_nt_rmd_data">
													<?php
													$get_conditional_opt = json_decode( asw_conditional_operator( $asw_cn_value['condition'] ) );
													foreach ( $get_conditional_opt as $key => $value ) {
														?>
														<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $asw_cn_value['operator'], $key ); ?>><?php echo wp_kses_post( $value ); ?></option>
														<?php
													}
													?>
												</select>
											</td>
											<td class="asw_<?php echo esc_attr( $data_gb ); ?>_condition_value_div asw_sub_div"
											    id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_value_id_<?php echo esc_attr( $asw_cn_key ); ?>">
												<?php
												if ( 'cc_country' === $asw_cn_value['condition'] ) {
													?>
													<select name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][value][]"
													        id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_value_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"
													        multiple="multiple"
													        class="asw_<?php echo esc_attr( $data_gb ); ?>_country_condition_value_cls multiselect2"
													        data-sel-attr="country"
													        data-placeholder="<?php echo esc_html( $placeholder_key ); ?>">
														<?php
														$country_list = json_decode( asw_get_country_list() );
														if ( ! empty( $country_list ) ) {
															foreach ( $country_list as $country_key => $country_val ) {
																if ( ! empty( $asw_cn_value['value'] ) ) {
																	$selected = in_array( $country_key, $asw_cn_value['value'], true ) ? 'selected=selected' : '';
																} else {
																	$selected = '';
																}
																?>
																<option value="<?php echo esc_attr( $country_key ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo wp_kses_post( $country_val ); ?></option>
																<?php
															}
														}
														?>
													</select>
													<?php
												}
												?>
											</td>
											<td class="asw_condition_add"
											    id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_add_id">
												<a class="button add-aws-condition" data-group="0"
												   href="javascript:void(0);">+</a>
											</td>
											<?php
											if ( 0 !== $asw_cn_key ) {
												?>
												<td class="asw_condition_remove"
												    id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_remove_id">
													<a class="button remove-aws-condition" data-group="0"
													   href="javascript:void(0);">-</a>
												</td>
												<?php
											}
											?>
										</tr>
										<?php
									}
								}
							}
							?>
						</table>
					</td>
				</tr>
				<?php
			}
		}
		/**
		 * Based on cart specific options function.
		 *
		 * @param string $asw_rule_status    Check rule status.
		 *
		 * @param string $asw_condition_name Check rule status.
		 *
		 * @since    1.0.0
		 */
		public function idm_asw_based_on_cart_specific( $asw_rule_status, $asw_condition_name ) {
			$data_gb = 'cs';
			if ( ! empty( $asw_rule_status ) ) {
				$asw_cs_rule_status = asw_check_array_key_exists( 'asw_cs_rule_status', $asw_rule_status );
			}
			$asw_cs_rule_status = ( ! empty( $asw_cs_rule_status ) && 'on' === $asw_cs_rule_status ) ? 'checked' : '';
			?>
			<tr>
				<th scope="row">
					<label for="asw_<?php echo esc_attr( $data_gb ); ?>_rule_status">
						<?php esc_html_e( 'Based on Cart Specific', 'advanced-easy-shipping-for-woocommerce' ); ?>
					</label>
					<?php echo wp_kses_post( wc_help_tip( esc_html__( 'In this option, you can apply shipping based on total cart qty, weight, height, width, coupon, shipping class, subtotal with inc. tax, subtotal with ex. tax and subtotal after coupon applied.', 'advanced-easy-shipping-for-woocommerce' ) ) ); ?>
				</th>
				<td class="forminp">
					<input type="checkbox"
					       id="asw_<?php echo esc_attr( $data_gb ); ?>_rule_status"
					       name="asw_rule_status[asw_<?php echo esc_attr( $data_gb ); ?>_rule_status]"
					       class="asw_rule_chk_status"
					       value="on" <?php echo esc_attr( $asw_cs_rule_status ); ?>
					       data-gb="<?php echo esc_attr( $data_gb ); ?>">
				</td>
			</tr>
			<?php
			if ( 'checked' === $asw_cs_rule_status ) {
				?>
				<tr class="asw-extra-rule" id="asw_<?php echo esc_attr( $data_gb ); ?>_rule_options"
				    data-gb="<?php echo esc_attr( $data_gb ); ?>">
					<td class="tbl_td">
						<table class="asw-extra-rule-div">
							<?php
							if ( ! empty( $asw_condition_name ) ) {
								if ( array_key_exists( $data_gb, $asw_condition_name ) ) {
									foreach ( $asw_condition_name[ $data_gb ] as $asw_cn_key => $asw_cn_value ) {
										$get_asw_cn_key         = $this->asw_get_select_attr_for_selection( $asw_cn_value['condition'] );
										$asw_placeholder_decode = json_decode( asw_placeholder_for_fields(), true );
										$placeholder_key        = '';
										if ( $asw_placeholder_decode ) {
											if ( $get_asw_cn_key ) {
												$placeholder_key = $asw_placeholder_decode[ $get_asw_cn_key ];
											}
										}
										?>
										<tr id="<?php echo esc_attr( $asw_cn_key ); ?>"
										    class="tr_clone">
											<td class="asw_<?php echo esc_attr( $data_gb ); ?>_condition_name_div asw_sub_div"
											    id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_name_div_<?php echo esc_attr( $asw_cn_key ); ?>">
												<select name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][condition]"
												        id=" asw_<?php echo esc_attr( $data_gb ); ?>_condition_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"
												        class="asw_<?php echo esc_attr( $data_gb ); ?>_condition_nt_rmd_data asw_condition_nt_rmd_data">
													<?php
													$get_cart_spec_opt = json_decode( asw_get_based_on_cart_specific_options() );
													foreach ( $get_cart_spec_opt as $key => $value ) {
														$disabled_attr = '';
														if ( 'cc_subtotal_af_disc' !== $key ) {
															$disabled_attr = 'disabled="disabled"';
														}
														?>
														<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $asw_cn_value['condition'], $key ); ?> <?php echo esc_attr( $disabled_attr ); ?>><?php echo wp_kses_post( $value ); ?></option>
														<?php
													}
													?>
												</select>
											</td>
											<td class="asw_<?php echo esc_attr( $data_gb ); ?>_operator_div asw_sub_div"
											    id="asw_<?php echo esc_attr( $data_gb ); ?>_operator_id_<?php echo esc_attr( $asw_cn_key ); ?>">
												<select name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][operator]"
												        id="asw_<?php echo esc_attr( $data_gb ); ?>_operator_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"
												        class="asw_<?php echo esc_attr( $data_gb ); ?>_operator_nt_rmd_data asw_operator_nt_rmd_data">
													<?php
													$get_conditional_opt = json_decode( asw_conditional_operator( $asw_cn_value['condition'] ) );
													foreach ( $get_conditional_opt as $key => $value ) {
														?>
														<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $asw_cn_value['operator'], $key ); ?>><?php echo wp_kses_post( $value ); ?></option>
														<?php
													}
													?>
												</select>
											</td>
											<td class="asw_<?php echo esc_attr( $data_gb ); ?>_condition_value_div asw_sub_div"
											    id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_value_id_<?php echo esc_attr( $asw_cn_key ); ?>">
												<input type="text"
												       name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][value]"
												       id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_value_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"
												       value="<?php echo esc_attr( $asw_cn_value['value'] ); ?>"
												       class="asw-td-input-field"
												       placeholder="<?php echo esc_html( $placeholder_key ); ?>"/>

											</td>
											<td class="asw_condition_add"
											    id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_add_id">
												<a class="button add-aws-condition" data-group="0"
												   href="javascript:void(0);">+</a>
											</td>
											<?php
											if ( 0 !== $asw_cn_key ) {
												?>
												<td class="asw_condition_remove"
												    id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_remove_id">
													<a class="button remove-aws-condition" data-group="0"
													   href="javascript:void(0);">-</a>
												</td>
												<?php
											}
											?>
										</tr>
										<?php
									}
								}
							}
							?>
						</table>
					</td>
				</tr>
				<?php
			}
		}
		/**
		 * Based on cart user specific options function.
		 *
		 * @param string $asw_rule_status    Check rule status.
		 *
		 * @param string $asw_condition_name Check rule status.
		 *
		 * @since    1.0.0
		 */
		public function idm_asw_based_on_user_specific( $asw_rule_status, $asw_condition_name ) {
			$data_gb = 'us';
			if ( ! empty( $asw_rule_status ) ) {
				$asw_us_rule_status = asw_check_array_key_exists( 'asw_us_rule_status', $asw_rule_status );
			}
			$asw_us_rule_status = ( ! empty( $asw_us_rule_status ) && 'on' === $asw_us_rule_status ) ? 'checked' : '';
			?>
			<tr>
				<th scope="row">
					<label for="asw_<?php echo esc_attr( $data_gb ); ?>_rule_status">
						<?php esc_html_e( 'Based on user', 'advanced-easy-shipping-for-woocommerce' ); ?>
					</label>
					<?php echo wp_kses_post( wc_help_tip( esc_html__( 'In this option, you can apply shipping based on user and user role.', 'advanced-easy-shipping-for-woocommerce' ) ) ); ?>
				</th>
				<td class="forminp">
					<input type="checkbox"
					       id="asw_<?php echo esc_attr( $data_gb ); ?>_rule_status"
					       name="asw_rule_status[asw_<?php echo esc_attr( $data_gb ); ?>_rule_status]"
					       class="asw_rule_chk_status"
					       value="on" <?php echo esc_attr( $asw_us_rule_status ); ?>
					       data-gb="<?php echo esc_attr( $data_gb ); ?>">
				</td>
			</tr>
			<?php
			if ( 'checked' === $asw_us_rule_status ) {
				?>
				<tr class="asw-extra-rule" id="asw_<?php echo esc_attr( $data_gb ); ?>_rule_options"
				    data-gb="<?php echo esc_attr( $data_gb ); ?>">
					<td class="tbl_td">
						<table class="asw-extra-rule-div">
							<tr id="0" class="tr_clone">
								<td>
									<label><?php esc_html_e( 'Available in Pro', 'advanced-easy-shipping-for-woocommerce' ); ?></label>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<?php
			}
		}
		/**
		 * Based on cart product specific options function.
		 *
		 * @param string $asw_rule_status    Check rule status.
		 *
		 * @param string $asw_condition_name Check rule status.
		 *
		 * @since    1.0.0
		 */
		public function idm_asw_based_on_product_specific( $asw_rule_status, $asw_condition_name ) {
			$data_gb = 'ps';
			if ( ! empty( $asw_rule_status ) ) {
				$asw_ps_rule_status = asw_check_array_key_exists( 'asw_ps_rule_status', $asw_rule_status );
			}
			$asw_ps_rule_status = ( ! empty( $asw_ps_rule_status ) && 'on' === $asw_ps_rule_status ) ? 'checked' : '';
			?>
			<tr>
				<th scope="row">
					<label for="asw_<?php echo esc_attr( $data_gb ); ?>_rule_status">
						<?php esc_html_e( 'Extra Shipping Price', 'advanced-easy-shipping-for-woocommerce' ); ?>
					</label>
					<?php echo wp_kses_post( wc_help_tip( esc_html__( 'In this option, you can apply additional shipping cost with apply per qty, weight, width, height and length and also apply additional cost based on min and max value.', 'advanced-easy-shipping-for-woocommerce' ) ) ); ?>
				</th>
				<td class="forminp">
					<input type="checkbox"
					       id="asw_<?php echo esc_attr( $data_gb ); ?>_rule_status"
					       name="asw_rule_status[asw_<?php echo esc_attr( $data_gb ); ?>_rule_status]"
					       class="asw_rule_chk_status"
					       value="on" <?php echo esc_attr( $asw_ps_rule_status ); ?>
					       data-gb="<?php echo esc_attr( $data_gb ); ?>">
				</td>
			</tr>
			<?php
			if ( 'checked' === $asw_ps_rule_status ) {
				$get_woo_currency_symbol = get_woocommerce_currency_symbol();
				$get_woo_dimension_unit  = get_option( 'woocommerce_dimension_unit' );
				$get_woo_weight_unit     = get_option( 'woocommerce_weight_unit' );
				?>
				<tr class="asw-extra-rule" id="asw_<?php echo esc_attr( $data_gb ); ?>_rule_options"
				    data-gb="<?php echo esc_attr( $data_gb ); ?>">
					<td class="tbl_td">
						<table class="asw-extra-rule-div">
							<?php
							if ( ! empty( $asw_condition_name ) ) {
								if ( array_key_exists( $data_gb, $asw_condition_name ) ) {
									foreach ( $asw_condition_name[ $data_gb ] as $asw_cn_key => $asw_cn_value ) {
										$get_cart_spec_opt = json_decode( asw_get_based_on_product_specific_options() );
										$ut_array          = json_decode( asw_per_unit_options( $asw_cn_value['condition'] ) );
										$get_apu_id        = '';
										$get_ut_id         = '';
										$get_minval        = '';
										$get_maxval        = '';
										$get_price         = '';
										$get_apsub         = '';
										if ( array_key_exists( 'advanced', $asw_cn_value ) ) {
											if ( array_key_exists( 'apu', $asw_cn_value['advanced'] ) ) {
												$get_apu_id = $asw_cn_value['advanced']['apu'];
											}
											$get_apu_status = ( ! empty( $get_apu_id ) && 'on' === $get_apu_id ) ? 'checked' : '';
											if ( array_key_exists( 'ut', $asw_cn_value['advanced'] ) ) {
												$get_ut_id = $asw_cn_value['advanced']['ut'];
											}
											if ( array_key_exists( 'minval', $asw_cn_value['advanced'] ) ) {
												$get_minval = $asw_cn_value['advanced']['minval'];
											}
											if ( array_key_exists( 'maxval', $asw_cn_value['advanced'] ) ) {
												$get_maxval = $asw_cn_value['advanced']['maxval'];
											}
											if ( array_key_exists( 'price', $asw_cn_value['advanced'] ) ) {
												$get_price = $asw_cn_value['advanced']['price'];
											}
											if ( array_key_exists( 'apsub', $asw_cn_value['advanced'] ) ) {
												$get_apsub = $asw_cn_value['advanced']['apsub'];
											}
										}
										$get_asw_cn_key = $this->asw_get_select_attr_for_selection( $asw_cn_value['condition'] );
										?>
										<tr id="<?php echo esc_attr( $asw_cn_key ); ?>"
										    class="tr_clone">
											<td class="asw_<?php echo esc_attr( $data_gb ); ?>_condition_name_div asw_sub_div"
											    id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_name_div_<?php echo esc_attr( $asw_cn_key ); ?>">
												<select name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][condition]"
												        id=" asw_<?php echo esc_attr( $data_gb ); ?>_condition_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"
												        class="asw_<?php echo esc_attr( $data_gb ); ?>_condition_nt_rmd_data asw_condition_nt_rmd_data">
													<?php
													foreach ( $get_cart_spec_opt as $key => $value ) {
														$disabled_attr = '';
														if ( 'cc_tc_spec' !== $key ) {
															$disabled_attr = 'disabled="disabled"';
														}
														?>
														<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $asw_cn_value['condition'], $key ); ?> <?php echo esc_attr( $disabled_attr ); ?>><?php echo wp_kses_post( $value ); ?></option>
														<?php
													}
													?>
												</select>
											</td>
											<td class="asw_<?php echo esc_attr( $data_gb ); ?>_condition_value_div asw_sub_div"
											    id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_value_id_<?php echo esc_attr( $asw_cn_key ); ?>">
												<?php
												if ( 'cc_tc_spec' === $asw_cn_value['condition'] ) {
													?>
													<label for="asw_<?php echo esc_attr( $data_gb ); ?>_condition_value_label_id_<?php echo esc_attr( $asw_cn_key ); ?>"
													       class="asw-td-label-field"><?php echo esc_html__( 'Total Cart' ); ?>
													</label>
													<input type="hidden"
													       name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][value]"
													       id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_value_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"
													       value="Total Cart"/>
													<?php
												}
												?>
											</td>
											<td id="asw_<?php echo esc_attr( $data_gb ); ?>_apu_id_<?php echo esc_attr( $asw_cn_key ); ?>"
											    class="asw_<?php echo esc_attr( $data_gb ); ?>_apu_div asw_sub_div">
												<input type="checkbox"
												       id="asw_<?php echo esc_attr( $data_gb ); ?>_apu_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"
												       class="asw_<?php echo esc_attr( $data_gb ); ?>_apu_nt_rmd_data asw_apu_nt_rmd_data"
												       name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][advanced][apu]" <?php echo esc_attr( $get_apu_status ); ?>>
												<label for="asw_<?php echo esc_attr( $data_gb ); ?>_apu_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"><?php echo esc_html__( 'Cost Per' ); ?></label>
											</td>
											<?php
											if ( ! empty( $get_apu_id ) ) {
												?>
												<td id="asw_<?php echo esc_attr( $data_gb ); ?>_apsub_id_<?php echo esc_attr( $asw_cn_key ); ?>"
												    class="asw_<?php echo esc_attr( $data_gb ); ?>_apsub_div asw_sub_div">
													<label for="asw_<?php echo esc_attr( $data_gb ); ?>_apsub_id_<?php echo esc_attr( $asw_cn_key ); ?>">
														<?php echo esc_html__( 'Each' ); ?>
													</label>
													<input type="number"
													       id="asw_<?php echo esc_attr( $data_gb ); ?>_apsub_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"
													       class="asw_<?php echo esc_attr( $data_gb ); ?>_apsub_nt_rmd_data asw_apsub_nt_rmd_data"
													       name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][advanced][apsub]"
													       placeholder="Each"
													       value="<?php echo esc_attr( $get_apsub ); ?>" min="1">
													<label id="asw_<?php echo esc_attr( $data_gb ); ?>_apsubunit_id_<?php echo esc_attr( $asw_cn_key ); ?>">
														<?php
														if ( 'st_without_tax_disc' === $get_ut_id ) {
															echo esc_html( $get_woo_currency_symbol );
														} elseif ( 'weight' === $get_ut_id ) {
															echo esc_html( $get_woo_weight_unit );
														}
														?>
													</label>
												</td>
												<?php
											}
											?>
											<td id="asw_<?php echo esc_attr( $data_gb ); ?>_ut_id_<?php echo esc_attr( $asw_cn_key ); ?>"
											    class="asw_<?php echo esc_attr( $data_gb ); ?>_ut_div asw_sub_div">
												<select name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][advanced][ut]"
												        class="asw_ps_ut_select">
													<?php
													foreach ( $ut_array as $ut_key => $ut_value ) {
														$disabled_ut_attr = '';
														if ( 'st_without_tax_disc' !== $ut_key &&
														     'weight' !== $ut_key ) {
															$disabled_ut_attr = 'disabled="disabled"';
														}
														?>
														<option value="<?php echo esc_attr( $ut_key ); ?>" <?php selected( $ut_key, $get_ut_id ); ?> <?php echo esc_attr( $disabled_ut_attr ); ?>><?php echo esc_html( $ut_value ); ?></option>
														<?php
													}
													?>
												</select>
											</td>
											<?php
											if ( empty( $get_apu_id ) ) {
												?>
												<td id="asw_<?php echo esc_attr( $data_gb ); ?>_minval_id_<?php echo esc_attr( $asw_cn_key ); ?>"
												    class="asw_<?php echo esc_attr( $data_gb ); ?>_minval_div asw_sub_div">
													<input type="textbox"
													       id="asw_<?php echo esc_attr( $data_gb ); ?>_minval_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"
													       class="asw_<?php echo esc_attr( $data_gb ); ?>_minval_nt_rmd_data asw_minval_nt_rmd_data"
													       name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][advanced][minval]"
													       placeholder="Min Value"
													       value="<?php echo esc_attr( $get_minval ); ?>">
												</td>
												<td id="asw_<?php echo esc_attr( $data_gb ); ?>_maxval_id_<?php echo esc_attr( $asw_cn_key ); ?>"
												    class="asw_<?php echo esc_attr( $data_gb ); ?>_maxval_div asw_sub_div">
													<input type="textbox"
													       id="asw_<?php echo esc_attr( $data_gb ); ?>_maxval_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"
													       class="asw_<?php echo esc_attr( $data_gb ); ?>_maxval_nt_rmd_data asw_maxval_nt_rmd_data"
													       name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][advanced][maxval]"
													       placeholder="Max Value"
													       value="<?php echo esc_attr( $get_maxval ); ?>">
												</td>
												<?php
											}
											?>
											<td id="asw_<?php echo esc_attr( $data_gb ); ?>_price_id_<?php echo esc_attr( $asw_cn_key ); ?>"
											    class="asw_<?php echo esc_attr( $data_gb ); ?>_price_div asw_sub_div">
												<input type="textbox"
												       id="asw_<?php echo esc_attr( $data_gb ); ?>_price_select_id_<?php echo esc_attr( $asw_cn_key ); ?>"
												       class="asw_<?php echo esc_attr( $data_gb ); ?>_price_nt_rmd_data asw_price_nt_rmd_data"
												       name="asw_condition_name[<?php echo esc_attr( $data_gb ); ?>][<?php echo esc_attr( $asw_cn_key ); ?>][advanced][price]"
												       placeholder="Amount"
												       value="<?php echo esc_attr( $get_price ); ?>">
											</td>
											<td class="asw_condition_add"
											    id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_add_id">
												<a class="button add-aws-condition" data-group="0"
												   href="javascript:void(0);">+</a>
											</td>
											<?php
											if ( 0 !== $asw_cn_key ) {
												?>
												<td class="asw_condition_remove"
												    id="asw_<?php echo esc_attr( $data_gb ); ?>_condition_remove_id">
													<a class="button remove-aws-condition" data-group="0"
													   href="javascript:void(0);">-</a>
												</td>
												<?php
											}
											?>
										</tr>
										<?php
									}
								}
							}
							?>
						</table>
					</td>
				</tr>
				<?php
			}
		}
	}
}
