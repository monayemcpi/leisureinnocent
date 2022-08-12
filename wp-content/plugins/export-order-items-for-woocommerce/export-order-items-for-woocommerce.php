<?php
/**
 * Plugin Name:       Export Order Items for WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/export-order-items-for-woocommerce/
 * Description:       Export order items (products ordered) in CSV (Comma Seperated Values) format, with product, line item, order, and customer data.
 * Version:           1.0.10
 * WC tested up to:   5.0.0
 * Author:            Aspen Grove Studios
 * Author URI:        http://aspengrovestudios.com/?utm_source=export-order-items-for-woocommerce&utm_medium=link&utm_campaign=wp-plugin-credit-link
 * License:           GNU General Public License version 3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain:       export-order-items-for-woocommerce
 * Domain Path:       /languages
 * GitLab Theme URI:  https://gitlab.com/aspengrovestudios/export-order-items-for-woocommerce
 */

/*
    Export Order Items for WooCommerce
    Copyright (C) 2021  Aspen Grove Studios

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <https://www.gnu.org/licenses/>.
*/

/* CREDITS:
 * This plugin contains code copied from and/or based on the following third-party products,
 * in addition to any others indicated in code comments or license files:
 *
 * WordPress, by Automattic, GPLv2+
 * WooCommerce, by Automattic, GPLv3+
 *
 * See licensing and copyright information in the ./license directory.
 *
 * This file was modified by Jonathan Hall, Dominika Rauk, Anna Kurowska and/or others.
 * Last modified 2021-02-15.
*/

load_plugin_textdomain('export-order-items-for-woocommerce', false, __DIR__ . '/languages');

// Add Export Order Items to the WordPress admin
add_action('admin_menu', 'hm_xoiwc_admin_menu');
function hm_xoiwc_admin_menu() {
    add_submenu_page('woocommerce', 'Export Order Items', 'Export Order Items', 'view_woocommerce_reports', 'hm_xoiwc', 'hm_xoiwc_page');
}

function hm_xoiwc_default_report_settings() {
    return array(
        'report_time'    => '30d',
        'report_start'   => date('Y-m-d', current_time('timestamp') - (86400 * 31)),
        'report_end'     => date('Y-m-d', current_time('timestamp') - 86400),
        'order_statuses' => array('wc-processing', 'wc-on-hold', 'wc-completed'),
        'orderby'        => 'order_id',
        'orderdir'       => 'asc',
        'fields'         => array('product_id', 'product_name', 'quantity', 'order_date', 'billing_name', 'billing_email'),
        'include_header' => 1
    );
}

// This function generates the Export Order Items page HTML
function hm_xoiwc_page() {

    $savedReportSettings = get_option('hm_xoiwc_report_settings');

    $reportSettings = (empty($savedReportSettings) ?
        hm_xoiwc_default_report_settings() :
        array_merge(hm_xoiwc_default_report_settings(),
            $savedReportSettings[0]
        ));

    $fieldOptions = array(
        'product_id'         => esc_html__('Product ID', 'export-order-items-for-woocommerce'),
        'product_sku'        => esc_html__('Product SKU', 'export-order-items-for-woocommerce'),
        'product_name'       => esc_html__('Product Name', 'export-order-items-for-woocommerce'),
        'product_categories' => esc_html__('Product Categories', 'export-order-items-for-woocommerce'),
        'order_id'           => esc_html__('Order ID', 'export-order-items-for-woocommerce'),
        'order_status'       => esc_html__('Order Status', 'export-order-items-for-woocommerce'),
        'order_date'         => esc_html__('Order Date/Time', 'export-order-items-for-woocommerce'),
        'quantity'           => esc_html__('Line Item Quantity', 'export-order-items-for-woocommerce'),
        'line_subtotal'      => esc_html__('Line Item Gross', 'export-order-items-for-woocommerce'),
        'line_total'         => esc_html__('Line Item Gross After Discounts', 'export-order-items-for-woocommerce'),
        'billing_name'       => esc_html__('Billing Name', 'export-order-items-for-woocommerce'),
        'billing_phone'      => esc_html__('Billing Phone', 'export-order-items-for-woocommerce'),
        'billing_email'      => esc_html__('Billing Email', 'export-order-items-for-woocommerce'),
        'billing_address'    => esc_html__('Billing Address', 'export-order-items-for-woocommerce'),
        'shipping_name'      => esc_html__('Shipping Name', 'export-order-items-for-woocommerce'),
        'shipping_phone'     => esc_html__('Shipping Phone', 'export-order-items-for-woocommerce'),
        'shipping_email'     => esc_html__('Shipping Email', 'export-order-items-for-woocommerce'),
        'shipping_address'   => esc_html__('Shipping Address', 'export-order-items-for-woocommerce'),
    );

    include(dirname(__FILE__) . '/admin.php');
}

