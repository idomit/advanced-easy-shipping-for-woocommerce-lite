<?php
/**
 * If this file is called directly, abort.
 *
 * @package    Advanced_Easy_Shipping_For_WooCommerce
 * @subpackage Advanced_Easy_Shipping_For_WooCommerce/settings
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$asw_general_save       = filter_input( INPUT_POST, 'asw_general_save', FILTER_SANITIZE_STRING );
$asw_general_save_nonce = filter_input( INPUT_POST, 'asw_general_save_nonce', FILTER_SANITIZE_STRING );
$get_tab                = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
$current_page           = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
$message                = filter_input( INPUT_GET, 'message', FILTER_SANITIZE_STRING );
$asw_admin_obj          = new ASW_Admin();
if ( isset( $asw_general_save ) ) {
	if ( empty( $asw_general_save_nonce ) || ! wp_verify_nonce( sanitize_text_field( $asw_general_save_nonce ), 'asw_general_save' ) ) {
		$asw_admin_obj->idm_asw_updated_message( 'nonce_check', $get_tab, '' );
	}
	$asw_general_setting = filter_input( INPUT_POST, 'asw_general_setting', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
	if ( isset( $asw_general_setting ) ) {
		update_option( 'idm_asw_general_option', $asw_general_setting );
		$edit_action_redirect_url = $asw_admin_obj->idm_dynamic_url( $current_page, $current_tab, '', '', '', 'saved' );
		wp_safe_redirect( $edit_action_redirect_url );
		exit();
	}
}
$idm_asw_general_option          = get_option( 'idm_asw_general_option' );
$shipping_selection_cost         = 'user_selection_cost';
$custom_shipping_label           = '';
$combine_shipping_label_list     = 'no';
if ( ! empty( $idm_asw_general_option ) ) {
	$shipping_selection_cost = asw_check_array_key_exists( 'shipping_selection_cost', $idm_asw_general_option );
	if ( $shipping_selection_cost ) {
		$shipping_selection_cost = $shipping_selection_cost;
	} else {
		$shipping_selection_cost = 'user_selection_cost';
	}
	$custom_shipping_label           = asw_check_array_key_exists( 'custom_shipping_label', $idm_asw_general_option );
	$combine_shipping_label_list     = asw_check_array_key_exists( 'combine_shipping_label_list', $idm_asw_general_option );
}
$title_text = esc_html__( 'General Setting', 'advanced-easy-shipping-for-woocommerce' );
?>
	<h1 class="wp-heading-inline"><?php echo esc_html( $title_text ); ?></h1>
	<hr class="wp-header-end">
<?php
if ( isset( $message ) ) {
	$asw_admin_obj->idm_asw_updated_message( 'saved', $get_tab, '' );
}
?>
	<table class="form-table asw-tbl-cls">
		<tbody>
		<tr>
			<th scope="row" class="titledesc">
				<label for="asw_shipping_cost_type"><?php esc_html_e( 'Shipping Cost Type', 'advanced-easy-shipping-for-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( esc_html__( 'You can display shipping cost with user selection cost, maximum cost, minimum cost and combine all cost', 'advanced-easy-shipping-for-woocommerce' ) ) ); ?>
			</th>
			<td class="forminp forminp-radio">
				<fieldset>
					<ul>
						<li>
							<label>
								<input name="asw_general_setting[shipping_selection_cost]" value="user_selection_cost"
								type="radio"
								class="" <?php checked( $shipping_selection_cost, 'user_selection_cost' ); ?>>
								<?php echo esc_html__( 'Based on user selection', 'advanced-easy-shipping-for-woocommerce' ); ?>
							</label>
						</li>
						<li>
							<label>
								<input name="asw_general_setting[shipping_selection_cost]" value="maximum_cost"
								type="radio" style=""
								class="" <?php checked( $shipping_selection_cost, 'maximum_cost' ); ?>>
								<?php echo esc_html__( 'Maximum Shipping Cost', 'advanced-easy-shipping-for-woocommerce' ); ?>
							</label>
						</li>
						<li>
							<label>
								<input name="asw_general_setting[shipping_selection_cost]" value="minimum_cost"
								type="radio" style=""
								class="" <?php checked( $shipping_selection_cost, 'minimum_cost' ); ?>>
								<?php echo esc_html__( 'Minimum Shipping Cost', 'advanced-easy-shipping-for-woocommerce' ); ?>
							</label>
						</li>
					</ul>
				</fieldset>
			</td>
		</tr>
		</tbody>
	</table>
	<p class="submit">
		<input type="submit" class="button button-primary" name="asw_general_save"
		value="<?php esc_html_e( 'Save Changes', 'advanced-easy-shipping-for-woocommerce' ); ?>">
	</p>
<?php
wp_nonce_field( 'asw_general_save', 'asw_general_save_nonce' );
