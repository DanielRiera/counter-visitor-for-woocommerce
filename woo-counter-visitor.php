<?php
/**
 * Plugin Name: Counter Visitor for Woocommerce
 * Description: Show number of visitors view a product on Woocommerce
 * Version: 1.4.0
 * Author: Daniel Riera
 * Author URI: https://danielriera.net
 * Text Domain: counter-visitor-for-woocommerce
 * Domain Path: /languages
 * WC requires at least: 3.0
 * WC tested up to: 10.6.1
 * Required WP: 5.0
 * Tested WP: 6.9.4
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WCVISITOR_FILE', __FILE__ );
define( 'WCVISITOR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCVISITOR_URL', plugin_dir_url( __FILE__ ) );
define( 'WCVISITOR_VERSION', '1.4.0' );
define( 'WCVISITOR_BASENAME', plugin_basename( __FILE__ ) );

require_once WCVISITOR_PATH . 'includes/class-settings.php';
require_once WCVISITOR_PATH . 'includes/Counter/class-repository.php';
require_once WCVISITOR_PATH . 'includes/Counter/class-service.php';
require_once WCVISITOR_PATH . 'includes/Admin/class-admin-controller.php';
require_once WCVISITOR_PATH . 'includes/Ajax/class-ajax-controller.php';
require_once WCVISITOR_PATH . 'includes/Plugin/class-plugin.php';
require_once WCVISITOR_PATH . 'includes/class.api.php';

function wcvisitor() {
    static $plugin = null;

    if ( null === $plugin ) {
        $plugin = new WCVisitor_Plugin();
    }

    return $plugin;
}

wcvisitor();