function hm_xoiwc_filter_nocache_headers($headers) {
	// Reference: https://owasp.org/www-community/OWASP_Application_Security_FAQ
	
	$cacheControl = array_map( 'trim', explode(',', $headers['Cache-Control']) );
	$cacheControl = array_unique( array_merge( [
		'no-cache',
		'no-store',
		'must-revalidate',
		'pre-check=0',
		'post-check=0',
		'max-age=0',
		's-maxage=0'
	], $cacheControl ) );
	
	$headers['Cache-Control'] = implode(', ', $cacheControl);
	$headers['Pragma'] = 'no-cache';
	
	return $headers;
}

// Hook into WordPress init; this function performs report generation when
// the admin form is submitted
add_action('init', 'hm_xoiwc_on_init');
function hm_xoiwc_on_init() {
    global $pagenow;

    // Check if we are in admin and on the report page
    if (!is_admin())
        return;
    if ($pagenow == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'hm_xoiwc') {
		
		add_filter('nocache_headers', 'hm_xoiwc_filter_nocache_headers', 9999);
		nocache_headers();
		
		if (current_user_can('view_woocommerce_reports') && !empty($_POST['hm_xoiwc_do_export'])) {

			// Verify the nonce
			check_admin_referer('hm_xoiwc_do_export');

			$newSettings = array_intersect_key($_POST, hm_xoiwc_default_report_settings());
			foreach ($newSettings as $key => $value)
				if (!is_array($value))
					$newSettings[$key] = htmlspecialchars($value);

			// Update the saved report settings
			$savedReportSettings = get_option('hm_xoiwc_report_settings');
			$savedReportSettings[0] = array_merge(hm_xoiwc_default_report_settings(), $newSettings);

			update_option('hm_xoiwc_report_settings', $savedReportSettings);

			// Check if no fields are selected
			if (empty($_POST['fields']))
				return;

			// Assemble the filename for the report download
			$filename = 'Order Items Export - ';
			if (!empty($_POST['cat']) && is_numeric($_POST['cat'])) {
				$cat = get_term($_POST['cat'], 'product_cat');
				if (!empty($cat->name))
					$filename .= addslashes(html_entity_decode($cat->name)) . ' - ';
			}
			$filename .= date('Y-m-d', current_time('timestamp')) . '.csv';

			// Send headers
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename="' . $filename . '"');

			// Output the report header row (if applicable) and body
			$stdout = fopen('php://output', 'w');
			if (!empty($_POST['include_header']))
				hm_xoiwc_export_header($stdout);
			hm_xoiwc_export_body($stdout);

			exit;
		}
    }
}

// divi-switch\functions.php
add_action('load-plugins.php', 'hm_xoiwc_export_onLoadPluginsPhp');

function hm_xoiwc_export_onLoadPluginsPhp() {
    add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'hm_xoiwc_export_pluginActionLinks');
}

function hm_xoiwc_export_pluginActionLinks($links) {
    array_unshift($links, '<a href="admin.php?page=hm_xoiwc">'.esc_html__('Settings', 'export-order-items-for-woocommerce').'</a>');
    return $links;
}


