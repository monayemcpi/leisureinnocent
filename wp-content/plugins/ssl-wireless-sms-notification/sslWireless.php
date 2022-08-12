<?php 
/**
*  Plugin Name: SSL Wireless SMS Notification
*  Plugin URI: https://sslwireless.com/
*  Description: This plugin allows you to send transactional alert to your customer. This will only work for woocommerce.
*  Version: 2.0.0
*  Stable tag: 2.0.0
*  WC tested up to: 4.3.0
*  Author: Prabal Mallick
*  Author URI: prabalsslw.github.io
*  Author Email: prabalsslw@gmail.com
*  License: GNU General Public License v3.0
*  License URI: http://www.gnu.org/licenses/gpl-3.0.html
**/
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    SSLWireless_Woocommerce
 * @author     Prabal Mallick <prabalsslw@gmail.com>
 */

	if (!defined('ABSPATH')) exit; // Exit if accessed directly

	define( 'SSLW_SMS_PATH', plugin_dir_path( __FILE__ ) );
	define( 'SSLW_SMS_URL', plugin_dir_url( __FILE__ ) );

	define ( 'SSLW_SMS_NOTIFICATION_VERSION', '2.0.0');
	
	global $plugin_slug;
	$plugin_slug = 'sslcare';
	$options = get_option( 'sslcare_notification' );

	require_once( SSLW_SMS_PATH . 'lib/sslcare-init.php' );
	require_once( SSLW_SMS_PATH . 'lib/sslcare-admin-setting.php' );
	require_once( SSLW_SMS_PATH . 'lib/sslcare-woo-alert.php' );

	use Sslcare\Admin\Init\Sslcare_Init;
	use Sslcare\Admin\Setting\Sslcare_Admin_Setting;
	use Sslcare\Sms\Woosms\Sslcare_Woo_Alert;

	new Sslcare_Admin_Setting;

	if(isset($options['enable_plugin']) && !empty($options['enable_plugin']))
	{
		new Sslcare_Woo_Alert;
	}

	/**
	 * Hook plugin activation
	*/
	register_activation_hook( __FILE__, 'WcSslwirelessActivator' );
	function WcSslwirelessActivator() {
		Sslcare_Init::install_sslcare();
		$sslcare_installed_version = get_option( "sslcare_plugin_version" );

		if ( $sslcare_installed_version == SSLW_SMS_NOTIFICATION_VERSION ) {
			return true;
		}
		update_option( 'sslcare_plugin_version', SSLW_SMS_NOTIFICATION_VERSION );
	}

	/**
	 * Hook plugin deactivation
	 */
	register_deactivation_hook( __FILE__, 'WcSslwirelessDeactivator' );
	function WcSslwirelessDeactivator() { }


	function sslwireless_care_settings_link($links)
	{
	    $pluginLinks = array(
            'settings' => '<a href="'. esc_url(admin_url( 'admin.php?page=sslcare-notification')) .'">Settings</a>',
            'docs'     => '<a href="https://www.sslwireless.com/enterprise-solutions/api-based-sms/" target="blank">Docs</a>',
            'support'  => '<a href="mailto:prabalsslw@gmail.com">Support</a>'
        );

	    $links = array_merge($links, $pluginLinks);

	    return $links;
	}

	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'sslwireless_care_settings_link');
?>