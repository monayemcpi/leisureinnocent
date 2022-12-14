<?php
/**
 * Plugin Name: Flexible Shipping
 * Plugin URI: https://wordpress.org/plugins/flexible-shipping/
 * Description: Create additional shipment methods in WooCommerce and enable pricing based on cart weight or total.
 * Version: 4.13.3
 * Author: Octolize
 * Author URI: https://octol.io/fs-author
 * Text Domain: flexible-shipping
 * Domain Path: /lang/
 * Requires at least: 5.7
 * Tested up to: 6.0
 * WC requires at least: 6.4
 * WC tested up to: 6.8
 * Requires PHP: 7.0
 *
 * Copyright 2017 WP Desk Ltd.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package Flexible Shipping
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/* THIS VARIABLE CAN BE CHANGED AUTOMATICALLY */
$plugin_version = '4.13.3';

$plugin_name        = 'Flexible Shipping';
$product_id         = 'Flexible Shipping';
$plugin_class_name  = 'Flexible_Shipping_Plugin';
$plugin_text_domain = 'flexible-shipping';
$plugin_file        = __FILE__;
$plugin_dir         = dirname( __FILE__ );

define( 'FLEXIBLE_SHIPPING_VERSION', $plugin_version );
define( $plugin_class_name, $plugin_version );

$requirements = [
	'php'     => '5.6',
	'wp'      => '4.5',
	'plugins' => [
		[
			'name'      => 'woocommerce/woocommerce.php',
			'nice_name' => 'WooCommerce',
		],
	],
];

require __DIR__ . '/vendor_prefixed/wpdesk/wp-plugin-flow-common/src/plugin-init-php52-free.php';
