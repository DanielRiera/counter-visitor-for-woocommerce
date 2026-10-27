<?php
/**
 * Plugin Name: Counter Visitor for Woocommerce
 * Description: Show number of visitors view a product on Woocommerce
 * Version: 1.4.2
 * Author: Daniel Riera
 * Author URI: https://danielriera.net
 * Text Domain: counter-visitor-for-woocommerce
 * Domain Path: /languages
 * Requires Plugins: woocommerce
 * Requires PHP: 8.0
 * WC requires at least: 3.0
 * WC tested up to: 10.6.1
 * Requires at least: 6.8
 * Tested up to: 7.1.3
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WCVISITOR_FILE', __FILE__ );
define( 'WCVISITOR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCVISITOR_URL', plugin_dir_url( __FILE__ ) );
define( 'WCVISITOR_VERSION', '1.4.1' );
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
