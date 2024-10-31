<?php
/**
 * Plugin Name:       Product Sales Analytics Report for WooCommerce
 * Description:       Setup a custom sales analytics report for the products in your WooCommerce store with toggle sorting options. Including or excluding items based on date range, sale status, product category and id, define display order, choose what fields to include, and generate your report with a click.
 * Version:           1.0.2
 * Author:            Codenix
 * Author URI:        https://codenix.io/
 * WC tested up to:   9.3.1
 * Requires Plugins:  woocommerce
 * License: 		  GPLv2 or later
 * License URI: 	  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       product-sales-analytics-reports-for-woocommerce
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'PSARFW_VERSION', '1.0.2' );

if (!defined('PSARFW_PLUGIN_DIR_PATH'))
    define('PSARFW_PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));

/**
 * The core plugin class that is used to define
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-product-sales-reports.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function psarfw_run_product_sales_analytics_report_for_woocommerce() {

	$plugin = new PSARFW_Product_Sales_Analytics_Report_For_Woocommerce();
	$plugin->run();
}
psarfw_run_product_sales_analytics_report_for_woocommerce();

/**
 * Settings links when plugin is active
*/
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_{$plugin}", 'psarfw_plugin_add_settings_link');

function psarfw_plugin_add_settings_link($links) {
	$settings_link = '<a href="admin.php?page=psarfw_settings">' . __('Settings','product-sales-analytics-reports-for-woocommerce') . '</a>';
	array_unshift($links, $settings_link);

	return $links;
}

/**
 * Added HPOS support for woocommerce
 */
add_action( 'before_woocommerce_init', 'psarfw_before_woocommerce_init' );
function psarfw_before_woocommerce_init() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
}