// This function outputs the report header row
function hm_xoiwc_export_header($dest, $return = false) {
    $header = array();

    foreach ($_POST['fields'] as $field) {
        switch ($field) {
            case 'product_id':
                $header[] = esc_html__('Product ID', 'export-order-items-for-woocommerce');
                break;
            case 'order_id':
                $header[] = esc_html__('Order ID', 'export-order-items-for-woocommerce');
                break;
            case 'order_status':
                $header[] = esc_html__('Order Status', 'export-order-items-for-woocommerce');
                break;
            case 'order_date':
                $header[] = esc_html__('Order Date/Time', 'export-order-items-for-woocommerce');
                break;
            case 'product_sku':
                $header[] = esc_html__('Product SKU', 'export-order-items-for-woocommerce');
                break;
            case 'product_name':
                $header[] = esc_html__('Product Name', 'export-order-items-for-woocommerce');
                break;
            case 'product_categories':
                $header[] = esc_html__('Product Categories', 'export-order-items-for-woocommerce');
                break;
            case 'billing_name':
                $header[] = esc_html__('Billing Name', 'export-order-items-for-woocommerce');
                break;
            case 'billing_phone':
                $header[] = esc_html__('Billing Phone', 'export-order-items-for-woocommerce');
                break;
            case 'billing_email':
                $header[] = esc_html__('Billing Email', 'export-order-items-for-woocommerce');
                break;
            case 'billing_address':
                $header[] = esc_html__('Billing Address', 'export-order-items-for-woocommerce');
                break;
            case 'shipping_name':
                $header[] = esc_html__('Shipping Name', 'export-order-items-for-woocommerce');
                break;
            case 'shipping_phone':
                $header[] = esc_html__('Shipping Phone', 'export-order-items-for-woocommerce');
                break;
            case 'shipping_email':
                $header[] = esc_html__('Shipping Email', 'export-order-items-for-woocommerce');
                break;
            case 'shipping_address':
                $header[] = esc_html__('Shipping Address', 'export-order-items-for-woocommerce');
                break;
            case 'quantity':
                $header[] = esc_html__('Line Item Quantity', 'export-order-items-for-woocommerce');
                break;
            case 'line_subtotal':
                $header[] = esc_html__('Line Item Gross', 'export-order-items-for-woocommerce');
                break;
            case 'line_total':
                $header[] = esc_html__('Line Item Gross After Discounts', 'export-order-items-for-woocommerce');
                break;
        }
    }

    if ($return)
        return $header;
    fputcsv($dest, $header);
}

