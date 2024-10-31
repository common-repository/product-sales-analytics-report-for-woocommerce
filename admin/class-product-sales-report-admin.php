<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 */
class PSARFW_Product_Sales_Analytics_Report_For_Woocommerce_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		global $pagenow;

		if ( $pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'psarfw_settings' ) {
			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in PSARFW_Product_Sales_Analytics_Report_For_Woocommerce_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The PSARFW_Product_Sales_Analytics_Report_For_Woocommerce_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/product-sales-reports-admin.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $pagenow;

		if ( $pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'psarfw_settings' ) {
			/**
			 * This function is provided for demonstration purposes only.
			 *
			 * An instance of this class should be passed to the run() function
			 * defined in PSARFW_Product_Sales_Analytics_Report_For_Woocommerce_Loader as all of the hooks are defined
			 * in that particular class.
			 *
			 * The PSARFW_Product_Sales_Analytics_Report_For_Woocommerce_Loader will then create the relationship
			 * between the defined hooks and the functions defined in this
			 * class.
			 */

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/product-sales-reports-admin.js', array( 'jquery' ), $this->version, false );
		}
	}

	/**
	 * Admin menu
	 */
	public function psarfw_admin_menu() {
		add_submenu_page('woocommerce', 'Product Sales Analytics Report', 'Product Sales Analytics Report', 'view_woocommerce_reports', 'psarfw_settings', array($this, 'psarfw_settings_render'));
	}

	/**
	 * Sales Analytics Reports Settings Render
	 */
	function psarfw_settings_render() {				
		include_once('partials/product-sales-reports-admin-display.php');
	}

	public function psarfw_admin_init() {
		global $pagenow;

		if (!is_admin())
			return;
		if ( $pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'psarfw_settings' ) {
			nocache_headers();

			if ( current_user_can('view_woocommerce_reports') && isset($_POST['psarfw_hidden_do_export']) && !empty($_POST['psarfw_hidden_do_export'])) {
				
				check_admin_referer('psarfw_do_export'); // Verify the nonce

				$postSettings = array();

				if(isset($_POST['psarfw_hidden_do_export'])) {
					$postSettings['psarfw_hidden_do_export'] = sanitize_text_field(wp_unslash($_POST['psarfw_hidden_do_export']));
				}
				
				if(isset($_POST['report_time'])) {
					$postSettings['report_time'] = sanitize_text_field(wp_unslash($_POST['report_time']));
				}
				
				if(isset($_POST['report_start'])) {
					$postSettings['report_start'] = sanitize_text_field(wp_unslash($_POST['report_start']));
				}
				
				if(isset($_POST['report_end'])) {
					$postSettings['report_end'] = sanitize_text_field(wp_unslash($_POST['report_end']));
				}
				
				if(isset($_POST['order_statuses'])) {
					$postSettings['order_statuses'] = array_map(function($val) { return sanitize_text_field(wp_unslash($val)); },$_POST['order_statuses']);
				}
				
				if(isset($_POST['products'])) {
					$postSettings['products'] = sanitize_text_field(wp_unslash($_POST['products']));
				}
				
				if(isset($_POST['product_cats'])) {
					$postSettings['product_cats'] = array_map(function($val) { return sanitize_text_field(wp_unslash($val)); },$_POST['product_cats']);
				}
				
				if(isset($_POST['product_ids'])) {
					$postSettings['product_ids'] = sanitize_text_field(wp_unslash($_POST['product_ids']));
				}
				
				if(isset($_POST['variations'])) {
					$postSettings['variations'] = sanitize_text_field(wp_unslash($_POST['variations']));
				}
				
				if(isset($_POST['orderby'])) {
					$postSettings['orderby'] = sanitize_text_field(wp_unslash($_POST['orderby']));
				}
				
				if(isset($_POST['orderdir'])) {
					$postSettings['orderdir'] = sanitize_text_field(wp_unslash($_POST['orderdir']));
				}
				
				if(isset($_POST['fields'])) {
					$postSettings['fields'] = array_map(function($val) { return sanitize_text_field(wp_unslash($val)); },$_POST['fields']);
				}
				
				if(isset($_POST['exclude_free'])) {
					$postSettings['exclude_free'] = sanitize_text_field(wp_unslash($_POST['exclude_free']));
				}
				
				if(isset($_POST['limit_on'])) {
					$postSettings['limit_on'] = sanitize_text_field(wp_unslash($_POST['limit_on']));
				}
				
				if(isset($_POST['limit'])) {
					$postSettings['limit'] = intval($_POST['limit']);
				}
				
				if(isset($_POST['intermediate_rounding'])) {
					$postSettings['intermediate_rounding'] = sanitize_text_field(wp_unslash($_POST['intermediate_rounding']));
				}
				
				if(isset($_POST['include_header'])) {
					$postSettings['include_header'] = sanitize_text_field(wp_unslash($_POST['include_header']));
				}
				
				if(isset($_POST['psarfw_debug'])) {
					$postSettings['psarfw_debug'] = sanitize_text_field(wp_unslash($_POST['psarfw_debug']));
				}

				$newSettings = array_intersect_key($postSettings, psarfw_default_report_options());
				foreach ($newSettings as $key => $value)
				if (!is_array($value))
					$newSettings[$key] = htmlspecialchars($value);
				
				// Update the saved report settings
				$savedReportSettings = get_option('psarfw_report_settings');
				if(!is_array($savedReportSettings))	$savedReportSettings = [];	// Create array if not exists
				$savedReportSettings[0] = array_merge(psarfw_default_report_options(), $newSettings);

				update_option('psarfw_report_settings', $savedReportSettings, false);
				
				// Check if no fields are selected or if not downloading
				if (empty($_POST['fields']) || empty($_POST['psarfw_sbp_download']))
					return;

				// Assemble the filename for the report download
				$filename =  'Product Sales - ';
				$filename .= gmdate('Y-m-d', current_time('timestamp', true)).'.csv';
				
				// Send headers
				header('Content-Type: text/csv');
				header('Content-Disposition: attachment; filename="'.$filename.'"');
				
				if (!empty($_POST['fields'])) {
				
					// Output the report header row (if applicable) and body
					$stdout = fopen('php://output', 'w');
					if (!empty($_POST['include_header'])) {
						psarfw_header_export($stdout);
						psarfw_body_export($stdout);
					}
				}
				exit;
			}
		}
	}
}