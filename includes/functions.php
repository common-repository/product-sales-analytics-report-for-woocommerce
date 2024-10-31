<?php
/**
 * Defines functions for the plugin.
*/
function psarfw_default_report_options() {
    return array(
        'report_time'    => '30d',
        'report_start'   => gmdate('Y-m-d', current_time('timestamp',true) - (86400 * 31)),
        'report_end'     => gmdate('Y-m-d', current_time('timestamp',true) - 86400),
        'order_statuses' => array('wc-processing', 'wc-on-hold', 'wc-completed'),
        'products'       => 'all',
        'product_cats'   => array(),
        'product_ids'    => '',
        'variations'     => 0,
        'orderby'        => 'quantity',
        'orderdir'       => 'desc',
        'fields'         => array('product_id', 'product_sku', 'product_name', 'quantity_sold', 'gross_sales'),
        'limit_on'       => 0,
        'limit'          => 10,
        'include_header' => 1,
        'intermediate_rounding' => 0,
        'exclude_free'   => 0,
        'psarfw_psr_debug' => 0
    );
}

// This function outputs the report header row
function psarfw_header_export($dest, $return = false) {
    $header = array();

    if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'psarfw_do_export' ) ) {
        if( isset( $_POST['fields'] ) ) {
            $fields = array_map(function($val) { return sanitize_text_field(wp_unslash($val)); },$_POST['fields']);

            foreach ($fields as $field) {
                switch ($field) {
                    case 'product_id':
                        $header[] = esc_html__('Product ID', 'product-sales-analytics-reports-for-woocommerce');
                        break;
                    case 'variation_id':
                        $header[] = esc_html__('Variation ID', 'product-sales-analytics-reports-for-woocommerce');
                        break;
                    case 'product_sku':
                        $header[] = esc_html__('Product SKU', 'product-sales-analytics-reports-for-woocommerce');
                        break;
                    case 'product_name':
                        $header[] = esc_html__('Product Name', 'product-sales-analytics-reports-for-woocommerce');
                        break;
                    case 'variation_attributes':
                        $header[] = esc_html__('Variation Attributes', 'product-sales-analytics-reports-for-woocommerce');
                        break;
                    case 'quantity_sold':
                        $header[] = esc_html__('Quantity Sold', 'product-sales-analytics-reports-for-woocommerce');
                        break;
                    case 'gross_sales':
                        $header[] = esc_html__('Gross Sales', 'product-sales-analytics-reports-for-woocommerce');
                        break;
                    case 'gross_after_discount':
                        $header[] = esc_html__('Gross Sales (After Discounts)', 'product-sales-analytics-reports-for-woocommerce');
                        break;
                    case 'product_categories':
                        $header[] = esc_html__('Product Categories', 'product-sales-analytics-reports-for-woocommerce');
                        break;
                }
            }
        }
    }

    if ($return)
        return $header;

    fputcsv($dest, $header);
}

function psarfw_fetch_report_dates() {
	// Calculate report start and end dates (timestamps)
    if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'psarfw_do_export' ) ) {
        if( isset( $_POST['report_time'] ) ) {
            switch ($_POST['report_time']) {
                case '0d':
                    $end_date = strtotime('midnight', current_time('timestamp',true));
                    $start_date = $end_date;
                    break;
                case '1d':
                    $end_date = strtotime('midnight', current_time('timestamp',true)) - 86400;
                    $start_date = $end_date;
                    break;
                case '7d':
                    $end_date = strtotime('midnight', current_time('timestamp',true)) - 86400;
                    $start_date = $end_date - (86400 * 6);
                    break;
                case '1cm':
                    $start_date = strtotime(gmdate('Y-m', current_time('timestamp',true)) . '-01 midnight -1month');
                    $end_date = strtotime('+1month', $start_date) - 86400;
                    break;
                case '0cm':
                    $start_date = strtotime(gmdate('Y-m', current_time('timestamp',true)) . '-01 midnight');
                    $end_date = strtotime('+1month', $start_date) - 86400;
                    break;
                case '+1cm':
                    $start_date = strtotime(gmdate('Y-m', current_time('timestamp',true)) . '-01 midnight +1month');
                    $end_date = strtotime('+1month', $start_date) - 86400;
                    break;
                case '+7d':
                    $start_date = strtotime('midnight', current_time('timestamp',true)) + 86400;
                    $end_date = $start_date + (86400 * 6);
                    break;
                case '+30d':
                    $start_date = strtotime('midnight', current_time('timestamp',true)) + 86400;
                    $end_date = $start_date + (86400 * 29);
                    break;
                case 'custom':
                    $end_date = (isset($_POST['report_end'])) ? strtotime('midnight', strtotime(sanitize_text_field(wp_unslash($_POST['report_end'])))) : "";
                    $start_date = (isset($_POST['report_start'])) ? strtotime('midnight', strtotime(sanitize_text_field(wp_unslash($_POST['report_start'])))) : "";
                    break;
                default: // 30 days is the default
                    $end_date = strtotime('midnight', current_time('timestamp')) - 86400;
                    $start_date = $end_date - (86400 * 29);
            }
            
            return [$start_date, $end_date];
        }
    }
}

