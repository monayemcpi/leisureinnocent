<?php
    namespace Sslcare\Admin\Init;

    class Sslcare_Init
    {
        public static function install_sslcare() {

            global $wpdb;

            $sslcare_table_name = $wpdb->prefix . "sslcare_woo_alert";

            $charset_collate = '';

            if ( ! empty( $wpdb->charset ) ) {
              $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
            }

            if ( ! empty( $wpdb->collate ) ) {
                $charset_collate .= " COLLATE {$wpdb->collate}";
            }
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            
            if ($wpdb->get_var("SHOW TABLES LIKE '$sslcare_table_name'") != $sslcare_table_name) {
            $sql = "CREATE TABLE $sslcare_table_name (
                id mediumint(15) UNSIGNED NOT NULL AUTO_INCREMENT,
                customer_name varchar(300) NULL,
                phone_no varchar(20) NULL,
                sending_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                sms_type varchar(100) NULL,
                api_response varchar(4000),
                UNIQUE KEY id (id)
                ) $charset_collate;";
                dbDelta( $sql );       
            }
        }
    }