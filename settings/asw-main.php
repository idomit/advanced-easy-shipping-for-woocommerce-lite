<?php
/**
 * Plugins main file.
 *
 * @package    Advanced_Easy_Shipping_For_WooCommerce
 * @subpackage Advanced_Easy_Shipping_For_WooCommerce/settings
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$get_page          = ASW_Admin::idm_asw_current_page();
$current_tab       = ASW_Admin::idm_asw_current_tab();
$current_tab_array = do_action( 'idm_asw_admin_action_current_tab' );
if ( has_filter( 'idm_asw_ie_admin_tab_ft' ) ) {
	$tabing_array = apply_filters( 'idm_asw_ie_admin_tab_ft', $current_tab_array );
} else {
	$tabing_array = apply_filters( 'idm_asw_admin_tab_ft', '' );
}
?>
<div class="wrap woocommerce">
	<form method="post" enctype="multipart/form-data">
		<nav class="nav-tab-wrapper woo-nav-tab-wrapper">
			<?php
			foreach ( $tabing_array as $name => $label ) {
				$idm_url = ASW_Admin::idm_dynamic_url( $get_page, $name );
				echo '<a href="' . esc_url( $idm_url ) . '" class="nav-tab ';
				if ( $current_tab === $name ) {
					echo 'nav-tab-active';
				}
				echo '">' . esc_html( $label ) . '</a>';
			}
			?>
		</nav>
		<?php
		if ( has_filter( 'idm_asw_ie_admin_page_ft' ) ) {
			apply_filters( 'idm_asw_ie_admin_page_ft', $current_tab );
			apply_filters( 'idm_asw_getting_page', $current_tab );
		} else {
			apply_filters( 'idm_asw_getting_page', $current_tab );
		}
		?>
	</form>
</div>
