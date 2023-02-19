<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class WCVisitorBD {

    static function install() {
        global $wpdb;
        $installed_ver = get_option( "wcvisitor_db_version", 0 );
        if ($installed_ver != WCVisitor_db_version) {
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE {$wpdb->prefix}wcvisitor_records (
                id BIGINT NOT NULL AUTO_INCREMENT,
                created datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                product_id BIGINT NOT NULL,
                ip_address varchar(15) NOT NULL,
                country varchar(10) DEFAULT '' NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";


            //Run SQL
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( $sql );

            //Update bd version plugin
            update_option( 'miravia_db_version', WCVisitor_db_version );
        }
    }
    static function record($product, $ip, $country = '') {
        global $wpdb;

        $checkIfExists = $wpdb->get_var("SELECT id FROM {$wpdb->prefix}wcvisitor_records WHERE product_id = '{$product}' and ip_address='{$ip}'");

        if ($checkIfExists == NULL) {
            $data = array(
                'product_id' => $product,
                'ip_address' => $ip,
                'country' => $country
            );
        
            $wpdb->insert($wpdb->prefix.'wcvisitor_records',$data);
        }

    }

    static function get_visitors($product, $seconds) {
        global $wpdb;
        
        $total = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}wcvisitor_records WHERE product_id = '{$product}' AND created >= now() - interval {$seconds} minute");
        return $total;
    }
}