// This function generates and outputs the report body rows
function hm_xoiwc_export_body($dest, $return = false) {
    global $woocommerce, $wpdb;

    // Calculate report start and end dates (timestamps)
    switch ($_POST['report_time']) {
        case '0d':
            $end_date = strtotime('midnight', current_time('timestamp'));
            $start_date = $end_date;
            break;
        case '1d':
            $end_date = strtotime('midnight', current_time('timestamp')) - 86400;
            $start_date = $end_date;
            break;
        case '7d':
            $end_date = strtotime('midnight', current_time('timestamp')) - 86400;
            $start_date = $end_date - (86400 * 6);
            break;
        case '1cm':
            $start_date = strtotime(date('Y-m', current_time('timestamp')) . '-01 midnight -1month');
            $end_date = strtotime('+1month', $start_date) - 86400;
            break;
        case '0cm':
            $start_date = strtotime(date('Y-m', current_time('timestamp')) . '-01 midnight');
            $end_date = strtotime('+1month', $start_date) - 86400;
            break;
        case '+1cm':
            $start_date = strtotime(date('Y-m', current_time('timestamp')) . '-01 midnight +1month');
            $end_date = strtotime('+1month', $start_date) - 86400;
            break;
        case '+7d':
            $start_date = strtotime('midnight', current_time('timestamp')) + 86400;
            $end_date = $start_date + (86400 * 6);
            break;
        case '+30d':
            $start_date = strtotime('midnight', current_time('timestamp')) + 86400;
            $end_date = $start_date + (86400 * 29);
            break;
        case 'custom':
            $end_date = strtotime('midnight', strtotime($_POST['report_end']));
            $start_date = strtotime('midnight', strtotime($_POST['report_start']));
            break;
        default: // 30 days is the default
            $end_date = strtotime('midnight', current_time('timestamp')) - 86400;
            $start_date = $end_date - (86400 * 29);
    }

    // Assemble order by string
    $orderby = (in_array($_POST['orderby'], array('order_id')) ? $_POST['orderby'] : 'product_id');
    $orderby .= ' ' . ($_POST['orderdir'] == 'asc' ? 'ASC' : 'DESC');

    // Create a new WC_Admin_Report object
    include_once($woocommerce->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php');
    $wc_report = new WC_Admin_Report();
    $wc_report->start_date = $start_date;
    $wc_report->end_date = $end_date;

    // Get report data
    $reportData = array(
        '_product_id' => array(
            'type'            => 'order_item_meta',
            'order_item_type' => 'line_item',
            'function'        => '',
            'name'            => 'product_id'
        ),
        'order_id'    => array(
            'type'            => 'order_item',
            'order_item_type' => 'line_item',
            'function'        => '',
            'name'            => 'order_id'
        )
    );

    if (in_array('quantity', $_POST['fields'])) {
        $reportData['_qty'] = array(
            'type'            => 'order_item_meta',
            'order_item_type' => 'line_item',
            'function'        => '',
            'name'            => 'quantity'
        );
    }
    if (in_array('line_subtotal', $_POST['fields'])) {
        $reportData['_line_subtotal'] = array(
            'type'            => 'order_item_meta',
            'order_item_type' => 'line_item',
            'function'        => '',
            'name'            => 'line_subtotal'
        );
    }

    if (in_array('line_total', $_POST['fields'])) {
        $reportData['_line_total'] = array(
            'type'            => 'order_item_meta',
            'order_item_type' => 'line_item',
            'function'        => '',
            'name'            => 'line_total'
        );
    }

    if (in_array('order_status', $_POST['fields'])) {
        $reportData['post_status'] = array(
            'type'     => 'post_data',
            'name'     => 'order_status',
            'function' => '',
        );
    }
    if (in_array('order_date', $_POST['fields'])) {
        $reportData['post_date'] = array(
            'type'     => 'post_data',
            'name'     => 'order_date',
            'function' => '',
        );
    }
    if (in_array('billing_name', $_POST['fields'])) {
        $reportData['_billing_first_name'] = array(
            'type'      => 'meta',
            'name'      => 'billing_first_name',
            'join_type' => 'LEFT',
            'function'  => '',
        );
        $reportData['_billing_last_name'] = array(
            'type'      => 'meta',
            'name'      => 'billing_last_name',
            'join_type' => 'LEFT',
            'function'  => '',
        );
    }
    if (in_array('billing_phone', $_POST['fields'])) {
        $reportData['_billing_phone'] = array(
            'type'      => 'meta',
            'name'      => 'billing_phone',
            'join_type' => 'LEFT',
            'function'  => '',
        );
    }
    if (in_array('billing_email', $_POST['fields'])) {
        $reportData['_billing_email'] = array(
            'type'      => 'meta',
            'name'      => 'billing_email',
            'join_type' => 'LEFT',
            'function'  => '',
        );
    }
    if (in_array('billing_address', $_POST['fields'])) {
        $reportData['_billing_address_1'] = array(
            'type'      => 'meta',
            'name'      => 'billing_address_1',
            'join_type' => 'LEFT',
            'function'  => '',
        );
        $reportData['_billing_address_2'] = array(
            'type'      => 'meta',
            'name'      => 'billing_address_2',
            'join_type' => 'LEFT',
            'function'  => '',
        );
        $reportData['_billing_city'] = array(
            'type'      => 'meta',
            'name'      => 'billing_city',
            'join_type' => 'LEFT',
            'function'  => '',
        );
        $reportData['_billing_state'] = array(
            'type'      => 'meta',
            'name'      => 'billing_state',
            'join_type' => 'LEFT',
            'function'  => '',
        );
        $reportData['_billing_postcode'] = array(
            'type'      => 'meta',
            'name'      => 'billing_postcode',
            'join_type' => 'LEFT',
            'function'  => '',
        );
        $reportData['_billing_country'] = array(
            'type'      => 'meta',
            'name'      => 'billing_country',
            'join_type' => 'LEFT',
            'function'  => '',
        );
    }
    if (in_array('shipping_name', $_POST['fields'])) {
        $reportData['_shipping_first_name'] = array(
            'type'      => 'meta',
            'name'      => 'shipping_first_name',
            'join_type' => 'LEFT',
            'function'  => '',
        );
        $reportData['_shipping_last_name'] = array(
            'type'      => 'meta',
            'name'      => 'shipping_last_name',
            'join_type' => 'LEFT',
            'function'  => '',
        );
    }
    if (in_array('shipping_phone', $_POST['fields'])) {
        $reportData['_shipping_phone'] = array(
            'type'      => 'meta',
            'name'      => 'shipping_phone',
            'join_type' => 'LEFT',
            'function'  => '',
        );
    }
    if (in_array('shipping_email', $_POST['fields'])) {
        $reportData['_shipping_email'] = array(
            'type'      => 'meta',
            'name'      => 'shipping_email',
            'join_type' => 'LEFT',
            'function'  => '',
        );
    }
    if (in_array('shipping_address', $_POST['fields'])) {
        $reportData['_shipping_address_1'] = array(
            'type'      => 'meta',
            'name'      => 'shipping_address_1',
            'join_type' => 'LEFT',
            'function'  => '',
        );
        $reportData['_shipping_address_2'] = array(
            'type'      => 'meta',
            'name'      => 'shipping_address_2',
            'join_type' => 'LEFT',
            'function'  => '',
        );
        $reportData['_shipping_city'] = array(
            'type'      => 'meta',
            'name'      => 'shipping_city',
            'join_type' => 'LEFT',
            'function'  => '',
        );
        $reportData['_shipping_state'] = array(
            'type'      => 'meta',
            'name'      => 'shipping_state',
            'join_type' => 'LEFT',
            'function'  => '',
        );
        $reportData['_shipping_postcode'] = array(
            'type'      => 'meta',
            'name'      => 'shipping_postcode',
            'join_type' => 'LEFT',
            'function'  => '',
        );
        $reportData['_shipping_country'] = array(
            'type'      => 'meta',
            'name'      => 'shipping_country',
            'join_type' => 'LEFT',
            'function'  => '',
        );
    }

    // Avoid max join size error
    $wpdb->query('SET SQL_BIG_SELECTS=1');

    // Prevent plugins from overriding the order status filter
    add_filter('woocommerce_reports_order_statuses', 'hm_xoiwc_report_order_statuses', 9999);

    // Based on woocoommerce/includes/admin/reports/class-wc-report-sales-by-product.php
    $sold_products = $wc_report->get_order_report_data(array(
        'data'         => $reportData,
        'query_type'   => 'get_results',
        'group_by'     => '',
        'order_by'     => $orderby,
        'limit'        => (!empty($_POST['limit_on']) && is_numeric($_POST['limit']) ? $_POST['limit'] : ''),
        'filter_range' => ($_POST['report_time'] != 'all'),
        'order_types'  => wc_get_order_types('order_count'),
        'order_status' => hm_xoiwc_report_order_statuses()
    ));

    // Remove report order statuses filter
    remove_filter('woocommerce_reports_order_statuses', 'hm_xoiwc_report_order_statuses', 9999);

    // Output report rows
    foreach ($sold_products as $product) {
        $row = array();

        foreach ($_POST['fields'] as $field) {
            switch ($field) {
                case 'product_id':
                    $row[] = $product->product_id;
                    break;
                case 'order_id':
                    $row[] = $product->order_id;
                    break;
                case 'order_status':
                    $row[] = wc_get_order_status_name($product->order_status);
                    break;
                case 'order_date':
                    $row[] = $product->order_date;
                    break;
                case 'product_sku':
                    $row[] = get_post_meta($product->product_id, '_sku', true);
                    break;
                case 'product_name':
                    $row[] = html_entity_decode(get_the_title($product->product_id));
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
                case 'billing_name':
                    $row[] = $product->billing_first_name . ' ' . $product->billing_last_name;
                    break;
                case 'billing_phone':
                    $row[] = $product->billing_phone;
                    break;
                case 'billing_email':
                    $row[] = $product->billing_email;
                    break;
                case 'billing_address':
                    $addressComponents = array();
                    if (!empty($product->billing_address_1))
                        $addressComponents[] = $product->billing_address_1;
                    if (!empty($product->billing_address_2))
                        $addressComponents[] = $product->billing_address_2;
                    if (!empty($product->billing_city))
                        $addressComponents[] = $product->billing_city;
                    if (!empty($product->billing_state))
                        $addressComponents[] = $product->billing_state;
                    if (!empty($product->billing_postcode))
                        $addressComponents[] = $product->billing_postcode;
                    if (!empty($product->billing_country))
                        $addressComponents[] = $product->billing_country;
                    $row[] = implode(', ', $addressComponents);
                    break;
                case 'shipping_name':
                    $row[] = $product->shipping_first_name . ' ' . $product->shipping_last_name;
                    break;
                case 'shipping_phone':
                    $row[] = $product->shipping_phone;
                    break;
                case 'shipping_email':
                    $row[] = $product->shipping_email;
                    break;
                case 'shipping_address':
                    $addressComponents = array();
                    if (!empty($product->shipping_address_1))
                        $addressComponents[] = $product->shipping_address_1;
                    if (!empty($product->shipping_address_2))
                        $addressComponents[] = $product->shipping_address_2;
                    if (!empty($product->shipping_city))
                        $addressComponents[] = $product->shipping_city;
                    if (!empty($product->shipping_state))
                        $addressComponents[] = $product->shipping_state;
                    if (!empty($product->shipping_postcode))
                        $addressComponents[] = $product->shipping_postcode;
                    if (!empty($product->shipping_country))
                        $addressComponents[] = $product->shipping_country;
                    $row[] = implode(', ', $addressComponents);
                    break;
                case 'quantity':
                    $row[] = $product->quantity;
                    break;
                case 'line_subtotal':
                    $row[] = $product->line_subtotal;
                    break;
                case 'line_total':
                    $row[] = $product->line_total;
                    break;
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

add_action('admin_enqueue_scripts', 'hm_xoiwc_admin_enqueue_scripts');
function hm_xoiwc_admin_enqueue_scripts() {

    if ( isset( $_GET["page"] ) &&  $_GET["page"] == "hm_xoiwc" ) {
        wp_enqueue_style('hm_xoiwc_admin_style', plugins_url('css/export-order-items.css', __FILE__));
        wp_enqueue_style('ags-xoiwc-addons-admin', plugins_url('addons/css/admin.css', __FILE__));
        wp_enqueue_style('pikaday', plugins_url('css/pikaday.css', __FILE__));
        wp_enqueue_script('moment', plugins_url('js/moment.min.js', __FILE__));
        wp_enqueue_script('pikaday', plugins_url('js/pikaday.js', __FILE__));
    }
}

// Schedulable email report hook
add_filter('pp_wc_get_schedulable_email_reports', 'hm_xoiwc_add_schedulable_email_reports');
function hm_xoiwc_add_schedulable_email_reports($reports) {
    $reports['hm_xoiwc'] = array(
        'name'     => esc_html__('Export Order Items', 'export-order-items-for-woocommerce'),
        'callback' => 'hm_xoiwc_run_scheduled_report',
        'reports'  => array(
            'last' => esc_html__('Last used settings', 'export-order-items-for-woocommerce')
        )
    );
    return $reports;
}

function hm_xoiwc_run_scheduled_report($reportId, $start, $end, $args = array(), $output = false) {
    $savedReportSettings = get_option('hm_xoiwc_report_settings');
    if (!isset($savedReportSettings[0]))
        return false;
    $prevPost = $_POST;
    $_POST = $savedReportSettings[0];
    $_POST['report_time'] = 'custom';
    $_POST['report_start'] = date('Y-m-d', $start);
    $_POST['report_end'] = date('Y-m-d', $end);
    $_POST = array_merge($_POST, array_intersect_key($args, $_POST));

    if ($output) {
        echo('<table><thead><tr>');
        foreach (hm_xoiwc_export_header(null, true) as $heading) {
            echo("<th>$heading</th>");
        }
        echo('</tr></thead><tbody>');
        foreach (hm_xoiwc_export_body(null, true) as $row) {
            echo('<tr>');
            foreach ($row as $cell)
                echo('<td>' . htmlspecialchars($cell) . '</td>');
            echo('</tr>');
        }
        echo('</tbody></table>');
        $_POST = $prevPost;
        return;
    }

    // hm-export-order-items-pro\hm-export-order-items-pro.php
    if (!function_exists('random_bytes')) {
        return false;
    }

    $tempDir = WP_CONTENT_DIR . '/potent-temp/' . sha1(random_bytes(256));
    if (!@mkdir($tempDir, 0755, true)) {
        return false;
    }

    $filename = $tempDir . '/Order Items Export.csv';
    $out = fopen($filename, 'w');
    if (!empty($_POST['include_header']))
        hm_xoiwc_export_header($out);
    hm_xoiwc_export_body($out);
    fclose($out);

    $_POST = $prevPost;

    return $filename;
}

function hm_xoiwc_report_order_statuses() {
    $wcOrderStatuses = wc_get_order_statuses();
    $orderStatuses = array();
    if (!empty($_POST['order_statuses'])) {
        foreach ($_POST['order_statuses'] as $orderStatus) {
            if (isset($wcOrderStatuses[$orderStatus]))
                $orderStatuses[] = substr($orderStatus, 3);
        }
    }
    return $orderStatuses;
}

?>