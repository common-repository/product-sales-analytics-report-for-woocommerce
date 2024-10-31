<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Get Saved Settings
$savedReportSettings = get_option('psarfw_report_settings');

$reportSettings = (empty($savedReportSettings) ? psarfw_default_report_options() : array_merge(psarfw_default_report_options(), $savedReportSettings[0] ));

$fieldOptions = array(
    'product_id'           => esc_html__('Product ID', 'product-sales-analytics-reports-for-woocommerce'),
    'product_sku'          => esc_html__('Product SKU', 'product-sales-analytics-reports-for-woocommerce'),
    'product_name'         => esc_html__('Product Name', 'product-sales-analytics-reports-for-woocommerce'),
    'product_categories'   => esc_html__('Product Categories', 'product-sales-analytics-reports-for-woocommerce'),
    'quantity_sold'        => esc_html__('Quantity Sold', 'product-sales-analytics-reports-for-woocommerce'),
    'gross_sales'          => esc_html__('Gross Sales', 'product-sales-analytics-reports-for-woocommerce'),
    'gross_after_discount' => esc_html__('Gross Sales (After Discounts)', 'product-sales-analytics-reports-for-woocommerce')
);
?>
<div class="psarfw-main-box">
    <div class='psarfw-container'>
        <div class='psarfw-header'>
            <h1 class='psarfw-h1'>
                <?php esc_html_e( 'Product Sales Analytics Report for WooCommerce' , 'product-sales-analytics-reports-for-woocommerce' ); ?>
            </h1>
        </div>
        <div class="psarfw-option-section">
            <form action="#psarfw-report-table" method="post">
                <input type="hidden" name="psarfw_hidden_do_export" value="1" />
                <?php wp_nonce_field('psarfw_do_export'); ?>
                <div class="psarfw-tabbing-option">
                    <div class="psarfw-wrap-row align-items-center">
                        <div class="psarfw-col-lg-2">
                            <label class="psarfw-input-label"><?php esc_html_e('Report Period', 'product-sales-analytics-reports-for-woocommerce') ?>:</label>
                        </div>
                        <div class="psarfw-col-lg-10">
                            <select name="report_time" id="psarfw_sbp_field_report_time">
                                <option value="0d" <?php selected( $reportSettings['report_time'], '0d' ); ?>><?php esc_html_e('Today', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                                <option value="1d" <?php selected( $reportSettings['report_time'], '1d' ); ?>><?php esc_html_e('Yesterday', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                                <option value="7d" <?php selected( $reportSettings['report_time'], '7d' ); ?>><?php esc_html_e('Previous 7 days (excluding today)', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                                <option value="30d" <?php selected( $reportSettings['report_time'], '30d' ); ?>><?php esc_html_e('Previous 30 days (excluding today)', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                                <option value="0cm" <?php selected( $reportSettings['report_time'], '0cm' ); ?>><?php esc_html_e('Current calendar month', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                                <option value="1cm" <?php selected( $reportSettings['report_time'], '1cm' ); ?>><?php esc_html_e('Previous calendar month', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                                <option value="+7d" <?php selected( $reportSettings['report_time'], '+7d' ); ?>><?php esc_html_e('Next 7 days (future dated orders)', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                                <option value="+30d" <?php selected( $reportSettings['report_time'], '+30d' ); ?>><?php esc_html_e('Next 30 days (future dated orders)', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                                <option value="+1cm" <?php selected( $reportSettings['report_time'], '+1cm' ); ?>><?php esc_html_e('Next calendar month (future dated orders)', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                                <option value="all" <?php selected( $reportSettings['report_time'], 'all' ); ?>><?php esc_html_e('All time', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                                <option value="custom" <?php selected( $reportSettings['report_time'], 'custom' ); ?>><?php esc_html_e('Custom date range', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="psarfw-wrap-row align-items-center psarfw_sbp_custom_time">
                        <div class="psarfw-col-lg-2">
                            <label for="psarfw_sbp_field_report_start" class="psarfw-input-label"><?php esc_html_e('Start Date', 'product-sales-analytics-reports-for-woocommerce') ?>:</label>
                        </div>
                        <div class="psarfw-col-lg-10">
                                <input type="date" name="report_start" id="psarfw_sbp_field_report_start" value="<?php echo esc_attr(strval($reportSettings['report_start'])); ?>" />
                            
                        </div>
                    </div>
                
                    <div class="psarfw-wrap-row align-items-center psarfw_sbp_custom_time">
                        <div class="psarfw-col-lg-2">
                            <label for="psarfw_sbp_field_report_end" class="psarfw-input-label"><?php esc_html_e('End Date', 'product-sales-analytics-reports-for-woocommerce') ?>:</label>
                        </div>
                        <div class="psarfw-col-lg-10">
                                <input type="date" name="report_end" id="psarfw_sbp_field_report_end" value="<?php echo esc_attr(strval($reportSettings['report_end'])); ?>" />
                            
                        </div>
                    </div>

                    <div class="psarfw-wrap-row psarfw-settings-cb-list">
                        <div class="psarfw-col-lg-2">
                            <label class="psarfw-input-label"><?php esc_html_e( 'Include Orders With Status' , 'product-sales-analytics-reports-for-woocommerce') ?>:</label>
                        </div>
                        <div class="psarfw-col-lg-10">
                            <div class="psarfw-orders-status-settings">
                                <?php
                                foreach (wc_get_order_statuses() as $status => $statusName) {
                                    ?>
                                    <div class="psarfw-checkbox-group">
                                        <input class="psarfw-checkbox-input" id="wp_<?php echo esc_attr($status); ?>" type="checkbox" name="order_statuses[]" value="<?php echo esc_attr(strval($status)); ?>" <?php checked(in_array($status,$reportSettings['order_statuses'])); ?> />
                                        <label for="wp_<?php echo esc_attr($status); ?>" class="psarfw-checkbox-label"><?php echo esc_html($statusName); ?></label>
                                    </div>
                                    <?php
                                } ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="psarfw-wrap-row psarfw-settings-cb-list">
                        <div class="psarfw-col-lg-2">
                            <label class="psarfw-input-label"><?php esc_html_e('Include Products', 'product-sales-analytics-reports-for-woocommerce') ?>:</label>
                        </div>
                        <div class="psarfw-col-lg-10">
                            <div class="psarfw-include-products-settings">
                                <label class="psarfw-radio-label">
                                    <input type="radio" name="products" value="all" <?php checked($reportSettings['products'], "all"); ?> /><?php esc_html_e('All products', 'product-sales-analytics-reports-for-woocommerce') ?>
                                </label>
                                <label class="psarfw-radio-label">
                                <input type="radio" name="products" value="cats" <?php checked($reportSettings['products'], "cats"); ?> /><?php esc_html_e('Products in categories', 'product-sales-analytics-reports-for-woocommerce') ?>:
                                </label>
                                <div class="psarfw-input-child-box">
                                    <ul id="psarfw-psr-product-cats">
                                        <?php
                                        foreach (get_terms('product_cat') as $term) {
                                            echo sprintf(
                                                '<li><div class="psarfw-checkbox-group"><input class="psarfw-checkbox-input" id="term_%1$d" type="checkbox" name="product_cats[]" value="%1$d" %2$s /> <label for="term_%1$d" class="psarfw-checkbox-label">%3$s</label></div></li>',
                                                esc_attr($term->term_id),
                                                checked(in_array($term->term_id, $reportSettings['product_cats']), $term->term_id, false),
                                                esc_html($term->name)
                                            );                                            
                                        } 
                                        ?>
                                    </ul>
                                </div>
                                <label class="psarfw-radio-label">
                                    <input type="radio" name="products" value="ids" <?php checked($reportSettings['products'], "ids"); ?> /><?php esc_html_e('Product ID(s)', 'product-sales-analytics-reports-for-woocommerce') ?>:
                                </label> 
                                <div class="psarfw-pro-id-box">
                                    <input type="text" name="product_ids" placeholder="<?php esc_html_e('Use commas to separate multiple product IDs', 'product-sales-analytics-reports-for-woocommerce') ?>" value="<?php echo esc_attr($reportSettings['product_ids']); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="psarfw-wrap-row">
                        <div class="psarfw-col-lg-2">
                            <label class="psarfw-input-label"><?php esc_html_e('Product Variations', 'product-sales-analytics-reports-for-woocommerce') ?>:</label>
                        </div>
                        <div class="psarfw-col-lg-10">
                            <div class="psarfw-orders-status-settings">
                                <div class="psarfw-checkbox-group">
                                    <input class="psarfw-checkbox-input" id="variations_field" type="checkbox" name="variations" value="0" <?php checked($reportSettings['variations'], 0); ?>> 
                                    <label for="variations_field" class="psarfw-checkbox-label">
                                        <?php esc_html_e('Group product variations together', 'product-sales-analytics-reports-for-woocommerce') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="psarfw-wrap-row">
                        <div class="psarfw-col-lg-2">
                            <label for="psarfw_sbp_field_orderby" class="psarfw-input-label"><?php esc_html_e('Sort By', 'product-sales-analytics-reports-for-woocommerce') ?>:</label>
                        </div>
                        <div class="psarfw-col-lg-10">
                            <div class="psarfw-sort-by-settings">
                                <select name="orderby" id="psarfw_sbp_field_orderby">
                                    <option value="product_id" <?php selected( $reportSettings['orderby'], 'product_id' ); ?>><?php esc_html_e('Product ID', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                                    <option value="quantity" <?php selected( $reportSettings['orderby'], 'quantity' ); ?>><?php esc_html_e('Quantity Sold', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                                    <option value="gross" <?php selected( $reportSettings['orderby'], 'gross' ); ?>><?php esc_html_e('Gross Sales', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                                    <option value="gross_after_discount" <?php selected( $reportSettings['report_time'], 'orderby' ); ?>><?php esc_html_e('Gross Sales (After Discounts)', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                                </select>
                                <select name="orderdir" id="psarfw_sbp_field_orderdir">
                                    <option value="asc" <?php selected( $reportSettings['orderdir'], 'asc' ); ?>><?php esc_html_e('Ascending', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                                    <option value="desc" <?php selected( $reportSettings['orderdir'], 'desc' ); ?>><?php esc_html_e('Descending', 'product-sales-analytics-reports-for-woocommerce') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="psarfw-wrap-row">
                        <div class="psarfw-col-lg-2">
                            <label for="psarfw_sbp_field_orderby" class="psarfw-input-label"><?php esc_html_e('Report Fields', 'product-sales-analytics-reports-for-woocommerce') ?>:</label>
                        </div>
                        <div class="psarfw-col-lg-10">
                            <div class="psarfw-orders-status-settings">
                                <?php
                                foreach ($reportSettings['fields'] as $fieldId) {
                                    if (!isset($fieldOptions[$fieldId]))
                                        continue;
                                    ?>
                                    <div class="psarfw-checkbox-group">
                                        <input class="psarfw-checkbox-input" id="<?php echo esc_attr($fieldId); ?>" type="checkbox" name="fields[]" value="<?php echo esc_attr($fieldId); ?>" checked> 
                                        <label for="<?php echo esc_attr($fieldId); ?>" class="psarfw-checkbox-label"><?php echo esc_html($fieldOptions[$fieldId]); ?></label>
                                    </div>
                                    <?php
                                    unset($fieldOptions[$fieldId]);
                                }
                                foreach ($fieldOptions as $fieldId => $fieldDisplay) {
                                    ?>
                                    <div class="psarfw-checkbox-group">
                                        <input class="psarfw-checkbox-input" id="<?php echo esc_attr($fieldId); ?>" type="checkbox" name="fields[]" value="<?php echo esc_attr($fieldId); ?>"> 
                                        <label for="<?php echo esc_attr($fieldId); ?>" class="psarfw-checkbox-label"><?php echo esc_html($fieldOptions[$fieldId]); ?></label>
                                    </div>
                                    <?php
                                }
                                unset($fieldOptions);
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="psarfw-wrap-row">
                        <div class="psarfw-col-lg-2">
                            <label for="psarfw_sbp_field_orderby" class="psarfw-input-label"><?php esc_html_e('Exclude free products', 'product-sales-analytics-reports-for-woocommerce') ?>:</label>
                        </div>
                        <div class="psarfw-col-lg-10">
                            <div class="psarfw-orders-status-settings">
                                <div class="psarfw-checkbox-group">
                                    <input class="psarfw-checkbox-input" id="exclude_free_products" type="checkbox" name="exclude_free" <?php checked(!empty($reportSettings['exclude_free'])); ?>> 
                                    <label for="exclude_free_products" class="psarfw-checkbox-label"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="psarfw-wrap-row align-items-center">
                        <div class="psarfw-col-lg-2">
                            <label for="psarfw_sbp_field_orderby" class="psarfw-input-label"><?php esc_html_e('Products number', 'product-sales-analytics-reports-for-woocommerce') ?>:</label>
                        </div>
                        <div class="psarfw-col-lg-10">
                            <div class="psarfw-orders-status-settings psarfw-product-no-box">
                                <div class="psarfw-checkbox-group">
                                    <input class="psarfw-checkbox-input" id="product_number_show" type="checkbox" name="limit_on" <?php checked(!empty($reportSettings['limit_on'])); ?>> 
                                    <label for="product_number_show" class="psarfw-checkbox-label"><?php esc_html_e('Show only the first', 'product-sales-analytics-reports-for-woocommerce') ?></label>
                                </div>
                                <div class="psarfw-pro-no-input">
                                    <input type="number" name="limit" id="psarfw_limit_number" value="<?php echo intval($reportSettings['limit']); ?>" min="0" step="1"><span><?php esc_html_e('Products', 'product-sales-analytics-reports-for-woocommerce') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="psarfw-wrap-row">
                        <div class="psarfw-col-lg-2">
                            <label for="psarfw_sbp_field_orderby" class="psarfw-input-label"><?php esc_html_e('Intermediate rounding', 'product-sales-analytics-reports-for-woocommerce') ?>:</label>
                        </div>
                        <div class="psarfw-col-lg-10">
                            <div class="psarfw-orders-status-settings">
                                <div class="psarfw-checkbox-group">
                                    <input class="psarfw-checkbox-input" id="intermediate_rounding_ck" type="checkbox" name="intermediate_rounding" value="2" <?php checked(!empty($reportSettings['intermediate_rounding'])); ?>> 
                                    <label for="intermediate_rounding_ck" class="psarfw-checkbox-label"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="psarfw-wrap-row">
                        <div class="psarfw-col-lg-2">
                            <label for="psarfw_sbp_field_orderby" class="psarfw-input-label"><?php esc_html_e('Include header row', 'product-sales-analytics-reports-for-woocommerce') ?>:</label>
                        </div>
                        <div class="psarfw-col-lg-10">
                            <div class="psarfw-orders-status-settings">
                                <div class="psarfw-checkbox-group">
                                    <input class="psarfw-checkbox-input" id="include_header_ck" type="checkbox" name="include_header" <?php checked(!empty($reportSettings['include_header'])); ?>>
                                    <label for="include_header_ck" class="psarfw-checkbox-label"></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="psarfw-wrap-row">
                        <div class="psarfw-col-lg-2">
                            <label for="psarfw_sbp_field_orderby" class="psarfw-input-label"><?php esc_html_e('Enable debug mode', 'product-sales-analytics-reports-for-woocommerce') ?>:</label>
                        </div>
                        <div class="psarfw-col-lg-10">
                            <div class="psarfw-orders-status-settings">
                                <div class="psarfw-checkbox-group">
                                    <input class="psarfw-checkbox-input" id="debug_mode" type="checkbox" name="psarfw_debug" value="1" <?php checked(!empty($reportSettings['psarfw_debug'])); ?>> 
                                    <label for="debug_mode" class="psarfw-checkbox-label"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="psarfw-wrap-row psarfw-btn-box">
                        <button type="submit" class="psarfw-btn" onclick="jQuery(this).closest(\'form\').attr(\'target\', \'\'); return true;"><?php esc_html_e('View Report', 'product-sales-analytics-reports-for-woocommerce'); ?></button>
                        <button type="submit" class="psarfw-btn psarfw-white-btn" name="psarfw_sbp_download" value="1" onclick="jQuery(this).closest(\'form\').attr(\'target\', \'_blank\'); return true;"><?php esc_html_e('Download Report as CSV', 'product-sales-analytics-reports-for-woocommerce'); ?></button>
                    </div>
                </div>
            </form>
            <?php
            if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'psarfw_do_export' ) ) {
                if (!empty($_POST['psarfw_hidden_do_export']) && !empty($_POST['fields'])) {
                    ?>
                        <table id="psarfw-report-table">
                            <?php
                            if (!empty($_POST['include_header'])) { ?>
                                <thead>
                                    <tr>
                                        <?php
                                            foreach (psarfw_header_export(null, true) as $rowItem)
                                                echo sprintf('<th>%s</th>', esc_html($rowItem));
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach (psarfw_body_export(null, true) as $row) { ?>
                                        <tr>
                                            <?php
                                            foreach ($row as $rowItem) {
                                                echo sprintf('<td>%s</td>', esc_html($rowItem));
                                            } ?>
                                        </tr>
                                        <?php
                                    } ?>
                                </tbody>
                                <?php
                            }
                            ?>
                        </table>
                    <?php
                }
            }
            ?>
        </div>
    </div>
</div>