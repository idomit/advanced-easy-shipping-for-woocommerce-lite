<?php
/**
 * Shipping setting file.
 *
 * @package    Advanced_Easy_Shipping_For_WooCommerce
 * @subpackage Advanced_Easy_Shipping_For_WooCommerce/settings
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$current_tab  = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
$current_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
$get_action   = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );
$get_id       = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
if ( 'edit' === $get_action ) {
	if ( ! empty( $get_id ) && '' !== $get_id ) {
		$get_post_id                = isset( $get_id ) ? sanitize_text_field( wp_unslash( $get_id ) ) : '';
		$asw_shipping_status        = get_post_status( $get_post_id );
		$asw_shipping_title         = get_the_title( $get_post_id );
		$asw_easy_shipping_data     = get_post_meta( $get_post_id, 'asw_easy_shipping_data', true );
		$asw_easy_shipping_data_dcd = json_decode( $asw_easy_shipping_data, true );
		$asw_gss                    = asw_check_array_key_exists( 'asw_gss', $asw_easy_shipping_data_dcd );
		if ( ! empty( $asw_gss ) ) {
			$asw_shipping_cost           = asw_check_array_key_exists( 'asw_shipping_cost', $asw_easy_shipping_data_dcd['asw_gss'] );
			$asw_shipping_tooltip        = asw_check_array_key_exists( 'asw_shipping_tooltip', $asw_easy_shipping_data_dcd['asw_gss'] );
			$asw_cost_per                = asw_check_array_key_exists( 'asw_cost_per', $asw_easy_shipping_data_dcd['asw_gss'] );
			$asw_shipping_handling_price = asw_check_array_key_exists( 'asw_shipping_handling_price', $asw_easy_shipping_data_dcd['asw_gss'] );
			$asw_rule_status             = asw_check_array_key_exists( 'asw_rule_status', $asw_easy_shipping_data_dcd );
			$asw_condition_name          = asw_check_array_key_exists( 'asw_condition_name', $asw_easy_shipping_data_dcd );
		} else {
			$asw_shipping_cost           = '';
			$asw_shipping_tooltip        = '';
			$asw_shipping_handling_price = '';
			$asw_rule_status             = '';
			$asw_condition_name          = '';
		}
		$title_text = esc_html__( 'Edit Shipping Method', 'advanced-easy-shipping-for-woocommerce' );
	}
} else {
	$get_post_id                 = '';
	$asw_shipping_status         = '';
	$asw_shipping_title          = '';
	$asw_shipping_cost           = '';
	$asw_shipping_tooltip        = '';
	$asw_cost_per                = 'not-need';
	$asw_shipping_handling_price = '';
	$asw_rule_status             = '';
	$asw_condition_name          = '';
	$title_text                  = esc_html__( 'Add Shipping Method', 'advanced-easy-shipping-for-woocommerce' );
}
$asw_shipping_status  = ( ( ! empty( $asw_shipping_status ) && 'publish' === $asw_shipping_status ) || empty( $asw_shipping_status ) ) ? 'checked' : '';
$asw_shipping_title   = ! empty( $asw_shipping_title ) ? esc_attr( stripslashes( $asw_shipping_title ) ) : '';
$asw_shipping_cost    = ( '' !== $asw_shipping_cost ) ? esc_attr( stripslashes( $asw_shipping_cost ) ) : '';
$asw_shipping_tooltip = ! empty( $asw_shipping_tooltip ) ? $asw_shipping_tooltip : '';
$submit_text          = esc_html__( 'Save changes', 'advanced-easy-shipping-for-woocommerce' );
?>
	<h1 class="wp-heading-inline"><?php echo esc_html( $title_text ); ?></h1>
<?php
$asw_admin_obj       = new ASW_Admin();
$manage_shipping_url = $asw_admin_obj->idm_dynamic_url( $current_page, 'shipping_section', '', '', '', '' );
do_action( 'idm_add_new_shipping_btn', $manage_shipping_url, esc_html__( 'Back to Shipping List', 'advanced-easy-shipping-for-woocommerce' ) );
?>
	<hr class="wp-header-end">
	<table class="form-table asw-tbl-cls">
		<tbody>
		<tr>
			<th scope="row">
				<label for="asw_shipping_status"><?php esc_html_e( 'Shipping Status', 'advanced-easy-shipping-for-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( esc_html__( 'You can enable or disable shipping method. If shipping method will disable then it will not apply.', 'advanced-easy-shipping-for-woocommerce' ) ) ); ?>
			</th>
			<td class="forminp">
				<input type="checkbox" name="asw_shipping_status"
				       value="on" <?php echo esc_attr( $asw_shipping_status ); ?>>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="asw_shipping_title"><?php esc_html_e( 'Shipping title', 'advanced-easy-shipping-for-woocommerce' ); ?>
					<span class="required-star">*</span>
				</label>
				<?php echo wp_kses_post( wc_help_tip( esc_html__( 'This name will be display as shipping at front side.', 'advanced-easy-shipping-for-woocommerce' ) ) ); ?>
			</th>
			<td class="forminp">
				<input type="text" name="asw_shipping_title" class="text-class"
				       id="asw_shipping_title" value="<?php echo esc_attr( $asw_shipping_title ); ?>" required="1"
				       placeholder="<?php esc_html_e( 'Enter shipping title', 'advanced-easy-shipping-for-woocommerce' ); ?>">
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="asw_shipping_cost">
					<?php esc_html_e( 'Shipping cost', 'advanced-easy-shipping-for-woocommerce' ); ?>
					( <?php echo esc_html( get_woocommerce_currency_symbol() ); ?> )
					<span class="required-star">*</span>
				</label>
				<?php echo wp_kses_post( wc_help_tip( esc_html__( 'Shipping base amount.', 'advanced-easy-shipping-for-woocommerce' ) ) ); ?>
			</th>
			<td class="forminp">
				<input type="text" name="asw_gss[asw_shipping_cost]" required="1" class="text-class"
				       id="asw_shipping_cost"
				       value="<?php echo esc_attr( $asw_shipping_cost ); ?>"
				       placeholder="<?php echo esc_attr( get_woocommerce_currency_symbol() ); ?>">
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="asw_shipping_handling_price"><?php esc_html_e( 'Is Amount Taxable?', 'advanced-easy-shipping-for-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( esc_html__( 'If yes, then shipping price will be calculated based on tax otherwise this will apply based on base price.', 'advanced-easy-shipping-for-woocommerce' ) ) ); ?>
			</th>
			<td class="forminp">
				<select name="asw_gss[asw_shipping_handling_price]" id="asw_shipping_handling_price" class="">
					<option value="no" <?php echo isset( $asw_shipping_handling_price ) && 'no' === $asw_shipping_handling_price ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'None', 'advanced-easy-shipping-for-woocommerce' ); ?></option>
					<option value="yes" <?php echo isset( $asw_shipping_handling_price ) && 'yes' === $asw_shipping_handling_price ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Taxable', 'advanced-easy-shipping-for-woocommerce' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="asw_cost_per"><?php esc_html_e( 'General Cost Per', 'advanced-easy-shipping-for-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( esc_html__( 'This will apply shipping cost based on selection. Like: 1. Per Order - It will apply basic cost. 2. Per Item - It will apply based on cart items. 3. Per Line Item - It will apply based on cart line items. 4. Per Class - It will apply based on shipping class in cart.', 'advanced-easy-shipping-for-woocommerce' ) ) ); ?>
			</th>
			<td class="forminp">
				<select name="asw_gss[asw_cost_per]" id="asw_cost_per" class="">
					<option value="not-need" <?php echo isset( $asw_cost_per ) && 'not-need' === $asw_cost_per ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'No Cost', 'advanced-easy-shipping-for-woocommerce' ); ?></option>
					<option value="per-order" <?php echo isset( $asw_cost_per ) && 'per-order' === $asw_cost_per ? 'selected="selected"' : ''; ?>><?php esc_html_e( 'Per Order', 'advanced-easy-shipping-for-woocommerce' ); ?></option>
					<option value="per-item" <?php echo isset( $asw_cost_per ) && 'per-item' === $asw_cost_per ? 'selected="selected"' : ''; ?> disabled><?php esc_html_e( 'Per Item (Qty)', 'advanced-easy-shipping-for-woocommerce' ); ?></option>
					<option value="per-line-item" <?php echo isset( $asw_cost_per ) && 'per-line-item' === $asw_cost_per ? 'selected="selected"' : ''; ?> disabled><?php esc_html_e( 'Per Line Item (Cart Item)', 'advanced-easy-shipping-for-woocommerce' ); ?></option>
					<option value="per-class" <?php echo isset( $asw_cost_per ) && 'per-class' === $asw_cost_per ? 'selected="selected"' : ''; ?> disabled><?php esc_html_e( 'Per Class', 'advanced-easy-shipping-for-woocommerce' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="asw_shipping_tooltip"><?php esc_html_e( 'Tooltip Description', 'advanced-easy-shipping-for-woocommerce' ); ?></label>
				<?php echo wp_kses_post( wc_help_tip( esc_html__( 'This tooltip description will display at front side for shipping method.', 'advanced-easy-shipping-for-woocommerce' ) ) ); ?>
			</th>
			<td class="forminp">
				<textarea name="asw_gss[asw_shipping_tooltip]" rows="3" cols="70" id="asw_shipping_tooltip"
				          placeholder="<?php esc_html_e( 'Enter tooltip description', 'advanced-easy-shipping-for-woocommerce' ); ?>"><?php echo wp_kses_post( $asw_shipping_tooltip ); ?></textarea>
			</td>
		</tr>
		<?php do_action( 'idm_cart_products_specific_rule', $asw_rule_status, $asw_condition_name ); ?>
		<?php do_action( 'idm_location_specific_rule', $asw_rule_status, $asw_condition_name ); ?>
		<?php do_action( 'idm_cart_specific_rule', $asw_rule_status, $asw_condition_name ); ?>
		<?php do_action( 'idm_user_specific_rule', $asw_rule_status, $asw_condition_name ); ?>
		<?php do_action( 'idm_product_specific_rule', $asw_rule_status, $asw_condition_name ); ?>
		</tbody>
	</table>
	<p class="submit">
		<input type="submit" class="button button-primary" name="asw_save"
		       value="<?php esc_html_e( 'Save Changes', 'advanced-easy-shipping-for-woocommerce' ); ?>">
	</p>
<?php
wp_nonce_field( 'woocommerce_save_method', 'woocommerce_save_method_nonce' );