// This function generates and outputs the report body rows
function psarfw_body_export($dest, $return = false) {
    global $woocommerce, $wpdb;
    
    if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'psarfw_do_export' ) ) {
        $product_ids = array();
        $inc_products               = (isset($_POST['products'])) ? sanitize_text_field(wp_unslash($_POST['products'])) : '';
        $inc_product_cats           = (isset($_POST['product_cats'])) ? array_map(function($val) { return sanitize_text_field(wp_unslash($val)); },$_POST['product_cats']) : array();
        $inc_product_ids            = (isset($_POST['product_ids'])) ? sanitize_text_field(wp_unslash($_POST['product_ids'])) : '';
        $product_orderby            = (isset($_POST['orderby'])) ? sanitize_text_field(wp_unslash($_POST['orderby'])) : '';
        $product_orderdir           = (isset($_POST['orderdir'])) ? sanitize_text_field(wp_unslash($_POST['orderdir'])) : '';
        $report_time                = (isset($_POST['report_time'])) ? sanitize_text_field(wp_unslash($_POST['report_time'])) : '';
        $exclude_free               = (isset($_POST['exclude_free']) && !empty($_POST['exclude_free'])) ? 'on' : '';
        $intermediate_rounding      = (isset($_POST['intermediate_rounding']) && !empty($_POST['intermediate_rounding'])) ? 'on' : '';
        $limit                      = (isset($_POST['limit_on']) && !empty($_POST['limit_on']) && isset($_POST['limit']) && is_numeric($_POST['limit'])) ? intval($_POST['limit']) : '';
        $debug                      = (isset($_POST['psarfw_debug']) && !empty($_POST['psarfw_debug'])) ? true : false;

        if ($inc_products == 'cats') {
            $cats = array();
            foreach ($inc_product_cats as $cat)
                if (is_numeric($cat))
                    $cats[] = $cat;
            $product_ids = get_objects_in_term($cats, 'product_cat');
        } else if ($inc_products == 'ids') {
            foreach (explode(',', $inc_product_ids) as $productId) {
                $productId = trim($productId);
                if (is_numeric($productId))
                    $product_ids[] = $productId;
            }
        }

        list($start_date, $end_date) = psarfw_fetch_report_dates();

        // Assemble order by string
        $orderby = (in_array($product_orderby, array('product_id', 'gross', 'gross_after_discount')) ? $product_orderby : 'quantity');
        $orderby .= ' ' . ($product_orderdir == 'asc' ? 'ASC' : 'DESC');

        // Create a new WC_Admin_Report object
        if ( psarfw_is_hpos() ) {
            include_once(__DIR__.'/class-wc-admin-report-hpos.php');
            $wc_report = new PSARFW_WC_ADMIN_REPORT_HPOS();
        } else {
            include_once($woocommerce->plugin_path().'/includes/admin/reports/class-wc-admin-report.php');
            $wc_report = new WC_Admin_Report();
        }
        $wc_report->start_date = $start_date;
        $wc_report->end_date = $end_date;

        $where_meta = array();
        if ($inc_products != 'all') {
            $where_meta[] = array(
                'type'       => 'order_item_meta',
                'meta_key'   => '_product_id',
                'operator'   => 'in',
                'meta_value' => $product_ids
            );
        }
        if ($exclude_free == 'on') {
            $where_meta[] = array(
                'meta_key'   => '_line_total',
                'meta_value' => 0,
                'operator'   => '!=',
                'type'       => 'order_item_meta'
            );
        }

        // Get report data

        // Avoid max join size error
        $wpdb->query('SET SQL_BIG_SELECTS=1');

        // Prevent plugins from overriding the order status filter
        add_filter('woocommerce_reports_order_statuses', 'psarfw_report_order_statuses', PHP_INT_MAX);
        
        if ($intermediate_rounding == 'on') {
            // Filter report query - intermediate rounding
            add_filter('woocommerce_reports_get_order_report_query', 'psarfw_filter_query_final_rounding');
        }

        // Based on woocommerce/includes/admin/reports/class-wc-report-sales-by-product.php
        $sold_products = $wc_report->get_order_report_data(array(
            'data'         => array(
                '_product_id'    => array(
                    'type'            => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function'        => '',
                    'name'            => 'product_id'
                ),
                '_qty'           => array(
                    'type'            => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function'        => 'SUM',
                    'name'            => 'quantity'
                ),
                '_line_subtotal' => array(
                    'type'            => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function'        => $intermediate_rounding == 'on' ? 'PSRSUM' : 'SUM',
                    'name'            => 'gross'
                ),
                '_line_total'    => array(
                    'type'            => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function'        => $intermediate_rounding == 'on' ? 'PSRSUM' : 'SUM',
                    'name'            => 'gross_after_discount'
                )
            ),
            'query_type'   => 'get_results',
            'group_by'     => 'product_id',
            'where_meta'   => $where_meta,
            'order_by'     => $orderby,
            'limit'        => $limit,
            'filter_range' => ($report_time != 'all'),
            'order_types'  => wc_get_order_types(),
            'order_status' => psarfw_report_order_statuses(),
            'debug'        => $debug
        ));

        // Remove report order statuses filter
        remove_filter('woocommerce_reports_order_statuses', 'psarfw_report_order_statuses', PHP_INT_MAX);
        
        if ($intermediate_rounding) {
            // Remove filter report query - intermediate rounding
            remove_filter('woocommerce_reports_get_order_report_query', 'psarfw_filter_query_final_rounding');
        }

        if ($return)
            $rows = array();

        // Output report rows
        foreach ($sold_products as $product) {
            $row = array();

            if( isset($_POST['fields']) ) {
                foreach ($_POST['fields'] as $field) {
                    switch ($field) {
                        case 'product_id':
                            $row[] = $product->product_id;
                            break;
                        case 'variation_id':
                            $row[] = (empty($product->variation_id) ? '' : $product->variation_id);
                            break;
                        case 'product_sku':
                            $row[] = get_post_meta($product->product_id, '_sku', true);
                            break;
                        case 'product_name':
                            $row[] = html_entity_decode(get_the_title($product->product_id));
                            break;
                        case 'quantity_sold':
                            $row[] = $product->quantity;
                            break;
                        case 'gross_sales':
                            $row[] = $product->gross;
                            break;
                        case 'gross_after_discount':
                            $row[] = $product->gross_after_discount;
                            break;
                        case 'product_categories':
                            $terms = get_the_terms($product->product_id, 'product_cat');
                            if (empty($terms)) {
                                $row[] = '';
                            } else {
                                $categories = array();
                                foreach ($terms as $term)
                                    $categories[] = $term->name;
                                $row[] = implode(', ', $categories);
                            }
                            break;
                    }
                }
            }

            if ($return)
                $rows[] = $row;
            else
                fputcsv($dest, $row);
        }
        if ($return)
            return $rows;
    }
}

function psarfw_filter_query_final_rounding($sql)
{
	$sql['select'] = preg_replace('/PSRSUM\\((.+)\\)/iU', 'SUM(ROUND($1, 2))', $sql['select']);
	return $sql;
}

function psarfw_is_hpos() {
	return method_exists('Automattic\WooCommerce\Utilities\OrderUtil', 'custom_orders_table_usage_is_enabled') && Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled();
}

function psarfw_report_order_statuses() {
    $wcOrderStatuses = wc_get_order_statuses();
    $orderStatuses = array();
    
    if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'psarfw_do_export' ) ) {
        $order_statuses = (isset($_POST['order_statuses'])) ? array_map(function($val) { return sanitize_text_field(wp_unslash($val)); },$_POST['order_statuses']) : array();

        if (!empty($order_statuses)) {
            foreach ($order_statuses as $orderStatus) {
                if (isset($wcOrderStatuses[$orderStatus]))
                    $orderStatuses[] = esc_sql(substr($orderStatus, 3));
            }
        }
    }
    return $orderStatuses;
}